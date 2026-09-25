<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Models\Mahaagent;
use App\Models\Api;
use App\Models\Report;
use App\Models\Utiid;
use App\Models\Role;
use App\Models\Companydata;
use App\Models\Provider;
use App\Models\Microatmreport;
use App\Models\Aepsreport;
use App\Models\Securedata;
use App\Models\Pindata;
use App\Models\Packagecommission;
use App\Models\Commission;
use Carbon\Carbon;
use App\Models\LoanEnquiry;
use App\Models\Fingagent;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use MiladRahimi\Jwt\Cryptography\Algorithms\Hmac\HS256;
use MiladRahimi\Jwt\JwtGenerator;

use function PHPUnit\Framework\isEmpty;

class UserController extends Controller
{
   


    public function registration(Request $post)
    {
       
        $rules = array(
            'name' => 'required',
            'mobile' => 'required|numeric|digits:10|unique:users,mobile',
            'email' => 'required|email|unique:users,email',
            'shopname' => 'required|unique:users,shopname',
            'pancard' => 'required|unique:users,pancard',
            'aadharcard' => 'required|numeric|unique:users,aadharcard|digits:12',
            'state' => 'required',
            'city' => 'required',
            'address' => 'required',
            'pincode' => 'required|digits:6|numeric'
           
        );

        $validate = \Myhelper::FormValidator($rules, $post);
        if ($validate != "no") {
            return $validate;
        }

        $admin = User::whereHas('role', function ($q) {
            $q->where('slug', 'admin');
        })->first(['id', 'company_id']);
        $insertuser = $post->all();
        $role = Role::where('slug', 'retailer')->first();
         $roleprearr = ['admin'=>'VBAD','apiuser'=>'VBAP','whitelable'=>'VBW','md'=>'VBMD','distributor'=>'VBD','retailer'=>'VBRT','subadmin'=>'VBSA'];
        $code = $roleprearr[$role->slug];
        $insertuser['agentcode']  = $code.str_pad(mt_rand(100000, 999999), 6, '0', STR_PAD_LEFT);
        $insertuser['role_id'] = $role->id;
        $insertuser['id'] = "new";
        $insertuser['parent_id'] = $admin->id;
        $insertuser['password'] = bcrypt('12345678');
        $insertuser['company_id'] = $admin->company_id;
        $insertuser['status'] = "active";
        $insertuser['kyc'] = "verified";

        $scheme = \DB::table('default_permissions')->where('type', 'scheme')->where('role_id', $role->id)->first();
        if ($scheme) {
            $insert['scheme_id'] = $scheme->permission_id;
        }

        $response = User::updateOrCreate(['id' => $post->id], $insertuser);
        if ($response) {
            $permissions = \DB::table('default_permissions')->where('type', 'permission')->where('role_id', $post->role_id)->get();
            if (sizeof($permissions) > 0) {
                foreach ($permissions as $permission) {
                    $insert = array('user_id' => $response->id, 'permission_id' => $permission->permission_id);
                    $inserts[] = $insert;
                }
                \DB::table('user_permissions')->insert($inserts);
            }

            try {
                $regards = "";
                $content = "Dear Partner, your login details are mobile - " . $post->mobile . " & password - 12345678 Don't share with anyone Regards " . $regards . " LCO FINTECH(OPC) PRIVATE LIMITED";
                \Myhelper::sms($post->mobile, $content);

                $otpmailid = \App\Models\PortalSetting::where('code', 'otpsendmailid')->first();
                $otpmailname = \App\Models\PortalSetting::where('code', 'otpsendmailname')->first();

                $mail = \Myhelper::mail('mail.member', ["username" => $post->mobile, "password" => "12345678", "name" => $post->name], $post->email, $post->name, $otpmailid, $otpmailname, "Member Registration");
            } catch (\Exception $e) {
            }

            return response()->json(['status' => "TXN", 'message' => "User Registered Successfully"]);
        } else {
            return response()->json(['status' => 'ERR', 'message' => "Something went wrong, please try again"], 400);
        }
    }
    
     public function getbalance(Request $post)
    {
        $rules = array(
           'token'=> 'required'
           
        );

        $validate = \Myhelper::FormValidator($rules, $post);
        if ($validate != "no") {
            return $validate;
        }

        $user = User::where('api_token', $post->token)->with(['role'])->first();
        if (!$user) {
            return response()->json(['status' => 'ERR', 'message' => "Invalid api token"]);
        }
        
        $userdat['mainwallet'] = $user->mainwallet;
        
        return response()->json(['status' => 'TXN', 'message' => 'Balance Fetched Successfully', 'userdata' => $userdat]);
    }
    
     public function creditwallet(Request $post)
    {
        $rules = array(
           
            'token' => 'required',
            'amount' => 'required|numeric',
        );

        $validate = \Myhelper::FormValidator($rules, $post);
        if ($validate != "no") {
            return $validate;
        }

        $user = User::where('api_token', $post->token)->with(['role'])->first();
        if (!$user) {
            return response()->json(['status' => 'ERR', 'message' => "Invalid api token"]);
        }
        do {
            $post['txnid'] = $this->transcode().rand(1111111111, 9999999999);
        } while (Report::where("txnid", "=", $post->txnid)->first() instanceof Report); 
        
         $insert = [
                'number'    => $user->mobile,
                'mobile'    => $post->mobile,
                'provider_id' => 0,
                'api_id'    => 0,
                'amount'    => $post->amount,
                'txnid'     => $post->txnid,
                'option1'   => $user->name,
                'status'    => 'success',
                'user_id'   => $user->id,
                'credit_by' => $user->id,
                'rtype'     => 'main',
                'via'       => 'api',
                'balance'   => $user->mainwallet,
                'closing_balance' => ($user->mainwallet - ($post->amount + $post->charge + $post->gst)),
                'trans_type'=> 'credit',
                'product'   => "creditwallet",
                
                'create_time'   => Carbon::now()->toDateTimeString()
            ];
            $report = Report::create($insert);
          User::where('id', $user->id)->increment('mainwallet', $post->amount);
        return response()->json(['status' => 'TXN', 'message' => 'Amount Added Successfully']);
    }
    
    public function debitwallet(Request $post)
    {
        $rules = array(
           
            'token' => 'required',
            'amount' => 'required|numeric',
        );

        $validate = \Myhelper::FormValidator($rules, $post);
        if ($validate != "no") {
            return $validate;
        }

        $user = User::where('api_token', $post->token)->with(['role'])->first();
        if (!$user) {
            return response()->json(['status' => 'ERR', 'message' => "Invalid api token"]);
        }
        //$post['charge'] = \Myhelper::getCommission($post->amount, $user->scheme_id, $post->provider_id, $user->role->slug);
        if($post->amount>=$user->mainwallet){
            return response()->json(['status' => 'ERR', 'message' => "Insufficent Balance"]);
        }
        do {
            $post['txnid'] = $this->transcode().rand(1111111111, 9999999999);
        } while (Report::where("txnid", "=", $post->txnid)->first() instanceof Report); 
        
         $insert = [
                'number'    => $user->mobile,
                'mobile'    => $user->mobile,
                'provider_id' => 0,
                'api_id'    => 0,
                'amount'    => $post->amount,
                'txnid'     => $post->txnid,
                'option1'   => $user->name,
                'status'    => 'success',
                'user_id'   => $user->id,
                'credit_by' => $user->id,
                'rtype'     => 'main',
                'via'       => 'api',
                'balance'   => $user->mainwallet,
                'closing_balance' => ($user->mainwallet - ($post->amount + $post->charge + $post->gst)),
                'trans_type'=> 'debit',
                'product'   => "creditwallet",
                
                'create_time'   => Carbon::now()->toDateTimeString()
            ];
            $report = Report::create($insert);
          User::where('id', $user->id)->decrement('mainwallet', $post->amount);
        return response()->json(['status' => 'TXN', 'message' => 'Amount Successfully Debited']);
    }

   

}