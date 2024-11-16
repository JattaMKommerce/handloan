<?php

namespace App\Http\Controllers;

use App\Models\Api;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FlightController extends Controller
{
    //
    public $flight_cred;

    public function __construct()
    {
        $flight_cred = Api::where('code', 'flight')->first();
        $this->flight_cred['username'] = "amtechworld";
        $this->flight_cred['password'] = "B698745DE46CC64C8F4135240DE349BB9D779730";
        $this->flight_cred['url'] = "http://uat.etrav.in";
        $this->flight_cred['ip'] = "14.97.25.214";


    }
    public function getCity()
    {
        try {
            $data['cities'] = \App\Models\flightCity::all()->makeHidden(["id", "created_at", "updated_at"]);


            return view('flight.search_flight')->with($data);

        } catch (Exception $e) {
            return response()->json([
                'statuscode' => 'ERR',
                'status' => 'error',
                'message' => "Something went wrong, please try after some time",
                'data' => $e->getMessage()

            ]);
        }
    }

    public function flight(Request $request)
    {
        // $request['type'] = $type;

        // dd($request->all());

        $rules["type"] = ['required'];

        if (\Auth::id() == null) {
            return response()->json(['statuscode' => "ERR", "message" => "User Not Found"]);
        } else {
            $getUserDetails = User::where('id', \Auth::id())->first();
        }

        switch ($request->type) {
            case "fetch":
                $rule['travelInfo'] = 'required';
                // $rules["origin"] = ['nullable']; // ['required'];
                // $rules["destination"] = ['nullable']; // ['required'];
                // $rules["travelDate"] = ['nullable']; // ['required'];
                $rules["adultCount"] = ['nullable']; // ['required|numeric|min:1'];
                $rules["childCount"] = ['nullable']; // ['required|numeric'];
                $rules["infantCount"] = ['nullable']; // ['required|numeric'];
                $rules["travelClass"] = ['required', 'in:0,1,2,3']; //0-ECONOMY,1-BUSINESS,2-FIRST,3-PREMIUM_ECONOMY
                $rules["airlinefilter"] = ['nullable'];
                $rules["travelType"] = ['required', 'in:0,1']; //0-DOMESTIC,1-INTERNATIONAL
                $rules["bookingType"] = ['required', 'in:0,1,2']; // 0-ONE_WAY,1-ROUNDTRIP,2-SPECIALROUNDTRIP


                break;

            case 'reprice':
                $rules["searchKey"] = ['nullable']; // ['required'];
                $rules["fareId"] = ['nullable']; // ['required'];
                $rules["flightKey"] = ['nullable']; // ['required'];
                break;

            case 'getSSR':
                $rules["flightKey"] = ['nullable']; // ['required'];
                $rules["searchKey"] = ['nullable']; // ['required'];
                break;

            case 'seatMap':
                $rules["flightKey"] = ['nullable']; // ['required'];
                $rules["searchKey"] = ['nullable']; // ['required'];
                $rules["passengerDetails"] = ['nullable']; // ['required'];

                break;
            case 'ticket':
                $rules['refNo'] = ['nullable']; // ['required'];
                $rules['ticketType'] = ['nullable']; // ['required'];

                break;
            case 'tempBook':
                $rules['psngrMobile'] = ['nullable']; // ["required"];
                $rules['whatsappMobile'] = ['nullable']; // ["required"];
                $rules['passengerEmail'] = ['nullable']; // ["required"];
                // $rules['gstNumber'] = ['nullable']; // ["nullable"];
                // $rules['gstHolderName'] = ['nullable']; // ["nullable"];
                // $rules['gstAddress'] = ['nullable']; // ["nullable"];
                $rules['searchKey'] = ['nullable']; // ["required"];
                $rules['flightKey'] = ['nullable']; // ["required"];
                // $rules['costCentreId'] = ['nullable']; // ["required"];
                // $rules['projectId'] = ['nullable']; // ["required"];
                $rules['bookingRemark'] = ['nullable']; // ["required"];
                // $rules['corporateStatus'] = ['nullable']; // ["required"];
                // $rules['corporatePaymentMode'] = ['nullable']; // ["required"];
                // $rules['missedSavingReason'] = ['nullable']; // ["nullable"];
                // $rules['corpTripType'] = ['nullable']; // ["nullable"];
                // $rules['corpTripSubType'] = ['nullable']; // ["nullable"];
                // $rules['tripRequestId'] = ['nullable']; // ["nullable"];
                // $rules['bookingAlertIds'] = ['nullable']; // ["nullable"];
                $rules["origin"] = ['nullable']; // ['required'];
                $rules["destination"] = ['nullable']; // ['required'];
                $rules["travelDate"] = ['nullable']; // ['required'];
                $rules["adultCount"] = ['nullable']; // ['required|numeric|min:1'];
                $rules["childCount"] = ['nullable']; // ['required|numeric'];
                $rules["infantCount"] = ['nullable']; // ['required|numeric'];
                $rules["travelClass"] = ['required', 'in:0,1,2,3']; //0-ECONOMY,1-BUSINESS,2-FIRST,3-PREMIUM_ECONOMY
                $rules["travelType"] = ['nullable', 'in:0,1']; //0-DOMESTIC,1-INTERNATIONAL
                $rules["bookingType"] = ['required', 'in:0,1,2']; // 0-ONE_WAY,1-ROUNDTRIP,2-SPECIALROUNDTRIP

                break;

            case 'reprint':
                $rules['refNo'] = ['nullable']; // ['required'];
                $rules['airlinePNR'] = ['nullable']; // ['required'];
                break;

            case 'cancellation':
                $rules['flightId'] = ['nullable']; // ['required'];
                $rules['psngrId'] = ['nullable']; // ['required'];
                $rules['segmentId'] = ['nullable']; // ['required'];
                $rules['airlinePNR'] = ['nullable']; // ['required'];
                $rules['refNo'] = ['nullable']; // ['required'];
                $rules['remarks'] = ['nullable']; // ['required'];
                $rules['cancelType'] = ['required', 'in:0,1,2']; // ['required'];   // 0-Normal Cancel,1-Full Refund,2-No Show

                break;

            case 'lowfare':
                $rules['destination'] = ['nullable']; // ['required'];
                $rules['month'] = ['nullable']; // ['required'];
                $rules['origin'] = ['nullable']; // ['required'];
                $rules['year'] = ['nullable']; // ['required'];

                break;
            case 'tripHistory':
                $rules["fromDate"] = ['nullable']; // ['required'];;
                $rules["month"] = ['nullable']; // ['required'];;
                $rules["toDate"] = ['nullable']; // ['required'];3/10/2023";
                $rules["flightType"] = ['nullable']; // ['required'];
                $rules["searchYear"] = ['nullable']; // ['required']; "2023";
                break;

            case 'getBalance':
                $rules = [];
                break;

            case 'addPayment':
                $rules = ["amount" => 'required|numeric|min:1'];

                break;

            case 'lowFare':
                $rules = [];

                break;

            case 'fareRule':
                $rules['searchKey'] = ['nullable']; // ['required'];
                $rules['fareId'] = ['nullable']; // ['required'];
                $rules['flightKey'] = ['nullable']; // ['required'];
                break;

            default:
                return response()->json(['statuscode' => "ERR", "message" => "Invalid Type used "]);
                break;

        }

        $validator = Validator::make($request->all(), $rules);

        if ($request->type == 'seatMap' || $request->type == 'tempBook' || $request->type == "fetch") {
            $validator->after(function ($validator) {
                $config = request()->get('passengerDetails');
                $tripInfo = request()->get('travelInfo');
                $isvalue = true;
                $istrip = true;
                $isvaluepassport = true;

                if (!empty($tripInfo) > 0) {
                    foreach ($tripInfo as $conf) {

                        if (empty($conf['origin']) && empty($conf['destination']) && empty($conf['travelDate']) && empty($conf['tripId'])) {
                            $istrip = false;
                            break;
                        }

                    }
                }




                if (!empty($config) > 0) {
                    foreach ($config as $conf) {

                        $paxtype = [0, 1, 2];
                        if (!in_array($conf['psngr_type'], $paxtype)) {
                            $validator->errors()->add("psngrType", "passenger type is invalid");
                        }


                        if (empty($conf['psngr_id']) && empty($conf['psngr_type']) && empty($conf['psngr_title']) && empty($conf['first_name']) && empty($conf['last_name']) && empty($conf['psngr_gender']) && empty($conf['psngr_age']) && empty($conf['psngr_dob']) && empty($conf['nationality'])) {
                            $isvalue = false;
                            break;
                        }

                        if (isset($conf['passport_number']) && isset($conf['passport_issuing_country']) && isset($conf['passport_expiry']) && isset($conf['frequent_flyer_details'])) {
                        } else {
                            $isvaluepassport = false;
                        }
                    }
                }

                if (!$isvaluepassport) {
                    $validator->errors()->add('passengerDetails', 'passport key fileds lable are required');
                }
                if (!$isvalue) {
                    $validator->errors()->add('passengerDetails', 'passenger field is required');
                }

                if (!$istrip) {
                    $validator->errors()->add('travelInfo', 'trip field is required');
                }
            });
        }

        if ($validator->fails()) {
            return response()->json(['status' => 'ERR', 'message' => $validator->errors()->first()]);
        }

        $req_id = "FL" . time();

        $headers = [
            'Content-Type: application/json',
        ];

        try {
            switch ($request->type) {
                case "fetch":
                    $url = $this->flight_cred['url'] . "/airlinehost/AirAPIService.svc/JSONService/Air_Search";

                    foreach ($request->travelInfo as $key => $val) {
                        $trip[$key]["Origin"] = $val['origin'];
                        $trip[$key]["Destination"] = $val['destination'];
                        $trip[$key]["TravelDate"] = $val['travelDate'];
                        $trip[$key]["Trip_Id"] = $val['tripId'];
                    }


                    $param = [
                        "Auth_Header" => [
                            "UserId" => $this->flight_cred['username'],
                            "Password" => $this->flight_cred['password'],
                            "IP_Address" => $this->flight_cred['ip'],
                            "Request_Id" => $req_id,
                            "IMEI_Number" => "223232223232323"
                        ],
                        "Travel_Type" => $request->travelType ?? 0,
                        "Booking_Type" => $request->bookingType ?? 0,
                        "TripInfo" => $trip,
                        // [
                        //     [
                        //         "Origin" => $request->origin ?? "BOM",
                        //         "Destination" => $request->destination ?? "DEL",
                        //         "TravelDate" => $request->travelDate ?? "27 SEP 2023",
                        //         "Trip_Id" => 0
                        //     ]
                        // ],
                        "Adult_Count" => $request->adultCount ?? "1",
                        "Child_Count" => $request->childCount ?? "0",
                        "Infant_Count" => $request->infantCount ?? "0",
                        "Class_Of_Travel" => $request->travelClass ?? "0",
                        "InventoryType" => "0",
                        "Filtered_Airline" => [
                            [
                                "Airline_Code" => $request->airlinefilter ?? "",
                            ]
                        ]

                    ];

                    // dd(json_encode($param));

                    break;

                case 'reprice':
                    $url = $this->flight_cred['url'] . "/airlinehost/AirAPIService.svc/JSONService/Air_Reprice";
                    $param = [
                        "Auth_Header" => [
                            "UserId" => $this->flight_cred['username'],
                            "Password" => $this->flight_cred['password'],
                            "IP_Address" => $this->flight_cred['ip'],
                            "Request_Id" => $req_id,
                            //$req_id,
                            "IMEI_Number" => "223232223232323"
                        ],
                        "Search_Key" => $request->searchKey ?? $this->flightkeys()['searchKey'],

                        "AirRepriceRequests" => [
                            [
                                "Fare_Id" => $request->fareId ?? "5316804529804600303",
                                "Flight_Key" => $request->flightKey ?? $this->flightkeys()['flightKey']
                            ]
                        ],
                        "Customer_Mobile" => $getUserDetails->mobile,
                        //retailer mobile number
                        "GST_Input" => false,
                        "SinglePricing" => true
                    ];
                    break;

                case 'getSSR':
                    $url = $this->flight_cred['url'] . "/airlinehost/AirAPIService.svc/JSONService/Air_GetSSR";
                    $param = [
                        "Auth_Header" => [
                            "UserId" => $this->flight_cred['username'],
                            "Password" => $this->flight_cred['password'],
                            "IP_Address" => $this->flight_cred['ip'],
                            "Request_Id" => $req_id,
                            //$req_id,
                            "IMEI_Number" => "223232223232323"
                        ],
                        "Search_Key" => $request->searchKey ?? $this->flightkeys()['searchKey'],
                        "AirSSRRequestDetails" =>
                        [
                            "Flight_Key" => $request->flightKey ?? $this->flightkeys()['flightKey']
                        ]
                    ];
                    break;

                case 'seatMap':
                    $url = $this->flight_cred['url'] . "/airlinehost/AirAPIService.svc/JSONService/Air_GetSeatMap";

                    $request23 = $this->flightkeys()['passengerDetails'];

                    foreach ($request23 as $key => $val) {
                        $val = json_decode($val);
                        $passDetail[$key]["Pax_Id"] = $val->psngr_id;
                        $passDetail[$key]["Pax_type"] = $val->psngr_type;
                        $passDetail[$key]["Title"] = $val->psngr_title;
                        $passDetail[$key]["First_Name"] = $val->first_name;
                        $passDetail[$key]["Last_Name"] = $val->last_name;
                        $passDetail[$key]["Gender"] = $val->psngr_gender;
                        $passDetail[$key]["Age"] = $val->psngr_age;
                        $passDetail[$key]["DOB"] = $val->psngr_dob;
                        $passDetail[$key]["Passport_Number"] = $val->passport_number;
                        $passDetail[$key]["Passport_Issuing_Country"] = $val->passport_issuing_country;
                        $passDetail[$key]["Passport_Expiry"] = $val->passport_expiry;
                        $passDetail[$key]["Nationality"] = $val->nationality;
                        $passDetail[$key]["FrequentFlyerDetails"] = $val->frequent_flyer_details;
                    }

                    $param = [
                        "Auth_Header" => [
                            "UserId" => $this->flight_cred['username'],
                            "Password" => $this->flight_cred['password'],
                            "IP_Address" => $this->flight_cred['ip'],
                            "Request_Id" => $req_id,
                            "IMEI_Number" => "223232223232323"
                        ],
                        "Search_Key" => $request->searchKey ?? $this->flightkeys()['searchKey'],
                        "AirSSRRequestDetails" =>
                        [
                            "Flight_Key" => $request->flightKey ?? $this->flightkeys()['flightKey']
                        ],
                        "PAX_Details" => $passDetail

                    ];

                    break;

                case 'tempBook':
                    $url = $this->flight_cred['url'] . "/airlinehost/AirAPIService.svc/JSONService/Air_TempBooking";

                    $request23 = $this->flightkeys()['passengerDetails'];


                    foreach ($request23 as $key => $val) {
                        $val = json_decode($val);
                        $passDetail[$key]["Pax_Id"] = $val->psngr_id;
                        $passDetail[$key]["Pax_type"] = $val->psngr_type;
                        $passDetail[$key]["Title"] = $val->psngr_title;
                        $passDetail[$key]["First_Name"] = $val->first_name;
                        $passDetail[$key]["Last_Name"] = $val->last_name;
                        $passDetail[$key]["Gender"] = $val->psngr_gender;
                        $passDetail[$key]["Age"] = $val->psngr_age;
                        $passDetail[$key]["DOB"] = $val->psngr_dob;
                        $passDetail[$key]["Passport_Number"] = $val->passport_number;
                        $passDetail[$key]["Passport_Issuing_Country"] = $val->passport_issuing_country;
                        $passDetail[$key]["Passport_Expiry"] = $val->passport_expiry;
                        $passDetail[$key]["Nationality"] = $val->nationality;
                        $passDetail[$key]["FrequentFlyerDetails"] = $val->frequent_flyer_details;
                    }

                    $param = [
                        "Auth_Header" => [
                            "UserId" => $this->flight_cred['username'],
                            "Password" => $this->flight_cred['password'],
                            "IP_Address" => $this->flight_cred['ip'],
                            "Request_Id" => $req_id,
                            //$req_id,
                            "IMEI_Number" => "223232223232323"
                        ],
                        "Customer_Mobile" => $getUserDetails->mobile,
                        //retailer mobile number
                        "Passenger_Mobile" => $request->psngrMobile ?? "7208822571",
                        "WhatsAPP_Mobile" => $request->whatsappMobile ?? "7208822571",
                        "Passenger_Email" => $request->passengerEmail ?? "demo@outlook.com",
                        "PAX_Details" => $passDetail,
                        "GST" => false,
                        "GST_Number" => $request->gstNumber ?? "",
                        "GST_HolderName" => $request->gstHolderName ?? "GST Holder Name",
                        "GST_Address" => $request->gstAddress ?? "GST Address",
                        "BookingFlightDetails" =>
                        [
                            [
                                "Search_Key" => $request->searchKey ?? $this->flightkeys()['searchKey'],
                                "Flight_Key" => $request->flightKey ?? $this->flightkeys()['flightKey'],
                                "BookingSSRDetails" => []

                            ]
                        ],
                        "CostCenterId" => $request->costCentreId ?? "0",
                        "ProjectId" => $request->projectId ?? "0",
                        "BookingRemark" => $request->bookingRemark ?? "MAA-TCR  18-Oct-2021  Test API With GST",
                        "CorporateStatus" => $request->corporateStatus ?? "0",
                        "CorporatePaymentMode" => $request->corporatePaymentMode ?? "0",
                        "MissedSavingReason" => $request->missedSavingReason ?? null,
                        "CorpTripType" => $request->corpTripType ?? null,
                        "CorpTripSubType" => $request->corpTripSubType ?? null,
                        "TripRequestId" => $request->tripRequestId ?? null,
                        "BookingAlertIds" => $request->bookingAlertIds ?? null,


                    ];

                    break;

                case 'ticket':
                    $url = $this->flight_cred['url'] . "/airlinehost/AirAPIService.svc/JSONService/Air_Ticketing";
                    $param = [
                        "Auth_Header" => [
                            "UserId" => $this->flight_cred['username'],
                            "Password" => $this->flight_cred['password'],
                            "IP_Address" => $this->flight_cred['ip'],
                            "Request_Id" => $req_id,
                            //$req_id,
                            "IMEI_Number" => "223232223232323"
                        ],
                        "Booking_RefNo" => $request->refNo ?? "FBB64ZDT",
                        "Ticketing_Type" => $request->ticketType ?? "1"

                    ];

                    break;

                case 'fareRule':
                    $url = $this->flight_cred['url'] . "/airlinehost/AirAPIService.svc/JSONService/Air_FareRule";
                    $param = [
                        "Auth_Header" => [
                            "UserId" => $this->flight_cred['username'],
                            "Password" => $this->flight_cred['password'],
                            "IP_Address" => $this->flight_cred['ip'],
                            "Request_Id" => $req_id,
                            //$req_id,
                            "IMEI_Number" => "223232223232323"
                        ],
                        "Search_Key" => $request->searchKey ?? $this->flightkeys()['searchKey'],

                        "Fare_Id" => $request->fareId ?? "5316804529804600303",
                        "Flight_Key" => $request->flightKey ?? $this->flightkeys()['flightKey']
                    ];

                    break;

                case 'reprint':
                    $url = $this->flight_cred['url'] . "/airlinehost/AirAPIService.svc/JSONService/Air_Reprint";
                    $param = [
                        "Auth_Header" => [
                            "UserId" => $this->flight_cred['username'],
                            "Password" => $this->flight_cred['password'],
                            "IP_Address" => $this->flight_cred['ip'],
                            "Request_Id" => $req_id,
                            //$req_id,
                            "IMEI_Number" => "223232223232323"
                        ],

                        "Booking_RefNo" => $request->refNo ?? "FBB64ZDT",
                        "Airline_PNR" => $request->airlinePNR ?? ""
                    ];
                    break;

                case 'cancellation':
                    $url = $this->flight_cred['url'] . "/AirlineHost/AirAPIService.svc/JSONService/Air_TicketCancellation";

                    $param = [
                        "Auth_Header" => [
                            "UserId" => $this->flight_cred['username'],
                            "Password" => $this->flight_cred['password'],
                            "IP_Address" => $this->flight_cred['ip'],
                            "Request_Id" => $req_id,
                            //$req_id,
                            "IMEI_Number" => "223232223232323"
                        ],
                        "AirTicketCancelDetails" => [
                            [
                                "FlightId" => $request->flightId ?? "5416863216316396891",
                                "PassengerId" => $request->psngrId ?? "1",
                                "SegmentId" => $request->segmentId ?? "0"
                            ]
                        ],
                        "Airline_PNR" => $request->airlinePNR ?? "KEVG6H",
                        "RefNo" => $request->refNo ?? "FBB64ZDT",
                        "CancelCode" => "005",
                        "ReqRemarks" => $request->remarks ?? "I cancelled the ticket directly with Airline",
                        "CancellationType" => $request->cancelType ?? 0
                    ];
                    break;

                case 'lowFare':
                    $url = $this->flight_cred['url'] . "/AirlineHost/AirAPIService.svc/JSONService/Air_LowFare";

                    $param = [
                        "Auth_Header" => [
                            "UserId" => $this->flight_cred['username'],
                            "Password" => $this->flight_cred['password'],
                            "IP_Address" => $this->flight_cred['ip'],
                            "Request_Id" => $req_id,
                            //$req_id,
                            "IMEI_Number" => "223232223232323"
                        ],
                        "Destination" => $request->destination ?? "BOM",
                        "Month" => $request->month ?? "08",
                        "Origin" => $request->origin ?? "DEL",
                        "Year" => $request->year ?? 2023
                    ];
                    break;

                case 'getBalance':
                    $url = $this->flight_cred['url'] . "/tradehost/TradeAPIService.svc/JSONService/GetBalance";

                    $param = [
                        "Auth_Header" => [
                            "UserId" => $this->flight_cred['username'],
                            "Password" => $this->flight_cred['password'],
                            "IP_Address" => $this->flight_cred['ip'],
                            "Request_Id" => $req_id,
                            //$req_id,
                            "IMEI_Number" => "223232223232323"
                        ],
                        "RefNo" => $request->refNo ?? "FBB64ZDT",
                        "Ticketing_Type" => $request->ticketType ?? "0",
                        "ProductId" => $request->productId ?? "1",
                        "EWalletID" => $request->ewalletId ?? "0"

                    ];
                    break;

                case 'addPayment':
                    $url = $this->flight_cred['url'] . "/tradehost/TradeAPIService.svc/JSONService/AddPayment";

                    $param = [
                        "Auth_Header" => [
                            "UserId" => $this->flight_cred['username'],
                            "Password" => $this->flight_cred['password'],
                            "IP_Address" => $this->flight_cred['ip'],
                            "Request_Id" => $req_id,
                            //$req_id,
                            "IMEI_Number" => "223232223232323"
                        ],
                        "ClientRefNo" => "REF" . $req_id,
                        //$request->clientRefNo ?? "FBB64ZDT",
                        "RefNo" => $request->refNo ?? "FBB64ZDT",
                        "TransactionType" => $request->trType ?? 0,
                        "ProductId" => $request->productId ?? "1"

                    ];

                    $getUser = User::where('id', \Auth::id())->first();

                    $user_wallet_amount = $getUser->mainwallet;

                    $requested_amount = $request->amount;

                    if ($user_wallet_amount < $requested_amount) {
                        return response()->json([
                            'statuscode' => 'TXF',
                            "status" => "Insufficient wallet balance",
                            'message' => "Insufficient wallet balance",
                            'data' => []
                        ]);
                    }
                    break;

                case 'tripHistory':
                    $url = $this->flight_cred['url'] . "/airlinehost/AirAPIService.svc/JSONService/Air_History";

                    $param = [
                        "Auth_Header" => [
                            "UserId" => $this->flight_cred['username'],
                            "Password" => $this->flight_cred['password'],
                            "IP_Address" => $this->flight_cred['ip'],
                            "Request_Id" => $req_id,
                            //$req_id,
                            "IMEI_Number" => "223232223232323"
                        ],

                        "Fromdate" => $request->fromDate ?? "01/01/2022",
                        "Month" => $request->month ?? "10",
                        "Todate" => $request->toDate ?? "03/10/2023",
                        "Type" => "0",
                        "Year" => $request->searchYear ?? "2023"
                    ];
                    break;
                default:
                    return response()->json(['statuscode' => "ERR", 'message' => 'Invalid Request']);

            }



            // if ($request->type == "reprice"){

            $result = \Myhelper::curl($url, "POST", str_replace(["\\\/\\\/", "\\\/", "\/", "'\'"], "/", json_encode($param)), $headers, "yes", "flight" . $request->type, $req_id);

            $response = json_decode($result['response']);

            switch ($request->type) {

                case "fetch":
                   
                    if ($result['code'] == 200 || $result['error'] == null) {
                        if ($response->Response_Header->Error_Code == 0000 && $response->Response_Header->Status_Id == "11") {
                            // foreach ($response->TripDetails[0]->Flights as $key1 => $val1) {
                            //     foreach ($val1 as $key2 => $val2) {
                            //         $flival[$key1][strtolower($key2)] = $val2;
                            //         foreach ($val1->Fares as $key3 => $val3) {
                            //             $flival[$key1]['all'][$key3] = $val1->Fares[$key3]->FareDetails;
                            //         }
                            //     }

                            // }
                            // dd($flival);
                            return response()->json
                            ([
                                    'statuscode' => 'TXN',
                                    "status" => $response->Response_Header->Error_Desc,
                                    'message' => $response->Response_Header->Error_Desc,
                                    'data' => [
                                        "Status_Id" => $response->Response_Header->Status_Id,
                                        "Request_Id" => $response->Response_Header->Request_Id,
                                        "Search_Key" => $response->Search_Key,
                                        "Flight" => $response->TripDetails,
                                        "Trip_Id" => $response->TripDetails,
                                        "Booking_Type" => $request->bookingType ?? 0,
                                        // "Fare_Details"=> $response->TripDetails[0]->Flights
                                    ]
                                ]);

                        } else {
                            return response()->json([
                                'statuscode' => 'TXF',
                                "status" => @$response->Response_Header->Error_Desc,
                                'message' => @$response->Response_Header->Error_Desc,
                                'data' => [
                                    "Status_Id" => @$response->Response_Header->Status_Id,
                                    "Request_Id" => @$response->Response_Header->Request_Id,
                                    "Search_Key" => @$response->Search_Key,
                                    "Flight" => @$response->TripDetails[0]->Flights,
                                    "Trip_Id" => @$response->TripDetails[0]->Trip_Id,
                                ]
                            ]);
                        }
                    } else {
                        return response()->json([
                            'statuscode' => 'TXF',
                            "status" => $result['error'],
                            'message' => $result['error'],
                            'data' => $result['error']
                        ]);
                    }

                    break;

                case 'reprice':
                    // dd($url, $response, $result, str_replace(["\\\/\\\/", "\\\/", "'\'"], "/", json_encode($param)));
                    if ($result['code'] == 200 || $result['error'] == null) {
                        if ($response->Response_Header->Error_Code == 0000 && $response->Response_Header->Status_Id == "11") {
                            return response()->json([
                                'statuscode' => 'TXN',
                                "status" => $response->Response_Header->Error_Desc,
                                'message' => $response->Response_Header->Error_Desc,
                                'data' => [
                                    "Status_Id" => $response->Response_Header->Status_Id,
                                    "Request_Id" => $response->Response_Header->Request_Id,
                                    "Flight" => $response->AirRepriceResponses[0]->Flight,
                                ]
                            ]);

                        } else {
                            return response()->json([
                                'statuscode' => 'TXF',
                                "status" => @$response->Response_Header->Error_Desc,
                                'message' => @$response->Response_Header->Error_Desc,
                                'data' => [
                                    "Status_Id" => @$response->Response_Header->Status_Id,
                                    "Request_Id" => @$response->Response_Header->Request_Id,
                                    "Flight" => @$response->AirRepriceResponses[0]->Flight,

                                ]
                            ]);
                        }
                    } else {
                        return response()->json([
                            'statuscode' => 'TXF',
                            "status" => $result['error'],
                            'message' => $result['error'],
                            'data' => $result['error']
                        ]);
                    }

                    break;

                case 'getSSR':
                    // dd($url, $response, $result, str_replace(["\\\/\\\/", "\\\/", "'\'"], "/", json_encode($param)));
                    if ($result['code'] == 200 || $result['error'] == null) {
                        if ($response->Response_Header->Error_Code == 0000 && $response->Response_Header->Status_Id == "11") {
                            return response()->json([
                                'statuscode' => 'TXN',
                                "status" => $response->Response_Header->Error_Desc,
                                'message' => $response->Response_Header->Error_Desc,
                                'data' => [
                                    "Status_Id" => $response->Response_Header->Status_Id,
                                    "Request_Id" => $response->Response_Header->Request_Id,
                                    "Flight" => $response->AirRepriceResponses[0]->Flight,
                                ]
                            ]);

                        } else {
                            return response()->json([
                                'statuscode' => 'TXF',
                                "status" => @$response->Response_Header->Error_Desc,
                                'message' => @$response->Response_Header->Error_Desc,
                                'data' => [
                                    "Status_Id" => @$response->Response_Header->Status_Id,
                                    "Request_Id" => @$response->Response_Header->Request_Id,
                                    "Flight" => @$response->AirRepriceResponses[0]->Flight,

                                ]
                            ]);
                        }
                    } else {
                        return response()->json([
                            'statuscode' => 'TXF',
                            "status" => $result['error'],
                            'message' => $result['error'],
                            'data' => $result['error']
                        ]);
                    }

                    break;

                case 'seatMap':
                    dd($url, $response, $result, str_replace(["\\\/\\\/", "\\\/", "'\'"], "/", json_encode($param)));
                    if ($result['code'] == 200 || $result['error'] == null) {
                        if ($response->Response_Header->Error_Code == 0000 && $response->Response_Header->Status_Id == "11") {
                            return response()->json([
                                'statuscode' => 'TXN',
                                "status" => $response->Response_Header->Error_Desc,
                                'message' => $response->Response_Header->Error_Desc,
                                'data' => [
                                    "Status_Id" => $response->Response_Header->Status_Id,
                                    "Request_Id" => $response->Response_Header->Request_Id,
                                    "Flight" => $response->AirSeatMaps,
                                ]
                            ]);

                        } else {
                            return response()->json([
                                'statuscode' => 'TXF',
                                "status" => @$response->Response_Header->Error_Desc,
                                'message' => @$response->Response_Header->Error_Desc,
                                'data' => [
                                    "Status_Id" => @$response->Response_Header->Status_Id,
                                    "Request_Id" => @$response->Response_Header->Request_Id,
                                    "Flight" => @$response->AirSeatMaps,

                                ]
                            ]);
                        }
                    } else {
                        return response()->json([
                            'statuscode' => 'TXF',
                            "status" => $result['error'],
                            'message' => $result['error'],
                            'data' => $result['error']
                        ]);
                    }

                    break;

                case 'tempBook':
                    dd($url, $response, $result, str_replace(["\\\/\\\/", "\\\/", "\/", "'\'"], "/", json_encode($param)));
                    if ($result['code'] == 200 || $result['error'] == null) {
                        if ($response->Response_Header->Error_Code == 0000 && $response->Response_Header->Status_Id == "11") {
                            $getUser = User::where('id', \Auth::id())->first();
                            $insertDB = [
                                "user_id" => \Auth::id(),
                                "passenger_details" => $passDetail,
                                //$request->passenger_details,
                                "passenger_mobile" => $request->psngrMobile,
                                "passenger_email" => $request->passengerEmail,
                                "flight_id" => $request->flight_id,
                                "booking_refno" => $response->Booking_RefNo,
                                "fare_Id" => $request->fare_Id,
                                "ticket_type" => $request->ticket_type,
                                "booking_remarks" => $request->bookingRemark,
                                "customer_mobile" => $getUser->mobile,
                                "passanger_count" => $request->passanger_count,
                                "origin" => $request->origin,
                                "destination" => $request->destination,
                                "travel_date" => $request->travelDate,
                                "booking_type" => $request->bookingType,
                                "travel_class" => $request->travelClass,
                                "travel_type" => $request->travelType,
                                "adult_passenger" => $request->adultCount,
                                "child_passenger" => $request->childCount,
                                "infant_passenger" => $request->infantCount
                            ];

                            if ($insertDB) {
                                return response()->json([
                                    'statuscode' => 'TXN',
                                    "status" => $response->Response_Header->Error_Desc,
                                    'message' => $response->Response_Header->Error_Desc,
                                    'data' => [
                                        "Status_Id" => $response->Response_Header->Status_Id,
                                        "Request_Id" => $response->Response_Header->Request_Id,
                                    ]
                                ]);
                            }

                        } else {
                            return response()->json([
                                'statuscode' => 'TXF',
                                "status" => @$response->Response_Header->Error_Desc,
                                'message' => @$response->Response_Header->Error_Desc,
                                'data' => [
                                    "Status_Id" => @$response->Response_Header->Status_Id,
                                    "Request_Id" => @$response->Response_Header->Request_Id,

                                ]
                            ]);
                        }
                    } else {
                        return response()->json([
                            'statuscode' => 'TXF',
                            "status" => $result['error'],
                            'message' => $result['error'],
                            'data' => $result['error']
                        ]);
                    }

                    break;

                case 'fareRule':
                    dd($url, $response, $result, str_replace(["\\\/\\\/", "\\\/", "\/", "'\'"], "/", json_encode($param)));
                    if ($result['code'] == 200 || $result['error'] == null) {
                        if ($response->Response_Header->Error_Code == 0000 && $response->Response_Header->Status_Id == "11") {
                            return response()->json([
                                'statuscode' => 'TXN',
                                "status" => $response->Response_Header->Error_Desc,
                                'message' => $response->Response_Header->Error_Desc,
                                'data' => [
                                    "Status_Id" => $response->Response_Header->Status_Id,
                                    "Request_Id" => $response->Response_Header->Request_Id,
                                    "fareRulesdesc" => $response->FareRules[0]->FareRuleDesc,
                                    "fareRuleName" => $response->FareRules[0]->FareRuleName,
                                    "segmentId" => $response->FareRules[0]->Segment_Id,
                                ]
                            ]);

                        } else {
                            return response()->json([
                                'statuscode' => 'TXF',
                                "status" => @$response->Response_Header->Error_Desc,
                                'message' => @$response->Response_Header->Error_Desc,
                                'data' => [
                                    "Status_Id" => @$response->Response_Header->Status_Id,
                                    "Request_Id" => @$response->Response_Header->Request_Id,
                                    "fareRulesdesc" => @$response->FareRules[0]->FareRuleDesc,
                                    "fareRuleName" => @$response->FareRules[0]->FareRuleName,
                                    "segmentId" => @$response->FareRules[0]->Segment_Id,

                                ]
                            ]);
                        }
                    } else {
                        return response()->json([
                            'statuscode' => 'TXF',
                            "status" => $result['error'],
                            'message' => $result['error'],
                            'data' => $result['error']
                        ]);
                    }

                    break;

                case 'ticket':
                    // dd($url, $response, $result, str_replace(["\\\/\\\/", "\\\/", "\/", "'\'"], "/", json_encode($param)));
                    if ($result['code'] == 200 || $result['error'] == null) {
                        if ($response->Response_Header->Error_Code == 0000 && $response->Response_Header->Status_Id == "11") {

                            $update_PNR_DB = DB::table('flight_txn_details')->where('booking_refno', $param['Booking_RefNo'])->update(['airline_PNR' => $response->AirlinePNRDetails, "ticket_type" => $param['Ticketing_Type']]);

                            if ($update_PNR_DB) {
                                return response()->json([
                                    'statuscode' => 'TXN',
                                    "status" => $response->Response_Header->Error_Desc,
                                    'message' => $response->Response_Header->Error_Desc,
                                    'data' => [
                                        "statusId" => $response->Response_Header->Status_Id,
                                        "requestId" => $response->Response_Header->Request_Id,
                                        "bookingRefNo" => $request->Booking_RefNo,
                                        "airlinePNR" => $response->AirlinePNRDetails,
                                        // "failureRemarks" => $request->AirlinePNRDetails[0]->Failure_Remark,
                                        // "flightId" => $request->AirlinePNRDetails[0]->Flight_Id,
                                        // "holdValidity" => $request->AirlinePNRDetails[0]->Hold_Validity,
                                        // // "statusId"=>$request->AirlinePNRDetails[0]->Status_Id
                                        // "airlineCode" => $request->AirlinePNRDetails[0]->AirlinePNRs[0]->Airline_Code,
                                        // "airlinePNR" => $request->AirlinePNRDetails[0]->AirlinePNRs[0]->Airline_PNR,
                                        // "crsCode" => $request->AirlinePNRDetails[0]->AirlinePNRs[0]->CRS_Code,
                                        // "crsPNR" => $request->AirlinePNRDetails[0]->AirlinePNRs[0]->CRS_PNR,
                                        // "recordLocator" => $request->AirlinePNRDetails[0]->AirlinePNRs[0]->Record_Locator,
                                        // "supplierRefNo" => $request->AirlinePNRDetails[0]->AirlinePNRs[0]->Supplier_RefNo

                                    ]
                                ]);
                            }

                        } else {
                            return response()->json([
                                'statuscode' => 'TXF',
                                "status" => @$response->Response_Header->Error_Desc,
                                'message' => @$response->Response_Header->Error_Desc,
                                'data' => [
                                    "Status_Id" => @$response->Response_Header->Status_Id,
                                    "Request_Id" => @$response->Response_Header->Request_Id,
                                    "bookingRefNo" => @$request->Booking_RefNo,
                                    "airlinePNR" => $response->AirlinePNRDetails,

                                    // "failureRemarks" => @$request->AirlinePNRDetails[0]->Failure_Remark,
                                    // "flightId" => @$request->AirlinePNRDetails[0]->Flight_Id,
                                    // "holdValidity" => @$request->AirlinePNRDetails[0]->Hold_Validity,
                                    // // "statusId"=>@$request->AirlinePNRDetails[0]->Status_Id
                                    // "airlineCode" => @$request->AirlinePNRDetails[0]->AirlinePNRs[0]->Airline_Code,
                                    // "airlinePNR" => @$request->AirlinePNRDetails[0]->AirlinePNRs[0]->Airline_PNR,
                                    // "crsCode" => @$request->AirlinePNRDetails[0]->AirlinePNRs[0]->CRS_Code,
                                    // "crsPNR" => @$request->AirlinePNRDetails[0]->AirlinePNRs[0]->CRS_PNR,
                                    // "recordLocator" => @$request->AirlinePNRDetails[0]->AirlinePNRs[0]->Record_Locator,
                                    // "supplierRefNo" => @$request->AirlinePNRDetails[0]->AirlinePNRs[0]->Supplier_RefNo


                                ]
                            ]);
                        }
                    } else {
                        return response()->json([
                            'statuscode' => 'TXF',
                            "status" => $result['error'],
                            'message' => $result['error'],
                            'data' => $result['error']
                        ]);
                    }

                    break;

                case 'tripHistory':
                    // dd($url, $response, $result, str_replace(["\\\/\\\/", "\\\/", "\/", "'\'"], "/", json_encode($param)));
                    if ($result['code'] == 200 || $result['error'] == null) {
                        if ($response->Response_Header->Error_Code == 0000 && $response->Response_Header->Status_Id == "11") {

                            // foreach($response->TicketHistory as $key=>$val){
                            //     $tickethistory[]
                            // }


                            return response()->json([
                                'statuscode' => 'TXN',
                                "status" => $response->Response_Header->Error_Desc,
                                'message' => $response->Response_Header->Error_Desc,
                                'data' => [
                                    "Status_Id" => $response->Response_Header->Status_Id,
                                    "Request_Id" => $response->Response_Header->Request_Id,
                                    "ticketHistory" => $response->TicketHistory
                                    // "airlinePNR" => $response->Response_Header->TicketHistory[0]->AirlinePNR,
                                    // "bookingType" => $response->Response_Header->TicketHistory[0]->BookingType,
                                    // "cancellationCharge" => $response->Response_Header->TicketHistory[0]->CancellationCharges,
                                    // "cstmrMobile" => $response->Response_Header->TicketHistory[0]->CustomerMobile,
                                    // "cstmrName" => $response->Response_Header->TicketHistory[0]->CustomerName,
                                    // "gatewayCharge" => $response->Response_Header->TicketHistory[0]->GatewayCharges,
                                    // "grossAmount" => $response->Response_Header->TicketHistory[0]->GrossAmount,
                                    // "invoiceAmount" => $response->Response_Header->TicketHistory[0]->InvoiceNumber,
                                    // "operatorId" => $response->Response_Header->TicketHistory[0]->OperatorId,
                                    // "operatorName" => $response->Response_Header->TicketHistory[0]->OperatorName,
                                    // "psngrName" => $response->Response_Header->TicketHistory[0]->PassengerName,
                                    // "promoAmount" => $response->Response_Header->TicketHistory[0]->PromoAmount,
                                    // "refNo" => $response->Response_Header->TicketHistory[0]->Refno,
                                    // "reqDate" => $response->Response_Header->TicketHistory[0]->ReqDate,
                                    // "retailerComm" => $response->Response_Header->TicketHistory[0]->RetailerCommission,
                                    // "ticketHistory" => $response->Response_Header->TicketHistory[0]->ReqDate,


                                ]
                            ]);

                        } else {
                            return response()->json([
                                'statuscode' => 'TXF',
                                "status" => @$response->Response_Header->Error_Desc,
                                'message' => @$response->Response_Header->Error_Desc,
                                'data' => [
                                    "Status_Id" => @$response->Response_Header->Status_Id,
                                    "Request_Id" => @$response->Response_Header->Request_Id,

                                ]
                            ]);
                        }
                    } else {
                        return response()->json([
                            'statuscode' => 'TXF',
                            "status" => $result['error'],
                            'message' => $result['error'],
                            'data' => $result['error']
                        ]);
                    }

                    break;

                case 'reprint':
                    // dd($url, $response, $result, str_replace(["\\\/\\\/", "\\\/", "\/", "'\'"], "/", json_encode($param)));
                    if ($result['code'] == 200 || $result['error'] == null) {
                        if ($response->Response_Header->Error_Code == 0000 && $response->Response_Header->Status_Id == "11") {
                            return response()->json([
                                'statuscode' => 'TXN',
                                "status" => $response->Response_Header->Error_Desc,
                                'message' => $response->Response_Header->Error_Desc,
                                'data' => [
                                    "Status_Id" => $response->Response_Header->Status_Id,
                                    "Request_Id" => $response->Response_Header->Request_Id,
                                    "Adult_Count" => $response->Adult_Count,
                                    "PNRDetails" => $response->AirPNRDetails
                                ]
                            ]);

                        } else {
                            return response()->json([
                                'statuscode' => 'TXF',
                                "status" => @$response->Response_Header->Error_Desc,
                                'message' => @$response->Response_Header->Error_Desc,
                                'data' => [
                                    "Status_Id" => @$response->Response_Header->Status_Id,
                                    "Request_Id" => @$response->Response_Header->Request_Id,

                                ]
                            ]);
                        }
                    } else {
                        return response()->json([
                            'statuscode' => 'TXF',
                            "status" => $result['error'],
                            'message' => $result['error'],
                            'data' => $result['error']
                        ]);
                    }

                    break;

                case 'cancellation':
                    // dd($url, $response, $result, str_replace(["\\\/\\\/", "\\\/", "\/", "'\'"], "/", json_encode($param)));
                    if ($result['code'] == 200 || $result['error'] == null) {
                        if ($response->Response_Header->Error_Code == 0000 && $response->Response_Header->Status_Id == "11") {

                            $update_cancel_DB = DB::table('flight_txn_details')->where('booking_refno', $param['RefNo'])->update(['cancel_remarks' => $param['ReqRemarks'], "cancel_type" => $param['cancel_type'], "cancellation_details" => $param['AirTicketCancelDetails'], "cancellation_date" => date('d-m-Y H:i:s')]);

                            if ($update_cancel_DB) {

                                return response()->json([
                                    'statuscode' => 'TXN',
                                    "status" => $response->Response_Header->Error_Desc,
                                    'message' => $response->Response_Header->Error_Desc,
                                    'data' => [
                                        "Status_Id" => $response->Response_Header->Status_Id,
                                        "Request_Id" => $response->Response_Header->Request_Id,
                                        "c_resp" => $response

                                    ]
                                ]);
                            }

                        } else {
                            return response()->json([
                                'statuscode' => 'TXF',
                                "status" => @$response->Response_Header->Error_Desc,
                                'message' => @$response->Response_Header->Error_Desc,
                                'data' => [
                                    "Status_Id" => @$response->Response_Header->Status_Id,
                                    "Request_Id" => @$response->Response_Header->Request_Id,

                                ]
                            ]);
                        }
                    } else {
                        return response()->json([
                            'statuscode' => 'TXF',
                            "status" => $result['error'],
                            'message' => $result['error'],
                            'data' => $result['error']
                        ]);
                    }

                    break;

                case 'lowFare':
                    // dd($url, $response, $result, str_replace(["\\\/\\\/", "\\\/", "\/", "'\'"], "/", json_encode($param)));
                    if ($result['code'] == 200 || $result['error'] == null) {
                        if ($response->Response_Header->Error_Code == 0000 && $response->Response_Header->Status_Id == "11") {
                            return response()->json([
                                'statuscode' => 'TXN',
                                "status" => $response->Response_Header->Error_Desc,
                                'message' => $response->Response_Header->Error_Desc,
                                'data' => [
                                    "Status_Id" => $response->Response_Header->Status_Id,
                                    "Request_Id" => $response->Response_Header->Request_Id,
                                    "c_resp" => $response
                                ]
                            ]);

                        } else {
                            return response()->json([
                                'statuscode' => 'TXF',
                                "status" => @$response->Response_Header->Error_Desc,
                                'message' => @$response->Response_Header->Error_Desc,
                                'data' => [
                                    "Status_Id" => @$response->Response_Header->Status_Id,
                                    "Request_Id" => @$response->Response_Header->Request_Id,

                                ]
                            ]);
                        }
                    } else {
                        return response()->json([
                            'statuscode' => 'TXF',
                            "status" => $result['error'],
                            'message' => $result['error'],
                            'data' => $result['error']
                        ]);
                    }

                    break;

                case 'getBalance':
                    // dd($url, $response, $result, str_replace(["\\\/\\\/", "\\\/", "\/", "'\'"], "/", json_encode($param)));
                    if ($result['code'] == 200 || $result['error'] == null) {
                        if ($response->Response_Header->Error_Code == 0000 && $response->Response_Header->Status_Id == "11") {

                            return response()->json([
                                'statuscode' => 'TXN',
                                "status" => $response->Response_Header->Error_Desc,
                                'message' => $response->Response_Header->Error_Desc,
                                'data' => [
                                    "Status_Id" => $response->Response_Header->Status_Id,
                                    "Request_Id" => $response->Response_Header->Request_Id,
                                    "CreditBalance" => $response->CreditBalance,
                                    "EffectiveBalance" => $response->EffectiveBalance,
                                    "LienBalance" => $response->LienBalance,
                                    "ODAmount" => $response->ODAmount


                                ]
                            ]);

                        } else {
                            return response()->json([
                                'statuscode' => 'TXF',
                                "status" => @$response->Response_Header->Error_Desc,
                                'message' => @$response->Response_Header->Error_Desc,
                                'data' => [
                                    "Status_Id" => @$response->Response_Header->Status_Id,
                                    "Request_Id" => @$response->Response_Header->Request_Id,
                                    "CreditBalance" => @$response->CreditBalance,
                                    "EffectiveBalance" => @$response->EffectiveBalance,
                                    "LienBalance" => @$response->LienBalance,
                                    "ODAmount" => @$response->ODAmount

                                ]
                            ]);
                        }
                    } else {
                        return response()->json([
                            'statuscode' => 'TXF',
                            "status" => $result['error'],
                            'message' => $result['error'],
                            'data' => $result['error']
                        ]);
                    }

                    break;

                case 'addPayment':
                    // dd($url, $response, $result, str_replace(["\\\/\\\/", "\\\/", "\/", "'\'"], "/", json_encode($param)));
                    if ($result['code'] == 200 || $result['error'] == null) {
                        if ($response->Response_Header->Error_Code == 0000 && $response->Response_Header->Status_Id == "11") {

                            $balanceupdate = $this->calculationAmount(\Auth::id(), $request->amount);

                            if (!$balanceupdate) {
                                return response()->json(['status' => 'TXF', 'message' => "Something went wrong,Please try after sometime"]);
                            }

                            $update_Ref_Amount = DB::table('flight_txn_details')
                                ->where('booking_refno', $param['RefNo'])
                                ->update(['client_ref_no' => $param['ClientRefNo'], "amount" => $request->amount, "payment_id" => $response->PaymentID, "updated_at" => date('d-m-Y H:i:s')]);

                            if ($update_Ref_Amount) {
                                return response()->json([
                                    'statuscode' => 'TXN',
                                    "status" => $response->Response_Header->Error_Desc,
                                    'message' => $response->Response_Header->Error_Desc,
                                    'data' => [
                                        "Status_Id" => $response->Response_Header->Status_Id,
                                        "Request_Id" => $response->Response_Header->Request_Id,
                                        "Amount" => $response->Amount,
                                        "PaymentID" => $response->PaymentID,
                                    ]
                                ]);
                            } else {
                                return response()->json([
                                    'statuscode' => 'TXF',
                                    "status" => "fail",
                                    'message' => "Please try after Sometime",
                                    'data' => "Please try after Sometime"
                                ]);
                            }


                        } else {
                            return response()->json([
                                'statuscode' => 'TXF',
                                "status" => @$response->Response_Header->Error_Desc,
                                'message' => @$response->Response_Header->Error_Desc,
                                'data' => [
                                    "Status_Id" => @$response->Response_Header->Status_Id,
                                    "Request_Id" => @$response->Response_Header->Request_Id,
                                    "Amount" => @$response->Amount,
                                    "PaymentID" => @$response->PaymentID,
                                ]
                            ]);
                        }
                    } else {
                        return response()->json([
                            'statuscode' => 'TXF',
                            "status" => $result['error'],
                            'message' => $result['error'],
                            'data' => $result['error']
                        ]);
                    }

                    break;

                default:
                    return response()->json(['statuscode' => "ERR", "message" => "Invalid Response"]);


            }


        } catch (Exception $e) {
            return response()->json([
                'statuscode' => 'ERR',
                'status' => 'error',
                'message' => "Something went wrong, please try after some time",
                'data' => $e->getMessage()

            ]);
        }
    }



    public function calculationAmount($user_id, $debitamount)
    {

        $getUser = User::where('id', $user_id)->first();

        $user_wallet_amount = $getUser->mainwallet;

        if ($user_wallet_amount < $debitamount) {
            return response()->json([
                'statuscode' => 'TXF',
                "status" => "Insufficient wallet balance",
                'message' => "Insufficient wallet balance",
                'data' => []
            ]);
        }

        $calculate_amount = $user_wallet_amount - $debitamount;

        // For Begin a transaction
        DB::beginTransaction();
        $update_wallet_balance = DB::table('users')->where('id', $user_id)->update(['mainwallet' => $calculate_amount]);
        if ($update_wallet_balance) {
            // Commit the transaction
            DB::commit();
            $val = true;
        } else {
            // An error occured
            DB::rollback();
            $val = false;
        }
        return $val;
    }

    public function flightkeys()
    {
        $flight["flightKey"] = "Z/Wy7sUWNIxMKEAXgPFIMBpa/5NWOLRci1eogWmWmb4z921dBrDPx2DlIS0EjvB5fyFSPHp0isKtGtgwcBK2VCP/ghtvswgwvxXKXEEQnKvDpyFtdAPzPVKRYVhVz7VMVomi9Gl3/p7XcFkDEPlL+isewielwODT611lOTAXnTkoyVmrqdL/Frw5zUIsQ1TxgEsKMYC7p0drQJT3dXAh7itsqU51tk7ondteNEeC09JMrriLmmWuICIlaRdLecwUcNgMW1iLJ2d4M9X0yzK+O7/qhWOd0wCANtUIh1Pva6aFA/lxpTBKtxcn73UQXH7P/Jg/PkgJtPghdabT3I7m/KCzVnuWzoBV7z/tX1LS5MJzIZYzfmF8kTWCX2fSGjMjCuhTHhBF0xbZQu7uxrSxGJrpdXHw52FUK6qHM/lztptbVmpYKYZA3u3K7V0/Kr4FGzEujNTGbwEMHJuBdo7uMJhs0OqHcpcZNPZVJZ3d0Hx2jmQ9KW9vboXlsfflDdRWJTXoy+1rLaqW5Dcz2+uDn22ay4sBrpRElIGc7PPR+x0Z72FJCD44t8wRsb+uonU0XaXvIQjYMW3pnJh5n5gFu2wTQ7OvpE2SF1n3fDD5j7Lz79F8b4t8uyFQuNMELIYS2XOeSO2a2Q6yN6Zvm+RRU/yEg14UP0fWTLYHsNrFRvJNFcqXNqy3a1KoYeLN6dfRWjKFMftHLrF8L7iTV7DT2T/3RDoMl2Bp9Kz4mH6YrVc5F6fnJ/4T8Nfa0nFnFGXVHaHel3ep8Pvy+ukyGvsO9uo8tvXVeEXaiLgAZGp43/RpuyEwJKDjQLo7TPDQeISHI0AyNWbraKRyuujWy6dRnyJP0kQVCfm8Fj54iKOai1ebub15QgdxSyfbiCRHdtjC47Et7d0YGNNh0D0Z/ok//uJav+kcSyMldoO/Z8uZVe+P2FQxYNFpxFSafkP8ls0PtxbMoXRp3on3hfaOAXMeKVWxpbiocmWO7zdVaSX3csc6P7eL85acsaW3yCZBxzeFLSacKxcL5GgQIJrewla5r4aojWSjQoH/iYhKwVK8x0654eobCExDzGVVP0i/8Dfn/niIu4AQC39sA1iGvriT9TIf1ZkaP/LuVdeZQNpdS8dbNS2xx/hR0Skgjkgvq4Gi0VgKptwGrVvoUs2c9MmniNNx7r3Ah7LhcxVUfVsF/VVz1HbTlgfh/ux+gUeNaVhzMs65n1v2xocDayb36FC8PZaWR9Qe/5wSnRnYTx6qyLyhsK0tdxdIegAwN6wHiSjcD0/bYGWOpH1zBrGnvARG7NDe4iEwDbkI6CyJg1LSd0jZZ+BMc2OA3a0iqz4+fKHEEe+u1Vsi6bwpPis0e+J5u3kj2q6/OjFz74oCB3YTyt3tMGq3sTg8eQBnZlk6NlJzJWQbjKNnpt0nzUn/vq7oie9haqw65xQc/lLlJZY41MNXc2wWpUoyKcq0LcOmJbj09zAMewZoVeA26/Y3totObmSkwf/Qis9a65WXHUJkTQQtdkaWVI2FXSZ9kzoCWR5GbvRRBK/atTY7iVf5W6rl6Jcobi9hYLVykGXAu+Jjl5Gr6D/tbjhr0gGygKJurasgl6mv0PhP1iBbYYbJldpKJtLT6wqdkqtVZsvxkkXDcxC16vCwA9Zsg+U156ir5iB0AYDZTkfVWwVOYDnnT07MJZJXp3lTnQ+Qya+s3qF1He9j9F6iYc41p4wGSJsJLASml6v7NoAj0LmPd+/BvhSr4etXKRLcS8BBsuDA2YPRW+oEyUpxyqOYGN97wbRRSi2IuqO5ZRhpDreZTnv32YFMNFbSTIkrfgbiJN49iixGkloJKCEOKZdXmouNvw3d5xsk5hjT4QAca2jsdyQniozboihVkJ23HlvoC+TqgHWXVbzryFNzz9ssOb1nmRHbAiNGYUupVEj9KevBoX9mw7NZe7VInDNDU/FgGxubPXvDDPrnZOW7y+qkacF1Jg+BN5OmtfhzFA4vSDtvADyN07pn6SgZ6FgC4mGsut4MAVtdtsbo50BwtSWqOWULpbNejxD0PVsgxTukl48n4LYTzizUrguktdexK+RedFLa16VtJGB1GPLbftqseUrfljk/X9Jh2rqQUml6s+ns+hMvR4FC8U/PTNafU0TAB46gj++H+3o71iJIYIbbKkPI93U/9i53ICzrtBHmzjOrN1qkYxO8Hib10J23eKDJg4U7xFKlxCtMqsAuzoSN7PGA6o3G3XD4OZ2U3eJDdindw7QtvgaMmIZHoTPKWCxgW9C+4aFuq9wG3Vznpr4tzXdunTn0edwp7rpHD3caueguDf8GnSoCKt0btXg4N5dGIxZmyA/rqdw48Rx8GAKMUVrIFqIwhXbC3LJEVprXzNOrVWr8tLeNop0rpCNs6kOuO8kR2zDSb93ay8L8bO4RmHSyGQlfXf+sV+eciSmczDBU+L+/atv5Vup0MWiMqtPNkBgl79y0hJXNCiV4wlrukSq1QRdfioKViS9SPTa0YSWFBRO32q9MqbBxPQQRD0xZu3hkLQaDqckXlbHsox3qk0Uz27/02dCDyyWACjERW7RrW49vLEsuWPF4rRfb2eExEmF8KEu2gKJNtkNTmcxd+nUNPB+8wr1+rhTXrF8EK17FrbyXoVGbdS+lQh6yOwU6utK2HU1nxoeG8hf2BOCcw1rga40gfS0RE1V0qP7XNPASU4kOe16CncMeqtqoUR5m8vHdMf+PnIbxIOoKTo1YbggyP1hYBMYXNKh16sPhODIOepJTxRUkdOJsNvL5e+kyWrvhx8jAkowWEj3uIrKaL6jDfu6wV3YlZt2u9/joE9xTS81Yf25Kf7dB8UouiwQQp22kS+jOat43Fuey3B7p9Hg4Cr0i0F4awpOyre3/7iPmhmFgnYxntTpYxLicty+BpCh55FEFEmE=";
        $flight['searchKey'] = "GtTLt6riGKCzFpiKvuSDBruvBfpW/v6PEgAMFOShKJh965WvysGUyaUp0gpZcg4IhzTgrMsbXahXCh4rRx/Mvn4+ujmIu6mejCugbWAeqsdimz79B4ONVU+oUrYXO8C3Q/9DmpaPWJZTe1ty97mjSZjkfFzjHcq6jxRD+oEZBdvsjMkCMEgo82N/N8iw1KXG3DPeDkRpCgIGj+Ja9R2kKJQeqHRK1FLJbdnT35iKW/0WaqI9OsRDbo5qIkHZKwdB6JDV7JeSZXhtYG0HOso0gKNNny7G6qbmPcy5QVUoHUTr7SUoVkIV73AviFrIiwsFXiLrPxCPWpLQg0kqDMRCxIYRgIRm/0qJate0v8gLAW6cKugChfcBNAUpqpgOeJlUNaACS8hMxpaF++/7lFkZ3wxBHSc/VCfbHhdKzAfvfro2xGlfdRBNdenSNUle5CpyCmy9kNEfJWR2j3r46WLeYRQb6aUwCHfVQolcs3eEXGstKu3a7OvSoTRkFou0tB408TazQYfdigqwgBvs+NMRWCqxcB+GohyS0S5/9UEaVcx3hSllNqFGbd8H/gNOzm9U0EaLikvq6Oj0kFUH/v/jBNWVQxLvbEu0aK1XEtJrFD29xQ6HHGu5usqLc+XBM4hCJZdZFwXv6BDA4G44erxXHe0sezkmz31P/1F9ica2hYY8jHWgCVx+CR/B5umtFpfF";
        $flight["passengerDetails"] = [
            json_encode([
                "psngr_id" => "1",
                "psngr_type" => "0",
                "psngr_title" => "Mr",
                "first_name" => "Sample",
                "last_name" => "test",
                "psngr_gender" => 0,
                "psngr_age" => null,
                "psngr_dob" => null,
                "passport_number" => null,
                "passport_issuing_country" => null,
                "passport_expiry" => null,
                "nationality" => "INDIAN",
                "frequent_flyer_details" => null
            ]),
            // [
            //     {
            //         "Pax_Id": 1,
            //         "Pax_type": 0,
            //         "Title": "Mr",
            //         "First_Name": "Testing",
            //         "Last_Name": "Sample",
            //         "Gender": 0,
            //         "Age": null,
            //         "DOB": null,
            //         "Passport_Number": null,
            //         "Passport_Issuing_Country": null,
            //         "Passport_Expiry": null,
            //         "Nationality": null,
            //         "FrequentFlyerDetails": null
            //     }
            // ],
            // json_encode([
            //     "psngr_id" => "2",
            //     "psngr_type" => "0",
            //     "psngr_title" => "Mr",
            //     "first_name" => "Sa3mple",
            //     "last_name" => "te2st",
            //     "psngr_gender" => "male",
            //     "psngr_age" => "23",
            //     "psngr_dob" => "19/09/1999",
            //     "passport_number" => "null",
            //     "passport_issuing_country" => "null",
            //     "passport_expiry" => "null",
            //     "nationality" => "INDIAN",
            //     "frequent_flyer_details" => "null"
            // ])
        ];

        return $flight;
    }
}