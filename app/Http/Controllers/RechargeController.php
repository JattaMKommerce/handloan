<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Provider;
use App\Models\Report;
use App\Models\Circle;
use App\Models\Integration;
use App\Models\IntegrationOperator;
use App\Models\IntegrationCircle;
use App\User;
use Carbon\Carbon;
use App\Models\Apiswitch;
use App\Models\Api;
use MiladRahimi\Jwt\Generator;
use MiladRahimi\Jwt\Parser;
use MiladRahimi\Jwt\Cryptography\Keys\HmacKey;
use MiladRahimi\Jwt\Cryptography\Algorithms\Hmac\HS256;

class RechargeController extends Controller
{
    protected $api;
    public function __construct()
    {
        
        $this->prechargeapi = Api::where('code', '3')->first();
         
    }
    public function index($type)
    {
        if (\Myhelper::hasRole('admin') || !\Myhelper::can('recharge_service')) {
            abort(403);
        }
        $data['type'] = $type;
        $data['providers'] = Provider::where('type', $type)->where('status', "1")->orderBy('name')->get();
        $data['circles'] = Circle::get();
        return view('service.recharge')->with($data);
    }
    
    public function getToken($uniqueid)
    {
        $payload =  [
            "timestamp" => time(),
            "partnerId" => $this->prechargeapi->username,
            "reqid"     => $uniqueid
        ];
        
        //$keyString = $this->api->password;
         $keyString = $this->prechargeapi->password;
        
        if (strlen($keyString) < 32) {
            throw new \Exception ("Key length is too short. It must be at least 32 characters.");
        }
        $key = new HmacKey($keyString);
    
        $algorithm = new HS256($key);
    
        // Generate a JWT
        $generator = new Generator($algorithm);
    
        try {
            $jwt = $generator->generate($payload);
           //dd($jwt);
            return ['token' => $jwt, 'payload' => $payload];
        } catch (\Exception $e) {
           
            dd($e->getMessage());
        }
         
    }
    public function payment(\App\Http\Requests\Recharge $post)
    {
        if (\Myhelper::hasRole('admin') || !\Myhelper::can('recharge_service')) {
            return response()->json(['status' => "Permission Not Allowed"], 400);
        }

        $user = \Auth::user();
        $post['user_id'] = $user->id;
        if ($user->status != "active") {
            return response()->json(['status' => "Your account has been blocked."], 400);
        }

        $provider = Provider::where('id', $post->provider_id)->first();


        if (!$provider) {
            return response()->json(['status' => "Operator Not Found"], 400);
        }

        if ($provider->status == 0) {
            return response()->json(['status' => "Operator Currently Down."], 400);
        }

        // if (!$provider->api || $provider->api->status == 0) {
        //     return response()->json(['status' => "Recharge Service Currently Down."], 400);
        // }

        if ($user->mainwallet < $post->amount) {
            return response()->json(['status' => 'Low Balance, Kindly recharge your wallet.'], 400);
        }

        $previousrecharge = Report::where('number', $post->number)->where('amount', $post->amount)->where('provider_id', $post->provider_id)->whereBetween('created_at', [Carbon::now()->subMinutes(2)->format('Y-m-d H:i:s'), Carbon::now()->format('Y-m-d H:i:s')])->count();
        if ($previousrecharge > 0) {
            // return response()->json(['status' => 'Same Transaction allowed after 2 min.'], 400);
        }

        $selectedsSwitchData = null;


        $switchTypes = ['amount', 'user', 'state'];

        foreach ($switchTypes as $switchType) {
            //  $switchDatas = Apiswitch::where('provider_id',$provider->id)->where('type', $switchType)->where("user_id",'like', '%,'.$post->user_id.',%')->get();
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
                        if ($post->state == $switchData->value) {
                            $selectedsSwitchData = $switchData;
                            break;
                        }
                    } else {
                        $selectedsSwitchData = $switchData;
                        break;
                    }
                }
            }
        }


        if ($selectedsSwitchData) {
            if ($selectedsSwitchData->action == "block") {
                return response()->json(['status' => 'Recharge Not Acceptable'], 400);
            } elseif ($selectedsSwitchData->action == "active") {
                $api = Api::where('id', $selectedsSwitchData->api_id)->first();
            } else {
                $api = Api::where('id', $provider->api_id)->first();
            }
        } else {
            $api = Api::where('id', $provider->api_id)->first();
        }



        if ($post->number == "1234567890") {
            return response()->json(['status' => $api->product], 400);
        }

        $integrationOperator = IntegrationOperator::where('provider_id', $provider->id)->first();
        if (!$integrationOperator) {
            return response()->json(['status' => 'Operator Code Not Mapped'], 400);
        }

        do {
            $post['txnid'] = $this->transcode() . rand(1111111111, 9999999999);
        } while (Report::where("txnid", "=", $post->txnid)->first() instanceof Report);


        $apiIntegration = Integration::find($api->code);

        if (!$apiIntegration) {
            return response()->json(['status' => 'Invalid Api'], 400);
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
                    return response()->json(['status' => 'Circle Code Not Mapped'], 400);
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
                if($api->code == 3)
                {
                    $token = $this->getToken(\Auth::id().Carbon::now()->timestamp);
                     $header = array(
                    "Cache-Control: no-cache",
                    "Content-Type: application/json",
                    "Token: ".$token['token'],
                     "Authorisedkey: ODU5ZjE0NWYxYjA3NTQ2ZTE2ZDQyYWQzMzUxMzBmZWY=" 
                   );
                }else
                {
                    $header = array(
                        "content-type" => "application/json",
                        "$apiIntegration->header1" => $apiIntegration->headerval1,
                        "$apiIntegration->header2" => $apiIntegration->headerval2,
    
                    );
                }
                


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
                'via' => 'portal',
                'balance' => $user->mainwallet,
                'trans_type' => 'debit',
                'product' => 'recharge',
                'create_time' => Carbon::now()->format('Y-m-d H:i:s')
            ];
            try {
                $report = Report::create($insert);
            } catch (\Exception $e) {
                User::where('id', $user->id)->increment('mainwallet', $post->amount - $post->profit);
                return response()->json(['status' => "failed", "description" => "Something went wrong"], 200);
            }





            if (env('APP_ENV') == "server") {

                $result = \Myhelper::curl($url, $apiIntegration->method, $query, $header, "yes", "App\Model\Report", $post->txnid);
            } else {
                $result = [
                    'error' => true,
                    'response' => ''
                ];
            }

            // dd($url,$apiIntegration->method,$query,$result,$header);//



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
                        $update['refno'] = $response[$apiIntegration->message];
                    } else {
                        $update['status'] = "pending";
                        $update['payid'] = isset($response[$apiIntegration->payid]) ? $response[$apiIntegration->payid] : "pending";
                        $update['refno'] = isset($response[$apiIntegration->refno]) ? $response[$apiIntegration->refno] : "pending";
                    }
                } else {
                    $update['status'] = "failed";
                    $update['payid'] = isset($response[$apiIntegration->payid]) ? $response[$apiIntegration->payid] : "failed";
                    $update['refno'] = isset($response[$apiIntegration->message]) ? $response[$apiIntegration->message] : "failed";
                }
            }

            //dd([$url,$apiIntegration->method, $query, $response, $update, $result]);

            if ($update['status'] == "success" || $update['status'] == "pending") {
                Report::where('id', $report->id)->update($update);
                \Myhelper::commission($report);
            } else {
                User::where('id', $user->id)->increment('mainwallet', $post->amount - $post->profit);
                Report::where('id', $report->id)->update($update);
            }




            return response()->json($update, 200);
        } else {
            return response()->json(['status' => "failed", "description" => "Something went wrong"], 200);
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
                return response()->json(['status' => "success", "data" => $response], 200);
            }

            return response()->json(['status' => "failed", "message" => "Something went wrong"]);
        } else {
            return response()->json(['status' => "failed", "message" => "Something went wrongs"]);
        }
    }

    public function getplan(Request $post)
    {
        // dd($post->all());
        $provider = Provider::where('id', $post->operator)->first();
        if (!$provider) {
            return response()->json(['status' => "Operator Not Found"], 400);
        }

        // $apis = Api::where('code', 'recharge3')->first();


        $header = array(
            "cache-control: no-cache",
            // "content-type: application/json",
            // "secretkey:0209893HCHGDVH002092GUD3330000", //.$apis->password,
            // "saltkey:0002028U3HDFD0298UBCVZZXMBH" //.$apis->username
        );
        
        //paysprint code
        $token = $this->getToken(\Auth::id().Carbon::now()->timestamp);
        $header = array(
            "Cache-Control: no-cache",
            "Content-Type: application/json",
            "Token: ".$token['token'],
            "Authorisedkey: ODU5ZjE0NWYxYjA3NTQ2ZTE2ZDQyYWQzMzUxMzBmZWY="
        );
        //end paysprint code
        if ($post->type == "mobile") {
            //$url = "https://www.mplan.in/api/plans.php?apikey=8ade08cd7ef58f22b91cc7027f8078d0&cricle=".urlencode($post->circle)."&operator=".$provider->name;
            
            //paysprint code
            $url = "https://api.paysprint.in/api/v1/service/recharge/hlrapi/browseplan";
            $parameter['circle'] = $post->circle;
            $parameter['op'] = $provider->name;
            $method = "POST";
            //end paysprint code
            
            //$parameter =[];
            //$method = "GET";
        } else {
            $parameter['Operator'] = $provider->name;
            //$url = "https://partners.mahagram.in/rechargesplan/api/PlanAPI/dthplan";
            $url = "https://sit.paysprint.in/service-api/api/v1/service/recharge/hlrapi/dthinfo";
            $parameter['canumber'] = $post->circle;
            $parameter['op'] = $provider->name;
            $method = "POST";
        }


        $result = \Myhelper::curl($url, $method, json_encode($parameter), $header, "yes");
        //dd($result);
        //  dd(json_encode([$url,$parameter,$header,$result]));
        if ($result['response'] != '') {
            $response = json_decode($result['response']);
            // dd($response);
            if (isset($response->status) && $response->status == 1) {
                //Mplan 
                // return response()->json(['status' => "success", "data" => $response->records], 200);
                
                //paysprint
                return response()->json(['status' => "success", "data" => $response->info], 200);
            }

            return response()->json(['status' => "failed", "message" => $response->message ?? "Something went wrong"]);
        } else {
            return response()->json(['status' => "failed", "message" => "Something went wrongs"]);
        }
    }

    public function roffer(Request $post)
    {
        $rules = array(
            'operator' => 'required|numeric',
            'number' => 'required|numeric'
        );

        $validate = \Myhelper::FormValidator($rules, $post);
        if ($validate != "no") {
            return $validate;
        }

        $provider = Provider::where('id', $post->operator)->first();

        if (!$provider) {
            return response()->json(['statuscode' => "ERR", "message" => "Provider Not Found"]);
        }

        $provider = Provider::where('id', $post->operator)->first();
        //dd($provider->ismandatory[0]);

        if ($provider->type == "mobile") {
            $url = "https://www.mplan.in/api/plans.php?apikey=d0b85ec761116fb44400ccc0ee608255&offer=roffer" . "&tel=" . $post->number . "&operator=" . urlencode($provider->ismandatory[0]);
        } else {
            $url = "https://www.mplan.in/api/DthRoffer.php?apikey=d0b85ec761116fb44400ccc0ee608255&offer=roffer" . "&tel=" . $post->number . "&operator=" . urlencode($provider->dthplancode);
        }

        $result = \Myhelper::curl($url, "GET", "", [], "no");

        //dd($url,$result);
        if ($result['response'] != '') {

            $response = json_decode($result['response']);

            //dd($response);

            //$datas = json_decode($response['response']);
            //dd($datas);
            if (isset($response->status) && $response->status == "1") {

                return response()->json(['status' => "success", "data" => $response->records], 200);
            }

            return response()->json(['status' => "failed", "message" => "Something went wrong"]);
        } else {
            return response()->json(['status' => "failed", "message" => "Something went wrong"]);
        }
    }

    public function operatorinfo(Request $post)
    {
        $rules = array(
            'Mobileno' => 'required|numeric'
        );

        $validate = \Myhelper::FormValidator($rules, $post);
        if ($validate != "no") {
            return $validate;
        }

        if ($post->type == "mobile") {
            $url = "http://operatorcheck.mplan.in/api/operatorinfo.php?apikey=d0b85ec761116fb44400ccc0ee608255&tel=" . $post->Mobileno;
        } else {
            $url = "http://operatorcheck.mplan.in/api/dthoperatorinfo.php?apikey=d0b85ec761116fb44400ccc0ee608255&tel=" . $post->Mobileno;
        }
        $result = \Myhelper::curl($url, "GET", "", [], "no");

        // dd([$url, $result]);
        if ($result['response'] != '') {
            $response = json_decode($result['response']);
            //dd($response->records);
            if (isset($response->records->status) && $response->records->status == "1") {
                $provider = Provider::where('dthplancode', $response->records->Operator)->first();
                if ($provider) {
                    return response()->json(['statuscode' => "TXN", "message" => "Fetch Successfully", "provider" => $provider, "providerid" => $provider->id, "providername" => $provider->name]);
                } else {
                    return response()->json(['status' => "ERR", "data" => "Record Not Found"], 200);
                }
            }

            return response()->json(['status' => "ERR", "message" => "Something went wrong"]);
        } else {
            return response()->json(['status' => "ERR", "message" => "Something went wrong"]);
        }
    }

    public function dthinfo(Request $post)
    {
        $rules = array(
            'operator' => 'required|numeric',
            'number' => 'required|numeric'
        );

        $validate = \Myhelper::FormValidator($rules, $post);
        if ($validate != "no") {
            return $validate;
        }


        $provider = Provider::where('id', $post->operator)->first();
        if (!$provider) {
            return response()->json(['statuscode' => "ERR", "message" => "Provider Not Found"]);
        }

        $provider = Provider::where('id', $post->operator)->first();
        $url = "https://www.mplan.in/api/Dthinfo.php?offer=roffer&apikey=d0b85ec761116fb44400ccc0ee608255&tel=" . $post->number . "&operator=" . urlencode($provider->dthplancode);
        $result = \Myhelper::curl($url, "GET", "", [], "no");

        // dd([$url, $result]); && 
        if ($result['response'] != '') {
            $response = json_decode($result['response']);

            if (isset($response->status) && $response->status == "1" && !isset($response->records->desc) && isset($response->records[0]->status)) {
                return response()->json(['status' => "TXN", "data" => $response->records[0]], 200);
            }

            return response()->json(['status' => "ERR", "message" => "Customer Details Not Found"]);
        } else {
            return response()->json(['status' => "ERR", "message" => "Something went wrong"]);
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
                return response()->json(['status' => "TXN", "name" => isset($response->records->customerName) ? $response->records->customerName : '', "data" => isset($response->records->desc) ? $response->records->desc : ''], 200);
            }

            return response()->json(['status' => "ERR", "message" => "Something went wrong"]);
        } else {
            return response()->json(['status' => "ERR", "message" => "Something went wrong"]);
        }
    }
    public function getprovider(Request $post)
    {
        $rules = array(
            // 'number'   => 'required|numeric'
        );

        $validate = \Myhelper::FormValidator($rules, $post);
        if ($validate != "no") {
            return $validate;
        }
        //dd($post->all());
        if ($post->type == "mobile") {
            $url = "http://planapi.in/api/Mobile/OperatorFetchNew?ApiUserID=3784&ApiPassword=9854684578&Mobileno=$post->Mobileno";
        } else {
            $provider = Provider::where('id', $post->Opcode)->first();
            //dd($provider);
            //  $url = "http://planapi.in/api/Mobile/DTHINFOCheck?apimember_id=3784&api_password=9854684578&Opcode=".$provider->recharge3."&mobile_no=$post->mobile_no";
            $url = "http://planapi.in/api/Mobile/OperatorFetchNew?ApiUserID=3784&ApiPassword=9854684578&Mobileno=$post->Mobileno";
        }
        $result = \Myhelper::curl($url, "GET", "", [], "no");

        // dd([$url, $result]);
        if ($result['response'] != '') {
            $response = json_decode($result['response']);
            //  dd($response,$post->all());
            if ($post->type == "mobile") {
                if (isset($response->STATUS) && $response->STATUS == "1") {
                    $provider = Provider::where('recharge3', $response->OpCode)->first();
                    if ($post->type == "mobile") {
                        $provider = Provider::where('recharge3', $response->OpCode)->first();
                        // dd($response);
                        return response()->json(['statuscode' => "TXN", "message" => "Fetch Successfully", "provider" => $provider, "providerid" => $provider->id, "providername" => $provider->name]);
                    } else {
                        return response()->json(['statuscode' => "ERR", "message" => "Record Not Found"]);
                    }
                }
            } else {
                if (isset($response->error) && $response->error == "0") {


                    //dd($response->DATA);
                    return response()->json(['statuscode' => "TXN", "message" => $response->Message, "data" => $response->DATA]);
                }
            }


            return response()->json(['statuscode' => "ERR", "message" => "Something went wrong1"]);
        } else {
            return response()->json(['statuscode' => "ERR", "message" => "Something went wrong2"]);
        }
    }
    
    public function getoperator(Request $post)
    {
        // $url = "https://api.paysprint.in/api/v1/service/recharge/recharge/recharge/getoperator";
        $url = "https://api.paysprint.in/api/v1/service/recharge/hlrapi/hlrcheck" ;
      
        $parameter = [
            "number" =>  $post->number, 
            "type"   =>  $post->type 
        ];

        $token = $this->getToken(\Auth::id().Carbon::now()->timestamp);
        $header = array(
            "Cache-Control: no-cache",
            "Content-Type: application/json",
            "Token: ".$token['token'],
            "Authorisedkey: ODU5ZjE0NWYxYjA3NTQ2ZTE2ZDQyYWQzMzUxMzBmZWY="
        );

        $query = json_encode($parameter);
        $method = "POST"; 
        
        $result = \Myhelper::curl($url, $method, $query, $header, "no");
            \DB::table('rp_log')->insert([
                            'ServiceName' => "Get Oprator",
                            'header' => json_encode($header),
                            'body' => json_encode($parameter),
                            'response' => $result['response'],
                            'url' => $url,
                            'created_at' => date('Y-m-d H:i:s')
                        ]);
        if($result['response'] != ''){
            $response = json_decode($result['response']);
            if(isset($response->response_code) && $response->response_code == "1"){
                $provider = Provider::where('name', 'like', '%'.strtolower($response->info->operator).'%')->where('type', $post->type)->first();
                //dd($result,$url,$parameter, $provider);
                return response()->json(['status' => "success", "data" => $provider->id, "circle" => $response->info->circle, "providername" => $response->info->operator], 200);
            }
            elseif($response->response_code == "3"){
              return response()->json(['status' => "failed", "message" => $response->message]);  
            }
            
        }else{
            return response()->json(['status' => "failed", "message" => "Something went wrongs"]);
        }
    }
}
