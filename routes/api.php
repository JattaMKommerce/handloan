<?php


use App\Http\Controllers\CallbackController;
use App\Http\Controllers\AepsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Android\FlightController;
use App\Http\Controllers\Android\BillpayController;
use App\Http\Controllers\Android\PancardController;
use App\Http\Controllers\Android\MoneyController;
use App\Http\Controllers\Android\UserController;
use App\Http\Controllers\Android\RechargeController;
use App\Http\Controllers\Android\CyrusFundController;
use App\Http\Controllers\Android\FundController;
use App\Http\Controllers\Android\InvestmentController;
use App\Http\Controllers\Android\VerificationController;
use App\Http\Controllers\Android\TransactionController;
use App\Http\Controllers\DynamicCallbackController;
use App\Http\Controllers\Android\ComplaintController;
use App\Models\Investment;
use GuzzleHttp\Middleware;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Android\LicBillpayController;
use App\Http\Controllers\Android\MatmController;
use App\Http\Controllers\Android\RaepsController;
use App\Http\Controllers\Android\FingpayController;
use App\Http\Controllers\PdmtController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::any('callbacks/payouts/m2money/payout',[CallbackController::class,'m2MoneyCallback']);
Route::any('paysprint/agent/onboard', [CallbackController::class, 'paysprintOnboard']);
Route::group(['prefix' => 'callback/update'], function () {
  Route::any('{api}', [CallbackController::class, 'callback']);
});
Route::group(['prefix'=> 'callback/update/recharge'], function() {
    Route::any('branchx', [CallbackController::class, 'branchxCallback']);
    Route::any('{api}', [CallbackController::class, 'recharge']);
});
Route::group(['prefix' => 'checkaeps'], function () {
  Route::any('icici/initiate', [AepsController::class, 'iciciaepslog']);
  Route::any('icici/update', [AepsController::class, 'iciciaepslogupdate'])->middleware('transactionlog:aeps');
});

// dynamic recharge callbacks

Route::any('callback/{type}', [DynamicCallbackController::class,'dynamicCallback']);

Route::any('rkbbps/service/update/callback', [CallbackController::class, 'rkbbps']);

Route::any('paysprint/service/update/callback', [CallbackController::class, 'paysprintcallback']);
Route::any('runpaisa/callback/runpaisaPg', [CallbackController::class, 'runpaisaPg']);
Route::any('android/getroles', [UserController::class, 'getroles']);
Route::any('android/secheme/list', [UserController::class, 'getroles']);
Route::any('getbal/{token}', [ApiController::class, 'getbalance']);
Route::any('getip', [ApiController::class, 'getip']);
Route::any('ambikarechargeupdate/callback', [CallbackController::class, 'ambikarechargeupdate']);

// /*Recharge Api*/
// Route::any('getprovider', [Api\RechargeController::class,'getProvider']);
// Route::any('recharge/pay', [Api\RechargeController::class,'payment'])->middleware('transactionlog:recharge');
// Route::any('recharge/status', [Api\RechargeController::class,'status']);


Route::any('android/employeelist', [UserController::class, 'getemployeelist']);
// Route::any('android/contactpost', 'UserController@contactpost')->name('contactpost');

/*Android App Apis*/
Route::any('android/slider', [UserController::class, 'slider']);
Route::any('android/auth/user/register', [UserController::class, 'registration']);
Route::any('android/auth', [UserController::class, 'login']);
Route::any('android/auth/logout', [UserController::class, 'logout']);
Route::any('android/auth/reset/request', [UserController::class, 'passwordResetRequest']);
Route::any('android/auth/reset', [UserController::class, 'passwordReset']);
Route::any('android/auth/password/change', [UserController::class, 'changepassword']);

// Profile Android 
Route::any('android/getstate', [UserController::class, 'getState']);
Route::any('android/auth/profile/change', [UserController::class, 'changeProfile']);

Route::any('android/getbalance', [UserController::class, 'getbalance']);
Route::any('android/aeps/initiate', [UserController::class, 'aepsInitiate']);
Route::any('android/aeps/status', [UserController::class, 'aepsStatus']);
Route::any('android/secure/microatm/initiate', [UserController::class, 'microatmInitiate'])->middleware('transactionlog:matm');
Route::any('android/secure/microatm/update', [UserController::class, 'microatmUpdate'])->middleware('transactionlog:amtmupdate');

Route::any('android/transaction', [TransactionController::class, 'transaction']);
Route::post('android/fundrequest', [CyrusFundController::class, 'transactionRunpaisa'])->middleware('transactionlog:fund');
// Route::any('android/upi/merchent', [Android\FundController::class,'createvpa']);
Route::any('android/tpin/getotp', [UserController::class, 'getotp']);
Route::any('android/tpin/generate', [UserController::class, 'setpin']);
// Route::any('android/fundrequest/cyrus', [Android\CyrusFundController::class,'transaction'])->middleware('transactionlog:fund');
Route::post('android/fundrequest/runpaisa', [CyrusFundController::class, 'transactionRunpaisa'])->middleware('transactionlog:fund');
Route::post('android/transaction/status', [TransactionController::class, 'transactionStatus']);


/*Recharge Android Api*/

Route::any('android/recharge/providers', [RechargeController::class, 'providersList']);
Route::any('android/recharge/pay', [RechargeController::class, 'transaction']);
Route::post('android/recharge/status', [RechargeController::class, 'statusCheck']);
Route::any('android/recharge/getplan', [RechargeController::class, 'getplan']);
// Route::any('android/recharge/roffer', 'Android\RechargeController@roffer');
// Route::any('android/recharge/getoperator', 'Android\RechargeController@operatorinfo');
// Route::any('android/recharge/getprovider', 'Android\RechargeController@getprovider');
// Route::any('android/recharge/dthinfo', 'Android\RechargeController@getdthinfo');
// Route::any('android/recharge/dthrefersh', 'Android\RechargeController@dthrefersh');

// Route::post('android/recharge/providers', [RechargeController::class, 'providersList']);
// Route::post('android/recharge/pay', [RechargeController::class, 'transaction'])->middleware('transactionlog:recharge');
// Route::post('android/recharge/status', [RechargeController::class, 'status']);
// Route::post('android/recharge/getplan', [RechargeController::class, 'getplan']);
// // Route::post('android/recharge/getoperator', [RechargeController::class,'getoperator']);
// Route::post('android/recharge/dthinfo', [RechargeController::class, 'getdthinfo']);

/*Bill Android Api*/

Route::post('android/billpay/providers', [BillpayController::class, 'providersList']);
Route::post('android/billpay/getprovider', [BillpayController::class, 'getprovider']);
Route::post('android/billpay/transaction', [BillpayController::class, 'transaction'])->middleware('transactionlog:billpay');
Route::post('android/billpay/status', [BillpayController::class, 'status']);

/*Bill Android Api*/

Route::post('android/pancard/transaction', [PancardController::class, 'transaction'])->middleware('transactionlog:pancard');
Route::post('android/pancard/status', [PancardController::class, 'status']);

/*Bill Android Api*/

Route::any('android/dmt/transaction', [MoneyController::class, 'transaction'])->middleware('transactionlog:dmt');

Route::any('android/aepsregistration', [UserController::class, 'aepskyc']);
Route::any('android/GetState', [UserController::class, 'GetState']);
Route::any('android/GetDistrictByState', [UserController::class, 'GetDistrictByState']);
Route::any('android/bcstatus', [UserController::class, 'bcstatus']);


/*Member Create Android Api*/
Route::any('android/member/create', [UserController::class, 'addMember']);
Route::any('android/member/list', [TransactionController::class, 'transaction']);

Route::any('android/support/store', [ComplaintController::class, 'support']);
Route::any('android/complaint/store', [ComplaintController::class, 'store']);

/*Adhar verify Android Api*/
Route::any('android/aadhar/verify', [UserController::class, 'adharnumberverify']);


/*LIC Bill Android Api*/
Route::any('android/licbillpay/transaction', [LicBillpayController::class, 'lictransaction'])->middleware('transactionlog:licbillpay');
Route::any('android/licbillpay/status', [LicBillpayController::class, 'status']);

/*LIC Bill Android Api*/

/* paysprint DMT */
Route::post('android/pdmt/transaction', [PdmtController::class, 'payment']);
Route::post('android/pdmt/getbank', [PdmtController::class, 'getbank']);

//paysprint aeps api for android
Route::any('android/paysprint/onboard', [RaepsController::class, 'getkyc']);
Route::any('android/raeps/transaction', [RaepsController::class, 'trasaction']);
Route::any('android/raeps/getdata', [RaepsController::class, 'getdata']);
Route::any('android/paysprint/aeps', [RaepsController::class, 'trasaction']);


//Paysprint APIS
Route::any('android/paysprint/uti', [PancardController::class, 'payment']);
Route::any('android/getcommission', [UserController::class, 'getcommission']);

Route::any('android/paysprint/microatm/initiate', [MatmController::class, 'microatmInitiate'])->middleware('transactionlog:matm');
Route::any('android/paysprint/microatm/update', [MatmController::class, 'microatmUpdate'])->middleware('transactionlog:matm');

//Loan Enquery 
Route::any('android/loan/enquery', [UserController::class, 'loanenquiery']);

Route::post('android/profile/update', [UserController::class, 'updateprofile']);

Route::any('android/iaeps/transaction', [FingpayController::class, 'transaction'])->middleware('transactionlog:faeps');
//Route::any('android/faeps/transaction', 'Android\FingpayController@transaction')->middleware('transactionlog:faeps');
// Route::group(['prefix' => 'iaeps'], function(){
//      Route::post('transaction','Api\FingpayController@transaction');
//     // Route::post('cash/deposit/transaction', 'Api\FingpayController@cashdeposittransaction');
//     Route::post('matm/transaction','Api\FingpayController@matmtransaction');
//     Route::post('matm/transaction/update','Api\FingpayController@microatmUpdate');
// });

// Route::any('android/secure/microatm/initiate', [UserController::class, 'fmicroatmInitiate'])->middleware('transactionlog:fmicroatm');
// Route::any('android/secure/microatm/update', [UserController::class, 'fmicroatmUpdate'])->middleware('transactionlog:fmicroatmupd');

// Route::group(['prefix' => 'iaeps'], function () {
//   Route::post('matm/transaction', [\App\Http\Controllers\Api\FingpayController::class, 'matmtransaction']);
//   Route::post('matm/transaction/update', [\App\Http\Controllers\Api\FingpayController::class, 'microatmUpdate']);
// });

Route::post('merchant/bank/payout', [\App\Http\Controllers\Api\AepsController::class, 'banksettlement']);


// investment route

Route::post('android/investment', [InvestmentController::class, 'investment']);
Route::post('android/accountverification', [VerificationController::class, 'accountVerification']);

//

Route::any('android/account/listing', [UserController::class, 'accountListing']);
Route::any('android/account/add', [UserController::class, 'addAccount']);
Route::any('android/tpin/reset', [UserController::class, 'resetTpin']);
Route::any('android/tpin/check', [UserController::class, 'userpin']);

Route::any('android/faeps/getdata', [FingpayController::class, 'getdata']);

Route::any('android/servicelist', [UserController::class, 'servicelist']);

Route::any('android/GetState', [UserController::class, 'GetState']);
Route::any('android/GetDistrictByState', [UserController::class, 'GetDistrictByState']);

Route::any('android/payout/accountStatus', [FundController::class, 'bankList']);

// Route::any('android/secure/fmicroatm/initiate', 'UserController@fmicroatmInitiate')->middleware('transactionlog:fmicroatm');
// Route::any('android/secure/fmicroatm/update', 'UserController@fmicroatmUpdate')->middleware('transactionlog:fmicroatmupd');


Route::post('android/flight', [FlightController::class, 'flight']);
Route::get('android/get/flight', [FlightController::class, 'getpdf']);

/*User App Apis*/

Route::any('user/register', [App\Http\Controllers\Api\UserController::class, 'registration']);
Route::any('user/check/balance', [App\Http\Controllers\Api\UserController::class, 'getbalance']);
Route::any('user/credit/wallet', [App\Http\Controllers\Api\UserController::class, 'creditwallet']);
Route::any('user/debit/wallet', [App\Http\Controllers\Api\UserController::class, 'debitwallet']);