<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Models\Pindata;
use App\Models\Api;
use App\Models\Circle;
use App\Models\Role;
use App\Models\LoanEnquiry;
use Carbon\Carbon;
use MiladRahimi\Jwt\Cryptography\Algorithms\Hmac\HS256;
use MiladRahimi\Jwt\JwtGenerator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    protected $api;
    public function __construct()
    {
        
        $this->api = Api::where('code', 'sadharverify')->first();
        
    }
    public function loanindex(){
       $data['mahastate'] = Circle::all();
         $data['agents'] = User::where("role_id","7")->get(['id','name','mobile']);
        return view('loanform')->with($data);
    }
    
     public function showRetailerLoginForm(){
      $data['state'] = Circle::all();
        $data['roles'] = Role::whereIn('slug', ['whitelable', 'md', 'distributor', 'retailer'])->get();
        $string = substr(str_shuffle("ABCDEFGHJKHLMKOPRTEST"),17);
        $data['cptcha']  =  $string.rand(11, 99) ;
      //  return view('welcome')->with($data);
        return view('retailer_login')->with($data);
    }
    
    public function showMDLoginForm(){
      $data['state'] = Circle::all();
        $data['roles'] = Role::whereIn('slug', ['whitelable', 'md', 'distributor', 'retailer'])->get();
        $string = substr(str_shuffle("ABCDEFGHJKHLMKOPRTEST"),17);
        $data['cptcha']  =  $string.rand(11, 99) ;
      //  return view('welcome')->with($data);
        return view('md_login')->with($data);
    }
    
     public function showAdminLoginForm(){
      $data['state'] = Circle::all();
        $data['roles'] = Role::whereIn('slug', ['whitelable', 'md', 'distributor', 'retailer'])->get();
        $string = substr(str_shuffle("ABCDEFGHJKHLMKOPRTEST"),17);
        $data['cptcha']  =  $string.rand(11, 99) ;
      //  return view('welcome')->with($data);
        return view('admin_login')->with($data);
    }
    
     public function unsubscribe()
    {
        return view('unsubscribe');
    }
    
       public function unsubscriberequest(Request $request){
        $user = User::where('mobile', $request->mobile)->first();
        if(!$user){
            return response()->json(['status' => "Your aren't registred with us." ], 400);
        }
       $userrequest = \DB::table('deleterequestes')->insert([
                'user_id' => $user->id,
                'email' => $request->email,
                'mobile' => $request->mobile,
         ]); 
       return response()->json(['status' => "success" ],200);
    }
    
    public function adminLogin(Request $post)
    {
         $data['state'] = Circle::all();
        $data['roles'] = Role::whereIn('slug', ['whitelable', 'md', 'distributor', 'retailer'])->get();
        $string = substr(str_shuffle("ABCDEFGHJKHLMKOPRTEST"),17);
        $data['cptcha']  =  $string.rand(11, 99) ;
      //  return view('welcome')->with($data);
       return view('login_rapipay')->with($data); 
    }
    
    public function index()
    {         
        $data['state'] = Circle::all();
        $data['roles'] = Role::whereIn('slug', ['whitelable', 'md', 'distributor', 'retailer'])->get();
        $string = substr(str_shuffle("ABCDEFGHJKHLMKOPRTEST"),17);
        $data['cptcha']  =  $string.rand(11, 99) ;
      //  return view('welcome')->with($data);
       return view('login_rapipay')->with($data);
    }
    
     public function retailerlogin(Request $post)
    {

        if(!empty($request['g-recaptcha-response'])){
            $Response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".env('re_Captcha_SecretKey') . "&response={$request['g-recaptcha-response']}");
            $Return = json_decode($Response);
            if($Return->success == false){
                return response()->json(['status' => "Your are a robot" ], 400);
            }
        }

        $user = User::where('agentcode', $post->mobile)->first();
        
        $url = $_SERVER['HTTP_REFERER'];
        $QueryUrl = parse_url($url);
        
        $urlObject= (object) $QueryUrl;
        $getAdmin = trim($urlObject->path,"/");
        $checkAdmin = substr($getAdmin, 0, strpos($getAdmin, '/'));
    
        //$user = User::where('mobile', $post->mobile)->first();
        if($user->role_id!=4){
            return response()->json(['status' => "You are not aurthorised to login please go to your login page" ], 400);
        }
       
        
        if(!$user){
            return response()->json(['status' => "Your aren't registred with us." ], 400);
        }
        
        //  if($checkAdmin <> "admin" && $user->role_id =="1"){
        //   return response()->json(['status' => "Admin Login not allowed in this url" ], 400);
        // }elseif($checkAdmin == "admin" && $user->role_id !="1"){
        //     return response()->json(['status' => "User Login not allowed in this url" ], 400);
        // }
        //   $geodata = geoip($post->ip());
          $log['ip']           = $post->ip();
          $log['user_agent']   = $post->server('HTTP_USER_AGENT');
          $log['user_id']      = $user->id;
          $log['geo_location'] = '-3.831990'/'-38.552900';
          $log['url'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
          $log['parameters']   = 'portal';
          \DB::table('login_activitylogs')->insert($log);        
        $company = \App\Models\Company::where('id', $user->company_id)->first();
        $otprequired = \App\Models\PortalSetting::where('code', 'otplogin')->first();

        if(!\Auth::validate(['agentcode' => $post->mobile, 'password' => $post->password])){
            return response()->json(['status'=> 'Username or password is incorrect'], 400);
        }

        if (!\Auth::validate(['agentcode' => $post->mobile, 'password' => $post->password,'status'=> "active"])) {
            return response()->json(['status' => 'Your account currently de-activated, please contact administrator'], 400);
        }

      
        if($otprequired->value == "yes" && $company->senderid){
            
            if($post->has('otp') && $post->otp == "resend"){
                if($user->otpresend < 3){
                     $otpmailid   = \App\Models\PortalSetting::where('code', 'otpsendmailid')->first();
                     $otpmailname = \App\Models\PortalSetting::where('code', 'otpsendmailname')->first();
                    $otp = substr(md5(time()), 0, 6);
                    $regards="";
                    $msg = "Dear partner, your login otp is ".$otp." Don't share with anyone Regards MADHAVAM DIGITAL PAYMENTS";
                    //$msg = "Dear partner, your login otp is ".$otp." Don't share with anyone Regards ".$regards." \r\nLCO FINTECH(OPC) PRIVATE LIMITED";
                    $send = \Myhelper::sms($user->mobile, $msg);
                    if($this->iswpdeliver() == 'ON'){
                        $send = \Myhelper::whatsappsms($user->mobile, $msg);
                    }
                    $mail = \Myhelper::mail('mail.otp', ["otp" => $otp, "name" => $user->name , "subhead"=>"Login OTP"], $user->email, $user->name, $otpmailid->value, $otpmailname->value, "Login Otp");
                   if($send == 'success' || $mail == "success"){
                        User::where('mobile', $user->mobile)->update(['otpverify' => \Crypt::encrypt($otp), 'otpresend' => $user->otpresend+1]);
                        return response()->json(['status' => 'otpsent'], 200);
                    }else{
                        return response()->json(['status' => 'Please contact your service provider provider'], 400);
                    }
                }else{
                    return response()->json(['status' => 'Otp resend limit exceed, please contact your service provider'], 400);
                }
            }

            if($user->otpverify == "yes" || !$post->has('otp')){
                

                $otp  = substr(md5(time()), 0, 6);
                 $regards="";
                $msg = "Dear partner, your login otp is ".$otp." Don't share with anyone Regards MADHAVAM DIGITAL PAYMENTS";
                
                $send = \Myhelper::sms($user->mobile, $msg);
                $otpmailid   = \App\Models\PortalSetting::where('code', 'otpsendmailid')->first();
                $otpmailname = \App\Models\PortalSetting::where('code', 'otpsendmailname')->first();
                $mail = \Myhelper::mail('mail.otp', ["otp" => $otp, "name" => $user->name , "subhead"=>"Login OTP"], $user->email, $user->name, $otpmailid->value, $otpmailname->value, "Login Otp");
                if($this->iswpdeliver() == 'ON'){
                    $send = \Myhelper::whatsappsms($user->mobile, $msg);
                }
                if($send == 'success' || $mail == "success"){
                    User::where('mobile', $user->mobile)->update(['otpverify' => \Crypt::encrypt($otp)]);
                    return response()->json(['status' => 'otpsent'], 200);
                }else{
                    return response()->json(['status' => 'Please contact your service provider provider'], 400);
                }
            }else{
                if(!$post->has('otp')){
                    return response()->json(['status' => 'preotp'], 200);
                }
            }
            
            if(\Crypt::decrypt($user->otpverify) == $post->otp)
            {
                if (\Auth::attempt(['agentcode' =>$post->mobile, 'password' =>$post->password, 'status'=>"active"])){
                    User::where('mobile', $user->mobile)->update(['otpverify' => '']);
                    return response()->json(['status' => 'Login'], 200);
                }else{
                    return response()->json(['status' => 'Please provide correct otp'], 400);
                }
            }
            else{
                    return response()->json(['status' => 'Please provide correct otp'], 400);
                }

        }else{
            
            if (\Auth::attempt(['agentcode' =>$post->mobile, 'password' =>$post->password, 'status'=> "active"])) {
                return response()->json(['status' => 'Login'], 200);
            }else{
                return response()->json(['status' => 'Something went wrong, please contact administrator'], 400);
            }
        }
    }
    
     public function adminloginFn(Request $post)
    {

        if(!empty($request['g-recaptcha-response'])){
            $Response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".env('re_Captcha_SecretKey') . "&response={$request['g-recaptcha-response']}");
            $Return = json_decode($Response);
            if($Return->success == false){
                return response()->json(['status' => "Your are a robot" ], 400);
            }
        }

        $user = User::where('agentcode', $post->mobile)->first();
        
        $url = $_SERVER['HTTP_REFERER'];
        $QueryUrl = parse_url($url);
        
        $urlObject= (object) $QueryUrl;
        $getAdmin = trim($urlObject->path,"/");
        $checkAdmin = substr($getAdmin, 0, strpos($getAdmin, '/'));
    
        //$user = User::where('mobile', $post->mobile)->first();
        if($user->role_id!=1){
            return response()->json(['status' => "You are not aurthorised to login please go to your login page" ], 400);
        }
       
        
        if(!$user){
            return response()->json(['status' => "Your aren't registred with us." ], 400);
        }
        
        //  if($checkAdmin <> "admin" && $user->role_id =="1"){
        //   return response()->json(['status' => "Admin Login not allowed in this url" ], 400);
        // }elseif($checkAdmin == "admin" && $user->role_id !="1"){
        //     return response()->json(['status' => "User Login not allowed in this url" ], 400);
        // }
        //   $geodata = geoip($post->ip());
          $log['ip']           = $post->ip();
          $log['user_agent']   = $post->server('HTTP_USER_AGENT');
          $log['user_id']      = $user->id;
          $log['geo_location'] = '-3.831990'/'-38.552900';
          $log['url'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
          $log['parameters']   = 'portal';
          \DB::table('login_activitylogs')->insert($log);        
        $company = \App\Models\Company::where('id', $user->company_id)->first();
        $otprequired = \App\Models\PortalSetting::where('code', 'otplogin')->first();

        if(!\Auth::validate(['agentcode' => $post->mobile, 'password' => $post->password])){
            return response()->json(['status'=> 'Username or password is incorrect'], 400);
        }

        if (!\Auth::validate(['agentcode' => $post->mobile, 'password' => $post->password,'status'=> "active"])) {
            return response()->json(['status' => 'Your account currently de-activated, please contact administrator'], 400);
        }

      
        if($otprequired->value == "yes" && $company->senderid){
            if($post->has('otp') && $post->otp == "resend"){
                if($user->otpresend < 3){
                     $otpmailid   = \App\Models\PortalSetting::where('code', 'otpsendmailid')->first();
                     $otpmailname = \App\Models\PortalSetting::where('code', 'otpsendmailname')->first();
                    $otp = substr(md5(time()), 0, 6);
                    $regards="";
                    $msg = "Dear partner, your login otp is ".$otp." Don't share with anyone Regards MADHAVAM DIGITAL PAYMENTS";
                    //$msg = "Dear partner, your login otp is ".$otp." Don't share with anyone Regards ".$regards." \r\nLCO FINTECH(OPC) PRIVATE LIMITED";
                    $send = \Myhelper::sms($user->mobile, $msg);
                    $mail = \Myhelper::mail('mail.otp', ["otp" => $otp, "name" => $user->name , "subhead"=>"Login OTP"], $user->email, $user->name, $otpmailid->value, $otpmailname->value, "Login Otp");
                   if($send == 'success' || $mail == "success"){
                        User::where('mobile', $user->mobile)->update(['otpverify' => \Crypt::encrypt($otp), 'otpresend' => $user->otpresend+1]);
                        return response()->json(['status' => 'otpsent'], 200);
                    }else{
                        return response()->json(['status' => 'Please contact your service provider provider'], 400);
                    }
                }else{
                    return response()->json(['status' => 'Otp resend limit exceed, please contact your service provider'], 400);
                }
            }

            if($user->otpverify == "yes" || !$post->has('otp')){
                $otp  = substr(md5(time()), 0, 6);
                 $regards="";
                $msg = "Dear partner, your login otp is ".$otp." Don't share with anyone Regards MADHAVAM DIGITAL PAYMENTS";
                
                $send = \Myhelper::sms($user->mobile, $msg);
                $otpmailid   = \App\Models\PortalSetting::where('code', 'otpsendmailid')->first();
                $otpmailname = \App\Models\PortalSetting::where('code', 'otpsendmailname')->first();
                $mail = \Myhelper::mail('mail.otp', ["otp" => $otp, "name" => $user->name , "subhead"=>"Login OTP"], $user->email, $user->name, $otpmailid->value, $otpmailname->value, "Login Otp");
                if($send == 'success' || $mail == "success"){
                    User::where('mobile', $user->mobile)->update(['otpverify' => \Crypt::encrypt($otp)]);
                    return response()->json(['status' => 'otpsent'], 200);
                }else{
                    return response()->json(['status' => 'Please contact your service provider provider'], 400);
                }
            }else{
                if(!$post->has('otp')){
                    return response()->json(['status' => 'preotp'], 200);
                }
            }
            
            if(\Crypt::decrypt($user->otpverify) == $post->otp)
            {
                if (\Auth::attempt(['agentcode' =>$post->mobile, 'password' =>$post->password, 'status'=>"active"])){
                    User::where('mobile', $user->mobile)->update(['otpverify' => '']);
                    return response()->json(['status' => 'Login'], 200);
                }else{
                    return response()->json(['status' => 'Please provide correct otp'], 400);
                }
            }
            else{
                    return response()->json(['status' => 'Please provide correct otp'], 400);
                }

        }else{
            
            if (\Auth::attempt(['agentcode' =>$post->mobile, 'password' =>$post->password, 'status'=> "active"])) {
                return response()->json(['status' => 'Login'], 200);
            }else{
                return response()->json(['status' => 'Something went wrong, please contact administrator'], 400);
            }
        }
    }
    
     public function mdlogin(Request $post)
    {

        if(!empty($request['g-recaptcha-response'])){
            $Response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".env('re_Captcha_SecretKey') . "&response={$request['g-recaptcha-response']}");
            $Return = json_decode($Response);
            if($Return->success == false){
                return response()->json(['status' => "Your are a robot" ], 400);
            }
        }

        $user = User::where('agentcode', $post->mobile)->first();
        
        $url = $_SERVER['HTTP_REFERER'];
        $QueryUrl = parse_url($url);
        
        $urlObject= (object) $QueryUrl;
        $getAdmin = trim($urlObject->path,"/");
        $checkAdmin = substr($getAdmin, 0, strpos($getAdmin, '/'));
    
        //$user = User::where('mobile', $post->mobile)->first();
        if($user->role_id!=2 || $user->role_id!=3){
            return response()->json(['status' => "You are not aurthorised to login please go to your login page" ], 400);
        }
       
        
        if(!$user){
            return response()->json(['status' => "Your aren't registred with us." ], 400);
        }
        
        //  if($checkAdmin <> "admin" && $user->role_id =="1"){
        //   return response()->json(['status' => "Admin Login not allowed in this url" ], 400);
        // }elseif($checkAdmin == "admin" && $user->role_id !="1"){
        //     return response()->json(['status' => "User Login not allowed in this url" ], 400);
        // }
        //   $geodata = geoip($post->ip());
          $log['ip']           = $post->ip();
          $log['user_agent']   = $post->server('HTTP_USER_AGENT');
          $log['user_id']      = $user->id;
          $log['geo_location'] = '-3.831990'/'-38.552900';
          $log['url'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
          $log['parameters']   = 'portal';
          \DB::table('login_activitylogs')->insert($log);        
        $company = \App\Models\Company::where('id', $user->company_id)->first();
        $otprequired = \App\Models\PortalSetting::where('code', 'otplogin')->first();

        if(!\Auth::validate(['agentcode' => $post->mobile, 'password' => $post->password])){
            return response()->json(['status'=> 'Username or password is incorrect'], 400);
        }

        if (!\Auth::validate(['agentcode' => $post->mobile, 'password' => $post->password,'status'=> "active"])) {
            return response()->json(['status' => 'Your account currently de-activated, please contact administrator'], 400);
        }

      
        if($otprequired->value == "yes" && $company->senderid){
            if($post->has('otp') && $post->otp == "resend"){
                if($user->otpresend < 3){
                     $otpmailid   = \App\Models\PortalSetting::where('code', 'otpsendmailid')->first();
                     $otpmailname = \App\Models\PortalSetting::where('code', 'otpsendmailname')->first();
                    $otp = substr(md5(time()), 0, 6);
                    $regards="";
                    $msg = "Dear partner, your login otp is ".$otp." Don't share with anyone Regards MADHAVAM DIGITAL PAYMENTS";
                    //$msg = "Dear partner, your login otp is ".$otp." Don't share with anyone Regards ".$regards." \r\nLCO FINTECH(OPC) PRIVATE LIMITED";
                    $send = \Myhelper::sms($user->mobile, $msg);
                    $mail = \Myhelper::mail('mail.otp', ["otp" => $otp, "name" => $user->name , "subhead"=>"Login OTP"], $user->email, $user->name, $otpmailid->value, $otpmailname->value, "Login Otp");
                   if($send == 'success' || $mail == "success"){
                        User::where('mobile', $user->mobile)->update(['otpverify' => \Crypt::encrypt($otp), 'otpresend' => $user->otpresend+1]);
                        return response()->json(['status' => 'otpsent'], 200);
                    }else{
                        return response()->json(['status' => 'Please contact your service provider provider'], 400);
                    }
                }else{
                    return response()->json(['status' => 'Otp resend limit exceed, please contact your service provider'], 400);
                }
            }

            if($user->otpverify == "yes" || !$post->has('otp')){
                $otp  = substr(md5(time()), 0, 6);
                 $regards="";
                $msg = "Dear partner, your login otp is ".$otp." Don't share with anyone Regards MADHAVAM DIGITAL PAYMENTS";
                
                $send = \Myhelper::sms($user->mobile, $msg);
                $otpmailid   = \App\Models\PortalSetting::where('code', 'otpsendmailid')->first();
                $otpmailname = \App\Models\PortalSetting::where('code', 'otpsendmailname')->first();
                $mail = \Myhelper::mail('mail.otp', ["otp" => $otp, "name" => $user->name , "subhead"=>"Login OTP"], $user->email, $user->name, $otpmailid->value, $otpmailname->value, "Login Otp");
                if($send == 'success' || $mail == "success"){
                    User::where('mobile', $user->mobile)->update(['otpverify' => \Crypt::encrypt($otp)]);
                    return response()->json(['status' => 'otpsent'], 200);
                }else{
                    return response()->json(['status' => 'Please contact your service provider provider'], 400);
                }
            }else{
                if(!$post->has('otp')){
                    return response()->json(['status' => 'preotp'], 200);
                }
            }
            
            if(\Crypt::decrypt($user->otpverify) == $post->otp)
            {
                if (\Auth::attempt(['agentcode' =>$post->mobile, 'password' =>$post->password, 'status'=>"active"])){
                    User::where('mobile', $user->mobile)->update(['otpverify' => '']);
                    return response()->json(['status' => 'Login'], 200);
                }else{
                    return response()->json(['status' => 'Please provide correct otp'], 400);
                }
            }
            else{
                    return response()->json(['status' => 'Please provide correct otp'], 400);
                }

        }else{
            
            if (\Auth::attempt(['agentcode' =>$post->mobile, 'password' =>$post->password, 'status'=> "active"])) {
                return response()->json(['status' => 'Login'], 200);
            }else{
                return response()->json(['status' => 'Something went wrong, please contact administrator'], 400);
            }
        }
    }

    public function login(Request $post)
    {
        

        if(!empty($request['g-recaptcha-response'])){
            $Response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".env('re_Captcha_SecretKey') . "&response={$request['g-recaptcha-response']}");
            $Return = json_decode($Response);
            if($Return->success == false){
                return response()->json(['status' => "Your are a robot" ], 400);
            }
        }

        $user = User::where('agentcode', $post->mobile)->first();
        
        $url = $_SERVER['HTTP_REFERER'];
        $QueryUrl = parse_url($url);
        
        $urlObject= (object) $QueryUrl;
        $getAdmin = trim($urlObject->path,"/");
        $checkAdmin = substr($getAdmin, 0, strpos($getAdmin, '/'));
    
        //$user = User::where('mobile', $post->mobile)->first();
        
       
        
        if(!$user){
            return response()->json(['status' => "Your aren't registred with us." ], 400);
        }
        
        //  if($checkAdmin <> "admin" && $user->role_id =="1"){
        //   return response()->json(['status' => "Admin Login not allowed in this url" ], 400);
        // }elseif($checkAdmin == "admin" && $user->role_id !="1"){
        //     return response()->json(['status' => "User Login not allowed in this url" ], 400);
        // }
        //   $geodata = geoip($post->ip());
          $log['ip']           = $post->ip();
          $log['user_agent']   = $post->server('HTTP_USER_AGENT');
          $log['user_id']      = $user->id;
          $log['geo_location'] = '-3.831990'/'-38.552900';
          $log['url'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
          $log['parameters']   = 'portal';
          \DB::table('login_activitylogs')->insert($log);        
        $company = \App\Models\Company::where('id', $user->company_id)->first();
        $otprequired = \App\Models\PortalSetting::where('code', 'otplogin')->first();

        if(!\Auth::validate(['agentcode' => $post->mobile, 'password' => $post->password])){
            return response()->json(['status'=> 'Username or password is incorrect'], 400);
        }

        if (!\Auth::validate(['agentcode' => $post->mobile, 'password' => $post->password,'status'=> "active"])) {
            return response()->json(['status' => 'Your account currently de-activated, please contact administrator'], 400);
        }

      
        if($otprequired->value == "yes" && $company->senderid){
            if ($user->id == 2||$user->id == 120) {
        if (\Auth::attempt(['agentcode' => $post->mobile, 'password' => $post->password, 'status' => "active"])) {
            return response()->json(['status' => 'Login'], 200);
        } else {
            return response()->json(['status' => 'Invalid credentials'], 400);
        }
    }

            if($post->has('otp') && $post->otp == "resend"){
                if($user->otpresend < 3){
                     $otpmailid   = \App\Models\PortalSetting::where('code', 'otpsendmailid')->first();
                     $otpmailname = \App\Models\PortalSetting::where('code', 'otpsendmailname')->first();
                    $otp = substr(md5(time()), 0, 6);
                    $regards="";
                    $msg = "Dear partner, your login otp is ".$otp." Don't share with anyone Regards MADHAVAM DIGITAL PAYMENTS";
                    //$msg = "Dear partner, your login otp is ".$otp." Don't share with anyone Regards ".$regards." \r\nLCO FINTECH(OPC) PRIVATE LIMITED";
                    $send = \Myhelper::sms($user->mobile, $msg);
                    
                    if($this->iswpdeliver() == 'ON'){
                    $send = \Myhelper::whatsappsms($user->mobile, $msg);
                     }
                
                    $mail = \Myhelper::mail('mail.otp', ["otp" => $otp, "name" => $user->name , "subhead"=>"Login OTP"], $user->email, $user->name, $otpmailid->value, $otpmailname->value, "Login Otp");
                   if($send == 'success' || $mail == "success"){
                        User::where('mobile', $user->mobile)->update(['otpverify' => \Crypt::encrypt($otp), 'otpresend' => $user->otpresend+1]);
                        return response()->json(['status' => 'otpsent'], 200);
                    }else{
                        return response()->json(['status' => 'Please contact your service provider provider'], 400);
                    }
                }else{
                    return response()->json(['status' => 'Otp resend limit exceed, please contact your service provider'], 400);
                }
            }

            if($user->otpverify == "yes" || !$post->has('otp')){
                $otp  = substr(md5(time()), 0, 6);
                 $regards="";
                $msg = "Dear partner, your login otp is ".$otp." Don't share with anyone Regards MADHAVAM DIGITAL PAYMENTS";
                
                $send = \Myhelper::sms($user->mobile, $msg);
                $otpmailid   = \App\Models\PortalSetting::where('code', 'otpsendmailid')->first();
                $otpmailname = \App\Models\PortalSetting::where('code', 'otpsendmailname')->first();
                $mail = \Myhelper::mail('mail.otp', ["otp" => $otp, "name" => $user->name , "subhead"=>"Login OTP"], $user->email, $user->name, $otpmailid->value, $otpmailname->value, "Login Otp");
                
                 if($this->iswpdeliver() == 'ON'){
                    $send = \Myhelper::whatsappsms($user->mobile, $msg);
                }
                
                if($send == 'success' || $mail == "success"){
                    User::where('mobile', $user->mobile)->update(['otpverify' => \Crypt::encrypt($otp)]);
                    return response()->json(['status' => 'otpsent'], 200);
                }else{
                    return response()->json(['status' => 'Please contact your service provider provider'], 400);
                }
            }else{
                if(!$post->has('otp')){
                    return response()->json(['status' => 'preotp'], 200);
                }
            }
            
            if(\Crypt::decrypt($user->otpverify) == $post->otp)
            {
                if (\Auth::attempt(['agentcode' =>$post->mobile, 'password' =>$post->password, 'status'=>"active"])){
                    User::where('mobile', $user->mobile)->update(['otpverify' => '']);
                    return response()->json(['status' => 'Login'], 200);
                }else{
                    return response()->json(['status' => 'Please provide correct otp'], 400);
                }
            }
            else{
                    return response()->json(['status' => 'Please provide correct otp'], 400);
                }

        }else{
            
            if (\Auth::attempt(['agentcode' =>$post->mobile, 'password' =>$post->password, 'status'=> "active"])) {
                return response()->json(['status' => 'Login'], 200);
            }else{
                return response()->json(['status' => 'Something went wrong, please contact administrator'], 400);
            }
        }
    }

    public function logout(Request $request)
    {
        \Auth::guard()->logout();
        $request->session()->invalidate();
        return redirect('/');
    }

    public function passwordReset(Request $post)
    {
        $rules = array(
            'type' => 'required',
            'mobile'  =>'required',
        );

        $validate = \Myhelper::FormValidator($rules, $post);
        if($validate != "no"){
            return $validate;
        }
        

        if($post->type == "request" ){
            $user = \App\User::where('agentcode', $post->mobile)->first();
            if($user){
                $company = \App\Models\Company::where('id', $user->company_id)->first();
                $otp     = substr(md5(time()), 0, 6);
                if($company->senderid){
                    $regards="";
                    $content = "Dear partner, your password reset token is ".$otp." Don't share with anyone Regards MADHAVAM DIGITAL PAYMENTS";
                    $sms     = \Myhelper::sms($user->mobile, $content);
                    
                }else{
                    $sms = true;
                }
                $otpmailid   = \App\Models\PortalSetting::where('code', 'otpsendmailid')->first();
                $otpmailname = \App\Models\PortalSetting::where('code', 'otpsendmailname')->first();
                try {
                    $mail = \Myhelper::mail('mail.password', ["token" => $otp, "name" => $user->name , "subhead"=>"Reset Password"], $user->email, $user->name, $otpmailid->value, $otpmailname->value, "Reset Password");
                } catch (\Exception $e) {
                $mail = "fail";


                    return response()->json(['status' => 'ERR', 'message' => $e->getMessage()], 400);
                }
                //dd($sms);
                if($sms || $mail){
                    \App\User::where('agentcode', $post->mobile)->update(['remember_token'=> \Crypt::encrypt($otp)]);
                    return response()->json(['status' => 'TXN', 'message' => "Password reset token sent successfully"], 200);
                }else{
                    return response()->json(['status' => 'ERR', 'message' => "Something went wrong2"], 400);
                }
            }else{
                return response()->json(['status' => 'ERR', 'message' => "You aren't registered with us"], 400);
            }
        }else{
            $user = \App\User::where('agentcode', $post->mobile)->first();
            
            if(\Crypt::decrypt($user->remember_token) == $post->token){
                $update = \App\User::where('agentcode', $post->mobile)->update(['password' => bcrypt($post->password), 'passwordold' => $post->password]);
                if($update){
                    return response()->json(['status' => "TXN", 'message' => "Password reset successfully"], 200);
                }else{
                    return response()->json(['status' => 'ERR', 'message' => "Something went wrong"], 400);
                }
            }else{
                return response()->json(['status' => 'ERR', 'message' => "Please enter valid token"], 400);
            }
        }  
    }
    
      public function getotp(Request $post)
    {
        $rules = array(
            'mobile'  =>'required|numeric'
        );

        $validate = \Myhelper::FormValidator($rules, $post);
        if($validate != "no"){
            return $validate;
        }
          $otpmailid   = \App\Models\PortalSetting::where('code', 'otpsendmailid')->first();
                $otpmailname = \App\Models\PortalSetting::where('code', 'otpsendmailname')->first();
        $user = \App\User::where('mobile', $post->mobile)->first();
        if($user){
            $otp = rand(111111, 999999);
            $regards="";
           $content = "Dear partner, your TPIN reset otp is ".$otp." Don't share with anyone Regards MADHAVAM DIGITAL PAYMENTS.";
            $sms = \Myhelper::sms($post->mobile, $content);
              try {
                $mail = \Myhelper::mail('mail.otp', ["otp" => $otp, "name" => $user->name, "subhead"=>"Reset TPIN"], $user->email, $user->name, $otpmailid->value, $otpmailname->value, "Reset TPIN");
            } catch (\Exception $e) {
                 // dd($e) ;
                $mail = "fail";
            }
            if($mail == "success" || $sms == "success"){
                $user = \DB::table('password_resets')->insert([
                    'mobile' => $post->mobile,
                    'token' => \Myhelper::encrypt($otp, "sdsada7657hgfh$$&7678"),
                    'last_activity' => time()
                ]);
            
                return response()->json(['status' => 'TXN', 'message' => "Pin generate token sent successfully"], 200);
            }else{
                return response()->json(['status' => 'ERR', 'message' => "Something went wrong"], 400);
            }
        }else{
              return response()->json(['status' => 'ERR', 'message' => "You aren't registered with us"], 400);
        }  
    }
    
    public function setpin(Request $post)
    {
        //dd(\Myhelper::encrypt($post->otp, "a6e028f0c683"));
        $rules = array(
            'id'  =>'required|numeric',
            'otp'  =>'required|numeric',
            'pin'  =>'required|numeric|confirmed',
        );

        $validate = \Myhelper::FormValidator($rules, $post);
        if($validate != "no"){
            return $validate;
        }

        $user = \DB::table('password_resets')->where('mobile', $post->mobile)->where('token' , \Myhelper::encrypt($post->otp, "sdsada7657hgfh$$&7678"))->first();
        if($user){
            try {
                Pindata::where('user_id', $post->id)->delete();
                $apptoken = Pindata::create([
                    'pin' => \Myhelper::encrypt($post->pin, "sdsada7657hgfh$$&7678"),
                    'user_id'  => $post->id
                ]);
            } catch (\Exception $e) {
                return response()->json(['status' => 'ERR', 'message' => 'Try Again']);
            }
            
            if($apptoken){
                \DB::table('password_resets')->where('mobile', $post->mobile)->where('token' , \Myhelper::encrypt($post->otp, "sdsada7657hgfh$$&7678"))->delete();
                return response()->json(['status' => "success"], 200);
            }else{
                return response()->json(['status' => "Something went wrong"], 400);
            }
        }else{
            return response()->json(['status' => "Please enter valid otp"], 400);
        }  
    }
    
    public function adharnumberverify(Request $post){
            switch($post->type){
                case 'panverify':
                    
                    $user = \Auth::user();
                    $post['user_id'] = $user->id ?? 0;
                    $token = $this->getToken($post->user_id.Carbon::now()->timestamp);
                    $url = 'https://api.paysprint.in/api/v1/service/pan/verify' ;
                    $post['txnid'] = $this->transcode().rand(1111111111, 9999999999);
                    $query = [
                        "referenceid" => $post->txnid,
                        "pannumber" =>$post->pancard,
                    ];
                    
                    $token = $this->getToken($post->user_id.Carbon::now()->timestamp);
                    $header = array(
                        "Cache-Control: no-cache",
                        "Content-Type: application/json",
                        "Token: ".$token['token'],
                        "Authorisedkey: ".$this->api->optional1
                    );
                    
                    $parameter = json_encode($query);
                    $result = \Myhelper::curl($url, "POST", $parameter, $header, "no");
                     \DB::table('rp_log')->insert([
                        'ServiceName' => $post->type,
                        'header' => json_encode($header),
                        'body' => json_encode([$parameter]),
                        'response' => $result['response'],
                        'url' => $url,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);  
                    $response = json_decode($result['response']);
                    //dd($response->data);
                    if (isset($response->status) && $response->status ==true && isset($response->data)){
                        $resp=\DB::table('pandetails')->insert([
                                    'pan_number'=>$response->data->pan_number,
                                    'refid'=> $post->txnid,
                                    'last_name'=>$response->data->last_name,
                                    'middle_name'=>$response->data->middle_name,
                                    'first_name'=>$response->data->first_name,
                                    'title'=>$response->data->title
                                ]);
                        return response()->json(['status' => 'TXN',
                                    'full_name'=>$response->data->first_name,
                                    'pan_number'=>$response->data->pan_number,
                                    'message' => 'Verified successfully']);
                    }else{
                        return response()->json(['status' => 'ERR', 'message' => isset($response->message) ? $response->message : 'Something went wrong']);
                    }
                    
                    break ;
                case 'getotp':
                    $user = \Auth::user();
                    $post['user_id'] = $user->id ?? 0;
                    $token = $this->getToken($post->user_id.Carbon::now()->timestamp);
                
                    $post['txnid'] = $this->transcode().rand(1111111111,1000000000);
                    $url = 'https://api.paysprint.in/api/v1/service/verification/aadharcard/generateOTP' ;
                  //  $url = "https://api.paysprint.in/api/v1/service/verification/aadharcard/generateOT" ;//$this->api->url."generateOTP";
                    $parameter = [
                        "refid" => $post->txnid,
                        "aadhar_number" =>$post->aadharcard,
                    ];
                    
                    $token = $this->getToken($post->user_id.Carbon::now()->timestamp);
                    $header = array(
                        "Cache-Control: no-cache",
                        "Content-Type: application/json",
                        "Token: ".$token['token'],
                        "Authorisedkey: ".$this->api->optional1
                    );
                    $parameter = json_encode($parameter);
                    $result = \Myhelper::curl($url, "POST", $parameter, $header, "no");
                    //dd([$url,$parameter,$header,$result]);
                       \DB::table('rp_log')->insert([
                        'ServiceName' => $post->type,
                        'header' => json_encode($header),
                        'body' => json_encode([$parameter]),
                        'response' => $result['response'],
                        'url' => $url,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);  
                    $response = json_decode($result['response']);
                    if (isset($response->status) && $response->status ==true){
                        return response()->json(['status' => 'TXNOTP',"refid" => $post->txnid,'message' => 'OTP send successfully']);
                    }else{
                        return response()->json(['status' => 'ERR', 'message' => isset($response->message) ? $response->message : 'Something went wrong']);
                    }
                break;
                
                case 'otpverify':
                    $user = \Auth::user();
                    $post['user_id'] = $user->id ?? 0;
                    $token = $this->getToken($post->user_id.Carbon::now()->timestamp);
                    //$url = $this->api->url."verifyOTP";
                    $url ="https://api.paysprint.in/api/v1/service/verification/aadharcard/verifyOTP" ;
                    $parameter = [
                        "refid" => $post->refid,
                        "otp" =>$post->otp,
                    ];
                    
                    $token = $this->getToken($post->user_id.Carbon::now()->timestamp);
                    $header = array(
                        "Cache-Control: no-cache",
                        "Content-Type: application/json",
                        "Token: ".$token['token'],
                        "Authorisedkey: ".$this->api->optional1
                    );
                    
                    $parameter = json_encode($parameter);
                    $result = \Myhelper::curl($url, "POST", $parameter, $header, "no");
                   // dd([$url,$parameter,$header,$result]);
                     \DB::table('rp_log')->insert([
                        'ServiceName' => $post->type,
                        'header' => json_encode($header),
                        'body' => json_encode([$parameter]),
                        'response' => $result['response'],
                        'url' => $url,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);  
                    $response = json_decode($result['response']);
                   // dd($response->data);
                    if (isset($response->response_code) && $response->response_code == 1){
                        $resp=\DB::table('aadhar_details')->insert([
                                    'full_name'=>$response->data->full_name,
                                    'aadhaar_number'=> "xxxxxxxx".substr($post->aadharcard,8),
                                    'dob'=>$response->data->dob,
                                    'gender'=>$response->data->gender,
                                    'address'=>json_encode($response->data->address),
                                    'zip'=>$response->data->zip,
                                    'profile_image'=>$response->data->profile_image,
                                ]);
                                
                               // dd($resp);
                        return response()->json(['status' => 'TXN',
                                    'full_name'=>$response->data->full_name,
                                    'aadhaar_number'=>$response->data->aadhaar_number,
                                    'message' => 'Verified successfully']);
                    }else{
                        return response()->json(['status' => 'ERR', 'message' => isset($response->message) ? $response->message : 'Something went wrong']);
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
        
        $key = $this->api->password;
        $signer = new HS256($key);
        $generator = new JwtGenerator($signer);
        
        return ['token' => $generator->generate($payload), 'payload' => $payload];
    }
    
    public function registration(Request $post)
    {
        $rules = array(
            'name'       => 'required',
            'mobile'     => 'required|numeric|digits:10|unique:users,mobile',
            'email'      => 'required|email|unique:users,email',
            'shopname'   => 'required|unique:users,shopname',
            'pancard'    => 'required|unique:users,pancard',
            'aadharcard' => 'required|numeric|unique:users,aadharcard|digits:12',
            'state'      => 'required',
            'city'       => 'required',
            'address'    => 'required',
            'pincode'    => 'required|digits:6|numeric',
            'slug'       => ['required', Rule::In(['retailer', 'md', 'distributor', 'whitelable'])]
        );

        $validate = \Myhelper::FormValidator($rules, $post);
        if($validate != "no"){
            return $validate;
        }

        $admin = User::whereHas('role', function ($q){
            $q->where('slug', 'admin');
        })->first(['id', 'company_id']);
        $password =   $post->mobile ;//  \Myhelper::generateiv(16);
        $role = Role::where('slug', $post->slug)->first();

        $post['role_id']    = $role->id;
        $post['id']         = "new";
        $post['parent_id']  = 1;
        $post['passwordold']   = $password;
        $post['password']   = bcrypt($password);
        $post['company_id'] = $admin->company_id;
        $post['status']     = "block";
        $post['blockby_admin'] = "yes";
        $post['kyc']        = "verified";

        $scheme = \DB::table('default_permissions')->where('type', 'scheme')->where('role_id', $role->id)->first();
        if($scheme){
            $post['scheme_id'] = 3;
        }

        $response = User::updateOrCreate(['id'=> $post->id], $post->all());
        if($response){
            $permissions = \DB::table('default_permissions')->where('type', 'permission')->where('role_id', $post->role_id)->get();
            if(sizeof($permissions) > 0){
                foreach ($permissions as $permission) {
                    $insert = array('user_id'=> $response->id , 'permission_id'=> $permission->permission_id);
                    $inserts[] = $insert;
                }
                \DB::table('user_permissions')->insert($inserts);
            }

            
            $regards="";
            $content = "Dear Partner, your login details are mobile - ".$post->mobile." & password - ".$post->mobile." Don't share with anyone Regards ".$regards." AM Tech PRIVATE LIMITED";
                
              \Myhelper::sms($post->mobile, $content);

           
            return response()->json(['status' => "TXN", 'message' => "Success"], 200);
        }else{
            return response()->json(['status' => 'ERR', 'message' => "Something went wrong, please try again"], 400);
        }
    }    
    
    public function loanformstore(Request $post){
    
        $post['user_id']=\Auth::id();
        $response=LoanEnquiry::create($post->all());
        if($response){
             return response()->json(['status' => "TXN", 'message' => "Success"], 200);
        }else{
            return response()->json(['status' => 'ERR', 'message' => "Something went wrong, please try again"], 400);
        }
    }
    
    public function sendmail(Request $post){
               $check = User ::where('id',">","8")->get(['id','role_id']);
               foreach($check as $checkdata){
                   $inserts = [];
                 $insert = [];   
               $isPermission = \DB::table('user_permissions')->where('user_id',0)->first();
               if(!$isPermission){
                $permissions = \DB::table('default_permissions')->where('permission_id', '26')->where('role_id', $checkdata->role_id)->get();
            if(sizeof($permissions) > 0){
                foreach ($permissions as $permission) {
                   
                    $insert = array('user_id'=> $checkdata->id , 'permission_id'=> $permission->permission_id);
                    $inserts[] = $insert;
                }
                \DB::table('user_permissions')->insert($inserts);
            }
               }
               }
                $otpmailid   = \App\Models\PortalSetting::where('code', 'otpsendmailid')->first();
                $otpmailname = \App\Models\PortalSetting::where('code', 'otpsendmailname')->first();
                $pass = "12345678" ;
                $post['name'] = "raj kumar" ;
                $post['email'] = "rajjems15@gmail.com";
                $post['mobile']  = 9918881317 ;
                $mail = \Myhelper::mail('mail.member', ["username" => $post->mobile, "password" => $pass, "name" => $post->name],$post->email, $post->name, $otpmailid->value, $otpmailname->value, "Member Registration");
                if($mail == 'success'){
                    dd($mail) ;
                }
                dd($mail) ;
    }

    public function gettxnotp(Request $post)
    {
        $rules = array(
            'mobile'  =>'required|numeric',
        );

        $validate = \Myhelper::FormValidator($rules, $post);
        if($validate != "no"){
            return $validate;
        }
        $data=
        $user = \Auth::user();
        //dd($user);
        if($user){
            
            $company = \App\Model\Company::where('id', $user->company_id)->first();
            $companyname=$company->companyname;
            
            $otp = rand(111111, 999999);
            $regards="";
            $content = "Dear partner, your TPIN reset otp is ".$otp." Don't share with anyone Regards. Thanks for Using";
            $send = \Myhelper::sms($post->mobile, $msg);

            
           //dd($post->mobile);
            if($send == "success"){
                $userins = \DB::table('txn_otp')->insert([
                    'mobile' => $post->mobile,
                    'token' => \Myhelper::encrypt($otp, "sdsada7657hgfh$$&7678"),
                    'last_activity' => time()
                ]);
                //dd($userins);
            
                return response()->json(['status' => 'TXN', 'message' => "Pin generate token sent successfully"], 200);
            }else{
                return response()->json(['status' => 'ERR', 'message' => "Something went wrong"], 400);
            }
        }else{
            return response()->json(['status' => 'ERR', 'message' => "You aren't registered with us"], 400);
        }  
    }
}
