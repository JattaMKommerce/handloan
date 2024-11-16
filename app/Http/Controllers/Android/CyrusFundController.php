<?php

namespace App\Http\Controllers\Android;

use App\Http\Controllers\Controller;
use App\Models\Fundbank;
use App\Models\Fundreport;
use App\Models\Paymode;
use Illuminate\Http\Request;
use App\User;
use App\Models\Aepsfundrequest;
use App\Models\Aepsreport;
use App\Models\Report;
use App\Models\Api;
use App\Models\Provider;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;


class CyrusFundController extends Controller
{
    public $fundapi, $admin, $runpaisafundapi, $fundfundapi;

    public function __construct()
    {
        $this->fundfundapi = Api::where('code', 'fund')->first();
        $this->fundapi = Api::where('code', 'cyrusfund')->first();
        $this->runpaisafundapi = Api::where('code', 'runpaisafund')->first();
        $this->admin = User::whereHas('role', function ($q) {
            $q->where('slug', 'admin');
        })->first();

    }

    public function transaction(Request $post)
    {
        if ($this->fundapi->status == "0") {
            return response()->json(['statuscode' => "ERR", 'message' => "This function is down."]);
        }
        $provide = Provider::where('recharge1', 'fund')->first();
        $post['provider_id'] = $provide->id;

        switch ($post->type) {

            case 'bank':

                if ($this->pinCheck($post) == "fail") {
                    return response()->json(['status' => "Transaction Pin is incorrect"]);
                }
                $banksettlementtype = $this->banksettlementtype();
                $impschargeupto25 = $this->impschargeupto25();
                $impschargeabove25 = $this->impschargeabove25();

                if ($banksettlementtype == "down") {
                    return response()->json(['statuscode' => "ERR", 'message' => "Aeps Settlement Down For Sometime"]);
                }

                $user = User::where('id', \Auth::user()->id)->first();
                $ifAcc = 0;
                if (!empty($post->account)) {
                    $user->account = $post->account;
                    $ifAcc = 1;
                }
                if (!empty($post->ifsc)) {
                    $user->ifsc = $post->ifsc;
                    $ifAcc = 1;
                }
                if (!empty($post->bank)) {
                    $user->bank = $post->bank;
                    $ifAcc = 1;
                }
                if ($ifAcc) {
                    $user->save();
                }
                if ($user->account == '' || $user->ifsc == '') {
                    return response()->json(['statuscode' => "ERR", 'message' => "Bank Not added Please add bank"]);
                }

                $post['user_id'] = \Auth::id();
                $rules = array(

                    'amount' => 'required|numeric|gt:1|max:200000',
                    'ifsc' => 'sometimes|required|string|size:11',
                    'comments' => 'sometimes|required'
                );


                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json($validator->errors(), 422);
                }
                if (!empty($post->bankacccount)) {
                    $bankAcc = explode("-", $post->bankacccount);
                    $post['account'] = $bankAcc[0];
                    $post['bank'] = $bankAcc[2];
                    $post['ifsc'] = $bankAcc[1];
                } else {
                    $post['account'] = $user->account;
                    $post['bank'] = $user->bankname;
                    $post['ifsc'] = $user->ifsc;
                }




                $settlerequest = Aepsfundrequest::where('user_id', \Auth::user()->id)->where('status', 'pending')->count();
                if ($settlerequest > 0) {
                    return response()->json(["statuscode" => "ERR", 'message' => "One request is already submitted"]);
                }

                $post['charge'] = 0;
                if ($post->amount <= 25000) {
                    $post['charge'] = $impschargeupto25;
                }

                if ($post->amount > 25000) {
                    $post['charge'] = $impschargeabove25;
                }

                if ($user->aepsbalance < $post->amount + $post->charge) {
                    return response()->json(['statuscode' => "ERR", 'message' => "Low aeps balance to make this request."]);
                }

                if ($banksettlementtype == "auto") {

                    $previousrecharge = Aepsfundrequest::where('account', $post->account)->where('amount', $post->amount)->where('user_id', $post->user_id)->whereBetween('created_at', [Carbon::now()->subSeconds(30)->format('Y-m-d H:i:s'), Carbon::now()->addSeconds(30)->format('Y-m-d H:i:s')])->count();
                    if ($previousrecharge) {
                        return response()->json(["statuscode" => "ERR", 'message' => "Transaction Allowed After 1 Min."]);
                    }

                    $api = Api::where('code', 'cyrusfund')->first();
                    do {
                        $post['payoutid'] = $this->transcode() . rand(111111111111, 999999999999);
                    } while (Aepsfundrequest::where("payoutid", "=", $post->payoutid)->first() instanceof Aepsfundrequest);

                    $post['status'] = "pending";
                    $post['pay_type'] = "payout";
                    $post['mode'] = "IMPS";
                    $post['payoutid'] = $post->payoutid;
                    $post['payoutref'] = $post->payoutid;
                    $post['create_time'] = Carbon::now()->toDateTimeString();
                    try {
                        $aepsrequest = Aepsfundrequest::create($post->all());
                    } catch (\Exception $e) {
                        return response()->json(["statuscode" => "ERR", 'message' => "Duplicate Transaction Not Allowed, Please Check Transaction History"]);
                    }

                    /******************* Cyrus Post Data **********************/
                    $postData['MerchantID'] = $this->fundapi->username;
                    $postData['MerchantKey'] = $this->fundapi->optional1;
                    $postData['MethodName'] = "sendmoney";
                    $postData['TransferType'] = "IMPS";
                    $postData['Name'] = $user->name;
                    $postData['MobileNo'] = $user->mobile;
                    $postData['beneficiaryIFSC'] = $post->ifsc;
                    $postData['beneficiaryAccount'] = $post->account;
                    $postData['comments'] = $post->comments;
                    $postData['orderId'] = $post->payoutid;
                    $postData['amount'] = $post->amount;
                    /*********************End Post Data ***********************/
                    $aepsreports['api_id'] = $api->id;
                    $aepsreports['payid'] = $aepsrequest->id;
                    $aepsreports['mobile'] = $user->mobile;
                    $aepsreports['refno'] = "success";
                    $aepsreports['aadhar'] = $post->account;
                    $aepsreports['amount'] = $post->amount;
                    $aepsreports['charge'] = $post->charge;
                    $aepsreports['bank'] = $post->bank . "(" . $post->ifsc . ")";
                    $aepsreports['txnid'] = $post->payoutid;
                    $aepsreports['user_id'] = $user->id;
                    $aepsreports['mode'] = "IMPS";
                    $aepsreports['credited_by'] = $this->admin->id;
                    $aepsreports['balance'] = $user->aepsbalance;
                    $aepsreports['type'] = "debit";
                    $aepsreports['transtype'] = 'fund';
                    $aepsreports['status'] = 'success';
                    $aepsreports['remark'] = "Bank Settlement";

                    User::where('id', $aepsreports['user_id'])->decrement('aepsbalance', $aepsreports['amount'] + $aepsreports['charge']);
                    $myaepsreport = Aepsreport::create($aepsreports);

                    $url = $this->fundapi->url . '/api/PayoutAPI.aspx';
                    $header = array(
                        'Content-Type: multipart/form-data',

                    );
                    $query = http_build_query($postData, '', '&');
                    $result = \Myhelper::curl($url, "POST", $query, [], 'yes');

                    \DB::table('rp_log')->insert([
                        'ServiceName' => "Payout ",
                        'header' => json_encode([]),
                        'body' => $query,
                        'response' => json_encode($result),
                        'url' => $url,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);

                    $response = json_decode($result['response']);

                    if (isset($response->statuscode) && $response->statuscode == "DE_001") {
                        Aepsfundrequest::updateOrCreate(
                            ['id' => $aepsrequest->id],
                            ['status' => "approved", "payoutref" => $response->data->rrn, 'apitxnid' => $response->data->cyrus_id]
                        );
                        return response()->json(['status' => "success"]);
                    } else if (isset($response->statuscode) && $response->statuscode == "ERR") {

                        User::where('id', $aepsreports['user_id'])->increment('aepsbalance', $aepsreports['amount'] + $aepsreports['charge']);
                        Aepsreport::updateOrCreate(['id' => $myaepsreport->id], ['status' => "failed", "refno" => isset($response->ackno) ? $response->ackno : $response->message]);

                        Aepsfundrequest::updateOrCreate(['id' => $aepsrequest->id], ['status' => "rejected"]);
                        return response()->json(['status' => 'ERR', 'message' => $response->message]);
                    }
                    return response()->json(['statuscode' => "ERR", 'message' => "pending"]);
                } else {
                    $post['pay_type'] = "manual";
                    $request = Aepsfundrequest::create($post->all());
                }

                if ($request) {
                    return response()->json(['status' => "TXN", 'message' => "Fund request successfully submitted"]);
                } else {
                    return response()->json(['status' => "ERR", 'message' => "Something went wrong."]);
                }
                break;

            case 'wallet':
                if ($this->pinCheck($post) == "fail") {
                    // return response()->json(['status' => "Transaction Pin is incorrect"]);
                }
                if (!\Myhelper::can('aeps_fund_request')) {
                    return response()->json(["statuscode" => "ERR", 'message' => "Permission not allowed"]);
                }
                $settlementtype = $this->settlementtype();

                if ($settlementtype == "down") {
                    return response()->json(["statuscode" => "ERR", 'message' => "Aeps Settlement Down For Sometime"]);
                }

                $rules = array(
                    'amount' => 'required|numeric|min:1',
                );

                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(["statuscode" => "ERR", 'message' => $validator->errors()->first()]);
                }

                $user = User::where('id', \Auth::user()->id)->first();

                $request = Aepsfundrequest::where('user_id', \Auth::user()->id)->where('status', 'pending')->count();
                if ($request > 0) {
                    return response()->json(['statuscode' => "ERR", 'message' => "One request is already submitted"]);
                }

                if (\Auth::user()->aepsbalance < $post->amount) {
                    return response()->json(['statuscode' => "ERR", 'message' => "Low aeps balance to make this request"]);
                }

                $post['user_id'] = \Auth::id();

                if ($settlementtype == "auto") {
                    $previousrecharge = Aepsfundrequest::where('type', $post->type)->where('amount', $post->amount)->where('user_id', $post->user_id)->whereBetween('created_at', [Carbon::now()->subMinutes(5)->format('Y-m-d H:i:s'), Carbon::now()->format('Y-m-d H:i:s')])->count();
                    if ($previousrecharge > 0) {
                        return response()->json(['statuscode' => "ERR", 'message' => "Transaction Allowed After 5 Min."]);
                    }

                    $post['status'] = "approved";
                    $load = Aepsfundrequest::create($post->all());
                    $payee = User::where('id', \Auth::id())->first();
                    User::where('id', $payee->id)->decrement('aepsbalance', $post->amount);
                    $inserts = [
                        "mobile" => $payee->mobile,
                        "amount" => $post->amount,
                        "bank" => $payee->bank,
                        'txnid' => date('ymdhis'),
                        'refno' => $post->refno,
                        "user_id" => $payee->id,
                        "credited_by" => $user->id,
                        "balance" => $payee->aepsbalance,
                        'type' => "debit",
                        'transtype' => 'fund',
                        'status' => 'success',
                        'remark' => "Move To Wallet Request",
                        'payid' => "Wallet Transfer Request",
                        'aadhar' => $payee->account
                    ];

                    Aepsreport::create($inserts);

                    if ($post->type == "wallet") {
                        $provide = Provider::where('recharge1', 'aepsfund')->first();
                        User::where('id', $payee->id)->increment('mainwallet', $post->amount);
                        $insert = [
                            'number' => $payee->account,
                            'mobile' => $payee->mobile,
                            'provider_id' => $provide->id,
                            'api_id' => $this->fundapi->id,
                            'amount' => $post->amount,
                            'charge' => '0.00',
                            'profit' => '0.00',
                            'gst' => '0.00',
                            'tds' => '0.00',
                            'txnid' => $load->id,
                            'payid' => $load->id,
                            'refno' => $post->refno,
                            'description' => "Aeps Fund Recieved",
                            'remark' => $post->remark,
                            'option1' => $payee->name,
                            'status' => 'success',
                            'user_id' => $payee->id,
                            'credit_by' => $payee->id,
                            'rtype' => 'main',
                            'via' => 'portal',
                            'balance' => $payee->mainwallet,
                            'trans_type' => 'credit',
                            'product' => "fund request"
                        ];

                        Report::create($insert);
                    }
                } else {
                    $load = Aepsfundrequest::create($post->all());
                }

                if ($load) {
                    return response()->json(['statuscode' => "TXN", 'message' => "success"]);
                } else {
                    return response()->json(['statuscode' => "TXF", 'message' => "fail"]);
                }
                break;


            default:
                # code...
                break;
        }
    }

    public function transactionRunpaisa(Request $post)
    {

        $rules["type"] = ['required'];
        $rules["user_id"] = ['required', 'numeric'];
        $rules["apptoken"] = ['required'];

        switch ($post->type) {
            case 'bank':
                $rules = [
                    "account" => "required",
                    "bank" => "required|numeric",
                    "ifsc" => "required|string|size:11",
                    "amount" => 'required|numeric|min:1|max:200000'
                ];
            case 'wallet':
            case 'getPaymentLink':
                $rules = array(
                    'amount' => 'required|numeric|min:1|max:199999',
                    // 'user_id' => 'required|numeric|min:1|max:199999',
                    // 'apptoken' => 'required',
                );
                break;
        }

        $validator = Validator::make($post->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['statuscode' => 'ERR', 'message' => $validator->errors()->first()]);
        }


        if ($post->user_id == null) {
            return response()->json(['statuscode' => "ERR", "message" => "User Not Found"]);
        }


        if ($this->fundapi->status == "0") {
            return response()->json(['statuscode' => "ERR", 'message' => "This service is down."]);
        }
        $provide = Provider::where('recharge1', 'fund')->first();
        $post['provider_id'] = $provide->id;

        switch ($post->type) {
            case 'getBankAccount':
                $getBank = \DB::table('users')->where('id', $post->user_id)->first();
                $data['userbanks'][0] = ['account' => @$getBank->account, 'ifsc' => @$getBank->ifsc, 'bank' => @$getBank->bank];
                $data['userbanks'][1] = ['account' => @$getBank->account2, 'ifsc' => @$getBank->ifsc2, 'bank' => @$getBank->bank2];
                $data['userbanks'][2] = ['account' => @$getBank->account3, 'ifsc' => @$getBank->ifsc3, 'bank' => @$getBank->bank3];


                $data['aepstiming'] = \DB::table('portal_settings')->where('code', 'aepsslabtime')->first();
                $data['settlementcharge'] = \DB::table('portal_settings')->where('code', 'settlementcharge')->first();
                $data['impschargeupto25'] = \DB::table('portal_settings')->where('code', 'impschargeupto25')->first();
                $data['impschargeabove25'] = \DB::table('portal_settings')->where('code', 'impschargeabove25')->first();


                return response()->json(['statuscode' => "TXN", "message" => "Bank fetch successfully", "data" => $data]);

            case 'bank':

                if ($this->pinCheck($post) == "fail") {
                    return response()->json(['statuscode' => "ERR", "message" => "Transaction Pin is incorrect"]);
                }
                $banksettlementtype = $this->banksettlementtype();
                $impschargeupto25 = $this->impschargeupto25();
                $impschargeabove25 = $this->impschargeabove25();

                if ($banksettlementtype == "down") {
                    return response()->json(['statuscode' => "ERR", 'message' => "Aeps Settlement Down For Sometime"]);
                }

                $user = User::where('id', $post->user_id)->first();
                $ifAcc = 0;
                if (!empty($post->account)) {
                    $user->account = $post->account;
                    $ifAcc = 1;
                }
                if (!empty($post->ifsc)) {
                    $user->ifsc = $post->ifsc;
                    $ifAcc = 1;
                }
                if (!empty($post->bank)) {
                    $user->bank = $post->bank;
                    $ifAcc = 1;
                }
                if ($ifAcc) {
                    $user->save();
                }
                if ($user->account == '' || $user->ifsc == '') {
                    return response()->json(["statuscode" => "TXN", 'message' => "Bank Not added Please add bank"]);
                }

                // $post['user_id'] = \Auth::id();
                $rules = array(

                    'amount' => 'required|numeric|gt:1|max:200000',
                    'ifsc' => 'sometimes|required|string|size:11',
                    'comments' => 'sometimes|required'
                );


                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(["statuscode" => "ERR", "message" => $validator->errors()->first()]);
                }
                // if (!empty($post->bankacccount)) {
                //     $bankAcc = explode("-", $post->bankacccount);
                //     $post['account'] = $bankAcc[0];
                //     $post['bank'] = $bankAcc[2];
                //     $post['ifsc'] = $bankAcc[1];
                // } else {
                // $post['account'] = $user->account;
                // $post['bank'] = $user->bankname;
                // $post['ifsc'] = $user->ifsc; 
                // }




                $settlerequest = Aepsfundrequest::where('user_id', $post->user_id)->where('status', 'pending')->count();

                if ($settlerequest > 0) {
                    return response()->json(['statuscode' => "ERR", 'message' => "One request is already submitted"]);
                }

                $post['charge'] = 0;
                if ($post->amount <= 25000) {
                    $post['charge'] = $impschargeupto25;
                }

                if ($post->amount > 25000) {
                    $post['charge'] = $impschargeabove25;
                }

                if ($user->aepsbalance < $post->amount + $post->charge) {
                    return response()->json(['statuscode' => "ERR", 'message' => "Low aeps balance to make this request."]);
                }

                if ($banksettlementtype == "auto") {

                    $previousrecharge = Aepsfundrequest::where('account', $post->account)->where('amount', $post->amount)->where('user_id', $post->user_id)->whereBetween('created_at', [Carbon::now()->subSeconds(30)->format('Y-m-d H:i:s'), Carbon::now()->addSeconds(30)->format('Y-m-d H:i:s')])->count();
                    if ($previousrecharge) {
                        return response()->json(['message' => "Transaction Allowed After 1 Min.", 'statuscode' => "TXF"]);
                    }

                    $bankpayoutapi = $this->bankpayoutapi();

                    if ($bankpayoutapi == "cyruspayout") {
                        $api = Api::where('code', 'cyrus')->first();
                    } else if ($bankpayoutapi == "runpaisa") {
                        $api = Api::where('code', 'runpaisafund')->first();
                    } else {
                        return response()->json(['status' => "Settlement Service is down."]);
                    }


                    do {
                        $post['payoutid'] = $this->transcode() . rand(111111111111, 999999999999);
                    } while (Aepsfundrequest::where("payoutid", "=", $post->payoutid)->first() instanceof Aepsfundrequest);

                    $post['status'] = "pending";
                    $post['pay_type'] = "payout";
                    $post['mode'] = "IMPS";
                    $post['payoutid'] = $post->payoutid;
                    $post['payoutref'] = $post->payoutid;
                    $post['create_time'] = Carbon::now()->toDateTimeString();
                    try {
                        $aepsrequest = Aepsfundrequest::create($post->all());
                    } catch (\Exception $e) {
                        return response()->json(["statuscode" => "ERR", 'message' => "Duplicate Transaction Not Allowed, Please Check Transaction History"]);
                    }

                    $aepsreports['api_id'] = $api->id;
                    $aepsreports['payid'] = $aepsrequest->id;
                    $aepsreports['mobile'] = $user->mobile;
                    $aepsreports['refno'] = "success";
                    $aepsreports['aadhar'] = $post->account;
                    $aepsreports['amount'] = $post->amount;
                    $aepsreports['charge'] = $post->charge;
                    $aepsreports['bank'] = $post->bank . "(" . $post->ifsc . ")";
                    $aepsreports['txnid'] = $post->payoutid;
                    $aepsreports['user_id'] = $user->id;
                    $aepsreports['mode'] = "IMPS";
                    $aepsreports['credited_by'] = $this->admin->id;
                    $aepsreports['balance'] = $user->aepsbalance;
                    $aepsreports['type'] = "debit";
                    $aepsreports['transtype'] = 'fund';
                    $aepsreports['status'] = 'success';
                    $aepsreports['remark'] = "Bank Settlement";

                    User::where('id', $aepsreports['user_id'])->decrement('aepsbalance', $aepsreports['amount'] + $aepsreports['charge']);
                    $myaepsreport = Aepsreport::create($aepsreports);

                    $url = $api->url;

                    if ($bankpayoutapi == "cyruspayout") {
                        /******************* Cyrus Post Data **********************/
                        $postData['MerchantID'] = $this->fundapi->username;
                        $postData['MerchantKey'] = $this->fundapi->optional1;
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
                        $result = \Myhelper::curl($url, "POST", $query, [], 'yes');

                        \DB::table('rp_log')->insert([
                            'ServiceName' => "CyrusDMT",
                            'header' => json_encode([]),
                            'body' => $query,
                            'response' => json_encode($result),
                            'url' => $url,
                            'created_at' => date('Y-m-d H:i:s')
                        ]);

                        $response = json_decode($result['response']);


                        if (isset($response->statuscode) && $response->statuscode == "DE_001") {

                            Aepsfundrequest::where('id', $aepsrequest->id)->update(['status' => "approved", "payoutref" => (isset($response->data->rrn)) ? $response->data->rrn : $response->data->cyrus_id, 'apitxnid' => isset($response->data->orderId) ? $response->data->orderId : $response->data->cyrus_id]);
                            Aepsreport::where('id', $myaepsreport->id)->update(['status' => "success", "refno" => (isset($response->cyrus_id)) ? $response->cyrus_id : $response->status, "apitxnid" => isset($response->data->orderId) ? $response->data->orderId : "pending"]);
                            return response()->json(['status' => "success"]);

                        } else if (isset($response->statuscode) && $response->statuscode == "ERR") {
                            $refno = isset($response->cyrus_id) ? $response->cyrus_id : $response->status;
                            User::where('id', $aepsreports['user_id'])->increment('aepsbalance', $aepsreports['amount'] + $aepsreports['charge']);
                            Aepsreport::updateOrCreate(['id' => $myaepsreport->id], ['status' => "failed", "refno" => $refno]);

                            Aepsfundrequest::updateOrCreate(['id' => $aepsrequest->id], ['status' => "rejected"]);
                            return response()->json(['status' => 'ERR', 'message' => $response->message ?? 'Transaction Failed',]);


                        }


                    } elseif ($bankpayoutapi == "runpaisa") {


                        /******************* Cyrus Post Data **********************/
                        $authToken = $this->getRunpaisaToken();


                        $postData = [
                            "amount" => $post->amount,
                            "orderId" => $post->payoutid,
                            "paymentMode" => "IMPS",
                            "callbackurl" => url('') . '/api/callback/update/runpaisa',
                            "beneficiaryName" => $user->name,
                            "beneficiaryAccountNumber" => $post->account,
                            "beneficiaryIfscCode" => $post->ifsc,
                        ];

                        /*********************End Post Data ***********************/

                        $header = array(
                            'token: ' . $authToken,
                            'client_id: ' . $api->optional1,
                            'Content-Type: multipart/form-data'
                        );
                        $url = $this->runpaisafundapi->optional2 . "/payment";

                        $query = http_build_query($postData, '', '&');
                        $result = \Myhelper::curl($url, "POST", $postData, $header, 'yes', 'Runpaisa');

                        \DB::table('rp_log')->insert([
                            'ServiceName' => "RUNPAISA_Payout ",
                            'header' => json_encode($header),
                            'body' => $query,
                            'response' => json_encode($result),
                            'url' => $url,
                            'created_at' => date('Y-m-d H:i:s')
                        ]);

                        $response = json_decode($result['response']);

                        if (isset($response->status) && $response->status == "ACCEPTED") {
                            /* Aepsfundrequest::updateOrCreate(['id'=> $aepsrequest->id],
                              ['status' => "approved"]); */
                            return response()->json(["statuscode" => "TXN", "message" => "Request Accepted"]);
                        } else if (isset($response->status) && ($response->status == "ERR" || $response->status == "FAIL")) {

                            User::where('id', $aepsreports['user_id'])->increment('aepsbalance', $aepsreports['amount'] + $aepsreports['charge']);
                            Aepsreport::updateOrCreate(['id' => $myaepsreport->id], ['status' => "failed", "refno" => isset($response->ackno) ? $response->ackno : $response->message]);

                            Aepsfundrequest::updateOrCreate(['id' => $aepsrequest->id], ['status' => "rejected"]);
                            return response()->json(['statuscode' => 'TXF', 'message' => $response->message]);
                        }

                        return response()->json(["statuscode" => "ERR", "message" => "something went wrong,please try after sometimes"]);
                    } else {
                        return response()->json(['status' => "Settlement Down for sometime"]);

                    }
                } else {
                    $post['pay_type'] = "manual";
                    $request = Aepsfundrequest::create($post->all());
                }

                if ($request) {
                    return response()->json(["statuscode" => "TXN", 'message' => "Fund request successfully submitted"]);
                } else {
                    return response()->json(["statuscode" => "ERR", 'message' => "Something went wrong."]);
                }
                break;

            case 'wallet':
                if ($this->pinCheck($post) == "fail") {
                    return response()->json(['statuscode' => "ERR", "message" => "Transaction Pin is incorrect"]);
                }
                if (!\Myhelper::can('aeps_fund_request', $post->user_id)) {
                    return response()->json(['statuscode' => "ERR", "message" => "Permission not allowed"]);
                }
                $settlementtype = $this->settlementtype();

                if ($settlementtype == "down") {
                    return response()->json(['statuscode' => "ERR", "message" => "Aeps Settlement Down For Sometime"]);
                }

                // $rules = array(
                //     'amount' => 'required|numeric|min:1|max:199999',
                // );

                // $validator = \Validator::make($post->all(), $rules);
                // if ($validator->fails()) {
                //     return response()->json(['statuscode' => "ERR", "message" => $validator->errors()->first(), 'status' => $validator->errors()->first()]);
                // }

                $user = User::where('id', $post->user_id)->first();

                $request = Aepsfundrequest::where('user_id', $post->user_id)->where('status', 'pending')->count();
                if ($request > 0) {
                    return response()->json(['statuscode' => "ERR", 'message' => "One request is already submitted"]);
                }

                if ($user->aepsbalance < $post->amount) {
                    return response()->json(['statuscode' => "ERR", 'message' => "Low aeps balance to make this request"]);
                }

                // $post['user_id'] = \Auth::id();

                if ($settlementtype == "auto") {
                    $previousrecharge = Aepsfundrequest::where('type', $post->type)->where('amount', $post->amount)->where('user_id', $post->user_id)->whereBetween('created_at', [Carbon::now()->subMinutes(5)->format('Y-m-d H:i:s'), Carbon::now()->format('Y-m-d H:i:s')])->count();
                    if ($previousrecharge > 0) {
                        return response()->json(["statuscode" => 'ERR', 'message' => "Transaction Allowed After 5 Min."]);
                    }

                    $post['status'] = "approved";
                    $load = Aepsfundrequest::create($post->all());
                    $payee = User::where('id', $post->user_id)->first();
                    User::where('id', $payee->id)->decrement('aepsbalance', $post->amount);
                    $inserts = [
                        "mobile" => $payee->mobile,
                        "amount" => $post->amount,
                        "bank" => $payee->bank,
                        'txnid' => date('ymdhis'),
                        'refno' => $post->refno,
                        "user_id" => $payee->id,
                        "credited_by" => $user->id,
                        "balance" => $payee->aepsbalance,
                        'type' => "debit",
                        'transtype' => 'fund',
                        'status' => 'success',
                        'remark' => "Move To Wallet Request",
                        'payid' => "Wallet Transfer Request",
                        'aadhar' => $payee->account
                    ];

                    Aepsreport::create($inserts);

                    if ($post->type == "wallet") {
                        $provide = Provider::where('recharge1', 'aepsfund')->first();
                        User::where('id', $payee->id)->increment('mainwallet', $post->amount);
                        $insert = [
                            'number' => $payee->account,
                            'mobile' => $payee->mobile,
                            'provider_id' => $provide->id,
                            'api_id' => $this->fundapi->id,
                            'amount' => $post->amount,
                            'charge' => '0.00',
                            'profit' => '0.00',
                            'gst' => '0.00',
                            'tds' => '0.00',
                            'txnid' => $load->id,
                            'payid' => $load->id,
                            'refno' => $post->refno,
                            'description' => "Aeps Fund Recieved",
                            'remark' => $post->remark,
                            'option1' => $payee->name,
                            'status' => 'success',
                            'user_id' => $payee->id,
                            'credit_by' => $payee->id,
                            'rtype' => 'main',
                            'via' => 'portal',
                            'balance' => $payee->mainwallet,
                            'trans_type' => 'credit',
                            'product' => "fund request"
                        ];

                        Report::create($insert);
                    }
                } else {
                    $load = Aepsfundrequest::create($post->all());
                }

                if ($load) {
                    return response()->json(["statuscode" => "TXN", "message" => "Wallet transafer successfull", "txnid" => $load->id]);
                } else {
                    return response()->json(["statuscode" => "TXN", "message" => "something went wrong"]);
                }
                break;


            case 'getPaymentLink':
                // $validator = \Validator::make($post->all(), $rules);
                // if ($validator->fails()) {
                //     return response()->json(['statuscode' => "ERR", 'message' => $validator->errors()->first(), 'status' => $validator->errors()->first()]);
                // }

                $api = Api::where('code', 'runpaisa_pg')->first();
                if (!$api || $api->status == 0) {
                    return response()->json(['statuscode' => "ERR", 'message' => "PG Service Currently Down"]);
                }


                $token = $this->getRunpaisaTokenPG();

                $request = [];

                $header = array(
                    'Content-Type:multipart/form-data',
                    'client_id: ' . $api->optional1,
                    'token:' . $token
                );

                $user = User::where('id', $post->user_id)->first();

                do {
                    $post['txnid'] = $post->user_id . "_order_" . rand(1111111111, 9999999999);
                } while (Aepsreport::where("txnid", "=", $post->txnid)->first() instanceof Aepsreport);


                $parameter = array(
                    "callbackurl" => url('') . "/api/runpaisa/callback/runpaisaPg",
                    "order_id" => $post->txnid,
                    "amount" => $post->amount,
                );

                $query = $parameter;

                $url = $api->optional2 . "/order";

                $result = \Myhelper::curl($url, 'POST', $query, $header, "yes", 'PG');

                if ($result['response'] != '') {
                    $datas = json_decode($result['response']);
                    // dd($datas);
                    if (isset($datas->status) && $datas->status == 'SUCCESS') {
                        return response()->json(['statuscode' => 'TXN', "message" => "Link generated Successfully", 'data' => $datas->paymentLink]);

                    } else {
                        return response()->json(['statuscode' => 'TXF', 'message' => $datas->message ?? "Try after sometimes"]);
                    }
                } else {

                    return response()->json(['statuscode' => 'TXF', 'message' => $result['error'] ?? "try after sometimes"]);
                }

            case 'transfer':
            case 'return':
                $request = $post;
                if ($request->type == "transfer" && !\Myhelper::can('fund_transfer', $request->user_id)) {
                    return response()->json(['statuscode' => "ERR", "message" => "Permission not allowed"]);
                }

                if ($request->type == "return" && !\Myhelper::can('fund_return', $request->user_id)) {
                    return response()->json(['statuscode' => "ERR", "message" => "Permission not allowed1"]);
                }

                $provide = Provider::where('recharge1', 'fund')->first();
                $request['provider_id'] = $provide->id;

                $rules = array(
                    'amount' => 'required|numeric|min:1',
                    'id' => 'required'
                );

                $validate = \Myhelper::FormValidator($rules, $request);
                if ($validate != "no") {
                    return $validate;
                }

                $user = User::where('id', $request->user_id)->first();
                $payee = User::where('id', $request->id)->first();

                if (!$payee) {
                    return response()->json(['statuscode' => "ERR", "message" => "PAYEE User not found"]);

                }

                if (!$user) {
                    return response()->json(['statuscode' => "ERR", "message" => "User not found"]);

                }

                if ($request->type == "transfer") {
                    if ($user->mainwallet < $request->amount) {
                        return response()->json(['statuscode' => "ERR", "message" => "Insufficient wallet balance."]);
                    }
                } else {
                    if ($payee->mainwallet - $payee->lockedamount < $request->amount) {
                        return response()->json(['statuscode' => "ERR", "message" => "Insufficient balance in user wallet."]);
                    }
                }
                $request['txnid'] = 0;
                $request['option1'] = 0;
                $request['option2'] = 0;
                $request['option3'] = 0;
                $request['refno'] = date('ymdhis');
                return $this->paymentAction($request);
                break;

            case 'getfundbank':
                $request = $post;
                $rules = array(
                    'apptoken' => 'required',
                    'user_id' => 'required|numeric'
                );

                $validate = \Myhelper::FormValidator($rules, $request);
                if ($validate != "no") {
                    return $validate;
                }
                $user = User::where('id', $request->user_id)->first();
                $data['banks'] = Fundbank::where('user_id', $user->parent_id)->where('status', '1')->get();
                if (!\Myhelper::can('setup_bank', $user->parent_id)) {
                    $admin = User::whereHas('role', function ($q) {
                        $q->where('slug', 'whitelable');
                    })->where('company_id', $user->company_id)->first(['id']);

                    if ($admin && \Myhelper::can('setup_bank', $admin->id)) {
                        $data['banks'] = Fundbank::where('user_id', $admin->id)->where('status', '1')->get();
                        if (!$data['banks']) {
                            $admin = User::whereHas('role', function ($q) {
                                $q->where('slug', 'admin');
                            })->first(['id']);
                            $data['banks'] = Fundbank::where('user_id', $admin->id)->where('status', '1')->get();
                        }
                    } else {
                        $admin = User::whereHas('role', function ($q) {
                            $q->where('slug', 'admin');
                        })->first(['id']);
                        $data['banks'] = Fundbank::where('user_id', $admin->id)->where('status', '1')->get();
                    }
                } else {
                    $admin = User::whereHas('role', function ($q) {
                        $q->where('slug', 'admin');
                    })->first(['id']);
                    $data['banks'] = Fundbank::where('user_id', $admin->id)->where('status', '1')->get();
                }
                $data['paymodes'] = Paymode::where('status', '1')->get();
                return response()->json(['statuscode' => "TXN", "message" => "Get successfully", "data" => $data]);
                break;
            case 'request':
                $request = $post;
                if (!\Myhelper::can('fund_request', $request->user_id)) {
                    return response()->json(['statuscode' => "ERR", "message" => "Permission not allowed"]);
                }

                $rules = array(
                    'fundbank_id' => 'required|numeric',
                    'paymode' => 'required',
                    'amount' => 'required|numeric|min:100',
                    'ref_no' => 'required|unique:fundreports,ref_no',
                    'paydate' => 'required',
                    'apptoken' => 'required'
                );

                $validate = \Myhelper::FormValidator($rules, $request);
                if ($validate != "no") {
                    return $validate;
                }
                $user = User::where('id', $request->user_id)->first();

                $request['user_id'] = $user->id;
                $request['credited_by'] = $user->parent_id;
                if (!\Myhelper::can('setup_bank', $user->parent_id)) {
                    $admin = User::whereHas('role', function ($q) {
                        $q->where('slug', 'whitelable');
                    })->where('company_id', $user->company_id)->first(['id']);

                    if ($admin && \Myhelper::can('setup_bank', $admin->id)) {
                        $request['credited_by'] = $admin->id;
                    } else {
                        $admin = User::whereHas('role', function ($q) {
                            $q->where('slug', 'admin');
                        })->first(['id']);
                        $request['credited_by'] = $admin->id;
                    }
                }

                $request['status'] = "pending";
                $action = Fundreport::create($request->all());
                if ($action) {
                    return response()->json(['statuscode' => "TXN", "message" => "Fund request send successfully", "txnid" => $action->id]);
                } else {
                    return response()->json(['statuscode' => "ERR", "message" => "Something went wrong, please try again."]);
                }
                break;

            case 'addbank':
                $request = $post;

                $rules = array(

                    'bankname' => 'required|string',
                    'account' => 'required|numeric',
                    'ifsc' => 'required'
                );

                $validator = Validator::make($request->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(["statuscode" => "ERR", "message" => $validator->errors()->first()]);
                }

                $user = User::where('id', \Auth::user()->id)->first();
                if (!$user) {
                    return response()->json(["statuscode" => "ERR", "message" => "user not found"]);
                }

                if ($request->account == $user->account || $request->account == $user->account2 || $request->account == $user->account3) {
                    return response()->json(['status' => 'ERR', 'message' => "Account already exist."]);
                }

                $updateBank = false;

                if ($user->account == '' && $user->bank == '' && $user->ifsc == '') {
                    $updateBank = User::where('id', \Auth::user()->id)->update(['account' => $request->account, 'bank' => $request->bankname, 'ifsc' => $request->ifsc]);
                } elseif ($user->account2 == '' && $user->bank2 == '' && $user->ifsc2 == '') {
                    $updateBank = User::where('id', \Auth::user()->id)->update(['account2' => $request->account, 'bank2' => $request->bankname, 'ifsc2' => $request->ifsc]);
                } elseif ($user->account3 == '' && $user->bank3 == '' && $user->ifsc3 == '') {
                    $updateBank = User::where('id', \Auth::user()->id)->update(['account3' => $request->account, 'bank3' => $request->bankname, 'ifsc3' => $request->ifsc]);
                }

                if ($updateBank) {
                    return response()->json(["statuscode" => "TXN", "message" => "Bank added Successfully"]);
                } else {
                    return response()->json(["statuscode" => "ERR", "message" => "Bank Add Limit Reached"]);
                }


            default:
                # code...
                return response()->json(['statuscode'=>"ERR","message"=>"Invalid type used"]);
                break;
        }
    }


    public function getRunpaisaToken()
    {
        $request = [];
        $header = array(
            'client_id: ' . $this->runpaisafundapi->optional1,
            'username: ' . $this->runpaisafundapi->username,
            'password: ' . $this->runpaisafundapi->password,
            'Content-Type: application/json',
        );

        $url = $this->runpaisafundapi->url . "/token";
        $result = \Myhelper::curl($url, "POST", json_encode($request), $header, 'yes', 1, 'runpaisa', 'Runpaisa');
        $response['data'] = json_decode($result['response']);
        if (isset($response['data']->status) && $response['data']->status == 'SUCCESS') {
            return $response['data']->data->token;
        }
        return "";
    }

    public function getRunpaisaTokenPG()
    {

        $api = Api::where('code', 'runpaisa_pg')->first();
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

    public function paymentAction($post)
    {
        $user = User::where('id', $post->id)->first();

        if ($post->type == "transfer" || $post->type == "request") {
            $action = User::where('id', $post->id)->increment('mainwallet', $post->amount);
        } else {
            $action = User::where('id', $post->id)->decrement('mainwallet', $post->amount);
        }

        if ($action) {
            if ($post->type == "transfer" || $post->type == "request") {
                $post['trans_type'] = "credit";
            } else {
                $post['trans_type'] = "debit";
            }

            $insert = [
                'number' => $user->mobile,
                'mobile' => $user->mobile,
                'provider_id' => $post->provider_id,
                'api_id' => $this->fundfundapi->id,
                'amount' => $post->amount,
                'charge' => '0.00',
                'profit' => '0.00',
                'gst' => '0.00',
                'tds' => '0.00',
                'apitxnid' => NULL,
                'txnid' => $post->txnid,
                'payid' => NULL,
                'refno' => $post->refno,
                'description' => NULL,
                'remark' => $post->remark,
                'option1' => $post->option1,
                'option2' => $post->option2,
                'option3' => $post->option3,
                'option4' => NULL,
                'status' => 'success',
                'user_id' => $user->id,
                'credit_by' => $post->user_id,
                'rtype' => 'main',
                'via' => 'portal',
                'adminprofit' => '0.00',
                'balance' => $user->mainwallet,
                'trans_type' => $post->trans_type,
                'product' => "fund " . $post->type
            ];
            $action = Report::create($insert);
            if ($action) {
                return $this->paymentActionCreditor($post);
            } else {
                return response()->json(['statuscode' => "ERR", "message" => "Technical error, please contact your service provider before doing transaction."]);
            }
        } else {
            return response()->json(['statuscode' => "ERR", "message" => "Fund transfer failed, please try again."]);
        }
    }

    public function paymentActionCreditor($post)
    {
        $payee = $post->id;
        $user = User::where('id', $post->user_id)->first();
        if ($post->type == "transfer" || $post->type == "request") {
            $action = User::where('id', $user->id)->decrement('mainwallet', $post->amount);
        } else {
            $action = User::where('id', $user->id)->increment('mainwallet', $post->amount);
        }

        if ($action) {
            if ($post->type == "transfer" || $post->type == "request") {
                $post['trans_type'] = "debit";
            } else {
                $post['trans_type'] = "credit";
            }

            $insert = [
                'number' => $user->mobile,
                'mobile' => $user->mobile,
                'provider_id' => $post->provider_id,
                'api_id' => $this->fundfundapi->id,
                'amount' => $post->amount,
                'charge' => '0.00',
                'profit' => '0.00',
                'gst' => '0.00',
                'tds' => '0.00',
                'apitxnid' => NULL,
                'txnid' => $post->txnid,
                'payid' => NULL,
                'refno' => $post->refno,
                'description' => NULL,
                'remark' => $post->remark,
                'option1' => $post->option1,
                'option2' => $post->option2,
                'option3' => $post->option3,
                'option4' => NULL,
                'status' => 'success',
                'user_id' => $user->id,
                'credit_by' => $payee,
                'rtype' => 'main',
                'via' => 'portal',
                'adminprofit' => '0.00',
                'balance' => $user->mainwallet,
                'trans_type' => $post->trans_type,
                'product' => "fund " . $post->type
            ];

            $action = Report::create($insert);
            if ($action) {
                return response()->json(['statuscode' => "TXN", "message" => "Transaction Successfull"]);
            } else {
                return response()->json(['statuscode' => "ERR", "message" => "Technical error, please contact your service provider before doing transaction."]);
            }
        } else {
            return response()->json(['statuscode' => "ERR", "message" => "Technical error, please contact your service provider before doing transaction."]);
        }
    }


}