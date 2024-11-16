<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Circle;
use App\User;
use App\Models\Report;
use App\Models\Aepsreport;
use App\Models\Api;
use App\Models\Microatmreport;
use Carbon\Carbon;
use App\Models\Investfundreport;
use App\Models\Investment;
use App\Models\InvestmentTxn;
use App\Models\Banner;
use App\Models\Video;
use App\Models\Paymode;
use App\Models\Fundbank;

use function PHPUnit\Framework\isEmpty;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public $flight_cred;

    public function __construct()
    {
        $this->middleware('auth');
        $flight_cred = Api::where('code', 'flight')->first();
        $this->flight_cred['username'] = !empty($flight_cred->username)?$flight_cred->username:'';
        $this->flight_cred['password'] = !empty($flight_cred->password)?$flight_cred->password:'';
        $this->flight_cred['url'] = !empty($flight_cred->url)?$flight_cred->url:'';
        $this->flight_cred['ip'] = !empty($flight_cred->optional1)?$flight_cred->optional1:'';
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function comingsoon()
    {
        return view('comingsoon');
    }

    public function index(Request $post)
    {

        $fromDate = !empty($post->fromDate) ? $post->fromDate : date("Y-m-d");
        $toDate = !empty($post->toDate) ? $post->toDate : date("Y-m-d");


        if (!\Myhelper::getParents(\Auth::id())) {
            session(['parentData' => \Myhelper::getParents(\Auth::id())]);
        }

        $data['state'] = Circle::all();
        $roles = ['whitelable', 'md', 'distributor', 'retailer', 'apiuser', 'other', 'employee'];

        foreach ($roles as $role) {
            if ($role == "other") {
                $data[$role] = User::whereHas('role', function ($q) {
                    $q->whereNotIn('slug', ['whitelable', 'md', 'distributor', 'retailer', 'apiuser', 'admin', 'employee']);
                })->whereIn('kyc', ['verified'])->count(); //->whereIn('id', \Myhelper::getParents(\Auth::id()))
            } else {
                if (\Myhelper::hasRole('admin')) {
                    $data[$role] = User::whereHas('role', function ($q) use ($role) {
                        $q->where('slug', $role);
                    })->whereIn('kyc', ['verified'])->count();
                } else {
                    $data[$role] = User::whereHas('role', function ($q) use ($role) {
                        $q->where('slug', $role);
                    })->whereIn('id', \Myhelper::getParents(\Auth::id()))->whereIn('kyc', ['verified'])->count();
                }
            }
        }


        $product = [
            'recharge',
            'billpayment',
            'utipancard',
            'money',
            'dmt',
            'aeps',
            'matm',
            'commission',
            'charge'
        ];

        $slot = ['today', 'month', 'lastmonth'];
        // $txnstatus = ['success', 'pending', 'failed'];
        $txnstatus = [
            'success' => ['success'],
            'pending' => ['pending'],
            'failed' => ['failed', 'reversed']
        ];

        $statuscount = ['successCount' => ['success'], 'pendingCount' => ['pending'], 'failedCount' => ['failed', 'reversed']];

        foreach ($product as $value) {
            foreach ($txnstatus as $status => $statusVal) {

                if ($value == "aeps") {
                    if (\Myhelper::hasRole('admin')) {
                        $query = Aepsreport::whereIn('status', $statusVal);
                    } else {
                        $query = Aepsreport::whereIn('user_id', \Myhelper::getParents(\Auth::id()));
                    }
                } else if ($value == 'matm') {
                    if (\Myhelper::hasRole('admin')) {
                        $query = Microatmreport::whereIn('status', $statusVal);
                    } else {
                        $query = Microatmreport::whereIn('user_id', \Myhelper::getParents(\Auth::id()))->whereIn('status', $statusVal);
                    }
                } else {
                    if (\Myhelper::hasRole('admin')) {
                        $query = Report::whereIn('status', $statusVal);
                    } else {
                        $query = Report::whereIn('user_id', \Myhelper::getParents(\Auth::id()))->whereIn('status', $statusVal);
                    }
                }

                if ($value == "charge" || $value == "commission") {
                    $query2 = Aepsreport::whereIn('user_id', \Myhelper::getParents(\Auth::id()))->whereIn('status', $statusVal);
                }


                switch ($value) {
                    case 'recharge':
                        $query->where('product', 'recharge');
                        break;

                    case 'billpayment':
                        $query->where('product', 'billpay');
                        break;

                    case 'utipancard':
                        $query->where('product', 'utipancard');
                        break;

                    case 'money':
                        $query->where('product', 'payout');
                        break;
                    case 'dmt':
                        $query->where('product', 'dmt');
                        break;
                    case 'commission':
                        $query2->where('aepstype', 'CW')->where('rtype', 'main');
                        break;
                    case 'charge':
                        $query2->where('aepstype', 'AP')->where('rtype', 'main');
                        break;
                    case 'aeps':
                        $query->where('rtype', 'main')->whereIn('aepstype', ['CW', 'AP']);
                        break;
                }

                if ($value == "charge") {
                    $sum1 = $query2->where('status', 'success')->sum('charge');
                    $sum2 = $query->where('status', 'success')->sum('charge');
                    $data[$value][$status] = $sum1 + $sum2;
                } else if ($value == "commission") {
                    $sum1 = $query2->where('status', 'success')->sum('charge');
                    $sum2 = $query->where('status', 'success')->where('profit', ">", 0)->sum('profit');
                    $data[$value][$status] = $sum1 + $sum2;
                } else {

                    $data[$value][$status] = $query->whereBetween('created_at', [Carbon::createFromFormat('Y-m-d', $fromDate)->format('Y-m-d'), Carbon::createFromFormat('Y-m-d', $toDate)->addDay(1)->format('Y-m-d')])->sum('amount');
                }
            }


            foreach ($statuscount as $keys => $values) {

                if ($value == "aeps") {
                    if (\Myhelper::hasRole('admin')) {
                        $query = Aepsreport::whereIn('status', $values);
                    } else {
                        $query = Aepsreport::whereIn('user_id', \Myhelper::getParents(\Auth::id()))->whereIn('status', $values);
                    }
                } else if ($value == 'matm') {
                    if (\Myhelper::hasRole('admin')) {
                        $query = Microatmreport::whereIn('status', $values);
                    } else {
                        $query = Microatmreport::whereIn('user_id', \Myhelper::getParents(\Auth::id()))->whereIn('status', $values);
                    }
                } else {
                    if (\Myhelper::hasRole('admin')) {

                        $query = Report::whereIn('status', $values);
                    } else {

                        $query = Report::whereIn('user_id', \Myhelper::getParents(\Auth::id()))->whereIn('status', $values);
                    }
                }
                switch ($value) {
                    case 'recharge':
                        $query->where('product', 'recharge');
                        break;

                    case 'billpayment':
                        $query->where('product', 'billpay');
                        break;

                    case 'utipancard':
                        $query->where('product', 'utipancard');
                        break;

                    case 'money':
                        $query->where('product', 'payout');
                        break;
                    case 'aeps':
                        $query->where('rtype', 'main')->whereIn('aepstype', ['CW', 'AP']);
                }

                $data[$value][$keys] = $query->whereBetween('created_at', [Carbon::createFromFormat('Y-m-d', $fromDate)->format('Y-m-d'), Carbon::createFromFormat('Y-m-d', $toDate)->addDay(1)->format('Y-m-d')])->count();
            }
        }

        if (request()->isMethod('post')) {

            return response()->json($data);
        }

        if (\Myhelper::hasRole('admin') || \Myhelper::can('invesment_show')) {


            $invesment = InvestmentTxn::where('investment_txns.user_id', \Auth::id())->get();
            $invesmentshceme = Investment::where('status', 'active')->get();
            $responseData = [];
            if ($invesmentshceme->isNotEmpty()) {
                $count = 0;
                foreach ($invesmentshceme as $row1) {
                    $responseData[$count] = $row1;
                    $responseData[$count]["investment_id"] = @$row1->id;

                    foreach ($invesment as $row2) {
                        if ($row1->id === intval($row2->investment_id)) {
                            $responseData[$count]["invested_amount"] = @$row2->amount;
                            $responseData[$count]["allotted_no_of_share"] = @$row2->allotted_no_of_share;
                            $responseData[$count]["status"] = @$row2->status;
                            $responseData[$count]["is_investment_complete"] = @$row2->is_investment_complete;
                            continue;
                        }
                    }
                    $count++;
                }
            }

            $data['invesmentshceme'] = $responseData;
          }

        if (\Myhelper::can('investment_fund_request')) {
         
            $data['banks'] = Fundbank::where('user_id', 1)->where('status', '1')->get();
            $data['paymodes'] = Paymode::where('status', '1')->get();
            // dd($data['banks']);
        } else {
            $data['banks'] = [];
            $data['paymodes'] = [];
        }
       
        if (count($data) > 0) {
            return view('home')->with($data);
        }
        return \Response::json(['statuscode' => 'ERR', 'status' => "Permission not allowed", 'message' => "Permission not allowed"], 400);

    }

    public function getbalance()
    {
        $data['apibalance'] = 0;
        $data['downlinebalance'] = round(User::whereIn('id', array_diff(\Myhelper::getParents(\Auth::id()), array(\Auth::id())))->sum('mainwallet'), 2);
        $data['mainwallet'] = \Auth::user()->mainwallet;
        $data['microatmbalance'] = \Auth::user()->microatmbalance;
        $data['lockedamount'] = \Auth::user()->lockedamount;
        if (\Myhelper::hasRole('admin') || \Myhelper::hasRole('employee')) {
            $data['aepsbalance'] = round(User::where('id', '!=', \Auth::id())->sum('aepsbalance'), 2);
        } else {
            $data['aepsbalance'] = round(\Auth::user()->aepsbalance, 2);
        }
        if (\Myhelper::hasRole('admin')) {
            $data['investment_wallet'] = round(User::where('id', '!=', \Auth::id())->sum('investment_wallet'), 2);
        } else {
            $data['investment_wallet'] = round(\Auth::user()->investment_wallet, 2);
        }
        // $balance = $this->getFlightBalance();
        // if (\Myhelper::hasRole('admin')) {

        //     $bal = json_encode($balance['EffectiveBalance']);
        //     // dd($bal);
        //     $data['flightBalance'] = $bal ?? "";
        // }
        return response()->json($data);
    }

    public function getFlightBalance()
    {

        $req_id = "FL" . time();

        $headers = [
            'Content-Type: application/json',
        ];

        $url = $this->flight_cred['url'] . "/tradehost/TradeAPIService.svc/JSONService/GetBalance";
        // http://uat.etrav.in/tradehost/TradeAPIService.svc/JSONService/GetBalance

        $param = [
            "Auth_Header" => [
                "UserId" => $this->flight_cred['username'],
                "Password" => $this->flight_cred['password'],
                "IP_Address" => $this->flight_cred['ip'],
                "Request_Id" => $req_id,
                //$req_id,
                "IMEI_Number" => "223232223232323"
            ],
            "RefNo" => "",
            "Ticketing_Type" => "0",
            "ProductId" => "",
            "EWalletID" => "0"

        ];

        // $parRequest = str_replace(["\\\/\\\/", "\\\/", "\/", "'\'"], "/", json_encode($param));
        $result = \Myhelper::curl($url, "POST", json_encode($param), $headers, "yes", "flightGetBalance", $req_id);
        // dd([$url, "POST", json_encode($param), $headers, "yes", "flightGetBalance", $req_id ,$result]);
        $response = json_decode($result['response']);



        if ($result['code'] == 200 && $result['error'] == null) {
            if ($response->Response_Header->Error_Code == 0000 && $response->Response_Header->Status_Id == "11") {
                $data = [
                    "Status_Id" => $response->Response_Header->Status_Id,
                    "Request_Id" => $response->Response_Header->Request_Id,
                    "CreditBalance" => $response->CreditBalance,
                    "EffectiveBalance" => $response->EffectiveBalance,
                    "LienBalance" => $response->LienBalance,
                    "ODAmount" => $response->ODAmount
                ];
            } else {
                $data = [];
            }
            // $data = [];
        }
        return $data;
    }

    public function getmysendip()
    {
        $url = "http://login.securepayments.co.in/api/getip";
        $result = \Myhelper::curl($url, "GET", "", [], "no");
        // dd($result);
    }

    public function setpermissions()
    {
        $users = User::whereHas('role', function ($q) {
            $q->where('slug', '!=', 'admin');
        })->get();

        foreach ($users as $user) {
            $inserts = [];
            $insert = [];
            $permissions = \DB::table('default_permissions')->where('type', 'permission')->where('role_id', $user->role_id)->get();

            if (sizeof($permissions) > 0) {
                \DB::table('user_permissions')->where('user_id', $user->id)->delete();
                foreach ($permissions as $permission) {
                    $insert = array('user_id' => $user->id, 'permission_id' => $permission->permission_id);
                    $inserts[] = $insert;
                }
                \DB::table('user_permissions')->insert($inserts);
            }
        }
    }

    public function setscheme()
    {
        // $users = User::whereHas('role', function($q){ $q->where('slug', '!=' ,'admin'); })->get();

        // foreach ($users as $user) {
        //     $inserts = [];
        //     $insert = [];
        //     $scheme = \DB::table('default_permissions')->where('type', 'scheme')->where('role_id', $user->role_id)->first();
        //     if ($scheme) {
        //         User::where('id', $user->id)->update(['scheme_id' => $scheme->permission_id]);
        //     }
        // }

        $bcids = App\Models\Mahaagent::get(['phone1', 'id']);

        foreach ($bcids as $user) {
            $userdata = User::where('mobile', $user->phone1)->first(['id']);
            if ($userdata) {
                App\Models\Mahaagent::where('id', $user->id)->update(['user_id' => $userdata->id]);
            }
        }
    }

    public function mydata()
    {
        $data['fundrequest'] = \App\Models\Fundreport::where('credited_by', \Auth::id())->where('status', 'pending')->count();
        $data['aepsfundrequest'] = \App\Models\Aepsfundrequest::where('status', 'pending')->where('pay_type', 'manual')->count();
        $data['aepspayoutrequest'] = \App\Models\Aepsfundrequest::where('status', 'pending')->count();
        $data['member'] = \App\User::where('status', 'block')->where('kyc', 'pending')->count();
        return response()->json($data);
    }

    public function bulkSms()
    {
        $content = "Welcome to Webtalk, Username-9971702408,Password-12345678, Web: http://b2b.webtalkatmmini.com/, App: http://bit.ly/webtalkapplication Thanks-Webtalk Team";
        \Myhelper::sms("9971702308", $content);
        // $user = User::get(['id', 'mobile']);

        // foreach ($user as $value) {
        //     $content = "Welcome to Webtalk, Username-".$user->mobile.",Password-12345678, Web: http://b2b.webtalkatmmini.com/, App: http://bit.ly/webtalkapplication Thanks-Webtalk Team";
        //     \Myhelper::sms("9971702308", $content);        
        // }   
    }

    public function checkcommission(Request $post)
    {
        // $total = "6000";

        // $amount = $total;
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

        // $amounts = array_fill(0,$n,5000);
        // if(isset($x)){
        //     array_push($amounts , $x);
        // }

        // //dd($amounts);

        // foreach($amounts as $value){
        //     echo $value."<br>";
        //     continue;
        //     echo "total - ".$total."<br>";
        //     $total = $total - $value;
        // }

        \Myhelper::commission($post);
    }




    function searchdatestatics(Request $post)
    {


        $session = \Myhelper::getParents(\Auth::id());
        $product = [
            // 'recharge',
            // 'billpayment',
            // 'utipancard',
            // 'money',
            // 'aeps',
            // 'matm',
            // 'nsdlpan',
            // 'insurance',
            // 'tax',
            // 'aepsadharpay',
            'commission',
            'charge'
        ];

        $slot = ['today', 'month', 'lastmonth'];

        $statuscount = ['success' => ['success'], 'pending' => ['pending'], 'failed' => ['failed', 'reversed']];

        foreach ($product as $value) {

            if ($value == "aeps" || $value == "aepsadharpay" || $value == "nsdlaeps") {
                $query = \DB::table('aepsreports');
            } elseif ($value == "matm") {
                $query = \DB::table('microatmreports');
            } elseif ($value == "upi") {
                $query = \DB::table('upireports');
            } else {
                $query = \DB::table('reports');
            }
            if ($value == "charge" || $value == "commission") {
                $query2 = Aepsreport::whereIn('user_id', \Myhelper::getParents(\Auth::id()));
            }
            if (\Myhelper::hasRole(['retailer', 'apiuser'])) {
                $query->where('user_id', \Auth::id());
            } elseif (\Myhelper::hasRole(['admin', 'distributor', 'whitelable', 'statepartner'])) {
                $query->whereIntegerInRaw('user_id', $session);
            }

            if ((isset($post->fromdate) && !empty($post->fromdate)) && (isset($post->todate) && !empty($post->todate))) {
                if ($post->fromdate == $post->todate) {
                    $query->whereDate('created_at', '=', Carbon::createFromFormat('Y-m-d', $post->fromdate)->format('Y-m-d'));
                    $query2->whereDate('created_at', '=', Carbon::createFromFormat('Y-m-d', $post->fromdate)->format('Y-m-d'));
                } else {
                    $query->whereBetween('created_at', [Carbon::createFromFormat('Y-m-d', $post->fromdate)->format('Y-m-d'), Carbon::createFromFormat('Y-m-d', $post->todate)->addDay(1)->format('Y-m-d')]);
                    $query2->whereBetween('created_at', [Carbon::createFromFormat('Y-m-d', $post->fromdate)->format('Y-m-d'), Carbon::createFromFormat('Y-m-d', $post->todate)->addDay(1)->format('Y-m-d')]);
                }
            }



            switch ($value) {
                case 'recharge':
                    $query->where('product', 'recharge');
                    break;

                case 'billpayment':
                    $query->where('product', 'billpay');
                    break;

                case 'utipancard':
                    $query->where('product', 'utipancard');
                    break;

                case 'money':
                    $query->where('product', 'payout');
                    break;

                case 'insurance':
                    $query->where('product', 'insurance');
                    break;

                case 'aepsadharpay':
                    $query->where('transtype', 'transaction')->where('rtype', 'main')->where('aepstype', 'AP');
                    break;

                case 'nsdlaeps':
                    $query->where('transtype', 'transaction')->where('rtype', 'main')->where('api_id', '22');
                    break;

                case 'aeps':
                    $query->where('transtype', 'transaction')->where('rtype', 'main');
                    break;

                case 'matm':
                    $query->where('transtype', 'transaction')->where('rtype', 'main');
                    break;
                case 'commission':
                    $query2->where('aepstype', 'CW')->where('rtype', 'main');
                    break;
                case 'charge':
                    $query2->where('aepstype', 'AP')->where('rtype', 'main');
                    break;
            }

            if ($value == "charge") {
                $sum1 = $query2->where('status', 'success')->sum('charge');
                $sum2 = $query->where('status', 'success')->sum('charge');
                $data[$value] = round($sum1 + $sum2, 2);
            } else if ($value == "commission") {
                $sum1 = $query2->where('status', 'success')->sum('charge');
                $sum2 = $query->where('status', 'success')->where('profit', ">", 0)->sum('profit');
                $data[$value] = round($sum1 + $sum2, 2);
            }
        }
        return response()->json($data);
    }
}