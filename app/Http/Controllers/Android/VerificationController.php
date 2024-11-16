<?php

namespace App\Http\Controllers\Android;

use App\Http\Controllers\Controller;
use App\Models\Api;
use App\Models\Provider;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class VerificationController extends Controller 
{
    //
    public $api;

    public function __construct()
    {
        $this->api = Api::where('code', 'runpaisa_validate')->first();

    }
    public function accountVerification(Request $post)
    {
        $rules = array(
            'beneifsc' => "required",
            'beneaccount' => "required|numeric|digits_between:6,20",
            'user_id' => "required|numeric",
            "apptoken" => "required"
        );


        $validator = Validator::make($post->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['statuscode' => 'ERR',  'message' => $validator->errors()->first()]);
        }

        // $getUser = DB::table('users')->where('id', $post->user_id)->first();

        $user = User::where('id', $post->user_id)->first();



        if ($post->user_id == null || !$user) {
            return response()->json(['statuscode' => "ERR", "message" => "User Not Found"]);
        }




        $api = Api::where('code', 'runpaisa_validate')->first();
        $url = $api->optional2 . "/account";
        $post['amount'] = 1;
        $provider = Provider::where('recharge1', 'dmt1accverify')->first();
        $post['charge'] = \Myhelper::getCommission($post->amount, $user->scheme_id, $provider->id, $user->role->slug);
        $post['provider_id'] = $provider->id;
        if ($user->mainwallet < $post->amount + $post->charge) {
            return response()->json(["statuscode" => "IWB",  'message' => 'Low balance, kindly recharge your wallet.'], 400);
        }
        $parameter["account"] = $post->beneaccount;
        $parameter["ifsc"] = $post->beneifsc;
        do {
            $post['txnid'] = $this->transcode() . rand(1111111111, 9999999999);
        } while (Report::where("txnid", "=", $post->txnid)->first() instanceof Report);
        $parameter["clientrefno"] = $post->txnid;
        $token = $this->getRunpaisaToken();
        // dd($token);
        $header = array(
            'Content-Type:multipart/form-data',
            'client_id: ' . $api->optional1,
            'token:' . $token
        );

        // $checkAlreadyverifiedAccount = DB::table('reports')->where('number', $post->beneaccount)->whereBetween('created_at', [Carbon::now()->subDays(90)->format('Y-m-d H:i:s'), Carbon::now()->format('Y-m-d H:i:s')])->first();


        // if ($checkAlreadyverifiedAccount) {
        //     $namebenefcry = $checkAlreadyverifiedAccount->option2;
        //     $result['response'] = json_encode([
        //         "STATUS" => "SUCCESS",
        //         "BENEFICIARY_NAME" => $namebenefcry
        //     ]);

        // } else {
        $result = \Myhelper::curl($url, "POST", $parameter, $header, "yes", 'App\Models\Report', '0');
        // }

        $response = json_decode($result['response']);


        if (isset($response->STATUS) && $response->STATUS == 'SUCCESS' && isset($response->BENEFICIARY_NAME) && $response->BENEFICIARY_NAME != "") {
            $insert = [
                'api_id' => $this->api->id,
                'provider_id' => $post->provider_id,
                'option1' => $user->name,
                'mobile' => $user->mobile,
                'number' => $post->beneaccount,
                'option2' => isset($response->BENEFICIARY_NAME) ? $response->BENEFICIARY_NAME : @$post->benename,
                // 'option3' => $->benebank,
                'option4' => $post->beneifsc,
                'txnid' => $post->txnid,
                'refno' => isset($response->UTRN) ? $response->UTRN : "none",
                'amount' => $post->amount,
                'charge' => $post->charge,
                'remark' => "Account Verification",
                'status' => 'success',
                'user_id' => $user->id,
                'credit_by' => $user->id,
                'product' => 'dmt',
                'balance' => $user->mainwallet,
                'description' => $user->mobile,
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
        return response()->json($output);

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
}