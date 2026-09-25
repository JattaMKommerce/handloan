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

class InsuranceController extends Controller
{
    protected $api;
    public function __construct()
    {
        $this->api = Api::where('code', 'insurance')->first();
    }
    
    public function index()
    {
        if (\Myhelper::hasRole('admin') || !\Myhelper::can('insurance_service')) {
            abort(403);
        }
        $data['providers'] = Provider::where('type','lic')->where('status', "1")->orderBy('name')->get();

       
        $post['user_id'] = \Auth::id();
        return view('service.insurance')->with($data);
    }
    
    public function payment(Request $post)
    {
        if (\Myhelper::hasRole('admin') || !\Myhelper::can('insurance_service')) {
            return response()->json(['status' => "Permission Not Allowed"], 400);
        }

        

        $user = \Auth::user();
        $post['user_id'] = $user->id;

        if($user->status != "active"){
           // return response()->json(['status' => "Your account has been blocked."], 400);
        }

        

        
        switch ($post->type) {
            case 'getbilldetails':
                $rules = array(
                   'number' => 'required|numeric',
                   'email' => 'required|email',
                   'dob' => 'required',
                   
                   
                  );
                $validate = \Myhelper::FormValidator($rules, $post);
                if($validate != "no"){
                    return $validate;
                }
                $post['txnid'] = $this->transcode().rand(1111111111,1000000000);
                $url = $this->api->url."/bill-payment/bill/fetchlicbill";
                
                $parameter = [
                    "canumber" => $post->number,
                    "ad1" => $post->email,
                    "ad2" => date('d/m/Y',strtotime($post->dob)),
                     "mode"   => 'online',
                ];

                $token = $this->getToken($post->user_id.Carbon::now()->timestamp);
                $header = array(
                    "Cache-Control: no-cache",
                    "Content-Type: application/json",
                    "Token: ".$token['token'],
                    "Authorisedkey: ".$this->api->optional1
                );

                $query = json_encode($parameter);

                $result = \Myhelper::curl($url, "POST", $query, $header, "no");
                \DB::table('rp_log')->insert([
                        'ServiceName' => "Insurance_Bill_FETCH",
                        'header' => json_encode($header),
                        'body' => json_encode($parameter),
                        'response' => $result['response'],
                        'url' => $url,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                        
                //dd([$url,$query,$result,$header]);
                if(!$result['error'] && $result['response'] != ''){
                    $doc = json_decode($result['response']);
                    if(isset($doc->response_code) && in_array($doc->response_code, [1, 4, 8])){
                        return response()->json([
                            'statuscode' => "TXN",
                            'data'       => [
                                "customername" => $doc->name,
                                "duedate"      => $doc->duedate,
                                "dueamount"    => $doc->amount,
                                "TransactionId" => json_encode($doc->bill_fetch),
                                "ad2"           => $doc->ad2,
                                "ad3"           => $doc->ad3    
                            ]
                        ]);
                    }else{
                        return response()->json(['statuscode' => "ERR", "message" => $doc->message]);
                    }
                }else{
                    return response()->json(['statuscode' => "ERR", "message" => $result['error']]);
                }
                
                
                break;
            
            case 'payment':
                //dd($post->all());
                $rules['amount'] = "required";
                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    foreach ($validator->errors()->messages() as $key => $value) {
                        $error = $value[0];
                    }
                    return response()->json(['status' => $error]);
                }
                
               
                // if ($this->pinCheck($post) == "fail") {
                //     return response()->json(['status' => "Transaction Pin is incorrect"], 400);
                // }
                
               
                //dd($user->mainwallet.' - '.$this->mainlocked().' <'. $post->amount);

                if($user->mainwallet - $this->mainlocked() < $post->amount){
                    //return response()->json(['status'=> 'Low Balance, Kindly recharge your wallet.'], 400);
                }

                $previousrecharge = Report::where('number', $post->number)->where('amount', $post->amount)->whereBetween('created_at', [Carbon::now()->subMinutes(2)->format('Y-m-d H:i:s'), Carbon::now()->format('Y-m-d H:i:s')])->count();
                if($previousrecharge > 0){
                    return response()->json(['status'=> 'Same Transaction allowed after 2 min.'], 400);
                }
                if($post->mode=="offline"){
                 if($post->amount >0 && $post->amount<=100000){
                $provider = Provider::where('recharge1', 'licslab1')->first();
                 }
                 elseif($post->amount > 100000){
                   $provider = Provider::where('recharge1', 'licslab2')->first();  
                 }
                $post['provider_id']=$provider->id;
                }
                else{
                    $provider = Provider::where('recharge1', 'pinsurance')->first();  
                   $post['provider_id']= $provider->id;  
                }
                $post['profit'] = \Myhelper::getCommission($post->amount, $user->scheme_id, $post->provider_id, $user->role->slug);
                $post['debitAmount'] = $post->amount - ($post->profit );
                $debit = User::where('id', $user->id)->increment('mainwallet', $post->debitAmount);
                
                if ($debit) {
                    do {
                        $post['txnid'] = $this->transcode().rand(1111111111, 9999999999);
                    } while (Report::where("txnid", "=", $post->txnid)->first() instanceof Report);

                    $insert = [
                        'number'  => $post->number,
                        'mobile'  => isset($post->number1)?$post->number1:$user->mobile,
                        'provider_id' => $provider->id ,
                        'api_id'  => 18,
                        'amount'  => $post->amount,
                        'profit'  => $post->profit,
                        'txnid'   => $post->txnid,
                        'payid'   => $post->TransactionId,
                        'option1' => $post->biller,
                        'option2' => $post->duedate,
                        'status'  => 'pending',
                        'user_id'    => $user->id,
                        'credit_by'  => $user->id,
                        'rtype'      => 'main',
                        'via'        => 'portal',
                        'balance'    => $user->mainwallet,
                        'trans_type' => 'debit',
                        'product'    => 'billpay', 
                        'transtype'  =>'mainwallet',
                    ];

                    $report = Report::create($insert);
                    $url = $this->api->url."/bill-payment/bill/paylicbill";
                    $gpsdata = Location::get($post->ip());
                    $parameter = [
                                "canumber" => $post->number,
                                "amount"   => $post->amount,
                                "ad1"      => $user->email,
                                "ad2"      => $post->dob,
                                "referenceid" => $post->txnid,
                                "latitude"    => number_format($gpsdata->latitude, 4),
                                "longitude"   => number_format($gpsdata->longitude, 4),
                                "mode"        => 'online',
                                "bill_fetch"  => json_decode($post->TransactionId)
                            ];

                    $token = $this->getToken($post->user_id.Carbon::now()->timestamp);
                    $header = array(
                                "Cache-Control: no-cache",
                                "Content-Type: application/json",
                                "Token: ".$token['token'],
                                "Authorisedkey: ".$this->api->optional1
                            );

                    $query  = json_encode($parameter);
                    $method = "POST";
                    //$result = \Myhelper::curl($url, "POST", $query, $header, "yes", "App\Models\Report", $post->txnid);
                    //dd($query,$result,$header);

                    if (env('APP_ENV') == "server") {
                        
                        $result = \Myhelper::curl($url, "POST", json_encode($parameter), $header, "yes", "App\Models\Report", $post->txnid);
                        //dd($result,$query,$header);
                    }else{
                        
                        $result = [
                            'error' => true,
                            'response' => '' 
                        ];
                    }

                    
                
                    if($result['error'] || $result['response'] == ''){
                       
                        $update['status'] = "pending";
                        $update['payid'] = "pending";
                        $update['description'] = "billpayment pending";
                    }else{
                        
                    \DB::table('rp_log')->insert([
                        'ServiceName' => "Insurance_BillPay",
                        'header' => json_encode($header),
                        'body' => json_encode($parameter),
                        'response' => $result['response'],
                        'url' => $url,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                
                    $doc = json_decode($result['response']);
                    
                    if(isset($doc->response_code) && in_array($doc->response_code, [1])){
                        $update['status'] = "success";
                        $update['refno'] = $doc->ackno;
                        $update['description'] = "LIC Bill Accepted";
                    }elseif(isset($doc->response_code) && in_array($doc->response_code, [5,14,16,6,17,8,18,12,9,13,14,15,16,17,18,10,11])){
                        $update['status'] = "failed";
                        $update['description'] = $doc->message;
                        $update['refno'] =  $doc->message;
                        if($doc->message == "Insufficient fund in your account. Please topup your wallet before initiating transaction."){
                            $update['refno'] =  "Service down for sometime";
                            $update['description'] = $doc->message;
                        }
                    }else{
                        $update['status'] = "pending";
                        $update['refno']  = "Please wait for status change or contact service provider";
                        $update['description'] = $doc->message;
                    }
                                
                    }

                    if($update['status'] == "success" || $update['status'] == "pending"){
                        Report::where('id', $report->id)->update($update);
                        \Myhelper::commission($report);
                    }else{
                        User::where('id', $user->id)->increment('mainwallet', $post->debitAmount);
                        Report::where('id', $report->id)->update($update);
                    }

                    return response()->json(['status' => $update['status'], 'data' => $report, 'description' => $update['description']], 200);
                }else{
                    return response()->json(['status'=> 'Transaction Failed, please try again.'], 400);
                }
                break;
        }
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
}