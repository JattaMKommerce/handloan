<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Models\Api;
use App\Models\Mahaagent;
use App\Models\Apitoken;
use App\Models\Provider;
use App\Models\Aepsreport;
use Carbon\Carbon;
use App\Models\Report;

class AepsController extends Controller
{
    protected $api;
    public function __construct()
    {
        $this->api    = Api::where('code', 'aeps')->first();
        $this->dmtapi = Api::where('code', 'dmt1')->first();
        $this->branchx_bankverifyapi = Api::where('code', 'branchx_bankverify')->first();
    }

    public function registration(Request $post)
    {
        $token = Apitoken::where('ip',$post->ip())->where('token', $post->token)->first(['user_id']);
        if(!$token){
            return response()->json(["statuscode"=>"IAT", 'status'=>"Invalid Api Token", 'message'=>"Invalid Api Token", "ip" => $post->ip()]);
        }

        $post['type'] = "kyc";
        $validate = $this->myvalidate($post);
        if($validate['status'] != 'NV'){
            return response()->json($validate, 400);
        }

        $post['user_id'] = $token->user_id;
        $userdata = User::where('id', $post->user_id)->first();
        if (!\Myhelper::can('aeps_service', $post->user_id)) {
            return response()->json(['statuscode' => "BPR", "status" => "Service Not Allowed", "message" => "Service Not Allowed"]);
        }

        $data["bc_f_name"] = $post->bc_f_name;
        $data["bc_m_name"] = "";
        $data["bc_l_name"] = $post->bc_l_name;
        $data["emailid"] = $post->emailid;
        $data["phone1"] = $post->phone1;
        $data["phone2"] = $post->phone2;
        $data["bc_dob"] = $post->bc_dob;
        $data["bc_state"] = $post->bc_state;
        $data["bc_district"] = $post->bc_district;
        $data["bc_address"] = $post->bc_address;
        $data["bc_block"] = $post->bc_block;
        $data["bc_city"] = $post->bc_city;
        $data["bc_landmark"] = $post->bc_landmark;
        $data["bc_mohhalla"] = $post->bc_mohhalla;
        $data["bc_loc"] = $post->bc_loc;
        $data["bc_pincode"] = $post->bc_pincode;
        $data["bc_pan"] = $post->bc_pan;
        $data["shopname"] = $post->shopname;
        $data["shopType"] = $post->shopType;
        $data["qualification"] = $post->qualification;
        $data["population"] = $post->population;
        $data["locationType"] = $post->locationType;
        $data["token"] = $this->api->username;
        $data['kyc1'] = $post->kyc1;
        $data['kyc2'] = $post->kyc2;
        $data['kyc3'] = $post->kyc3;
        $data['kyc4'] = $post->kyc4;

        $url = $this->api->url."registration";
        $header = array("Content-Type: application/json");
        $result = \Myhelper::curl($url, "POST", json_encode($data), $header, "yes", "Kyc", $post->user_id);
        if($result['response'] != ''){
            $datas = json_decode($result['response']);
            if(isset($datas->statuscode) && $datas->statuscode == "TXN"){
                $data['bc_id'] = $datas->message;
                $data['user_id'] = $post->user_id;
                $user = Mahaagent::create($data);
                return response()->json(['statuscode'=>'TXN', 'status'=>'Transaction Successfull', 'message'=> $datas->message]);
            }else{
                return response()->json(['statuscode'=>'TXF', 'status'=>'Transaction Failed', 'message'=> $datas->message]);
            }
        }else{
            return response()->json(['statuscode'=>'TXF', 'status'=>'Transaction Failed', 'message'=> "Something went wrong"]);
        }
    }

    public function myvalidate($post)
    {
        $validate = "yes";
        switch ($post->type) {
            case 'kyc':
                $rules = array('bc_f_name' => 'required','bc_l_name' => 'required','emailid' => 'required','phone1' => 'required|numeric|digits:10','bc_dob' => 'required','bc_state' => 'required','bc_district' => 'required','bc_address' => 'required','bc_block' => 'required','bc_city' => 'required','bc_landmark' => 'required','bc_mohhalla' => 'required','bc_mohhalla' => 'required','bc_loc' => 'required','bc_pincode' => 'required|numeric|digits:6','bc_pan' => 'required','shopname' => 'required','shopType' => 'required','qualification' => 'required','population' => 'required','locationType' => 'required');
            break;

            default:
                return ['statuscode'=>'BPR', "status" => "Bad Parameter Request", 'message'=> "Invalid request format"];
            break;
        }

        if($validate == "yes"){
            $validator = \Validator::make($post->all(), $rules);
            if ($validator->fails()) {
                foreach ($validator->errors()->messages() as $key => $value) {
                    $error = $value[0];
                }
                $data = ['statuscode'=>'BPR', "status" => "Bad Parameter Request", 'message'=> $error];
            }else{
                $data = ['status'=>'NV'];
            }
        }else{
            $data = ['status'=>'NV'];
        }
        return $data;
    }

    public function initiate(Request $post)
    {
        $token = Apitoken::where('ip',$post->ip())->where('token', $post->token)->first(['user_id']);
        if(!$token){
            return response()->json(["statuscode"=>"IAT", 'status'=>"Invalid Api Token", 'message'=>"Invalid Api Token", "ip" => $post->ip()]);
        }

        if (!\Myhelper::can('aeps_service', $token->user_id)) {
            return response()->json(['statuscode' => "BPR", "status" => "Service Not Allowed", "message" => "Service Not Allowed"]);
        }

        if (isset($post->aepstype) && $post->aepstype == "kotak" && !\Myhelper::can('kaeps_service', $token->user_id)) {
            return response()->json(['statuscode' => "BPR", "status" => "Service Not Allowed", "message" => "Service Not Allowed"]);
        }

        $data["bc_id"] = $post->bc_id;
        $data["phone1"] = $post->phone1;
        $data["token"] = $this->api->username;
        $data["aepstype"]   =$post->aepstype;

        $url = $this->api->url."initiate";
        $header = array("Content-Type: application/json");
        $result = \Myhelper::curl($url, "POST", json_encode($data), $header, "no");
        
        if($result['response'] != ''){
            $datas = json_decode($result['response']);
            if(isset($datas->statuscode) && $datas->statuscode == "TXN"){
                return response()->json(['statuscode' => 'TXN', "data" => $datas->data]);
            }else{
                return response()->json(['statuscode' => 'TXF', "data" => "Transaction Failed"]);
            }
        }else{
            return response()->json(['statuscode' => 'TXF', "data" => "Transaction Failed"]);
        }
    }

    public function getstate(Request $post)
    {
        $url = $this->dmtapi->url."/transaction";
        $header = array("Content-Type: application/json");
        $parameter["token"] = $this->dmtapi->username;
        $parameter["type"] = "getdistrict";
        $parameter["stateid"] = $post->stateid;     

        $result = \Myhelper::curl($url, "POST", json_encode($parameter), $header, "yes", 'App\Model\Report', '0');

        if ($result['error'] || $result['response'] == "") {
            return response()->json(["statuscode" => "ERR", 'status'=>'System Error'], 400);
        }
        $response = json_decode($result['response']);
        return response()->json($response);
    }

    public function transaction(Request $post)
    {
        $token = Apitoken::where('ip',$post->ip())->where('token', $post->token)->first(['user_id']);
        if(!$token){
            return response()->json(["statuscode"=>"IAT", 'status'=>"Invalid Api Token", 'message'=>"Invalid Api Token", "ip" => $post->ip()]);
        }

        if (!\Myhelper::can('aeps_service', $token->user_id)) {
            return response()->json(['statuscode' => "BPR", "status" => "Service Not Allowed", "message" => "Service Not Allowed"]);
        }

        if (isset($post->aepstype) && $post->aepstype == "kotak" && !\Myhelper::can('kaeps_service', $token->user_id)) {
            return response()->json(['statuscode' => "BPR", "status" => "Service Not Allowed", "message" => "Service Not Allowed"]);
        }

        $data["bc_id"] = $post->bc_id;
        $data["token"] = $this->api->username;
        // $data["code"]  = "EDSEC2308";
        $data["code"]  = $this->api->optional1;

        $url = $this->api->url."transaction";
        $header = array("Content-Type: application/json");
        $result = \Myhelper::curl($url, "POST", json_encode($data), $header, "no");
        // echo $result['response'];
        // exit();
        //return response()->json([$url, $data, $result]);
        if($result['response'] != ''){
            $datas = json_decode($result['response']);
            if(isset($datas->statuscode) && $datas->statuscode == "TXN"){
                return response()->json(['statuscode' => 'TXN', "data" => $datas->data]);
            }else{
                return response()->json(['statuscode' => 'TXF', "data" => "Transaction Failed"]);
            }
        }else{
            return response()->json(['statuscode' => 'TXF', "data" => "Transaction Failed"]);
        }
    }
    
    public function status(Request $post)
    {
        $rules = array(
            'token' => 'required',
            'txnid'  =>'required'
        );

        $validator = \Validator::make($post->all(), array_reverse($rules));
        if ($validator->fails()) {
            foreach ($validator->errors()->messages() as $key => $value) {
                $error = $value[0];
            }
            return response()->json(array(
                'statuscode' => 'BPR',
                'status'     => 'Bad Parameter Request',  
                'message'    => $error
            ));
        }

        $token = Apitoken::where('ip',$post->ip())->where('token', $post->token)->first(['user_id']);
        if(!$token){
            return response()->json(['statuscode' => 'BPR', 'status' => 'Bad Parameter Request', 'message'=>"Invalid Api token"]);
        }

        $report = Aepsreport::where('mytxnid', $post->txnid)->first();
        if(!$report){
            return response()->json(['statuscode' => 'TNF', 'status' => 'Bad Parameter Request', 'message'=>"Transaction Not Found"]);
        }

        if(!in_array($report->status , ['pending'])){
            $data['statuscode'] = "TXN";
            $data['trans_status'] = $report->status;
            $data['refno'] = $report->refno;
            return response()->json($data);
        }

        $url = $report->api->url.'status?token='.$report->api->username.'&txnid='.$report->mytxnid;
        $method = "GET";

        $result = \Myhelper::curl($url, $method, '', []);
        if($result['response'] != ''){
            $doc = json_decode($result['response']);
            if(isset($doc->statuscode)){
                if($doc->statuscode == "TXN" && $doc->trans_status == "success"){
                    $update['refno'] = $doc->refno;
                    $update['status'] = "success";
                }elseif($doc->statuscode == "TXN" && $doc->trans_status == "complete"){
                    $update['refno'] = $doc->refno;
                    $update['status'] = "complete";
                }elseif($doc->statuscode == "TXN" && $doc->trans_status == "failed"){
                    $update['status'] = "failed";
                    $update['refno'] = $doc->refno;
                }elseif($doc->statuscode == "TXN" && $doc->trans_status == "pending"){
                    $update['status'] = "pending";
                    $update['refno'] = $doc->refno;
                }else{
                    $update['status'] = "Unknown";
                }
            }else{
                $update['status'] = "Unknown";
            }
        }else{
            $update['status'] = "Unknown";
        }

        if ($update['status'] != "Unknown") {
            
            $reportupdate = Aepsreport::where('id', $report->id)->update($update);
            if($report->status == "pending" && in_array($update['status'], ['complete', 'success'])){
                $user = User::where('id', $report->user_id)->first();
                $insert = [
                    "mobile"  => $report->mobile,
                    "aadhar"  => $report->aadhar,
                    "api_id"  => $report->api_id,
                    "txnid"   => $report->txnid,
                    "refno"   => "Txnid - ".$report->id. " Cleared",
                    "amount"  => $report->amount,
                    "bank"    => $report->bank,
                    "user_id" => $report->user_id,
                    "balance" => $report->user->aepsbalance,
                    'aepstype'=> $report->aepstype,
                    'status'  => 'success',
                    'authcode'=> $report->authcode,
                    'payid'   => $report->payid,
                    'mytxnid' => $report->mytxnid,
                    'terminalid'  => $report->terminalid,
                    'TxnMedium'   => $report->TxnMedium,
                    'credited_by' => $report->credited_by,
                    'type'        => 'credit'
                ];
                
                if($report->amount >= 100 && $report->amount <= 3000){
                    $provider = Provider::where('recharge1', 'aeps1')->first();
                }elseif($report->amount>3000 && $report->amount<=10000){
                    $provider = Provider::where('recharge1', 'aeps2')->first();
                }
        
                $post['provider_id'] = $provider->id;
                $post['service']     = $provider->type;
    
                if($report->aepstype == "CW"){
                    if($report->amount > 500){
                        $usercommission = \Myhelper::getCommission($report->amount, $user->scheme_id, 'aeps',$post->provider_id, $user->role->slug);
                    }else{
                        $usercommission = 0;
                    }
                }else{
                    $usercommission = 0;
                }
                
                $insert['charge'] = $usercommission;
                $action = Aepsreport::where('id',$report->id)->update(['status' => 'complete', 'charge' => $usercommission]);
                
                if($action){
                    $aeps = Aepsreport::create($insert);
                    User::where('id', $report->user_id)->increment('aepsbalance', $report->amount + $usercommission);
                }
            }
        }
        $report = Aepsreport::where('id', $report->id)->first();
        
        $data['statuscode'] = "TXN";
        $data['trans_status'] = $report->status;
        $data['refno'] = $report->refno;
        return response()->json($data);
    }
    
    public function banksettlement(Request $post)
    {
       \DB::enableQueryLog();
        $token = Apitoken::where('token', $post->token)->first(['user_id','ip']);
        // print_r(\DB::getQueryLog());
        // echo $post->token . $post->ip().' !='. $token->ip;
        if(empty($token))
        {
            return response()->json(array(
                'status' => 'ERR',
                'message' => 'Unauthorized access.'
            ));
        }
        else if($post->ip() != $token->ip)
        {
            return response()->json(array(
                'status' => 'ERR',
                'message' => 'Unauthorized IP.'
            ));
        }
        $post['user_id'] = $token->user_id;
        
        $rules = array(
            'user_id' => 'required|numeric',
            'amount'  => 'required|numeric|min:10|max:200000',
            'name'    => 'required',
            'account' => 'required',
            'ifsc'    => 'required',
            'bank'    => 'required',
            'apitxnid'=> 'required|unique:reports,apitxnid',
            'callback'=> 'required|url',
            'ip'      => 'required|ip'
        );
        if (isset($post->paymode) && strtoupper($post->paymode) === 'UPI') {
            unset($rules['account'], $rules['ifsc'], $rules['bank']);
            $rules['upiid'] = 'required';
        }
        $validator = \Validator::make($post->all(), array_reverse($rules));
        if ($validator->fails()) {
            foreach ($validator->errors()->messages() as $key => $value) {
                $error = $value[0];
            }
            return response()->json(array(
                'status' => 'ERR',
                'message' => $error
            ));
        }

        $user = User::where('id', $post->user_id)->first();
        //$post['api_id']  = $this->api->id;
        $post['mode']    = $post->paymode;
        

        if (!\Myhelper::can(['payout_service'], $post->user_id)) {
            return \Response::json(["status" => "ERR", 'message' => "Permission not allowed"]);
        }
        
         
            
                
                
        if($post->mode == "IMPS"){
            if($post->amount > 1 && $post->amount <= 1000){
                    $provider = Provider::where('recharge1', 'payout1k')->first();
                    $providerflat = Provider::where('recharge1', 'payout1kflat')->first();
                }elseif($post->amount>1000 && $post->amount<=25000){
                    $provider = Provider::where('recharge1', 'payout25k')->first();
                    $providerflat = Provider::where('recharge1', 'payout25kflat')->first();
                }elseif($post->amount>25000 && $post->amount<=200000){
                    $provider = Provider::where('recharge1', 'payout2l')->first();
                    $providerflat = Provider::where('recharge1', 'payout2lflat')->first();
                }else{
                    $provider = Provider::where('recharge1', 'payout1k')->first();
                    $providerflat = Provider::where('recharge1', 'payout1kflat')->first();
                }
            
        }elseif($post->mode == "NEFT"){
            $provider = Provider::where('recharge1', 'payoutneft')->first();
            $providerflat = Provider::where('recharge1', 'payoutneftflat')->first();
        }elseif($post->mode == "RTGS"){
            $provider = Provider::where('recharge1', 'payoutrtgs')->first();
            $providerflat = Provider::where('recharge1', 'payoutrtgsflat')->first();
        }

       /* $post['provider_id'] = $provider->id;
        $post['charge'] = \Myhelper::getCommission($post->amount, $user->scheme_id, $post->provider_id, $user->role->slug);*/
        
        $post['provider_id'] = $provider->id;
        $post['provider_id_flat'] = $providerflat->id;
        $usercommission = \Myhelper::getCommission($post->amount, $user->scheme_id, $post->provider_id, $user->role->slug);
        $usercommissionflat = \Myhelper::getCommission($post->amount, $user->scheme_id, $post->provider_id_flat, $user->role->slug);
        $post['charge'] = $usercommission;
        if ($usercommission > $usercommissionflat) {
            $post['charge'] = $usercommission;
        } else {
            $post['charge'] = $usercommissionflat;
        }
        $post['gst'] =   ($post->charge * 18)/100;   
                
        if($user->mainwallet- $this->mainlocked($user->id) < $post->amount + $post->charge + $post->gst){
            return response()->json(["status" => "ERR", 'message' =>"Low balance, kindly recharge your wallet"]);
        }

        do {
            $post['txnid'] = $this->transcode().rand(1111111111, 9999999999);
        } while (Report::where("txnid", "=", $post->txnid)->first() instanceof Report); 
        
        $bankpayoutapi      = $this->bankpayoutapi($post->user_id);
        if (\Myhelper::can('payout_service', $post->user_id)) {
            if($bankpayoutapi == "razorpay"){
            $api = Api::where('code', 'razorpaypayout')->first();
            }else if($bankpayoutapi == 'haodapay'){
                $api = Api::where('code', 'haodapaypayout')->first();
            }else if($bankpayoutapi == 'xettle'){
                $api = Api::where('code', 'xettle')->first();
            }
            else if($bankpayoutapi == 'safex'){
                $api = Api::where('code', 'safex')->first();
            }
            else if($bankpayoutapi == 'paymonkpayout'){
                $api = Api::where('code', 'paymonkpayout')->first();
            }else if($bankpayoutapi == 'cbpayout'){
                $api = Api::where('code', 'touras')->first();
            }
            else if($bankpayoutapi == 'branchx'){
                $api = Api::where('code', 'bxpayout')->first();
            }
            else{
            $api = Api::where('code', 'psettlement')->first();
            }
        }else{
            return response()->json(["status" => "ERR", 'message' => "Service Down For Sometime1"]);
        }
        //print_r(\DB::getQueryLog());
        
        if(!$api || $api->status == "0") {
            //return response()->json(["status" => "ERR", 'message' => "Service Down For Sometime2".$bankpayoutapi]);
        }

        $previousrecharge = Report::where('number', $post->account)->where('amount', $post->amount)->where('user_id', $post->user_id)->whereBetween('created_at', [Carbon::now()->subSeconds(30)->format('Y-m-d H:i:s'), Carbon::now()->addSeconds(30)->format('Y-m-d H:i:s')])->count();
        if($previousrecharge > 0){
            //return response()->json(["status" => "ERR", 'message' => "Duplicate Transaction Not Allowed"]);
        }
        // if($post->amount>=100 && $post->amount<=24000)
        // {
        //      $api = Api::where('code', 'haodapaypayout')->first();
        //      $bankpayoutapi = 'haodapay';
        // }
        for ($randomNumber = mt_rand(1, 9), $i = 1; $i < 10; $i++) {
                $randomNumber .= mt_rand(0, 9);
            }
        $post['mobile'] = !empty($post->mobile)?$post->mobile:$randomNumber;
        $post['api_id'] = $api->id;
        $insert = [
            'number'    => $post->account??$post->upiid,
            'mobile'    => $post->mobile,
            'provider_id' => $provider->id,
            'api_id'    => $post->api_id,
            'amount'    => $post->amount,
            'charge'    => $post->charge,
            'gst'       => $post->gst,
            'txnid'     => $post->txnid,
            'remark'    => $post->remark,
            'option1'   => $post->name,
            'option2'   => $post->bank??'',
            'option3'   => $post->ifsc??'',
            'option4'   => $post->callback,
            'status'    => 'pending',
            'user_id'   => $user->id,
            'credit_by' => $user->id,
            'rtype'     => 'main',
            'via'       => 'api',
            'balance'   => $user->mainwallet,
            'trans_type'=> 'debit',
            'product'   => "payout",
            'apitxnid'      => $post->apitxnid,
            'create_time'   => Carbon::now()->toDateTimeString()
        ];
        
        $transaction = User::where('id', $user->id)->decrement('mainwallet', $post->amount + $post->charge + $post->gst);

        if(!$transaction){
            return response()->json([
                'status'  => 'ERR',
                'message' => 'Transaction Failed, Try Again'
            ]);
        }

        try {
            $report = Report::create($insert);
        } catch (\Exception $e) {
            User::where('id', $user->id)->increment('mainwallet', $post->amount + $post->charge + $post->gst);
            return response()->json([
                'status'  => 'ERR',
                'message' => 'Transaction Failed, Duplicate Transaction Found'
            ]);
        }

        if($bankpayoutapi == "razorpay"){
            $url = $api->url;
            $acc_no_razorpay=$api->password;
            
            //dd($post->mode);
            $post['pmode'] = strtoupper($post->paymode)=="UPI"?"UPI":"IMPS";
            //dd($post->pmode);
            $parameter = [
                "account_number" => "$acc_no_razorpay",
                "amount"     => $post->amount*100,
                "currency"   => 'INR', 
                "mode"       => $post->pmode, //'IMPS',
                "purpose"    => "payout",
                "fund_account" =>[
                    "account_type" =>($post->pmode == "UPI")?'vpa':"bank_account",
                    "contact"=>[
                        "name"    => $user->name,
                        "email"   => $user->email,
                        "contact" => $user->mobile,
                        "type"    => "vendor",
                        "reference_id" => "WEBT".$user->id,
                        "notes" => [
                            "notes_key_1" => "",
                            "notes_key_2" => ""
                        ]
                    ]
                ],
                "queue_if_low_balance" => true,
                "reference_id" => $post->txnid,
                "narration"    => "payout",
                "notes" => [
                    "notes_key_1"=>"",
                    "notes_key_2"=> ""
                ]
            ];
            if ($post->pmode != "UPI") {
                $parameter['fund_account']['bank_account'] = [
                    "name" => $user->name,
                    "ifsc" => $post->ifsc,
                    "account_number" => $post->account
                ];
            }else{
                $parameter['fund_account']['vpa'] = [
                    "address" => $post->upiid
                ];
            }
            //dd(json_encode($parameter));
            $header = array("Content-Type: application/json", "Authorization: Basic ".base64_encode($api->username.":".$api->optional1));

            if(env('APP_ENV') != "local"){
                $result = \Myhelper::curl($url, 'POST', json_encode($parameter), $header, 'yes', '\App\Model\Report', $post->txnid);
            }else{
                $result = [
                    'error'    => true,
                    'response' => ''
                ];
            }

            if($result['error'] || $result['response'] == ''){
                if(isset($data->error->description)){
                    $rrn = $data->error->description;
                }else{
                    if(isset($data->failure_reason)){
                        $rrn = $data->failure_reason;
                    }else{
                        $rrn = "Transaction Under Process";
                    }
                }
                Report::where('id', $report->id)->update(['refno' => $rrn]);
                return response()->json([
                    'status'    => 'TUP', 
                    'message'   => $rrn,
                    'rrn'       => $report->id
                ]);
            }
    
            $data = json_decode($result['response']);
    
            if(!isset($data->status)){
                return response()->json([
                    'status'    => 'TUP', 
                    'message'   => 'Transaction Under Process',
                    'rrn'       => $report->id
                ]);
            }
            
            if((isset($data->status) && in_array($data->status, ['rejected', 'cancelled', 'reversed']))){
                User::where('id', $user->id)->increment('mainwallet', $post->amount + $post->charge+ $post->gst);
                if(isset($data->error->description)){
                    $rrn = $data->error->description;
                }else{
                    if(isset($data->failure_reason)){
                        $rrn = $data->failure_reason;
                    }else{
                        $rrn = "Failed";
                    }
                }
                //$rrn = isset($data->error->description) ? $data->error->description : isset($data->failure_reason) ? $data->failure_reason : "Failed";
                Report::where('id', $report->id)->update([
                    'status' => 'failed',
                    'refno'  => $rrn
                ]);
                
                return response()->json([
                    'status' => 'TXF',
                    'message' => $rrn,
                    'rrn'     => $report->id,
                ]);
            }else{
                Report::where('id', $report->id)->update([
                    'status' => 'success',
                    'refno'  => isset($data->utr)?$data->utr:$data->id,
                ]);

                return response()->json([
                    'status' => 'TUP',
                    'message'=> 'Transaction Processing',
                    'rrn'    => isset($data->utr)?$data->utr:$data->id,
                ]);
            }
        }else if ($bankpayoutapi == "safex") {
            $parameter = [
                 
                  "header"=> [ 
                    "operatingSystem"=> "WEB", 
                    "sessionId"=> "AGEN3580011616", 
                    "version"=> "1.0.0" 
                  ], 
                  "userInfo"=> [], 
                  "transaction"=> [ 
                    "requestType"=> "WTW", 
                    "requestSubType"=> "PWTB", 
                    "tranCode"=> 0, 
                    "txnAmt"=> 0.0, 
                    "id"=> "AGEN3580011616", 
                    "surChargeAmount"=> 0.0, 
                    "txnCode"=> 0, 
                    "userType"=> 0 
                  ], 
                  "payOutBean"=> [ 
                    "mobileNo"=> $post->mobile, 
                    "txnAmount"=> number_format((float)$post->amount, 2, '.', ''), 
                    "accountNo"=> $post->account, 
                    "ifscCode"=> $post->ifsc, 
                    "bankName"=> $post->bank, 
                    "accountHolderName"=> $post->name, 
                    "txnType"=> "IMPS", 
                    "accountType"=> "Saving", 
                    "emailId"=> "test@gmail.com", 
                    "orderRefNo"=> $post->txnid, 
                    "count"=> 0 
                  ] 
                 
                ];
                
                $encReq = \Myhelper::safxencrypt(json_encode($parameter),'8MgOLNIVVWVUYVAQ+umAQBTKPb0YNsU0optbyBrAtSQ=');
                $payload = [
                    'payload' => $encReq,
                    'uId' => "AGEN3580011616"
                    ];
                $header = array("Content-Type: application/json");
                $url = "https://remittance.safexpay.com/agWalletAPI/v2/agg";
                if (env('APP_ENV') != "local") {
                    $result = \Myhelper::curl($url, 'POST', json_encode($payload), $header, 'yes', '\App\Model\Report', $post->txnid);
                } else {
                    $result = [
                        'error'    => true,
                        'response' => ''
                    ];
                }
                if(!empty($result['response']))
                {
                    $request = json_decode($result['response']);
                   
                    $decReq = \Myhelper::safexdecrypt($request->payload,'8MgOLNIVVWVUYVAQ+umAQBTKPb0YNsU0optbyBrAtSQ=');
                    
                    //dd(preg_replace('/[\x00-\x1F\x7F]/', '', $decReq));
                    $response = json_decode(preg_replace('/[\x00-\x1F\x7F]/', '', $decReq));
                    \DB::table('rp_log')->insert([
                        'ServiceName' => 'Safexpayout',
                        'header' => json_encode($header),
                        'body' => json_encode($payload),
                        'response' => $decReq,
                        'encbody' => $request->payload,
                        'url' => $url
                    ]);
                    //dd($response);
                    if(!empty($response->payOutBean))
                    {
                        if ((isset($response->payOutBean->bankStatus) && in_array($response->payOutBean->bankStatus, ['FAILED','REJECTED','Failed','server_busy','ERR','rejected', 'cancelled', 'reversed']))) {
                            User::where('id', $user->id)->increment('mainwallet', $post->amount + $post->charge + $post->gst);
                            if (isset($response->payOutBean->statusDesc)) {
                                $rrn = $response->payOutBean->statusDesc;
                            } else {
                                if (isset($response->payOutBean->statusDesc)) {
                                    $rrn = $response->payOutBean->statusDesc;
                                } else {
                                    $rrn = "Failed";
                                }
                            }
                            //$rrn = isset($data->error->description) ? $data->error->description : isset($data->failure_reason) ? $data->failure_reason : "Failed";
                            Report::where('id', $report->id)->update([
                                'status' => 'failed',
                                'refno'  => $rrn,
                                'payid' => !empty($response->payOutBean->payoutId)?$response->payOutBean->payoutId:''
                            ]);
                            $payout = Report::where(['id'=>$report->id])->first();
                            if($user->role->slug == "apiuser" && $user->callbackurl != null){
                                \Myhelper::callback($payout, 'payout');
                            }
                            return response()->json([
                                'status' => 'TXF',
                                'message' => $rrn,
                                'rrn'     => $report->id,
                            ]);
                        }else {
                            Report::where('id', $report->id)->update([
                                //'status' => 'success',
                                'refno'  => isset($data->utr) ? $data->utr : '',
                                'payid' => !empty($response->payOutBean->payoutId)?$response->payOutBean->payoutId:''
                            ]);
            
                            return response()->json([
                                'status' => 'TUP',
                                'message' => 'Transaction Processing',
                                'rrn'    => isset($data->utr) ? $data->utr : '',
                            ]);
                        }
                    }else
                    {
                        return response()->json([
                            'status'    => 'TUP',
                            'message'   => !empty($response->payOutBean->statusDesc)?$response->payOutBean->statusDesc:'',
                            'rrn'       => $report->id
                        ]);
                    }
                }
                
        }else if($bankpayoutapi == 'cbpayout'){
            $parameter = [
                                "cbPayoutBean"=> [
                                "mobileNo"=> "8888888888",
                                "txnAmount"=> "11",
                                "accountNo"=> "45678901234567",
                                "ifscCode"=> "YESB0000546",
                                "bankId"=> "24",
                                "beneBankName"=> "AXIS",
                                "accountHolderName"=> "Test1",
                                "txnType"=> "IMPS",
                                "orderRefNo"=> $post->txnid,
                                "emailId"=> "test@gmail.com",
                                "debitAccountno"=> "1234567890",
                                "isCBtxn"=>"true"
                            ]
                            ];
            $encReq = \Myhelper::safxencrypt(json_encode($parameter),$api->optional1);
            $payload = [
                    'payload' => $encReq,
                    'uId' => $api->username
                    ];
                $header = array("Content-Type: application/json");
                $url = $api->url."/payment";
                if (env('APP_ENV') != "local") {
                    $result = \Myhelper::curl($url, 'POST', json_encode($payload), $header, 'yes', 'CBpayout', $post->txnid);
                } else {
                    $result = [
                        'error'    => true,
                        'response' => ''
                    ];
                }
            if(!empty($result['response']))
                {
                    $request = json_decode($result['response']);
                   
                    $decReq = \Myhelper::safexdecrypt($request->payload,$api->optional1);
                    
                    //dd(preg_replace('/[\x00-\x1F\x7F]/', '', $decReq));
                    $response = json_decode(preg_replace('/[\x00-\x1F\x7F]/', '', $decReq));
                    \DB::table('rp_log')->insert([
                        'ServiceName' => 'CBpayout',
                        'header' => json_encode($header),
                        'body' => json_encode($payload),
                        'response' => $decReq,
                        'encbody' => $request->payload,
                        'url' => $url,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                    //dd($response);
                    if(!empty($response->cbPayoutBean))
                    {
                        if ((isset($response->cbPayoutBean->bankStatus) && in_array($response->cbPayoutBean->bankStatus, ['FAILED','REJECTED','Failed','server_busy','ERR','rejected', 'cancelled', 'reversed']))) {
                            User::where('id', $user->id)->increment('mainwallet', $post->amount + $post->charge + $post->gst);
                            if (isset($response->cbPayoutBean->statusDesc)) {
                                $rrn = $response->cbPayoutBean->statusDesc;
                            } else {
                                if (isset($response->cbPayoutBean->statusDesc)) {
                                    $rrn = $response->cbPayoutBean->statusDesc;
                                } else {
                                    $rrn = "Failed";
                                }
                            }
                            //$rrn = isset($data->error->description) ? $data->error->description : isset($data->failure_reason) ? $data->failure_reason : "Failed";
                            Report::where('id', $report->id)->update([
                                'status' => 'failed',
                                'refno'  => $rrn,
                                'payid' => !empty($response->cbPayoutBean->payoutId)?$response->cbPayoutBean->payoutId:''
                            ]);
                            $payout = Report::where(['id'=>$report->id])->first();
                            if($user->role->slug == "apiuser" && $user->callbackurl != null){
                                \Myhelper::callback($payout, 'payout');
                            }
                            return response()->json([
                                'status' => 'TXF',
                                'message' => $rrn,
                                'rrn'     => $report->id,
                            ]);
                        }else {
                            Report::where('id', $report->id)->update([
                                //'status' => 'success',
                                'refno'  => isset($data->utr) ? $data->utr : '',
                                'payid' => !empty($response->cbPayoutBean->payoutId)?$response->cbPayoutBean->payoutId:''
                            ]);
            
                            return response()->json([
                                'status' => 'TUP',
                                'message' => 'Transaction Processing',
                                'rrn'    => isset($data->utr) ? $data->utr : '',
                            ]);
                        }
                    }else
                    {
                        return response()->json([
                            'status'    => 'TUP',
                            'message'   => !empty($response->cbPayoutBean->statusDesc)?$response->cbPayoutBean->statusDesc:'',
                            'rrn'       => $report->id
                        ]);
                    }
                }
        }else if ($bankpayoutapi == "haodapay") {
            $url = 'https://kepler.haodapayments.com/api/v1/payout2/initiate';
            $parameter =  [

                "beneficiary_name" => $post->name,
                "amount" => $post->amount,
                "narration" => "bank transaction",
                "reference" => $post->txnid
            ];
            if ($post->pmode != "UPI") {
                $parameter['account_number'] = $post->account;
                $parameter['account_ifsc'] = $post->ifsc;
                $parameter['bankname'] = $post->bank;
                $parameter['confirm_acc_number'] =  $post->account;
                $parameter['requesttype'] = "IMPS";
            } else {
                $parameter['vpa'] = $post->upiid;
                $url = 'https://kepler.haodapayments.com/api/v1/payout2/initiate';
            }

            $header = array("Content-Type: application/json", "x-client-id: RgqJcmEue66933", "x-client-secret: gPsTch25N7RX2409111105276933");
            if (env('APP_ENV') != "local") {
                $result = \Myhelper::curl($url, 'POST', json_encode($parameter), $header, 'yes', '\App\Model\Report', $post->txnid);
            } else {
                $result = [
                    'error'    => true,
                    'response' => ''
                ];
            }
            $response = json_decode($result['response']);
            if(isset($response->statusCode) && $response->statusCode== 1){
                if($response->status== 1){
                    Report::where('id', $report->id)->update([
                    'status' => 'success',
                   "refno" => isset($response->utr) ? $response->utr : "Failed"
                ]);
                 return response()->json([
                    'status' => 'TXN',
                    'message' => 'Transaction Processing',
                    'rrn'    => isset($response->utr) ? $response->utr : "Failed"
                ]);
            }
        elseif($response->status== 2){
             Report::where('id', $report->id)->update([
                    'status' => 'pending',
                   "refno" => isset($response->utr) ? $response->utr : "Failed"
                ]);
                return response()->json([
                    'status' => 'TUP',
                    'message' => 'Transaction Processing',
                    'rrn'    => isset($response->utr) ? $response->utr : "Failed"
                ]);
                                   
            }
            else{
                User::where('id', $user->id)->increment('mainwallet', $post->amount + $post->charge + $post->gst);
                Report::where('id', $report->id)->update([
                    'status' => 'failed',
                    'refno'  => isset($response->message) ? $response->message : "Failed"
                ]);

                return response()->json([
                    'status' => 'TXF',
                    'message' => isset($response->message) ? $response->message : "Failed",
                    'rrn'     => isset($response->message) ? $response->message : "Failed"
                ]);
                                   
                                    
            }
                                
                                
        }else{
            if(isset($response->message) && $response->message== "Low balance"){
                User::where('id', $user->id)->increment('mainwallet', $post->amount + $post->charge + $post->gst);
                Report::where('id', $report->id)->update([
                    'status' => 'failed',
                    'refno'  => isset($response->message) ? $response->message : "Failed"
                ]);

                return response()->json([
                    'status' => 'TXF',
                    'message' => isset($response->message) ? $response->message : "Failed",
                    'rrn'     => isset($response->message) ? $response->message : "Failed"
                ]);
                                  
                                    
                                }
            elseif($response->status == 400){
                
                User::where('id', $user->id)->increment('mainwallet', $post->amount + $post->charge + $post->gst);
                Report::where('id', $report->id)->update([
                    'status' => 'failed',
                    'refno'  => isset($response->title) ? $response->title : "Failed"
                ]);

                return response()->json([
                    'status' => 'TXF',
                    'message' => "Invalid IFSC Code or Account No",
                    'rrn'     => isset($response->title) ? $response->title : "Failed"
                ]);
            }                    
                                
                            }
            

            
        }else if($bankpayoutapi == 'xettle'){
            $url = "https://dashboard.xettle.net/v1/service/payout/ordersInitiate";
            $param = [
                        "customer_name"=> $post->name,
                        "accountNo"=> $post->account,
                        "ifsc" => $post->ifsc,
                        "bank" => $post->bank,
                        "amount" => $post->amount,
                        "purpose"=> "OTHERS",
                        "mode"=> "imps",
                        "narration"=> "narration",
                        "remark"=> "Refund",
                        "clientRefId"=> $post->txnid
                    ];
                $key = 'SAFEE_7afae856a0cbeea64825687442163017';
                $secret = '876bf2757e4e3ca1fc13d521c2be80a24825687442168496';
                $basic = base64_encode($key.':'.$secret);
                $header = array("Content-Type: application/json", "Authorization: Basic ".$basic);
                if (env('APP_ENV') != "local") {
                    $result = \Myhelper::curl($url, 'POST', json_encode($param), $header, 'yes', 'Xettle', $post->txnid);
                } else {
                    $result = [
                        'error'    => true,
                        'response' => ''
                    ];
                }
                if ($result['error'] || $result['response'] == '') {
                    if (isset($data->error->description)) {
                        $rrn = $data->error->description;
                    } else {
                        if (isset($data->failure_reason)) {
                            $rrn = $data->failure_reason;
                        } else {
                            $rrn = "Transaction Under Process";
                        }
                    }
                    Report::where('id', $report->id)->update(['refno' => $rrn]);
                    return response()->json([
                        'status'    => 'TUP',
                        'message'   => $rrn,
                        'rrn'       => $report->id
                    ]);
                }
                $data = json_decode($result['response']);
    
                if (!isset($data->status_code)) {
                    return response()->json([
                        'status'    => 'TUP',
                        'message'   => 'Transaction Under Process',
                        'rrn'       => $report->id
                    ]);
                }
                if ((isset($data->status) && in_array($data->status, ['FAILED','Failed','server_busy','ERR','rejected', 'cancelled', 'reversed']))) {
                    User::where('id', $user->id)->increment('mainwallet', $post->amount + $post->charge + $post->gst);
                    if (isset($data->message)) {
                        $rrn = $data->message;
                    } else {
                        if (isset($data->message)) {
                            $rrn = $data->message;
                        } else {
                            $rrn = "Failed";
                        }
                    }
                    //$rrn = isset($data->error->description) ? $data->error->description : isset($data->failure_reason) ? $data->failure_reason : "Failed";
                    Report::where('id', $report->id)->update([
                        'status' => 'failed',
                        'refno'  => $rrn
                    ]);
    
                    return response()->json([
                        'status' => 'TXF',
                        'message' => $rrn,
                        'rrn'     => $report->id,
                    ]);
                } else {
                    Report::where('id', $report->id)->update([
                        //'status' => 'success',
                        'refno'  => isset($data->utr) ? $data->utr : '',
                        'payid' => !empty($data->payout_id)?$data->payout_id:''
                    ]);
    
                    return response()->json([
                        'status' => 'TUP',
                        'message' => 'Transaction Processing',
                        'rrn'    => isset($data->utr) ? $data->utr : '',
                    ]);
                }
        }
        else if ($bankpayoutapi == "paymonkpayout") {
            
            $parameter = [
                            "clientId" => $api->username,
                            "secretKey"     => $api->password,
                            "number"   => $post->mobile, 
                            "amount"       =>(string)$post->amount,
                            "transferMode"    => $post->mode,
                            "accountNo"    => $post->account,
                            "ifscCode"    =>  $post->ifsc,
                            "beneficiaryName"    =>  !empty($post->name)?$post->name:$user->name,
                            "vpa"    => "",
                            "clientOrderId"    => $post->txnid,
                            
                        ];
                         
            $header = array('Content-Type: application/json');
           
            $url = "https://api.paymonk.com/api/api/api-module/payout/payout";
                if (env('APP_ENV') != "local") {
                    $result = \Myhelper::curl($url, 'POST', json_encode($parameter), $header, 'yes', '\App\Model\Report', $post->txnid);
                } else {
                    $result = [
                        'error'    => true,
                        'response' => ''
                    ];
                }
                if(!empty($result['response']))
                {
                    $response = json_decode($result['response']);
                   
                    \DB::table('rp_log')->insert([
                        'ServiceName' => 'Paymonk',
                        'header' => json_encode($header),
                        'body' => json_encode($parameter),
                        'response' => $result['response'],
                        'encbody' => '',
                        'url' => $url
                    ]);
                   
                    $response = json_decode($result['response']);
            if(isset($response->statusCode) && $response->statusCode== 1){
                if($response->status== 1){
                    Report::where('id', $report->id)->update([
                    'status' => 'success',
                   "refno" => isset($response->utr) ? $response->utr : "Failed"
                ]);
                 return response()->json([
                    'status' => 'TXN',
                    'message' => 'Transaction Processing',
                    'rrn'    => isset($response->utr) ? $response->utr : "Failed"
                ]);
            }
        elseif($response->status== 2){
             Report::where('id', $report->id)->update([
                    'status' => 'pending',
                   "refno" => isset($response->utr) ? $response->utr : "Failed"
                ]);
                return response()->json([
                    'status' => 'TUP',
                    'message' => 'Transaction Processing',
                    'rrn'    => isset($response->utr) ? $response->utr : "Failed"
                ]);
                                   
            }
            else{
                User::where('id', $user->id)->increment('mainwallet', $post->amount + $post->charge + $post->gst);
                Report::where('id', $report->id)->update([
                    'status' => 'failed',
                    'refno'  => isset($response->message) ? $response->message : "Failed"
                ]);

                return response()->json([
                    'status' => 'TXF',
                    'message' => isset($response->message) ? $response->message : "Failed",
                    'rrn'     => isset($response->message) ? $response->message : "Failed"
                ]);
                                   
                                    
            }
                                
                                
        }else{
            if(isset($response->message) && $response->message== "Low balance"){
                User::where('id', $user->id)->increment('mainwallet', $post->amount + $post->charge + $post->gst);
                Report::where('id', $report->id)->update([
                    'status' => 'failed',
                    'refno'  => isset($response->message) ? $response->message : "Failed"
                ]);

                return response()->json([
                    'status' => 'TXF',
                    'message' => isset($response->message) ? $response->message : "Failed",
                    'rrn'     => isset($response->message) ? $response->message : "Failed"
                ]);
                                  
                                    
                                }
            elseif($response->status == 400){
                
                User::where('id', $user->id)->increment('mainwallet', $post->amount + $post->charge + $post->gst);
                Report::where('id', $report->id)->update([
                    'status' => 'failed',
                    'refno'  => isset($response->title) ? $response->title : "Failed"
                ]);

                return response()->json([
                    'status' => 'TXF',
                    'message' => "Invalid IFSC Code or Account No",
                    'rrn'     => isset($response->title) ? $response->title : "Failed"
                ]);
            }                    
                                
                            }
                
        }
        }else if($bankpayoutapi == 'branchx')
        {
            //$gpsdata = geoip($post->ip);
            
            $gpsdata = $this->getGeoLocation($post->ip());
            //print_r($gpsdata->location->latitude);exit;
            $parameter = [
                    "amount"=> $post->amount, 
                    "mobileNumber"=> $post->mobile, 
                    "requestId"=> $post->txnid, 
                    "accountNumber"=> $post->account, 
                    "ifscCode"=> $post->ifsc, 
                    "beneficiaryName"=> !empty($post->name)?$post->name:$user->name, 
                    "bankName"=> $post->bank, 
                    "transferMode"=> $post->mode, 
                    "latitude"=> $gpsdata->location->latitude, 
                    "longitude"=> $gpsdata->location->longitude, 
                    "emailId"=> "test@gmail.com", 
                    "purpose"=> "Settlement"
                    
                ];
            $header = array('Content-Type: application/json','apiToken:'.$api->username);
            \Log::info('branchX start time:'.date('Y-m-d H:i:s'));
            $url = $api->url.'payout';
            if (env('APP_ENV') != "local") {
                $result = \Myhelper::curl($url, 'POST', json_encode($parameter), $header, 'yes', 'branchx', $post->txnid);
            } else {
                $result = [
                    'error'    => true,
                    'response' => ''
                ];
            }
            \Log::info('branchX end time:'.date('Y-m-d H:i:s'));
            if(!empty($result['response']))
            {
                $response = json_decode($result['response']);
               
                \DB::table('rp_log')->insert([
                    'ServiceName' => 'branchx',
                    'header' => json_encode($header),
                    'body' => json_encode($parameter),
                    'response' => $result['response'],
                    
                    'url' => $url
                ]);
               
                $response = json_decode($result['response']);
            if(isset($response->status) && $response->status== 'SUCCESS'){
                    Report::where('id', $report->id)->update([
                        'status' => 'success',
                       "refno" => isset($response->data->utr) ? $response->data->utr : "Failed"
                    ]);
                     return response()->json([
                        'status' => 'TXN',
                        'message' => 'Transaction Processing',
                        'rrn'    => isset($response->data->utr) ? $response->data->utr : "Failed"
                    ]);
                
            
                                
                                
        }
        else if(isset($response->status) && $response->status== 'FAILED')
        {
            User::where('id', $user->id)->increment('mainwallet', $post->amount + $post->charge + $post->gst);
                Report::where('id', $report->id)->update([
                    'status' => 'failed',
                    'refno'  => isset($response->message) ? $response->message : "Failed"
                ]);

                return response()->json([
                    'status' => 'TXF',
                    'message' => isset($response->message) ? $response->message : "Failed",
                    'rrn'     => isset($response->message) ? $response->message : "Failed"
                ]);
        }else if(isset($response->status) && $response->status== 'PENDING')
        {
            $payout_id = !empty($response->data->apiTxnId)?$response->data->apiTxnId:'';
             Report::where('id', $report->id)->update([
                        
                       "payid" => $payout_id
                    ]);
                     return response()->json([
                        'status' => 'TUP',
                        'message' => 'Transaction Processing',
                        'rrn'    => isset($response->data->utr) ? $response->data->utr : "Pending"
                    ]);
        }else{
                if(isset($response->message) && $response->message== "Low balance"){
                    User::where('id', $user->id)->increment('mainwallet', $post->amount + $post->charge + $post->gst);
                    Report::where('id', $report->id)->update([
                        'status' => 'failed',
                        'refno'  => isset($response->message) ? $response->message : "Failed"
                    ]);
    
                    return response()->json([
                        'status' => 'TXF',
                        'message' => isset($response->message) ? $response->message : "Failed",
                        'rrn'     => isset($response->message) ? $response->message : "Failed"
                    ]);
                                      
                                        
                                    }
                elseif($response->status == 400){
                    
                    User::where('id', $user->id)->increment('mainwallet', $post->amount + $post->charge + $post->gst);
                    Report::where('id', $report->id)->update([
                        'status' => 'failed',
                        'refno'  => isset($response->title) ? $response->title : "Failed"
                    ]);
    
                    return response()->json([
                        'status' => 'TXF',
                        'message' => "Invalid IFSC Code or Account No",
                        'rrn'     => isset($response->title) ? $response->title : "Failed"
                    ]);
                }                    
                                
            }
            }
        }
        else{
            $url = $api->url;
            $parameter = [
                "apitxnid" => $post->apitxnid,
                "amount"   => $post->amount, 
                "account"  => $post->account,
                "name"     => $user->name,
                "bank"     => $post->bank,
                "ifsc"     => $post->ifsc,
                "ip"       => $post->ip(),
                "token"    => $api->username,
                'callback' => url('api/callback/update/recharge/spayout')
            ];
            $header = array("Content-Type: application/json");
    
            if(env('APP_ENV') != "local"){
                $result = \Myhelper::curl($url, 'POST', json_encode($parameter), $header, 'yes', '\App\Model\Report', $post->apitxnid);
            }else{
                $result = [
                    'error'    => true,
                    'response' => ''
                ];
            }
    
            if($result['error'] || $result['response'] == ''){
                return response()->json([
                    'status'    => 'TUP', 
                    'message'   => 'Transaction Under Process',
                    'rrn'       => $report->id
                ]);
            }
    
            $data = json_decode($result['response']);
    
            if(!isset($data->status)){
                return response()->json([
                    'status'    => 'TUP', 
                    'message'   => 'Transaction Under Process',
                    'rrn'       => $report->id
                ]);
            }
    
            switch ($data->status) {
                case 'TXN'  :
                case 'TUP' :
                    Report::where('id', $report->id)->update([
                        'status' => 'success',
                        'refno'  => $data->rrn
                    ]);
    
                    return response()->json([
                        'status' => 'TXN',
                        'message'=> 'Transaction Successfull',
                        'rrn'    => $data->rrn
                    ]);
                    break;
    
                case 'ERR' :
                case 'TXF'  :
                    if($data->message=="Low balance, kindly recharge your wallet"){
                        $data->message = "Service Down For Sometime"; 
                    }
                    User::where('id', $user->id)->increment('mainwallet', $post->amount + $post->charge + $post->gst);
                    Report::where('id', $report->id)->update([
                        'status' => 'failed',
                        'refno'  => $data->message
                    ]);
                    
                    return response()->json([
                        'status'  => 'TXF',
                        'message' => $data->message,
                        'rrn'     => $post->txnid
                    ]);
                    break;
    
                default:
                    return response()->json([
                        'status' => 'TUP',
                        'message'=> 'Transaction Under Process',
                        'rrn'    => $post->txnid
                    ]);
                    break;
            }
        }
    }
    
    
    public function beneficiaryVerification(Request $post)
    {
        $token = Apitoken::where('token', $post->token)->first(['user_id']);
        $post['user_id'] = $token->user_id;
        
        $rules = array(
            'user_id' => 'required|numeric',
            'account' => 'required',
            'ifsc'    => 'required',
            'apitxnid'=> 'required|unique:reports,apitxnid'
        );

        $validator = \Validator::make($post->all(), array_reverse($rules));
        if ($validator->fails()) {
            foreach ($validator->errors()->messages() as $key => $value) {
                $error = $value[0];
            }
            return response()->json(array(
                'status' => 'ERR',
                'message' => $error
            ));
        }

        $user = User::where('id', $post->user_id)->first();
        $post['api_id']  = $this->api->id;

        $provider = Provider::where('recharge1', 'dmt1accverify')->first();
        $post['provider_id'] = $provider->id;
        $post['charge'] = \Myhelper::getCommission($post->amount, $user->scheme_id, $post->provider_id, $user->role->slug);
        if($user->mainwallet < $post->amount + $post->charge){
            return response()->json(["status" => "ERR", 'message' =>"Low balance, kindly recharge your wallet"]);
        }

        do {
            $post['txnid'] = $this->transcode().rand(1111111111, 9999999999);
        } while (Report::where("txnid", "=", $post->txnid)->first() instanceof Report); 

        $payoutapi = Api::where('code', 'idmt')->first();

        if(!$payoutapi || $payoutapi->status == "0") {
            return response()->json(["status" => "ERR", 'message' => "Service Down For Sometime"]);
        }

        $previousrecharge = Report::where('number', $post->account)->where('amount', $post->amount)->where('user_id', $post->user_id)->whereBetween('created_at', [Carbon::now()->subSeconds(30)->format('Y-m-d H:i:s'), Carbon::now()->addSeconds(30)->format('Y-m-d H:i:s')])->count();
        if($previousrecharge > 0){
            return response()->json(["status" => "ERR", 'message' => "Duplicate Transaction Not Allowed"]);
        }

        $post['api_id'] = $payoutapi->id;
        $insert = [
            'number'    => $post->account,
            'mobile'    => $user->mobile,
            'provider_id' => $provider->id,
            'api_id'    => $post->api_id,
            'amount'    => "1",
            'charge'    => $post->charge,
            'txnid'     => $post->txnid,
            'remark'    => $post->remark,
            'option3'   => $post->ifsc,
            'status'    => 'pending',
            'user_id'   => $user->id,
            'credit_by' => $user->id,
            'rtype'     => 'main',
            'via'       => 'api',
            'balance'   => $user->mainwallet,
            'trans_type'=> 'debit',
            'product'   => "dmt",
            'apitxnid'      => $post->apitxnid,
            'create_time'   => Carbon::now()->toDateTimeString()
        ];
        
        $transaction = User::where('id', $user->id)->decrement('mainwallet', $post->amount + $post->charge);

        if(!$transaction){
            return response()->json([
                'status'  => 'ERR',
                'message' => 'Transaction Failed, Try Again'
            ]);
        }

        try {
            $report = Report::create($insert);
        } catch (\Exception $e) {
            User::where('id', $user->id)->increment('mainwallet', $post->amount + $post->charge);
            return response()->json([
                'status'  => 'ERR',
                'message' => 'Transaction Failed, Duplicate Transaction Found'
            ]);
        }

        $gpsdata = geoip("157.39.116.242");
        $parameters = array(
            'token' => $payoutapi->username
        );
        
        $parameters['request']["sp_key"]         = "DPN";
        $parameters['request']["external_ref"]   = $post->txnid;
        $parameters['request']["credit_account"] = $post->account;
        $parameters['request']["ifs_code"]       = strtoupper($post->ifsc);
        $parameters['request']["bene_name"]      = "Test";
        $parameters['request']["credit_amount"]  = "1";
        $parameters['request']["latitude"]       = sprintf('%0.4f', $gpsdata->lat);
        $parameters['request']["longitude"]      = sprintf('%0.4f', $gpsdata->lon);
        $parameters['request']["endpoint_ip"]    = "157.39.116.242";
        $parameters['request']["remarks"]        = "Settle";
        $parameters['request']["alert_mobile"]   = "";
        $parameters['request']["alert_email"]    = "";

        $url = $payoutapi->url."payouts/direct";

        $result = \Myhelper::curl($url, 'POST', json_encode($parameters), array("Accept: application/json","Cache-Control: no-cache","Content-Type: application/json"), "yes", 'App\Model\Report', $post->txnid);

        if($result['error'] || $result['response'] == ''){
            return response()->json([
                'status'    => 'TUP', 
                'message'   => 'Transaction Under Process',
                'rrn'       => $report->id
            ]);
        }

        $data = json_decode($result['response']);

        if(!isset($data->statuscode)){
            return response()->json([
                'status'    => 'TUP', 
                'message'   => 'Transaction Under Process',
                'rrn'       => $report->id
            ]);
        }

        switch ($data->statuscode) {
            case 'TXN':
                Report::where('id', $report->id)->update([
                    'status' => 'success',
                    'refno'  => (isset($data->data->payout->credit_refid)) ? $data->data->payout->credit_refid : "Success",
                    'option1'=> $data->data->payout->name
                ]);

                return response()->json([
                    'status' => 'TXN',
                    'message'=> (isset($data->status))? $data->status : 'Transaction Successfull',
                    'rrn'    => (isset($data->data->payout->credit_refid))? $data->data->payout->credit_refid : $report->id,
                    'name'   => $data->data->payout->name
                ]);
            break;

            case 'TUP':
                return response()->json([
                    'status' => 'TUP',
                    'message'=> (isset($data->status))? $data->status : 'Transaction Successfull',
                    'rrn'    => (isset($data->data->payout->credit_refid))? $data->data->payout->credit_refid : $report->id,
                    'name'    => (isset($data->data->payout->name))? $data->data->payout->name : "Not Found",
                ]);
            break;

            default:
                if(in_array($data->statuscode, ["RPI","UAD","IAC","IAT","AAB","IAB","ISP","DID","DTX","IAN","IRA","DTB","RBT","SPE","SPD","UED","IEC","IRT","ITI","TSU","IPE","ISE","TRP","OUI","ODI","TDE","DLS","RNF","RAR","IVC","IUA","SNA","ERR","FAB","TRP","ERR","UFC","OLR","OTP","EOP","ONV","RAB"])){
                    User::where('id', $user->id)->increment('mainwallet', $post->amount + $post->charge);
                    
                    if($data->statuscode == "IAB"){
                        $remark = "Service Down For Sometime";
                    }else{
                        $remark = $data->status;
                    }
                    
                    Report::where('id', $report->id)->update([
                        'status' => 'failed',
                        'refno'  => $remark
                    ]);
                    
                    return response()->json([
                        'status' => 'TXF',
                        'message' => $remark,
                        'rrn'     => (isset($data->data->payout->credit_refid))? $data->data->payout->credit_refid : $report->id,
                    ]);
                }else{
                    return response()->json([
                        'status' => 'TUP',
                        'message'=> (isset($data->status))? $data->status : 'Transaction Successfull',
                        'rrn'    => (isset($data->data->payout->credit_refid))? $data->data->payout->credit_refid : $report->id,
                    ]);
                }
                break;
        }
    }

    public function banksettlementstatus(Request $post)
    {
        $token = Apitoken::where('token', $post->token)->first(['user_id']);
        $post['user_id'] = $token->user_id;

        $rules = array(
            'user_id' => 'required|numeric',
            'txnid'=> 'required'
        );

        $validator = \Validator::make($post->all(), array_reverse($rules));
        if ($validator->fails()) {
            foreach ($validator->errors()->messages() as $key => $value) {
                $error = $value[0];
            }
            return response()->json(array(
                'statuscode'   => 'BPR',
                'trans_status' => "error",
                'refno'        => $error
            ));
        }

        $user = User::where('id', $post->user_id)->first();
        $report = Report::where('apitxnid', $post->txnid)->first();

        if(!$report){
            $data['statuscode'] = "TNF";
            $data['trans_status'] = "unknown";
            $data['refno'] = "Transaction Not Found";
            return response()->json($data);
        }
        $data['statuscode'] = "TXN";
            $data['trans_status'] = $report->status;
            $data['refno'] = $report->refno;
            return response()->json($data);
        /*if(in_array($report->status , ['pending'])){
            $data['statuscode'] = "TXN";
            $data['trans_status'] = $report->status;
            $data['refno'] = $report->refno;
            return response()->json($data);
        }*/

        switch ($report->api->code) {
            case 'idmt':
                $data['statuscode']   = "TXN";
                $data['trans_status'] = $report->status;
                $data['refno']        = $report->refno;
                return response()->json($data);
                break;

            case 'paytmpayout':
                $url = $report->api->url."/bpay/api/v1/disburse/order/query";

                $parameter = [
                    "orderId" => $report->txnid,
                ];
                $method = "POST";
                $parameter = json_encode($parameter, true);
                $checksum  = \Paytm::getChecksumFromString($parameter, $report->api->password);
                $header    = array("Content-Type: application/json", "x-mid: ".$report->api->username, "x-checksum: ".$checksum);
                break;
                
            case 'paymonkpayout':
                 $url = 'https://api.paymonk.com/api/api/api-module/payout/status-check';
			        $header = array("Content-Type: application/json");
			        
			        $parameter = [
			            'clientId'=>$report->api->username,
			            'secretKey'=>$report->api->password,
			            'clientOrderId'=>$report->txnid
			            
			            ];
			        $method = "POST";
                break;    
            
            default:
                $data['statuscode'] = "ERR";
                $data['trans_status'] = "Invalid Transaction";
                $data['refno'] = "Invalid Transaction";
                return response()->json($data);
                break;
        } 
        
        $result = \Myhelper::curl($url, $method, $parameter, $header);
        //dd($result);
        if($result['response'] != ''){
            switch ($report->api->code) {
                case 'paytmpayout':
                    $doc = json_decode($result['response']);
                    if(strtolower($doc->status) && strtolower($doc->status) == "success"){
                        $update['status'] = "success";
                        $update['refno'] = $doc->result->rrn;
                    }elseif (strtolower($doc->status) && strtolower($doc->status) == "failure") {
                        $update['status'] = "reversed";
                        if($doc->statusMessage == "Account balance is low. Please add funds and try again."){
                            $update['refno'] = "Service Down For Sometime";
                        }else{
                            $update['refno'] = isset($doc->result->rrn) ? $doc->result->rrn : $doc->statusMessage;
                        }
                    }else{
                        $update['status'] = "unknown";
                    }
                    break;
                    
                case 'paymonkpayout':
                   
                     $doc = json_decode($result['response']);
			                if($doc->statusCode == 1 && ($doc->status ==1)){
    							$update['refno'] = $doc->utr;
    							$update['status'] = "success";
    						}elseif($doc->statusCode ==0){
    							$update['status'] = "reversed";
    							$update['refno'] = !empty($doc->utr)?$doc->utr:$doc->message;
    						}else{
    							$update['status'] = "Unknown";
    							$update['refno'] = $doc->message;
    						}
                    break;    
            } 

            if ($update['status'] != "unknown") {
                $reportupdate = Report::where('id', $report->id)->update($update);
                if ($reportupdate && $update['status'] == "reversed" && in_array($report->status , ['success', 'pending'])) {
                    \Myhelper::transactionRefund($report->id, 'recharge');
                }
                $newreport =  Report::where('id', $report->id)->first();

                $data['statuscode'] = "TXN";
                $data['trans_status'] = $newreport->status;
                $data['refno'] = $newreport->refno;
                return response()->json($data);
            }else{
                $data['statuscode']   = "TXN";
                $data['trans_status'] = $report->status;
                $data['refno']        = $report->refno;
                return response()->json($data);
            }
        }else{
            $data['statuscode']   = "TXN";
            $data['trans_status'] = $report->status;
            $data['refno']        = $report->refno;
            return response()->json($data);
        }
    }
    
    public function bankVerification(Request $post)
    {
        $rules = array(
            
            'token' => 'required',
            'apitxnid' => 'required|unique:reports,apitxnid',
            'account' => 'required',
            'ifsc'    => 'required',
            'bankname'    => 'required',
            'mobile'    => 'required',
            
        );

        $validator = \Validator::make($post->all(), array_reverse($rules));
        if ($validator->fails()) {
            foreach ($validator->errors()->messages() as $key => $value) {
                $error = $value[0];
            }
            return response()->json(array(
                'status' => 'ERR',
                'message' => $error
            ));
        }
        $token = Apitoken::where('token', $post->token)->first(['user_id']);
        $post['user_id'] = $token->user_id;
        //dd($post->user_id);
        $userdata=User::where('id',$post->user_id)->first();
        
        $provider = Provider::where('recharge1', 'bankverifycharge')->first();
        //dd($userdata->scheme_id, $provider->id, $userdata->role->slug);
        $post['charge'] = \Myhelper::getCommission(0, $userdata->scheme_id, $provider->id, $userdata->role->slug);
       //dd($userdata->scheme_id, $provider->id, $userdata->role->slug,$post->charge,$post->user_id);
        $post['provider_id'] = $provider->id;
        if($userdata->mainwallet - $this->mainlocked() <  $post->charge){
            return response()->json(["statuscode" => "IWB", 'status'=>'Low balance, kindly recharge your wallet.'], 400);
        }
         do {
            $post['txnid'] = $this->transcode().rand(1111111111, 9999999999);
        } while (Report::where("txnid", "=", $post->txnid)->first() instanceof Report); 
        
       // $requestid = rand(1111,9999);
        $insert = [
            'number'    => $post->account,
            'mobile'    => $post->mobile,
            'provider_id' => $provider->id,
            'api_id'    => $this->branchx_bankverifyapi->id,
            'amount'    => "0",
            'charge'    => $post->charge,
            'txnid'     => $post->txnid,
            'option3'   => $post->ifsc,
            'status'    => 'pending',
            'user_id'   => $userdata->id,
            'credit_by' => $userdata->id,
            'rtype'     => 'main',
            'via'       => 'api',
            'balance'   => $userdata->mainwallet,
            'trans_type'=> 'debit',
            'product'   => "bankverifycharge",
            'apitxnid'      => $post->apitxnid,
            'create_time'   => Carbon::now()->toDateTimeString()
        ];
        User::where('id', $post->user_id)->decrement('mainwallet', $post->charge);
        $report = Report::create($insert);
        $url = $this->branchx_bankverifyapi->url;
        $apitoken = $this->branchx_bankverifyapi->username;
        $header = array(
            'apiToken:'.$apitoken,
            'Content-Type: application/json'
            );
        $param = [
            "mobileNumber"=> $post->mobile,
            "requestId"=> $post->txnid,
            "accountNumber"=> $post->account,
            "ifscCode"=> $post->ifsc,
            "bankName"=> $post->bankname
        ];
        $result = \Myhelper::curl($url, 'POST', json_encode($param), $header,"yes",$post->txnid);        
        //dd($result,$url,json_encode($param),$header);
        $response = json_decode($result['response']);
        if($response->status == 'SUCCESS'){
           
            Report::where('txnid',$report->txnid)->update(['status'=>'success','payid'=>$response->utr,'refno'=>$response->api_ref,'option1'=>$response->name,'remark'=>$response->message]);
            return response()->json(['status'=>'success','apitxnid'=>$post->apitxnid,'message'=>$response->message,'utr'=>$response->utr,'name'=>$response->name]);
            
        }
        if($response->status == 'PENDING'){
           $utr=$response->utr?? 'null';
           $reno=$response->api_ref ?? 'null';
           $name=$response->name ?? 'null';
            Report::where('txnid',$report->txnid)->update(['status'=>'pending','payid'=>$utr,'refno'=>$reno,'option1'=>$name,'remark'=>$response->message]);
            return response()->json(['status'=>'pending','apitxnid'=>$post->apitxnid,'message'=>$response->message,'utr'=>$utr,'name'=>$name]);
            
        }
        else{
             User::where('id', $report->user_id)->increment('mainwallet', $report->charge);
             Report::where('txnid',$report->txnid)->update(['status'=>'failed','payid'=>$response->message,'remark'=>$response->message]);
            return response()->json(['status'=>'failed','message'=>$response->message,'apitxnid'=>$post->apitxnid]);
        }
        
    }
    
    public function upiValidate(Request $post)
    {
        $rules = array(
            
            'token' => 'required',
            'apitxnid' => 'required|unique:reports,apitxnid',
            'vpa' => 'required',
            
            
        );

        $validator = \Validator::make($post->all(), array_reverse($rules));
        if ($validator->fails()) {
            foreach ($validator->errors()->messages() as $key => $value) {
                $error = $value[0];
            }
            return response()->json(array(
                'status' => 'ERR',
                'message' => $error
            ));
        }
        $token = Apitoken::where('token', $post->token)->first(['user_id']);
        $post['user_id'] = $token->user_id;
        //dd($post->user_id);
        $userdata=User::where('id',$post->user_id)->first();
        
        $provider = Provider::where('recharge1', 'vpaverifycharge')->first();
        
        $post['charge'] = \Myhelper::getCommission(0, $userdata->scheme_id, $provider->id, $userdata->role->slug);
       
        $post['provider_id'] = $provider->id;
        if($userdata->mainwallet - $this->mainlocked() <  $post->charge){
            return response()->json(["statuscode" => "IWB", 'status'=>'Low balance, kindly recharge your wallet.'], 400);
        }
         do {
            $post['txnid'] = $this->transcode().rand(1111111111, 9999999999);
        } while (Report::where("txnid", "=", $post->txnid)->first() instanceof Report); 
        $api = Api::where('code','accverify')->first();
       // $requestid = rand(1111,9999);
        $insert = [
            'number'    => $post->vpa,
            'mobile'    => $userdata->mobile,
            'provider_id' => $provider->id,
            'api_id'    => $api->id,
            'amount'    => "0",
            'charge'    => $post->charge,
            'txnid'     => $post->txnid,
            'option3'   => $post->vpa,
            'status'    => 'pending',
            'user_id'   => $userdata->id,
            'credit_by' => $userdata->id,
            'rtype'     => 'main',
            'via'       => 'api',
            'balance'   => $userdata->mainwallet,
            'trans_type'=> 'debit',
            'product'   => "vpaverifycharge",
            'apitxnid'      => $post->apitxnid,
            'create_time'   => Carbon::now()->toDateTimeString()
        ];
        User::where('id', $post->user_id)->decrement('mainwallet', $post->charge);
        $report = Report::create($insert);
        
        $api = Api::where('code','accverify')->first();
        $acc_no_razorpay=$api->password;
        $header = array("Content-Type: application/json", "Authorization: Basic ".base64_encode($api->username.":".$api->optional1));
        $url = "https://api.razorpay.com/v1/payments/validate/vpa";
        $param['vpa'] = $post->vpa;
        $result = \Myhelper::curl($url, 'POST', json_encode($param), $header,"yes",$post->txnid);        
        //dd($result,$url,json_encode($param),$header);
        $response = json_decode($result['response']);
        if(!empty($response->success) && $response->success){
           
            Report::where('txnid',$report->txnid)->update(['status'=>'success','option1'=>$response->customer_name]);
            return response()->json(['status'=>'success','apitxnid'=>$post->apitxnid,'message'=>'Valid VPA','name'=>$response->customer_name]);
            
        }
        
        else{
             User::where('id', $report->user_id)->increment('mainwallet', $report->charge);
             Report::where('txnid',$report->txnid)->update(['status'=>'failed']);
            return response()->json(['status'=>'failed','message'=>!empty($response->message)?$response->message:'','apitxnid'=>$post->apitxnid]);
        }
    }
}
