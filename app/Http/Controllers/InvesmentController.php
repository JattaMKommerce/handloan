<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Fundbank;
use App\Models\Investfundreport;
use App\Models\Investment;
use App\Models\InvestmentTxn;
use App\Models\Paymode;
use App\Models\Report;
use App\Models\User;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Torann\GeoIP\Console\Update;

class InvesmentController extends Controller
{
    public function index()
    {
        if (\Myhelper::hasRole('admin') || \Myhelper::can('invesment')) {
            $investmentData['banner'] = Banner::where('status', 'active')->get();
            $investmentData['video'] = Video::where('status', 'active')->get();
            // dd($investmentData);
            return view('invesment.index')->with('investmentData', $investmentData);
        }

        return \Response::json(['statuscode' => 'ERR', 'status' => "Permission not allowed", 'message' => "Permission not allowed"], 400);
    }

    public function indexShow()
    {
        if (\Myhelper::hasRole('admin') || \Myhelper::can('invesment_show')) {
            $invesment = Investment::leftJoin(
                'investment_txns',
                'investment_txns.investment_id',
                'investments.id'
            )
                ->select('investments.*', 'investment_txns.status as txnStatus')
                ->where('investments.status', 'active')->get();

            return view('invesment.invesment')->with('invesment', $invesment);
            // dd($invesment);

        }

        return \Response::json(['statuscode' => 'ERR', 'status' => "Permission not allowed", 'message' => "Permission not allowed"], 400);
    }


    public function fundReq()
    {
        if (\Myhelper::can('investment_fund_request')) {
            $data['banks'] = Fundbank::where('user_id', \Auth::user()->parent_id)->where('status', '1')->get();
            $data['paymodes'] = Paymode::where('status', '1')->get();
            return view('fund.request_investment')->with($data);
        }

        return \Response::json(['statuscode' => 'ERR', 'status' => "Permission not allowed", 'message' => "Permission not allowed"], 400);
    }


    public function investfundReq()
    {
        if (\Myhelper::hasRole('admin')) {
            $data['paymodes'] = Paymode::where('status', '1')->get();

            return view('fund.investrequestview')->with($data);
        }

        return \Response::json(['statuscode' => 'ERR', 'status' => "Permission not allowed", 'message' => "Permission not allowed"], 400);
    }

    public function investReport()
    {
        if (\Myhelper::hasRole('admin')) {
            $data['paymodes'] = Paymode::where('status', '1')->get();

            return view('fund.investment_statement')->with($data);
        }

        return \Response::json(['statuscode' => 'ERR', 'status' => "Permission not allowed", 'message' => "Permission not allowed"], 400);
    }


    public function investmentRequestUpdate(Request $post)
    {
        $fundreport = Investfundreport::where('id', $post->id)->first();

        if ($fundreport->status != "pending") {
            return response()->json(['status' => "Request already approved"], 400);
        }

        $post['charge'] = 0;
        $post['amount'] = $fundreport->amount;
        $post['type'] = "request";
        $post['user_id'] = $fundreport->user_id;
        if ($fundreport->mode == "CASH") {
            if ($fundreport->amount >= 100000) {
                $lakh = round($fundreport->amount / 100000);
            } else {
                $lakh = 1;
            }
            $fundbank = Fundbank::where('id', $fundreport->fundbank_id)->first();
            if (($fundbank) && $fundbank->charge > 0) {
                $post['charge'] = $fundbank->charge * $lakh;
            }
        }
        if ($post->status == "approved") {
            if (\Auth::user()->mainwallet < $post->amount) {
                return response()->json(['status' => "Insufficient wallet balance."], 200);
            }

            $checkinvest = $this->checkInvestmentComplete($fundreport, 1);

            $action = Investfundreport::updateOrCreate(['id' => $post->id], [
                "status" => $post->status,
                "remark" => $post->remark
            ]);

            $post['txnid'] = $fundreport->id;
            $post['option1'] = $fundreport->fundbank_id;
            $post['option2'] = $fundreport->paymode;
            $post['option3'] = $fundreport->paydate;
            $post['refno'] = $fundreport->ref_no;

            return $this->paymentAction($post);
        } else {
            // if($post->status == 'rejected'){

            //     $getInvestsh = DB::table('investment_txns')->where('investment_id',$fundreport->scheme_id)->where('user_id',$fundreport->user_id)->first();

            //     $getNoOfShare = abs($getInvestsh->allotted_no_of_share-$fundreport->numberOfshares);
            //     $getAmountInvest = abs($getInvestsh->amount-$fundreport->amount);

            //     $rejectInvest = DB::table('investment_txns')->where('investment_id',$fundreport->scheme_id)->where('user_id',$fundreport->user_id)->Update(['allotted_no_of_share'=>$getNoOfShare, 'amount'=>$getAmountInvest]);
            // }
            $action = Investfundreport::updateOrCreate(['id' => $post->id], [
                "status" => $post->status,
                "remark" => $post->remark
            ]);

            if ($action) {
                return response()->json(['status' => "success"], 200);
            } else {
                return response()->json(['status' => "Something went wrong, please try again."], 200);
            }
        }
    }


    public function investFundStore(Request $post)
    {

        if (!\Myhelper::can('fund_request')) {
            return response()->json(['status' => "Permission not allowed"], 400);
        }

        $rules = array(
            'fundbank_id' => 'required|numeric',
            "numberOfshares" => "required|numeric|min:0",
            "scheme_id" => "required|numeric",
            'paymode' => 'required',
            'amount' => 'required|numeric|min:50',
            'ref_no' => 'required|unique:investfundreports,ref_no',
            'paydate' => "date_format:Y-m-d",
            'investment_title' => 'required',
            "payslips" => 'required|mimes:png,jpg,jpeg,pdf'
        );

        $validator = \Validator::make($post->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $action = Investfundreport::where(['status' => 'pending', 'user_id' => Auth::user()->id])->count();
        if ($action > 0) {
            return response()->json(['status' => "Already reqeust is pending."], 200);
        }

        $post['user_id'] = \Auth::id();
        $post['credited_by'] = \Auth::user()->parent_id;
        if (!\Myhelper::can('setup_bank', \Auth::user()->parent_id)) {
            $admin = User::whereHas('role', function ($q) {
                $q->where('slug', 'whitelable');
            })->where('company_id', \Auth::user()->company_id)->first(['id']);

            if ($admin && \Myhelper::can('setup_bank', $admin->id)) {
                $post['credited_by'] = $admin->id;
            } else {
                $admin = User::whereHas('role', function ($q) {
                    $q->where('slug', 'admin');
                })->first(['id']);
                $post['credited_by'] = $admin->id;
            }
        }
        $getTotalshare = DB::table('investments')->where('id', $post->scheme_id)->first();
        if (empty($getTotalshare)) {
            return response()->json(["statuscode" => "ERR", 'message' => "No investment scheme found", 'status' => "No investment scheme found"]);
        }

        if ( $post->amount > $getTotalshare->mature_amount || $post->amount < $getTotalshare->amount ) {
            return response()->json(["statuscode" => "ERR", 'message' => "Amount should be equal to the investement amount", 'status' => "Amount should be equal to the investement amount"]);

        }
        // if ($post->numberOfshares > $getTotalshare->totalNoOfShare || $post->amount > $getTotalshare->mature_amount) {
        //     return response()->json(["statuscode" => "ERR", 'message' => "Number of share should be less than totalNoOfShare/mature amount", 'status' => "Number of share should be less than totalNoOfShare/mature amount"]);
        // }
        // $checkShare = DB::table('investment_txns')->where('investment_id', $post->scheme_id)->where('user_id', $post->user_id)->first();
        // if (!empty($checkShare)) {
        //     if ($post->numberOfshares > ($getTotalshare->totalNoOfShare - $checkShare->allotted_no_of_share)) {
        //         return response()->json(["statuscode" => "ERR", 'status' => "pending share for investment:" . ($getTotalshare->totalNoOfShare - $checkShare->allotted_no_of_share), "message" => "pending share for investment:" . ($getTotalshare->totalNoOfShare - $checkShare->allotted_no_of_share)]);
        //     }
        // }
        $post['status'] = "pending";
        if ($post->hasFile('payslips')) {
            $filename = 'payslip' . \Auth::id() . date('ymdhis') . "." . $post->file('payslips')->guessExtension();
            $post->file('payslips')->move(public_path('deposit_slip/'), $filename);
            $post['payslip'] = $filename;
        }


        // dd($checkinvest['statuscode']);
        // if ($checkinvest['statuscode'] == 'ERR') {
        //     return response()->json($checkinvest);
        // } else {
        unset($post['type']);
        $post['type'] = 'request';
        $action = Investfundreport::create($post->all());
        if ($action) {
            return response()->json(["statuscode" => "TXN", 'status' => "success", "message" => "Request Accepted, Please wait for approval"]);
        } else {
            return response()->json(["statuscode" => "TXF", 'message' => "Something went wrong, please try again.", 'status' => "Something went wrong, please try again."]);
        }
        // }
    }

    public function store(Request $post)
    {

        $rules = array(
            'banner_id' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'mature_amount' => 'required|digits_between:1,999999',
            'maturity_at' => 'required',
            'totalNoOfShare' => 'nullable|min:0|integer',
            'amount' => 'required|numeric|min:50',
            'title' => 'required'
        );

        $validator = \Validator::make($post->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $insert = $post->all();
        $insert['user_id'] = Auth::user()->id;

        $get_m_amount = $post->mature_amount; // $post->totalNoOfShare;
        $getamount = $post->mature_amount; // $post->totalNoOfShare;

        unset($post->amount);
        unset($post->mature_amount);

        $post['amount'] = round($getamount, 2);
        $post['mature_amount'] = round($get_m_amount, 2);

        $action = Investment::updateOrCreate(['id' => $post->id], $insert);
        if ($action) {
            return response()->json(['status' => "success"], 200);
        } else {
            return response()->json(['status' => "Task Failed, please try again"], 200);
        }
    }

    public function investNow(Request $post)
    {

        $rules = array(
            'investment_id' => 'required'
        );

        $validator = \Validator::make($post->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 200);
        }


        if (InvestmentTxn::where(['user_id' => Auth::user()->id, 'investment_id' => $post->investment_id])->count()) {
            return response()->json(['errors' => [0 => 'Already purchased']], 200);
        }

        $inv = Investment::where('id', $post->investment_id)->first();
        $user = User::where('id', Auth::user()->id)->first();

        if ($user->investment_wallet < $inv->amount) {
            return response()->json(['errors' => [0 => 'insufficient wallet balance']], 200);
        }

        $user->investment_wallet = $user->investment_wallet - $inv->amount;
        $user->save();

        $insert = $post->all();
        $insert['user_id'] = Auth::user()->id;
        $insert['amount'] = $inv->amount;
        $insert['status'] = 'approved';

        $action = InvestmentTxn::updateOrCreate(['id' => $post->id], $insert);
        if ($action) {
            return response()->json(['status' => "success"], 200);
        } else {
            return response()->json(['status' => "Task Failed, please try again"], 200);
        }
    }


    public function paymentAction($post)
    {
        $user = User::where('id', $post->user_id)->first();
        $charge = $post->charge ?? 0;
        if ($post->type == "transfer" || $post->type == "request") {
            $action = User::where('id', $post->user_id)->increment('investment_wallet', $post->amount - $charge);
        } else {
            $action = User::where('id', $post->user_id)->decrement('investment_wallet', $post->amount);
        }

        if ($action) {
            if ($post->type == "transfer" || $post->type == "request") {
                $post['trans_type'] = "credit";
            } else {
                $post['trans_type'] = "debit";
            }

            $insert = [
                'number' => $user->mobile,
                'mobile' => $user->mobile,
                'provider_id' => $post->provider_id,
                'api_id' => 0,
                'amount' => $post->amount,
                'charge' => $charge,
                'profit' => '0.00',
                'gst' => '0.00',
                'tds' => '0.00',
                'apitxnid' => NULL,
                'txnid' => $post->txnid,
                'payid' => NULL,
                'refno' => $post->refno,
                'description' => NULL,
                'remark' => $post->remark,
                'option1' => $post->option1,
                'option2' => $post->option2,
                'option3' => $post->option3,
                'option4' => NULL,
                'status' => 'success',
                'user_id' => $user->id,
                'credit_by' => \Auth::id(),
                'rtype' => 'main',
                'via' => 'portal',
                'adminprofit' => '0.00',
                'balance' => $user->investment_wallet,
                'trans_type' => $post->trans_type,
                'product' => "investment"
            ];
            $action = Report::create($insert);
            if ($action) {
                return response()->json(['status' => "success"], 200);
                // return $this->paymentActionCreditor($post);
            } else {
                return response()->json(['status' => "Technical error, please contact your service provider before doing transaction."], 400);
            }
        } else {
            return response()->json(['status' => "Fund transfer failed, please try again."], 400);
        }
    }

    public function paymentActionCreditor($post)
    {
        $payee = $post->user_id;
        $user = User::where('id', \Auth::id())->first();
        if ($post->type == "transfer" || $post->type == "request") {
            $action = User::where('id', $user->id)->decrement('mainwallet', $post->amount);
        } else {
            $action = User::where('id', $user->id)->increment('mainwallet', $post->amount);
        }

        if ($action) {
            if ($post->type == "transfer" || $post->type == "request") {
                $post['trans_type'] = "debit";
            } else {
                $post['trans_type'] = "credit";
            }

            $insert = [
                'number' => $user->mobile,
                'mobile' => $user->mobile,
                'provider_id' => $post->provider_id,
                'api_id' => 0,
                'amount' => $post->amount,
                'charge' => '0.00',
                'profit' => '0.00',
                'gst' => '0.00',
                'tds' => '0.00',
                'apitxnid' => NULL,
                'txnid' => $post->txnid,
                'payid' => NULL,
                'refno' => $post->refno,
                'description' => NULL,
                'remark' => $post->remark,
                'option1' => $post->option1,
                'option2' => $post->option2,
                'option3' => $post->option3,
                'option4' => NULL,
                'status' => 'success',
                'user_id' => $user->id,
                'credit_by' => $payee,
                'rtype' => 'main',
                'via' => 'portal',
                'adminprofit' => '0.00',
                'balance' => $user->mainwallet,
                'trans_type' => $post->trans_type,
                'product' => "investment"
            ];

            $action = Report::create($insert);
            if ($action) {
                return response()->json(['status' => "success"], 200);
            } else {
                return response()->json(['status' => "Technical error, please contact your service provider before doing transaction."], 400);
            }
        } else {
            return response()->json(['status' => "Technical error, please contact your service provider before doing transaction."], 400);
        }
    }

    public function checkInvestmentComplete($post, $tcheck)
    {
        $inrt = [];

        $getInvestmentStatus = DB::table('investment_txns')->where('investment_id', $post->scheme_id)->where('user_id', $post->user_id)->first();

        $getInvestShare = DB::table('investfundreports')
            ->select('investments.*', 'investfundreports.*', 'investfundreports.amount as am')
            ->where('scheme_id', $post->scheme_id)->where('investfundreports.user_id', $post->user_id)
            ->where('investfundreports.status', 'approved')
            ->leftJoin('investments', 'investments.id', 'investfundreports.scheme_id')
            ->get();

        $totalAmount = 0;
        $totalShare = 0;

        $getMatureAmount = DB::table('investments')->where('id', $post->scheme_id)->first();


        // if ($getInvestShare->isEmpty()) {
        //     return ["statuscode" => "ERR", "message" => 'Invest Data not found', 'status' => 'error'];
        // }

        if (empty($getInvestmentStatus)) {
            $inrt = [
                "user_id" => @$post->user_id,
                "investment_id" => @$post->scheme_id,
                "amount" => @$post->amount,
                "allotted_no_of_share" => @$post->numberOfshares,
                "updated_at" => date('Y-m-d H:i:s')

            ];
            $getMatureAmount = DB::table('investments')->where('id', $post->scheme_id)->first();

            if ($post->amount <= $getMatureAmount->mature_amount && $post->amount >= $getMatureAmount->amount){//$post->numberOfshares >= $getMatureAmount->totalNoOfShare) {
                $inrt["is_investment_complete"] = "1";
                $inrt["status"] = "approved";
                // $inrt["updated_at"] = date('Y-m-d H:i:s');
            }

            $insertInInvestmentTxn = DB::table('investment_txns')->insert($inrt);
            if ($tcheck == 1) {
                if ($insertInInvestmentTxn) {
                    return ["statuscode" => "TXN", "message" => 'Invest Completed', 'status' => 'success'];
                }
            }

        } else {
            if (!empty($getInvestmentStatus) && $getInvestShare->isEmpty()) {
                $ud = [
                    "amount" => @$post->amount,
                    "allotted_no_of_share" => @$post->numberOfshares,
                    "updated_at" => date('Y-m-d H:i:s')
                ];
                if ($post->amount <= $getMatureAmount->mature_amount && $post->amount >= $getMatureAmount->amount ){//$post->numberOfshares >= $getMatureAmount->totalNoOfShare) {
                    $ud["is_investment_complete"] = "1";
                    $ud["status"] = "approved";
                    // $inrt["updated_at"] = date('Y-m-d H:i:s');
                }

                $dbud = DB::table('investment_txns')->where('investment_id', $post->scheme_id)->where('user_id', $post->user_id)->update($ud);
                if ($dbud) {
                    return ["statuscode" => "TXN", "message" => 'Invest Completed', 'status' => 'success'];
                }
            }

            if ($getInvestmentStatus->is_investment_complete != 1) {

                foreach ($getInvestShare as $key => $val) {
                    $totalAmount += $val->am;
                    $totalShare += $val->numberOfshares;
                }

                if ($totalAmount <= $getMatureAmount->mature_amount && $post->amount >= $getMatureAmount->amount){//$post->numberOfshares >= $getMatureAmount->totalNoOfShare) {
                    $update = [
                        "amount" => $totalAmount,
                        "allotted_no_of_share" => $totalShare,
                        "status" => "approved",
                        "is_investment_complete" => "1",
                        "updated_at" => date('Y-m-d H:i:s')
                    ];
                } else {
                    $am = $totalAmount + @$post->amount;
                    $sh = $totalShare + @$post->numberOfshares;
                    $update = [
                        "amount" => $am,
                        "allotted_no_of_share" => $sh,
                        "updated_at" => date('Y-m-d H:i:s')
                    ];

                    if ($am <= $getMatureAmount->mature_amount && $sh >= $getMatureAmount->amount ){//$getMatureAmount->totalNoOfShare) {
                        $update["is_investment_complete"] = "1";
                        $update["status"] = "approved";
                        // $inrt["updated_at"] = date('Y-m-d H:i:s');
                        // dd('fkkdk');
                    }


                }
                $dbup = DB::table('investment_txns')->where('investment_id', $post->scheme_id)->where('user_id', $post->user_id)->update($update);
                if ($dbup) {
                    return ["statuscode" => "TXN", "message" => 'Invest Completed', 'status' => 'success'];
                }
            }
        }
        return ["statuscode" => "ERR", "message" => 'Investment Completed', 'status' => 'error'];


    }
}