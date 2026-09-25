<?php

namespace App\Http\Controllers\Android;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Api;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use function PHPUnit\Framework\isEmpty;

class FlightController extends Controller
{
    //
    public $flight_cred;

    public function __construct()
    {
        $flight_cred = Api::where('code', 'flight')->first();
        if ($flight_cred) {
            $this->flight_cred['username'] = $flight_cred->username;
            $this->flight_cred['password'] = $flight_cred->password;
            $this->flight_cred['url'] = $flight_cred->url;
            $this->flight_cred['ip'] = $flight_cred->optional1;
            $this->flight_cred['imei'] = $flight_cred->optional2;
        } else {
            $this->flight_cred = ['username' => '', 'password' => '', 'url' => '', 'ip' => '', 'imei' => ''];
        }
    }

    public function flight(Request $request)
    {
        $rules["type"] = ['required'];
        $rules["user_id"] = ['required'];


        if ($request->user_id == null) {
            return response()->json(['Status_Code' => "ERR", "Message" => "User Not Found"]);
        } else {
            $getUserDetails = User::where('id', $request->user_id)->first();
        }

        switch ($request->type) {
            case "getCity":
                $rules["city"] = ['nullable'];
                break;

            case "airlineImage":
                $rule['airlineCode'] = ['nullable'];
                break;

            case "fetch":
                $rules['travelInfo'] = 'required|array';
                $rules["adultCount"] = ['required', 'numeric', 'min:0'];
                $rules["childCount"] = ['required', 'numeric', 'min:0'];
                $rules["infantCount"] = ['required', 'numeric', 'min:0']; // ['required|numeric'];
                $rules["travelClass"] = ['required', 'in:0,1,2,3']; //0-ECONOMY,1-BUSINESS,2-FIRST,3-PREMIUM_ECONOMY
                $rules["airlinefilter"] = ['nullable'];
                $rules["travelType"] = ['required', 'in:0,1']; //0-DOMESTIC,1-INTERNATIONAL
                $rules["bookingType"] = ['required', 'in:0,1,2']; // 0-ONE_WAY,1-ROUNDTRIP,2-SPECIALROUNDTRIP



                break;

            case 'reprice':
                $rules["searchKey"] = ['required']; // ['required'];
                $rules["flightSearch"] = 'required|array'; // ['required'];
                $rules["requestId"] = ['required'];
                break;

            case 'getSSR':
                $rules["flightKey"] = ['nullable']; // ['required'];
                $rules["searchKey"] = ['nullable']; // ['required'];
                $rules["requestId"] = ['required'];

                break;

            case 'seatMap':
                $rules["flightKey"] = ['nullable']; // ['required'];
                $rules["searchKey"] = ['nullable']; // ['required'];
                $rules["passengerDetails"] = ['nullable']; // ['required'];
                $rules["requestId"] = ['required'];


                break;
            case 'ticket':
                // dd($request->all());
                $rules['bookingRefNo'] = ['required'];
                $rules['ticketType'] = ['nullable'];
                $rules["requestId"] = ['required'];


                break;
            case 'tempBook':
                $rules["requestId"] = ['required'];
                $rules['travelInfo'] = 'required|array';
                // $rules["passengerDetails"] = ['required']; // ['required'];
                $rules['passengerMobile'] = ['required', "numeric", 'digits_between:9,11']; // ["required"];
                $rules['whatsappMobile'] = ['nullable', "numeric", 'digits_between:9,11']; // ["required"];
                $rules['passengerEmail'] = ['required']; // ["required"];
                $rules['flightId'] = ['required']; // ["required"];
                $rules['fareId'] = ['required']; // ["required"];
                // $rules['gstNumber'] = ['nullable']; // ["nullable"];
                // $rules['gstHolderName'] = ['nullable']; // ["nullable"];
                // $rules['gstAddress'] = ['nullable']; // ["nullable"];
                $rules['searchKey'] = ['required']; // ["required"];
                $rules['flightKey'] = ['required']; // ["required"];
                // $rules['costCentreId'] = ['nullable']; // ["required"];
                // $rules['projectId'] = ['nullable']; // ["required"];
                $rules['bookingRemark'] = ['nullable']; // ["required"];
                $rules['total_amount'] = ['required', "numeric", 'min:1']; // ["required"];
                $rules['base_amount'] = ['nullable', "numeric", 'min:1']; // ["required"];
                $rules['tax_amount'] = ['nullable', "numeric", "min:1"]; // ["required"];
                // $rules['corporateStatus'] = ['nullable']; // ["required"];
                // $rules['corporatePaymentMode'] = ['nullable']; // ["required"];
                // $rules['missedSavingReason'] = ['nullable']; // ["nullable"];
                // $rules['corpTripType'] = ['nullable']; // ["nullable"];
                // $rules['corpTripSubType'] = ['nullable']; // ["nullable"];
                // $rules['tripRequestId'] = ['nullable']; // ["nullable"];
                // $rules['bookingAlertIds'] = ['nullable']; // ["nullable"];
                // $rules["origin"] = ['nullable']; // ['required'];
                // $rules["destination"] = ['nullable']; // ['required'];
                // $rules["travelDate"] = ['nullable']; // ['required'];
                $rules["adultCount"] = ['required', "numeric", "min:0"]; // ['required|numeric|min:1'];
                $rules["childCount"] = ['required', "numeric", "min:0"]; // ['required|numeric'];
                $rules["infantCount"] = ['required', "numeric", "min:0"]; // ['required|numeric'];
                $rules["travelClass"] = ['required', 'in:0,1,2,3']; //0-ECONOMY,1-BUSINESS,2-FIRST,3-PREMIUM_ECONOMY
                $rules["travelType"] = ['required', 'in:0,1']; //0-DOMESTIC,1-INTERNATIONAL
                $rules["bookingType"] = ['required', 'in:0,1,2']; // 0-ONE_WAY,1-ROUNDTRIP,2-SPECIALROUNDTRIP

                break;

            case 'reprint':
                $rules["requestId"] = ['nullable'];
                $rules['bookingRefNo'] = ['required']; // ['required'];
                $rules['airlinePNR'] = ['nullable']; // ['required'];
                break;

            case 'cancellation':
                $rules["requestId"] = ['nullable'];
                $rules['flightId'] = ['nullable']; // ['required'];
                $rules['pId'] = ['nullable']; // ['required'];
                $rules['segmentId'] = ['nullable']; // ['required'];
                $rules['airlinePNR'] = ['nullable']; // ['required'];
                $rules['bookingRefNo'] = ['required']; // ['required'];
                $rules['cancelRemarks'] = ['nullable']; // ['required'];
                $rules['cancelType'] = ['nullable', 'in:0,1,2']; // ['required'];   // 0-Normal Cancel,1-Full Refund,2-No Show

                break;

            case 'lowfare':
                $rules["requestId"] = ['nullable'];
                $rules['destination'] = ['required']; // ['required'];
                $rules['month'] = ['required']; // ['required'];
                $rules['origin'] = ['required']; // ['required'];
                $rules['year'] = ['required']; // ['required'];

                break;
            case 'tripHistory':
                $rules["requestId"] = ['required'];
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
                $rules["requestId"] = ['required'];
                $rules = ["amount" => 'required|numeric|min:1'];
                $rules = ["bookingRefNo" => 'required|string'];

                break;

            case 'lowFare':
                $rules = [];
                $rules["requestId"] = ['nullable'];


                break;

            case 'fareRule':
                $rules["requestId"] = ['required'];
                $rules['searchKey'] = ['required']; // ['required'];
                $rules['fareId'] = ['required']; // ['required'];
                $rules['flightKey'] = ['required']; // ['required'];
                break;

            default:
                return response()->json(['Status_Code' => "ERR", "Message" => "Invalid Type used "]);
                break;
        }

        $validator = Validator::make($request->all(), $rules);

        if ($request->type == 'seatMap' || $request->type == 'tempBook' || $request->type == "fetch" || $request->type == 'reprice') {
            $validator->after(function ($validator) {
                $config = request()->get('passengerDetails');
                if (request()->get('type') == "tempBook" || request()->get('type') == "fetch") {
                    $tripInfo = request()->get('travelInfo');

                }

                if (request()->get('type') == 'reprice') {
                    $flightSearch = request()->get('flightSearch');
                }
                $getAmount = request()->get('amount');
                $t_Amount = request()->get('total_amount');

                $isflight = true;
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


                if (!empty($flightSearch) > 0) {
                    foreach ($flightSearch as $conf) {
                        if (empty($conf['flightKey']) && empty($conf['fareId'])) {
                            $isflight = false;
                            break;
                        }
                    }

                }

                if (!empty($config) > 0) {
                    foreach ($config as $key => $conf) {

                        $paxtype = [0, 1, 2];
                        if (!in_array($conf["pType"], $paxtype)) {
                            $validator->errors()->add("psngrType", "passenger type is invalid");
                        }

                        $paxgender = [0, 1];
                        if (!in_array($conf["gender"], $paxgender)) {
                            $validator->errors()->add("psngrType", "passenger gender is invalid");
                        }

                        $paxid = [1, 2, 3, 4, 5, 6, 8, 9, 10];
                        if (!in_array($conf["pId"], $paxid)) {
                            $validator->errors()->add("psngrType", "passenger ID is invalid");
                        }

                        if (empty($conf['pId']) && empty($conf['pType']) && empty($conf['title']) && empty($conf['firstName']) && empty($conf['lastName']) && empty($conf['gender']) && empty($conf['age']) && empty($conf['dob']) && empty($conf['nationality'])) {
                            $isvalue = false;
                            break;
                        }

                        if (isset($conf['passportNumber']) && isset($conf['passportIssuingCountry']) && isset($conf['passportExpiry'])) {
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

                if (!$isflight) {
                    $validator->errors()->add('flightSearch', 'flight search field is required');
                }
            });
        }

        if ($validator->fails()) {
            return response()->json(['Status_Code' => 'ERR', 'Message' => $validator->errors()->first()]);
        }


        $req_id = "FL" . time() . rand(1111, 99999);

        $headers = [
            'Content-Type: application/json',
        ];

        try {
            switch ($request->type) {
                case 'airlineImage':
                    if ($request->airlineCode == null || $request->airlineCode == "") {
                        $getImage = DB::table('airlinelogo')->get();
                    } else {
                        $getImage = DB::table('airlinelogo')->where('airline_code', 'like', '%' . $request->airlineCode . '%')->get();
                    }
                    if ($getImage->isEmpty()) {
                        $getImag = [];
                    }
                    foreach ($getImage as $key => $val) {
                        // dd($val);
                        $getImag[$key]['id'] = $val->id;
                        $getImag[$key]['flight_logo'] = $val->flight_logo;
                        $getImag[$key]['flight_name'] = $val->flight_name;
                        $getImag[$key]['airline_code'] = $val->airline_code;
                        $getImag[$key]['flight_logo_url'] = asset('/') . "airlinelogo" . "/" . $val->flight_logo;

                    }
                    return response()->json([
                        'Status_Code' => 'TXN',
                        'Status' => 'success',
                        'Message' => "Image fetched Successfully",
                        'Data' => $getImag
                    ]);
                    break;
                case "getCity":
                    if ($request->city != null || $request->city != "") {
                        $Data['cities'] = DB::table('flightcity')->select('airport_code as Airport_Code', 'airport_name as AirPort_Name', 'city as City', 'country_name as Country_Name', 'country_code as Country_Code')->Where('city', 'like', '%' . $request->city . '%')->orWhere('airport_code', 'like', '%' . $request->city . '%')->get();
                    } else {
                        $Data['cities'] = DB::table('flightcity')->select('airport_code as Airport_Code', 'airport_name as AirPort_Name', 'city as City', 'country_name as Country_Name', 'country_code as Country_Code')->Where('country_code', 'IN')->limit(25)->get();
                    }
                    return response()->json([
                        'Status_Code' => 'TXN',
                        'Status' => 'success',
                        'Message' => "City fetched Successfully",
                        'Data' => $Data['cities']
                    ]);
                    break;
                case "fetch":
                    $url = $this->flight_cred['url'] . "/airlinehost/AirAPIService.svc/JSONService/Air_Search";

                    $uniq = $req_id;

                    foreach ($request->travelInfo as $key => $val) {
                        $trip[$key]["Origin"] = $val['origin'];
                        $trip[$key]["Destination"] = $val['destination'];
                        $trip[$key]["TravelDate"] = date("m/d/Y", strtotime(@$val['travelDate']));
                        $trip[$key]["Trip_Id"] = $val['tripId'];
                    }


                    $param = [
                        "Auth_Header" => [
                            "UserId" => $this->flight_cred['username'],
                            "Password" => $this->flight_cred['password'],
                            "IP_Address" => $this->flight_cred['ip'],
                            "Request_Id" => $uniq,
                            "IMEI_Number" => $this->flight_cred['imei'] ?? "23456666666"
                        ],
                        "Travel_Type" => $request->travelType ?? 0,
                        "Booking_Type" => $request->bookingType ?? 0,
                        "TripInfo" => $trip,
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

                    foreach ($request->flightSearch as $key => $val) {
                        $flightTrip[$key]["Fare_Id"] = $val['fareId'];
                        $flightTrip[$key]["Flight_Key"] = $val['flightKey'];
                    }

                    $param = [
                        "Auth_Header" => [
                            "UserId" => $this->flight_cred['username'],
                            "Password" => $this->flight_cred['password'],
                            "IP_Address" => $this->flight_cred['ip'],
                            "Request_Id" => $request->requestId,
                            //$req_id,
                            "IMEI_Number" => $this->flight_cred['imei'] ?? "23456666666"
                        ],
                        "Search_Key" => $request->searchKey,
                        // ?? $this->flightkeys()['searchKey'],

                        "AirRepriceRequests" => $flightTrip,
                        // [
                        //     [
                        //         "Fare_Id" => $request->fareId,
                        //         // ?? "5316804529804600303",
                        //         "Flight_Key" => $request->flightKey,
                        //         // ?? $this->flightkeys()['flightKey']
                        //     ]
                        // ],
                        "Customer_Mobile" => $getUserDetails->mobile,
                        //retailer mobile number
                        "GST_Input" => false,
                        "SinglePricing" => true
                    ];
                    break;

                case 'getSSR':
                    $url = $this->flight_cred['url'] . "/airlinehost/AirAPIService.svc/JSONService/Air_GetSSR";
                    // $param = [
                    //     "Auth_Header" => [
                    //         "UserId" => $this->flight_cred['username'],
                    //         "Password" => $this->flight_cred['password'],
                    //         "IP_Address" => $this->flight_cred['ip'],
                    //         "Request_Id" => $request->requestId,
                    //         //$req_id,
                    //         "IMEI_Number" => $this->flight_cred['imei'] ?? "23456666666" "223232223232323"
                    //     ],
                    //     "Search_Key" => $request->searchKey , //?? $this->flightkeys()['searchKey'],
                    //     "AirSSRRequestDetails" =>
                    //     [
                    //         "Flight_Key" => $request->flightKey, // ?? $this->flightkeys()['flightKey']
                    //     ]
                    // ];
                    break;

                case 'seatMap':
                    $url = $this->flight_cred['url'] . "/airlinehost/AirAPIService.svc/JSONService/Air_GetSeatMap";

                    // $request23 = $this->flightkeys()['passengerDetails'];

                    // foreach ($request23 as $key => $val) {
                    //     $val = json_decode($val);
                    //     $passDetail[$key]["Pax_Id"] = $val->psngr_id;
                    //     $passDetail[$key]["Pax_type"] = $val->psngr_type;
                    //     $passDetail[$key]["Title"] = $val->psngr_title;
                    //     $passDetail[$key]["First_Name"] = $val->first_name;
                    //     $passDetail[$key]["Last_Name"] = $val->last_name;
                    //     $passDetail[$key]["Gender"] = $val->psngr_gender;
                    //     $passDetail[$key]["Age"] = $val->psngr_age;
                    //     $passDetail[$key]["DOB"] = $val->psngr_dob;
                    //     $passDetail[$key]["Passport_Number"] = $val->passport_number;
                    //     $passDetail[$key]["Passport_Issuing_Country"] = $val->passport_issuing_country;
                    //     $passDetail[$key]["Passport_Expiry"] = $val->passport_expiry;
                    //     $passDetail[$key]["Nationality"] = $val->nationality;
                    //     $passDetail[$key]["FrequentFlyerDetails"] = $val->frequent_flyer_details;
                    // }

                    // $param = [
                    //     "Auth_Header" => [
                    //         "UserId" => $this->flight_cred['username'],
                    //         "Password" => $this->flight_cred['password'],
                    //         "IP_Address" => $this->flight_cred['ip'],
                    //         "Request_Id" => $request->requestId,
                    //         "IMEI_Number" => $this->flight_cred['imei'] ?? "23456666666" "223232223232323"
                    //     ],
                    //     "Search_Key" => $request->searchKey, //?? $this->flightkeys()['searchKey'],
                    //     "AirSSRRequestDetails" =>
                    //     [
                    //         "Flight_Key" => $request->flightKey, // ?? $this->flightkeys()['flightKey']
                    //     ],
                    //     "PAX_Details" => $passDetail

                    // ];

                    break;

                case 'tempBook':
                    $getUser = User::where('id', $request->user_id)->first();

                    $user_wallet_amount = $getUser->mainwallet;

                    $requested_amount = $request->total_amount;

                    if ($user_wallet_amount < $requested_amount) {
                        return response()->json([
                            'Status_Code' => 'IWB',
                            "Status" => "Insufficient wallet balance",
                            'Message' => "Insufficient wallet balance",
                            "Data" => []
                        ]);
                    } else {
                        $url = $this->flight_cred['url'] . "/airlinehost/AirAPIService.svc/JSONService/Air_TempBooking";
                        foreach ($request->passengerDetails as $key => $val) {
                            $flightTrip[$key]["Pax_Id"] = $val['pId'];
                            $flightTrip[$key]["Pax_type"] = $val['pType'];
                            $flightTrip[$key]["Title"] = $val['title'];
                            $flightTrip[$key]["First_Name"] = $val['firstName'];
                            $flightTrip[$key]["Last_Name"] = $val['lastName'];
                            $flightTrip[$key]["Gender"] = $val['gender'];
                            $flightTrip[$key]["Age"] = $val['age'];
                            $flightTrip[$key]["DOB"] = $val['dob'];
                            $flightTrip[$key]["Passport_Number"] = $val['passportNumber'];
                            $flightTrip[$key]["Passport_Issuing_Country"] = $val['passportIssuingCountry'];
                            $flightTrip[$key]["Passport_Expiry"] = $val['passportExpiry'];
                            $flightTrip[$key]["Nationality"] = $val['nationality'];
                            $flightTrip[$key]["FrequentFlyerDetails"] = [];
                        }

                        $param = [
                            "Auth_Header" => [
                                "UserId" => $this->flight_cred['username'],
                                "Password" => $this->flight_cred['password'],
                                "IP_Address" => $this->flight_cred['ip'],
                                "Request_Id" => $request->requestId,
                                "IMEI_Number" => $this->flight_cred['imei'] ?? "23456666666"
                            ],
                            "Customer_Mobile" => $getUserDetails->mobile,
                            "Passenger_Mobile" => $request->passengerMobile ?? null,
                            "WhatsAPP_Mobile" => $request->whatsappMobile ?? $request->passengerMobile,
                            "Passenger_Email" => $request->passengerEmail ?? null,
                            "PAX_Details" => $flightTrip,
                            "GST" => false,
                            "GST_Number" => $request->gstNumber ?? "",
                            "GST_HolderName" => $request->gstHolderName ?? "",
                            "GST_Address" => $request->gstAddress ?? "",
                            // "BookingFlightDetails" =>
                            // [
                            //     [
                            //         "Search_Key" => $request->searchKey,
                            //         //?? $this->flightkeys()['searchKey'],
                            //         "Flight_Key" => $request->flightKey,
                            //         //?? $this->flightkeys()['flightKey'],
                            //         "BookingSSRDetails" => []

                            //     ]
                            // ],
                            "CostCenterId" => $request->costCentreId ?? 0,
                            "ProjectId" => $request->projectId ?? 0,
                            "BookingRemark" => $request->bookingRemark ?? ".",
                            "CorporateStatus" => $request->corporateStatus ?? 0,
                            "CorporatePaymentMode" => $request->corporatePaymentMode ?? 0,
                            "MissedSavingReason" => $request->missedSavingReason ?? null,
                            "CorpTripType" => $request->corpTripType ?? null,
                            "CorpTripSubType" => $request->corpTripSubType ?? null,
                            "TripRequestId" => $request->tripRequestId ?? null,
                            "BookingAlertIds" => $request->bookingAlertIds ?? null,


                        ];

                        if ($request->bookingType == 0) {
                            $param['BookingFlightDetails'] =
                                [
                                    [
                                        "Search_Key" => $request->searchKey,
                                        "Flight_Key" => $request->flightKey,
                                        "BookingSSRDetails" => []
                                    ],
                                ];
                        }
                        if ($request->bookingType == 1) {
                            $param['BookingFlightDetails'] =
                                [
                                    [
                                        "Search_Key" => $request->searchKey,
                                        "Flight_Key" => $request->flightKey,
                                        "BookingSSRDetails" => []
                                    ],
                                    [
                                        "Search_Key" => $request->searchKey,
                                        "Flight_Key" => $request->returnflightKey,
                                        "BookingSSRDetails" => []
                                    ],
                                ];
                        }
                    }

                    break;

                case 'ticket':
                    $url = $this->flight_cred['url'] . "/airlinehost/AirAPIService.svc/JSONService/Air_Ticketing";
                    $param = [
                        "Auth_Header" => [
                            "UserId" => $this->flight_cred['username'],
                            "Password" => $this->flight_cred['password'],
                            "IP_Address" => $this->flight_cred['ip'],
                            "Request_Id" => $request->requestId,
                            //$req_id,
                            "IMEI_Number" => $this->flight_cred['imei'] ?? "23456666666"
                        ],
                        "Booking_RefNo" => $request->bookingRefNo ?? "",
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
                            "Request_Id" => $request->requestId,
                            //$req_id,
                            "IMEI_Number" => $this->flight_cred['imei'] ?? "23456666666"
                        ],
                        "Search_Key" => $request->searchKey,
                        "Fare_Id" => $request->fareId,
                        "Flight_Key" => $request->flightKey
                    ];

                    break;

                case 'reprint':
                    $url = $this->flight_cred['url'] . "/airlinehost/AirAPIService.svc/JSONService/Air_Reprint";
                    $param = [
                        "Auth_Header" => [
                            "UserId" => $this->flight_cred['username'],
                            "Password" => $this->flight_cred['password'],
                            "IP_Address" => $this->flight_cred['ip'],
                            "Request_Id" => $request->requestId ?? $req_id,
                            //$req_id,
                            "IMEI_Number" => $this->flight_cred['imei'] ?? "23456666666"
                        ],

                        "Booking_RefNo" => $request->bookingRefNo ?? "",
                        "Airline_PNR" => $request->airlinePNR ?? ""
                    ];
                    break;

                case 'cancellation':
                    $url = $this->flight_cred['url'] . "/AirlineHost/AirAPIService.svc/JSONService/Air_TicketCancellation";
                    $getFlight = DB::table('flight_txn_details')->select('*')->where('booking_refno', $request->Booking_RefNo)->first();

                    $counPass = count(json_decode($getFlight->passenger_details));

                    // dd([$getFlight->flight_id,$counPass]);

                    $param = [
                        "Auth_Header" => [
                            "UserId" => $this->flight_cred['username'],
                            "Password" => $this->flight_cred['password'],
                            "IP_Address" => $this->flight_cred['ip'],
                            "Request_Id" => $request->requestId ?? $req_id,
                            //$req_id,
                            "IMEI_Number" => $this->flight_cred['imei'] ?? "23456666666"
                        ],
                        "AirTicketCancelDetails" => [
                            [
                                "FlightId" => $getFlight->flight_id,
                                "PassengerId" => $counPass ?? "1",
                                "SegmentId" => $request->segmentId ?? "0"
                            ]
                        ],
                        "Airline_PNR" => $request->airlinePNR ?? "",
                        "RefNo" => $request->bookingRefNo ?? "",
                        "CancelCode" => "005",
                        "ReqRemarks" => $request->cancelRemarks ?? "I cancell the ticket",
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
                            "IMEI_Number" => $this->flight_cred['imei'] ?? "23456666666"
                        ],
                        "Destination" => $request->destination ?? "",
                        "Month" => $request->month ?? "",
                        "Origin" => $request->origin ?? "",
                        "Year" => $request->year ?? ""
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
                            "IMEI_Number" => $this->flight_cred['imei'] ?? "23456666666"
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
                            "Request_Id" => $request->requestId,
                            //$req_id,
                            "IMEI_Number" => $this->flight_cred['imei'] ?? "23456666666"
                        ],
                        "ClientRefNo" => "REF" . $req_id . rand('111111', '999999'),
                        //$request->clientRefNo ?? "FBB64ZDT",
                        "RefNo" => $request->bookingRefNo ?? "",
                        "TransactionType" => $request->trType ?? 0,
                        "ProductId" => "1"

                    ];

                    $getUser = User::where('id', $request->user_id)->first();

                    $user_wallet_amount = $getUser->mainwallet;

                    $requested_amount = $request->amount;

                    if ($user_wallet_amount < $requested_amount) {
                        return response()->json([
                            'Status_Code' => 'IWB',
                            "Status" => "Insufficient wallet balance",
                            'Message' => "Insufficient wallet balance",
                            "Data" => []
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
                            "IMEI_Number" => $this->flight_cred['imei'] ?? "23456666666"
                        ],

                        "Fromdate" => $request->fromDate ?? "01/01/2022",
                        "Month" => $request->month ?? "10",
                        "Todate" => $request->toDate ?? "03/10/2023",
                        "Type" => "0",
                        "Year" => $request->searchYear ?? "2023"
                    ];
                    break;
                default:
                    return response()->json(['Status_Code' => "ERR", 'Message' => 'Invalid Request']);
            }


            $parRequest = str_replace(["\\\/\\\/", "\\\/", "\/", "'\'"], "/", json_encode($param));
            $result = \Myhelper::curl($url, "POST", $parRequest, $headers, "yes", "flight" . $request->type, $request->requestId);

            $response = json_decode($result['response']);

            switch ($request->type) {

                case "fetch":
                    if ($result['code'] == 200 || $result['error'] == null) {
                        if ($response->Response_Header->Error_Code == 0000 && $response->Response_Header->Status_Id == "11") {

                            return response()->json([
                                'Status_Code' => 'TXN',
                                "Status" => $response->Response_Header->Error_InnerException,
                                'Message' => $response->Response_Header->Error_Desc,
                                'Data' => [
                                    "Status_Id" => $response->Response_Header->Status_Id,
                                    "Request_Id" => $response->Response_Header->Request_Id,
                                    "Search_Key" => $response->Search_Key,
                                    "Flight" => $response->TripDetails,
                                    // "Trip_Id" => $response->TripDetails,
                                    "Booking_Type" => @$request->bookingType
                                ]
                            ]);
                        } else {
                            return response()->json([
                                'Status_Code' => 'TXF',
                                "Status" => @$response->Response_Header->Error_InnerException,
                                'Message' => @$response->Response_Header->Error_Desc,
                                'Data' => [
                                    "Status_Id" => @$response->Response_Header->Status_Id,
                                    "Request_Id" => @$response->Response_Header->Request_Id,
                                    "Search_Key" => @$response->Search_Key,
                                    "Flight" => @$response->TripDetails[0]->Flights,
                                    // "Trip_Id" => @$response->TripDetails[0]->Trip_Id,
                                ]
                            ]);
                        }
                    } else {
                        return response()->json([
                            'Status_Code' => 'TXF',
                            "Status" => $result['error'],
                            'Message' => $result['error'],
                            'Data' => $result['error']
                        ]);
                    }

                    break;

                case 'reprice':
                    // dd($url, $response, $result, str_replace(["\\\/\\\/", "\\\/", "'\'"], "/", json_encode($param)));
                    if ($result['code'] == 200 || $result['error'] == null) {
                        if ($response->Response_Header->Error_Code == 0000 && $response->Response_Header->Status_Id == "11") {
                            return response()->json([
                                'Status_Code' => 'TXN',
                                "Status" => $response->Response_Header->Error_InnerException,
                                'Message' => $response->Response_Header->Error_Desc,
                                'Data' => [
                                    "Status_Id" => $response->Response_Header->Status_Id,
                                    "Request_Id" => $response->Response_Header->Request_Id,
                                    "Flight" => $response->AirRepriceResponses,
                                ]
                            ]);
                        } else {
                            return response()->json([
                                'Status_Code' => 'TXF',
                                "Status" => @$response->Response_Header->Error_InnerException,
                                'Message' => @$response->Response_Header->Error_Desc,
                                'Data' => [
                                    "Status_Id" => @$response->Response_Header->Status_Id,
                                    "Request_Id" => @$response->Response_Header->Request_Id,
                                    "Flight" => @$response->AirRepriceResponses,
                                ]
                            ]);
                        }
                    } else {
                        return response()->json([
                            'Status_Code' => 'TXF',
                            "Status" => $result['error'],
                            'Message' => $result['error'],
                            'Data' => $result['error']
                        ]);
                    }


                    break;

                case 'getSSR':
                    // dd($url, $response, $result, str_replace(["\\\/\\\/", "\\\/", "'\'"], "/", json_encode($param)));
                    // if ($result['code'] == 200 || $result['error'] == null) {
                    //     if ($response->Response_Header->Error_Code == 0000 && $response->Response_Header->Status_Id == "11") {
                    //         return response()->json([
                    //             'Status_Code' => 'TXN',
                    //             "Status" => $response->Response_Header->Error_InnerException,
                    //             'Message' => $response->Response_Header->Error_Desc,
                    //             'Data' => [
                    //                 "Status_Id" => $response->Response_Header->Status_Id,
                    //                 "Request_Id" => $response->Response_Header->Request_Id,
                    //                 "Flight" => $response->AirRepriceResponses[0]->Flight,
                    //             ]
                    //         ]);
                    //     } else {
                    //         return response()->json([
                    //             'Status_Code' => 'TXF',
                    //             "Status" => @$response->Response_Header->Error_InnerException,
                    //             'Message' => @$response->Response_Header->Error_Desc,
                    //             'Data' => [
                    //                 "Status_Id" => @$response->Response_Header->Status_Id,
                    //                 "Request_Id" => @$response->Response_Header->Request_Id,
                    //                 "Flight" => @$response->AirRepriceResponses[0]->Flight,

                    //             ]
                    //         ]);
                    //     }
                    // } else {
                    return response()->json([
                        'Status_Code' => 'TXF',
                        "Status" => $result['error'],
                        'Message' => $result['error'],
                        'Data' => $result['error']
                    ]);
                    // }

                    break;

                case 'seatMap':
                    // dd($url, $response, $result, str_replace(["\\\/\\\/", "\\\/", "'\'"], "/", json_encode($param)));
                    // if ($result['code'] == 200 || $result['error'] == null) {
                    //     if ($response->Response_Header->Error_Code == 0000 && $response->Response_Header->Status_Id == "11") {
                    //         return response()->json([
                    //             'Status_Code' => 'TXN',
                    //             "Status" => $response->Response_Header->Error_InnerException,
                    //             'Message' => $response->Response_Header->Error_Desc,
                    //             'Data' => [
                    //                 "Status_Id" => $response->Response_Header->Status_Id,
                    //                 "Request_Id" => $response->Response_Header->Request_Id,
                    //                 "Flight" => $response->AirSeatMaps,
                    //             ]
                    //         ]);
                    //     } else {
                    //         return response()->json([
                    //             'Status_Code' => 'TXF',
                    //             "Status" => @$response->Response_Header->Error_InnerException,
                    //             'Message' => @$response->Response_Header->Error_Desc,
                    //             'Data' => [
                    //                 "Status_Id" => @$response->Response_Header->Status_Id,
                    //                 "Request_Id" => @$response->Response_Header->Request_Id,
                    //                 "Flight" => @$response->AirSeatMaps,

                    //             ]
                    //         ]);
                    //     }
                    // } else {
                    return response()->json([
                        'Status_Code' => 'TXF',
                        "Status" => $result['error'],
                        'Message' => $result['error'],
                        'Data' => $result['error']
                    ]);
                    // }

                    break;

                case 'tempBook':
                    // dd($url, $response, $result, str_replace(["\\\/\\\/", "\\\/", "\/", "'\'"], "/", json_encode($param)));
                    if ($result['code'] == 200 && $result['error'] == null) {
                        if ($response->Response_Header->Error_Code == 0000 && $response->Response_Header->Status_Id == "11") {
                            $getUser = User::where('id', $request->user_id)->first();
                            $insertDB = [
                                "user_id" => $request->user_id,
                                "passenger_details" => json_encode($flightTrip),
                                "passenger_mobile" => @$request->whatsappMobile,
                                "passenger_email" => @$request->passengerEmail,
                                "flight_id" => @$request->flightId,
                                "booking_refno" => @$response->Booking_RefNo,
                                "fare_Id" => @$request->fareId,
                                "ticket_type" => @$request->ticket_type,
                                "booking_remarks" => @$request->bookingRemark ?? "",
                                "customer_mobile" => @$getUser->mobile,
                                "travelInfo" => json_encode(@$request->travelInfo),
                                "booking_type" => @$request->bookingType,
                                "travel_class" => @$request->travelClass,
                                "travel_type" => @$request->travelType,
                                "adult_passenger" => @$request->adultCount,
                                "child_passenger" => @$request->childCount,
                                "infant_passenger" => @$request->infantCount,
                                'request_id' => @$response->Response_Header->Request_Id ?? $request->requestId
                            ];

                            $insertDBData = DB::table('flight_txn_details')->insert($insertDB);

                            // dd($insertDB);

                            if ($insertDBData) {
                                return response()->json([
                                    'Status_Code' => 'TXN',
                                    "Status" => $response->Response_Header->Error_InnerException,
                                    'Message' => $response->Response_Header->Error_Desc,
                                    'Data' => [
                                        "Status_Id" => @$response->Response_Header->Status_Id,
                                        "Request_Id" => @$response->Response_Header->Request_Id,
                                        "Booking_Ref_No" => @$response->Booking_RefNo,
                                        // "Amount" => @$request->total_amount ?? "",
                                    ]
                                ]);
                            }
                        } else {
                            return response()->json([
                                'Status_Code' => 'TXF',
                                "Status" => @$response->Response_Header->Error_InnerException,
                                'Message' => @$response->Response_Header->Error_Desc,
                                'Data' => [
                                    "Status_Id" => @$response->Response_Header->Status_Id,
                                    "Request_Id" => @$response->Response_Header->Request_Id,
                                    "Booking_Ref_No" => @$response->Booking_RefNo,
                                    // "Amount" => @$request->amount ?? ""
                                ]
                            ]);
                        }
                    } else {
                        return response()->json([
                            'Status_Code' => 'ERR',
                            "Status" => @$result['error'] ?? "Try After Sometime",
                            'Message' => @$result['error'] ?? "Try After Sometime",
                            // 'Data' => $result['error']
                        ]);
                    }

                    break;

                case 'fareRule':
                    // dd($url, $response, $result, str_replace(["\\\/\\\/", "\\\/", "\/", "'\'"], "/", json_encode($param)));
                    if ($result['code'] == 200 || $result['error'] == null) {
                        if ($response->Response_Header->Error_Code == 0000 && $response->Response_Header->Status_Id == "11") {
                            return response()->json([
                                'Status_Code' => 'TXN',
                                "Status" => $response->Response_Header->Error_InnerException,
                                'Message' => $response->Response_Header->Error_Desc,
                                'Data' => [
                                    "Status_Id" => $response->Response_Header->Status_Id,
                                    "Request_Id" => $response->Response_Header->Request_Id,
                                    "fareRulesdesc" => $response->FareRules,
                                    // "fareRuleName" => $response->FareRules[0]->FareRuleName,
                                    // "segmentId" => $response->FareRules[0]->Segment_Id,
                                ]
                            ]);
                        } else {
                            return response()->json([
                                'Status_Code' => 'TXF',
                                "Status" => @$response->Response_Header->Error_InnerException,
                                'Message' => @$response->Response_Header->Error_Desc,
                                'Data' => [
                                    "Status_Id" => @$response->Response_Header->Status_Id,
                                    "Request_Id" => @$response->Response_Header->Request_Id,
                                    "fareRulesdesc" => @$response->FareRules,
                                    // "fareRuleName" => @$response->FareRules[0]->FareRuleName,
                                    // "segmentId" => @$response->FareRules[0]->Segment_Id,

                                ]
                            ]);
                        }
                    } else {
                        return response()->json([
                            'Status_Code' => 'TXF',
                            "Status" => $result['error'],
                            'Message' => $result['error'],
                            'Data' => $result['error']
                        ]);
                    }

                    break;

                case 'ticket':
                    if ($result['code'] == 200 && $result['error'] == null) {
                        // dd($result);?
                        $update_PNR_DB = DB::table('flight_txn_details')->where('booking_refno', $param['Booking_RefNo'])->update(['airline_PNR' => @$response->AirlinePNRDetails, "ticket_type" => @$param['Ticketing_Type']]);

                        if ($response->Response_Header->Error_Code == 0000 && $response->Response_Header->Status_Id == "11") {
                            $update_Ref_Amount = DB::table('flight_txn_details')->where('booking_refno', $param['RefNo'])->update(['is_ticket_booked' => '1', "updated_at" => date('Y-m-d H:i:s')]);

                            $Data = [
                                'Status_Code' => 'TXN',
                                "Status" => @$response->Response_Header->Error_InnerException,
                                'Message' => @$response->Response_Header->Error_Desc,
                                "Status_Id" => @$response->Response_Header->Status_Id,
                                "Request_Id" => @$response->Response_Header->Request_Id,
                                "Booking_RefNo" => @$param['Booking_RefNo'],
                                "Airline_Details" => @$response->AirlinePNRDetails

                            ];

                        } else {
                            $Data = [
                                'Status_Code' => 'TXF',
                                "Status" => @$response->Response_Header->Error_InnerException,
                                'Message' => @$response->Response_Header->Error_Desc,
                                "Status_Id" => @$response->Response_Header->Status_Id,
                                "Request_Id" => @$response->Response_Header->Request_Id,
                                "Booking_RefNo" => @$param['Booking_RefNo'],
                            ];

                        }
                    } else {
                        $Data = [
                            'Status_Code' => 'ERR',
                            "Status" => @$result['error'],
                            'Message' => @$result['error'],
                            'Data' => @$result['error']
                        ];
                    }
                    return response()->json($Data);
                    break;

                case 'tripHistory':
                    // dd($url, $response, $result, str_replace(["\\\/\\\/", "\\\/", "\/", "'\'"], "/", json_encode($param)));
                    if ($result['code'] == 200 || $result['error'] == null) {
                        if ($response->Response_Header->Error_Code == 0000 && $response->Response_Header->Status_Id == "11") {
                            return response()->json([
                                'Status_Code' => 'TXN',
                                "Status" => @$response->Response_Header->Error_InnerException,
                                'Message' => @$response->Response_Header->Error_Desc,
                                'Data' => [
                                    "Status_Id" => @$response->Response_Header->Status_Id,
                                    "Request_Id" => @$response->Response_Header->Request_Id,
                                    "ticketHistory" => @$response->TicketHistory
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
                                'Status_Code' => 'TXF',
                                "Status" => @$response->Response_Header->Error_InnerException,
                                'Message' => @$response->Response_Header->Error_Desc,
                                'Data' => [
                                    "Status_Id" => @$response->Response_Header->Status_Id,
                                    "Request_Id" => @$response->Response_Header->Request_Id,

                                ]
                            ]);
                        }
                    } else {
                        return response()->json([
                            'Status_Code' => 'TXF',
                            "Status" => @$result['error'],
                            'Message' => @$result['error'],
                            'Data' => @$result['error']
                        ]);
                    }

                    break;

                case 'reprint':
                    // dd($url, $response, $result, str_replace(["\\\/\\\/", "\\\/", "\/", "'\'"], "/", json_encode($param)));
                    if ($result['code'] == 200 && $result['error'] == null) {
                        if ($response->Response_Header->Error_Code == 0000 && $response->Response_Header->Status_Id == "11") {
                            // if (@$response->AirPNRDetails[0]->Ticket_Status_Id == 4) {
                            //     $vstat = 'success';
                            // }

                            if (@$response->AirPNRDetails != null) {
                                foreach (@$response->AirPNRDetails as $key => $value) {
                                    $statusResp[$key] = @$value->Ticket_Status_Id;
                                    $statusRespDesc[$key] = @$value->Ticket_Status_Desc;
                                }

                                $updateStatusformETRV = DB::table('flight_txn_details')->where('booking_refno', $param['Booking_RefNo'])->update(['ticket_status_id' => json_encode($statusResp), 'ticket_status_desc' => json_encode($statusRespDesc)]);

                            }

                            return response()->json([
                                'Status_Code' => 'TXN',
                                "Status" => @$response->Response_Header->Error_InnerException,
                                'Message' => @$response->Response_Header->Error_Desc,
                                'Data' => [
                                    "Status_Id" => @$response->Response_Header->Status_Id,
                                    "Request_Id" => @$response->Response_Header->Request_Id,
                                    "Adult_Count" => @$response->Adult_Count,
                                    "AirPNRDetails" => @$response->AirPNRDetails,
                                    "Biller_Id" => @$response->Biller_Id,
                                    "BookingPaymentDetail" => @$response->BookingPaymentDetail,
                                    "Booking_DateTime" => @$response->Booking_DateTime,
                                    "Booking_RefNo" => @$response->Booking_RefNo,
                                    "Booking_Type" => $this->checkType("booking", @$response->Booking_Type),
                                    "Child_Count" => @$response->Child_Count,
                                    "Class_of_Travel" => $this->checkType("class", @$response->Class_of_Travel),
                                    "CompanyDetail" => @$response->CompanyDetail,
                                    "CorporatePaymentMode" => @$response->CorporatePaymentMode,
                                    "CustomerDetail" => @$response->CustomerDetail,
                                    "Infant_Count" => @$response->Infant_Count,
                                    "Invoice_Number" => @$response->Invoice_Number,
                                    "Passenger_EmailId" => @$response->PAX_EmailId,
                                    "Passenger_Mobile" => @$response->PAX_Mobile,
                                    "RetailerDetail" => @$response->RetailerDetail,
                                    "Travel_Type" => $this->checkType("travel", @$response->Travel_Type),
                                    "Ticket_StatusCode" => @$response->AirPNRDetails[0]->Ticket_Status_Id ?? "",
                                    "Ticket_Status" => @$response->AirPNRDetails[0]->Ticket_Status_Desc
                                ]
                            ]);
                        } else {
                            return response()->json([
                                'Status_Code' => 'TXF',
                                "Status" => @$response->Response_Header->Error_InnerException,
                                'Message' => @$response->Response_Header->Error_Desc,
                                'Data' => [
                                    "Status_Id" => @$response->Response_Header->Status_Id,
                                    "Request_Id" => @$response->Response_Header->Request_Id,
                                    "Adult_Count" => $response->Adult_Count,
                                    "AirPNRDetails" => @$response->AirPNRDetails,
                                    "Biller_Id" => @$response->Biller_Id,
                                    "BookingPaymentDetail" => @$response->BookingPaymentDetail,
                                    "Booking_DateTime" => @$response->Booking_DateTime,
                                    "Booking_RefNo" => @$response->Booking_RefNo,
                                    "Booking_Type" => $this->checkType("booking", @$response->Booking_Type),
                                    "Child_Count" => @$response->Child_Count,
                                    "Class_of_Travel" => $this->checkType("class", @$response->Class_of_Travel),
                                    "CompanyDetail" => @$response->CompanyDetail,
                                    "CorporatePaymentMode" => @$response->CorporatePaymentMode,
                                    "CustomerDetail" => @$response->CustomerDetail,
                                    "Infant_Count" => @$response->Infant_Count,
                                    "Invoice_Number" => @$response->Invoice_Number,
                                    "Passenger_EmailId" => @$response->PAX_EmailId,
                                    "Passenger_Mobile" => @$response->PAX_Mobile,
                                    "RetailerDetail" => @$response->RetailerDetail,
                                    "Travel_Type" => $this->checkType("travel", @$response->Travel_Type),

                                ]
                            ]);
                        }
                    } else {
                        return response()->json([
                            'Status_Code' => 'TXF',
                            "Status" => @$result['error'],
                            'Message' => @$result['error'],
                            'Data' => @$result['error']
                        ]);
                    }

                    break;

                case 'cancellation':
                    // dd($url, $response, $result, str_replace(["\\\/\\\/", "\\\/", "\/", "'\'"], "/", json_encode($param)));
                    if ($result['code'] == 200 || $result['error'] == null) {
                        // $update_cancel_DB = DB::table('flight_txn_details')->where('booking_refno', $param['RefNo'])->update(['cancel_remarks' => $param['ReqRemarks'], "cancel_type" => $param['cancel_type'], "cancellation_details" => $param['AirTicketCancelDetails'], "cancellation_date" => date('Y-m-d H:i:s')]);

                        if ($response->Response_Header->Error_Code == 0000 && $response->Response_Header->Status_Id == "11") {

                            $update_cancel_DB = DB::table('flight_txn_details')->where('booking_refno', $param['RefNo'])->update(['cancel_remarks' => $param['ReqRemarks'], "cancel_type" => $param['cancel_type'], "cancellation_details" => $param['AirTicketCancelDetails'], "cancellation_date" => date('Y-m-d H:i:s')]);

                            if ($update_cancel_DB) {

                                return response()->json([
                                    'Status_Code' => 'TXN',
                                    "Status" => @$response->Response_Header->Error_InnerException,
                                    'Message' => @$response->Response_Header->Error_Desc,
                                    'Data' => [
                                        "Status_Id" => @$response->Response_Header->Status_Id,
                                        "Request_Id" => @$response->Response_Header->Request_Id,
                                        "c_resp" => @$response

                                    ]
                                ]);
                            }
                        } else {
                            return response()->json([
                                'Status_Code' => 'TXF',
                                "Status" => @$response->Response_Header->Error_InnerException,
                                'Message' => @$response->Response_Header->Error_InnerException,
                                'Data' => [
                                    "Status_Id" => @$response->Response_Header->Status_Id,
                                    "Request_Id" => @$response->Response_Header->Request_Id,

                                ]
                            ]);
                        }
                    } else {
                        return response()->json([
                            'Status_Code' => 'TXF',
                            "Status" => @$result['error'],
                            'Message' => @$result['error'],
                            'Data' => @$result['error']
                        ]);
                    }

                    break;

                case 'lowFare':
                    // dd($url, $response, $result, str_replace(["\\\/\\\/", "\\\/", "\/", "'\'"], "/", json_encode($param)));
                    if ($result['code'] == 200 || $result['error'] == null) {
                        if ($response->Response_Header->Error_Code == 0000 && $response->Response_Header->Status_Id == "11") {
                            foreach ($response->LowFares as $key => $val) {
                                $lowfare[$key]["airlineCode"] = $val->AirlineCode;
                                $lowfare[$key]["amount"] = $val->Amount;
                                $lowfare[$key]["travelDate"] = $val->TravelDate;
                                $lowfare[$key]["travelDay"] = date("d", strtotime(@$val->TravelDate));
                                $lowfare[$key]["travelDateStamp"] = date("Ymd", strtotime(@$val->TravelDate));
                            }

                            return response()->json([
                                'Status_Code' => 'TXN',
                                "Status" => @$response->Response_Header->Error_InnerException,
                                'Message' => @$response->Response_Header->Error_Desc,
                                'Data' => [
                                    "Status_Id" => @$response->Response_Header->Status_Id,
                                    "Request_Id" => @$response->Response_Header->Request_Id,
                                    "c_resp" => $lowfare
                                ]
                            ]);
                        } else {
                            return response()->json([
                                'Status_Code' => 'TXF',
                                "Status" => @$response->Response_Header->Error_InnerException,
                                'Message' => @$response->Response_Header->Error_Desc,
                                'Data' => [
                                    "Status_Id" => @$response->Response_Header->Status_Id,
                                    "Request_Id" => @$response->Response_Header->Request_Id,
                                    "c_resp" => []
                                ]
                            ]);
                        }
                    } else {
                        return response()->json([
                            'Status_Code' => 'TXF',
                            "Status" => @$result['error'],
                            'Message' => @$result['error'],
                            'Data' => @$result['error']
                        ]);
                    }

                    break;

                case 'getBalance':
                    // dd($url, $response, $result, str_replace(["\\\/\\\/", "\\\/", "\/", "'\'"], "/", json_encode($param)));
                    if ($result['code'] == 200 || $result['error'] == null) {
                        if ($response->Response_Header->Error_Code == 0000 && $response->Response_Header->Status_Id == "11") {

                            return response()->json([
                                'Status_Code' => 'TXN',
                                "Status" => @$response->Response_Header->Error_InnerException,
                                'Message' => @$response->Response_Header->Error_Desc,
                                'Data' => [
                                    "Status_Id" => @$response->Response_Header->Status_Id,
                                    "Request_Id" => @$response->Response_Header->Request_Id,
                                    "CreditBalance" => @$response->CreditBalance,
                                    "EffectiveBalance" => @$response->EffectiveBalance,
                                    "LienBalance" => @$response->LienBalance,
                                    "ODAmount" => @$response->ODAmount


                                ]
                            ]);
                        } else {
                            return response()->json([
                                'Status_Code' => 'TXF',
                                "Status" => @$response->Response_Header->Error_InnerException,
                                'Message' => @$response->Response_Header->Error_Desc,
                                'Data' => [
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
                            'Status_Code' => 'TXF',
                            "Status" => @$result['error'],
                            'Message' => @$result['error'],
                            'Data' => @$result['error']
                        ]);
                    }

                    break;

                case 'addPayment':
                    // dd($url, $response, $result, str_replace(["\\\/\\\/", "\\\/", "\/", "'\'"], "/", json_encode($param)));
                    if ($result['code'] == 200 && $result['error'] == null) {
                        $update_Ref_Amount = DB::table('flight_txn_details')
                            ->where('booking_refno', $param['RefNo'])
                            ->update(['client_ref_no' => $param['ClientRefNo'], "amount" => $request->amount, "payment_id" => @$response->PaymentID, "updated_at" => date('Y-m-d H:i:s')]);


                        if ($response->Response_Header->Error_Code == 0000 && $response->Response_Header->Status_Id == "11") {
                            $balanceupdate = $this->calculationAmount($request->user_id, $request->amount);
                            // if (!$balanceupdate) {
                            //     return response()->json(['Status' => 'TXF', 'Message' => "Something went wrong,Please try after sometime"]);
                            // }

                            $update_Ref_Amount = DB::table('flight_txn_details')->where('booking_refno', $param['RefNo'])->update(['is_payment' => '1', "updated_at" => date('Y-m-d H:i:s')]);
                            return $this->confirmTicket($request, $param['RefNo']);
                        } else
                            return response()->json([
                                'Status_Code' => 'TXF',
                                "Status" => @$response->Response_Header->Error_InnerException,
                                'Message' => @$response->Response_Header->Error_Desc,
                            ]);


                    } else {
                        return response()->json(['Status_Code' => 'ERR', "Status" => @$result['error'], 'Message' => @$result['error']]);

                    }


                    break;

                default:
                    return response()->json(['Status_Code' => "ERR", "Message" => "Invalid Response"]);
            }
        } catch (Exception $e) {
            return response()->json([
                'Status_Code' => 'ERR',
                'Status' => 'error',
                'Message' => "Something went wrong, please try after some time",
                'Data' => $e->getMessage()

            ]);
        }
    }

    public function confirmTicket($request, $Booking_RefNo)
    {

        $headers = [
            'Content-Type: application/json',
        ];

        $url = $this->flight_cred['url'] . "/airlinehost/AirAPIService.svc/JSONService/Air_Ticketing";
        $param = [
            "Auth_Header" => [
                "UserId" => $this->flight_cred['username'],
                "Password" => $this->flight_cred['password'],
                "IP_Address" => $this->flight_cred['ip'],
                "Request_Id" => $request->requestId,
                //$req_id,
                "IMEI_Number" => $this->flight_cred['imei'] ?? "23456666666"
            ],
            "Booking_RefNo" => $Booking_RefNo ?? "",
            "Ticketing_Type" => $request->ticketType ?? "1"
        ];




        $parRequest = str_replace(["\\\/\\\/", "\\\/", "\/", "'\'"], "/", json_encode($param));
        $result = \Myhelper::curl($url, "POST", $parRequest, $headers, "yes", "flight" . $request->type, $request->requestId);

        $response = json_decode($result['response']);

        // dd($url, $response, $result, str_replace(["\\\/\\\/", "\\\/", "\/", "'\'"], "/", json_encode($param)));
        if ($result['code'] == 200 && $result['error'] == null) {
            $update_PNR_DB = DB::table('flight_txn_details')->where('booking_refno', $param['Booking_RefNo'])->update(['airline_PNR' => @$response->AirlinePNRDetails, "ticket_type" => $param['Ticketing_Type'], "updated_at" => date('Y-m-d H:i:s')]);
            if ($response->Response_Header->Error_Code == 0000 && $response->Response_Header->Status_Id == "11") {
                $update_Ref_Amount = DB::table('flight_txn_details')->where('booking_refno', $param['Booking_RefNo'])->update(['is_ticket_booked' => '1', "updated_at" => date('Y-m-d H:i:s')]);

                $Data = [
                    'Status_Code' => 'TXN',
                    "Status" => @$response->Response_Header->Error_InnerException,
                    'Message' => @$response->Response_Header->Error_Desc,
                    "Status_Id" => @$response->Response_Header->Status_Id,
                    "Request_Id" => @$response->Response_Header->Request_Id,
                    "Booking_RefNo" => $param['Booking_RefNo'],
                    "Airline_Details" => @$response->AirlinePNRDetails

                ];

            } else {
                $Data = [
                    'Status_Code' => 'TXF',
                    "Status" => @$response->Response_Header->Error_InnerException,
                    'Message' => @$response->Response_Header->Error_Desc,
                    "Status_Id" => @$response->Response_Header->Status_Id,
                    "Request_Id" => @$response->Response_Header->Request_Id,
                    "Booking_RefNo" => @$param['Booking_RefNo'],
                ];

            }
        } else {
            $Data = [
                'Status_Code' => 'ERR',
                "Status" => @$result['error'],
                'Message' => @$result['error'],
                'Data' => @$result['error']
            ];
        }
        return response()->json($Data);

    }

    public function checkType($type, $value)
    {
        switch ($type) {
            case "booking":
                switch ($value) {
                    case 0:
                        $val = "ONE_WAY";
                        break;
                    case 1:
                        $val = "ROUNDTRIP";
                        break;
                    case 2:
                        $val = "SPECIALROUNDTRIP";
                        break;
                    default:
                        $val = "";
                        break;
                }
                break;
            case "class":
                switch ($value) {
                    case 0:
                        $val = "ECONOMY";
                        break;
                    case 1:
                        $val = "BUSINESS";
                        break;
                    case 2:
                        $val = "FIRST";
                        break;
                    case 3:
                        $val = "PREMIUM_ECONOMY";
                        break;
                    default:
                        $val = "";
                        break;

                }
                break;

            case "travel":
                switch ($value) {
                    case 0:
                        $val = "DOMESTIC";
                        break;
                    case 1:
                        $val = "INTERNATIONAL";
                        break;
                    default:
                        $val = "";
                        break;


                }
                break;


        }
        return $val;
    }





    public function calculationAmount($user_id, $debitamount)
    {

        $getUser = User::where('id', $user_id)->first();

        $user_wallet_amount = $getUser->mainwallet;

        if ($user_wallet_amount < $debitamount) {
            return response()->json([
                'Status_Code' => 'IWB',
                "Status" => "Insufficient wallet balance",
                'Message' => "Insufficient wallet balance",
                'Data' => []
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


    /////////////////stating of testing static data///////////////-------------

    public function flightkeys()
    {
        $flight["flightKey"] = "Z/Wy7sUWNIxMKEAXgPFIMBpa/5NWOLRci1eogWmWmb4z921dBrDPx2DlIS0EjvB5fyFSPHp0isKtGtgwcBK2VCP/ghtvswgwvxXKXEEQnKvDpyFtdAPzPVKRYVhVz7VMVomi9Gl3/p7XcFkDEPlL+isewielwODT611lOTAXnTkoyVmrqdL/Frw5zUIsQ1TxgEsKMYC7p0drQJT3dXAh7itsqU51tk7ondteNEeC09JMrriLmmWuICIlaRdLecwUcNgMW1iLJ2d4M9X0yzK+O7/qhWOd0wCANtUIh1Pva6aFA/lxpTBKtxcn73UQXH7P/Jg/PkgJtPghdabT3I7m/KCzVnuWzoBV7z/tX1LS5MJzIZYzfmF8kTWCX2fSGjMjCuhTHhBF0xbZQu7uxrSxGJrpdXHw52FUK6qHM/lztptbVmpYKYZA3u3K7V0/Kr4FGzEujNTGbwEMHJuBdo7uMJhs0OqHcpcZNPZVJZ3d0Hx2jmQ9KW9vboXlsfflDdRWJTXoy+1rLaqW5Dcz2+uDn22ay4sBrpRElIGc7PPR+x0Z72FJCD44t8wRsb+uonU0XaXvIQjYMW3pnJh5n5gFu2wTQ7OvpE2SF1n3fDD5j7Lz79F8b4t8uyFQuNMELIYS2XOeSO2a2Q6yN6Zvm+RRU/yEg14UP0fWTLYHsNrFRvJNFcqXNqy3a1KoYeLN6dfRWjKFMftHLrF8L7iTV7DT2T/3RDoMl2Bp9Kz4mH6YrVc5F6fnJ/4T8Nfa0nFnFGXVHaHel3ep8Pvy+ukyGvsO9uo8tvXVeEXaiLgAZGp43/RpuyEwJKDjQLo7TPDQeISHI0AyNWbraKRyuujWy6dRnyJP0kQVCfm8Fj54iKOai1ebub15QgdxSyfbiCRHdtjC47Et7d0YGNNh0D0Z/ok//uJav+kcSyMldoO/Z8uZVe+P2FQxYNFpxFSafkP8ls0PtxbMoXRp3on3hfaOAXMeKVWxpbiocmWO7zdVaSX3csc6P7eL85acsaW3yCZBxzeFLSacKxcL5GgQIJrewla5r4aojWSjQoH/iYhKwVK8x0654eobCExDzGVVP0i/8Dfn/niIu4AQC39sA1iGvriT9TIf1ZkaP/LuVdeZQNpdS8dbNS2xx/hR0Skgjkgvq4Gi0VgKptwGrVvoUs2c9MmniNNx7r3Ah7LhcxVUfVsF/VVz1HbTlgfh/ux+gUeNaVhzMs65n1v2xocDayb36FC8PZaWR9Qe/5wSnRnYTx6qyLyhsK0tdxdIegAwN6wHiSjcD0/bYGWOpH1zBrGnvARG7NDe4iEwDbkI6CyJg1LSd0jZZ+BMc2OA3a0iqz4+fKHEEe+u1Vsi6bwpPis0e+J5u3kj2q6/OjFz74oCB3YTyt3tMGq3sTg8eQBnZlk6NlJzJWQbjKNnpt0nzUn/vq7oie9haqw65xQc/lLlJZY41MNXc2wWpUoyKcq0LcOmJbj09zAMewZoVeA26/Y3totObmSkwf/Qis9a65WXHUJkTQQtdkaWVI2FXSZ9kzoCWR5GbvRRBK/atTY7iVf5W6rl6Jcobi9hYLVykGXAu+Jjl5Gr6D/tbjhr0gGygKJurasgl6mv0PhP1iBbYYbJldpKJtLT6wqdkqtVZsvxkkXDcxC16vCwA9Zsg+U156ir5iB0AYDZTkfVWwVOYDnnT07MJZJXp3lTnQ+Qya+s3qF1He9j9F6iYc41p4wGSJsJLASml6v7NoAj0LmPd+/BvhSr4etXKRLcS8BBsuDA2YPRW+oEyUpxyqOYGN97wbRRSi2IuqO5ZRhpDreZTnv32YFMNFbSTIkrfgbiJN49iixGkloJKCEOKZdXmouNvw3d5xsk5hjT4QAca2jsdyQniozboihVkJ23HlvoC+TqgHWXVbzryFNzz9ssOb1nmRHbAiNGYUupVEj9KevBoX9mw7NZe7VInDNDU/FgGxubPXvDDPrnZOW7y+qkacF1Jg+BN5OmtfhzFA4vSDtvADyN07pn6SgZ6FgC4mGsut4MAVtdtsbo50BwtSWqOWULpbNejxD0PVsgxTukl48n4LYTzizUrguktdexK+RedFLa16VtJGB1GPLbftqseUrfljk/X9Jh2rqQUml6s+ns+hMvR4FC8U/PTNafU0TAB46gj++H+3o71iJIYIbbKkPI93U/9i53ICzrtBHmzjOrN1qkYxO8Hib10J23eKDJg4U7xFKlxCtMqsAuzoSN7PGA6o3G3XD4OZ2U3eJDdindw7QtvgaMmIZHoTPKWCxgW9C+4aFuq9wG3Vznpr4tzXdunTn0edwp7rpHD3caueguDf8GnSoCKt0btXg4N5dGIxZmyA/rqdw48Rx8GAKMUVrIFqIwhXbC3LJEVprXzNOrVWr8tLeNop0rpCNs6kOuO8kR2zDSb93ay8L8bO4RmHSyGQlfXf+sV+eciSmczDBU+L+/atv5Vup0MWiMqtPNkBgl79y0hJXNCiV4wlrukSq1QRdfioKViS9SPTa0YSWFBRO32q9MqbBxPQQRD0xZu3hkLQaDqckXlbHsox3qk0Uz27/02dCDyyWACjERW7RrW49vLEsuWPF4rRfb2eExEmF8KEu2gKJNtkNTmcxd+nUNPB+8wr1+rhTXrF8EK17FrbyXoVGbdS+lQh6yOwU6utK2HU1nxoeG8hf2BOCcw1rga40gfS0RE1V0qP7XNPASU4kOe16CncMeqtqoUR5m8vHdMf+PnIbxIOoKTo1YbggyP1hYBMYXNKh16sPhODIOepJTxRUkdOJsNvL5e+kyWrvhx8jAkowWEj3uIrKaL6jDfu6wV3YlZt2u9/joE9xTS81Yf25Kf7dB8UouiwQQp22kS+jOat43Fuey3B7p9Hg4Cr0i0F4awpOyre3/7iPmhmFgnYxntTpYxLicty+BpCh55FEFEmE=";
        $flight['searchKey'] = "hZJGvxjxVtO7FHSsgfqjDBZwWP1Hn7Eq2hNakC1JWK7wXXLd6KJ3mW/XWx0zcNa4ZoEObuh7XCU3Gpd1ck3R4Bqwb3no+0bHTbz6QiJwLytszLkKHxP3pfzh2vAaIePBnZ93o2nQBkswBgTgKHM3rHlf/h+8lX1aRTXKDnW15Fnyg4oW/KSSYWCvs/b2Cnv5zwTuI/m84i+l5XeAbZ1Ym2VGkQTmIvrMK0QPocap3i3+xVxZuQvBDAg/UkDYR70gzy9S53+Psbsm1g+beWmA1765wFOkTXnqT651qEMu1nZfLi5xSgb0F3hb+2g581d5EQY9QN9aOA548fPecTihP2Er7c4B3he2+nqvKHkyAqf6c4AByXWK3XiT89RZX0jyKz67+nJlWfjYqscEfoqONgzHXJatA+DG8b/F3T5BlZwNxiKxpXCOf5e/pNuZ/fKV+os2h+4J0wsP6S25G/VAz6Ho/oyelBkI/cn/bX0UARW7V2nAW7oLsM172kJZELh9k25qE8t+dybOp69xrfHu7xIoyKUBJSEeMphIvhu7ZG8g3zTzYGGkNFNcmU/sUSBGl/fX0237T2TraLxGWi2ut5NUq8YW8bgNOGuEMBRTID8XflCjkmbaWozxwIjZCTan0ehBeD5RK+LuTsx5flST+Q0dUvUlwqdKb0r3gddkWD+wK30uKYPUbxSqq/xkpXod";
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

    ///////////////////////end of testing data ///////////////////////-------------


    public function getpdf(Request $request)
    {
        $request['type'] = "reprint";
        $data = $this->flight($request);
        $data2 = json_decode(json_encode($data->original));

        if ($data2->Status_Code != 'TXN'){
            return response()->json(['statuscode'=>"ERR","message"=>@$data2->Message ?? "Please try after sometime"]);
        }

        $data3['test'] = $data2->Data;
        return view('invoiceandroid',$data3);
    }

}