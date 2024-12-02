<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Api;
use App\Models\Provider;
use App\Models\Mahabank;
use App\Models\Report;
use App\Models\Commission;
use App\Models\Packagecommission;
use App\Models\User;
use Carbon\Carbon;
use MiladRahimi\Jwt\Generator;
use MiladRahimi\Jwt\Parser;
use MiladRahimi\Jwt\Cryptography\Keys\HmacKey;
use MiladRahimi\Jwt\Cryptography\Algorithms\Hmac\HS256;
use Stevebauman\Location\Facades\Location;

class PdmtController extends Controller
{
    protected $api;
    public function __construct()
    {
        $this->api = Api::where('code', 'pdmt')->first();
    }

    public function index()
    {
        if (\Myhelper::hasRole('admin') || !\Myhelper::can('dmt1_service')) {
            abort(403);
        }
        $isdone2fa = \DB::table('dmtkyc')->where(['user_id'=>\Auth::id(),'date'=>strtotime(date('Y-m-d')),'api'=>'dmtkyc'])->first();
        if($isdone2fa){
            $data['dmtkyc'] = 'true';
        }else{
            $data['dmtkyc'] = 'false';
        }
        $data['banks'] = \DB::table('dmtbanks')->get();
        $data['state'] = \DB::table('circles')->get();
        return view('service.dmt2')->with($data);
    }
    
    public function xdmtindex()
    {
        
        if (\Myhelper::hasRole('admin') || !\Myhelper::can('dmt1_service')) {
            abort(403);
        }
        
        $agents = \DB::table('dmt_agents')->where('user_id',\Auth::id())->first();
        if(empty($agents))
        {
            $is_agent = false;
        }
        else
        {
            $is_agent = true;
        }
        $data['is_agent'] = $is_agent;
        $data['banks'] = \DB::table('dmtbanks')->get();
        $data['state'] = \DB::table('circles')->get();
        return view('service.xdmt')->with($data);
    }
    public function payment(Request $post)
    {
         
         if ((!\Myhelper::can('dmt1_service') && ($post->type != 'getdistrict' && $post->type != 'refundotp'))) {
           return \Response::json(['statuscode' => 'ERR', 'message' => "Permission not allowed"]);
         }
        
    
        if(!$this->api || $this->api->status == 0){
           return response()->json(['statuscode' => 'ERR', 'message' => "Money Transfer Service Currently Down."]);
        }

        $post['user_id'] = \Auth::id();
        $userdata = User::where('id', $post->user_id)->first();
        // if(!$userdata->pincode || !$userdata->address){
        //       return response()->json(['statuscode' => 'ERR', 'message' => "Kindly update your address and pincode."],400);
        // }           
        if($post->type == "transfer"){
            $codes = ['dmt1', 'dmt2', 'dmt3', 'dmt4', 'dmt5'];
            $providerids = [];
            foreach ($codes as $value) {
                $providerids[] = Provider::where('recharge1', $value)->first(['id'])->id;
            }
            $commission = Commission::where('scheme_id', $userdata->scheme_id)->whereIn('slab', $providerids)->get();
            if(!$commission || sizeof($commission) < 5){
               return response()->json(['statuscode' => 'ERR', 'message' => "Money Transfer charges not set, contact administrator."],400);
            }
        }
        
      
        // if($post->type=='accountverification'){
        //           $token = $this->getpayoutToken($post->user_id.Carbon::now()->timestamp.rand(999,111));
        //           $header = array(
        //             "Cache-Control: no-cache",
        //             "Content-Type: application/json",
        //             "Token: ".$token['token'],
        //             "Authorisedkey: TVRJek5EVTJOelUwTnpKRFQxSlFNREF3TURFPQ=="
        //         ); 
        //         $url='https://sit.paysprint.in/service-api/api/v1/service/verification/bank/verify';
        //         $parameters = [
        //                 "refid" => \Myhelper::generateUniqueToken(),
        //                 "account_number" =>  $post->beneaccount,
        //                 "ifsc_code"     => $post->beneifsc,
        //                 "ifsc_details"  => true
        //             ];
                    
        //       $result = \Myhelper::curl($url, "POST", json_encode($parameters), $header, "no", 'App\Models\Report', '0');
              
        //       dd([$url,$header,json_encode($parameters),$result]);
                
        //   }
        
        switch ($post->type) {
            case 'verification':
            case 'KYC':
            case 'outletotp':
            case 'ekycotp':
            case 'getbeneficiary':
                $rules = array('user_id' => 'required|numeric','mobile' => 'required|numeric');
            break;
            case 'beniverification':
                 $rules = array('user_id' => 'required|numeric','mobile' => 'required|numeric','beneid' => 'required|numeric');
             break ;
            case 'outletregister':
                $rules = array('user_id' => 'required|numeric','merchantPhoneNumber' => 'required|numeric','merchantName' => 'required','merchantState' => 'required','merchantEmail' => 'required', 'userPan' => 'required', 'merchantAddress' => 'required', 'otp' => 'required');
            break;

            case 'mobilechange':
                $rules = array('user_id' => 'required|numeric','mobile' => 'required|numeric','newmobile' => 'required|numeric');
            break;

            case 'mobilechangeverify':
                $rules = array('user_id' => 'required|numeric','mobile' => 'required|numeric','newmobile' => 'required|numeric','otp' => 'required|numeric','newotp' => 'required|numeric');
            break;

            case 'benedelete':
                $rules = array('rid' => 'required|numeric', 'bid' => 'required|numeric');
            break;

            case 'benedeletevalidate':
                $rules = array('transid' => 'required', 'otp' => 'required|numeric');
            break;
            
            case 'registration':
                $rules = array('user_id' => 'required|numeric','mobile' => 'required|numeric|digits:10', 'firstname' => 'required|regex:/^[\pL\s\-]+$/u', 'lastname' => 'required|regex:/^[\pL\s\-]+$/u');
            break;

            case 'registrationValidate':
                $rules = array('rid' => 'required', 'otp' => 'required|numeric');
            break;

            case 'addbeneficiary':
                $rules = array('user_id' => 'required|numeric','mobile' => 'required|numeric|digits:10', 'benebank' => 'required', 'beneifsc' => "required", 'beneaccount' => "required|numeric|digits_between:6,20", "benename" => "required|regex:/^[\pL\s\-]+$/u");
            break;

            case 'beneverify':
                $rules = array('user_id' => 'required|numeric','mobile' => 'required|numeric|digits:10','beneaccount' => "required|numeric|digits_between:6,20", "benemobile" => 'required|numeric|digits:10', "otp" => 'required|numeric');
            break;

            case 'accountverification':
                $rules = array('user_id' => 'required|numeric','mobile' => 'required|numeric|digits:10', 'benebank' => 'required', 'beneifsc' => "required", 'beneaccount' => "required|numeric|digits_between:6,20");
            break;

            case 'transfer':
                $rules = array('user_id' => 'required|numeric','otp'=>'required','name' => 'required','mobile' => 'required|numeric|digits:10', 'benebank' => 'required', 'beneifsc' => "required", 'beneaccount' => "required|numeric|digits_between:6,20","benename" => "required",'amount' => 'required|numeric|min:100|max:25000');
            break;
            
            case 'send_otp':
                $rules = array('user_id' => 'required|numeric','name' => 'required','mobile' => 'required|numeric|digits:10', 'benebank' => 'required', 'beneifsc' => "required", 'beneaccount' => "required|numeric|digits_between:6,20","benename" => "required",'amount' => 'required|numeric|min:100|max:25000');
            break;
            
            case 'refundotp':
                $rules = array('user_id' => 'required|numeric', 'id' => 'required|numeric');
            break;

            case 'getrefund':
                $rules = array('user_id' => 'required|numeric', 'otp' => 'required|numeric');
            break;

            default:
                return ['statuscode'=>'BPR', "status" => "Bad Parameter Request", 'message'=> "Invalid request format"];
            break;
        }

        $validate = \Myhelper::FormValidator($rules, $post);
        if($validate != "no"){
            return $validate;
        }


        
        $token = $this->getToken($post->user_id.Carbon::now()->timestamp.rand(999,111));
        $header = array(
            "Cache-Control: no-cache",
            "Content-Type: application/json",
            "Token: ".$token['token'],
            "Authorisedkey: ".$this->api->optional1
        );

        switch ($post->type) {
            case 'verification':
               
                $url = $this->api->url."kyc/remitter/queryremitter";
                $parameters = [
                    "bank3_flag" => "no",
                    "mobile"     => $post->mobile
                ];

                break;
                
            case 'KYC':
                $post['amount'] = 1;
                $provider = Provider::where('recharge1', 'dmt1kyc')->first();
                $post['charge'] = \Myhelper::getCommission($post->amount, $userdata->scheme_id, $provider->id, $userdata->role->slug);
                $post['provider_id'] = $provider->id;
                if($userdata->mainwallet < $post->amount + $post->charge){
                    return response()->json(["statuscode" => "IWB", 'status'=>'Low balance, kindly recharge your wallet.', 'message' => 'Low balance, kindly recharge your wallet.'], 400);
                }
                $url="https://api.paysprint.in/api/v1/service/dmt/kyc/remitter/queryremitter/kyc";
                $geodata = Location::get($post->ip());
                $biodata       =  str_replace("&lt;","<",str_replace("&gt;",">",$post->mybiodata));
                $xml           =  simplexml_load_string($biodata);
                $skeyci        =  (string)$xml->Skey['ci'][0];
                $headerarray   =  json_decode(json_encode((array)$xml), TRUE);
                $key = "2157b06367dc5a0c";
                $iv  = "75ce455527f148fb";
                $ciphertext_raw = openssl_encrypt($post->mybiodata, "AES-128-CBC", $key, $options=OPENSSL_RAW_DATA, $iv);
                $enctoken = base64_encode($ciphertext_raw);
         
                $token = $this->getpeniToken($post->user_id.Carbon::now()->timestamp.rand(999,111));
                $header = array(
                        "Cache-Control: no-cache",
                        "Content-Type: application/json",
                        "Token: ".$token['token'],
                        "Authorisedkey: ".$this->api->optional1
                      );
                $parameters = [
                            "mobile"     => $post->mobile,
                            "lat"     => $geodata->latitude,
                            "long"     => $geodata->longitude,
                            "aadhaar_number"     => $post->adhaarNumber,
                            "data"     => $enctoken,
                        ];
                
                break;    
                
            case 'beniverification':
                $url = $url = $this->api->url.'kyc/beneficiary/registerbeneficiary/fetchbeneficiarybybeneid';
                  $parameters = [
                    "mobile" =>  $post->mobile,
                    "beneid"     => $post->beneid
                ];
                $post['type']  = 'verification' ;
                break ;
                
            case 'getbeneficiary':
                $url = $this->api->url."kyc/beneficiary/registerbeneficiary/fetchbeneficiary";
                $parameters = [
                    "mobile"     => $post->mobile
                ];

                break;

            case "registration":
                $url = $this->api->url."kyc/remitter/registerremitter";
                $parameters = [
                    "bank3_flag" => "no",
                    "mobile"     => $post->mobile,
                    "firstname"  => $post->firstname,
                    "lastname"   => $post->lastname,
                    "otp"        => $post->otp,
                    "stateresp"  => $post->stateresp,
                    "pincode"    =>  $userdata->pincode ?? '110005',
                    "address"    => $post->address ??  $userdata->address,
                    "dob"        => $post->dob ?? date("d-m")."-".rand(1980, 2000),
                    "gst_state"  => "09" ?? $post->gst_state,
                    
                ];
                break;
                
            case "ekycotp":
                $url = "https://api.paysprint.in/api/v1/service/dmt/kyc/remitter/registerremitter";
                $parameters = [
                   
                    "mobile"     => $post->mobile,
                    "otp"        => $post->otp,
                    "stateresp"  => $post->stateresp,
                    "ekyc_id"  => $post->ekyc_id
                   
                    
                ];
                break;    

            case "registrationValidate":
                $parameters['remitterid'] = $post->rid;
                $parameters['otp'] = $post->otp;
                break;
            
            case "addbeneficiary":
                $url = $this->api->url."kyc/beneficiary/registerbeneficiary";
                $parameters = [
                    "mobile"     => $post->mobile,
                    "benename"   => $post->benename,
                    "bankid"     => $post->benebank,
                    "accno"      => $post->beneaccount,
                    "ifsccode"   => $post->beneifsc,
                    "pincode"    => $userdata->pincode ?? '110005',
                    "verified"   => "0",
                    "dob"        => $post->dob ?? date("d-m")."-".rand(1980, 2000),
                    "gst_state"  => "09" ?? $post->gst_state,
                ];
                break;
                
            case 'benedelete':
                $url = $this->api->url."kyc/beneficiary/registerbeneficiary/deletebeneficiary";
                $parameters = [
                    "mobile" => $post->rid,
                    "bene_id"=> $post->bid
                ];
                break;

            case 'beneverify':
                $url = $this->api->url."AIRTEL/verifybeneotp";
                $parameter["custno"] = $post->mobile;
                $parameter["otp"]    = $post->otp;
                $parameter["beneaccno"] = $post->beneaccount;
                $parameter["benemobile"]= $post->benemobile;
                break;
            
            case 'accountverification':
                if ($this->pinCheck($post) == "fail") {
                    //return response()->json(['status' => "Transaction Pin is incorrect"], 400);
                }

                $post['amount'] = 1;
                $provider = Provider::where('recharge1', 'dmt1accverify')->first();
                $post['charge'] = \Myhelper::getCommission($post->amount, $userdata->scheme_id, $provider->id, $userdata->role->slug);
                $post['provider_id'] = $provider->id;
                if($userdata->mainwallet < $post->amount + $post->charge){
                    return response()->json(["statuscode" => "IWB", 'status'=>'Low balance, kindly recharge your wallet.', 'message' => 'Low balance, kindly recharge your wallet.'], 400);
                }
                
                do {
                    $post['txnid'] = \Myhelper::generateUniqueToken();
                } while (Report::where("txnid", "=", $post->txnid)->first() instanceof Report);
                
               // $url = "https://api.verifya2z.com/api/v1/verification/penny_drop_v2";
                $url="https://api.paysprint.in/api/v1/service/dmt/kyc/beneficiary/registerbeneficiary/benenameverify";
                //$url = "https://api.paysprint.in/api/v1/service/dmt/beneficiary/registerbeneficiary/benenameverify";
               
                // $parameters = [
                //     "refid"            =>\Myhelper::generateUniqueToken(),
                //     "account_number"   => $post->beneaccount,
                //     "ifsc_code"        => $post->beneifsc
                // ];
                $parameters = [
                    "mobile"     => $post->mobile,
                    "accno"      => $post->beneaccount,
                    "bankid"     => $post->benebank,
                    "benename"   => $post->benename,
                    "referenceid"   => $post->txnid,
                    "pincode"    => $userdata->pincode ?? '110005',
                    "address"    => $post->address ??  $userdata->address,
                    "dob"        => $post->dob ?? date("d-m")."-".rand(1980, 2000),
                    "gst_state"  => "09" ?? $post->gst_state,
                    "bene_id" => $post->beneid
                ];
               	
                break;
            
            case 'transfer':
    
                if ($this->pinCheck($post) == "fail") {
                   return response()->json(["statuscode" => "ERR", 'message' => "Transaction Pin is incorrect"],400);
                }
                return $this->transfer($post);
                break;
                
            case 'send_otp':
    
               $userdata = User::where('id', $post->user_id)->first();
       
        $url = $url = "https://api.paysprint.in/api/v1/service/dmt/kyc/transact/transact/send_otp";
         do {
            $post['referenceid'] = rand('11111111','99999999');
        } while (Report::where("txnid", "=", $post->referenceid)->first() instanceof Report);
         $geodata = Location::get($post->ip());
        $parameters = array(
            'mobile'=> $post->mobile,
            'referenceid'=>$post->referenceid,
            "pipe"  =>  $post->pipe ?? "bank1",
            'bene_id'  => $post->beneid,
            'txntype'  => $post->mode ?? "IMPS",
            'amount'  => $post->amount,
            "pincode"    =>  $userdata->pincode ?? '110005',
            "address"    => $post->address ??  $userdata->address,
            "dob"        => $post->dob ?? date("d-m")."-".rand(1980, 2000) ,
            "gst_state"  => "09" ?? $post->gst_state,
            "lat"  => $geodata->latitude,
            "long"  => $geodata->longitude,
        );
                break;    
                
            case 'refundotp':
                $report = Report::where('id', $post->id)->first();
                if($report && $report->status != "refund"  && $report->status != "reversed"){
                    //return response()->json(["statuscode" => "ERR", 'message'=>'Money Refund Not Allowed']);
                }

                $url = $this->api->url."kyc/refund/refund/resendotp";
                $parameters['ackno'] = $report->payid;
                $parameters['referenceid'] = $report->txnid; 
                break;

            case 'getrefund':
                $report = Report::where('id', $post->transid)->first();
                if($report && $report->status != "refund"){
                    //return response()->json(["statuscode" => "ERR", 'message'=>'Money Refund Not Allowed']);
                }

                $url = $this->api->url."kyc/refund/refund";
                $parameters['ackno'] = $report->payid;
                $parameters['referenceid'] = $report->txnid;
                $parameters['otp'] = $post->otp;
                break;
            
            default:
                return response()->json(['statuscode'=> 'BPR', 'message'=> 'Bad Parameter Request']);
                break;
        }        

        if($post->type != "accountverification"){
            
            $result = \Myhelper::curl($url, "POST", json_encode($parameters), $header, "yes", 'App\Models\Report', '0');
            
            
        }else{
              $token = $this->getpeniToken($post->user_id.Carbon::now()->timestamp.rand(999,111));
              $header = array(
                "Cache-Control: no-cache",
                "Content-Type: application/json",
                "Token: ".$token['token'],
                "Authorisedkey: ".$this->api->optional1
              ); 
            $result = \Myhelper::curl($url, "POST", json_encode($parameters), $header, "yes", 'App\Models\Report', $post->txnid);
        }
        
        \DB::table('rp_log')->insert([
            'ServiceName' => $post->type,
            'header' => json_encode($header),
            'body' => json_encode($parameters),
            'response' => $result['response'],
            'url' => $url,
            'created_at' => date('Y-m-d H:i:s')
        ]);


        
        if ($result['error'] && $result['response'] == "") {
            return response()->json(["statuscode" => "ERR", 'status'=>'System Error', 'message'=>'Api Not responding']);
            if($post->type == "accountverification"){
                $response = [
                    "message"=>"Success",
                    "statuscode"=>"001",
                    "availlimit"=>"0",
                    "total_limit"=>"0",
                    "used_limit"=>"0",
                    "Data"=>[["fesessionid"=>"CP1801861S131436",
                    "tranid"=>"pending",
                    "rrn"=>"pending",
                    "externalrefno"=>"MH357381218131436",
                    "amount"=>"0",
                    "responsetimestamp"=>"0",
                    "benename"=>"",
                    "messagetext"=>"Success",
                    "code"=>"1",
                    "errorcode"=>"1114",
                    "mahatxnfee"=>"10.00"
                    ]]
                ];

                return $this->output($post, json_encode($response), $userdata);
            }

            return response()->json(["statuscode" => "ERR", 'status'=>'System Error', 'message'=>'System Error'], 400);
        }

        return $this->output($post, $result['response'] , $userdata);
    }
    
    public function updateAck(Request $post){
      $report = Report::where('id',$post->transid)->first(['id']);
      if($report){
         $update = Report::where('id',$post->transid)->update(['payid'=>$post->ackno]); 
         if($update){
           return response()->json(["statuscode" => "TXN", 'message'=>'Success'], 200);  
          } else {
            return response()->json(["statuscode" => "ERR", 'status'=>'System Error', 'message'=>'System Error'], 400);   
         }
      }else{
        return response()->json(["statuscode" => "ERR", 'status'=>'System Error', 'message'=>'System Error'], 400);  
      }
    }

    public function myvalidate($post)
    {
        $validate = "yes";
        switch ($post->type) {
            case 'getdistrict':
                $rules = array('stateid' => 'required|numeric');
            break;

            case 'verification':
            case 'outletotp':
                $rules = array('user_id' => 'required|numeric','mobile' => 'required|numeric');
            break;

            case 'outletregister':
                $rules = array('user_id' => 'required|numeric','merchantPhoneNumber' => 'required|numeric','merchantName' => 'required','merchantState' => 'required','merchantEmail' => 'required', 'userPan' => 'required', 'merchantAddress' => 'required', 'otp' => 'required');
            break;

            case 'mobilechange':
                $rules = array('user_id' => 'required|numeric','mobile' => 'required|numeric','newmobile' => 'required|numeric');
            break;

            case 'mobilechangeverify':
                $rules = array('user_id' => 'required|numeric','mobile' => 'required|numeric','newmobile' => 'required|numeric','otp' => 'required|numeric','newotp' => 'required|numeric');
            break;

            case 'benedelete':
                $rules = array('rid' => 'required|numeric', 'bid' => 'required|numeric');
            break;

            case 'benedeletevalidate':
                $rules = array('transid' => 'required', 'otp' => 'required|numeric');
            break;
            
            case 'registration':
                $rules = array('user_id' => 'required|numeric','mobile' => 'required|numeric|digits:10', 'firstname' => 'required|regex:/^[\pL\s\-]+$/u', 'lastname' => 'required|regex:/^[\pL\s\-]+$/u', 'pincode' => "required|numeric|digits:6");
            break;

            case 'registrationValidate':
                $rules = array('rid' => 'required', 'otp' => 'required|numeric');
            break;

            case 'addbeneficiary':
                $rules = array('user_id' => 'required|numeric','mobile' => 'required|numeric|digits:10', 'benebank' => 'required', 'beneifsc' => "required", 'beneaccount' => "required|numeric|digits_between:6,20", "benename" => "required|regex:/^[\pL\s\-]+$/u");
            break;

            case 'beneverify':
                $rules = array('user_id' => 'required|numeric','mobile' => 'required|numeric|digits:10','beneaccount' => "required|numeric|digits_between:6,20", "benemobile" => 'required|numeric|digits:10', "otp" => 'required|numeric');
            break;

            case 'accountverification':
                $rules = array('user_id' => 'required|numeric','mobile' => 'required|numeric|digits:10', 'benebank' => 'required', 'beneifsc' => "required", 'beneaccount' => "required|numeric|digits_between:6,20");
            break;

            case 'transfer':
                $rules = array('user_id' => 'required|numeric','name' => 'required','mobile' => 'required|numeric|digits:10', 'benebank' => 'required', 'beneifsc' => "required", 'beneaccount' => "required|numeric|digits_between:6,20","benename" => "required",'amount' => 'required|numeric|min:10|max:25000');
            break;
            
             case 'send_otp':
                $rules = array('user_id' => 'required|numeric','name' => 'required','mobile' => 'required|numeric|digits:10', 'benebank' => 'required', 'beneifsc' => "required", 'beneaccount' => "required|numeric|digits_between:6,20","benename" => "required",'amount' => 'required|numeric|min:10|max:25000');
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

    public function transfer($post)
    {   
        $userdata = User::where('id', $post->user_id)->first();
        $agents = \DB::table('dmt_agents')->where('user_id',\Auth::id())->first();
        if(empty($agents))
        {
            return response()->json(['statuscode' => 'ERR', 'message' => "Agent not registered"],400);
        }
        //dd($post->transactionvia);
        if($post->transactionvia == 'dmt'){
            $url = $url = $this->api->url."transact/transact/index";
            $parameters = array(
                'merchant_code' => $agents->merchant_code,
                'mobile'=> $post->mobile,
                
                'bene_id'  => $post->beneid,
                'txntype'  => $post->mode ?? "IMPS",
                'otp' => $post->otp,
                'stateresp' => $post->stateresp
                
            );
    
            $amount = $post->amount;
            // for ($i=1; $i < 6; $i++) { 
            //     if(5000*($i-1) <= $amount  && $amount <= 5000*$i){
            //         if($amount == 5000*$i){
            //             $n = $i;
            //         }else{
            //             $n = $i-1;
            //             $x = $amount - $n*5000;
            //         }
            //         break;
            //     }
            // }
            $n=1;
            $amounts = array_fill(0,$n,$post->amount);
            if(isset($x)){
                array_push($amounts , $x);
            }
   // dd($amounts);
            foreach ($amounts as $amount) {
                $outputs['statuscode'] = "TXN";
                $post['amount'] = $amount;
                $user = User::where('id', $post->user_id)->first();
                $post['charge'] = $this->getCharge($post->amount);
                if($user->mainwallet < $post->amount + $post->charge){
                    $outputs['data'][] = array(
                        'amount' => $amount,
                        'status' => 'TXF',
                        'data'   => [
                            "statuscode" => "TXF",
                            "status" => "Insufficient Wallet Balance",
                            "message" => "Insufficient Wallet Balance",
                        ]
                    );
                }else{
                    $post['amount'] = $amount;
                    
                    do {
                        $post['txnid'] = $this->transcode().rand(1111111111, 9999999999);
                    } while (Report::where("txnid", "=", $post->txnid)->first() instanceof Report);
    
                    if($post->amount >= 100 && $post->amount <= 1000){
                        $provider = Provider::where('recharge1', 'dmt1')->first();
                    }elseif($amount>1000 && $amount<=2000){
                        $provider = Provider::where('recharge1', 'dmt2')->first();
                    }elseif($amount>2000 && $amount<=3000){
                        $provider = Provider::where('recharge1', 'dmt3')->first();
                    }elseif($amount>3000 && $amount<=4000){
                        $provider = Provider::where('recharge1', 'dmt4')->first();
                    }else{
                        $provider = Provider::where('recharge1', 'dmt5')->first();
                    }
                    
                    $post['provider_id'] = $provider->id;
                    $post['service'] = $provider->type;
                    $insert = [
                        'api_id' => $this->api->id,
                        'provider_id' => $post->provider_id,
                        'option1' => $post->name,
                        'mobile' => $post->mobile,
                        'number' => $post->beneaccount,
                        'option2' => $post->benename,
                        'option3' => $post->benebank,
                        'option4' => $post->beneifsc,
                        'txnid' => $post->txnid,
                        'amount' => $post->amount,
                        'charge' => $post->charge,
                        'status' => 'success',
                        'user_id' => $user->id,
                        'credit_by' => $user->id,
                        'product' => 'dmt',
                        'balance' => $user->mainwallet,
                        'description' => $post->benemobile,
                        'trans_type' => 'debit'
                    ];
                    
                    $previousrecharge = Report::where('number', $post->beneaccount)->where('amount', $post->amount)->where('provider_id', $post->provider_id)->whereBetween('created_at', [Carbon::now()->subSeconds(1)->format('Y-m-d H:i:s'), Carbon::now()->addSeconds(1)->format('Y-m-d H:i:s')])->count();
                    if($previousrecharge == 0){
                        $transaction = User::where('id', $user->id)->decrement('mainwallet', $post->amount + $post->charge);
                        if(!$transaction){
                            $outputs['data'][] = array(
                                'amount' => $amount,
                                'status' => 'TXF',
                                'data' => [
                                    "statuscode" => "TXF",
                                    "status" => "Transaction Failed",
                                ]
                            );
                        }else{
                            
                            //try {
                                $report = Report::create($insert);
                                $post['reportid'] = $report->id;
                                switch ($provider->api->code) {
                                    case 'pdmt':
                                        
                                        $parameters['amount'] = $post->amount;
                                        $parameters['referenceid']= $post->txnid;
                                        
                                        $token = $this->getToken($post->user_id.Carbon::now()->timestamp);
                                        $header = array(
                                            "Cache-Control: no-cache",
                                            "Content-Type: application/json",
                                            "Token: ".$token['token'],
                                            "Authorisedkey: ".$this->api->optional1
                                        );
                
                                        if (env('APP_ENV') == "server") {
                                            $result = \Myhelper::curl($url, "POST", json_encode($parameters), $header, "yes", 'App\Models\Report', $post->txnid);
                                        }else{
                                            $result = [
                                                'error' => true,
                                                'response' => '' 
                                            ];
                                        }
                                        break;
                                    case 'm2payout':
                                        
                                        $url = $provider->api->url.'bank/payout';
                                        
                                        $parameter = [
                                            "token" => $provider->api->username,
                                            "paymode" => "IMPS",
                                            "ip" => "195.250.21.239",
                                            "amount" => $post->amount,
                                            "name" => $post->benename,
                                            "apitxnid" => $post->txnid,
                                            "callback" => "https://login.ujjwalpayworld.in/api/callbacks/payouts/m2money",
                                            "account" => $post->beneaccount,
                                            "ifsc" => $post->beneifsc,
                                            "bank" => $post->benebank
                                        ];
                                         $header = array(
                                            "Cache-Control: no-cache",
                                            "Content-Type: application/json"
                                        );
                                        $result = \Myhelper::curl($url,'POST', json_encode($parameter), $header, "yes", "App\Model\Report", $post->txnid);
                                        
                                        \DB::table('rp_log')->insert([
                                            'ServiceName' => $post->type,
                                            'header' => json_encode($header),
                                            'body' => json_encode($parameters),
                                            'response' => $result['response'],
                                            'url' => $url,
                                            'created_at' => date('Y-m-d H:i:s')
                                        ]);
                                        $response = json_decode($result['response']);
                                        
                                        if(isset($response->status) && $response->status == "TXN")
                                        {
                                            $dataarr = ['statuscode'=> 'TXN', 'status'=> 'Transaction Success','message'=> "Transaction Success", 'rrn' => $response->rrn, 'payid' => $post->reportid];
                                            
                                            $outputs['data'][] = array(
                                                'amount' => $post->amount,
                                                'status' => 'TXN',
                                                'data' => $dataarr
                                            );
                                             Report::where('id', $post->reportid)->update([
                                                'status'=> 'success',
                                                'refno' => (isset($response->rrn))? $response->rrn : 'failed'
                                            ]);
                                        }
                                        else if(isset($response->status) && $response->status == "TUP"){
                                           
                                            $dataarr = ['statuscode'=> 'TXN', 'status'=> 'Transaction Under Process','message'=> "Transaction Under Process", 'rrn' => 'pending', 'payid' => $post->reportid];
                                            
                                            $outputs['data'][] = array(
                                                'amount' => $post->amount,
                                                'status' => 'TXN',
                                                'data' => $dataarr
                                            );
                                        }else if(isset($response->status) && ($response->status == "ERR")){
                                           
                                            User::where('id', $user->id)->increment('mainwallet', $report->charge + $report->amount);
                                            if($response->message == 'Low balance, kindly recharge your wallet')
                                            {
                                                $response->message = 'Something went wrong';
                                            }
                                            Report::where('id', $post->reportid)->update([
                                                'status'=> 'failed',
                                                'refno' => (isset($response->message))? $response->message : 'failed'
                                            ]);
                                  
                                            $outputs['data'][] = array(
                                                'amount' => $post->amount,
                                                'status' => 'TXF',
                                                'data' => [
                                                    "statuscode" => "TXF",
                                                    "status" => $response->message??"Something went wrong",
                                                    "message" => $response->message??"Something went wrong",
                                                ]
                                            );
                                        }else{ 
                                            $dataarr = ['statuscode'=> 'TXN', 'status'=> 'Transaction Under Process','message'=> "Transaction Under Process", 'rrn' => 'pending', 'payid' => $post->reportid];
                                            
                                            $outputs['data'][] = array(
                                                'amount' => $post->amount,
                                                'status' => 'TXN',
                                                'data' => $dataarr
                                            );
                                        }
                                        sleep(1);
                                        return response()->json($outputs, 200);
                                    break; 
                                }
                                
                                
                                \DB::table('rp_log')->insert([
                                    'ServiceName' => $post->type,
                                    'header' => json_encode($header),
                                    'body' => json_encode($parameters),
                                    'response' => $result['response'],
                                    'url' => $url,
                                    'created_at' => date('Y-m-d H:i:s')
                                ]);
        
                                if(env('APP_ENV') == "local" || $result['error'] || $result['response'] == ''){
                                    $result['response'] = json_encode([
                                        "message"=>"Pending",
                                        "statuscode"=>"001",
                                        "availlimit"=>"0",
                                        "total_limit"=>"0",
                                        "used_limit"=>"0",
                                        "Data"=>[
                                            ["fesessionid"=>"CP1801861S131436",
                                                "tranid"=>"pending",
                                                "rrn"=>"pending",
                                                "externalrefno"=>"MH357381218131436",
                                                "amount"=>"0",
                                                "responsetimestamp"=>"0",
                                                "benename"=>"",
                                                "messagetext"=>"Success",
                                                "code"=>"1",
                                                "errorcode"=>"1114",
                                                "mahatxnfee"=>"10.00"
                                            ]
                                        ]
                                    ]);
                                }
        
                                $outputs['data'][] = array(
                                    'amount' => $amount,
                                    'status' => 'TXN',
                                    'data' => $this->output($post, $result['response'], $user)
                                );
                            // } catch (\Exception $e) {
                            //     if(isset($report)){
                            //         $charge = \Myhelper::getCommission($post->amount, $user->scheme_id, $post->provider_id, $user->role->slug);
                            //         $post['gst'] = 0;
                            //         User::where('id', $post->user_id)->increment('mainwallet', $report->charge - $post->gst - $charge);
                            //         Report::where('id', $post->reportid)->update([
                            //             'status'=> "pending",
                            //             'payid' => "Pedning" ,
                            //             'refno' => "Pedning",
                            //             'remark'=> "Pedning",
                            //             'gst'   => $post->gst,
                            //             'profit'=> $report->charge - $post->gst - $charge
                            //         ]);
                            //         \Myhelper::commission($report);
                            //         $outputs['data'][] = array(
                            //             'amount' => $amount,
                            //             'status' => 'TXN',
                            //             'data' => ['statuscode'=> 'TUP', 'status'=> 'Transaction Under Process','message'=> "Transaction Under Process", 'rrn' => "pedning", 'payid' => $post->reportid]
                            //         );
                            //     }else{
                            //         User::where('id', $user->id)->increment('mainwallet', $post->amount + $post->charge);
                            //         $outputs['data'][] = array(
                            //             'amount' => $amount,
                            //             'status' => 'TXF',
                            //             'data' => [
                            //                 "statuscode" => "TXF",
                            //                 "status" => "Same Transaction Repeat2",
                            //                 "message" => "Same Transaction Repeat",
                            //             ]
                            //         );
                            //     }
                            // }
                        }
                    }else{
                        $outputs['data'][] = array(
                            'amount' => $amount,
                            'status' => 'TXF',
                            'data' => [
                                "statuscode" => "TXF",
                                "status" => "Same Transaction Repeat1",
                                "message" => "Same Transaction Repeat",
                            ]
                        );
                    }
                }
                sleep(1);
            }
        }else{  
                $outputs['statuscode'] = "TXN";
                $user = User::where('id', $post->user_id)->first();
                $post['charge'] = 0;
                
                if($post->amount >= 10 && $post->amount <= 1000){
                    $provider = Provider::where('recharge1', 'xdmt1')->first();
                }else if($post->amount > 1001 && $post->amount <= 10000){
                    $provider = Provider::where('recharge1', 'xdmt2')->first();
                }elseif($post->amount > 10001 && $post->amount <= 25000){
                    $provider = Provider::where('recharge1', 'xdmt3')->first();
                }elseif($post->amount > 25001 && $post->amount <= 50000){
                    $provider = Provider::where('recharge1', 'xdmt4')->first();
                }else{
                    $provider = Provider::where('recharge1', 'xdmt5')->first();
                }
                
                 $post['provider_id'] = $provider->id;
                 $post['charge'] = \Myhelper::getCommission($post->amount, $user->scheme_id, $post->provider_id, $user->role->slug);
                
                
                if($user->mainwallet < $post->amount + $post->charge){
                    $outputs['data'][] = array(
                        'amount' => $post->amount,
                        'status' => 'TXF',
                        'data'   => [
                            "statuscode" => "TXF",
                            "status" => "Insufficient Wallet Balance",
                            "message" => "Insufficient Wallet Balance",
                        ]
                    );
                }else{
                    
                    
                    do {
                        $post['txnid'] = $this->transcode().rand(1111111111, 9999999999);
                    } while (Report::where("txnid", "=", $post->txnid)->first() instanceof Report);
    
                    
                    
                    $post['service'] = $provider->type;
                    $insert = [
                        'api_id' => $provider->api->id,
                        'provider_id' => $post->provider_id,
                        'option1' => $post->benename,
                        'mobile' => $post->mobile,
                        'number' => $post->beneaccount,
                        'option2' => $post->benename,
                        'option3' => $post->benebank,
                        'option4' => $post->beneifsc,
                        'txnid' => $post->txnid,
                        'amount' => $post->amount,
                        'charge' => $post->charge,
                        'status' => 'pending',
                        'user_id' => $user->id,
                        'credit_by' => $user->id,
                        'product' => 'dmt',
                        'balance' => $user->mainwallet,
                        'description' => $post->benemobile, 
                        'trans_type' => 'debit'
                    ];
                    
                    $previousrecharge = Report::where('number', $post->beneaccount)->where('amount', $post->amount)->where('provider_id', $post->provider_id)->whereBetween('created_at', [Carbon::now()->subMinutes(2)->format('Y-m-d H:i:s'), Carbon::now()->format('Y-m-d H:i:s')])->count();
                 
                    if($previousrecharge == 0){
                       $transaction = User::where('id', $user->id)->decrement('mainwallet', $post->amount + $post->charge);
                    
                        if(!$transaction){
                            $outputs['data'][] = array(
                                'amount' => $post->amount,
                                'status' => 'TXF',
                                'data' => [
                                    "statuscode" => "TXF",
                                    "status" => "Transaction Failed",
                                ]
                            );
                        }else{
                           
                                $report = Report::create($insert);
                                
                                $post['reportid'] = $report->id;
                               // dd($provider->api->code);
                                switch ($provider->api->code) {
                                    case 'anshpaydmt':
                                        $url = $provider->api->url.'PayoutRequest';
                                        
                                        $parameter = [
                                            "Account" => $post->beneaccount,
                                            "IFSC" => $post->beneifsc,
                                            "Amount"   => $post->amount,
                                            "UniqueId" => $post->txnid   
                                        ];
                        
                                        $header = array(
                                            "Cache-Control: no-cache",
                                            "Content-Type: application/json",
                                            "ClientId:".$provider->api->password,
                                            "ClientSecret:".$provider->api->optional1
                                        );
                                        
                                        $result = \Myhelper::curl($url,'POST', json_encode($parameter), $header, "yes", "App\Model\Report", $post->txnid);
                 
                                        
                                    break;
                                    
                                    case 'm2payout':
                                        
                                        $url = $provider->api->url.'bank/payout';
                                        
                                        $parameter = [
                                            "token" => $provider->api->username,
                                            "paymode" => "IMPS",
                                            "ip" => "195.250.21.239",
                                            "amount" => $post->amount,
                                            "name" => $post->benename,
                                            "apitxnid" => $post->txnid,
                                            "callback" => "https://login.ujjwalpayworld.in/api/callbacks/payouts/m2money",
                                            "account" => $post->beneaccount,
                                            "ifsc" => $post->beneifsc,
                                            "bank" => $post->benebank
                                        ];
                                         $header = array(
                                            "Cache-Control: no-cache",
                                            "Content-Type: application/json"
                                        );
                                        $result = \Myhelper::curl($url,'POST', json_encode($parameter), $header, "yes", "App\Model\Report", $post->txnid);
                                        $response = json_decode($result['response']);
                                        if(isset($response->status) && $response->status == "TXN")
                                        {
                                            $dataarr = ['statuscode'=> 'TXN', 'status'=> 'Transaction Success','message'=> "Transaction Success", 'rrn' => $response->rrn, 'payid' => $post->reportid];
                                            
                                            $outputs['data'][] = array(
                                                'amount' => $post->amount,
                                                'status' => 'TXN',
                                                'data' => $dataarr
                                            );
                                             Report::where('id', $post->reportid)->update([
                                                'status'=> 'success',
                                                'refno' => (isset($response->rrn))? $response->rrn : 'failed'
                                            ]);
                                        }
                                        else if(isset($response->status) && $response->status == "TUP"){
                                           
                                            $dataarr = ['statuscode'=> 'TXN', 'status'=> 'Transaction Under Process','message'=> "Transaction Under Process", 'rrn' => 'pending', 'payid' => $post->reportid];
                                            
                                            $outputs['data'][] = array(
                                                'amount' => $post->amount,
                                                'status' => 'TXN',
                                                'data' => $dataarr
                                            );
                                        }else if(isset($response->status) && ($response->status == "ERR")){
                                           
                                            User::where('id', $user->id)->increment('mainwallet', $report->charge + $report->amount);
                                            if($response->message == 'Low balance, kindly recharge your wallet')
                                            {
                                                $response->message = 'Something went wrong';
                                            }
                                            Report::where('id', $post->reportid)->update([
                                                'status'=> 'failed',
                                                'refno' => (isset($response->message))? $response->message : 'failed'
                                            ]);
                                  
                                            $outputs['data'][] = array(
                                                'amount' => $post->amount,
                                                'status' => 'TXF',
                                                'data' => [
                                                    "statuscode" => "TXF",
                                                    "status" => $response->message??"Something went wrong",
                                                    "message" => $response->message??"Something went wrong",
                                                ]
                                            );
                                        }else{ 
                                            $dataarr = ['statuscode'=> 'TXN', 'status'=> 'Transaction Under Process','message'=> "Transaction Under Process", 'rrn' => 'pending', 'payid' => $post->reportid];
                                            
                                            $outputs['data'][] = array(
                                                'amount' => $post->amount,
                                                'status' => 'TXN',
                                                'data' => $dataarr
                                            );
                                        }
                                        sleep(1);
                                        return response()->json($outputs, 200);
                                    break; 
                                    
                                    case 'paysprintpaybank':
                                      $url = $provider->api->url;
                                        $datass = [
                                            "apiId" => '30012',
                                            "bankId" => '5',
                                            "acctNumber" => '409002136531',
                                            "fromDate" => '25-05-2024',
                                            "toDate" => '29-05-2024',
                                            "transType" => 'D'
                                        ];
                                        $enccData = \Myhelper::encData($datass);
                                        $request = [
                                            "body" => [
                                                "payload"   => $enccData['payload'],
                                                "key"       => $enccData['key'],
                                                "partnerId" => $provider->api->username,
                                                "clientid"  => $provider->api->password
                                            ]
                                        ];
                    
                                        $header = [
                                            'accept: application/json',
                                            'client-id: ' . $provider->api->password,
                                            'Content-Type: application/json',
                                            'key: ' . $enccData['key'],
                                            'partnerId: ' . $provider->api->username
                                        ];
    
                                       $result = \Myhelper::curl2($url, 'POST', json_encode($request), $header, 'yes', 'PPBankPayout', $post->account, 25002);
                                       $response = json_decode($result['response']);
                                       $decData = \Myhelper::decData($result['key'],$response->body);
                                       $decodedResponse = json_decode($decData);
                                   
                                   
                                 print_r(['EncryptedRequest'=>json_encode($datass),'URL'=>$url,'Header'=>$header,'EncryptedResponse'=>$result['response'],'decryptedResponse'=>$decodedResponse]);
                   
                                  dd($decodedResponse);
                                        
                                    break;        
                                    
                                    case 'cipherpay':
                                       // $url = $provider->api->url;
         
                                        $reqData = array(
                                              "mode"=>"IMPS", //strtoupper($post->mode),
                                              "remarks"=> "Vendor Payout",
                                              "amount"=> $post->amount,
                                              "type"=> "vendor",
                                              "bene_name"=> $post->name??$user->name,
                                              "bene_mobile"=> $user->mobile,//$user->mobile,
                                              "bene_email"=> $user->email,//$user->email,
                                              "bene_acc"=> $post->beneaccount,//$post->account,
                                              "bene_ifsc"=> $post->beneifsc,//strtoupper($post->ifsc),
                                              "bene_acc_type"=> "Saving",
                                              "refid"=> $post->txnid,
                                              "bene_bank_name"=> $post->benebank//$post->bank
                                            
                                        );
                                        $request = array(
                                            "method" => "POST",
                                            "url" => "pay/singlepayout",
                                            "parameter" => $reqData
                                        );
                                        $chypierpay = new Chypierpay();
                                        $res = $chypierpay->hit($request);
                                        $output = json_encode($res);
                                        $response = json_decode($output);
                                        
                                        $log = \DB::table('payoutlogs')->insert(['request'=>json_encode($reqData),"response"=>$output??"","txnId"=>$post->txnid,"user_id"=>$user->id,"service"=>"payout"]);
                               
                                            
                                        if(isset($response->statuscode) && $response->statuscode == "200"){
                                           
                                            $dataarr = ['statuscode'=> 'TXN', 'status'=> 'Transaction Under Process','message'=> "Transaction Under Process", 'rrn' => 'pending', 'payid' => $post->reportid];
                                            
                                            $outputs['data'][] = array(
                                                'amount' => $post->amount,
                                                'status' => 'TXN',
                                                'data' => $dataarr
                                            );
                                        }else if(isset($response->statuscode) && ($response->statuscode == "422" || $response->statuscode == "203")){
                                           
                                            User::where('id', $user->id)->increment('mainwallet', $report->charge + $report->amount);
                                            Report::where('id', $post->reportid)->update([
                                                'status'=> 'failed',
                                                'refno' => (isset($response->message))? $response->message : 'failed'
                                            ]);
                                  
                                            $outputs['data'][] = array(
                                                'amount' => $post->amount,
                                                'status' => 'TXF',
                                                'data' => [
                                                    "statuscode" => "TXF",
                                                    "status" => $response->message??"Something went wrong",
                                                    "message" => $response->message??"Something went wrong",
                                                ]
                                            );
                                        }else{ 
                                            $dataarr = ['statuscode'=> 'TXN', 'status'=> 'Transaction Under Process','message'=> "Transaction Under Process", 'rrn' => 'pending', 'payid' => $post->reportid];
                                            
                                            $outputs['data'][] = array(
                                                'amount' => $post->amount,
                                                'status' => 'TXN',
                                                'data' => $dataarr
                                            );
                                        }
                                        
                                        //message handling
                                        if(isset($response->message) && ($response->message == "Service Provider Error" || $response->message =="Allowed MICR time is invalid!")){
                                            if(isset($response->message) && $response->message =="Allowed MICR time is invalid!"){
                                                $response="Service not allowed 11.30 PM TO 6 AM";
                                            }
                                            
                                            User::where('id', $user->id)->increment('mainwallet', $report->charge + $report->amount);
                                            Report::where('id', $post->reportid)->update([
                                                'status'=> 'failed',
                                                'refno' => (isset($response->message))? $response->message : 'failed'
                                            ]);
                                            return ['statuscode'=> 'TXF', 'status'=> $response->message??"Something went wrong" , 'message'=> $response->message??"Something went wrong", "rrn" => (isset($response->message))? $response->message : 'failed', 'payid' => $post->reportid];
      
                                           
                                        }
                                        
                                        sleep(1);
                                        return response()->json($outputs, 200);
                                    break;
                                    
                                }    
    
        
                                if(isset($result) && (env('APP_ENV') == "local" || $result['error'] || $result['response'] == '')){
                                    $result['response'] = json_encode([
                                        "message"=>"Pending",
                                        "statuscode"=>"001",
                                        "availlimit"=>"0",
                                        "total_limit"=>"0",
                                        "used_limit"=>"0",
                                        "Data"=>[
                                            ["fesessionid"=>"CP1801861S131436",
                                                "tranid"=>"pending",
                                                "rrn"=>"pending",
                                                "externalrefno"=>"MH357381218131436",
                                                "amount"=>"0",
                                                "responsetimestamp"=>"0",
                                                "benename"=>"",
                                                "messagetext"=>"Success",
                                                "code"=>"1",
                                                "errorcode"=>"1114",
                                                "mahatxnfee"=>"10.00"
                                            ]
                                        ]
                                    ]);
                                }
        
                                $outputs['data'][] = array(
                                    'amount' => $post->amount,
                                    'status' => 'TXN',
                                    'data' => $this->output($post, $result['response'], $user)
                                );
    
                        }
                    }else{
                        $outputs['data'][] = array(
                            'amount' => $post->amount,
                            'status' => 'TXF',
                            'data' => [
                                "statuscode" => "TXF",
                                "status" => "Same Transaction Repeat",
                                "message" => "Same Transaction allowed after 5 mins",
                            ]
                        );
                    }
                    
                }
                sleep(1);
        
        }
        return response()->json($outputs, 200);
    }
    // public function transfer($post)
    // {   
        

    //     $userdata = User::where('id', $post->user_id)->first();
       
    //     $url = $url = $this->api->url."kyc/transact/transact";
    //     $parameters = array(
           
    //         'mobile'=> $post->mobile,
    //         'otp'=> $post->otp,
    //         "pipe"  =>  $post->pipe ?? "bank1",
    //         'bene_id'  => $post->beneid,
    //         'txntype'  => $post->mode ?? "IMPS",
    //         "pincode"    =>  $userdata->pincode ?? '110005',
    //         "address"    => $post->address ??  $userdata->address,
    //         "dob"        => $post->dob ?? date("d-m")."-".rand(1980, 2000) ,
    //         "gst_state"  => "09" ?? $post->gst_state,
    //         "stateresp"  =>$post->stateresp,
    //     );

    //     $amount = $post->amount;
    //     for ($i=1; $i < 6; $i++) { 
    //         if(5000*($i-1) <= $amount  && $amount <= 5000*$i){
    //             if($amount == 5000*$i){
    //                 $n = $i;
    //             }else{
    //                 $n = $i-1;
    //                 $x = $amount - $n*5000;
    //             }
    //             break;
    //         }
    //     }

    //     $amounts = array_fill(0,$n,5000);
    //     if(isset($x)){
    //         array_push($amounts , $x);
    //     }

    //     do {
    //         $post['referenceid'] = rand('11111111','99999999');
    //     } while (Report::where("txnid", "=", $post->referenceid)->first() instanceof Report);

    //     foreach ($amounts as $amount) {
    //         $outputs['statuscode'] = "TXN";
    //         $post['amount'] = $amount;
    //         $user = User::where('id', $post->user_id)->first();
    //         $post['charge'] = $this->getCharge($post->amount);
    //         if($user->mainwallet < $post->amount + $post->charge){
    //             $outputs['data'][] = array(
    //                 'amount' => $amount,
    //                 'status' => 'TXF',
    //                 'data'   => [
    //                     "statuscode" => "TXF",
    //                     "status" => "Insufficient Wallet Balance",
    //                     "message" => "Insufficient Wallet Balance",
    //                 ]
    //             );
    //         }else{
    //             $post['amount'] = $amount;
                
    //             do {
    //                 $post['txnid'] = \Myhelper::generateUniqueToken();
    //             } while (Report::where("txnid", "=", $post->txnid)->first() instanceof Report);

    //             if($post->amount >= 100 && $post->amount <= 1000){
    //                 $provider = Provider::where('recharge1', 'dmt1')->first();
    //             }elseif($amount > 1000 && $amount <= 2000){
    //                 $provider = Provider::where('recharge1', 'dmt2')->first();
    //             }elseif($amount > 2000 && $amount <= 3000){
    //                 $provider = Provider::where('recharge1', 'dmt3')->first();
    //             }elseif($amount > 3000 && $amount <= 4000){
    //                 $provider = Provider::where('recharge1', 'dmt4')->first();
    //             }else{
    //                 $provider = Provider::where('recharge1', 'dmt5')->first();
    //             }
                
    //             $post['provider_id'] = $provider->id;
    //             $post['service'] = $provider->type;
    //             $insert = [
    //                 'api_id' => $this->api->id,
    //                 'provider_id' => $post->provider_id,
    //                 'option1' => $post->name,
    //                 'txnid' => $post->referenceid,
    //                 'mobile' => $post->mobile,
    //                 'number' => $post->beneaccount,
    //                 'option2' => $post->benename,
    //                 'option3' => $post->benebank,
    //                 'option4' => $post->beneifsc,
    //                 'txnid' => $post->txnid,
    //                 'amount' => $post->amount,
    //                 'charge' => $post->charge,
    //                 'status' => 'success',
    //                 'user_id' => $user->id,
    //                 'credit_by' => $user->id,
    //                 'product' => 'dmt',
    //                 'balance' => $user->mainwallet,
    //                 'description' => $post->benemobile,
    //                 'trans_type' => 'debit'
    //             ];
                
    //             $previousrecharge = Report::where('number', $post->beneaccount)->where('amount', $post->amount)->where('provider_id', $post->provider_id)->whereBetween('created_at', [Carbon::now()->subSeconds(1)->format('Y-m-d H:i:s'), Carbon::now()->format('Y-m-d H:i:s')])->count();
    //             if($previousrecharge == 0){
    //                 $transaction = User::where('id', $user->id)->decrement('mainwallet', $post->amount + $post->charge);
    //                 if(!$transaction){
    //                     $outputs['data'][] = array(
    //                         'amount' => $amount,
    //                         'status' => 'TXF',
    //                         'data' => [
    //                             "statuscode" => "TXF",
    //                             "status" => "Transaction Failed",
    //                         ]
    //                     );
    //                 }else{
                        
    //                     //try {
    //                         $report = Report::create($insert);
    //                         $post['reportid'] = $report->id;
    //                         $parameters['amount'] = $post->amount;
    //                         $parameters['referenceid']= $post->txnid;
                            
                            
    //                         $token = $this->getToken($post->user_id.Carbon::now()->timestamp);
    //                         $header = array(
    //                             "Cache-Control: no-cache",
    //                             "Content-Type: application/json",
    //                             "Token: ".$token['token'],
    //                             "Authorisedkey: ".$this->api->optional1
    //                         );
    

    //                         $result = \Myhelper::curl($url, "POST", json_encode($parameters), $header, "yes", 'App\Models\Report', $post->txnid);

                            
    //                         \DB::table('rp_log')->insert([
    //                             'ServiceName' => $post->type,
    //                             'header' => json_encode($header),
    //                             'body' => json_encode($parameters),
    //                             'response' => $result['response'],
    //                             'url' => $url,
    //                             'created_at' => date('Y-m-d H:i:s')
    //                         ]);
    
    //                         if(env('APP_ENV') == "local" || $result['error'] || $result['response'] == ''){
    //                             $result['response'] = json_encode([
    //                                 "message"=>"Pending",
    //                                 "statuscode"=>"001",
    //                                 "availlimit"=>"0",
    //                                 "total_limit"=>"0",
    //                                 "used_limit"=>"0",
    //                                 "Data"=>[
    //                                     ["fesessionid"=>"CP1801861S131436",
    //                                         "tranid"=>"pending",
    //                                         "rrn"=>"pending",
    //                                         "externalrefno"=>"MH357381218131436",
    //                                         "amount"=>"0",
    //                                         "responsetimestamp"=>"0",
    //                                         "benename"=>"",
    //                                         "messagetext"=>"Success",
    //                                         "code"=>"1",
    //                                         "errorcode"=>"1114",
    //                                         "mahatxnfee"=>"10.00"
    //                                     ]
    //                                 ]
    //                             ]);
    //                         }
    
    //                         $outputs['data'][] = array(
    //                             'amount' => $amount,
    //                             'status' => 'TXN',
    //                             'data' => $this->output($post, $result['response'], $user)
    //                         );
                      
    //                 }
    //             }else{
    //                 $outputs['data'][] = array(
    //                     'amount' => $amount,
    //                     'status' => 'TXF',
    //                     'data' => [
    //                         "statuscode" => "TXF",
    //                         "status" => "Same Transaction Repeat1",
    //                         "message" => "Same Transaction Repeat",
    //                     ]
    //                 );
    //             }
    //         }
    //         sleep(1);
    //     }
    //     return response()->json($outputs, 200);
    // }
    
    //  public function send_otp($post)
    // {   
        
    //     $userdata = User::where('id', $post->user_id)->first();
       
    //     $url = $url = "https://api.paysprint.in/api/v1/service/dmt/kyc/transact/transact/send_otp";
    //      do {
    //         $post['referenceid'] = rand('11111111','99999999');
    //     } while (Report::where("referenceid", "=", $post->referenceid)->first() instanceof Report);
    //      $geodata = Location::get($post->ip());
    //     $parameters = array(
    //         'mobile'=> $post->mobile,
    //         'referenceid'=>$post->referenceid,
    //         "pipe"  =>  $post->pipe ?? "bank1",
    //         'bene_id'  => $post->beneid,
    //         'txntype'  => $post->mode ?? "IMPS",
    //         'amount'  => $post->amount,
    //         "pincode"    =>  $userdata->pincode ?? '110005',
    //         "address"    => $post->address ??  $userdata->address,
    //         "dob"        => $post->dob ?? date("d-m")."-".rand(1980, 2000) ,
    //         "gst_state"  => "09" ?? $post->gst_state,
    //         "lat"  => $geodata->latitude,
    //         "long"  => $geodata->longitude,
    //     );

    //     $token = $this->getToken($post->user_id.Carbon::now()->timestamp);
    //                         $header = array(
    //                             "Cache-Control: no-cache",
    //                             "Content-Type: application/json",
    //                             "Token: ".$token['token']
                               
    //                         );
    

    //                         $result = \Myhelper::curl($url, "POST", json_encode($parameters), $header, "yes", 'App\Models\Report', $post->referenceid);
                           
                            
    //                         \DB::table('rp_log')->insert([
    //                             'ServiceName' => 'Transfer-Send-Otp',
    //                             'header' => json_encode($header),
    //                             'body' => json_encode($parameters),
    //                             'response' => $result['response'],
    //                             'url' => $url,
    //                             'created_at' => date('Y-m-d H:i:s')
    //                         ]);
       
    //     $outputs['data'][] = array(
    //                             'amount' => $post->amount,
    //                             'status' => 'TXN',
    //                             'data' => $this->output($post, $result['response'], $userdata)
    //                         );
    //     return response()->json($outputs, 200);
    // }

    public function output($post, $response, $userdata)
    {
        $response = json_decode($response);
        switch ($post->type) {
            case 'verification':
                if(isset($response->response_code) && $response->response_code == 1){
                
                    $post['type'] = "getbeneficiary";
                    $benedata = $this->payment($post);
                    return response()->json(['statuscode'=> 'TXN', 'message'=> 'Transaction Successfull','data'=> $response->data, "benedata" => $benedata]);
                }elseif(isset($response->response_code) && $response->response_code == 0){
                    return response()->json(['statuscode'=> 'RNF', 'message'=> 'Transaction Successfull', "data" => $response]);
                }
                elseif(isset($response->response_code) && $response->response_code == 2){
                    return response()->json(['statuscode'=> 'EKYC']);
                }
                elseif(isset($response->response_code) && $response->response_code == 3){
                    return response()->json(['statuscode'=> 'EKYCOTP',"data" => $response->data]);
                }
                else{
                    return response()->json(['statuscode'=> 'ERR', 'message'=> $response->message]);
                }
                break;
            case 'KYC':
                if(isset($response->response_code) && $response->response_code == "1"){
                    $balance = User::where('id', $userdata->id)->first(['mainwallet']);
                    
                    
                    $insert = [
                        'api_id' => $this->api->id,
                        'provider_id' => $post->provider_id,
                        'option1' => '',
                        'mobile' => $post->mobile,
                        'number' => $post->adhaarNumber,
                        'option2' => isset($response->benename) ? $response->benename : $post->benename,
                        'option3' => '',
                        'option4' => '',
                        'txnid' => $post->txnid,
                        'refno' => isset($response->refid) ? $response->refid : "none",
                        'amount' => $post->amount,
                        'charge' => $post->charge,
                        'remark' => "Money Transfer",
                        'status' => 'success',
                        'user_id' => $userdata->id,
                        'credit_by' => $userdata->id,
                        'product' => 'dmtkyc',
                        'balance' => $balance->mainwallet,
                        'description' => '',
                        'trans_type'  => 'debit',
                    ];

                    User::where('id', $post->user_id)->decrement('mainwallet', $post->charge + $post->amount);
                    $report = Report::create($insert);
                
                    return response()->json(['status'=> 'TXN', 'message'=> 'Transaction Successfull','data'=> $response->data]);
                }
                else{
                    return response()->json(['statuscode'=> 'ERR', 'message'=> $response->message]);
                }
                break;    
            case 'registration':
            case 'ekycotp':
            case 'addbeneficiary':
            case 'benedelete':
            case 'refundotp':
                if(isset($response->response_code) && $response->response_code == "1"){
                    return response()->json(['statuscode'=> 'TXN', 'message'=> 'Transaction Successfull']);
                }else{
                    return response()->json(['statuscode'=> 'ERR', 'message'=> $response->message]);
                }
                break;
                
            case 'getrefund':
                if(isset($response->response_code) && $response->response_code == "1"){
                    Report::where('id', $post->transid)->update(['status' => "reversed"]);
                    \Myhelper::transactionRefund($post->transid);
                    return response()->json(['statuscode'=> 'TXN', 'message'=> 'Transaction Successfull']);
                }else{
                    return response()->json(['statuscode'=> 'ERR', 'message'=> $response->message]);
                }
                break;
                
            case 'getbeneficiary':
                if(isset($response->response_code) && $response->response_code == "1"){
                    return $response->data;
                }else{
                    return [];
                }
                break;
                
            case 'benedelete':
            case 'benedeletevalidate':
            case 'registration':
            case 'registrationValidate':
            case 'addbeneficiary':
            case 'outletotp':
            case 'mobilechange':
            case 'mobilechangeverify':
                return response()->json($response);
                break;

            case 'outletregister':
                $post['merchantLoginId'] = $data->outletid;
                $count = Fingagent::where('merchantLoginId', $post->merchantLoginId)->count();
                if($count == 0){
                    Fingagent::create($post->all());
                }
                return response()->json([
                    'status'=> 'TXN', 
                    'message'=> 'Transaction Successfull'
                ]);
            break;

            case 'accountverification':
                if(isset($response->response_code) && $response->response_code == 1 && $response->status == "true"){
                    
                    $balance = User::where('id', $userdata->id)->first(['mainwallet']);
                    $getbank = \DB::table('dmtbanks')->where('bankid', $post->benebank)->first(['name']);
                    
                    $insert = [
                        'api_id' => $this->api->id,
                        'provider_id' => $post->provider_id,
                        'option1' => $post->name,
                        'mobile' => $post->mobile,
                        'number' => $post->beneaccount,
                        'option2' => isset($response->benename) ? $response->benename : $post->benename,
                        'option3' => $getbank->name,
                        'option4' => $post->beneifsc,
                        'txnid' => $post->txnid,
                        'refno' => isset($response->refid) ? $response->refid : "none",
                        'amount' => $post->amount,
                        'charge' => $post->charge,
                        'remark' => "Money Transfer",
                        'status' => 'success',
                        'user_id' => $userdata->id,
                        'credit_by' => $userdata->id,
                        'product' => 'verification',
                        'balance' => $balance->mainwallet,
                        'description' => $post->benemobile,
                        'trans_type'  => 'debit',
                    ];

                    User::where('id', $post->user_id)->decrement('mainwallet', $post->charge + $post->amount);
                    $report = Report::create($insert);
                    return response()->json(['status'=> 'TXN', 'message'=>$response->benename]);
                }else{
                    
                    $balance = User::where('id', $userdata->id)->first(['mainwallet']);
                    $insert = [
                        'api_id' => $this->api->id,
                        'provider_id' => $post->provider_id,
                        'option1' => $post->name,
                        'mobile' => $post->mobile,
                        'number' => $post->beneaccount,
                        'option2' => isset($response->benename) ? $response->benename : $post->benename,
                        'option3' => $post->benebank,
                        'option4' => $post->beneifsc,
                        'txnid' => $post->txnid,
                        'refno' => isset($response->message) ? $response->message : "none",
                        'amount' => '0',
                        'charge' => '0',
                        'remark' => "Money Transfer",
                        'status' => 'failed',
                        'user_id' => $userdata->id,
                        'credit_by' => $userdata->id,
                        'product' => 'verification',
                        'balance' => $balance->mainwallet,
                        'description' => $post->benemobile,
                        'trans_type'  => 'debit',
                    ];

                    $report = Report::create($insert);
                    
                    return response()->json(['status'=> 'TXR', 'message'=> $response->message]);
                }
                break;
            
            case 'transfer':
                $report = Report::where('id', $post->reportid)->first();
                
                if(isset($response->response_code) && $response->response_code == "1"){
                    if(in_array($response->txn_status, ["0", "5"])){
                        User::where('id', $post->user_id)->increment('mainwallet', $report->charge + $report->amount);
                        Report::where('id', $post->reportid)->update([
                            'status'=> 'failed',
                            'refno' => (isset($response->message))? $response->message : 'failed'
                        ]);
                        return ['statuscode'=> 'TXF', 'status'=> 'Transaction Failed' , 'message'=> 'Transaction Failed', "rrn" => (isset($response->message))? $response->message : 'failed', 'payid' => $report->txnid];
                    }else if(in_array($response->txn_status, ["4"])){
                        $charge = \Myhelper::getCommission($post->amount, $userdata->scheme_id, $post->provider_id, $userdata->role->slug);
                        $profit = $report->charge - $charge;
                        $post['gst'] = $this->calculateGlobalyGST($profit);
                        $post['tds'] = $this->calculateGlobalyTDS($profit);
                        $finallyprofitRemain = $profit - $post->gst - $post->tds;
                        User::where('id', $post->user_id)->increment('mainwallet', $finallyprofitRemain );
                        
                        Report::where('id', $post->reportid)->update([
                            'status'=> "pending",
                            'payid' => (isset($response->ackno))? $response->ackno : 'pending',
                            'refno' => (isset($response->utr))? $response->utr : 'pending',
                            'gst'   => $post->gst,
                            'tds'   => $post->tds,
                            'profit'=> $report->charge - $charge
                        ]);
                        \Myhelper::commission($report);
                        return ['statuscode'=> 'TUP', 'status'=> 'Transaction Under Process','message'=> "Transaction Under Process", 'rrn' => (isset($response->utr))? $response->utr : $report->txnid, 'payid' => $report->txnid];
                    }elseif(isset($response->response_code) && in_array($response->response_code, ["11", "13","3","4","7","8","9","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25"])){
                        User::where('id', $post->user_id)->increment('mainwallet', $report->charge + $report->amount);
                        Report::where('id', $post->reportid)->update([
                            'status'=> 'failed',
                            'refno' => (isset($response->message))? $response->message : 'failed'
                        ]);
                        return ['statuscode'=> 'TXF', 'status'=> 'Transaction Failed' , 'message'=> 'Transaction Failed', "rrn" => (isset($response->message))? $response->message : 'failed', 'payid' => $report->txnid];
                    }else{
                        $charge = \Myhelper::getCommission($post->amount, $userdata->scheme_id, $post->provider_id, $userdata->role->slug);
                        $profit = $report->charge - $charge;
                        $post['gst'] = $this->calculateGlobalyGST($profit);
                        $post['tds'] = $this->calculateGlobalyTDS($profit);
                        $finallyprofitRemain = $profit - $post->gst - $post->tds;
                        User::where('id', $post->user_id)->increment('mainwallet', $finallyprofitRemain );
                        
                        Report::where('id', $post->reportid)->update([
                            'status'=> "success",
                            'payid' => (isset($response->ackno))? $response->ackno : 'success',
                            'refno' => (isset($response->utr))? $response->utr : 'success',
                            'gst'   => $post->gst,
                            'tds'   => $post->tds,
                            'profit'=> $report->charge - $charge
                        ]);
                        \Myhelper::commission($report);
                        return ['statuscode'=> 'TXN', 'status'=> 'Transaction Success','message'=> "Transaction Under Process", 'rrn' => (isset($response->utr))? $response->utr : $report->txnid, 'payid' => $report->txnid];
                    }
                }elseif(isset($response->response_code) && in_array($response->response_code, ["11", "13","3","4","7","8","9","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25"])){
                        User::where('id', $post->user_id)->increment('mainwallet', $report->charge + $report->amount);
                        Report::where('id', $post->reportid)->update([
                            'status'=> 'failed',
                            'refno' => (isset($response->message))? $response->message : 'failed'
                        ]);
                        return ['statuscode'=> 'TXF', 'status'=> 'Transaction Failed' , 'message'=> 'Transaction Failed', "rrn" => (isset($response->message))? $response->message : 'failed', 'payid' => $report->txnid];
                }else{
                        $charge = \Myhelper::getCommission($post->amount, $userdata->scheme_id, $post->provider_id, $userdata->role->slug);
                        $profit = $report->charge - $charge;
                        $post['gst'] = $this->calculateGlobalyGST($profit);
                        $post['tds'] = $this->calculateGlobalyTDS($profit);
                        $finallyprofitRemain = $profit - $post->gst - $post->tds;
                        User::where('id', $post->user_id)->increment('mainwallet', $finallyprofitRemain );
                        Report::where('id', $post->reportid)->update([
                            'status'=> "pending",
                            'payid' => (isset($response->ackno))? $response->ackno : 'pending',
                            'refno' => (isset($response->utr))? $response->utr : 'pending',
                            'gst'   => $post->gst,
                            'tds'   => $post->tds,
                            'profit'=> $report->charge - $charge
                        ]);
                        \Myhelper::commission($report);
                        return ['statuscode'=> 'TXN', 'status'=> 'Transaction Success','message'=> "Transaction Under Process", 'rrn' => (isset($response->utr))? $response->utr : $report->txnid, 'payid' => $report->txnid];
                }
                break;
                case 'send_otp':
                   if(isset($response->response_code) && $response->response_code == 1){
                   
                    return response()->json(['statuscode'=> 'TOTP', 'message'=> 'Transaction Successfull','stateresp'=> $response->stateresp]);
                   }
                    break;

            default:
                return response()->json(['statuscode'=> 'BPR', 'status'=> 'Bad Parameter Request','message'=> "Bad Parameter Request"]);
                break;
        }
    }

    public function getCharge($amount)
    {
        if($amount > 99.9 && $amount < 1000){
            return 12;
        }
        else if($amount > 1001 && $amount < 2000){
            
            return 24;
        }
        else{
            return $amount*1.2/100;
        }
    }

    public function getGst($amount)
    {
        return $amount*18/100;
    }

    public function getTds($amount)
    {
        return $amount*5/100;
    }
    
    public function getpeniToken($uniqueid)
    {
        $payload =  [
            "timestamp" => time(),
            "partnerId" => $this->api->username,
            "reqid"     => $uniqueid
        ];
        
        $keyString =$this->api->password;
        // $keyString = "UFMwMDM3NzAyZjdmOTBiZmFhOWNmODViYmZkMzdkYTZjMjI2MTg2Yg==";
        
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

    public function getToken($uniqueid){
        $payload =  [
            "timestamp" => time(),
            "partnerId" => $this->api->username,
            "reqid"     => $uniqueid
        ];
        
        $keyString =$this->api->password;
        //$keyString = "UFMwMDM3NzAyZjdmOTBiZmFhOWNmODViYmZkMzdkYTZjMjI2MTg2Yg==";
        
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
