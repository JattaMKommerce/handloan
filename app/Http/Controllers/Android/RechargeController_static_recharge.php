<?php

namespace App\Http\Controllers\Android;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Provider;
use App\User;
use App\Models\Report;
use Carbon\Carbon;
use App\Models\Api;
use MiladRahimi\Jwt\Cryptography\Algorithms\Hmac\HS256;
use MiladRahimi\Jwt\JwtGenerator;
use Illuminate\Support\Facades\Validator;


class RechargeController extends Controller
{
    protected $api;
    public function __construct()
    {
        $this->api = Api::where('code', 'recharge2')->first();
    }

    public function providersList(Request $post)
    {
        $rules["type"] = ['required'];
        $rules["user_id"] = ['required'];
        $rules["apptoken"] = ['required'];

        $validator = Validator::make($post->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['statuscode' => 'ERR', 'message' => $validator->errors()->first()]);
        }
        if ($post->user_id == null) {
            return response()->json(['statuscode' => "ERR", "message" => "User Not Found"]);
        }

        $providers = Provider::where('type', $post->type)->where('status', "1")->orderBy('name')->get(['id', 'name']);
        return response()->json(['statuscode' => "TXN", 'message' => "Provider Fetched Successfully", 'data' => $providers]);
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
        if (!$user) {
            $output['statuscode'] = "ERR";
            $output['status'] = "error";
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
            return response()->json(['statuscode' => "ERR", "message" => "Transaction Pin is incorrect"]);
        }
        do {
            $post['txnid'] = $this->transcode() . rand(11, 99) . Carbon::now()->timestamp;
        } while (Report::where("txnid", "=", $post->txnid)->first() instanceof Report);
        try {
            switch ($provider->api->code) {
                case 'recharge1':
                    $url = $provider->api->url . "/pay?token=" . $provider->api->username . "&number=" . $post->number . "&operator=" . $provider->recharge1 . "&amount=" . $post->amount . "&apitxnid=" . $post->txnid;
                    $header = [];
                    $query = '';
                    $method = "POST";

                    break;
                case 'recharge4':
                    $gpsdata = geoip($post->ip());
                    $latlong = $gpsdata->lat . ',' . $gpsdata->lon;

                    do {
                        $post['txnid'] = $this->transcode() . rand(1111111111, 9999999999);
                    } while (Report::where("txnid", "=", $post->txnid)->first() instanceof Report);
                    $url = $provider->api->url . "TransactionAPI?UserID=" . $provider->api->username . "&Token=" . $provider->api->password . "&Account=" . $post->number . "&SPKey=" . $provider->recharge4 . "&Amount=" . $post->amount . "&APIRequestID=" . $post->txnid . "&Optional1=&Optional2=&Optional3=&Optional4=&CustomerNumber=8910992282&Pincode=743129&Format=1&GEOCode=" . $latlong;



                    $query = json_encode([]);
                    $header = [];
                    $method = "GET";
                    break;
                case 'recharge3':
                    // $ip_server = $_SERVER['SERVER_ADDR'];
                    $gpsdata = geoip($post->ip());

                    do {
                        $post['txnid'] = $this->transcode() . rand(1111111111, 9999999999);
                    } while (Report::where("txnid", "=", $post->txnid)->first() instanceof Report);

                    $parameter['mmusercode'] = $provider->api->optional1;
                    $parameter['phoneno'] = $post->number;
                    $parameter['operatorid'] = $provider->recharge3;
                    $parameter['amount'] = $post->amount;
                    $parameter['rechargetype'] = "1";
                    $parameter['switchto'] = "2";
                    $parameter['latitude'] = sprintf('%0.4f', $gpsdata->lat);
                    $parameter['longitude'] = sprintf('%0.4f', $gpsdata->lon);
                    $parameter['ip'] = $post->ip();
                    $parameter['routetype'] = "web";
                    $parameter['clientrefid'] = $post->txnid;
                    $url = $provider->api->url . "Recharges/Process";
                    $query = '';
                    $header = [];
                    $method = "post";
                    break;
                case 'recharge2':
                    $url = $provider->api->url . "recharge/dorecharge";
                    $parameter = [
                        "operator" => $provider->recharge3,
                        "canumber" => $post->number,
                        "amount" => $post->amount,
                        "referenceid" => $post->txnid
                    ];


                    $header = array(
                        "Cache-Control: no-cache",
                        "Content-Type: application/json",
                        "Token: " . $token['token'],
                        "Authorisedkey: " . $provider->api->optional3
                    );

                    $query = json_encode($parameter);
                    $header = [];
                    $method = "POST";
                    // $result = \Myhelper::curl($url, "POST", json_encode($parameter),$header , "yes", "App\Models\Report", $post->txnid);
                    /// dd(json_encode($parameter),$header,$url,$result);

                    //$query = json_encode($parameter);
                    break;

                case 'recharge5':
                    $gpsdata = geoip($post->ip());
                    $latlong = $gpsdata->lat . ',' . $gpsdata->lon;

                    do {
                        $post['txnid'] = $this->transcode() . rand(1111111111, 9999999999);
                    } while (Report::where("txnid", "=", $post->txnid)->first() instanceof Report);
                    $url = $provider->api->url . "TransactionAPI?UserID=" . $provider->api->username . "&Token=" . $provider->api->password . "&Account=" . $post->number . "&SPKey=" . $provider->recharge4 . "&Amount=" . $post->amount . "&APIRequestID=" . $post->txnid . "&Optional1=&Optional2=&Optional3=&Optional4=&CustomerNumber=8910992282&Pincode=743129&Format=1&GEOCode=" . $latlong;
                    $header = [];
                    $query = '';
                    $method = "POST";

                    break;

                default:
                    return response()->json(['status' => 'Api down for Sometime.']);
                    break;

            }
        } catch (\Exception $e) {
            return response()->json(['statuscode' => "ERR", "message" => "something went wrong, try after sometime"]);
        }

        $previousrecharge = Report::where('number', $post->number)->where('amount', $post->amount)->where('provider_id', $post->provider_id)->whereBetween('created_at', [Carbon::now()->subMinutes(2)->format('Y-m-d H:i:s'), Carbon::now()->format('Y-m-d H:i:s')])->count();
        if ($previousrecharge > 0) {
            return response()->json(['statuscode' => "ERR", "message" => 'Same Transaction allowed after 2 min.'], 400);
        }

        $post['profit'] = \Myhelper::getCommission($post->amount, $user->scheme_id, $post->provider_id, $user->role->slug);
        $debit = User::where('id', $user->id)->decrement('mainwallet', $post->amount - $post->profit);
        if ($debit) {

            $insert = [
                'number' => $post->number,
                'mobile' => $user->mobile,
                'provider_id' => $provider->id,
                'api_id' => $provider->api->id,
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
                'product' => 'recharge'
            ];

            $report = Report::create($insert);

            // dd($url, $method, $query, $header, "yes", "App\Models\Report", $post->txnid);

            // if (env('APP_ENV') == "local") {
            //     $result = \Myhelper::curl($url, $method, $query, $header, "yes", "App\Models\Report", $post->txnid);
            // } else {
            //     $result = [
            //         'error' => true,
            //         'response' => 'local env'
            //     ];
            // }

            if (env('APP_ENV') == "server") {
                if ($provider->api->code == 'recharge3') {
                    $header = array(
                        "cache-control: no-cache",
                        "content-type: application/json",
                        "secretkey:" . $provider->api->password,
                        "saltkey:" . $provider->api->username
                    );
                    $result = \Myhelper::curl($url, "POST", json_encode($parameter), $header, "yes", "App\Model\Report", $post->txnid);
                    sleep(5);


                } else {
                    $result = \Myhelper::curl($url, "GET", "", [], "yes", "App\Model\Report", $post->txnid);
                }


            } else {
                $result = [
                    'error' => true,
                    'response' => ''
                ];
            }


            if ($result['error'] || $result['response'] == '') {
                $update['status'] = "pending";
                $update['payid'] = "pending";
                $update['refno'] = "pending";
                $update['description'] = "recharge pending";
            } else {
                switch ($provider->api->code) {
                    case 'recharge1':
                        $doc = json_decode($result['response']);
                        if (isset($doc->status)) {
                            if ($doc->status == "TXN" || $doc->status == "TUP") {
                                $update['status'] = "success";
                                $update['payid'] = $doc->payid;
                                $update['refno'] = $doc->refno;
                                $update['description'] = "Recharge Accepted";
                            } elseif ($doc->status == "TXF") {
                                $update['status'] = "failed";
                                $update['payid'] = $doc->payid;
                                $update['refno'] = $doc->refno;
                                $update['description'] = (isset($doc->message)) ? $doc->message : "failed";
                            } else {
                                $update['status'] = "failed";
                                if (isset($doc->message) && $doc->message == "Insufficient Wallet Balance") {
                                    $update['description'] = "Service down for sometime.";
                                } else {
                                    $update['description'] = (isset($doc->message)) ? $doc->message : "failed";
                                }
                            }
                        } else {
                            $update['status'] = "pending";
                            $update['payid'] = "pending";
                            $update['refno'] = "pending";
                            $update['description'] = "recharge pending";
                        }
                        break;

                    case 'recharge2':

                        \DB::table('rp_log')->insert([
                            'ServiceName' => "Recharge",
                            'header' => json_encode($header),
                            'body' => json_encode($parameter),
                            'response' => $result['response'],
                            'url' => $url,
                            'created_at' => date('Y-m-d H:i:s')
                        ]);

                        $doc = json_decode($result['response']);
                        if (isset($doc->response_code) && in_array($doc->response_code, [1, 3])) {
                            $update['status'] = "success";
                            $update['payid'] = $doc->ackno;
                            $update['refno'] = $doc->operatorid;
                        } elseif (isset($doc->response_code) && in_array($doc->response_code, [0, 2, 4, 5, 6, 7, 8, 9, 10, 11, 12, 16, 18])) {
                            $update['status'] = "failed";
                            $update['refno'] = $doc->message;
                            if ($doc->message == "Insufficient fund in your account. Please topup your wallet before initiating transaction.") {
                                $update['refno'] = "Service down for sometime";
                            }
                        } else {
                            $update['status'] = "pending";
                            $update['refno'] = "Please wait for status change or contact service provider";
                        }
                        break;

                    case 'recharge3':
                        $doc = json_decode($result['response']);
                        if (isset($doc->statuscode)) {
                            if (strtolower($doc->statuscode) == "000") {
                                $update['status'] = "success";
                                $update['payid'] = $doc->txnid;
                                $update['refno'] = $doc->operatorid;
                                $update['description'] = "Recharge Accepted";
                            } elseif (strtolower($doc->statuscode) == "001") {
                                $update['status'] = "failed";
                                $update['payid'] = isset($doc->txnid) ? $doc->txnid : '';
                                $update['refno'] = (isset($doc->operatorid)) ? $doc->operatorid : "failed";
                                //$update['description'] = (isset($doc->message)) ? $doc->message : "failed";
                                if (isset($doc->message) && $doc->message == "Insufficient balance") {
                                    $update['description'] = "Service down for sometime.";
                                } else {
                                    $update['description'] = (isset($doc->message)) ? $doc->message : "failed";
                                }
                            } else {
                                $update['status'] = "pending";
                                $update['payid'] = (isset($doc->txnid)) ? $doc->txnid : "pending";
                                $update['refno'] = (isset($doc->operatorid)) ? $doc->operatorid : "pending";
                                $update['description'] = (isset($doc->message)) ? $doc->message : "failed";
                            }
                        } else {
                            $update['status'] = "pending";
                            $update['payid'] = "pending";
                            $update['refno'] = "pending";
                            $update['description'] = "recharge pending";
                        }
                        break;
                    case 'recharge4':
                        $doc = json_decode($result['response']);
                        if (isset($doc->status)) {
                            if ($doc->status == "2" || $doc->status == "1") {
                                $update['status'] = "success";
                                $update['payid'] = $doc->rpid;
                                $update['refno'] = $doc->opid;
                                $update['description'] = "Recharge Accepted";
                            } elseif ($doc->status == "3") {
                                $update['status'] = "failed";
                                $update['payid'] = $doc->rpid;
                                $update['refno'] = $doc->opid ?? $doc->msg;
                                $update['description'] = (isset($doc->msg)) ? $doc->msg : "failed";
                            } else {
                                $update['status'] = "failed";
                                if (isset($doc->MSG) && $doc->MSG == "Insufficient Wallet Balance") {
                                    $update['description'] = "Service down for sometime.";
                                } else {
                                    $update['description'] = (isset($doc->msg)) ? $doc->msg : "failed";
                                }
                            }
                        } else {
                            $update['status'] = "pending";
                            $update['payid'] = "pending";
                            $update['refno'] = "pending";
                            $update['description'] = "recharge pending";
                        }
                        break;

                    case 'recharge5':

                        $doc = json_decode($result['response']);

                        if (isset($doc->status)) {
                            if ($doc->status == "2" && $doc->errorcode == "200") {
                                $update['status'] = "success";
                                $update['payid'] = $doc->rpid;
                                $update['refno'] = $doc->opid;
                                $update['description'] = "Recharge Accepted";
                            } elseif ($doc->status == "3") {
                                $update['status'] = "failed";
                                $update['payid'] = $doc->rpid;
                                $update['refno'] = $doc->opid;
                                $update['description'] = (isset($doc->msg)) ? $doc->msg : "Failed";
                            } elseif ($doc->status == "1") {
                                $update['status'] = "pending";
                                $update['payid'] = $doc->rpid;
                                $update['refno'] = $doc->opid;
                                $update['description'] = (isset($doc->msg)) ? $doc->msg : "Pending";
                            } else {
                                $update['status'] = "failed";
                                if (isset($doc->MSG) && $doc->MSG == "Insufficient Wallet Balance") {
                                    $update['description'] = "Service down for sometime.";
                                } else {
                                    $update['description'] = (isset($doc->msg)) ? $doc->msg : "failed";
                                }
                            }
                        } else {
                            $update['status'] = "pending";
                            $update['payid'] = "pending";
                            $update['refno'] = "pending";
                            $update['description'] = "recharge pending";
                        }
                        break;
                    default:
                        return response()->json(["status" => "Contact to Administrator."]);
                        break;
                }
            }

            if ($update['status'] == "success" || $update['status'] == "pending") {
                Report::where('id', $report->id)->update($update);
                \Myhelper::commission($report);
                if ($update['status'] == "pending") {
                    $output['statuscode'] = "TUP";
                    $output['status'] = "Recharge Pending";
                } else {
                    $output['statuscode'] = "TXN";
                    $output['status'] = "Recharge Accepted";
                }
                $output['message'] = "Recharge Accepted";


            } else {
                User::where('id', $user->id)->increment('mainwallet', $post->amount - $post->profit);
                Report::where('id', $report->id)->update($update);
                $output['statuscode'] = "TXF";
                $output['status'] = "failed";

                if ($update['refno'] == "" || $update['refno'] == null || empty($update['refno'])) {
                    $output['message'] = "Payment Failed. Please try again later or contact administrator.";
                } else {
                    $output['message'] = $update['refno'];

                }

            }
            $output['txnid'] = $post->txnid;
            $output['rrn'] = $update['refno'] ?? "Please try again later";
            return response()->json($output);
        } else {
            return response()->json(['statuscode' => "ERR", "message" => "Something went wrong"]);
        }
    }

    public function status(Request $post)
    {
        $rules = array(
            'apptoken' => 'required',
            'user_id' => 'required|numeric',
            'txnid' => 'required|numeric'
        );

        $validate = \Myhelper::FormValidator($rules, $post);
        if ($validate != "no") {
            return $validate;
        }

        $user = User::where('id', $post->user_id)->first();
        if (!$user) {
            $output['statuscode'] = "ERR";
            $output['status'] = "error";
            $output['message'] = "User details not matched";
            return response()->json($output);
        }

        if (!\Myhelper::can('recharge_status', $user->id)) {
            return response()->json(['statuscode' => "ERR", "message" => "Service Not Allowed"]);
        }

        $report = Report::where('id', $post->txnid)->first();

        if (!$report || !in_array($report->status, ['pending', 'success'])) {
            return response()->json(['statuscode' => "ERR", 'message' => "Recharge Status Not Allowed"]);
        }

        switch ($report->api->code) {
            // case 'recharge1':
            //     $method = "GET";
            //     $parameter = "";
            //     $header = [];
            //     $url = $report->api->url . '/status?token=' . $report->api->username . '&apitxnid=' . $report->txnid;
            //     break;
            // case 'recharge2':
            //     $url = $report->api->url . "recharge/status";
            //     $method = "POST";
            //     $parameter = json_encode(
            //         array(
            //             'referenceid' => $report->txnid,
            //         )
            //     );

            //     $payload = [
            //         "timestamp" => time(),
            //         "partnerId" => $report->api->username,
            //         "reqid" => $report->user_id . Carbon::now()->timestamp
            //     ];

            //     $key = $report->api->password;
            //     $signer = new HS256($key);
            //     $generator = new JwtGenerator($signer);
            //     $header = array(
            //         "Cache-Control: no-cache",
            //         "Content-Type: application/json",
            //         "Token: " . $generator->generate($payload),
            //         "Authorisedkey: " . $report->api->optional3
            //     );
            //     // dd($url,$parameter,$header) ;
            //     break;
            // default:
            //     return response()->json(['statuscode' => "ERR", "message" => "Recharge Status Not Allowed"]);
            //     break;

            case 'recharge1':
                $url = $report->api->url . '/status?token=' . $report->api->username . '&apitxnid=' . $report->txnid;
                $method = "GET";
                $parameter = "";
                $header = [];
                break;

            case 'recharge2':
                $url = "https://api.paysprint.in/api/v1/service/recharge/recharge/status"; //$report->api->url."recharge/status";
                //$url = 'https://paysprint.in/service-api/api/v1/service/recharge/recharge/status' ;
                $method = "POST";
                $parameter = json_encode(
                    array(
                        'referenceid' => $report->txnid,
                    )
                );

                $payload = [
                    "timestamp" => time(),
                    "partnerId" => $report->api->username,
                    "reqid" => $report->user_id . Carbon::now()->timestamp
                ];

                $key = $report->api->password;
                $signer = new HS256($key);
                $generator = new JwtGenerator($signer);
                $header = array(
                    "Cache-Control: no-cache",
                    "Content-Type: application/json",
                    "Token: " . $generator->generate($payload),
                    "Authorisedkey: " . $report->api->optional3
                );
                // dd($url,$parameter,$header) ;
                break;
            case 'recharge5':
                $url = $report->api->url . 'StatusCheck?UserID=' . $report->api->username . '&Token=' . $report->api->password . '&RPID=' . $report->payid . '&AGENTID=' . $report->txnid;
                $method = "GET";
                $parameter = "";
                $header = [];
                break;
            default:
                return response()->json(['statuscode' => "ERR", 'message' => "Recharge Status Not Allowed"]);
                break;
        }



        if (env('APP_ENV') != "local") {
            $result = \Myhelper::curl($url, $method, $parameter, $header);
        } else {
            $result = [
                'error' => false,
                'response' => json_encode([
                            'statuscode' => 'TXN',
                            'trans_status' => 'success',
                            'refno' => 'local',
                            'message' => 'local'
                        ])
            ];
        }
        if ($result['response'] != '') {
            switch ($report->api->code) {
                //     case 'recharge1':
                //         $doc = json_decode($result['response']);
                //         if ($doc->statuscode == "TXN" && ($doc->trans_status == "success" || $doc->trans_status == "pending")) {
                //             $update['refno'] = $doc->refno;
                //             $update['status'] = "success";

                //             $output['statuscode'] = "TXN";
                //             $output['txn_status'] = "success";
                //             $output['refno'] = $doc->refno;

                //         } elseif ($doc->statuscode == "TXN" && $doc->trans_status == "reversed") {
                //             $update['status'] = "reversed";
                //             $update['refno'] = $doc->refno;

                //             $output['statuscode'] = "TXR";
                //             $output['txn_status'] = "reversed";
                //             $output['refno'] = $doc->refno;
                //         } else {
                //             $update['status'] = "Unknown";
                //             $update['refno'] = $doc->refno;

                //             $output['statuscode'] = "TNF";
                //             $output['txn_status'] = "unknown";
                //             $output['refno'] = $doc->refno;
                //         }
                //         break;
                //     case 'recharge2':
                //         \DB::table('rp_log')->insert([
                //             'ServiceName' => "RechargeStatus",
                //             'header' => json_encode($header),
                //             'body' => json_encode($parameter),
                //             'response' => $result['response'],
                //             'url' => $url,
                //             'created_at' => date('Y-m-d H:i:s')
                //         ]);

                //         $doc = json_decode($result['response']);
                //         //	dd($doc,$result['response'],$url, $method, $parameter, $header) ;
                //         if (isset($doc->data->status) && $doc->data->status == "1") {
                //             $update['refno'] = $doc->data->operatorid ?? $report->refno;
                //             $update['status'] = "success";
                //             $output['statuscode'] = "TXN";

                //             $output['txn_status'] = "success";
                //             $output['refno'] = $doc->data->operatorid ?? $report->refno;
                //         } elseif (isset($doc->data->status) && $doc->data->status == "0") {
                //             $update['status'] = "reversed";
                //             $update['refno'] = (isset($doc->data->operatorid)) ? $doc->data->operatorid : "failed";

                //             $output['statuscode'] = "TXR";
                //             $output['txn_status'] = "reversed";
                //             $output['refno'] = $doc->data->operatorid ?? $report->refno;
                //         } else {
                //             $update['status'] = "Unknown";
                //             $update['refno'] = (isset($doc->data->operatorid)) ? $doc->data->operatorid : "Unknown";

                //             $output['statuscode'] = "TNF";
                //             $output['txn_status'] = "unknown";
                //             $output['refno'] = (isset($doc->data->operatorid)) ? $doc->data->operatorid : "Unknown";
                //         }
                //         break;
                // }
                case 'recharge1':
                    $doc = json_decode($result['response']);
                    if ($doc->statuscode == "TXN" && ($doc->trans_status == "success" || $doc->trans_status == "pending")) {
                        $update['refno'] = $doc->refno;
                        $update['status'] = "success";
                        $update['description'] = "Recharge " . $doc->trans_status;

                    } elseif ($doc->statuscode == "TXN" && $doc->trans_status == "reversed") {
                        $update['status'] = "reversed";
                        $update['refno'] = $doc->refno;
                        $update['description'] = "Recharge reversed";

                    } else {
                        $update['status'] = "Unknown";
                        $update['refno'] = $doc->message;
                        $update['description'] = "Recharge unknown";

                    }
                    break;

                case 'recharge2':
                    \DB::table('rp_log')->insert([
                        'ServiceName' => "RechargeStatus",
                        'header' => json_encode($header),
                        'body' => json_encode($parameter),
                        'response' => $result['response'],
                        'url' => $url,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);

                    $doc = json_decode($result['response']);
                    // dd($doc,$result['response'],$url, $method, $parameter, $header) ;
                    if (isset($doc->data->status) && $doc->data->status == "1") {
                        $update['refno'] = $doc->data->operatorid ?? $report->refno;
                        $update['status'] = "success";
                    } elseif (isset($doc->data->status) && $doc->data->status == "0") {
                        $update['status'] = "reversed";
                        $update['refno'] = (isset($doc->data->operatorid)) ? $doc->data->operatorid : "failed";
                    } else {
                        $update['status'] = "Unknown";
                        $update['refno'] = (isset($doc->data->operatorid)) ? $doc->data->operatorid : "Unknown";
                    }
                    break;
                case 'recharge5':

                    $doc = json_decode($result['response']);

                    if ($doc->status == "2") {
                        $update['status'] = "success";
                        $update['payid'] = $doc->rpid;
                        $update['refno'] = $doc->opid;
                        $update['description'] = "Recharge Accepted";
                    } elseif ($doc->status == "3") {
                        $update['status'] = "reversed";
                        $update['payid'] = $doc->rpid;
                        $update['refno'] = $doc->opid;
                        $update['description'] = (isset($doc->MSG)) ? $doc->MSG : "Failed";
                    } elseif ($doc->status == "1") {
                        $update['status'] = "pending";
                        $update['payid'] = $doc->rpid;
                        $update['refno'] = $doc->opid;
                        $update['description'] = (isset($doc->MSG)) ? $doc->MSG : "Pending";
                    } else {
                        $update['status'] = "Unknown";
                        $update['refno'] = (isset($doc->data->operatorid)) ? $doc->data->operatorid : "Unknown";
                    }
                    //dd($update,$doc,$result['response'],$url, $method, $parameter, $header) ;
                    break;
            }
            $product = "recharge";

            if ($update['status'] != "Unknown") {
                $reportupdate = Report::where('id', $report->id)->update($update);
                if ($reportupdate && $update['status'] == "reversed") {
                    \Myhelper::transactionRefund($post->id);
                    $update['statuscode'] = "TXR";
                    $update['message'] = $update['description'];
                    unset($update['description']);

                }

                if ($report->user->role->slug == "apiuser" && $report->status == "pending" && $post->status != "pending") {
                    \Myhelper::callback($report, $product);
                    $update['statuscode'] = "TXN";
                    $update['message'] = $update['description'];
                    unset($update['description']);
                }
            }
            return response()->json($update);
        } else {
            return response()->json(['statuscode' => "ERR", "message" => "Something went wrong, contact your service provider"]);
        }
    }

    public function getplan(Request $post)
    {
        // dd($post->all());

        $rules["circle"] = ['required'];
        $rules["user_id"] = ['required'];
        $rules["apptoken"] = ['required'];
        $rules["provider_id"] = ['required'];
        $rules["type"] = ['required', "in:dth,mobile"];

        $validator = Validator::make($post->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['statuscode' => 'ERR', 'message' => $validator->errors()->first()]);
        }


        $provider = Provider::where('id', $post->provider_id)->first();
        if (!$provider) {
            return response()->json(["statuscode" => "ERR", "message" => "Operator Not Found",]);
        }

        $apis = Api::where('code', 'recharge3')->first();

        //$url = "http://securepayments.net.in/api/recharge/getplan?token=".$provider->api->username."&operator=".$provider->recharge1;
        $header = array(
            "cache-control: no-cache",
            "content-type: application/json",
            "secretkey:" . $apis->password,
            "saltkey:" . $apis->username
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

        if ($result['response'] != '') {
            $response = json_decode($result['response']);

            if (isset($response->statuscode) && $response->statuscode == 000) {
                return response()->json(["statuscode" => 'TXN', "message" => "plan fetch successfully", "data" => $response->data]);
            }

            return response()->json(["statuscode" => 'TXF', "message" => $response->message ?? "Something went wrong"]);
        } else {
            return response()->json(["statuscode" => 'TXF', "message" => "Something went wrongs"]);
        }
    }










    // public function getplan(Request $post)
    // {
    //     $url = "https://api.paysprint.in/api/v1/service/recharge/hlrapi/browseplan";
    //     $parameter = [
    //         "op" => $post->providername,
    //         "circle" => $post->circle
    //     ];

    //     $token = $this->getToken($post->user_id . "MP" . Carbon::now()->timestamp);
    //     $header = array(
    //         "Cache-Control: no-cache",
    //         "Content-Type: application/json",
    //         "Token: " . $token['token'],
    //         "Authorisedkey: " . $this->api->optional3
    //     );


    //     $query = json_encode($parameter);
    //     $method = "POST";

    //     $result = \Myhelper::curl($url, $method, $query, $header, "no");
    //     //dd($url, $parameter, $result);
    //     \DB::table('rp_log')->insert([
    //         'ServiceName' => "Recharge Plan",
    //         'header' => json_encode($header),
    //         'body' => json_encode($query),
    //         'response' => $result['response'],
    //         'url' => $url,
    //         'created_at' => date('Y-m-d H:i:s')
    //     ]);
    //     if ($result['response'] != '') {
    //         $response = json_decode($result['response']);
    //         if (isset($response->response_code) && $response->response_code == "1") {
    //             return response()->json(['status' => "success", "data" => $response->info], 200);
    //         }
    //         return response()->json(['status' => "failed", "message" => "Something went wrong"]);
    //     } else {
    //         return response()->json(['status' => "failed", "message" => "Something went wrongs"]);
    //     }
    // }


    //  public function getoperator(Request $post)
//     {   
//         if($post->type ="mobile"){
//              $url = "https://api.paysprint.in/api/v1/service/recharge/hlrapi/hlrcheck";
//              $parameter = [
//                 "number" =>  $post->number, 
//                 "type"   =>  "mobile"
//              ];
//         }else{
//               return response()->json(['status' => "failed", "message" => "Something went wrongs"]);
//             $url = "https://api.paysprint.in/api/v1/service/recharge/hlrapi/dthinfo" ;
//              $parameter = [
//             "number" =>  "1369814007", 
//             "op"   =>    "TataSky"
//             ];
//         }

    //        // dd($url,$parameter,$post->all()) ;
//        // $url = "https://paysprint.in/service-api/api/v1/service/recharge/hlrapi/hlrcheck" ;


    //         $token = $this->getToken($post->user_id."OP".Carbon::now()->timestamp);
//         $header = array(
//             "Cache-Control: no-cache",
//             "Content-Type: application/json",
//             "Token: ".$token['token'],
//            "Authorisedkey: OWU3ZjExYjI1YmVhYjkyMGU5ZWRkMmMxYTVmZTYzOWE="
//         );

    //         $query = json_encode($parameter);
//         $method = "POST"; 

    //         $result = \Myhelper::curl($url, $method, $query, $header, "no");

    //          \DB::table('rp_log')->insert([
//             'ServiceName' => "getoprator",
//             'header' => json_encode($header),
//             'body' => json_encode($parameter),
//             'response' => $result['response'],
//              'url' => $url,
//              'created_at' => date('Y-m-d H:i:s')
//           ]);
//        // dd($result,$url,$parameter,$post->all()) ;
//         if($result['response'] != ''){
//             $response = json_decode($result['response']);
//             if(isset($response->response_code) && $response->response_code == "1"){
//                 $provider = Provider::where('name', 'like', '%'.strtolower($response->info->operator).'%')->where('type', $post->type)->first();
//                 //dd($result,$url,$parameter, $provider);
//                 return response()->json(['status' => "success", "provider_id" => $provider->id, "circle" => $response->info->circle, "providername" => $response->info->operator], 200);
//             }
//             return response()->json(['status' => "failed", "message" => "Something went wrong"]);
//         }else{
//             return response()->json(['status' => "failed", "message" => "Something went wrongs"]);
//         }
//     }


    //      public function getToken($uniqueid)
    // {
    //     $payload =  [
    //         "timestamp" => time(),
    //         "partnerId" => $this->api->username,
    //         "reqid"     => $uniqueid
    //     ];

    //         $key = $this->api->password;
    //     $signer = new HS256($key);
    //     $generator = new JwtGenerator($signer);
    //     return ['token' => $generator->generate($payload), 'payload' => $payload];
    // }
    public function getoperator(Request $post)
    {
        $url = "https://api.paysprint.in/api/v1/service/recharge/hlrapi/hlrcheck";
        // $url = "https://paysprint.in/service-api/api/v1/service/recharge/hlrapi/hlrcheck" ;

        $parameter = [
            "number" => $post->number,
            "type" => $post->type
        ];


        $header = array(
            "Cache-Control: no-cache",
            "Content-Type: application/json",
            "Token: " . $token['token'],
            "Authorisedkey: OWU3ZjExYjI1YmVhYjkyMGU5ZWRkMmMxYTVmZTYzOWE="
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
        if ($result['response'] != '') {
            $response = json_decode($result['response']);
            if (isset($response->response_code) && $response->response_code == "1") {
                $provider = Provider::where('name', 'like', '%' . strtolower($response->info->operator) . '%')->where('type', $post->type)->first();
                //dd($result,$url,$parameter, $provider);
                return response()->json(["statuscode" => "TXN", "data" => $provider->id, "circle" => $response->info->circle, "providername" => $response->info->operator]);
            }
            return response()->json(['status' => "failed", "message" => "Something went wrong"]);
        } else {
            return response()->json(['status' => "failed", "message" => "Something went wrongs"]);
        }
    }




    public function getdthinfo(Request $post)
    {
        $rules["number"] = ['required'];
        $rules["user_id"] = ['required'];
        $rules["apptoken"] = ['required'];
        $rules["provider_id"] = ['required'];

        $validator = Validator::make($post->all(), $rules);

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
            "secretkey:" . $apis->password,
            "saltkey:" . $apis->username
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

}