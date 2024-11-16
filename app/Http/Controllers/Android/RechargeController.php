<?php

namespace App\Http\Controllers\Android;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Provider;
use App\User;
use App\Models\Report;
use Carbon\Carbon;
use App\Models\Integration;
use App\Models\IntegrationOperator;
use App\Models\IntegrationCircle;
use App\Models\IntegrationStatus;
use App\Models\Apiswitch;
use App\Models\Api;
use App\Models\Circle;

class RechargeController extends Controller
{
    public function providersList(Request $post)
    {
        $providers = Provider::where('type', $post->type)->where('status', "1")->orderBy('name')->get(['id', 'name']);
        $states = Circle::get();
        return response()->json(['statuscode' => "TXN", 'message' => "Provider Fetched Successfully", 'data' => $providers, "states" => $states]);
    }

    public function transaction(Request $post)
    {

        $rules = array(
            'apptoken' => 'required',
            'user_id' => 'required|numeric',
            'provider_id' => 'required|numeric',
            'amount' => 'required|numeric|min:10',
            'number' => 'required|numeric'
        );

        $validate = \Myhelper::FormValidator($rules, $post);
        if ($validate != "no") {
            return $validate;
        }

        $user = User::where('id', $post->user_id)->first();
        // if($user->id != "2"){
        //     return response()->json(['statuscode' => "ERR", "message" => "Service Not Allowed"]);
        // }

        if (!$user) {
            $output['statuscode'] = "ERR";
            $output['message'] = "User details not matched";
            return response()->json($output);
        }

        if (!\Myhelper::can('recharge_service', $user->id)) {
            return response()->json(['statuscode' => "ERR", "message" => "Service Not Allowed"]);
        }

        if ($user->status != "active") {
            return response()->json(['statuscode' => "ERR", "message" => "Your account has been blocked."]);
        }

        $provider = Provider::where('id', $post->provider_id)->first();

        if (!$provider) {
            return response()->json(['statuscode' => "ERR", "message" => "Operator Not Found"]);
        }

        if ($provider->status == 0) {
            return response()->json(['statuscode' => "ERR", "message" => "Operator Currently Down."]);
        }

        if (!$provider->api || $provider->api->status == 0) {
            return response()->json(['statuscode' => "ERR", "message" => "Recharge Service Currently Down."]);
        }

        if ($user->mainwallet < $post->amount) {
            return response()->json(['statuscode' => "ERR", "message" => 'Low Balance, Kindly recharge your wallet.']);
        }

        if ($this->pinCheck($post) == "fail") {
            $pin = \Myhelper::encrypt($post->pin, "sdsada7657hgfh$$&7678");
        }

        $previousrecharge = Report::where('number', $post->number)->where('amount', $post->amount)->where('provider_id', $post->provider_id)->whereBetween('created_at', [Carbon::now()->subMinutes(2)->format('Y-m-d H:i:s'), Carbon::now()->format('Y-m-d H:i:s')])->count();
        if ($previousrecharge > 0) {
            return response()->json(['statuscode' => "ERR", "message" => 'Same Transaction allowed after 2 min.'], 200);
        }

        $selectedsSwitchData = null;

        $switchTypes = ['amount', 'state', 'user'];

        foreach ($switchTypes as $switchType) {
            // $switchDatas = Apiswitch::where('provider_id',$provider->id)->where('type', $switchType)->where("user_id",'like', '%,'.$post->user_id.',%')->get();

            $switchDatas = Apiswitch::where('provider_id', $provider->id)->where('type', $switchType)->where('user_id', $post->user_id)->get();
            foreach ($switchDatas as $switchData) {
                if (in_array($post->user_id, $switchData->users)) {
                    if ($switchData->type == "amount") {
                        $amounts = explode(",", $switchData->value);
                        if ($post->amount == $switchData->value) {
                            $selectedsSwitchData = $switchData;
                            break;
                        } elseif (in_array($post->amount, $amounts)) {
                            $selectedsSwitchData = $switchData;
                            break;
                        } else {
                            foreach ($amounts as $amount) {
                                $splitAmount = explode("-", $amount);
                                if ((sizeof($splitAmount) > 1) && ($post->amount >= $splitAmount[0] && $post->amount <= $splitAmount[1])) {
                                    $selectedsSwitchData = $switchData;
                                    break;
                                }
                            }
                        }
                    } elseif ($switchData->type == "state") {
                        if ($post->circle_id == $switchData->value) {
                            $selectedsSwitchData = $switchData;
                            break;
                        }
                    } else {
                        $selectedsSwitchData = $switchData;
                        break;
                    }
                }
            }
            // if($selectedsSwitchData){
            //     break;
            // }
        }

        if ($selectedsSwitchData) {
            if ($selectedsSwitchData->action == "block") {
                return response()->json(['statuscode' => "ERR", "message" => 'Recharge Not Acceptable']);
            } elseif ($selectedsSwitchData->action == "active") {
                $api = Api::where('id', $selectedsSwitchData->api_id)->first();
            } else {
                $api = Api::where('id', $provider->api_id)->first();
            }
        } else {
            $api = Api::where('id', $provider->api_id)->first();
        }

        if ($post->number == "1234567890") {
            return response()->json(['statuscode' => "ERR", "message" => $api->product]);
        }

        $previousrecharge = Report::where('number', $post->number)->where('amount', $post->amount)->where('provider_id', $post->provider_id)->whereIn('status', ['pending', 'success'])->whereBetween('created_at', [Carbon::now()->subMinutes(60)->format('Y-m-d H:i:s'), Carbon::now()->format('Y-m-d H:i:s')])->count();
        if ($previousrecharge > 0) {
            return response()->json(['statuscode' => "ERR", "message" => 'Same Transaction allowed after 1 hours.'], 200);
        }

        $integrationOperator = IntegrationOperator::where('provider_id', $provider->id)->first();
        if (!$integrationOperator) {
            return response()->json(['statuscode' => "ERR", "message" => 'Operator Code Not Mapped']);
        }

        do {
            $post['txnid'] = $this->transcode() . rand(1111111111, 9999999999);
        } while (Report::where("txnid", "=", $post->txnid)->first() instanceof Report);

        $apiIntegration = Integration::find(@$api->code);
        if (!$apiIntegration) {
            return response()->json(['statuscode' => "ERR", "message" => 'Invalid Api']);
        }

        for ($i = 0; $i < sizeof($integrationOperator->apis); $i++) {
            if ($integrationOperator->apis[$i]->id == $apiIntegration->id) {
                $post['apiProvider'] = $integrationOperator->codes[$i];
            }
        }

        $url = $apiIntegration->baseurl;
        $parameter[$apiIntegration->username] = $apiIntegration->usernameval;
        if ($apiIntegration->password) {
            $parameter[$apiIntegration->password] = $apiIntegration->passwordval;
        }
        $parameter[$apiIntegration->mobile] = $post->number;
        $parameter[$apiIntegration->operator] = $post->apiProvider;
        $parameter[$apiIntegration->txnid] = $post->txnid;
        $parameter[$apiIntegration->amount] = $post->amount;
        if ($apiIntegration->state) {
            for ($i = 0; $i < sizeof($integrationCircle->apis); $i++) {
                $integrationCircle = IntegrationCircle::where('circle_id', $post->circle_id)->first();
                if (!$integrationCircle) {
                    return response()->json(['statuscode' => "ERR", "message" => 'Circle Code Not Mapped']);
                }

                if ($integrationCircle->apis[$i]->id == $apiIntegration->id) {
                    $post['state'] = $integrationOperator->codes[$i];
                }
            }

            $parameter[$apiIntegration->state] = $post->state;
        }
        if ($apiIntegration->other) {
            $others = explode("&", $apiIntegration->other);

            foreach ($others as $other) {
                $param = explode("=", $other);
                $parameter[$param[0]] = $param[1];
            }
        }

        switch ($apiIntegration->requesttype) {
            case 'json':
                $header = array(
                    "content-type: application/json"
                );
                $query = json_encode($parameter);
                break;

            default:
                $header = [];
                $query = http_build_query($parameter);
                $url = $url . "?" . $query;

                if ($apiIntegration->method == "GET") {
                    $query = '';
                }
                break;
        }

        $post['profit'] = \Myhelper::getCommission($post->amount, $user->scheme_id, $post->provider_id, $user->role->slug);
        $debit = User::where('id', $user->id)->decrement('mainwallet', $post->amount - $post->profit);
        if ($debit) {

            $insert = [
                'number' => $post->number,
                'mobile' => $user->mobile,
                'provider_id' => $provider->id,
                'api_id' => $api->id ?? 0,
                'amount' => $post->amount,
                'profit' => $post->profit,
                'txnid' => $post->txnid,
                'status' => 'pending',
                'user_id' => $user->id,
                'credit_by' => $user->id,
                'rtype' => 'main',
                'via' => 'app',
                'balance' => $user->mainwallet,
                'trans_type' => 'debit',
                'disid' => $user->parent_id,
                'product' => 'recharge'
            ];

            $report = Report::create($insert);

            if (env('APP_ENV') == "server") {
                if ($apiIntegration->id == 1) {
                    // dd([$url, $apiIntegration->method, $query, $header,$result]);
                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                        CURLOPT_URL => $url,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => $apiIntegration->method,
                        CURLOPT_POSTFIELDS => $query,
                        CURLOPT_HTTPHEADER => array(
                            'secretkey: 0209893HCHGDVH002092GUD3330000',
                            'saltkey: 0002028U3HDFD0298UBCVZZXMBH',
                            'Content-Type: application/json'
                        ),
                    ));
                    $err = curl_error($curl);
                    $resultdata = curl_exec($curl);

                    curl_close($curl);

                    $result['response'] = $resultdata;
                    $result['error'] = $err;
                    \DB::table('microlog')->insert(['response' => ($resultdata), 'product' => $url]);
                } else {
                    $result = \Myhelper::curl($url, $apiIntegration->method, $query, $header, "yes", "App\Model\Report", $post->txnid);
                }
            } else {
                $result = [
                    'error' => true,
                    'response' => ''
                ];
            }

            $response = '';
            switch ($apiIntegration->responsetype) {
                case 'json':
                    $response = json_decode($result['response'], true);
                    break;

                case 'xml':
                    $xmlResponse = simplexml_load_string($result['response']);
                    $response = json_decode(json_encode($xmlResponse), true);
                    break;

                default:
                    $response = json_decode($result['response'], true);
                    break;
            }

            if ($result['error'] || $result['response'] == '') {
                $update['status'] = "pending";
                $update['payid'] = "pending";
                $update['refno'] = "pending";
                $update['description'] = "recharge pending";
            } else {
                if (isset($response[$apiIntegration->message][$apiIntegration->status])) {

                    $status = $response[$apiIntegration->message][$apiIntegration->status];
                    $failedVal = explode(',', $apiIntegration->failed);
                    $successVal = explode(',', $apiIntegration->success);

                    if (in_array($status, $successVal)) {
                        $update['status'] = "success";
                        $update['payid'] = $response[$apiIntegration->message][$apiIntegration->payid];
                        $update['refno'] = $response[$apiIntegration->message][$apiIntegration->refno];
                    } elseif (in_array($status, $failedVal)) {
                        $update['status'] = "failed";
                        $update['payid'] = isset($response[$apiIntegration->message][$apiIntegration->payid]) ? $response[$apiIntegration->message][$apiIntegration->payid] : "failed";
                        $update['refno'] = $response[$apiIntegration->message][$apiIntegration->refno];
                        $update['description'] = $response[$apiIntegration->message]['reason'];
                    } else {
                        $update['status'] = "pending";
                        $update['payid'] = isset($response[$apiIntegration->message][$apiIntegration->payid]) ? $response[$apiIntegration->message][$apiIntegration->payid] : "pending";
                        $update['refno'] = isset($response[$apiIntegration->message][$apiIntegration->refno]) ? $response[$apiIntegration->message][$apiIntegration->refno] : "pending";
                    }
                } else if (isset($response[$apiIntegration->status])) {
                    $status = $response[$apiIntegration->status];
                    $failedVal = explode(',', $apiIntegration->failed);
                    $successVal = explode(',', $apiIntegration->success);

                    if (in_array($status, $successVal)) {
                        $update['status'] = "success";
                        $update['payid'] = $response[$apiIntegration->payid];
                        $update['refno'] = $response[$apiIntegration->refno];
                    } elseif (in_array($status, $failedVal)) {
                        $update['status'] = "failed";
                        $update['payid'] = isset($response[$apiIntegration->payid]) ? $response[$apiIntegration->payid] : "failed";
                        if ($response[$apiIntegration->message] == $apiIntegration->balance) {
                            $update['refno'] = "Service Down For Sometime";
                        } else {
                            $update['refno'] = $response[$apiIntegration->message];
                        }
                    } else {
                        $update['status'] = "pending";
                        $update['payid'] = isset($response[$apiIntegration->payid]) ? $response[$apiIntegration->payid] : "pending";
                        $update['refno'] = isset($response[$apiIntegration->refno]) ? $response[$apiIntegration->refno] : "pending";
                    }
                } else {
                    $update['status'] = "failed";
                    $update['payid'] = isset($response[$apiIntegration->payid]) ? $response[$apiIntegration->payid] : "failed";
                    if ($response[$apiIntegration->message] == $apiIntegration->balance) {
                        $update['refno'] = "Service Down For Sometime";
                    } else {
                        $update['refno'] = isset($response[$apiIntegration->message]) ? $response[$apiIntegration->message] : "failed";
                    }
                }
            }

            if ($update['status'] == "success" || $update['status'] == "pending") {
                Report::where('id', $report->id)->update($update);
                \Myhelper::commission($report);
                $output['statuscode'] = "TXN";
                $output['message'] = "Recharge Accepted";
            } else {
                User::where('id', $user->id)->increment('mainwallet', $post->amount - $post->profit);
                Report::where('id', $report->id)->update($update);
                $output['statuscode'] = "TXF";
                $output['message'] = "Failed ! " . $update['refno'];
                if ($update['refno'] == "" || $update['refno'] == "null" || $update['refno'] == null) {
                    $update['refno'] = "Failed";
                }
            }

            $output['txnid'] = $post->txnid;
            $output['rrn'] = $update['refno'];
            return response()->json($output);
        } else {
            return response()->json(['statuscode' => "ERR", "message" => "Something went wrong"]);
        }
    }

    public function statusCheck(Request $post)
    {

        $report = Report::where('id', $post->txnid)->first();

        if (\Myhelper::hasNotRole(['admin'])) {

            return response()->json(['status' => 'Please Contact to administrator']);
        }
        // dd($report);

        $apiIntegration = IntegrationStatus::where('api_id', $report->api->code)->first();

        if (!$apiIntegration) {
            return response()->json(['statuscode' => "ERR", 'message' => 'Invalid Api or Status Integration not found']);
        }

        $url = $apiIntegration->baseurl;
        $parameter[$apiIntegration->username] = $apiIntegration->usernameval;
        if ($apiIntegration->password) {
            $parameter[$apiIntegration->password] = $apiIntegration->passwordval;
        }
        $parameter[$apiIntegration->mobile] = $report->number;
        $parameter[$apiIntegration->txnid] = $report->txnid;
        $parameter[$apiIntegration->amount] = $report->amount;
        if ($apiIntegration->other) {
            $others = explode("&", $apiIntegration->other);

            foreach ($others as $other) {
                $param = explode("=", $other);
                $parameter[$param[0]] = $param[1];
            }
        }

        switch ($apiIntegration->requesttype) {
            case 'json':
                $header = array(
                    "content-type: application/json"
                );
                $query = json_encode($parameter);
                break;

            default:
                $header = [];
                $query = http_build_query($parameter);
                $url = $url . "?" . $query;

                if ($apiIntegration->method == "GET") {
                    $query = '';
                }
                break;
        }

        $method = $apiIntegration->method;

        $result = \Myhelper::curl($url, $method, $parameter, $header);


        if ($result['response'] != '') {
            // switch ($post->type) {
            // 	case 'recharge':
            $response = '';
            switch ($apiIntegration->responsetype) {
                case 'json':
                    $response = json_decode($result['response'], true);
                    break;

                case 'xml':
                    $xmlResponse = simplexml_load_string($result['response']);
                    $response = json_decode(json_encode($xmlResponse), true);
                    break;

                default:
                    $response = json_decode($result['response'], true);
                    break;
            }

            if ($result['error'] || $result['response'] == '') {
                $update['status'] = "pending";
                $update['payid'] = "pending";
                $update['refno'] = "pending";
                $update['description'] = "recharge pending";
            } else {
                if (isset($response[$apiIntegration->status])) {
                    $status = $response[$apiIntegration->status];
                    $failedVal = explode(',', $apiIntegration->failed);
                    $successVal = explode(',', $apiIntegration->success);

                    if (in_array($status, $successVal)) {
                        $update['status'] = "success";
                        $update['refno'] = $response[$apiIntegration->refno];
                    } elseif (in_array($status, $failedVal)) {
                        $update['status'] = "reversed";
                        $update['refno'] = $response[$apiIntegration->message];
                    } else {
                        $update['status'] = "pending";
                        $update['refno'] = isset($response[$apiIntegration->refno]) ? $response[$apiIntegration->refno] : "pending";
                    }
                } else {
                    $update['status'] = "pending";
                    $update['refno'] = isset($response[$apiIntegration->refno]) ? $response[$apiIntegration->refno] : "pending";
                }
            }

            $reportupdate = Report::updateOrCreate(['id' => $post->txnid], $update);
            if ($reportupdate && $update['status'] == "reversed") {
                \Myhelper::transactionRefund($post->txnid);
            }

            // $reportgetData = Report::where('id', $post->txnid)->first();

            $update['statuscode'] = "TXN";
            $update['txn_status'] = $update['status'];
            // $update['refno'] = $reportgetData->refno;

            return response()->json($update);
        } else {
            return response()->json(['status' => 'ERR', 'message' => "Status Not Fetched , Try Again."]);
        }
    }

    public function mplan(Request $post)
    {
        $provider = Provider::where('id', $post->operator)->first();

        if (!$provider) {
            return response()->json(['status' => "Operator Not Found"], 400);
        }

        $url = "http://securepayments.net.in/api/recharge/getplan?token=AEsZl6QzaouNZWPcqOmZ8V8v9tggzl&operator=" . $provider->recharge1;

        $result = \Myhelper::curl($url, "GET", "", [], "no");
        //dd([$url, $result]);
        if ($result['response'] != '') {
            $response = json_decode($result['response']);

            if (!isset($response->statuscode)) {
                return response()->json(['statuscode' => "TXN", "data" => $response], 200);
            }

            return response()->json(['statuscode' => "ERR", "message" => "Something went wrong"]);
        } else {
            return response()->json(['statuscode' => "ERR", "message" => "Something went wrongs"]);
        }
    }

    public function getplan(Request $post)
    {
        $rules = array(
            'operator' => 'required|numeric',
            'type' => 'required',
            'circle' => "required"
        );

        $validate = \Myhelper::FormValidator($rules, $post);
        if ($validate != "no") {
            return $validate;
        }

        $provider = Provider::where('id', $post->operator)->first();
        if (!$provider) {
            return response()->json(['statuscode' => "ERR", 'message' => "Operator Not Found"]);
        }

        // $apis = Api::where('code', 'recharge3')->first();


        $header = array(
            "cache-control: no-cache",
            "content-type: application/json",
            "secretkey:0209893HCHGDVH002092GUD3330000",
            //.$apis->password,
            "saltkey:0002028U3HDFD0298UBCVZZXMBH" //.$apis->username
        );

        if ($post->type == "mobile") {
            $url = "https://partners.mahagram.in/rechargesplan/api/PlanAPI/simpleplan";
            $parameter['cricle'] = $post->circle;
            $parameter['Operator'] = $provider->name;
        } else {
            $parameter['Operator'] = $provider->name;
            $url = "https://partners.mahagram.in/rechargesplan/api/PlanAPI/dthplan";
        }


        $result = \Myhelper::curl($url, "POST", json_encode($parameter), $header, "yes");
        //dd($result);
        //  dd(json_encode([$url,$parameter,$header,$result]));
        if ($result['response'] != '') {
            $response = json_decode($result['response']);

            if (isset($response->statuscode) && $response->statuscode == 000) {
                return response()->json(['status' => "success", "data" => $response->data]);
            }

            return response()->json(['status' => "failed", "message" => $response->message ?? "Something went wrong"]);
        } else {
            return response()->json(['status' => "failed", "message" => "Something went wrongs"]);
        }
    }

    public function operatorinfo(Request $post)
    {
        // dd($post->all());
        $rules = array(
            'mobile' => 'required|numeric',
            'type' => 'required'
        );

        // dd($post->all());

        if ($post->type == "mobile") {
            $url = "http://operatorcheck.mplan.in/api/operatorinfo.php?apikey=d0b85ec761116fb44400ccc0ee608255&tel=" . $post->mobile;
        } else if ($post->type == "dth") {
            $url = "http://operatorcheck.mplan.in/api/dthoperatorinfo.php?apikey=d0b85ec761116fb44400ccc0ee608255&tel=" . $post->mobile;
        } else {
            return response()->json(['statuscode' => "ERR", "data" => "This type not Found"], 200);
        }
        $result = \Myhelper::curl($url, "GET", "", [], "no");

        //  dd([$url, $result]);
        if ($result['response'] != '') {
            $response = json_decode($result['response']);
            // dd($response->records);
            if (isset($response->records->status) && $response->records->status == "1") {
                if ($post->type == "mobile") {
                    $column = "ismandatory";
                } else if ($post->type == "dth") {
                    $column = "dthplancode";
                }
                $provider = Provider::where($column, $response->records->Operator)->first();
                //   dd($provider);
                if ($provider) {
                    return response()->json(['statuscode' => "TXN", "message" => "Fetch Successfully", "providerid" => $provider->id, "providername" => $provider->name], 200);
                } else {
                    return response()->json(['statuscode' => "ERR", "data" => "Record Not Found"], 200);
                }
            }

            return response()->json(['status' => "ERR", "message" => "user not found"]);
        } else {
            return response()->json(['status' => "ERR", "message" => "Something went wrong"]);
        }
    }



    public function getdthinfo(Request $post)
    {
        $rules["number"] = ['required'];
        $rules["user_id"] = ['required'];
        $rules["apptoken"] = ['required'];
        $rules["provider_id"] = ['required'];

        $validator = \Validator::make($post->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['statuscode' => 'ERR', 'message' => $validator->errors()->first()]);
        }


        $provider = Provider::where('id', @$post->operator)->first();
        $apis = Api::where('code', 'recharge3')->first();

        $url = "https://partners.mahagram.in/rechargesplan/api/PlanAPI/dthcustomerinfo";
        $parameter = [
            "phno" => $post->number,
            "operator" => @$provider->MahaDth
        ];


        $header = array(
            "cache-control: no-cache",
            "content-type: application/json",
            "secretkey:0209893HCHGDVH002092GUD3330000",
            //.$apis->password,
            "saltkey:0002028U3HDFD0298UBCVZZXMBH" //.$apis->username
        );
        $query = json_encode($parameter);
        $method = "POST";

        $result = \Myhelper::curl($url, $method, $query, $header, "yes");
        if ($result['response'] != '') {
            $response = json_decode($result['response']);

            if (!empty($response->data[0]) && $response->statuscode == "000") {
                return response()->json(["statuscode" => "TXN", 'message' => "dth info fetch successfully", "data" => $response->data[0]]);
            }
            return response()->json(['statuscode' => "TXF", "message" => isset($response->data->desc) ? $response->data->desc : "Something went wrong"]);
        } else {
            return response()->json(["statuscode" => "TXF", "message" => "Something went wrongs"]);
        }
    }

    public function dthrefersh(Request $post)
    {
        $rules = array(
            'operator' => 'required|numeric',
            'number' => 'required|numeric'
        );

        $validate = \Myhelper::FormValidator($rules, $post);
        if ($validate != "no") {
            return $validate;
        }

        $apptoken = \App\Model\Securedata::where('apptoken', $post->apptoken)->where('user_id', $post->user_id)->first();

        if (!$apptoken) {
            return response()->json(['statuscode' => 'UA', 'status' => 'UA', 'message' => "Unauthorize Access Ip"]);
        }

        $provider = Provider::where('id', $post->operator)->first();
        if (!$provider) {
            return response()->json(['statuscode' => "ERR", "message" => "Provider Not Found"]);
        }

        $provider = Provider::where('id', $post->operator)->first();
        $url = "https://www.mplan.in/api/Dthheavy.php?offer=roffer&apikey=d0b85ec761116fb44400ccc0ee608255&tel=" . $post->number . "&operator=" . urlencode($provider->recharge3);
        $result = \Myhelper::curl($url, "GET", "", [], "no");

        //dd([$url, $result]);
        if ($result['response'] != '') {
            $response = json_decode($result['response']);

            if (isset($response->status) && $response->status == "1") {
                return response()->json(['statuscode' => "TXN", "name" => isset($response->records->customerName) ? $response->records->customerName : '', "data" => isset($response->records->desc) ? $response->records->desc : ''], 200);
            }

            return response()->json(['statuscode' => "ERR", 'message' => $response->records->msg]);
        } else {
            return response()->json(['statuscode' => "ERR", "message" => "Something went wrong"]);
        }
    }

    public function roffer(Request $post)
    {
        $rules = array(
            'provider_id' => 'required|numeric',
            'number' => 'required|numeric'
        );

        $validate = \Myhelper::FormValidator($rules, $post);
        if ($validate != "no") {
            return $validate;
        }

        $provider = Provider::where('id', $post->provider_id)->first();

        if (!$provider) {
            return response()->json(['statuscode' => "ERR", "message" => "Provider Not Found"]);
        }

        $provider = Provider::where('id', $post->provider_id)->first();
        //dd($provider->ismandatory[0]);

        if ($provider->type == "mobile") {
            $url = "https://www.mplan.in/api/plans.php?apikey=d0b85ec761116fb44400ccc0ee608255&offer=roffer" . "&tel=" . $post->number . "&operator=" . urlencode($provider->ismandatory[0]);
        } else if ($provider->type == "dth") {
            $url = "https://www.mplan.in/api/DthRoffer.php?apikey=d0b85ec761116fb44400ccc0ee608255&offer=roffer" . "&tel=" . $post->number . "&operator=" . urlencode($provider->dthplancode);
        }

        $result = \Myhelper::curl($url, "GET", "", [], "no");
        //dd([$url,$result]);


        if ($result['response'] != '') {
            $response = json_decode($result['response']);
            //dd($response);
            if (isset($response->records) && $response->status == "1") {

                return response()->json(['status' => "TXN", "data" => $response->records], 200);
            }

            return response()->json(['status' => "ERR", "message" => "No Offer Found"]);
        } else {
            return response()->json(['status' => "ERR", "message" => "Something went wrong"]);
        }
    }
}
