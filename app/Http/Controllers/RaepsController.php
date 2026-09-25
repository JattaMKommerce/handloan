<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Api;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Aepsreport;
use App\Models\Provider;
use App\Models\Aepsaccount;
use App\Models\Report;
use App\Models\Aepsuser;
use App\Models\Circle;
use Illuminate\Validation\Rule;

use MiladRahimi\Jwt\Generator;
use MiladRahimi\Jwt\Parser;
use MiladRahimi\Jwt\Cryptography\Keys\HmacKey;
use MiladRahimi\Jwt\Cryptography\Algorithms\Hmac\HS256;
use Stevebauman\Location\Facades\Location;

class RaepsController extends Controller
{
    protected $api;
    public function __construct()
    {
        $this->api = Api::where('code', 'raeps')->first();
    }

    public function index()
    {
        if (\Myhelper::hasRole('admin') || !\Myhelper::can('aeps_service')) {
            abort(403);
        }

        $isdone2fa1 = \DB::table('twostepauths')->where(['user_id'=>\Auth::id(),'date'=>strtotime(date('Y-m-d')),'api'=>'paysprint1'])->first();
        
        if($isdone2fa1){
            $data['isdoneaepsauth1'] = 'true';
        }else{
            $data['isdoneaepsauth1'] = 'true';
        }
        
        $isdone2fa2 = \DB::table('twostepauths')->where(['user_id'=>\Auth::id(),'date'=>strtotime(date('Y-m-d')),'api'=>'paysprint2'])->first();
        
        if($isdone2fa2){
            $data['isdoneaepsauth2'] = 'true';
        }else{
            $data['isdoneaepsauth2'] = 'true';
        }       
      
      
        $isdone2fa3 = \DB::table('twostepauths')->where(['user_id'=>\Auth::id(),'date'=>strtotime(date('Y-m-d')),'api'=>'paysprint3'])->first();
        
        if($isdone2fa3){
            $data['isdoneaepsauth3'] = 'true';
        }else{
            $data['isdoneaepsauth3'] = 'true';
        }
        
        $isdone2fa5 = \DB::table('twostepauths')->where(['user_id'=>\Auth::id(),'date'=>strtotime(date('Y-m-d')),'api'=>'paysprint5'])->first();
        
        if($isdone2fa5){
            $data['isdoneaepsauth5'] = 'true';
        }else{
            $data['isdoneaepsauth5'] = 'false';
        }
        
       
        
        $data['stateData'] =  \DB::table('mahastates')->get();
        $data['agent'] = Aepsuser::where('user_id', \Auth::id())->first();
        $data['user'] = \Auth::user();
        $data['mahastate'] = Circle::all();
        $data['bankName'] = \DB::table('fingaepsbanks')->get();
        
        return view('service.raeps')->with($data);
    }
    
    public function getbank(Request $post){
        $url = "https://api.paysprint.in/api/v1/service/aeps/banklist/index";
        $parameter[] = "";
        $ip = $post->ip();
        $gpsdata = Location::get($ip);
       
        
        $token = $this->getToken(\Auth::id().Carbon::now()->timestamp);

         $key = $this->api->optional2;
         $iv  =  $this->api->optional3;
        $cipher   = openssl_encrypt(json_encode($parameter,true), 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
        $request  = base64_encode($cipher);
        $request  = array('body'=>$request);
        
         $header = array(
            "Cache-Control: no-cache",
            "Content-Type: application/x-www-form-urlencoded",
            "Token: ".$token['token']
            // "Authorisedkey: ".$this->api->optional1 
        );

        if(env('APP_ENV') == "local"){
            $result = array(
                "error" => false,
                "response" => json_encode([
                    "status" => true,
                    "message" => "Request Completed",
                    "ackno" => "local",
                    "amount" => $post->amount,
                    "balanceamount" => 0,
                    "bankrrn" => "local",
                    "response" => 200 
                ])
            );
        }else{
            $result = \Myhelper::curl($url, "POST", http_build_query($request), $header, "no");
        }
       // dd($result);
        \DB::table('rp_log')->insert([
            'ServiceName' => "Bank List",
            'header' => json_encode($header),
            'body' => json_encode([$parameter, $request]),
            'response' => $result['response'],
            'url' => $url,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function trasaction(Request $post)
    {
        if (\Myhelper::hasRole('admin') || !\Myhelper::can('aeps_service')) {
            return response()->json(["statuscode" => "ERR", 'status' => "Permission not allowed"]);
        }

        if(!$this->api || $this->api->status == 0){
            return response()->json(["statuscode" => "ERR", 'status' => "R-Aeps Service Currently Down."]);
        }

        $post['user_id'] = \Auth::id();
        $user = User::where('id', $post->user_id)->first();
        $agent = Aepsuser::where('user_id', \Auth::id())->first();

        $validate = $this->myvalidate($post);
        if($validate['status'] != 'NV'){
            if($validate['status'] == 'yes'){
                return response()->json($validate, 422);
            }else{
                return response()->json($validate, 400);
            }
        }
        $ip = $post->ip();
        $gpsdata = Location::get($ip);
        $token = $this->getToken(\Auth::id().Carbon::now()->timestamp);

        switch ($post->transactionType) {
            case "getbanks":
                $url = $this->api->url."aeps/banklist/index";
                $parameter[] = "";

                $key = $this->api->optional2;
                $iv  = $this->api->optional3;
                $cipher   = openssl_encrypt(json_encode($parameter,true), 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
                $request  = base64_encode($cipher);
                $request  = array('body'=>$request);
                break;

            case "getaepsfundbanks":
                $banks = \DB::table('aepssettlebanks')->get();
                return response()->json(['statuscode' => 'TXN', 'message' => 'Bank List Fetched Successfully', 'data' => $banks]);
                break;

            case "BE":
            case "MS":
                $post['transactionAmount'] = 0;
                $bank = \DB::table('fingaepsbanks')->where('iinno', $post->bankid)->first();
                do {
                    $post['txnid'] = \Myhelper::generateAepsUniqueToken();
                } while (Aepsreport::where("txnid", "=", $post->txnid)->first() instanceof Aepsreport);
                
                  if($post->transactionType == "MS"){
                    $provider = Provider::where('recharge1', 'aepsmini')->first(); 
                      $post['provider_id'] = $provider->id ?? 0;
                     $post['charge'] = \Myhelper::getCommission($post->transactionAmount, $user->scheme_id, $post->provider_id, $user->role->slug);
                  }
                   
                 $insert = [
                        "mobile" => $post->mobileNumber,
                        "aadhar" => "XXXXXXXX".substr($post->adhaarNumber, -4),
                        "txnid"  => $post->txnid,
                        "amount" => 0,
                        "charge" => $post->charge ?? 0,
                        "gst"    => 0,
                        "tds"     => 0,
                        "bank"    => $bank->bankName,
                        "user_id" => $user->id,
                        'aepstype'=> $post->transactionType,
                        'authcode'=> $post->device,
                        'status'  => 'pending',
                        'credited_by' => $user->id,
                        'type' => 'credit',
                        'balance' => $user->aepsbalance,
                        'provider_id' => $post->provider_id ?? 0,
                        'api_id' => $this->api->id,
                        'product' => 'aeps'
                    ];
                
                 try {
                        $report = Aepsreport::create($insert);
                    } catch (\Exception $e) {
                        return response()->json(['status' => "ERR", "message" => "Technical Issue, Try Again"]);
                    }
                    
               if($post->transactionType == "BE"){
                    
                    // $url = "https://api.paysprint.in/api/v1/service/aeps/v2/balanceenquiry/index";
                    $url = "https://sit.paysprint.in/service-api/api/v1/service/aeps/balanceenquiry/index";
                    
                } else {
                    $url="https://sit.paysprint.in/service-api/api/v1/service/aeps/ministatement/index";
                    // $url = "https://api.paysprint.in/api/v1/service/aeps/v2/ministatement/index";
                }
               
                $parameter['timestamp'] =  time();
                $parameter['pannumber'] = $agent->userPan;
                $parameter['merchant_name'] = $agent->merchantName;
                $parameter['transcationtype'] = $post->transactionType;
                $parameter['longitude'] = number_format($gpsdata->longitude, 4);
                $parameter['latitude'] = number_format($gpsdata->latitude, 4);
                $parameter['nationalbankidentification'] = $bank->iinno;
                $parameter['requestremarks'] = "Aeps";
                $parameter['mobilenumber'] = $post->mobileNumber;
                $parameter['merchantmobilenumber'] = $agent->merchantPhoneNumber;
                $parameter['adhaarnumber'] = $post->adhaarNumber;
                $parameter['data'] = $post->txtPidData;
                $parameter['referenceno'] = $post->txnid;
                $parameter['accessmodetype'] = "SITE";
                $parameter['ipaddress'] = $post->ip();
                $parameter['pipe'] = $post->pipe;
                $parameter['submerchantid'] = $agent->merchantLoginId;
                $parameter['address'] = $agent->merchantAddress;
                $parameter['pincode'] = $agent->merchantPinCode;
                $parameter['city'] = $agent->merchantCityName;
                $parameter['statecode'] = $agent->merchantState;
                  $key = $this->api->optional2;
                 $iv  = $this->api->optional3;
                $cipher   = openssl_encrypt(json_encode($parameter,true), 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
                $request  = base64_encode($cipher);
                $request  = array('body'=>$request);
                break;

            case "CW":
            case "M":
                $bank = \DB::table('fingaepsbanks')->where('iinno', $post->bankid)->first();
                do {
                    $post['txnid'] = \Myhelper::generateAepsUniqueToken();
                } while (Aepsreport::where("txnid", "=", $post->txnid)->first() instanceof Aepsreport);
        
                if($post->transactionType == "CW" || $post->transactionType == "M"){
                    if($post->transactionType == "CW"){
                        
                        if($post->transactionAmount > 499 && $post->transactionAmount <= 999){
                            $provider = Provider::where('recharge1', 'aeps1')->first();
                        }elseif($post->transactionAmount > 999 && $post->transactionAmount <= 1499){
                            $provider = Provider::where('recharge1', 'aeps2')->first();
                        }elseif($post->transactionAmount > 1499 && $post->transactionAmount <= 1999){
                            $provider = Provider::where('recharge1', 'aeps3')->first();
                        }elseif($post->transactionAmount > 1999 && $post->transactionAmount <= 2499){
                            $provider = Provider::where('recharge1', 'aeps4')->first();
                        }elseif($post->transactionAmount > 2499 && $post->transactionAmount <= 2999){
                            $provider = Provider::where('recharge1', 'aeps5')->first();
                        }elseif($post->transactionAmount > 2999 && $post->transactionAmount <= 5999){
                            $provider = Provider::where('recharge1', 'aeps6')->first();
                        }elseif($post->transactionAmount > 5999 && $post->transactionAmount <= 10000){
                            $provider = Provider::where('recharge1', 'aeps7')->first();
                        }
                        
                        $post['provider_id'] = $provider->id ?? 0;
                        if($post->transactionAmount >= 500){
                            $post['charge'] = \Myhelper::getCommission($post->transactionAmount, $user->scheme_id, $post->provider_id, $user->role->slug);
                        }else{
                            $post['charge'] = 0;
                        }
                        $post['tds'] = 0 ;
                        $post['gst'] = 0 ;
                    }else{
                        if($post->transactionAmount > 0 && $post->transactionAmount <= 10000){
                            $provider = Provider::where('recharge1', 'aadharpay')->first();
                        }
                        
                        $post['provider_id'] = $provider->id;
                        if($post->transactionAmount > 0){
                            $post['charge'] = \Myhelper::getCommission($post->transactionAmount, $user->scheme_id, $post->provider_id, $user->role->slug);
                        }else{
                            $post['charge'] = 0;
                        }
                        $post['tds'] = 0;
                        $post['gst'] = 0;
                    }
                    
                    $insert = [
                        "mobile" => $post->mobileNumber,
                        "aadhar" => "XXXXXXXX".substr($post->adhaarNumber, -4),
                        "txnid"  => $post->txnid,
                        "amount" => $post->transactionAmount,
                        "charge" => $post->charge,
                        "gst"    => $post->gst,
                        "tds"     => $post->tds,
                        "bank"    => $bank->bankName,
                        "user_id" => $user->id,
                        'aepstype'=> $post->transactionType,
                        'authcode'=> $post->device,
                        'status'  => 'pending',
                        'credited_by' => $user->id,
                        'type' => 'credit',
                        'balance' => $user->aepsbalance,
                        'provider_id' => $post->provider_id,
                        'api_id' => $this->api->id,
                        'product' => 'aeps'
                    ];
                    
                    if($post->transactionType == "M"){
                        $insert['product'] = "aadharpay";
                    }
                    //try {
                        $report = Aepsreport::create($insert);
                    // } catch (\Exception $e) {
                    //     return response()->json(['status' => "ERR", "message" => "Technical Issue, Try Again"]);
                    // }
                }
                
                if($post->transactionType == "CW"){
                    $url = "https://sit.paysprint.in/service-api/api/v1/service/aeps/authcashwithdraw/index";
                    // $url = 'https://api.paysprint.in/api/v1/service/aeps/v2/cashwithdraw/index';
                    $parameter['transactionType'] = $post->transactionType;
                }else{
                    $url = "https://sit.paysprint.in/service-api/api/v1/service/aadharpay/aadharpay/index";
                    // $url = $this->api->url."aadharpay/aadharpay/index";
                    $parameter['transactionType'] = $post->transactionType;
                }

        
                $parameter['timestamp'] =  time();
                $parameter['pannumber'] = $agent->userPan;
                $parameter['merchant_name'] = $agent->merchantName;
                $parameter['transcationtype'] = $post->transactionType;
                $parameter['longitude'] = number_format($gpsdata->longitude, 4);
                $parameter['latitude'] = number_format($gpsdata->latitude, 4);
                $parameter['nationalbankidentification'] = $bank->iinno;
                $parameter['requestremarks'] = "Aeps";
                $parameter['mobilenumber'] = $post->mobileNumber;
                $parameter['merchantmobilenumber'] = $agent->merchantPhoneNumber;
                $parameter['adhaarnumber'] = $post->adhaarNumber;
                $parameter['data'] = $post->txtPidData;
                $parameter['referenceno'] = $post->txnid;
                $parameter['accessmodetype'] = "SITE";
                $parameter['ipaddress'] = $post->ip();
                $parameter['pipe'] = $post->pipe;
                $parameter['submerchantid'] = $agent->merchantLoginId;
                $parameter['address'] = $agent->merchantAddress;
                $parameter['pincode'] = $agent->merchantPinCode;
                $parameter['city'] = $agent->merchantCityName;
                $parameter['statecode'] = $agent->merchantState;
                $parameter['amount'] = $post->transactionAmount;

                $key = $this->api->optional2;
                $iv  = $this->api->optional3;
                $cipher   = openssl_encrypt(json_encode($parameter,true), 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
                $request  = base64_encode($cipher);
                $request  = array('body' => $request);
                break;
        }

        $header = array(
            "Cache-Control: no-cache",
            "Content-Type: application/x-www-form-urlencoded",
            "Token: ".$token['token'],
            "Authorisedkey: ".$this->api->optional1 
        );

        if(env('APP_ENV') == "local"){
            $result = array(
                "error" => false,
                "response" => json_encode([
                    "status" => true,
                    "message" => "Request Completed",
                    "ackno" => "local",
                    "amount" => $post->amount,
                    "balanceamount" => 0,
                    "bankrrn" => "local",
                    "response" => 200
                ])
            );
        }else{
            $result = \Myhelper::curl($url, "POST", http_build_query($request), $header, "yes", "App/Models/Aepsreport", $post->txnid);
        }
        
        \DB::table('rp_log')->insert([
            'ServiceName' => $post->transactionType,
            'header' => json_encode($header),
            'body' => json_encode([$parameter, $request]),
            'response' => $result['response'],
            'url' => $url,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
       // dd([$url, $token['payload'], json_encode($parameter), json_encode($request), $header, $result]);
        if($post->transactionType == "BE"){
          //dd([$url, json_encode($parameter), json_encode($request), $header, $result]);
        }
        
        if(isset($result['response']) && $result['response'] != ''){
            $response = json_decode($result['response']);
          //  dd($url, $token['payload'], json_encode($parameter), json_encode($request), $header, $result,$response);
            switch ($post->transactionType) {
                case 'getbanks':
                    $banks = \DB::table('aepsbanks')->get();
                    return response()->json(['statuscode' => 'TXN', 'message' => 'Bank List Fetched Successfully', 'data' => $banks]);
                    break;
                    
                case "BE":
                case "MS":
                    if(isset($response->status) && $response->status == true){
                        $outputdata['statuscode'] = "TXN";
                        $outputdata['status'] = "Success";
                        $outputdata['message'] = isset($response->message) ? $response->message : "Transaction Successfull";
                        $outputdata['id'] = isset($response->ackno) ? $response->ackno : "Not Found";
                        $outputdata['balance'] = isset($response->balanceamount) ? $response->balanceamount : "0";
                        $outputdata['rrn'] = isset($response->bankrrn) ? $response->bankrrn : "Not Found";
                        $outputdata['amount'] = $post->transactionAmount;
                        $outputdata['aadhar'] = "XXXXXXXX".substr($post->adhaarNumber, -4);
                        $outputdata['bank'] = $bank->bankName;
                        $outputdata['amount'] = $post->transactionAmount;
                        $outputdata['transactionType'] = $post->transactionType;
                        $update['status'] = "success";
                        $update['payid'] = isset($response->ackno) ? $response->ackno : "pending";
                        $update['refno'] = isset($response->bankrrn) ? $response->bankrrn : "pending";
                        $update['remark'] = isset($response->balanceamount) ? $response->balanceamount : "0";
                        Aepsreport::where('id', $report->id)->update($update);
                        if($post->transactionType == "BE"){
                            $outputdata['title'] = "Balance Enquiry";
                        }else{
                            
                            $outputdata['title'] = "Mini Statement";
                            $outputdata['data'] = isset($response->ministatement) ? $response->ministatement : [];
                        }
                        if($post->transactionType == "MS"){
                            User::where('id', $user->id)->increment('aepsbalance', $post->charge);
                        }
                        
        
                    }else{
                        $outputdata['statuscode'] = "TXF";
                        $outputdata['status'] = "Failed";
                        $outputdata['message'] = isset($response->message) ? $response->message : "Transaction Failed";
                        $outputdata['id'] = isset($response->ackno) ? $response->ackno : "failed";
                        $outputdata['balance'] = isset($response->balanceamount) ? $response->balanceamount : "0";
                        $outputdata['rrn'] = isset($response->bankrrn) ? $response->bankrrn : "failed";
                        $outputdata['amount'] = $post->transactionAmount;
                        $outputdata['aadhar'] = "XXXXXXXX".substr($post->adhaarNumber, -4);
                        $outputdata['bank'] = $bank->bankName;
                        $outputdata['amount'] = $post->transactionAmount;
                        $outputdata['transactionType'] = $post->transactionType;
                        $update['status'] = "failed";
                        $update['payid'] = isset($response->ackno) ? $response->ackno : "failed";
                        $update['refno'] = isset($response->message) ? $response->message : "failed";
                        $update['remark'] = isset($response->balanceamount) ? $response->balanceamount : "0";
                        Aepsreport::where('id', $report->id)->update($update);
                        if($post->transactionType == "BE"){
                            $outputdata['title'] = "Balance Enquiry";
                        }else{
                            $outputdata['title'] = "Mini Statement";
                            $outputdata['data'] = [];
                        }
                        
                       
                    }
                    return response()->json($outputdata);
                    break;
                    
                case "CW":
                case "M":
                    if(isset($response->status) && $response->status == true){
                        $update['status'] = "success";
                        $update['payid'] = isset($response->ackno) ? $response->ackno : "pending";
                        $update['refno'] = isset($response->bankrrn) ? $response->bankrrn : "pending";
                        $update['remark'] = isset($response->balanceamount) ? $response->balanceamount : "0";
                        Aepsreport::where('id', $report->id)->update($update);
                        
                        if($post->transactionType == "CW"){
                            User::where('id', $user->id)->increment('aepsbalance', $post->transactionAmount + $post->charge);
                        }else{
                            User::where('id', $post->user_id)->increment('aepsbalance', $post->transactionAmount - $post->charge);
                        }
                        
                         if($post->transactionAmount > 99 && $post->transactionType == "CW"){
                            \Myhelper::commission(Aepsreport::where('id', $report->id)->first());
                        }
                        
                        $outputdata['statuscode'] = "TXN";
                        $outputdata['status'] = "Success";
                        $outputdata['message'] = isset($response->message) ? $response->message : "Transaction Successfull";
                        $outputdata['id'] = isset($response->ackno) ? $response->ackno : "Not Found";
                        $outputdata['balance'] = isset($response->balanceamount) ? $response->balanceamount : "0";
                        $outputdata['rrn'] = isset($response->bankrrn) ? $response->bankrrn : "Not Found";
                        $outputdata['amount'] = $post->transactionAmount;
                        $outputdata['aadhar'] = "XXXXXXXX".substr($post->adhaarNumber, -4);
                        $outputdata['bank'] = $bank->bankName;
                        $outputdata['amount'] = $post->transactionAmount;
                        $outputdata['transactionType'] = $post->transactionType;
                        if($post->transactionType == "CW"){
                            $outputdata['title'] = "Cash Withdrawal";
                        }else{
                            $outputdata['title'] = "Aadhar Pay";
                        }
                        
                        $outputdata['statuscode'] = "TXN";
                        $outputdata['message'] = isset($response->message) ? $response->message : "Transaction Successfull";
                        $outputdata['ackno'] = isset($response->ackno) ? $response->ackno : "Not Found";
                        $outputdata['balanceamount'] = isset($response->balanceamount) ? $response->balanceamount : "0";
                        $outputdata['bankrrn'] = isset($response->bankrrn) ? $response->bankrrn : "Not Found";
                        
                        $token = $this->getToken(\Auth::id().Carbon::now()->timestamp);
                        $url = $this->api->url."aeps/threeway/threeway";
                        $parameter = [];
                        $parameter['reference'] = $post->txnid;
                        $parameter['status']    = "success";
        
                        $key = $this->api->optional2;
                        $iv  = $this->api->optional3;
                        $cipher   = openssl_encrypt(json_encode($parameter,true), 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
                        $request  = base64_encode($cipher);
                        $request  = array('body' => $request);
                        
                        $header = array(
                            "Cache-Control: no-cache",
                            "Content-Type: application/json",
                            "Token: ".$token['token']
                            //  "Authorisedkey: ".$this->api->optional1 
                        );
                        
                        $result = \Myhelper::curl($url, "POST", json_encode($request), $header, "yes", "App/Models/Aepsreport", $post->txnid);
                        \DB::table('rp_log')->insert([
                            'ServiceName' => $post->transactionType."-ThreeWay",
                            'header' => json_encode($header),
                            'body' => json_encode([$parameter, $request]),
                            'response' => $result['response'],
                            'url' => $url,
                            'created_at' => date('Y-m-d H:i:s')
                        ]);
                    }else{
                        $update['status'] = "failed";
                        $update['payid'] = isset($response->ackno) ? $response->ackno : "failed";
                        $update['refno'] = isset($response->message) ? $response->message : "failed";
                        $update['remark'] = isset($response->balanceamount) ? $response->balanceamount : "0";
                        Aepsreport::where('id', $report->id)->update($update);
                        
                        $outputdata['statuscode'] = "TXF";
                        $outputdata['status'] = "Failed";
                        $outputdata['message'] = isset($response->message) ? $response->message : "Transaction Failed";
                        $outputdata['id'] = isset($response->ackno) ? $response->ackno : "Not Found";
                        $outputdata['balance'] = isset($response->balanceamount) ? $response->balanceamount : "0";
                        $outputdata['rrn'] = isset($response->bankrrn) ? $response->bankrrn : "Not Found";
                        $outputdata['amount'] = $post->transactionAmount;
                        $outputdata['aadhar'] = "XXXXXXXX".substr($post->adhaarNumber, -4);
                        $outputdata['bank'] = $bank->bankName;
                        $outputdata['amount'] = $post->transactionAmount;
                        $outputdata['transactionType'] = $post->transactionType;
                        if($post->transactionType == "CW"){
                            $outputdata['title'] = "Cash Withdrawal";
                        }else{
                            $outputdata['title'] = "Aadhar Pay";
                        }
                        
                        $token = $this->getToken(\Auth::id().Carbon::now()->timestamp);
                        $url = $this->api->url."aeps/threeway/threeway";
                        $parameter = [];
                        $parameter['reference'] = $post->txnid;
                        $parameter['status']    = "failed";
        
                        $key = $this->api->optional2;
                        $iv  = $this->api->optional3;
                        $cipher   = openssl_encrypt(json_encode($parameter,true), 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
                        $request  = base64_encode($cipher);
                        $request  = array('body'=>$request);
                        
                        $header = array(
                            "Cache-Control: no-cache",
                            "Content-Type: application/json",
                            "Token: ".$token['token']
                            //  "Authorisedkey: ".$this->api->optional1  
                        );
                        $result = \Myhelper::curl($url, "POST", json_encode($request), $header, "yes", "App/Models/Aepsreport", $post->txnid);
                        \DB::table('rp_log')->insert([
                            'ServiceName' => $post->transactionType."-ThreeWay",
                            'header' => json_encode($header),
                            'body' => json_encode([$parameter, $request]),
                            'response' => $result['response'],
                            'url' => $url,
                            'created_at' => date('Y-m-d H:i:s')
                        ]);
                    }

                    Aepsreport::where('id', $report->id)->update($update);
                    return response()->json($outputdata);
                    break;
            }
        }
    } 

    public function setbank($data)
    {
        \DB::table('aepssettlebanks')->delete();
        foreach ($data as $data) {
            $insert['bankname'] = $data->bankname;
            $inserts[] = $insert;
        }
        \DB::table('aepssettlebanks')->insert($inserts);
    }

    public function getToken($uniqueid)
    {
        $payload =  [
            "timestamp" => time(),
            "partnerId" => $this->api->username,
            "reqid"     => $uniqueid
        ];
        
        //$keyString =$this->api->password;
        $keyString = "UFMwMDIwMTc3MzE4YWI2ODFlODhiMDNiN2U2Y2MyN2ExYjA1YTFjMQ==";
        
        if (strlen($keyString) < 32) {
            throw new \Exception ("Key length is too short. It must be at least 32 characters.");
        }
        $key = new HmacKey($keyString);
    
        $algorithm = new HS256($key);
    
        // Generate a JWT
        $generator = new Generator($algorithm);
    
        try {
            $jwt = $generator->generate($payload);
           
            return ['token' => $jwt, 'payload' => $payload];
        } catch (\Exception $e) {
           
            dd($e->getMessage());
        }
         
        }
    
    
     public function getTokenUat($uniqueid)
    {
        $payload =  [
            "timestamp" => time(),
            "partnerId" => "PS001160", //$this->api->username,
            "reqid"     => $uniqueid
        ];
        
        $key = "UFMwMDExNjA1OTBhYzAzODcyODkwNzFhNjUxZmQzYzM2NThmN2VkMg=="; //$this->api->password;
        $signer = new HS256($key);
        $generator = new JwtGenerator($signer);
        return ['token' => $generator->generate($payload), 'payload' => $payload];
    }

    public function myvalidate($post)
    {
        $validate = "yes";
        switch ($post->transactionType) {
            case 'BE':
            case "MS":
                $rules = array('user_id' => 'required|numeric', 'mobileNumber' => 'required|numeric|digits:10', 'adhaarNumber' => 'required|numeric|digits:12', "txtPidData" => "required", 'bankid' => 'required|numeric');
            break;

            case 'CW':
            case "M":
                $rules = array('user_id' => 'required|numeric', 'mobileNumber' => 'required|numeric|digits:10', 'adhaarNumber' => 'required|numeric|digits:12', "txtPidData" => "required", 'bankid' => 'required|numeric', 'transactionAmount' => 'required|numeric|min:100');
            break;

            case 'getbanks':
            case 'getaepsfundbanks':
                $rules = array('user_id' => 'required|numeric');
            break;

            default:
                return ['status'=>'Invalid request format','message'=> "Invalid request format"];
            break;
        }

        if($validate == "yes"){
            $validator = \Validator::make($post->all(), $rules);
            if ($validator->fails()) {
                return ['status' => 'yes','errors'=>$validator->errors()];
            }else{
                $data = ['status'=>'NV'];
            }
        }else{
            $data = ['status'=>'NV'];
        }
        return $data;
    }

    public function kyc(Request $post)
    {
        $user = User::where('id', \Auth::id())->first();
        
        if (\Myhelper::hasRole('admin') || !\Myhelper::can('aeps_service')) {
            return response()->json(["statuscode" => "ERR", 'status' => "Permission not allowed"]);
        }

        if(!$this->api || $this->api->status == 0){
            return response()->json(["statuscode" => "ERR", 'status' => "R-Aeps Service Currently Down."]);
        }

        $rules = array(
            'merchantName' => 'required',
            'merchantShopname' => 'required',
            'merchantEmail' => 'required',
            'merchantPhoneNumber' => 'required'
        );

        $validator = \Validator::make($post->all(), $rules);
        if ($validator->fails()) {
            return \Response::json($validator->getMessageBag()->toArray(), 422);
        }
        $checkmerchent = Aepsuser :: where( 'user_id' , $user->id)->first();
       
        $token = $this->getToken(\Auth::id().Carbon::now()->timestamp);
        if($checkmerchent){
          $parameter['merchantcode'] = $checkmerchent->merchantLoginId ;
          $post['merchantPhoneNumber'] = $checkmerchent->merchantPhoneNumber ;
        }else{
        $parameter['merchantcode'] = "MP".date('ymdH').$user->id;
        }
        //dd($checkmerchent,$parameter);
        $parameter['is_new']    = "1";
        $parameter['mobile']    = $post->merchantPhoneNumber;
        $parameter['email']     = $post->merchantEmail;
        $parameter['firm']      = $post->merchantShopname;
        $parameter['callback']  = url('api/paysprint/service/update/callback');

        $key = $this->api->optional2;
        $iv  = $this->api->optional3;
                    
        $header = array(
            "Cache-Control: no-cache",
            "Content-Type: application/json",
            "Token: ".$token['token'],
            "Authorisedkey: ".$this->api->optional1
            
        );
        $url = $this->api->url."onboard/onboardnew/getonboardurl";
        //live ul
        // $url = $this->api->url."onboard/onboard/getonboardurl";
        // dd($url);
        $results = \Myhelper::curl($url, "POST", json_encode($parameter), $header, "yes", json_encode($token['payload']), \Auth::id().Carbon::now()->timestamp);
         //dd($results,$url,json_encode($parameter), $header);
        \DB::table('rp_log')->insert([
            'ServiceName' => "Onboard",
            'header' => json_encode($header),
            'body' => json_encode($parameter),
            'response' => $results['response'],
            'url' => $url,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        $result=trim($results['response']);
          if($result != ''){
            $data = json_decode($result);
         
             if($data->status == "true" && isset($data->redirecturl)){
                $action = Aepsuser::updateOrCreate(['user_id'=> $user->id], [
                    "merchantLoginId" => $parameter['merchantcode'],
                    "merchantName" => $user->name,
                    "merchantEmail" => $post->merchantEmail,
                    "merchantPhoneNumber" => $post->merchantPhoneNumber,
                    "merchantShopname" => $post->merchantShopname,
                    'status' => 'pending',
                    'user_id' => $user->id
                ]);
                
                return \Redirect::away($data->redirecturl);
            }else{
                $datas = $data;
                $isdone2fa1 = \DB::table('twostepauths')->where(['user_id'=>\Auth::id(),'date'=>strtotime(date('Y-m-d')),'api'=>'paysprint1'])->first();
                
                if($isdone2fa1){
                    $data1['isdoneaepsauth1'] = 'true';
                }else{
                    $data1['isdoneaepsauth1'] = 'false';
                }
                
                 $isdone2fa2 = \DB::table('twostepauths')->where(['user_id'=>\Auth::id(),'date'=>strtotime(date('Y-m-d')),'api'=>'paysprint2'])->first();
                
                if($isdone2fa2){
                    $data1['isdoneaepsauth2'] = 'true';
                }else{
                    $data1['isdoneaepsauth2'] = 'false';
                }       
              
              
                 $isdone2fa3 = \DB::table('twostepauths')->where(['user_id'=>\Auth::id(),'date'=>strtotime(date('Y-m-d')),'api'=>'paysprint3'])->first();
                
                if($isdone2fa3){
                    $data1['isdoneaepsauth3'] = 'true';
                }else{
                    $data1['isdoneaepsauth3'] = 'false';
                }
                
                
                $data1['agent'] = Aepsuser::where('user_id', \Auth::id())->first();
                $data1['user']  = \Auth::user();
                $data1['mahastate'] = Circle::all();
                $data1['bankName'] = \DB::table('fingaepsbanks')->get();
                $data1['error'] = isset($datas->message)?$datas->message:'null';
               return view('service.raeps')->with($data1);  
            }
        }
        
        $datas = $data;
        
        $data1['agent'] = Aepsuser::where('user_id', \Auth::id())->first();
        $data1['user']  = \Auth::user();
        $data1['mahastate'] = Circle::all();
        $data1['bankName'] = \DB::table('fingaepsbanks')->get();
        $data1['error'] = isset($datas->message)?$datas->message:'null';
        return view('service.raeps')->with($data1);
    }
    
    public function bankList(Request $post)
    {
            $token = $this->getTokenUat($post->user_id.Carbon::now()->timestamp);
                
                $parameter['bankid'] = 1177;
                $parameter['merchant_code']  = "R121";
                $parameter['account']  = "917479108684";
                $parameter['ifsc']  = "PYTM0123456";
                $parameter['name']  = "SOURAV";
                $parameter['account_type']  = "PRIMARY";
                
            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => 'https://paysprint.in/service-api/api/v1/service/payout/payout/add',
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_POSTFIELDS =>'{"bankid":1085,"merchant_code":"MB2302244","account":"917479108689","ifsc":"PYTM0123456","name":"SOURAV","account_type":"PRIMARY" }',
              CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Token:'.$token['token'],
                'Authorisedkey:NjJlNGEwZDBlNzJmOTU1NmVlNWU1NTI0ZmYxYTQ0MzI=',
                'Content-Tsion=6b06cc8557c3f39b14b77eb940ae475b516513ac'
              ),
            ));
            
            $response = curl_exec($curl);
            
            curl_close($curl);
            
            dd($response);
            
            curl_close($curl);
            
            dd([$response,$token,'https://paysprint.in/service-api/api/v1/service/payout/payout/add']);
  
    }
    
 
    public function fingpay2fa(Request $post)
    {
        

        $post['user_id'] = \Auth::id();
        $user = User::where('id', $post->user_id)->first();
        $agent = Aepsuser::where('user_id', \Auth::id())->first();


        do {
            $post['txnid'] = $this->transcode().rand(11111111, 99999999);
        } while (Report::where("txnid", "=", $post->txnid)->first() instanceof Report);

        $ip = $post->ip();
        $gpsdata = Location::get($ip);
        
      
        $parameter['timestamp'] = Carbon::now()->format('d/m/Y h:i:s');
 
        $parameter['longitude'] = $gpsdata->longitude;
        $parameter['latitude'] = $gpsdata->latitude;
       
        $parameter['mobilenumber'] = $user->mobile;
        $parameter['adhaarnumber'] = $post->adhaarNumber;
        $parameter['data'] = $post->mybiodata;
        $parameter['referenceno'] = $post->txnid;
        $parameter['accessmodetype'] = "SITE";
        $parameter['ipaddress'] = $post->ip();
        //$parameter['pipe'] = $post->bankpipe;
        $parameter['submerchantid'] = $agent->merchantLoginId;

        $key = $this->api->optional2;
        $iv  = $this->api->optional3;
        $cipher   = openssl_encrypt(json_encode($parameter,true), 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
        $request  = base64_encode($cipher);
        $request  = array('body'=>$request);
        
        //dd($request);
        if($post->bankpipe == 'bank2'){
            if($user->isbank2regauth == 'no'){
                // $url = "https://api.paysprint.in/api/v1/service/aeps/kyc/Twofactorkyc/registration";
                $url = "https://sit.paysprint.in/service-api/api/v1/service/aeps/kyc/Twofactorkyc/registration";
            }else{
                
                $url = "https://sit.paysprint.in/service-api/api/v1/service/aeps/kyc/Twofactorkyc/authentication";
                // $url = "https://api.paysprint.in/api/v1/service/aeps/kyc/Twofactorkyc/auth_login";
            }
           
        }else{
            
            if($user->isbank1regauth == 'no'){
                 $url = "https://sit.paysprint.in/service-api/api/v1/service/aeps/kyc/Twofactorkyc/register_agent";
                //  $url = "https://api.paysprint.in/api/v1/service/aeps/kyc/Twofactorkyc/auth_login";
            }else{
                // $url = "https://api.paysprint.in/api/v1/service/aeps/kyc/Twofactorkyc/auth_login";
                $url="https://sit.paysprint.in/service-api/api/v1/service/aeps/kyc/Twofactorkyc/auth_login";
            }
           
        }

        $token = $this->getToken(\Auth::id().Carbon::now()->timestamp);

        $header = array(
            "Cache-Control: no-cache",
            "Content-Type: application/json",
            "Token: ".$token['token'],
            "Authorisedkey: ".$this->api->optional1 
        );
        
        $result  = \Myhelper::curl($url, 'POST',json_encode($request), $header, "yes", "authvalidate", $post->txnid);

        
        $response = json_decode($result['response']);
        
        
        if(isset($response->status) && $response->status == true){
            if($post->bankpipe == 'bank2'){
                if($user->isbank2regauth == 'no'){
                    $checkyser = User::where('id',$user->id)->update(['isbank2regauth'=>'yes']);
                    return response()->json(['status' => 'TUP', 'message' =>'Registration 2fa done, continue for authenticatin again']); 
                }
                
                $aaray = array();
                $aaray['user_id'] = $post->user_id;
                $aaray['date'] = strtotime(date('Y-m-d'));
                $aaray['api'] = "paysprint2";
                $aaray['status'] = "success";
                
                $checkyser = \DB::table('twostepauths')->where(['user_id'=>$post->user_id,'date'=>$aaray['date'],'api'=>'paysprint2'])->first();
                if(!$checkyser){
                    \DB::table('twostepauths')->insert($aaray);
                }
                
                return response()->json(['status' => 'TXN', 'message' =>'Bank2 2fa verification done, continue the transaction']);  
                    
                    
            }else if($post->bankpipe == 'bank3'){
                if($user->isbank3regauth == 'no'){
                    $checkyser = User::where('id',$user->id)->update(['isbank3regauth'=>'yes']);
                    return response()->json(['status' => 'TUP', 'message' =>'Registration 2fa done, continue for authenticatin again']); 
                }
                
                $aaray = array();
                $aaray['user_id'] = $post->user_id;
                $aaray['date'] = strtotime(date('Y-m-d'));
                $aaray['api'] = "paysprin3";
                $aaray['status'] = "success";
                
                $checkyser = \DB::table('twostepauths')->where(['user_id'=>$post->user_id,'date'=>$aaray['date'],'api'=>'paysprint3'])->first();
                if(!$checkyser){
                    \DB::table('twostepauths')->insert($aaray);
                }
                
                return response()->json(['status' => 'TXN', 'message' =>'Bank3 2fa verification done, continue the transaction']);  
                    
                    
            }else if($post->bankpipe == 'bank5'){
                if($user->isbank5regauth == 'no'){
                    $checkyser = User::where('id',$user->id)->update(['isbank5regauth'=>'yes']);
                    return response()->json(['status' => 'TUP', 'message' =>'Registration 2fa done, continue for authenticatin again']); 
                }
                
                $aaray = array();
                $aaray['user_id'] = $post->user_id;
                $aaray['date'] = strtotime(date('Y-m-d'));
                $aaray['api'] = "paysprint5";
                $aaray['status'] = "success";
                
                $checkyser = \DB::table('twostepauths')->where(['user_id'=>$post->user_id,'date'=>$aaray['date'],'api'=>'paysprint5'])->first();
                if(!$checkyser){
                    \DB::table('twostepauths')->insert($aaray);
                }
                
                return response()->json(['status' => 'TXN', 'message' =>'Bank5 2fa verification done, continue the transaction']);  
                    
                    
            }
            else{
                if($user->isbank1regauth == 'no'){
                    $checkyser = User::where('id',$user->id)->update(['isbank1regauth'=>'yes']);
                    return response()->json(['status' => 'TUP', 'message' =>'Registration 2fa done, continue for authenticatin again']); 
                }
                
                $aaray = array();
                $aaray['user_id'] = $post->user_id;
                $aaray['date'] = strtotime(date('Y-m-d'));
                $aaray['api'] = "paysprint1";
                $aaray['status'] = "success";
                
                $checkyser = \DB::table('twostepauths')->where(['user_id'=>$post->user_id,'date'=>$aaray['date'],'api'=>'paysprint1'])->first();
                if(!$checkyser){
                    \DB::table('twostepauths')->insert($aaray);
                }
                
                return response()->json(['status' => 'TXN', 'message' =>'Bank1 2fa verification done, continue the transaction']);  
                    
            }
            
        
        }else{
            return response()->json(['status' => 'ERR', 'message' => isset($response->message) ? $response->message : 'Authentication Failed']);
        }
            
    }  
    
    
      public function serActive(Request $post)
    {
      
        $post['user_id'] = \Auth::id();
        $user = User::where('id', $post->user_id)->first();
        $agent = Aepsuser::where('user_id', \Auth::id())->first();

        do {
            $post['txnid'] = $this->transcode().rand(11111111, 99999999);
        } while (Report::where("txnid", "=", $post->txnid)->first() instanceof Report);

        $ip = $post->ip();
        $gpsdata = Location::get($ip);
        
        $parameter['aadhaar'] = $post->adhaarNumber;
        // $parameter['piddata'] = $post->mybiodata;
        $parameter['merchantcode'] = $agent->merchantLoginId;
        $parameter['dob'] = $post->dob;
        $parameter['is_casa'] ='0';

        $key = $this->api->optional2;
        $iv  = $this->api->optional3;
        $cipher   = openssl_encrypt($post->mybiodata, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
        $parameter['piddata']  = base64_encode($cipher);
        
        
        //dd($request);
        
        $url="https://sit.paysprint.in/service-api/api/v1/service/onboard/onboard/activate_merchant";

        $token = $this->getToken(\Auth::id().Carbon::now()->timestamp);

        $header = array(
            "Cache-Control: no-cache",
            "Content-Type: application/json",
            "Token: ".$token['token'],
            "Authorisedkey: ".$this->api->optional1 
        );
        
        $result  = \Myhelper::curl($url, 'POST',json_encode($parameter), $header, "yes", "authvalidate", $post->txnid);
        
        // dd($url,json_encode($parameter), $header,$result);
        $response = json_decode($result['response']);
        
        
        if(isset($response->status) && $response->status == true){
           
                // if($user->isbank2regauth == 'no'){
                //     $checkyser = User::where('id',$user->id)->update(['isbank2regauth'=>'yes']);
                //     return response()->json(['status' => 'TUP', 'message' =>'Registration 2fa done, continue for authenticatin again']); 
                // }
                
                // $aaray = array();
                // $aaray['user_id'] = $post->user_id;
                // $aaray['date'] = strtotime(date('Y-m-d'));
                // $aaray['api'] = "paysprint2";
                // $aaray['status'] = "success";
                
                // $checkyser = \DB::table('twostepauths')->where(['user_id'=>$post->user_id,'date'=>$aaray['date'],'api'=>'paysprint2'])->first();
                // if(!$checkyser){
                //     \DB::table('twostepauths')->insert($aaray);
                // }
                
                return response()->json(['status' => 'TXN', 'message' =>$response->message]);  
                    
        
        }else{
            return response()->json(['status' => 'ERR', 'message' => isset($response->message) ? $response->message : 'Authentication Failed']);
        }
            
    } 
    
    
}
