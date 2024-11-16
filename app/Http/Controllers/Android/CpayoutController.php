<?php

namespace App\Http\Controllers\Android;

use App\Http\Controllers\Controller;
use App\Models\Api;
use App\Models\Commission;
use App\Models\Packagecommission;
use App\Models\PortalSetting;
use App\Models\Provider;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CpayoutController extends Controller
{

    protected $api, $cyrus, $runpaisa, $bulkpauout;
    public function __construct()
    {
        $this->api = Api::where('code', 'dmt1')->first();
        $this->cyrus = Api::where('code', 'cyrusfund')->first();
        $this->runpaisa = Api::where('code', 'runpaisafund')->first();
    }

    public function index2($type)
    {
        if (\Myhelper::hasRole('admin') || !\Myhelper::can('dmt1_service')) {
            abort(403);
        }
        if (!in_array($type, ['cyruspayout', 'runpaisa'])) { //['cashfree', 'bulkpayout', 'easebuzz', 'runpaisa'])){
            abort(404);
        }
        switch ($type) {
            case 'cyruspayout':
                if (\Myhelper::can('cyrus_payout_service', \Auth::id())) {
                    $data['type'] = $type;
                    $data['banks'] = \App\Models\Mahabank::get();
                    return view('service.xpayout')->with($data);
                } else {
                    abort(404);
                }
                break;
            case 'runpaisa':
                if (\Myhelper::can('runpaisa_payout_service', \Auth::id())) {
                    $data['type'] = $type;
                    $data['banks'] = \App\Models\Mahabank::get();
                    return view('service.xpayout')->with($data);
                } else {
                    abort(404);
                }
                break;
            default:
                abort(404);
                break;
        }



        // $data['type'] = $type;
        // $data['banks'] = \App\Models\Mahabank::get();
        // return view('service.xpayout')->with($data);
    }

    public function payment(Request $post)
    {
        if (\Myhelper::hasRole('admin') || (!\Myhelper::can('dmt1_service') && $post->type != 'getdistrict')) {
            return \Response::json(['statuscode' => 'ERR', 'status' => "Permission not allowed", 'message' => "Permission not allowed"], 400);
        }


        if (!$this->api || $this->api->status == 0) {
            return response()->json(['statuscode' => 'ERR', 'status' => "Money Transfer Service Currently Down.", 'message' => "Money Transfer Service Currently Down."], 400);
        }


        $post['user_id'] = \Auth::id();
        $userdata = User::where('id', $post->user_id)->first();

        if ($post->type == "transfer") {
            $codes = ['dmt1', 'dmt2', 'dmt3', 'dmt4', 'dmt5'];
            $providerids = [];
            foreach ($codes as $value) {
                $providerids[] = Provider::where('recharge1', $value)->first(['id'])->id;
            }
            if ($this->schememanager() == "admin") {
                $commission = Commission::where('scheme_id', $userdata->scheme_id)->whereIn('slab', $providerids)->get();
            } else {
                $commission = Packagecommission::where('scheme_id', $userdata->scheme_id)->whereIn('slab', $providerids)->get();
            }
            if (!$commission || sizeof($commission) < 5) {
                return response()->json(['statuscode' => 'ERR', 'message' => "Money Transfer charges not set, contact administrator."], 400);
            }
        }

        $validate = $this->myvalidate($post);
        if ($validate['status'] != 'NV') {
            return response()->json($validate, 400);
        }
        $bcid = PortalSetting::where('code', 'bcid')->first();
        $cpid = PortalSetting::where('code', 'cpid')->first();

        if (isset($cpid->value)) {
            $post['cpid'] = $cpid->value;
        } else {
            return response()->json(['statuscode' => 'ERR', 'status' => "CP id not mapped", 'message' => "CP id not mapped"], 400);
        }

        if (isset($bcid->value)) {
            $post['bc_id'] = $bcid->value;
        } else {
            return response()->json(['statuscode' => 'ERR', 'status' => "BC id not mapped", 'message' => "Bc id not mapped"], 400);
        }

        $header = array("Content-Type: application/json");

        switch ($post->type) {
            case 'getdistrict':
                $dis = DB::table('districts')->select('id as districtid', 'district_title as districtname')->where('state_id', $post->stateid)->get();
                return response()->json(['statuscode' => 'TXN', 'status' => 'Transaction Successfull', 'message' => $dis]);
                // $url = "http://Uat.dhansewa.com/Common/GetDistrictByState";
                // $parameter["stateid"] = $post->stateid;
                break;

            case 'verification':
                $url = $this->api->url . "AIRTEL/getairtelbenedetails";
                $parameter["bc_id"] = $post->bc_id;
                $parameter["custno"] = $post->mobile;
                break;

            case 'otp':
                $url = $this->api->url . "AIRTEL/airtelOTP";
                $parameter["bc_id"] = $post->bc_id;
                $parameter["custno"] = $post->mobile;
                break;

            case 'registration':
                $circle = DB::table('circles')->where('state', 'like', '%' . $userdata->state . '%')->first();

                if (!$circle || $userdata->pincode == '' || $userdata->address == '') {
                    return response()->json(['statuscode' => 'ERR', 'message' => "Please update your profile or contact administrator"], 400);
                }

                $url = $this->api->url . "AIRTEL/apiCustRegistration";
                $parameter["bc_id"] = $post->bc_id;
                $parameter["custno"] = $post->mobile;
                $parameter["cust_f_name"] = $post->fname;
                $parameter["cust_l_name"] = $post->lname;
                $parameter["Dob"] = date("d-m") . "-" . rand(1980, 2000);
                $parameter["otp"] = $post->otp;
                $parameter["Address"] = $userdata->address;
                $parameter["pincode"] = $userdata->pincode;
                $parameter["StateCode"] = $circle->statecode;
                $parameter["usercode"] = $post->cpid;
                $parameter["saltkey"] = $this->api->username;
                $parameter["secretkey"] = $this->api->password;
                break;

            case 'addbeneficiary':
                $url = $this->api->url . "AIRTEL/airtelbeneadd";
                $parameter["custno"] = $post->mobile;
                $parameter["bankname"] = $post->benebank;
                $parameter["beneaccno"] = $post->beneaccount;
                $parameter["benemobile"] = $post->benemobile;
                $parameter["benename"] = $post->benename;
                $parameter["ifsc"] = $post->beneifsc;
                break;

            case 'beneverify':
                $url = $this->api->url . "AIRTEL/verifybeneotp";
                $parameter["custno"] = $post->mobile;
                $parameter["otp"] = $post->otp;
                $parameter["beneaccno"] = $post->beneaccount;
                $parameter["benemobile"] = $post->benemobile;
                break;

            case 'accountverification':
                $api = Api::where('code', 'runpaisa_validate')->first();
                $url = $api->optional2 . "/account";
                $post['amount'] = 1;
                $provider = Provider::where('recharge1', 'dmt1accverify')->first();
                $post['charge'] = \Myhelper::getCommission($post->amount, $userdata->scheme_id, $provider->id, $userdata->role->slug);
                $post['provider_id'] = $provider->id;
                if ($userdata->mainwallet < $post->amount + $post->charge) {
                    return response()->json(["statuscode" => "IWB", 'status' => 'Low balance, kindly recharge your wallet.', 'message' => 'Low balance, kindly recharge your wallet.'], 400);
                }


                $parameter["account"] = $post->beneaccount;
                $parameter["ifsc"] = $post->beneifsc;
                do {
                    $post['txnid'] = $this->transcode() . rand(1111111111, 9999999999);
                } while (Report::where("txnid", "=", $post->txnid)->first() instanceof Report);
                $parameter["clientrefno"] = $post->txnid;
                $token = $this->getRunpaisaToken();
                $header = array(
                    'Content-Type:multipart/form-data',
                    'client_id: ' . $api->optional1,
                    'token:' . $token
                );

                break;

            case 'transfer':

                if ($this->pinCheck($post) == "fail") {
                    return response()->json(['status' => "Transaction Pin is incorrect"], 400);
                }


                $bankpayoutapi = $post->payoutapi; //$this->payoutapi();
                if ($bankpayoutapi == "cyruspayout") {

                    if (!$this->cyrus || $this->cyrus->status == 0) {
                        return response()->json(['statuscode' => "ERR", "message" => "Service Currently Down"], 400);
                    }
                    return $this->cashfreetrasfer($post);
                } else if ($bankpayoutapi == "runpaisa") {

                    if (!$this->runpaisa || $this->runpaisa->status == 0) {
                        return response()->json(['statuscode' => "ERR", "message" => "Service Currently Down"], 400);
                    }
                    return $this->runpaisatransfer($post);
                } else {
                    return response()->json(['status' => "Currently Service is Down"], 400);
                }



                // return $this->transfer($post);
                break;

            default:
                return response()->json(['statuscode' => 'BPR', 'status' => 'Bad Parameter Request', 'message' => "Bad Parameter Request"]);
                break;
        }

        if ($post->type != "accountverification") {
            $result = \Myhelper::curl($url, "POST", json_encode($parameter), $header, "no", 'App\Model\Report', '0');
        } else {
            if ($post->type == "accountverification") {

                $result = \Myhelper::curl(
                    $url,
                    "POST",
                    $parameter,
                    $header,
                    "yes",
                    'App\Models\Report',
                    $post->txnid
                );
            } else {
                $result = \Myhelper::curl(
                    $url,
                    "POST",
                    json_encode($parameter),
                    $header,
                    "yes",
                    'App\Models\Report',
                    $post->txnid
                );
            }
            //    $result = \Myhelper::curl($url, "GET", $query, $header, "yes", 'App\Model\Report', $post->txnid);
        }

        /*if(\Auth::id() == "2"){
            dd([$url, $parameter , $result]);
        }*/

        if ($result['error'] && $result['response'] == "") {
            if ($post->type == "accountverification") {
                //     $response = [
                //         "message"=>"Success",
                //         "statuscode"=>"001",
                //         "availlimit"=>"0",
                //         "total_limit"=>"0",
                //         "used_limit"=>"0",
                //         "Data"=>[["fesessionid"=>"CP1801861S131436",
                //         "tranid"=>"pending",
                //         "rrn"=>"pending",
                //         "externalrefno"=>"MH357381218131436",
                //         "amount"=>"0",
                //         "responsetimestamp"=>"0",
                //         "benename"=>"",
                //         "messagetext"=>"Success",
                //         "code"=>"1",
                //         "errorcode"=>"1114",
                //         "mahatxnfee"=>"10.00"
                //         ]]
                //     ];

                //     return $this->output($post, json_encode($response), $userdata);
            }

            return response()->json(["statuscode" => "ERR", 'status' => 'System Error', 'message' => 'System Error'], 400);
        }

        return $this->output($post, $result['response'], $userdata);
    }

    public function myvalidate($post)
    {
        $validate = "yes";
        switch ($post->type) {
            case 'getdistrict':
                $rules = array('stateid' => 'required|numeric');
                break;

            case 'verification':
            case 'otp':
                $rules = array('user_id' => 'required|numeric', 'mobile' => 'required|numeric|digits:10');
                break;

            case 'registration':
                $rules = array('user_id' => 'required|numeric', 'mobile' => 'required|numeric|digits:10', 'fname' => 'required|regex:/^[\pL\s\-]+$/u', 'lname' => 'required|regex:/^[\pL\s\-]+$/u', 'otp' => "required|numeric", 'pincode' => "required|numeric|digits:6");
                break;

            case 'addbeneficiary':
                $rules = array('user_id' => 'required|numeric', 'mobile' => 'required|numeric|digits:10', 'benebank' => 'required', 'beneifsc' => "required", 'beneaccount' => "required|numeric|digits_between:6,20", "benemobile" => 'required|numeric|digits:10', "benename" => "required|regex:/^[\pL\s\-]+$/u");
                break;

            case 'beneverify':
                $rules = array('user_id' => 'required|numeric', 'mobile' => 'required|numeric|digits:10', 'beneaccount' => "required|numeric|digits_between:6,20", "benemobile" => 'required|numeric|digits:10', "otp" => 'required|numeric');
                break;

            case 'accountverification':
                $rules = array('user_id' => 'required|numeric', 'mobile' => 'required|numeric|digits:10', 'benebank' => 'required', 'beneifsc' => "required", 'beneaccount' => "required|numeric|digits_between:6,20", "benemobile" => 'required|numeric|digits:10', "benename" => "required|regex:/^[\pL\s\-]+$/u");
                break;

            case 'transfer':
                $rules = array('user_id' => 'required|numeric', 'name' => 'required', 'mobile' => 'required|numeric|digits:10', 'benebank' => 'required', 'beneifsc' => "required", 'beneaccount' => "required|numeric|digits_between:6,20", "benemobile" => 'required|numeric|digits:10', "benename" => "required", 'amount' => 'required|numeric|min:10|max:200000');
                break;

            default:
                return ['statuscode' => 'BPR', "status" => "Bad Parameter Request", 'message' => "Invalid request format"];
                break;
        }

        if ($validate == "yes") {
            $validator = \Validator::make($post->all(), $rules);
            if ($validator->fails()) {
                foreach ($validator->errors()->messages() as $key => $value) {
                    $error = $value[0];
                }
                $data = ['statuscode' => 'BPR', "status" => "Bad Parameter Request", 'message' => $error];
            } else {
                $data = ['status' => 'NV'];
            }
        } else {
            $data = ['status' => 'NV'];
        }
        return $data;
    }

    public function runpaisatransfer($post)
    {

        $authToken = $this->getRunpaisaTokenPayout();

        $totalamount = $post->amount;

        $amount = $post->amount;

        if (!$post->mode) {
            $post->mode = 'imps';
        }

        $outputs['statuscode'] = "TXN";
        $post['amount'] = $amount;
        $user = User::where('id', $post->user_id)->first();
        $avlbalance = $user->mainwallet;

        if ($post->amount >= 0 && $post->amount <= 25000) {
            $provider = Provider::where('recharge1', 'pgdmt1')->first();
        } elseif ($amount > 25001 && $amount <= 50000) {
            $provider = Provider::where('recharge1', 'pgdmt2')->first();
        } else {
            $provider = Provider::where('recharge1', 'pgdmt2')->first();
        }
        $post['provider_id'] = $provider->id;
        $post['charge'] = \Myhelper::getCommission($post->amount, $user->scheme_id, $post->provider_id, $user->role->slug);

        if ($avlbalance < $post->amount + $post->charge) {
            $outputs['data'][] = array(
                'amount' => $amount,
                'status' => 'TXF',
                'data' => [
                    "statuscode" => "TXF",
                    "status" => "Insufficient Wallet Balance",
                    "message" => "Insufficient Wallet Balance",
                ]
            );
        } else {
            $post['amount'] = $amount;

            do {
                $post['txnid'] = $this->transcode() . rand(1111111111, 9999999999);
            } while (Report::where("txnid", "=", $post->txnid)->first() instanceof Report);

            $post['service'] = $provider->type;

            $bank = Mahabank::where('bankid', $post->benebank)->first();

            $insert = [
                'api_id' => $this->runpaisa->id,
                'provider_id' => $post->provider_id,
                'option1' => $post->name,
                'mobile' => $post->mobile,
                'number' => $post->beneaccount,
                'option2' => $post->benename,
                'option3' => $bank->bankname ?? $post->benebank,
                'option4' => $post->beneifsc ?? '',
                'option5' => $post->benemobile,
                'txnid' => $post->txnid,
                'amount' => $post->amount,
                'charge' => $post->charge,
                'remark' => "Money Transfer",
                'status' => 'pending',
                'user_id' => $user->id,
                'credit_by' => $user->id,
                'product' => 'payout',
                'balance' => $avlbalance,
                'description' => $post->benemobile,
                'trans_type' => 'debit'
            ];
            // dd($insert);
            $previousrecharge = Report::where('number', $post->beneaccount)->where('amount', $post->amount)->where('provider_id', $post->provider_id)->whereBetween('created_at', [Carbon::now()->subSeconds(1)->format('Y-m-d H:i:s'), Carbon::now()->format('Y-m-d H:i:s')])->count();
            if ($previousrecharge == 0) {
                $transaction = User::where('id', $user->id)->decrement('mainwallet', $post->amount + $post->charge);

                if (!$transaction) {
                    $outputs['data'][] = array(
                        'amount' => $amount,
                        'status' => 'TXF',
                        'data' => [
                            "statuscode" => "TXF",
                            "status" => "Transaction Failed",
                        ]
                    );
                } else {
                    $totalamount = $totalamount - $amount;
                    $report = Report::create($insert);
                    $post['reportid'] = $report->id;
                    $post['amount'] = $amount;

                    $header = array(
                        'token: ' . $authToken,
                        'client_id: ' . $this->runpaisa->optional1,
                        'Content-Type: multipart/form-data'
                    );

                    $url = $this->runpaisa->optional2 . "/payment";

                    $request = [
                        "amount" => $post->amount,
                        "orderId" => $post->txnid,
                        "paymentMode" => strtoupper($post->mode),
                        "callbackurl" => url('') . '/api/callback/update/runpaisa',
                        "beneficiaryName" => $post->benename,
                        "beneficiaryAccountNumber" => $post->beneaccount,
                        "beneficiaryIfscCode" => $post->beneifsc,
                    ];
                    $payload = json_encode($request);
                    if (env('APP_ENV') == "server") {
                        $result = \Myhelper::curl($url, "POST", $request, $header, 'yes', 'Payout', $post->txnid);
                    } else {
                        $result = [
                            'error' => true,
                            'response' => ''
                        ];
                    }

                    // dd([$url,$payload, $header,$result]);

                    if (env('APP_ENV') == "local" || $result['error'] || $result['response'] == '') {
                        $result['response'] = json_encode([
                            "message" => "Pending",
                            "statuscode" => "001",
                            "availlimit" => "0",
                            "total_limit" => "0",
                            "used_limit" => "0",
                            "Data" => [
                                [
                                    "fesessionid" => "CP1801861S131436",
                                    "tranid" => "pending",
                                    "rrn" => "pending",
                                    "externalrefno" => "MH357381218131436",
                                    "amount" => "0",
                                    "responsetimestamp" => "0",
                                    "benename" => "",
                                    "messagetext" => "Success",
                                    "code" => "1",
                                    "errorcode" => "1114",
                                    "mahatxnfee" => "10.00"
                                ]
                            ]
                        ]);
                    }

                    $outputs['data'][] = array(
                        'amount' => $amount,
                        'status' => 'TXN',
                        'data' => $this->output($post, $result['response'], $user)
                    );
                }
            } else {
                $outputs['data'][] = array(
                    'amount' => $amount,
                    'status' => 'TXF',
                    'data' => [
                        "statuscode" => "TXF",
                        "status" => "Same Transaction Repeat",
                        "message" => "Same Transaction Repeat",
                    ]
                );
            }
        }
        sleep(1);
        return response()->json($outputs, 200);
    }

    public function cashfreetrasfer($post) // cyrus payout function
    {


        $totalamount = $post->amount;

        $url = $this->cyrus->url . '/api/PayoutAPI.aspx';


        $amount = $post->amount;

        if (!$post->mode) {
            $post->mode = 'imps';
        }
        $outputs['statuscode'] = "TXN";
        $post['amount'] = $amount;
        $user = User::where('id', $post->user_id)->first();
        $avlbalance = $user->mainwallet;
        if ($post->amount >= 0 && $post->amount <= 25000) {
            $provider = Provider::where('recharge1', 'pgdmt1')->first();
        } elseif ($amount > 25001 && $amount <= 50000) {
            $provider = Provider::where('recharge1', 'pgdmt2')->first();
        } else {
            $provider = Provider::where('recharge1', 'pgdmt2')->first();
        }
        $post['provider_id'] = $provider->id;
        $post['charge'] = \Myhelper::getCommission($post->amount, $user->scheme_id, $post->provider_id, $user->role->slug);

        if ($avlbalance < $post->amount + $post->charge) {
            $outputs['data'][] = array(
                'amount' => $amount,
                'status' => 'TXF',
                'data' => [
                    "statuscode" => "TXF",
                    "status" => "Insufficient Wallet Balance",
                    "message" => "Insufficient Wallet Balance",
                ]
            );
        } else {
            $post['amount'] = $amount;

            do {
                $post['txnid'] = $this->transcode() . rand(1111111111, 9999999999);
            } while (Report::where("txnid", "=", $post->txnid)->first() instanceof Report);

            $post['service'] = $provider->type;

            $bank = Mahabank::where('bankid', $post->benebank)->first();

            $insert = [
                'api_id' => $this->cyrus->id,
                'provider_id' => $post->provider_id,
                'option1' => $post->name,
                'mobile' => $post->mobile,
                'number' => $post->beneaccount,
                'option2' => $post->benename,
                'option3' => $bank->bankname ?? $post->benebank,
                'option4' => $post->beneifsc ?? '',
                'option5' => $post->benemobile,
                'txnid' => $post->txnid,
                'amount' => $post->amount,
                'charge' => $post->charge,
                'remark' => "Money Transfer",
                'status' => 'pending',
                'user_id' => $user->id,
                'credit_by' => $user->id,
                'product' => 'payout',
                'balance' => $avlbalance,
                'description' => $post->benemobile,
                'create_time' => $user->id . "-" . date("ymdhis"),
                'trans_type' => 'debit'
            ];
            // dd($insert);
            $previousrecharge = Report::where('number', $post->beneaccount)->where('amount', $post->amount)->where('provider_id', $post->provider_id)->whereBetween('created_at', [Carbon::now()->subSeconds(1)->format('Y-m-d H:i:s'), Carbon::now()->format('Y-m-d H:i:s')])->count();
            if ($previousrecharge == 0) {
                $transaction = User::where('id', $user->id)->decrement('mainwallet', $post->amount + $post->charge);

                if (!$transaction) {
                    $outputs['data'][] = array(
                        'amount' => $amount,
                        'status' => 'TXF',
                        'data' => [
                            "statuscode" => "TXF",
                            "status" => "Transaction Failed",
                        ]
                    );
                } else {
                    $totalamount = $totalamount - $amount;
                    $report = Report::create($insert);
                    $post['reportid'] = $report->id;
                    $post['amount'] = $amount;
                    // $isNumaric = is_numeric($post->beneaccount);
                    // $ifUpi = str_contains($post->beneaccount, "@");
                    // dd([$isNumaric,$ifUpi]);
                    // $parameter['amount'] = $post->amount;
                    // $parameter["transferId"] = $post->txnid;

                    // $parameter['transferMode']     =  strtolower($post->mode); 

                    // $parameter['remarks']            = "Payout";
                    // if ($isNumaric == false && $ifUpi = true) {
                    //     $parameter['beneDetails']['vpa'] = $post->beneaccount;
                    // } else {
                    //     $parameter['beneDetails']['bankAccount'] = $post->beneaccount;
                    //     $parameter['beneDetails']['ifsc'] = $post->beneifsc;
                    // }

                    // $parameter['beneDetails']['name'] = $user->name;
                    // $parameter['beneDetails']['email'] = $user->email;
                    // $parameter['beneDetails']['phone'] = $user->mobile;
                    // $parameter['beneDetails']['address1'] = $user->address; 
                    // $parameter['paymentInstrumentId'] = "ICICI_CONNECTED_11629_d1feb53";
                    // $payload = json_encode($parameter);

                    /******************* Cyrus Post Data **********************/
                    $postData['MerchantID'] = $this->cyrus->username;
                    $postData['MerchantKey'] = $this->cyrus->optional1;
                    $postData['MethodName'] = "sendmoney";
                    $postData['TransferType'] = "IMPS";
                    $postData['Name'] = $user->name;
                    $postData['MobileNo'] = $user->mobile;
                    $postData['beneficiaryIFSC'] = $post->beneifsc;
                    $postData['beneficiaryAccount'] = $post->beneaccount;
                    $postData['comments'] = $post->comments ?? "";
                    $postData['orderId'] = $post->txnid;
                    $postData['amount'] = $post->amount;
                    /*********************End Post Data ***********************/

                    $query = http_build_query($postData, '', '&');

                    // dd($postData);

                    if (env('APP_ENV') == "server") {
                        $result = \Myhelper::curl($url, "POST", $query, [], 'yes', 'Payout', $post->txnid);
                    } else {
                        $result = [
                            'error' => true,
                            'response' => ''
                        ];
                    }
                    \DB::table('rp_log')->insert([
                        'ServiceName' => "CyrusDMT",
                        'header' => json_encode([]),
                        'body' => $query,
                        'response' => json_encode($result),
                        'url' => $url,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);

                    // dd([$url,$payload, $header,$result]);
                    // if (env('APP_ENV') == "local" || $result['error'] || $result['response'] == '') {
                    //     $result['response'] = json_encode([
                    //         "message" => "Pending",
                    //         "statuscode" => "001",
                    //         "availlimit" => "0",
                    //         "total_limit" => "0",
                    //         "used_limit" => "0",
                    //         "Data" => [
                    //             [
                    //                 "fesessionid" => "CP1801861S131436",
                    //                 "tranid" => "pending",
                    //                 "rrn" => "pending",
                    //                 "externalrefno" => "MH357381218131436",
                    //                 "amount" => "0",
                    //                 "responsetimestamp" => "0",
                    //                 "benename" => "",
                    //                 "messagetext" => "Success",
                    //                 "code" => "1",
                    //                 "errorcode" => "1114",
                    //                 "mahatxnfee" => "10.00"
                    //             ]
                    //         ]
                    //     ]);
                    // }

                    $outputs['data'][] = array(
                        'amount' => $amount,
                        'status' => 'TXN',
                        'data' => $this->output($post, $result['response'], $user)
                    );
                }
            } else {
                $outputs['data'][] = array(
                    'amount' => $amount,
                    'status' => 'TXF',
                    'data' => [
                        "statuscode" => "TXF",
                        "status" => "Same Transaction Repeat",
                        "message" => "Same Transaction Repeat",
                    ]
                );
            }
        }
        sleep(1);

        return response()->json($outputs, 200);
    }

    // public function easbuzztransfer($post){

    //   $key= $this->easbuzz->username;
    //   $salt= $this->easbuzz->password; 
    //   $totalamount = $post->amount;
    //   $url = $this->easbuzz->url;
    //   $amount = intval($post->amount);
    //     $outputs['statuscode'] = "TXN";
    //     $post['amount'] = $amount;
    //     $user = User::where('id', $post->user_id)->first();
    //     if ($post->amount >= 0 && $post->amount <= 25000) {
    //         $provider = Provider::where('recharge1', 'pgdmt1')->first();
    //     } elseif ($amount > 25001 && $amount <= 50000) {
    //         $provider = Provider::where('recharge1', 'pgdmt2')->first();
    //     } else {
    //         $provider = Provider::where('recharge1', 'pgdmt2')->first();
    //     }
    //     $post['provider_id'] = $provider->id;
    //     $post['charge'] =  \Myhelper::getCommission($post->amount, $user->scheme_id, $post->provider_id, $user->role->slug);
    //     if ($user->mainwallet < $post->amount + $post->charge) {
    //         $outputs['data'][] = array(
    //             'amount' => $amount,
    //             'status' => 'TXF',
    //             'data' => [
    //                 "statuscode" => "TXF",
    //                 "status" => "Insufficient Wallet Balance",
    //                 "message" => "Insufficient Wallet Balance",
    //             ]
    //         );
    //     } else {
    //         $post['amount'] = $amount;

    //         do {
    //             $post['txnid'] = $this->transcode() . rand(1111111111, 9999999999);
    //         } while (Report::where("txnid", "=", $post->txnid)->first() instanceof Report);

    //         $post['service'] = $provider->type;

    //         $bank = Mahabank::where('bankid', $post->benebank)->first();

    //         $insert = [
    //             'api_id' => $this->easbuzz->id,
    //             'provider_id' => $post->provider_id,
    //             'option1' => $post->name,
    //             'mobile' => $post->mobile,
    //             'number' => $post->beneaccount,
    //             'option2' => $post->benename,
    //             'option3' => $bank->bankname ?? $post->benebank,
    //             'option4' => $post->beneifsc ?? '',
    //             'option5' => $post->benemobile,
    //             'txnid' => $post->txnid,
    //             'amount' => $post->amount,
    //             'charge' => $post->charge,
    //             'remark' => "Money Transfer",
    //             'status' => 'pending',
    //             'user_id' => $user->id,
    //             'credit_by' => $user->id,
    //             'product' => 'payout',
    //             'balance' => $user->mainwallet,
    //             'description' => $post->benemobile,
    //             'trans_type' => 'debit'
    //         ];
    //         // dd($insert);
    //         $previousrecharge = Report::where('number', $post->beneaccount)->where('amount', $post->amount)->where('provider_id', $post->provider_id)->whereBetween('created_at', [Carbon::now()->subSeconds(1)->format('Y-m-d H:i:s'), Carbon::now()->format('Y-m-d H:i:s')])->count();
    //         if ($previousrecharge == 0) {
    //             $transaction = User::where('id', $user->id)->decrement('mainwallet', $post->amount + $post->charge);

    //             if (!$transaction) {
    //                 $outputs['data'][] = array(
    //                     'amount' => $amount,
    //                     'status' => 'TXF',
    //                     'data' => [
    //                         "statuscode" => "TXF",
    //                         "status" => "Transaction Failed",
    //                     ]
    //                 );
    //             } else {
    //                 $totalamount = $totalamount - $amount;
    //                 $report = Report::create($insert);
    //                 $post['reportid'] = $report->id;
    //                 $post['amount'] = $amount;
    //                  if(!$post->mode){
    //                   $post->mode = 'IMPS';
    //                      }  

    //                       $url = "https://wire.easebuzz.in/api/v1/quick_transfers/initiate/"; //$api->url ;
    //                       $key=  "06CB84F212"; //$api->username;
    //                       $salt= "7983FA33A7"; //$api->password; 
    //                       $string=$key."|".$post->beneaccount."|".$post->beneifsc."||".$post->txnid."|".$amount."|".$salt;

    //                       $hash = hash('sha512', $string);
    //                       $header = array(
    //                           'Authorization: '.$hash,
    //                           'Content-Type: application/json',
    //                           'WIRE-API-KEY : '.$key
    //                       );
    //                      $parameter = array (
    //                         "key" => $key,
    //                         'beneficiary_type' => "bank_account",
    //                         'beneficiary_name' =>  $post->benename,
    //                         'account_number' => $post->beneaccount,
    //                         'ifsc' => $post->beneifsc,
    //                         "unique_request_number" => $post->txnid,
    //                         "payment_mode" => $post->mode,
    //                         "amount" =>  $amount,
    //                         'email' => $user->email,
    //                         'phone' => $post->mobile,
    //                         "narration" => 'Initiatetransfertest',
    //                       );
    //                       //  'virtual_account_number' => "041863400001113",
    //                      $query = json_encode($parameter);

    //                 if (env('APP_ENV') == "server") {
    //                     $result = \Myhelper::curl($url, "POST", $query, $header, 'yes', 'DMTPayout', $post->txnid);
    //                 } else {
    //                     $result = [
    //                         'error' => true,
    //                         'response' => ''
    //                     ];
    //                 }

    //                 // dd([$url,$payload, $header,$result]);

    //                 if (env('APP_ENV') == "local" || $result['error'] || $result['response'] == '') {
    //                     $result['response'] = json_encode([
    //                         "message" => "Pending",
    //                         "statuscode" => "001",
    //                         "availlimit" => "0",
    //                         "total_limit" => "0",
    //                         "used_limit" => "0",
    //                         "Data" => [
    //                             [
    //                                 "fesessionid" => "CP1801861S131436",
    //                                 "tranid" => "pending",
    //                                 "rrn" => "pending",
    //                                 "externalrefno" => "MH357381218131436",
    //                                 "amount" => "0",
    //                                 "responsetimestamp" => "0",
    //                                 "benename" => "",
    //                                 "messagetext" => "Success",
    //                                 "code" => "1",
    //                                 "errorcode" => "1114",
    //                                 "mahatxnfee" => "10.00"
    //                             ]
    //                         ]
    //                     ]);
    //                 }

    //                 $outputs['data'][] = array(
    //                     'amount' => $amount,
    //                     'status' => 'TXN',
    //                     'data' => $this->output($post, $result['response'], $user)
    //                 );
    //             }
    //         } else {
    //             $outputs['data'][] = array(
    //                 'amount' => $amount,
    //                 'status' => 'TXF',
    //                 'data' => [
    //                     "statuscode" => "TXF",
    //                     "status" => "Same Transaction Repeat",
    //                     "message" => "Same Transaction Repeat",
    //                 ]
    //             );
    //         }
    //     }
    //     sleep(1);

    //     return response()->json($outputs, 200);
    // }

    //  public function bulkpayout($post){

    //     $totalamount = $post->amount;

    //     $header = array(
    //         'Authorization: Bearer ' .$this->bulkpauout->username ,
    //         'Content-Type: application/json'
    //     );
    //     $url = $this->bulkpauout->url;


    //     $amount = $post->amount;

    //     if(!$post->mode){
    //         $post->mode = 'imps';
    //     }  
    //     $outputs['statuscode'] = "TXN";
    //     $post['amount'] = $amount;
    //     $user = User::where('id', $post->user_id)->first();
    //     $avlbalance = $user->mainwallet;

    //     if ($post->amount >= 0 && $post->amount <= 25000) {
    //         $provider = Provider::where('recharge1', 'pgdmt1')->first();
    //     } elseif ($amount > 25001 && $amount <= 50000) {
    //         $provider = Provider::where('recharge1', 'pgdmt2')->first();
    //     } else {
    //         $provider = Provider::where('recharge1', 'pgdmt2')->first();
    //     }
    //     $post['provider_id'] = $provider->id;
    //     $post['charge'] =  \Myhelper::getCommission($post->amount, $user->scheme_id, $post->provider_id, $user->role->slug);

    //     if ($avlbalance < $post->amount + $post->charge) {
    //         $outputs['data'][] = array(
    //             'amount' => $amount,
    //             'status' => 'TXF',
    //             'data' => [
    //                 "statuscode" => "TXF",
    //                 "status" => "Insufficient Wallet Balance",
    //                 "message" => "Insufficient Wallet Balance",
    //             ]
    //         );
    //     } else {
    //         $post['amount'] = $amount;

    //         do {
    //             $post['txnid'] = $this->transcode() . rand(1111111111, 9999999999);
    //         } while (Report::where("txnid", "=", $post->txnid)->first() instanceof Report);

    //         $post['service'] = $provider->type;

    //         $bank = Mahabank::where('bankid', $post->benebank)->first();

    //         $insert = [
    //             'api_id' => $this->bulkpauout->id,
    //             'provider_id' => $post->provider_id,
    //             'option1' => $post->name,
    //             'mobile' => $post->mobile,
    //             'number' => $post->beneaccount,
    //             'option2' => $post->benename,
    //             'option3' => $bank->bankname ?? $post->benebank,
    //             'option4' => $post->beneifsc ?? '',
    //             'option5' => $post->benemobile,
    //             'txnid' => $post->txnid,
    //             'amount' => $post->amount,
    //             'charge' => $post->charge,
    //             'remark' => "Money Transfer",
    //             'status' => 'pending',
    //             'user_id' => $user->id,
    //             'credit_by' => $user->id,
    //             'product' => 'payout',
    //             'balance' => $avlbalance,
    //             'description' => $post->benemobile,
    //             'create_time' => $user->id."-".date("ymdhis"),
    //             'trans_type' => 'debit'
    //         ];
    //         // dd($insert);
    //         $previousrecharge = Report::where('number', $post->beneaccount)->where('amount', $post->amount)->where('provider_id', $post->provider_id)->whereBetween('created_at', [Carbon::now()->subSeconds(1)->format('Y-m-d H:i:s'), Carbon::now()->format('Y-m-d H:i:s')])->count();
    //         if ($previousrecharge == 0) {
    //             $transaction = User::where('id', $user->id)->decrement('mainwallet', $post->amount + $post->charge);
    //             if (!$transaction) {
    //                 $outputs['data'][] = array(
    //                     'amount' => $amount,
    //                     'status' => 'TXF',
    //                     'data' => [
    //                         "statuscode" => "TXF",
    //                         "status" => "Transaction Failed",
    //                     ]
    //                 ); 
    //             } else {
    //                 $totalamount = $totalamount - $amount;
    //                 $report = Report::create($insert);
    //                 $post['reportid'] = $report->id;
    //                 $post['amount'] = $amount;
    //                 $parameter = [
    //                     'amount' => $amount,
    //                     'payment_mode' => $post->mode ?? "IMPS",
    //                     'beneficiaryName' => $post->benename,
    //                     'account_number'  => $post->beneaccount,
    //                     'ifsc'   => $post->beneifsc,
    //                     'reference_id' =>$post->txnid,
    //                     'transcation_note' => "Vanavi payments"
    //                     ];

    //                 $payload = json_encode($parameter);
    //                 if (env('APP_ENV') == "server") {
    //                     $result = \Myhelper::curl($url, "POST", $payload, $header, 'yes', 'Payout', $post->txnid);
    //                 } else {
    //                     $result = [
    //                         'error' => true,
    //                         'response' => ''
    //                     ];
    //                 }

    //                 // dd([$url,$payload, $header,$result]);

    //                 if (env('APP_ENV') == "local" || $result['error'] || $result['response'] == '') {
    //                     $result['response'] = json_encode([
    //                         "message" => "Pending",
    //                         "statuscode" => "001",
    //                         "availlimit" => "0",
    //                         "total_limit" => "0",
    //                         "used_limit" => "0",
    //                         "Data" => [
    //                             [
    //                                 "fesessionid" => "CP1801861S131436",
    //                                 "tranid" => "pending",
    //                                 "rrn" => "pending",
    //                                 "externalrefno" => "MH357381218131436",
    //                                 "amount" => "0",
    //                                 "responsetimestamp" => "0",
    //                                 "benename" => "",
    //                                 "messagetext" => "Success",
    //                                 "code" => "1",
    //                                 "errorcode" => "1114",
    //                                 "mahatxnfee" => "10.00"
    //                             ]
    //                         ]
    //                     ]);
    //                 }

    //                 $outputs['data'][] = array(
    //                     'amount' => $amount,
    //                     'status' => 'TXN',
    //                     'data' => $this->output($post, $result['response'], $user)
    //                 );
    //             }
    //         } else {
    //             $outputs['data'][] = array(
    //                 'amount' => $amount,
    //                 'status' => 'TXF',
    //                 'data' => [
    //                     "statuscode" => "TXF",
    //                     "status" => "Same Transaction Repeat",
    //                     "message" => "Same Transaction Repeat",
    //                 ]
    //             );
    //         }
    //     }
    //     sleep(1);

    //     return response()->json($outputs, 200);  


    // }


    public function output($post, $response, $userdata)
    {
        $response = json_decode($response);
        switch ($post->type) {
            case 'getdistrict':
                return response()->json(['statuscode' => 'TXN', 'status' => 'Transaction Successfull', 'message' => $response]);
                break;

            case 'verification':
                if ($response->statuscode == 001 || $response->statuscode == 003) {
                    return response()->json(['statuscode' => 'TXN', 'status' => 'Transaction Successfull', 'message' => $response]);
                } elseif ($response->statuscode == 111) {
                    $parameters["bc_id"] = $post->bc_id;
                    $parameters["custno"] = $post->mobile;
                    $urls = $this->api->url . "AIRTEL/airtelOTP";
                    $headers = array("Content-Type: application/json");
                    $results = \Myhelper::curl($urls, "POST", json_encode($parameters), $headers, "no");
                    return response()->json(['statuscode' => 'RNF', 'status' => 'Customer Not Found', 'message' => $response->message]);
                } elseif ($response->statuscode == 002 && $response->message == "No Customer found") {
                    $parameters["bc_id"] = $post->bc_id;
                    $parameters["custno"] = $post->mobile;
                    $urls = $this->api->url . "AIRTEL/airtelOTP";
                    $headers = array("Content-Type: application/json");
                    $results = \Myhelper::curl($urls, "POST", json_encode($parameters), $headers, "no");
                    return response()->json(['statuscode' => 'RNF', 'status' => 'Customer Not Found', 'message' => $response->message]);
                } else {
                    return response()->json(['statuscode' => 'TXR', 'status' => 'Transaction Error', 'message' => $response->message]);
                }
                break;

            case 'otp':
                if (is_array($response) && isset($response[0]->StatusCode) && $response[0]->StatusCode == 001) {
                    return response()->json(['statuscode' => 'TXN', 'status' => 'Transaction Successfull', 'message' => $response[0]->Message]);
                } else {
                    return response()->json(['statuscode' => 'TXR', 'status' => 'Transaction Error', 'message' => $response[0]->Message]);
                }
                break;

            case 'registration':
                if (isset($response->StatusCode) && $response->StatusCode == 001) {
                    return response()->json(['statuscode' => 'TXN', 'status' => 'Transaction Successfull', 'message' => $response]);
                } elseif (is_array($response) && isset($response[0]->StatusCode) && $response[0]->StatusCode == 001) {
                    return response()->json(['statuscode' => 'TXN', 'status' => 'Transaction Successfull', 'message' => $response]);
                } elseif (is_array($response) && isset($response[0]->StatusCode) && $response[0]->StatusCode == 000) {
                    return response()->json(['statuscode' => 'TXR', 'status' => 'Transaction Error', 'message' => $response[0]->Message]);
                } else {
                    return response()->json(['statuscode' => 'TXR', 'status' => 'Transaction Error', 'message' => $response[0]->Message]);
                }
                break;

            case 'addbeneficiary':
                if (isset($response->statuscode) && $response->statuscode == 001) {
                    return response()->json(['statuscode' => 'TXN', 'status' => 'Transaction Successfull', 'message' => $response]);
                } elseif (is_array($response) && isset($response[0]->StatusCode) && $response[0]->StatusCode == 000) {
                    return response()->json(['statuscode' => 'TXR', 'status' => 'Transaction Error', 'message' => $response[0]->Message]);
                } else {
                    return response()->json(['statuscode' => 'TXR', 'status' => 'Transaction Error', 'message' => $response->message]);
                }
                break;

            case 'beneverify':
                if (!is_array($response) && isset($response->StatusCode) && $response->StatusCode == 001) {
                    return response()->json(['statuscode' => 'TXN', 'status' => 'Transaction Successfull', 'message' => $response]);
                } elseif (is_array($response) && isset($response[0]->StatusCode) && $response[0]->StatusCode == 001) {
                    return response()->json(['statuscode' => 'TXN', 'status' => 'Transaction Successfull', 'message' => $response]);
                } elseif (is_array($response) && isset($response[0]->StatusCode) && $response[0]->StatusCode == 000) {
                    return response()->json(['statuscode' => 'TXR', 'status' => 'Transaction Error', 'message' => $response[0]->Message]);
                } elseif (is_array($response) && isset($response[0]->StatusCode) && $response[0]->StatusCode == 003) {
                    return response()->json(['statuscode' => 'TXR', 'status' => 'Transaction Error', 'message' => $response[0]->Message]);
                } else {
                    return response()->json(['statuscode' => 'TXR', 'status' => 'Transaction Error', 'message' => $response->message]);
                }
                break;

            case 'accountverification':
                if (isset($response->STATUS) && $response->STATUS == 'SUCCESS' && isset($response->BENEFICIARY_NAME) && $response->BENEFICIARY_NAME != "") {

                    $balance = User::where('id', $userdata->id)->first(['mainwallet']);
                    $insert = [
                        'api_id' => $this->api->id,
                        'provider_id' => $post->provider_id,
                        'option1' => $post->name,
                        'mobile' => $post->mobile,
                        'number' => $post->beneaccount,
                        'option2' => isset($response->BENEFICIARY_NAME) ? $response->BENEFICIARY_NAME : $post->benename,
                        'option3' => $post->benebank,
                        'option4' => $post->beneifsc,
                        'txnid' => $post->txnid,
                        'refno' => isset($response->UTRN) ? $response->UTRN : "none",
                        'amount' => $post->amount,
                        'charge' => $post->charge,
                        'remark' => "Money Transfer",
                        'status' => 'success',
                        'user_id' => $userdata->id,
                        'credit_by' => $userdata->id,
                        'product' => 'dmt',
                        'balance' => $balance->mainwallet,
                        'description' => $post->benemobile
                    ];

                    User::where('id', $post->user_id)->decrement('mainwallet', $post->charge + $post->amount);
                    $report = Report::create($insert);
                    return response()->json(['statuscode' => 'TXN', 'status' => 'Transaction Successfull', 'message' => @$response->BENEFICIARY_NAME]);
                } elseif (isset($response) && isset($response->STATUS) && $response->STATUS == 'FAIL') {
                    return response()->json(['statuscode' => 'TXR', 'status' => 'Transaction Error', 'message' => @$response->MESSAGE]);
                } else {
                    return response()->json(['statuscode' => 'TXR', 'status' => 'Transaction Error', 'message' => @$response->MESSAGE]);
                }
                break;

            case 'transfer':

                $bankpayoutapi = $post->payoutapi; //$this->payoutapi();
                $report = Report::where('id', $post->reportid)->first();

                if ($bankpayoutapi == "cashfree") {
                    if (isset($response->status) && $response->status == "SUCCESS" || $response->status == "PENDING") {
                        $post['gst'] = 0;
                        $wallet = 'mainwallet';
                        Report::where('id', $post->reportid)->update([
                            'status' => "success",
                            'payid' => (isset($response->data->referenceId)) ? $response->data->referenceId : "Pending",
                            'refno' => (isset($response->data->utr)) ? $response->data->utr : "Pending",
                            'remark' => (isset($response->data->referenceId)) ? $response->data->referenceId : "Pending",
                            'gst' => $post->gst,
                        ]);
                        try {
                            \Myhelper::commission($report);
                        } catch (\Exception $e) {
                        }
                        return ['statuscode' => 'TXN', 'status' => 'Transaction Successfull', 'message' => "Transaction Successfull", 'rrn' => (isset($response->data->utr)) ? $response->data->utr : $report->txnid, 'payid' => $post->reportid];
                    } elseif (isset($response->status) && ($response->status == "ERROR")) {

                        User::where('id', $post->user_id)->increment('mainwallet', $report->charge + $report->amount);
                        $refno = 'Failed';
                        Report::where('id', $post->reportid)->update([
                            'status' => 'failed',
                            'refno' => $refno,
                        ]);
                        try {
                            if (isset($response->message) && $response->message == "You have Insufficent balance") {
                                $refno = "Service Down for some time";
                            }
                        } catch (\Exception $th) {
                        }
                        return ['statuscode' => 'TXF', 'status' => 'Transaction Failed', 'message' => 'Transaction Failed', "rrn" => $refno, 'payid' => $post->reportid];
                    } elseif (
                        isset($response->message) && ($response->message == "Unexpected character encountered while parsing value: <. Path " ||
                            $response->message == "You have Insufficent balance" ||
                            $response->message == "Service is down. Please try Again later." ||
                            $response->message == "Invalid IFSC code" ||
                            strpos($response->message, 'deadlocked on lock resources with another process and has been chosen as the deadlock victim. Rerun the transaction') !== false ||
                            $response->message == "Invalid Beneficiary details" ||
                            $response->message == "Beneficiary is not verified. Please verify"
                        )
                    ) {
                        User::where('id', $post->user_id)->increment('mainwallet', $report->charge + $report->amount);

                        if (isset($response->Data[0]) && isset($response->Data[0]->messagetext)) {
                            $refno = $response->Data[0]->messagetext;
                        } elseif (isset($response->message)) {
                            $refno = $response->message;
                        } else {
                            $refno = 'Failed';
                        }

                        Report::where('id', $post->reportid)->update([
                            'status' => 'failed',
                            'refno' => $refno,
                        ]);
                        try {
                            if (isset($response->message) && $response->message == "You have Insufficent balance") {
                                $refno = "Service Down for some time";
                            }
                        } catch (\Exception $th) {
                        }
                        return ['statuscode' => 'TXF', 'status' => 'Transaction Failed', 'message' => 'Transaction Failed', "rrn" => $refno, 'payid' => $post->reportid];
                    } else {

                        Report::where('id', $post->reportid)->update([
                            'status' => "pending",
                            'payid' => (isset($response->Data[0]->externalrefno)) ? $response->Data[0]->externalrefno : "Pending",
                            'refno' => (isset($response->Data[0]->rrn)) ? $response->Data[0]->rrn : "Pending",
                            'remark' => (isset($response->Data[0]->fesessionid)) ? $response->Data[0]->fesessionid : "Pending",
                        ]);
                        try {
                            \Myhelper::commission($report);
                        } catch (\Exception $e) {
                        }
                        return ['statuscode' => 'TUP', 'status' => 'Transaction Under Process', 'message' => "Transaction Under Process", 'rrn' => (isset($response->data->utr)) ? $response->data->utr : $report->txnid, 'payid' => $post->reportid];
                    }
                } else if ($bankpayoutapi == "easebuzz") {
                    if (isset($response->success) && $response->success == true) {
                        $post['gst'] = 0;
                        Report::where('id', $post->reportid)->update([
                            'status' => "success",
                            'payid' => (isset($response->data->transfer_request->id)) ? $response->data->transfer_request->id : "Pending",
                            'refno' => (isset($response->data->transfer_request->unique_transaction_reference)) ? $response->data->transfer_request->unique_transaction_reference : "Pending",
                        ]);
                        try {
                            \Myhelper::commission($report);
                        } catch (\Exception $e) {
                        }
                        return ['statuscode' => 'TXN', 'status' => 'Transaction Successfull', 'message' => "Transaction Successfull", 'rrn' => (isset($response->data->transfer_request->unique_transaction_reference)) ? $response->data->transfer_request->unique_transaction_reference : $report->txnid, 'payid' => $post->reportid];
                    } else {
                        User::where('id', $post->user_id)->increment('mainwallet', $report->charge + $report->amount);
                        $refno = 'Failed';
                        Report::where('id', $post->reportid)->update([
                            'status' => 'failed',
                            'refno' => $refno,
                        ]);
                        try {
                            if (isset($response->message) && $response->message == "You have Insufficent balance") {
                                $refno = "Service Down for some time";
                            }
                        } catch (\Exception $th) {
                        }
                        return ['statuscode' => 'TXF', 'status' => 'Transaction Failed', 'message' => $response->message ?? 'Transaction Failed', "rrn" => $refno, 'payid' => $post->reportid];
                    }
                } else if ($bankpayoutapi == "runpaisa") {

                    $report = Report::where('id', $post->reportid)->first();
                    $refno = isset($response->ackno) ? $response->ackno : $response->message;
                    if (isset($response->status) && $response->status == "SUCCESS") {
                        $post['gst'] = 0;
                        Report::where('id', $post->reportid)->update([
                            'status' => "success",
                            'payid' => (isset($response->data->transfer_request->id)) ? $response->data->transfer_request->id : "Pending",
                            'refno' => (isset($response->data->transfer_request->unique_transaction_reference)) ? $response->data->transfer_request->unique_transaction_reference : "Pending",
                        ]);
                        try {
                            \Myhelper::commission($report);
                        } catch (\Exception $e) {
                        }
                        return ['statuscode' => 'TXN', 'status' => 'Transaction Successfull', 'message' => "Transaction Successfull", 'rrn' => (isset($response->data->transfer_request->unique_transaction_reference)) ? $response->data->transfer_request->unique_transaction_reference : $report->txnid, 'payid' => $post->reportid];
                    } else {
                        if (isset($response->status) && $response->status == "FAIL") {
                            User::where('id', $post->user_id)->increment('mainwallet', $report->charge + $report->amount);
                            if ($response->message == "INSUFFICIENT BALANCE") {
                                $refno = 'Please Try After Sometime';
                            } else {
                                $refno = $refno ?? "Failed";
                            }

                            Report::where('id', $post->reportid)->update([
                                'status' => 'failed',
                                'refno' => $refno,
                            ]);
                            try {
                                if (isset($response->message) && $response->message == "You have Insufficent balance") {
                                    $refno = "Service Down for some time";
                                }
                            } catch (\Exception $th) {
                            }
                            return ['statuscode' => 'TXF', 'status' => 'Transaction Failed', 'message' => 'Transaction Failed', "rrn" => $refno ?? $post->txnid, 'payid' => $post->reportid];
                        }
                        return ['statuscode' => 'TUP', 'status' => 'Transaction Under Process', 'message' => "Transaction Under Process", 'rrn' => (isset($response->Data[0]->rrn)) ? $response->Data[0]->rrn : $report->txnid, 'payid' => $post->reportid];
                    }
                } else if ($bankpayoutapi == "bulkpayout") {
                    if (isset($response->root->sattus) && $response->root->status == true) {
                        $post['gst'] = 0;
                        $wallet = 'mainwallet';
                        Report::where('id', $post->reportid)->update([
                            'status' => "success",
                            'payid' => (isset($response->root->data->transcation_id)) ? $response->root->data->transcation_id : "Pending",
                            'refno' => (isset($response->root->data->utr)) ? $response->root->data->utr : "Pending",
                            'remark' => (isset($response->root->data->referenceId)) ? $response->root->data->referenceId : "Pending",
                        ]);
                        try {
                            \Myhelper::commission($report);
                        } catch (\Exception $e) {
                        }

                        return ['statuscode' => 'TXN', 'status' => 'Transaction Successfull', 'message' => "Transaction Successfull", 'rrn' => (isset($response->data->utr)) ? $response->data->utr : $report->txnid, 'payid' => $post->reportid];
                    } elseif (isset($response->status) && ($response->status == false)) {

                        User::where('id', $post->user_id)->increment('mainwallet', $report->charge + $report->amount);
                        $refno = 'Failed';
                        Report::where('id', $post->reportid)->update([
                            'status' => 'failed',
                            'refno' => $refno,
                        ]);
                        try {
                            if (isset($response->message) && $response->message == "You have Insufficent balance") {
                                $refno = "Service Down for some time";
                            }
                        } catch (\Exception $th) {
                        }
                        return ['statuscode' => 'TXF', 'status' => 'Transaction Failed', 'message' => 'Transaction Failed', "rrn" => $refno, 'payid' => $post->reportid];
                    } else {

                        Report::where('id', $post->reportid)->update([
                            'status' => "pending",
                            'payid' => (isset($response->Data[0]->externalrefno)) ? $response->Data[0]->externalrefno : "Pending",
                            'refno' => (isset($response->Data[0]->rrn)) ? $response->Data[0]->rrn : "Pending",
                            'remark' => (isset($response->Data[0]->fesessionid)) ? $response->Data[0]->fesessionid : "Pending",
                        ]);
                        try {
                            \Myhelper::commission($report);
                        } catch (\Exception $e) {
                        }
                        return ['statuscode' => 'TUP', 'status' => 'Transaction Under Process', 'message' => "Transaction Under Process", 'rrn' => (isset($response->data->utr)) ? $response->data->utr : $report->txnid, 'payid' => $post->reportid];
                    }
                } else if ($bankpayoutapi == 'cyruspayout') {
                    if (isset($response->statuscode) && $response->statuscode == "DE_001") {

                        $post['gst'] = 0;
                        Report::where('id', $post->reportid)->update([
                            'status' => "success",
                            'payid' => (isset($response->data->cyrus_id)) ? $response->data->cyrus_id : "Pending",
                            'refno' => (isset($response->data->rrn)) ? $response->data->rrn : "Pending",
                            'apitxnid' => (isset($response->data->orderId)) ? $response->data->orderId : "Pending",
                        ]);
                        try {
                            \Myhelper::commission($report);
                        } catch (\Exception $e) {
                        }

                        return ['statuscode' => 'TXN', 'status' => 'Transaction Successfull', 'message' => "Transaction Successfull", 'rrn' => (isset($response->data->rrn)) ? $response->data->rrn : $report->cyrus_id, 'payid' => $post->reportid];
                    } else if (isset($response->statuscode) && $response->statuscode == "ERR") {
                        // isset($response->ackno) ? $response->ackno : $response->message
                        User::where('id', $post->user_id)->increment('mainwallet', $report->charge + $report->amount);
                        $refno = isset($response->cyrus_id) ? $response->cyrus_id : $response->status;
                        Report::where('id', $post->reportid)->update(['status' => 'failed', 'refno' => $refno]);
                        return ['statuscode' => 'TXF', 'status' => 'Transaction Failed', 'message' => $response->message ?? 'Transaction Failed', "rrn" => $refno, 'payid' => $post->reportid];
                    }
                }

                break;

            default:
                return response()->json(['statuscode' => 'BPR', 'status' => 'Bad Parameter Request', 'message' => "Bad Parameter Request"]);
                break;
        }
    }

    public function getCharge($amount)
    {
        if ($amount < 1000) {
            return 10;
        } else {
            return $amount * 1 / 100;
        }
    }

    public function getGst($amount)
    {
        return $amount * 100 / 118;
    }

    public function getTds($amount)
    {
        return $amount * 5 / 100;
    }

    public function getToken()
    {
        $cred = Api::where('code', 'cashfreepayout')->first();
        $request = [];
        $header = array(
            'X-Client-Id: ' . $cred->username,
            'X-Client-Secret: ' . $cred->password,
            'Content-Type: application/json',
        );

        $result = \Myhelper::curl($cred->url . '/payout/v1/authorize', "POST", json_encode($request), $header, 'yes', 1, 'cashfree', 'Authorized');
        $response['data'] = json_decode($result['response']);
        //dd($response['data'], $cred);
        if (isset($response['data']->subCode) && $response['data']->subCode == '200') {
            return $response['data']->data->token;
        }
        return "";
    }

    public function getRunpaisaToken()
    {

        $api = Api::where('code', 'runpaisa_validate')->first();
        $request = [];
        $header = array(
            'client_id: ' . $api->optional1,
            'username: ' . $api->username,
            'password: ' . $api->password,
            'Content-Type: application/json',
        );

        $url = $api->url . "/token";
        $result = \Myhelper::curl($url, "POST", json_encode($request), $header, 'yes', 1, 'runpaisa', 'Runpaisa');
        $response['data'] = json_decode($result['response']);
        if (isset($response['data']->status) && $response['data']->status == 'SUCCESS') {
            return $response['data']->data->token;
        }
        return "";
    }

    public function getRunpaisaTokenPayout()
    {

        $api = Api::where('code', 'runpaisafund')->first();
        $request = [];
        $header = array(
            'client_id: ' . $api->optional1,
            'username: ' . $api->username,
            'password: ' . $api->password,
            'Content-Type: application/json',
        );

        $url = $api->url . "/token";
        $result = \Myhelper::curl($url, "POST", json_encode($request), $header, 'yes', 1, 'runpaisa', 'Runpaisa');
        $response['data'] = json_decode($result['response']);
        if (isset($response['data']->status) && $response['data']->status == 'SUCCESS') {
            return $response['data']->data->token;
        }
        return "";
    }
}