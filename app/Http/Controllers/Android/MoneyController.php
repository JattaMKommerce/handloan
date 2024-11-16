<?php

namespace App\Http\Controllers\Android;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Api;
use App\Models\Provider;
use App\Models\Mahabank;
use App\Models\Report;
use App\Models\Commission;
use App\Models\Packagecommission;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MoneyController extends Controller
{
    protected $api, $cyrus, $runpaisa, $bulkpauout;
    public function __construct()
    {
        $this->api = Api::where('code', 'dmt1')->first();
        $this->cyrus = Api::where('code', 'cyrusfund')->first();
        $this->runpaisa = Api::where('code', 'runpaisafund')->first();
    }

    public function transaction(Request $post)
    {
        if (!$this->api || $this->api->status == 0) {
            return response()->json(['statuscode' => "ERR", "message" => "Money Transfer Service Currently Down"]);
        }

        $rules = array(
            'apptoken' => 'required',
            'user_id' => 'required|numeric',
            'type' => 'required',
        );

        $validate = \Myhelper::FormValidator($rules, $post);
        if ($validate != "no") {
            return $validate;
        }

        $user = User::where('id', $post->user_id)->first();

        if (!$user) {
            $output['statuscode'] = "ERR";
            // $output['status'] = "error";
            $output['message'] = "User details not matched";
            return response()->json($output);
        }

        if (!\Myhelper::can('dmt1_service', $user->id)) {
            return response()->json(['statuscode' => "ERR", "message" => "Service Not Allowed"]);
        }

        switch ($post->type) {
            case 'getbank':
                $rules = array(
                    'type' => 'required'
                );
                break;

            case 'getbenedetail':
            case 'otp':
                $rules = array(
                    'type' => 'required',
                    'mobile' => 'required|numeric|digits:10'
                );
                break;

            case 'registration':
                $rules = array(
                    'type' => 'required',
                    'mobile' => 'required|numeric|digits:10',
                    'firstname' => 'required|regex:/^[\pL\s\-]+$/u',
                    'lastname' => 'required|regex:/^[\pL\s\-]+$/u',
                    'dob' => 'required',
                    'otp' => 'required',
                );
                break;

            case 'addbeneficiary':
                $rules = array(
                    'mobile' => 'required|numeric|digits:10',
                    'benebank' => 'required',
                    'beneifsc' => "required",
                    'beneaccount' => "required|numeric|digits_between:6,20",
                    'benemobile' => 'required|numeric|digits:10',
                    'benename' => "required|regex:/^[\pL\s\-]+$/u"
                );
                break;

            case 'beneverify':
                $rules = array(
                    'mobile' => 'required|numeric|digits:10',
                    'beneaccount' => 'required|numeric|digits_between:6,20',
                    'benemobile' => 'required|numeric|digits:10',
                    'otp' => 'required|numeric'
                );
                break;

            case 'accountverification':
                $rules = array(
                    'mobile' => 'required|numeric|digits:10',
                    'benebank' => 'required',
                    'beneifsc' => "required",
                    'beneaccount' => "required|numeric|digits_between:6,20",
                    'benemobile' => 'required|numeric|digits:10',
                    'benename' => "required|regex:/^[\pL\s\-]+$/u",
                    // 'name'        => "required"
                );
                break;

            case 'transfer':
                $rules = array(
                    'name' => 'required',
                    'mobile' => 'required|numeric|digits:10',
                    'benebank' => 'required',
                    'beneifsc' => "required",
                    'beneaccount' => "required|numeric|digits_between:6,20",
                    'benemobile' => 'required|numeric|digits:10',
                    'benename' => "required",
                    'amount' => 'required|numeric|min:100|max:25000',
                    'payoutapi' => 'required|in:cyruspayout,runpaisa,dmt'
                );
                break;

            default:
                return response()->json(['statuscode' => "ERR", "message" => "Bad Parameter Request"]);
                break;
        }

        $validate = \Myhelper::FormValidator($rules, $post);
        if ($validate != "no") {
            return $validate;
        }

        $bcid = \App\Models\PortalSetting::where('code', 'bcid')->first();
        $cpid = \App\Models\PortalSetting::where('code', 'cpid')->first();

        if (isset($cpid->value)) {
            $post['cpid'] = $cpid->value;
        } else {
            return response()->json(['statuscode' => 'ERR', 'message' => "CP id not mapped"]);
        }

        if (isset($bcid->value)) {
            $post['bc_id'] = $bcid->value;
        } else {
            return response()->json(['statuscode' => 'ERR', 'message' => "Bc id not mapped"]);
        }

        if ($post->type == "transfer") {
            $codes = ['dmt1', 'dmt2', 'dmt3', 'dmt4', 'dmt5'];
            $providerids = [];
            foreach ($codes as $value) {
                $providerids[] = Provider::where('recharge1', $value)->first(['id'])->id;
            }
            if ($this->schememanager() == "admin") {
                $commission = Commission::where('scheme_id', $user->scheme_id)->whereIn('slab', $providerids)->get();
            } else {
                $commission = Packagecommission::where('scheme_id', $user->scheme_id)->whereIn('slab', $providerids)->get();
            }
            if (!$commission || sizeof($commission) < 5) {
                return response()->json(['statuscode' => 'ERR', 'message' => "Money Transfer charges not set, contact administrator."]);
            }
        }

        $header = array("Content-Type: application/json");

        switch ($post->type) {
            case 'getbank':
                $banks = Mahabank::get();
                return response()->json(['statuscode' => "TXN", "message" => "Bank details fetched", 'data' => $banks]);
                break;

            case 'getbenedetail':
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
                $circle = \DB::table('circles')->where('state', 'like', '%' . $user->state . '%')->first();

                if (!$circle || $user->pincode == '' || $user->address == '') {
                    return response()->json(['statuscode' => 'ERR', 'message' => "Please update your profile or contact administrator"]);
                }

                $url = $this->api->url . "AIRTEL/apiCustRegistration";
                // $name = explode(" ", $post->fname);
                $parameter["bc_id"] = $post->bc_id;
                $parameter["custno"] = $post->mobile;
                $parameter["cust_f_name"] = $post->firstname;
                $parameter["cust_l_name"] = $post->lastname;
                $parameter["Dob"] = date("m/d/Y", strtotime(@$post->dob));
                $parameter["otp"] = $post->otp;
                $parameter["Address"] = $user->address;
                $parameter["pincode"] = $user->pincode;
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
                // $url = $this->api->url."AIRTEL/VerifybeneApi";
                // $post['amount'] = 1;
                // $provider = Provider::where('recharge1', 'dmt1accverify')->first();
                // $post['charge'] = \Myhelper::getCommission($post->amount, $user->scheme_id, $provider->id, $user->role->slug);
                // $post['provider_id'] = $provider->id;
                // if($user->mainwallet < $post->amount + $post->charge){
                //     return response()->json(["statuscode" => "IWB", 'message'=>'Low balance, kindly recharge your wallet.']);
                // }

                // $parameter["custno"]    = $post->mobile;
                // $parameter["bankname"]  = $post->benebank;
                // $parameter["beneaccno"] = $post->beneaccount;
                // $parameter["benemobile"]= $post->benemobile;
                // $parameter["benename"]  = $post->benename;
                // $parameter["ifsc"]      = $post->beneifsc;
                // $parameter['bc_id']     = $post->bc_id;
                // $parameter["saltkey"]   = $this->api->username;
                // $parameter["secretkey"] = $this->api->password;
                // do {
                //     $post['txnid'] = $this->transcode().rand(1111111111, 9999999999);
                // } while (Report::where("txnid", "=", $post->txnid)->first() instanceof Report);
                // $parameter["clientrefno"] = $post->txnid;
                // break;

                $api = Api::where('code', 'runpaisa_validate')->first();
                $url = $api->optional2 . "/account";
                $post['amount'] = 1;
                $provider = Provider::where('recharge1', 'dmt1accverify')->first();
                $post['charge'] = \Myhelper::getCommission($post->amount, $user->scheme_id, $provider->id, $user->role->slug);
                $post['provider_id'] = $provider->id;
                if ($user->mainwallet < $post->amount + $post->charge) {
                    return response()->json(["statuscode" => "IWB", 'message' => 'Low balance, kindly recharge your wallet.']);
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
                    //  return response()->json(['statuscode' => "ERR", "message" => "Transaction Pin is incorrect"]);
                }





                $bankpayoutapi = $post->payoutapi; //$this->payoutapi();
                if ($bankpayoutapi == "cyruspayout") {

                    if (!$this->cyrus || $this->cyrus->status == 0) {
                        return response()->json(['statuscode' => "ERR", "message" => "Service Currently Down"]);
                    }
                    return $this->cashfreetrasfer($post);
                } else if ($bankpayoutapi == "runpaisa") {

                    if (!$this->runpaisa || $this->runpaisa->status == 0) {
                        return response()->json(['statuscode' => "ERR", "message" => "Service Currently Down"]);
                    }
                    return $this->runpaisatransfer($post);
                } else if ($bankpayoutapi == "dmt") {
                    return $this->transfer($post);
                } else {
                    return response()->json(['status' => "Currently Service is Down"]);
                }

                break;
        }

        if ($post->type == "accountverification") {

            $result = \Myhelper::curl($url, "POST", $parameter, $header, "yes", 'App\Models\Report', $post->txnid);
        } else {

            $result = \Myhelper::curl($url, "POST", json_encode($parameter), $header, "yes", 'App\Models\Report', '0');
        }

        if ($user->id == "489") {
            //dd([$url, $parameter , $result]);
        }
        if ($result['error'] || $result['response'] == "") {
            return response()->json(['statuscode' => "ERR", "message" => "Technical Error, contact service provider"]);
        }

        $response = json_decode($result['response']);
        switch ($post->type) {
            case 'getbenedetail':
                if (
                    isset($response->statuscode) &&
                    $response->statuscode == 001 ||
                    $response->statuscode == 003 ||
                    $response->statuscode == 111
                ) {
                    $output['statuscode'] = "TXN";
                    // $output['status'] = "success";
                    $output['message'] = "Transaction Successfull";
                    $output['name'] = $response->custfirstname . " " . $response->custlastname;
                    $output['mobile'] = $response->custmobile;
                    $output['totallimit'] = '1000000';//$response->total_limit;
                    $benedatas = [];

                    $data_used_limit = DB::table('reports')
                            ->selectRaw("
                                sum(amount) AS amount, 
                                DATE_FORMAT(created_at, '%Y-%m') AS new_date
                            ")
                            ->groupBy('new_date')
                            ->where('user_id',$post->user_id)
                            ->whereIn('status',['success','refund'])
                            ->whereDate('created_at', '>=', date('Y-m').'-01')
                            ->whereDate('created_at', '<=', date('Y-m-d'))
                            ->where('product','payout')
                            ->first();

                            


                    $net_limit = (@$data_used_limit->amount ?? 0);

                    $output['usedlimit'] = $net_limit;//$response->used_limit;

                    if (sizeof($response->Data) > 0) {
                        foreach ($response->Data as $value) {
                            $benedata['beneid'] = $value->id;
                            $benedata['benename'] = $value->benename;
                            $benedata['beneaccount'] = $value->beneaccno;
                            $benedata['benemobile'] = $value->benemobile;
                            $benedata['benebank'] = $value->bankname;
                            $benedata['beneifsc'] = $value->ifsc;
                            $benedata['benebankid'] = $value->bankid;
                            $benedata['benestatus'] = $value->status;
                            $benedatas[] = $benedata;
                        }
                    }
                    $output['beneficiary'] = $benedatas;

                } elseif (
                    $response->statuscode == 002 &&
                    $response->message == "No Customer found"
                ) {
                    $parameter["bc_id"] = $post->bc_id;
                    $parameter["custno"] = $post->mobile;
                    $url = $this->api->url . "AIRTEL/airtelOTP";
                    $header = array("Content-Type: application/json");
                    \Myhelper::curl($url, "POST", json_encode($parameter), $header, "no");

                    $output['statuscode'] = "RNF";
                    // $output['status'] = "failed";
                    $output['message'] = "Customer Not Found";
                } else {
                    $output['statuscode'] = "ERR";
                    // $output['status'] = "error";
                    $output['message'] = isset($response->message) ? $response->message : 'Transaction Error';
                }
                break;

            case 'otp':
                if (isset($response[0]->StatusCode) && $response[0]->StatusCode == 001) {
                    $output['statuscode'] = 'TXN';
                    // $output['status'] = "success";
                    $output['message'] = $response[0]->Message;
                } else {
                    $output['statuscode'] = 'ERR';
                    // $output['status'] = "error";
                    $output['message'] = isset($response[0]->Message) ? $response[0]->Message : 'Transaction Error';
                }
                break;

            case 'registration':
                if (isset($response->StatusCode) && $response->StatusCode == 001) {
                    $output['statuscode'] = 'TXN';
                    // $output['status'] = "success";
                    $output['message'] = 'Transaction Successfull';
                } elseif (is_array($response) && isset($response[0]->StatusCode) && $response[0]->StatusCode == 001) {
                    $output['statuscode'] = 'TXN';
                    // $output['status'] = "success";

                    $output['message'] = 'Transaction Successfull';
                } elseif (is_array($response) && isset($response[0]->StatusCode) && $response[0]->StatusCode == 000) {
                    $output['statuscode'] = 'ERR';
                    // $output['status'] = "error";
                    $output['message'] = $response[0]->Message;
                } else {
                    $output['statuscode'] = 'ERR';
                    // $output['status'] = "error";
                    $output['message'] = $response[0]->Message;
                }
                break;

            case 'addbeneficiary':
                if (isset($response->statuscode) && $response->statuscode == 001) {
                    $output['statuscode'] = 'TXN';
                    $output['message'] = 'Transaction Successfull';
                } elseif (is_array($response) && isset($response[0]->StatusCode) && $response[0]->StatusCode == 000) {
                    $output['statuscode'] = 'ERR';
                    $output['message'] = $response[0]->Message;
                } else {
                    $output['statuscode'] = 'ERR';
                    $output['message'] = $response->message;
                }
                break;

            case 'beneverify':
                if (!is_array($response) && isset($response->StatusCode) && $response->StatusCode == 001) {
                    $output['statuscode'] = 'TXN';
                    $output['message'] = 'Transaction Successfull';
                } elseif (is_array($response) && isset($response[0]->StatusCode) && $response[0]->StatusCode == 001) {
                    $output['statuscode'] = 'TXN';
                    $output['message'] = 'Transaction Successfull';
                } elseif (is_array($response) && isset($response[0]->StatusCode) && $response[0]->StatusCode == 000) {
                    $output['statuscode'] = 'ERR';
                    $output['message'] = $response[0]->Message;
                } elseif (is_array($response) && isset($response[0]->StatusCode) && $response[0]->StatusCode == 003) {
                    $output['statuscode'] = 'ERR';
                    $output['message'] = $response[0]->Message;
                } else {
                    $output['statuscode'] = 'ERR';
                    $output['message'] = $response->Message;
                }
                break;

            case 'accountverification':
                if (isset($response->STATUS) && $response->STATUS == 'SUCCESS' && isset($response->BENEFICIARY_NAME) && $response->BENEFICIARY_NAME != "") {
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
                        'user_id' => $user->id,
                        'credit_by' => $user->id,
                        'product' => 'dmt',
                        'balance' => $user->mainwallet,
                        'description' => $post->benemobile,
                        'via' => 'app',
                        'trans_type' => 'debit'
                    ];

                    User::where('id', $post->user_id)->decrement('mainwallet', $post->charge + $post->amount);
                    $report = Report::create($insert);
                    $output['statuscode'] = 'TXN';
                    // $output['status'] = 'success';
                    $output['message'] = 'Transaction Successfull';
                    $output['benename'] = @$response->BENEFICIARY_NAME;
                } elseif (isset($response) && isset($response->STATUS) && $response->STATUS == 'FAIL') {
                    $output['statuscode'] = 'TXF';
                    // $output['status'] = 'failed';
                    $output['message'] = @$response->MESSAGE;
                } else {
                    $output['statuscode'] = 'ERR';
                    // $output['status'] = 'error';
                    $output['message'] = @$response->MESSAGE;
                }
                break;
        }
        return response()->json($output);
    }

    public function transfer($post)
    {
        $totalamount = $post->amount;
        $url = $this->api->url . "AIRTEL/Apipaymode";
        $parameter['bc_id'] = $post->bc_id;
        $parameter["saltkey"] = $this->api->username;
        $parameter["secretkey"] = $this->api->password;

        $amount = $post->amount;
        for ($i = 1; $i < 6; $i++) {
            if (5000 * ($i - 1) <= $amount && $amount <= 5000 * $i) {
                if ($amount == 5000 * $i) {
                    $n = $i;
                } else {
                    $n = $i - 1;
                    $x = $amount - $n * 5000;
                }
                break;
            }
        }

        $amounts = array_fill(0, $n, 5000);
        if (isset($x)) {
            array_push($amounts, $x);
        }

        foreach ($amounts as $amount) {
            if ($totalamount < $amount) {
                continue;
            }

            $outputs['statuscode'] = "TXN";
            $post['amount'] = $amount;
            $user = User::where('id', $post->user_id)->first();
            $post['charge'] = $this->getCharge($post->amount);
            if ($user->mainwallet < $post->amount + $post->charge) {
                $outputs['data'][] = array(
                    'amount' => $amount,
                    'statuscode' => 'IWB',
                    // 'status' => 'Insufficient Wallet Balance',
                    'message' => 'Insufficient Wallet Balance',
                );
            } else {

                do {
                    $post['txnid'] = $this->transcode() . rand(1111111111, 9999999999);
                } while (Report::where("txnid", "=", $post->txnid)->first() instanceof Report);

                if ($post->amount >= 100 && $post->amount <= 1000) {
                    $provider = Provider::where('recharge1', 'dmt1')->first();
                } elseif ($amount > 1000 && $amount <= 2000) {
                    $provider = Provider::where('recharge1', 'dmt2')->first();
                } elseif ($amount > 2000 && $amount <= 3000) {
                    $provider = Provider::where('recharge1', 'dmt3')->first();
                } elseif ($amount > 3000 && $amount <= 4000) {
                    $provider = Provider::where('recharge1', 'dmt4')->first();
                } else {
                    $provider = Provider::where('recharge1', 'dmt5')->first();
                }

                $post['provider_id'] = $provider->id;
                $bank = Mahabank::where('bankid', $post->benebank)->first();
                $insert = [
                    'api_id' => $this->api->id,
                    'provider_id' => $post->provider_id,
                    'option1' => $post->name,
                    'mobile' => $post->mobile,
                    'number' => $post->beneaccount,
                    'option2' => $post->benename,
                    'option3' => $bank->bankname,
                    'option4' => $post->beneifsc,
                    'txnid' => $post->txnid,
                    'amount' => $post->amount,
                    'charge' => $post->charge,
                    'remark' => "Money Transfer",
                    'status' => 'pending',
                    'user_id' => $user->id,
                    'credit_by' => $user->id,
                    'product' => 'dmt',
                    'via' => 'app',
                    'balance' => $user->mainwallet,
                    'description' => $post->benemobile,
                    'trans_type' => 'debit'
                ];

                $previousrecharge = Report::where('number', $post->beneaccount)->where('amount', $post->amount)->where('provider_id', $post->provider_id)->whereBetween('created_at', [Carbon::now()->subSeconds(5)->format('Y-m-d H:i:s'), Carbon::now()->format('Y-m-d H:i:s')])->count();
                if ($previousrecharge == 0) {
                    $transaction = User::where('id', $user->id)->decrement('mainwallet', $post->amount + $post->charge);
                    if (!$transaction) {
                        $outputs['data'][] = array(
                            'amount' => $amount,
                            'statuscode' => 'TXF',
                            // 'status' => 'Transaction Failed',
                            'message' => 'Transaction Failed'
                        );
                    } else {
                        $totalamount = $totalamount - $amount;
                        $report = Report::create($insert);
                        $post['service'] = $provider->type;
                        $post['reportid'] = $report->id;
                        $post['amount'] = $amount;
                        $parameter["custno"] = $post->mobile;
                        $parameter["bankname"] = $post->benebank;
                        $parameter["beneaccno"] = $post->beneaccount;
                        $parameter["benemobile"] = $post->benemobile;
                        $parameter["benename"] = $post->benename;
                        $parameter["ifsc"] = $post->beneifsc;
                        $parameter['amount'] = $amount;
                        $parameter["clientrefno"] = $post->txnid;
                        $header = array("Content-Type: application/json");

                        if (env('APP_ENV') == "server") {
                            $result = \Myhelper::curl($url, "POST", json_encode($parameter), $header, "yes", 'App\Models\Report', $post->txnid);
                        } else {
                            $result['error'] = true;
                            $result['response'] = '';
                        }

                        if ($result['error'] || $result['response'] == '') {
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
                                        "code" => "0",
                                        "errorcode" => "1114",
                                        "mahatxnfee" => "10.00"
                                    ]
                                ]
                            ]);
                        }

                        $response = json_decode($result['response']);
                        $report = Report::where('id', $post->reportid)->first();

                        // dd($response);

                        if (isset($response->Data[0]->errorcode) && $response->Data[0]->errorcode == 0 && $response->Data[0]->code === 0) {
                            $charge = \Myhelper::getCommission($post->amount, $user->scheme_id, $post->provider_id, $user->role->slug);
                            $post['gst'] = 0;
                            User::where('id', $post->user_id)->increment('mainwallet', $report->charge - $post->gst - $charge);
                            Report::where('id', $post->reportid)->update([
                                'status' => "success",
                                'payid' => (isset($response->Data[0]->externalrefno)) ? $response->Data[0]->externalrefno : "Pending",
                                'refno' => (isset($response->Data[0]->rrn)) ? $response->Data[0]->rrn : "Pending",
                                'remark' => (isset($response->Data[0]->fesessionid)) ? $response->Data[0]->fesessionid : "Pending",
                                'gst' => $post->gst,
                                'profit' => $report->charge - $post->gst - $charge
                            ]);
                            \Myhelper::commission($report);
                            $output = ['amount' => $amount, 'status' => 'success', 'message' => 'success', "rrn" => (isset($response->Data[0]->rrn)) ? $response->Data[0]->rrn : "Success"];
                        } elseif (isset($response->Data[0]->errorcode) && in_array($response->Data[0]->errorcode, ['8', '1', '10', '86', '3', '52', '101', 'M5', 'M3', '20', '1076', '100', '608', 'BL101', '96', '9001', 'M2', '12', '3403', 'PM0405', '999001', 'PM0640', '333998', 'M0', '1206', '1077', 'M4', '1075', '9001', '93097', '911', 'PM0684', '54', '3', '51', '13', '14', '92', '94', '4', '1515', '302', 'M7', '1616', '99', '801', '22', 'M1', 'AS_111', '9001', '999001', '333998', '934210', '9006', '801', '911', '302', '9001', '912', '010'])) {
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
                            $output = ['amount' => $amount, 'status' => 'failed', 'message' => 'failed', 'rrn' => $refno];
                        } elseif (
                            isset($response->message) && (
                                $response->message == "Unexpected character encountered while parsing value: <. Path " ||
                                $response->message == "You have Insufficent balance" ||
                                $response->message == "Invalid IFSC code" ||
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
                            $output = ['amount' => $amount, 'status' => 'failed', 'message' => 'failed', 'rrn' => $refno];
                        } else {
                            $charge = \Myhelper::getCommission($post->amount, $user->scheme_id, $post->provider_id, $user->role->slug);
                            $post['gst'] = 0;
                            User::where('id', $post->user_id)->increment('mainwallet', $report->charge - $post->gst - $charge);
                            Report::where('id', $post->reportid)->update([
                                'status' => "pending",
                                'payid' => (isset($response->Data[0]->externalrefno)) ? $response->Data[0]->externalrefno : "Pending",
                                'refno' => (isset($response->Data[0]->rrn)) ? $response->Data[0]->rrn : "Pending",
                                'remark' => (isset($response->Data[0]->fesessionid)) ? $response->Data[0]->fesessionid : "Pending",
                                'gst' => $post->gst,
                                'profit' => $report->charge - $post->gst - $charge
                            ]);
                            \Myhelper::commission($report);
                            $output = ['amount' => $amount, 'status' => 'pending', 'message' => 'pending', "rrn" => (isset($response->Data[0]->rrn)) ? $response->Data[0]->rrn : "Pending"];
                        }

                        $outputs['data'][] = $output;
                    }
                } else {
                    $outputs['data'][] = array(
                        'amount' => $amount,
                        'statuscode' => 'TXF',
                        // 'status' => 'Transaction Failed, Same Transaction Repeat',
                        'message' => 'Transaction Failed, Same Transaction Repeat'
                    );
                }
            }
            sleep(1);
        }
        return response()->json($outputs);
    }

    public function getCommission($scheme, $slab, $amount)
    {
        if ($amount < 1000) {
            $amount = 1000;
        }
        $userslab = Commission::where('scheme_id', $scheme)->where('product', 'money')->where('slab', $slab)->first();
        if ($userslab) {
            if ($userslab->type == "percent") {
                $usercharge = $amount * $userslab->value / 100;
            } else {
                $usercharge = $userslab->value;
            }
        } else {
            $usercharge = 7;
        }

        return $usercharge;
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
                        // $result = \Myhelper::curl($url, "POST", $query, [], 'yes');
                        // }
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

                    $outputs['data'][] = $this->output($post, $result['response'], $user, $amount);

                }
            } else {
                $outputs['data'][] = array(
                    'amount' => $amount,
                    "statuscode" => "TXF",
                    "status" => "Same Transaction Repeat",
                    "message" => "Same Transaction Repeat",

                );
            }
        }
        sleep(1);

        return response()->json($outputs, 200);
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

                    $outputs['data'][] = $this->output($post, $result['response'], $user, $amount);

                }
            } else {
                $outputs['data'][] = array(
                    'amount' => $amount,
                    "statuscode" => "TXF",
                    "status" => "Same Transaction Repeat",
                    "message" => "Same Transaction Repeat",

                );
            }
        }
        sleep(1);
        return response()->json($outputs, 200);
    }

    public function output($post, $response, $userdata, $amt)
    {
        $response = json_decode($response);
        switch ($post->type) {
            case 'getdistrict':
                break;

            case 'verification':
                break;

            case 'otp':
                break;

            case 'registration':
                break;

            case 'addbeneficiary':
                break;

            case 'beneverify':
                break;

            case 'accountverification':
                break;

            case 'transfer':
                $bankpayoutapi = $post->payoutapi; //$this->payoutapi();
                $report = Report::where('id', $post->reportid)->first();

                if ($bankpayoutapi == "cashfree") {

                } elseif ($bankpayoutapi == "runpaisa") {
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
                        return ['statuscode' => 'TXN', 'status' => 'Transaction Successfull', 'amount' => $amt, 'message' => "Transaction Successfull", 'rrn' => (isset($response->data->transfer_request->unique_transaction_reference)) ? $response->data->transfer_request->unique_transaction_reference : $report->txnid, 'payid' => $post->reportid];
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
                            return ['statuscode' => 'TXF', 'status' => 'Transaction Failed', 'amount' => $amt, 'message' => 'Transaction Failed', "rrn" => $refno ?? $post->txnid, 'payid' => $post->reportid];
                        }
                        return ['statuscode' => 'TUP', 'status' => 'Transaction Under Process', 'amount' => $amt, 'message' => "Transaction Under Process", 'rrn' => (isset($response->Data[0]->rrn)) ? $response->Data[0]->rrn : $report->txnid, 'payid' => $post->reportid];
                    }

                } elseif ($bankpayoutapi == 'cyruspayout') {
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

                        return ['statuscode' => 'TXN', 'status' => 'Transaction Successfull', 'amount' => $amt, 'message' => "Transaction Successfull", 'rrn' => (isset($response->data->rrn)) ? $response->data->rrn : $report->cyrus_id, 'payid' => $post->reportid];
                    } else {
                        if (isset($response->statuscode) && $response->statuscode == "ERR") {
                            User::where('id', $post->user_id)->increment('mainwallet', $report->charge + $report->amount);
                            $refno = isset($response->cyrus_id) ? $response->cyrus_id : $response->status;
                            Report::where('id', $post->reportid)->update(['status' => 'failed', 'refno' => $refno]);
                            return response()->json(['statuscode' => 'TXF', 'status' => 'Transaction Failed', 'amount' => $amt, 'message' => $response->message ?? 'Transaction Failed', "rrn" => $refno, 'payid' => $post->reportid]);
                        }

                        return ['statuscode' => 'TUP', 'status' => 'Transaction Under Process', 'amount' => $amt, 'message' => "Transaction Under Process", 'rrn' => (isset($response->data->rrn)) ? $response->data->rrn : $report->txnid, 'payid' => $post->reportid];

                    }
                }

                break;

            default:
                return response()->json(['statuscode' => 'BPR', 'status' => 'Bad Parameter Request', 'message' => "Bad Parameter Request"]);
                break;
        }
    }


}