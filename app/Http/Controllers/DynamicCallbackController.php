<?php

namespace App\Http\Controllers;

use App\Models\IntegrationCallback;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;

class DynamicCallbackController extends Controller
{
    //
    public function dynamicCallback(Request $request, $type)
    {
        \DB::table('microlog')->insert(['product'=>'recharge','response'=>json_encode($request->all())]);
        $getCallbackVal = IntegrationCallback::where('baseurl', $type)->first();
        if ($getCallbackVal) {

            $status = $request[$getCallbackVal->status];
            $failedVal = explode(',', $getCallbackVal->failed);
            $successVal = explode(',', $getCallbackVal->success);
            $pendingVal = explode(',', $getCallbackVal->pending);

            $getDataFromDB = Report::where('txnid', $request[$getCallbackVal->payid])->first();
            if (!$getDataFromDB){
                $getDataFromDB = Report::where('payid', $request[$getCallbackVal->payid])->first();
            }
            if ($getDataFromDB) {
                if (in_array($status, $successVal)) {
                    $update['status'] = "success";
                    $update['payid'] = @$request[$getCallbackVal->txnid];
                    $update['refno'] = @$request[$getCallbackVal->refno];
                    $update['option1'] = @$request[$getCallbackVal->refno];
                    $update['description'] = @$request[$getCallbackVal->message];
                } elseif ((in_array($status, $failedVal))) {
                    $update['status'] = "failed";
                    $update['payid'] = isset($request[$getCallbackVal->txnid]) ? $request[$getCallbackVal->txnid] : "failed";
                    $update['refno'] = @$request[$getCallbackVal->message];
                    $update['option1'] = @$request[$getCallbackVal->refno];
                    $update['description'] = @$request[$getCallbackVal->message];
                } elseif ((in_array($status, $pendingVal))) {
                    $update['status'] = "pending";
                    $update['payid'] = isset($request[$getCallbackVal->txnid]) ? $request[$getCallbackVal->txnid] : "pending";
                    $update['refno'] = isset($request[$getCallbackVal->refno]) ? $request[$getCallbackVal->refno] : "pending";
                    $update['option1'] = @$request[$getCallbackVal->refno];
                    $update['description'] = @$request[$getCallbackVal->message];
                }

                if (@$update['status'] == "success") {
                    Report::where('id', $getDataFromDB->id)->update($update);
                    \Myhelper::commission($getDataFromDB);
                } elseif(@$update['status'] == "failed")  {
                    User::where('id', $getDataFromDB->id)->increment('mainwallet', $getDataFromDB->amount - $getDataFromDB->profit);
                    Report::where('id', $getDataFromDB->id)->update($update);
                }
                
                return response()->json(['success'], 200);
            }

        }
    }
}