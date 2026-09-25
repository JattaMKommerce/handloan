<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Api;
use App\Models\User;
use App\Models\Aepsreport;
use App\Models\Report;
use App\Models\Balanceinqury;
use App\Models\Fingagent;
use App\Models\Mahastate;
use Illuminate\Support\Str;
use Auth;
use Carbon\Carbon;
use App\Models\Provider;
use App\Models\Fingtempdata;
use App\Models\Fingaepsbank;
use App\Models\Fingaadharpaybank;

class FingpayController extends Controller
{
    public function test(){
        return view('service.test');
    }
    public function index()
    {
        if (\Myhelper::hasRole('admin') || !\Myhelper::can('aeps_service')) {
            abort(403);
        }
        
        $agent = Fingagent::where('user_id', \Auth::id())->first();
        $data['agent'] = $agent;
        
        $isdone2fa = \DB::table('twostepauths')->where(['user_id'=>\Auth::id(),'date'=>strtotime(date('Y-m-d')),'api'=>'fingpayAEPS'])->first();
       
        if($isdone2fa){
            $data['isdoneaepsauth'] = 'true';
        }else{
            $data['isdoneaepsauth'] = 'false';
        }
        
        $isdone2faap = \DB::table('twostepauths')->where(['user_id'=>\Auth::id(),'date'=>strtotime(date('Y-m-d')),'api'=>'fingpayAP'])->first();
        if($isdone2faap){
            $data['isdoneapauth'] = 'true';
        }else{
            $data['isdoneapauth'] = 'false';
        }
        $data['mahastate'] = Mahastate::get();
        $data['bankName']  = Fingaepsbank::get();
        $data['bankName1'] = Fingaadharpaybank::get();
        $url="https://login.m2money.in/api/iaeps/transaction";
        $apidata = Api::where('code','faeps')->first();
        $param['token'] = $apidata->username;
        $param['transactionType'] = "companyType";
        $result = \Myhelper::curl($apidata->url, "POST", json_encode($param), ["Content-Type: application/json", "Accept: application/json"], "no");  
        $response= json_decode($result['response'], true);
       
        $data['companyTypes'] = $response['companyTypes'];
       
        if($agent && $agent->status != "rejected"){
            $apidata = Api::where('code','faeps')->first();
            $param['token'] = $apidata->username;
            
            $param['merchantLoginId'] = $agent->merchantLoginId;
            $param['transactionType'] = "useronboardstatus";
            $result = \Myhelper::curl($apidata->url, "POST", json_encode($param), ["Content-Type: application/json", "Accept: application/json"], "no");
            //dd($result);
            if(!$result['error'] || $result['response'] != ''){
                $doc = json_decode($result['response']);
                if(isset($doc->data) && $doc->data == "rejected"){
                    Fingagent::where('user_id', \Auth::id())->update(['status' => 'rejected']);
                }elseif(isset($doc->data) && $doc->data == "approved"){
                    Fingagent::where('user_id', \Auth::id())->update(['status' => 'approved']);
                }
                $agent = Fingagent::where('user_id', \Auth::id())->first();
                $data['agent'] = $agent;
            }
        }
       
        return view('service.faeps')->with($data);
    }
    
    public function transaction(Request $post)
    {        
        $post['user_id'] = \Auth::id();
        $user = User::where('id', $post->user_id)->first(); 
        $apidata = Api::where('code','faeps')->first();
        $post['token'] = $apidata->username;
        switch ($post->transactionType) {
            case 'useronboard':
                $fingdata = Fingagent::where('user_id', $post->user_id)->where('status', 'rejected')->first();
                
                if($fingdata){
                    try {
                        \Storage::deleteDirectory('kyc/useronboard'.$post->user_id);
                    } catch (\Exception $e) {}
                    
                    Fingagent::where('user_id', $post->user_id)->where('status', 'rejected')->delete();
                }
                
                $rules = array(
                    'merchantFName'    => 'required',
                    'merchantAddress' => 'required',
                    'merchantState'   => 'required',
                    'merchantCityName'    => 'required',
                    'merchantPhoneNumber' => 'required|numeric|digits:10',
                    'merchantAadhar'      => 'required|numeric|digits:12',
                    'userPan'         => 'required',
                    'merchantPinCode' => 'sometimes|numeric|digits:6',
                    'aadharPics'   => 'required|mimes:jpg,jpeg,pdf|max:1024',
                    'pancardPics'  => 'required|mimes:jpg,jpeg,pdf|max:1024',
                    'passports'  => 'required|mimes:jpg,jpeg,pdf|max:1024',
                    'shoppics'  => 'required|mimes:jpg,jpeg,pdf|max:1024',
                    'companyType'=>'required',
                    'companyBankAccountNumber'=>'required',
                    'bankIfscCode'=>'required',
                );
                break;

            case 'useronboarded':
                $rules = array(
                    'id'    => 'required',
                );
                break;
                
            case 'useronboardvalidate':
                $rules = array(
                    'transactionType'   => 'required',
                    'primaryKeyId'      => 'required',
                    'encodeFPTxnId'     => 'required',
                    'otp'    => 'required',
                );
                break;
                
            case 'useronboardotp':
                $rules = array(
                    'transactionType'    => 'required',
                );
                break;
                
            case 'useronboardekyc':
                $rules = array(
                    'transactionType'    => 'required',
                );
                break;
            case '2fa':
                
                $rules = array(
                    'transactionType' => 'required',
                    'txtPidData'      => 'required'
                    
                );
                break;    

            case 'BE':
            case 'MS':
                $post['transactionAmount'] = 0;
                $rules = array(
                    'transactionType' => 'required',
                    'mobileNumber'    => 'required|numeric|digits:10',
                    'adhaarNumber'    => 'required|numeric|digits:12',
                    'bankName1'       => 'required',
                    'txtPidData'      => 'required'
                );
                break;

            case 'CW':
                $rules = array(
                    'transactionType' => 'required',
                    'mobileNumber'    => 'required|numeric|digits:10',
                    'adhaarNumber'    => 'required|numeric|digits:12',
                    'bankName1'       => 'required',
                    'txtPidData'      => 'required',
                    'transactionAmount' => 'required|numeric|min:1|max:10000'
                );
                break;
                
            case 'M':
                $rules = array(
                    'transactionType' => 'required',
                    'mobileNumber'    => 'required|numeric|digits:10',
                    'adhaarNumber'    => 'required|numeric|digits:12',
                    'bankName2'       => 'required',
                    'txtPidData'      => 'required',
                    'transactionAmount' => 'required|numeric|min:1|max:10000'
                );
                break;
            
            default:
                return response()->json(['status' => "ERR", "message" => "Invalid Transaction Type"]);
                break;
        }
                
        $validator = \Validator::make($post->all(), $rules);
        if ($validator->fails()) {
            foreach ($validator->errors()->messages() as $key => $value) {
                $error = $value[0];
            }
            return response()->json(['status'=>'ERR', 'message'=> $error]);
        }

        switch ($post->transactionType) {
            case 'useronboard':
                $apidata = Api::where('code','faeps')->first();
                $agent= Fingagent::where("user_id",$user->id)->first();
               if($agent){
                   $post['merchantLoginId']= $agent->merchantLoginId;
                   $post['merchantLoginPin']= $agent->merchantLoginPin;
                   $post['merchantPhoneNumber']= $agent->merchantPhoneNumber;
               }
               else{
                   do {
                    $post['merchantLoginId']  = "EPM".rand(1111111111, 9999999999);
                } while (Fingagent::where("merchantLoginId", "=", $post->merchantLoginId)->first() instanceof Fingagent);

                do {
                    $post['merchantLoginPin'] = "EPMP".rand(111111, 999999);
                } while (Fingagent::where("merchantLoginPin", "=", $post->merchantLoginPin)->first() instanceof Fingagent);

               }
                
                try {
                    \Storage::deleteDirectory('kyc/useronboard'.$post->user_id);
                } catch (\Exception $e) {}

                if($post->hasFile('aadharPics')){
                    $post['aadharPic']  = url('public')."/".$post->file('aadharPics')->store('kyc/useronboard'.$post->user_id);
                }
                
                if($post->hasFile('pancardPics')){
                    $post['pancardPic'] = url('public')."/".$post->file('pancardPics')->store('kyc/useronboard'.$post->user_id);
                }
                if($post->hasFile('passports')){
                    $post['passport'] = url('public')."/".$post->file('passports')->store('kyc/useronboard'.$post->user_id);
                }
                if($post->hasFile('shoppics')){
                    $post['shoppic'] = url('public')."/".$post->file('shoppics')->store('kyc/useronboard'.$post->user_id);
                }
                // $json=[
                //     'token'           => $apidata->username,
                //     'merchantFName'    => $post->merchantFName,
                //     'merchantAddress' => $post->merchantAddress,
                //     'merchantState'   => $post->merchantState,
                //     'dob'   => $post->dob,
                //     'merchantalernativeNumber'   => $post->merchantalernativeNumber,
                //     'merchantCityName'    => $post->merchantCityName,
                //     'merchantPhoneNumber' => $post->merchantPhoneNumber,
                //     'merchantAadhar'      => $post->merchantAadhar,
                //     'userPan'         => $post->userPan,
                //     'merchantPinCode' => $post->merchantPinCode,
                //     'companyType'=>$post->companyType,
                //     'companyBankAccountNumber'=>$post->companyBankAccountNumber,
                //     'bankIfscCode'=>$post->bankIfscCode,
                //     ];
                 $post['status'] = "pending";
                if(!$agent){
                 $agent = Fingagent::create($post->all());
                }   
                $result = \Myhelper::curl($apidata->url, 'POST',$post->all(), array("Content-Type:multipart/form-data"), "yes", 'Fingagent',$post->merchantPhoneNumber);
                // print_r($post->all());
                // echo "<br>";
                // print_r($result['response']);
                // exit();
                $response = json_decode($result['response']);
                if(isset($response->status) && $response->status == "TXN"){
                    $post['status'] = "approved";
                    $post['merchant_status'] = "approved";
                    $post['merchantLoginId']  = $response->merchantLoginId;
                    $post['merchantLoginPin'] = $response->merchantLoginPin;
                    $agent = Fingagent::where('merchantLoginId',$post->merchantLoginId)->update(['status'=>'approved','merchant_status'=>'approved']);
                    return response()->json([
                        'status' => 'TXN', 
                        'message'=>'User onboard request submitted, wait for approval',
                        'merchantLoginId'  => $post->merchantLoginId,
                        'merchantLoginPin' => $post->merchantLoginPin
                    ]);
                    return response()->json(['status' => 'TXN', 'message' =>'User onboard successfully']);
                }else{
                    return response()->json(['status' => 'ERR', 'message' => isset($response->message) ? $response->message : 'Something went wrong']);
                }
                break;
                
                
            case 'useronboardvalidate':
                $agent = Fingagent::where('user_id', $post->user_id)->first();
                $post['merchantLoginId'] = $agent->merchantLoginId;
                $result = \Myhelper::curl($apidata->url, 'POST', $post->all(), array("Content-Type:multipart/form-data"), "yes", 'Fingagent',$post->merchantPhoneNumber);
                //dd([$apidata->url, $post->all(), $result]);
                $response = json_decode($result['response']);
                return response()->json($response);
                break;
                
            case 'useronboardotp':
                $agent = Fingagent::where('user_id', $post->user_id)->first();
                $post['merchantLoginId'] = $agent->merchantLoginId;
                $result = \Myhelper::curl($apidata->url, 'POST', $post->all(), array("Content-Type:multipart/form-data"), "yes", 'Fingagent',$post->merchantPhoneNumber);
                // echo $apidata->url;
                // echo $result['response'];
                // exit();
                //dd([$apidata->url, $post->all(), $result]);
                $response = json_decode($result['response']);
                
                if($result['response'] != ''){
                    if(isset($response->message) && $response->message == "Ekyc already Verified"){
                        Fingagent::where('user_id', $post->user_id)->update(['everify' => 'success']);
                    }
                }
                
                return response()->json($response);
                break;
                
            case 'useronboardekyc':
                $agent = Fingagent::where('user_id', $post->user_id)->first();
                $post['merchantLoginId'] = $agent->merchantLoginId;
                $result = \Myhelper::curl($apidata->url, 'POST', $post->all(), array("Content-Type:multipart/form-data"), "yes", 'Fingagent',$post->merchantLoginId);
                $response = json_decode($result['response']);
                
                if($result['response'] != ''){
                    if(isset($response->status) && $response->status == "TXN"){
                        Fingagent::where('user_id', $post->user_id)->update(['everify' => 'success']);
                    }
                    
                    if(isset($response->message) && $response->message == "Ekyc already Verified"){
                        Fingagent::where('user_id', $post->user_id)->update(['everify' => 'success']);
                    }
                    // if($response->status == "ERR"){
                    //   return response()->json(['status' => "ERR", "message" => $response->message]);  
                    // }
                    
                }
                
                return response()->json($response);
                break;
                
            case '2fa':
                $agent = Fingagent::where('user_id', $post->user_id)->first();
                $post['merchantLoginId'] = $agent->merchantLoginId;
                $result = \Myhelper::curl($apidata->url, 'POST', $post->all(), array("Content-Type:multipart/form-data"), "yes", 'Fingagent',$post->merchantPhoneNumber);
                $response = json_decode($result['response']);
                
                if($result['response'] != ''){
                    if(isset($response->status) && $response->status == "success"){
                        $aaray = array();
                        $aaray['api'] = 'fingpayAEPS';
                        $aaray['user_id'] = $post->user_id;
                        $aaray['date'] = strtotime(date('Y-m-d'));
                        $aaray['status'] = "success";
                         $checkyser = \DB::table('twostepauths')->where(['user_id'=>$post->user_id,'date'=>$aaray['date'],'api'=>'fingpayAEPS'])->first();
                        if(!$checkyser){
                            \DB::table('twostepauths')->insert($aaray);
                        }
                        
                    }
                    
                    
                }
                
                return response()->json($response);
                break;    
        }

        $apidata = Api::where('code', 'faeps')->first();
        
        $agent = Fingagent::where('user_id', $post->user_id)->first();
        if(!$agent){
            return response()->json(['status' => "ERR", "message" => "User Not Onboarded"]);
        }
        
        do {
            $post['txnid'] = $this->transcode().rand(1111111111, 9999999999);
        } while (Aepsreport::where("txnid", "=", $post->txnid)->first() instanceof Aepsreport);

        switch ($post->transactionType) {
            case 'CW':
            case 'BE':
            case 'MS':
                $bank  = \DB::table('fingaepsbanks')->where('iinno', $post->bankName1)->first();
                break;

            case 'M':
                $bank  = \DB::table('fingaadharpaybanks')->where('iinno', $post->bankName2)->first();
                break;
                
                
        }

        if($post->transactionType == "CW" || $post->transactionType == "M" || $post->transactionType == "MS"){
            if($post->transactionType == "CW"){
                if($post->transactionAmount >=100 && $post->transactionAmount <=499){
                    $provider = Provider::where('recharge1', 'aeps1')->first();
                }elseif($post->transactionAmount >499 && $post->transactionAmount <=1000){
                    $provider = Provider::where('recharge1', 'aeps2')->first();
                }elseif($post->transactionAmount >1000 && $post->transactionAmount <=1500){
                    $provider = Provider::where('recharge1', 'aeps3')->first();
                }elseif($post->transactionAmount >1500 && $post->transactionAmount <=2000){
                    $provider = Provider::where('recharge1', 'aeps4')->first();
                }elseif($post->transactionAmount >2000 && $post->transactionAmount <=2500){
                    $provider = Provider::where('recharge1', 'aeps5')->first();
                }elseif($post->transactionAmount >2500 && $post->transactionAmount <=3000){
                    $provider = Provider::where('recharge1', 'aeps6')->first();
                }elseif($post->transactionAmount >3000 && $post->transactionAmount <=4000){
                    $provider = Provider::where('recharge1', 'aeps7')->first();
                }elseif($post->transactionAmount >4000 && $post->transactionAmount <=5000){
                    $provider = Provider::where('recharge1', 'aeps8')->first();
                }elseif($post->transactionAmount >5000 && $post->transactionAmount <=7000){
                    $provider = Provider::where('recharge1', 'aeps9')->first();
                }elseif($post->transactionAmount >7000 && $post->transactionAmount <=10000){
                    $provider = Provider::where('recharge1', 'aeps10')->first();
                }
                // elseif($post->transactionAmount >4999 && $post->transactionAmount <=10000){
                //     $provider = Provider::where('recharge1', 'aeps11')->first();
                // }
            }elseif($post->transactionType == "M"){
                if($post->transactionAmount >=1 && $post->transactionAmount <=10000){
                    $provider = Provider::where('recharge1', 'aadharpay1')->first();
                }
            }else{
                $provider = Provider::where('recharge1', 'ms')->first();
            }

            $post['provider_id'] = $provider->id;
            $post['profit'] = \Myhelper::getCommission($post->transactionAmount, $user->scheme_id, $post->provider_id, $user->role->slug);
            $post['tds']    = (5 * $post->profit) / 100;
            
            if($post->transactionType == "M"){
                $post['gst'] = $post->profit- (100 * $post->profit) / 118;
            }else{
                $post['gst'] = 0;
            }
            
            $insert = [
                "mobile" => $post->mobileNumber,
                "aadhar" => "XXXXXXXX".substr($post->adhaarNumber, -4),
                "txnid"  => $post->txnid,
                "amount" => $post->transactionAmount,
                "charge" => $post->profit,
                'gst'    => $post->gst,
                "bank"   => $bank->bankName,
                "user_id"  => $user->id,
                'aepstype' => $post->transactionType,
                'authcode' => $post->device,
                'status'  => 'pending',
                'credited_by' => $user->id,
                'type' => 'credit',
                'balance' => $user->aepsbalance,
                'provider_id' => $post->provider_id,
                'api_id'      => $apidata->id,
            ];
            
            $insertReport = [
                'number' => "XXXXXXXX".substr($post->adhaarNumber, -4),
                'mobile' => $post->mobileNumber,
                'provider_id' => $post->provider_id,
                'amount' => $post->transactionAmount,
                "charge" => $post->profit,
                'gst'    => $post->gst,
                'txnid'  => $post->txnid,
                'option1'  => $bank->bankName,
                'option2'  => $post->transactionType,
                'option3'  => $post->device,
                'status' => 'pending',
                'user_id'=> $user->id,
                'credit_by' => $user->id,
                'rtype' => 'main',
                'via'   => 'portal',
                'balance' => $user->aepsbalance,
                'trans_type'  => 'credit',
                'product'   => 'aeps',
                'api_id' => $apidata->id
            ];
            
            if($post->transactionType == "M"){
                $insert['product'] = "aadharpay";
            }
            //try {
                $report = Aepsreport::create($insert);
           // } catch (\Exception $e) {
               // return response()->json(['status' => "ERR", "message" => "Technical Issue, Try Again"]);
            //}
            $insertReport['option4']=$report->id;
            //Report::create($insertReport);
        }

        $parameter = [
            "apitxnid"         => $post->txnid,
            "transactionType"  => $post->transactionType, 
            "mobileNumber"     => $post->mobileNumber,
            "adhaarNumber"     => $post->adhaarNumber,
            "iinno"            => $bank->iinno,
            "txtPidData"       => $post->txtPidData,
            "merchantLoginId"  => $agent->merchantLoginId,
            'token'            => $post->token,
            "transactionAmount" => $post->transactionAmount,
            'device' => $post->device
        ];

        $header = array("Content-Type: application/json");
        
        $result = \Myhelper::curl($apidata->url,'POST', json_encode($parameter), $header, "yes", 'Fingagent',$post->txnid);
        if($result['response'] == ''){
            return response()->json([
                'status'   => 'pending', 
                'message'  => 'Transaction Under Process',
                'balance'  => isset($response->balance) ? $response->balance : '0',
                'rrn'      => isset($response->rrn) ? $response->rrn : 'pending',
                "transactionType"   => $post->transactionType,
                "title"    => "Cash Withdrawal",
                'aadhar'   => "XXXXXXXX".substr($post->adhaarNumber, -4),
                'id'       => $post->txnid,
                'amount'     => $post->transactionAmount,
                'created_at' => date('d M Y H:i'),
                'bank'     => $bank->bankName,
                'data'     => []
            ]);
        }

        $response = json_decode($result['response']);
        if(isset($response->status)){
            switch ($post->transactionType) {
                case 'BE':
                case 'MS':
                    if($response->status == "success" ){
                        
                        if($post->transactionType == "MS"){
                            $balance = User::where('id', $user->id)->first(['aepsbalance']);
                            Report::where('option4', "$report->id")->update([
                                'status'  => 'success',
                                'refno'   => $response->rrn,
                                'balance' => $balance->aepsbalance
                            ]);
                            Aepsreport::where('id', $report->id)->update([
                                'status'  => 'success',
                                'refno'   => $response->rrn,
                                'balance' => $balance->aepsbalance
                            ]);
    
                            User::where('id', $user->id)->increment('aepsbalance', $post->profit);
                        }
                        
                        return response()->json([
                            'status'   => 'success', 
                            'message'  => 'Transaction Successfull',
                            'aadhar'   => "XXXXXXXX".substr($post->adhaarNumber, -4),
                            'balance'  => $response->balance,
                            'rrn'      => $response->rrn,
                            "transactionType"   => $post->transactionType,
                            "title"    => ($post->transactionType == "BE") ? "Balance Enquiry" : "Mini Statement",
                            'id'       => $post->txnid,
                            'amount'   => $post->transactionAmount,
                            'created_at'=> isset($report->created_at)?$report->created_at: date('d M Y H:i'),
                            'bank'     => $bank->bankName,
                            "data"     => isset($response->data)?$response->data: []
                        ]);
                    }else{
                        return response()->json([
                            'status'   => 'failed', 
                            'message'  => isset($response->message) ? $response->message : "Failed",
                            'balance'  => isset($response->balance) ? $response->balance : '0',
                            'rrn'      => isset($response->rrn) ? $response->rrn : 'Failed',
                            "transactionType"   => $post->transactionType,
                            "title"    => "Mini Statement",
                            'aadhar'   => "XXXXXXXX".substr($post->adhaarNumber, -4),
                            'id'       => $post->txnid,
                            'created_at'=> date('d M Y H:i'),
                            'bank'     => $bank->bankName,
                            "data"     => isset($response->data)?$response->data: []
                        ]);
                    }
                    break;

                case 'CW':
                    if($response->status == "success" ){
                        $balance = User::where('id', $user->id)->first(['aepsbalance']);
                        Report::where('option4', "$report->id")->update([
                            'status' => 'success',
                            'refno'  => $response->rrn,
                            'balance'=> $balance->aepsbalance
                        ]);
                        Aepsreport::where('id', $report->id)->update([
                            'status' => 'success',
                            'refno'  => $response->rrn,
                            'balance'=> $balance->aepsbalance
                        ]);

                        User::where('id', $user->id)->increment('aepsbalance', $post->transactionAmount + $post->profit);

                        if($post->transactionAmount > 500 && $user->role->slug != "apiuser"){
                            \Myhelper::commission(Aepsreport::where('id', $report->id)->first());
                        }

                        return response()->json([
                            'status'   => 'success', 
                            'message'  => 'Transaction Successfull',
                            'aadhar'   => "XXXXXXXX".substr($post->adhaarNumber, -4),
                            'balance'  => $response->balance,
                            'rrn'      => $response->rrn,
                            "transactionType"   => $post->transactionType,
                            "title"    => "Cash Withdrawal",
                            'aadhar'   => "XXXXXXXX".substr($post->adhaarNumber, -4),
                            'id'       => $post->txnid,
                            'amount'   => $post->transactionAmount,
                            'created_at'=> isset($report->created_at)?$report->created_at: date('d M Y H:i'),
                            'bank'     => $bank->bankName
                        ]);
                    }else{
                        Report::where('option4', "$report->id")->update([
                            'status' => 'failed',
                            'refno'  => isset($response->rrn) ? $response->rrn : $response->message,
                            'remark' => isset($response->message) ? $response->message : ""
                        ]);
                        Aepsreport::where('id', $report->id)->update([
                            'status' => 'failed',
                            'refno'  => isset($response->rrn) ? $response->rrn : $response->message,
                            'remark' => isset($response->message) ? $response->message : ""
                        ]);

                        return response()->json([
                            'status'   => 'failed', 
                            'message'  => $response->message,
                            'balance'  => isset($response->balance) ? $response->balance : '0',
                            'rrn'      => isset($response->rrn) ? $response->rrn : 'Failed',
                            "transactionType"   => $post->transactionType,
                            "title"    => ($post->transactionType == "BE") ? "Balance Enquiry" : "Cash Withdrawal",
                            'aadhar'   => "XXXXXXXX".substr($post->adhaarNumber, -4),
                            'id'       => $post->txnid,
                            'amount'   => $post->transactionAmount,
                            'created_at'=> isset($report->created_at)?$report->created_at: date('d M Y H:i'),
                            'bank'     => $bank->bankName,
                            'aadhar'   => "XXXXXXXX".substr($post->adhaarNumber, -4),
                        ]);
                    }
                    break;

                case 'M':
                    if($response->status == "success" ){
                        $balance = User::where('id', $user->id)->first(['aepsbalance']);
                        Report::where('option4', "$report->id")->update([
                            'status' => 'success',
                            'refno'  => $response->rrn,
                            'balance'=> $balance->aepsbalance
                        ]);
                        Aepsreport::where('id', $report->id)->update([
                            'status' => 'success',
                            'refno'  => $response->rrn,
                            'balance'=> $balance->aepsbalance
                        ]);

                        User::where('id', $post->user_id)->increment('aepsbalance', $post->transactionAmount - $post->profit - $post->gst);

                        if($post->transactionAmount > 99 && $user->role->slug != "apiuser"){
                           \Myhelper::commission(Aepsreport::where('id', $report->id)->first());
                        }

                        return response()->json([
                            'status'   => 'success', 
                            'message'  => 'Transaction Successfull',
                            'aadhar'   => "XXXXXXXX".substr($post->adhaarNumber, -4),
                            'balance'  => $response->balance,
                            'rrn'      => $response->rrn,
                            "transactionType"   => $post->transactionType,
                            "title"    => (($post->transactionType == "BE") ? "Balance Enquiry" : (($post->transactionType == "CW") ? "Cash Withdrawal" : "Aadhar Pay")),
                            'aadhar'   => "XXXXXXXX".substr($post->adhaarNumber, -4),
                            'id'       => $post->txnid,
                            'amount'   => $post->transactionAmount,
                            'created_at'=> isset($report->created_at)?$report->created_at: date('d M Y H:i'),
                            'bank'     => $bank->bankName
                        ]);
                    }else{
                        Report::where('option4', "$report->id")->update([
                            'status' => 'failed',
                            'refno'  => isset($response->rrn) ? $response->rrn : $response->message,
                            'remark' => $response->message
                        ]);
                        Aepsreport::where('id', $report->id)->update([
                            'status' => 'failed',
                            'refno'  => isset($response->rrn) ? $response->rrn : $response->message,
                            'remark' => $response->message
                        ]);

                        return response()->json([
                            'status'   => 'failed', 
                            'message'  => $response->message,
                            'balance'  => isset($response->balance) ? $response->balance : '0',
                            'rrn'      => isset($response->rrn) ? $response->rrn : 'Failed',
                            "transactionType"   => $post->transactionType,
                            "title"    => ($post->transactionType == "BE") ? "Balance Enquiry" : "Cash Withdrawal",
                            'aadhar'   => "XXXXXXXX".substr($post->adhaarNumber, -4),
                            'id'       => $post->txnid,
                            'amount'   => $post->transactionAmount,
                            'created_at'=> isset($report->created_at)?$report->created_at: date('d M Y H:i'),
                            'bank'     => $bank->bankName,
                            'aadhar'   => "XXXXXXXX".substr($post->adhaarNumber, -4),
                        ]);
                    }
                    break;
            }
        }else{
            return response()->json([
                'status'   => 'pending', 
                'message'  => 'Transaction Under Process',
                'balance'  => isset($response->balance) ? $response->balance : '0',
                'rrn'      => isset($response->rrn) ? $response->rrn : 'pending',
                'errorMsg' => $response->message,
                "transactionType"   => $post->transactionType,
                "title"    => "Cash Withdrawal",
                'aadhar'   => "XXXXXXXX".substr($post->adhaarNumber, -4),
                'id'       => $post->txnid,
                'amount'   => $post->transactionAmount,
                'created_at'=> date('d M Y H:i'),
                'bank'     => $bank->bankName,
                'data'     => []
            ]);
        }
    }
    
    public function cashdeposit(Request $post)
    {
        $data['agent'] = Fingagent::where('user_id', \Auth::id())->first();
        $data['state'] = \DB::table('fingstate')->orderBy('state','asc')->get();
        $data['aadharbanks'] = \DB::table('fingaepscashbanks')->get();
        return view('service.cashdeposit')->with($data);
    }

    public function cashdepositbanklist(Request $post)
    {
        $result = \Myhelper::curl("https://fingpayap.tapits.in/fpaepsservice/api/bankdata/bank/details", 'GET', "", [], "no");
        $banks  = json_decode($result['response']);

        foreach ($banks->data as $bank) {
            if($bank->iinno != "NULL"){
                $insert['activeFlag'] = $bank->activeFlag;
                $insert['bankName']   = $bank->bankName;
                $insert['iinno'] = $bank->iinno;

                $inserts[] = $insert;
            }
        }

        \DB::table('fingaepscashbanks')->insert($inserts);
    }

    public function cashdeposittransaction(Request $post)
    {
        $post['user_id'] = \Auth::id();
        $user     = User::where('id', $post->user_id)->first(); 
        $apidata  = Api::where('code', 'faeps')->first();
        $post['token'] = $apidata->username;
        switch ($post->transactionType) {
            case 'sendotp':
                $rules = array(
                    'transactionType' => 'required',
                    'mobileNumber'    => 'required|numeric|digits:10',
                    'iinno'           => 'required',
                    'accountNumber'   => 'required'
                );
                break;

            case 'otpvalidate':
                $rules = array(
                    'transactionType' => 'required',
                    'mobileNumber'    => 'required|numeric|digits:10',
                    'iinno'           => 'required',
                    'accountNumber'   => 'required',
                    'otp'             => 'required',
                    'txnid'           => 'required'
                );
                break;

            case 'cashdeposit':
                $rules = array(
                    'transactionType'   => 'required',
                    'mobileNumber'      => 'required|numeric|digits:10',
                    'iinno'             => 'required',
                    'transactionAmount' => 'required',
                    'accountNumber'     => 'required',
                    'otp'               => 'required',
                    'txnid'             => 'required'
                );
                break;
            
            default:
                return response()->json(['status' => "ERR", "message" => "Invalid Transaction Type"]);
                break;
        }

        $validator = \Validator::make($post->all(), $rules);
        if ($validator->fails()) {
            foreach ($validator->errors()->messages() as $key => $value) {
                $error = $value[0];
            }
            return response()->json(['status'=>'ERR', 'message'=> $error]);
        }
        
        $agent = Fingagent::where('user_id', $post->user_id)->first();
        if(!$agent){
            return response()->json(['status' => "ERR", "message" => "User Not Onboarded"]);
        }

        if($agent->status != "approved" ){
            return response()->json(['status' => "ERR", "message" => "User Onboard ".ucfirst($agent->status)]);
        }
        
        if($post->transactionAmount > $user->aepsbalance){
            return response()->json(['status' => "ERR", "message" => "Insufficient Wallet Balance"]);
        }

        switch ($post->transactionType) {
            case 'sendotp':
                do {
                    $post['txnid'] = $this->transcode().rand(1111111111, 9999999999);
                } while (Aepsreport::where("txnid", "=", $post->txnid)->first() instanceof Aepsreport);
                $deviceIMEI = "3452342342";
                break;

            case 'otpvalidate':
                $tempdata = Fingtempdata::where("merchantTranId", "=", $post->txnid)->first();

                if(!$tempdata){
                    return response()->json(['status' => "ERR", "message" => "Invalid Transaction"]);
                }
                $deviceIMEI = $tempdata->deviceIMEI;
                break;

            case 'cashdeposit':
                $tempdata = Fingtempdata::where("merchantTranId", "=", $post->txnid)->first();
                if(!$tempdata){
                    return response()->json(['status' => "ERR", "message" => "Invalid Transaction"]);
                }
                $deviceIMEI = $tempdata->deviceIMEI;
                break;
            
            default:
                return response()->json(['status' => "ERR", "message" => "Invalid Transaction Type"]);
                break;
        }

        $bank  = \DB::table('fingaepscashbanks')->where('iinno', $post->iinno)->first();
        switch ($post->transactionType) {
            case 'sendotp':
                //try {
                    $tempdata = Fingtempdata::create([
                        'merchantTranId' => $post->txnid,
                        'mobileNumber'   => $post->mobileNumber, 
                        'accountNumber'  => $post->accountNumber,
                        'transactiontype'=> $post->transactiontype, 
                        'user_id'        => $post->user_id,
                        'deviceIMEI'     => $deviceIMEI
                    ]);

                // } catch (\Exception $e) {
                //     return response()->json(['status' => "ERR", "message" => "Technical Issue, Try Again"]);
                // }
                $agent = Fingagent::where('user_id', $post->user_id)->first();
                $post['merchantLoginId'] = $agent->merchantLoginId;
                break;

            case 'otpvalidate':
                $post["fingpayTransactionId"] = $tempdata->fingpayTransactionId;
                $post["cdPkId"] = $tempdata->cdPkId;
                $post['merchantLoginId'] = $agent->merchantLoginId;
                break;

            case 'cashdeposit':
                if($post->transactionAmount > 0 && $post->transactionAmount <= 3000){
                    $provider = Provider::where('recharge1', 'cashdeposit1')->first();
                }elseif($post->transactionAmount>3000 && $post->transactionAmount<=4000){
                    $provider = Provider::where('recharge1', 'cashdeposit2')->first();
                }elseif($post->transactionAmount>4000 && $post->transactionAmount<=5000){
                    $provider = Provider::where('recharge1', 'cashdeposit3')->first();
                }elseif($post->transactionAmount>5000 && $post->transactionAmount<=10000){
                    $provider = Provider::where('recharge1', 'cashdeposit4')->first();
                }
                $post['provider_id'] = $provider->id;
                if($post->transactionAmount > 99){
                    $post['charge'] = \Myhelper::getCommission($post->transactionAmount, $user->scheme_id, $post->provider_id, $user->role->slug);
                }else{
                    $post['charge'] = 0;
                }

                $insert = [
                    "mobile" => $post->mobileNumber,
                    "aadhar" => $post->accountNumber,
                    "txnid"  => $post->txnid,
                    "amount" => $post->transactionAmount,
                    "charge" => $post->charge,
                    "bank"   => $bank->bankName,
                    "user_id"=> $user->id,
                    'aepstype'=> "CDO",
                    'authcode'=> $post->device,
                    'mytxnid' => $post->name,
                    'status'  => 'pending',
                    'credited_by' => $user->id,
                    'type' => 'credit',
                    'balance' => $user->aepsbalance,
                    'provider_id' => $post->provider_id,
                    'api_id' => $apidata->id,
                    'product' => 'cashdeposit'
                ];
                
                $insertReport = [
                    'number' => $post->accountNumber,
                    'mobile' => $post->mobileNumber,
                    'provider_id' => $post->provider_id,
                    'amount' => $post->transactionAmount,
                    "profit" => $post->charge,
                    'txnid'  => $post->txnid,
                    'option1'  => $bank->bankName,
                    'option3'  => $gpsdata->lat."/".$gpsdata->lon,
                    'status' => 'pending',
                    'user_id'=> $user->id,
                    'credit_by' => $user->id,
                    'rtype' => 'main',
                    'via'   => 'portal',
                    'balance' => $user->aepsbalance,
                    'trans_type'  => 'debit',
                    'product'   => 'aeps',
                    'apitxnid' => $post->apitxnid,
                    'api_id' => $apidata->id
                ];

                //try {
                    $report = Aepsreport::create($insert);
                    $insertReport['option4']=$report->id;
                    //Report::create($insertReport);
                //} catch (\Exception $e) {
                  //  return response()->json(['status' => "ERR", "message" => "Technical Issue, Try Again"]);
                //}
                $post['merchantLoginId'] = $agent->merchantLoginId;
                break;
        }
        
        $result = \Myhelper::curl("https://corporate.spayu.in/api/iaeps/cash/deposit/transaction", 'POST', $post->all(), array("Content-Type:multipart/form-data"), "yes", 'Fingagent',$post->merchantPhoneNumber);
        
        if($result['response'] == ''){
            return response()->json([
                'status'   => 'TUP', 
                'message'  => 'Transaction Under Process',
                'rrn'      => 'pending',
                'txnid'    => $post->txnid,
                "bank" => $bank->bankName
            ]);
        }

        $response = json_decode($result['response']);
        if(isset($response->status)){
            switch ($post->transactionType) {
                case 'sendotp':
                    if($response->status == "TXN"){
                        return response()->json([
                            'status'   => 'TXN', 
                            'message'  => $response->message,
                            'txnid'    => $post->txnid
                        ]);

                    }else{
                        return response()->json([
                            'status'   => 'TXR', 
                            'message'  => $response->message
                        ]);
                    }
                    break;

                case 'otpvalidate':
                    if($response->status == "TXN"){
                        return response()->json([
                            'status'   => 'TXN', 
                            'message'  => $response->message,
                            'benename' => $response->benename,
                            'account'  => $post->accountNumber,
                            'amount'   => $post->transactionAmount,
                            'txnid'    => $post->txnid
                        ]);

                    }else{
                        return response()->json([
                            'status'   => 'TXR', 
                            'message'  => $response->message
                        ]);
                    }
                    break;

                case 'cashdeposit':
                    //dd($result);
                    if($response->status == "TXN"){
                        $balance = User::where('id', $user->id)->first(['aepsbalance']);
                        Report::where('option4', "$report->id")->update([
                            'status' => 'success',
                            'refno'  => $response->rrn,
                            'balance'=> $balance->aepsbalance,
                        ]);
                        Aepsreport::where('id', $report->id)->update([
                            'status' => 'success',
                            'refno'  => $response->rrn,
                            'balance'=> $balance->aepsbalance,
                        ]);

                        User::where('id', $post->user_id)->decrement('aepsbalance', $post->transactionAmount + $post->charge);

                        if($post->transactionAmount > 99){
                            try {
                                \Myhelper::commission(Aepsreport::where('id', $report->id)->first());
                            } catch (\Exception $e) {}
                        }

                        return response()->json([
                            'status'   => 'TXN', 
                            'message'  => 'Transaction Successfull',
                            'rrn'      => $response->rrn,
                            'benename' => $post->benename,
                            'account'  => $post->accountNumber,
                            'amount'   => $post->transactionAmount,
                            'txnid'    => $post->txnid,
                            "bank"     => $bank->bankName,
                            'date'     => date('d-M-Y')
                        ]);
                    }else{
                        Aepsreport::where('id', $report->id)->update([
                            'status' => 'failed',
                            'refno'  => isset($response->rrn) ? $response->rrn : $response->message,
                            'balance'=> $user->aepsbalance,
                            'payid'  => isset($response->data->fingpayTransactionId) ? $response->data->fingpayTransactionId : '',
                            'mytxnid'    => isset($response->data->fpRrn) ? $response->data->fpRrn : '',
                            'terminalid' => isset($response->data->stan) ? $response->data->stan : '',
                            'remark' => isset($response->message) ? $response->message : ''
                        ]);

                        return response()->json([
                            'status'   => 'TXR', 
                            'message'  => $response->message,
                            'rrn'      => isset($response->rrn) ? $response->rrn : $response->message,
                            'benename' => $post->benename,
                            'account'  => $post->accountNumber,
                            'amount'   => $post->transactionAmount,
                            'txnid'    => $post->txnid,
                            "bank"     => $bank->bankName
                        ]);
                    }
                    break;
            }
        }else{
            return response()->json([
                'status'   => 'TUP', 
                'message'  => 'Transaction Under Process',
                'rrn'      => 'pending',
                'txnid'    => $post->txnid,
                "bank" => $bank->bankName
            ]);
        }
    }
}
