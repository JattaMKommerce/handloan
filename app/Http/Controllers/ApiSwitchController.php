<?php

namespace App\Http\Controllers;

use App\Models\IntegrationCallback;
use App\Models\IntegrationComplain;
use App\Models\IntegrationStatus;
use App\Models\IntegrationOperator;
use App\Models\Integration;
use App\Models\IntegrationCircle;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Fundbank;
use App\Models\Api;
use App\Models\Provider;
use App\Models\PortalSetting;
use App\Models\Complaintsubject;
use App\Models\Link;
use App\Models\Circle;
use App\Models\Apiswitch;
use App\Models\IntegrationBalance;
use Illuminate\Support\Str;

class ApiSwitchController extends Controller
{
    public function index($type, $id = 0)
    {
        //dd($id);
        switch ($type) {
            case 'rintegration':
                if($id === 0){
                    return view("apiswitch.rintegration");
                }else{
                    $data['id'] = $id;
                    $data['api'] = Integration::find($id);
                    return view("apiswitch.recharge")->with($data);
                }
                break;

            // case 'eintegration':
            //     if($id === 0){
            //         return view("apiswitch.eintegration");
            //     }else{
            //         $data['id'] = $id;
            //         $data['api'] = Integration::find($id);
            //         return view("apiswitch.electricity")->with($data);
            //     }
            //     break;

            case 'integrationstatus':
                $data['id'] = $id;
                $data['api']    = Integration::find($id);
                $data['status'] = IntegrationStatus::where('api_id', $id)->first();
                return view("apiswitch.status")->with($data);
                break;

            case 'integrationcomplain':
                $data['id'] = $id;
                $data['api']    = Integration::find($id);
                $data['status'] = IntegrationComplain::where('api_id', $id)->first();
                return view("apiswitch.complain")->with($data);
                break;

            case 'integrationbalance':
                $data['id'] = $id;
                $data['api']    = Integration::find($id);
                $data['status'] = IntegrationBalance::where('api_id', $id)->first();
                return view("apiswitch.balance")->with($data);
                break;

            case 'integrationcallback':
                $data['id'] = $id;
                $data['api']    = Integration::find($id);

                do {
                    $string = Str::random(10);
                } while (IntegrationCallback::where("baseurl", "=", $string)->first() instanceof IntegrationCallback);

                $data['callback'] = IntegrationCallback::where('api_id', $data['api']->id)->first();
                
                if(!$data['callback']){
                    $data['callback'] = IntegrationCallback::updateOrCreate(['api_id'=> $data['api']->id], [
                        "api_id"  => $id,
                        "baseurl" => $string,
                        'type' => 'recharge'
                    ]);
                }

                return view("apiswitch.callback")->with($data);
                break;

            case 'integrationcomplaincallback':
                $data['id'] = $id;
                $data['api']    = Integration::find($id);

                do {
                    $string = str::random(10);
                } while (IntegrationCallback::where("baseurl", "=", $string)->first() instanceof IntegrationCallback);

                $data['callback'] = IntegrationCallback::where('api_id', $data['api']->id)->first();
                
                if(!$data['callback']){
                    $data['callback'] = IntegrationCallback::updateOrCreate(['api_id'=> $data['api']->id], [
                        "api_id"  => $id,
                        "baseurl" => $string,
                        "type" => "complain"
                    ]);
                }

                return view("apiswitch.callback")->with($data);
                break;

            case 'operator':
                $data['apis']      = Integration::get(['id', 'name']);
                $data['providers'] = Provider::whereIn('type', ['mobile', 'dth','recharge'])->get(['id', 'name']);
                return view("apiswitch.operator")->with($data);
                break;

            case 'circle':
                $data['apis']    = Integration::get(['id', 'name']);
                $data['circles'] = Circle::get();
                return view("apiswitch.circle")->with($data);
                break;
            
            default:
                $permission = "setup_apiswitch";
                $data['apis']   = \App\Models\Api::whereIn('type', ['recharge','recharge'])->where('status', '1')->get(['id', 'product']);
                $data['circle'] = \App\Models\Circle::get();
                $data['users']  = User::where('id', '!=', \Auth::id())->get();
                $data['providers'] = \App\Models\Provider::whereIn('type', ['mobile', 'dth','recharge'])->where('status', '1')->get(['id', 'name']);
                if (!\Myhelper::can($permission)) {
                    abort(403);
                }
                $data['type'] = $type;

                return view("apiswitch.apiswitch")->with($data);
                break;
        }
    }

    public function update(Request $post)
    {
        switch ($post->actiontype) {
            case 'apiswitch':
            case 'Integration':
            case 'operator':
            case 'circle':
            case 'integrationstatus':
                $permission = "setup_apiswitch";
                break;
        }

        if (isset($permission) && !\Myhelper::can($permission)) {
            return response()->json(['status' => "Permission Not Allowed"], 400);
        }

        switch ($post->actiontype) {
            case 'apiswitch':
                $rules = array(
                    'id' => 'required'
                );
                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors'=>$validator->errors()], 422);
                }

                if($post->has('user_id')){
                    $post['user_id'] = implode(",", $post->user_id);
                }

                $action = \App\Models\Apiswitch::updateOrCreate(['id'=> $post->id], $post->all());
                if ($action) {
                    return response()->json(['status' => "success"], 200);
                }else{
                    return response()->json(['status' => "Task Failed, please try again"], 200);
                }
                break;

            case 'Integration':
                $rules = array(
                    'name' => 'required',
                    'usernameval' => 'required',
                    'baseurl' => 'required',
                    'method' => 'required',
                    'requesttype' => 'required',
                    'username' => 'required',
                    'mobile' => 'required',
                    'operator' => 'required',
                    'amount' => 'required',
                    'txnid' => 'required',
                    'refno' => 'required',
                    'payid' => 'required',
                    'message' => 'required',
                    'status' => 'required',
                    'success' => 'required',
                    'pending' => 'required',
                    'failed' => 'required',
                );

                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors'=>$validator->errors()], 422);
                }
                $action = Integration::updateOrCreate(['id'=> $post->id], $post->all());

                if ($action){
                    $insert['name']  = $post->name;
                    $insert['product'] = $post->name;
                    $insert['url']  = $post->baseurl;
                    $insert['type']  = $post->type;
                    $insert['code']  = $action->id;
                    $action = \App\Models\Api::updateOrCreate(['code'=> $action->id], $insert);

                    return response()->json(['status' => "success"], 200);
                }else{
                    return response()->json(['status' => "Task Failed, please try again"], 200);
                }
                break;

            case 'integrationstatus':
                $rules = array(
                    'api_id' => 'required',
                    'usernameval' => 'required',
                    'baseurl' => 'required',
                    'method' => 'required',
                    'requesttype' => 'required',
                    'username' => 'required',
                    'txnid' => 'required',
                    'refno' => 'required',
                    'message' => 'required',
                    'status' => 'required',
                    'success' => 'required',
                    'pending' => 'required',
                    'failed' => 'required',
                );

                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors'=>$validator->errors()], 422);
                }
                $action = IntegrationStatus::updateOrCreate(['api_id'=> $post->api_id], $post->all());

                if ($action){
                    return response()->json(['status' => "success"], 200);
                }else{
                    return response()->json(['status' => "Task Failed, please try again"], 200);
                }
                break;

            case 'integrationcomplain':
                $rules = array(
                    'api_id' => 'required',
                    'usernameval' => 'required',
                    'baseurl' => 'required',
                    'method' => 'required',
                    'requesttype' => 'required',
                    'username' => 'required',
                    'txnid' => 'required',
                    'refno' => 'required',
                    'message' => 'required',
                    'status' => 'required',
                    'success' => 'required',
                    'pending' => 'required',
                    'failed' => 'required',
                );

                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors'=>$validator->errors()], 422);
                }
                $action = IntegrationComplain::updateOrCreate(['api_id'=> $post->api_id], $post->all());

                if ($action){
                    return response()->json(['status' => "success"], 200);
                }else{
                    return response()->json(['status' => "Task Failed, please try again"], 200);
                }
                break;

            case 'integrationbalance':
                $rules = array(
                    'api_id'  => 'required',
                    'usernameval' => 'required',
                    'baseurl' => 'required',
                    'method'  => 'required',
                    'requesttype' => 'required',
                    'username'=> 'required',
                    'balance' => 'required',
                    'status'  => 'required',
                    'success' => 'required',
                    'failed'  => 'required',
                );

                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors'=>$validator->errors()], 422);
                }
                $action = IntegrationBalance::updateOrCreate(['api_id'=> $post->api_id], $post->all());

                if ($action){
                    return response()->json(['status' => "success"], 200);
                }else{
                    return response()->json(['status' => "Task Failed, please try again"], 200);
                }
                break;

            case 'integrationcallback':
                $rules = array(
                    'api_id' => 'required',
                    'txnid' => 'required',
                    'payid' => 'required',
                    'refno' => 'required',
                    'message' => 'required',
                    'status' => 'required',
                    'success' => 'required',
                    'pending' => 'required',
                    'failed' => 'required',
                );

                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors'=>$validator->errors()], 422);
                }
                $action = IntegrationCallback::updateOrCreate(['api_id'=> $post->api_id], $post->all());

                if ($action){
                    return response()->json(['status' => "success"], 200);
                }else{
                    return response()->json(['status' => "Task Failed, please try again"], 200);
                }
                break;

            case 'operator':
                $rules = array(
                    'id' => 'required'
                );
                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors'=>$validator->errors()], 422);
                }

                $action = IntegrationOperator::updateOrCreate(['id'=> $post->id], $post->all());
                if ($action) {
                    return response()->json(['status' => "success"], 200);
                }else{
                    return response()->json(['status' => "Task Failed, please try again"], 200);
                }
                break;

            case 'circle':
                $rules = array(
                    'id' => 'required'
                );
                $validator = \Validator::make($post->all(), $rules);
                if ($validator->fails()) {
                    return response()->json(['errors'=>$validator->errors()], 422);
                }

                $action = IntegrationCircle::updateOrCreate(['id'=> $post->id], $post->all());
                if ($action) {
                    return response()->json(['status' => "success"], 200);
                }else{
                    return response()->json(['status' => "Task Failed, please try again"], 200);
                }
                break;

            case 'getcode':

                $data['url'] = $post->baseurl;
                $parameter[$post->username] = $post->usernameval;
                if($post->password){
                    $parameter[$post->password] = $post->passwordval;
                }
                $parameter[$post->mobile] = $post->number;
                $parameter[$post->operator] = $post->operator;
                $parameter[$post->txnid] = $post->txnid;
                $parameter[$post->amount] = $post->amount;
                $parameter[$post->state] = $post->state;

                if($post->other){
                    $others = explode("&", $post->other);

                    foreach ($others as $other) {
                        $param = explode("=", $other);
                        $parameter[$param[0]] = $param[1];
                    }
                }

                switch ($post->requesttype) {
                    case 'json':
                        $data['header'] = array(
                            "content-type: application/json"
                        );
                        $data['query'] = json_encode($parameter);
                        break;
                    
                    default:
                        $data['header'] = [];
                        $data['query'] = http_build_query($parameter);
                        $data['url'] = $data['url']."?".$data['query'];

                        if($post->method == "GET"){
                            $data['query'] = '';
                        }
                        break;
                }
                return response()->json(['status' => "success", "data" => $data]);
                break;

            default:
                # code...
                break;
        }
    }
}
