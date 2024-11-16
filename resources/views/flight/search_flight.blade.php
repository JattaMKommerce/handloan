@extends('layouts.app')
@section('title', 'Search Flight')
@section('content')


<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/simple-notify@0.5.5/dist/simple-notify.min.css" />
<link href="//code.jquery.com/ui/1.9.2/themes/smoothness/jquery-ui.css" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />


<style>
    body {
        background-image: url("https://png.pngtree.com/thumb_back/fh260/background/20220314/pngtree-spring-festival-ticket-grab-concept-map-air-ticket-map-image_1050156.jpg");
        background-size: cover;
        background-repeat: no-repeat;
    }

    .button-css {
        display: inline-flex;
        /* background: #1e3d73; */
        border-radius: 30px;
        font-weight: 600;
        /* color: rgb(255, 255, 255); */
        padding: 8px 25px;
        cursor: pointer;
        transform: translateY(35px);
        margin: auto;
    }

    ul {
        margin: 0px;
        list-style: none;
        padding: 0px;
    }

    .ptag {
        font-size: 14px;
        font-weight: 600;
        color: #777d74;
    }

    .select2-search__field {
        padding: 3px !important;
        border-radius: 5px;
        box-shadow: 1px 1px 2px grey;
    }

    .form-control {
        border: 1px solid #a09e9e;
    }

    .select2-container--default .select2-selection--single {
        border: 1px solid #a09e9e !important;
    }

    .bg-silver {
        background-color: #1e3d73 !important;
        color: white;
        font-weight: 700;
        cursor: pointer;
    }

    .form-control1 {
        height: 35px !important;
    }


    .range-slider {
        width: 231px;

        position: relative;
        height: 1em;
    }

    .range-slider svg,
    .range-slider input[type="range"] {
        position: absolute;
        left: 0;
        bottom: 0;
    }

    input[type="number"] {
        border: 1px solid #ddd;
        text-align: center;
        font-size: 1.6em;
    }

    input[type="number"]:invalid,
    input[type="number"]:out-of-range {
        border: 2px solid #ff6347;
    }

    input[type="range"] {
        -webkit-appearance: none;
        appearance: none;
        width: 100%;
    }

    input[type="range"]:focus {
        outline: none;
    }

    input[type="range"]:focus::-webkit-slider-runnable-track {
        background: #f57c32;
    }

    input[type="range"]:focus::-ms-fill-lower {
        background: #f57c32;
    }

    input[type="range"]:focus::-ms-fill-upper {
        background: #f57c32;
    }

    input[type="range"]::-webkit-slider-runnable-track {
        width: 100%;
        height: 5px;
        cursor: pointer;
        animate: 0.2s;
        background: #f57c32;
        border-radius: 1px;
        box-shadow: none;
        border: 0;
    }

    input[type="range"]::-webkit-slider-thumb {
        z-index: 2;
        position: relative;
        box-shadow: 0px 0px 0px #000;
        border: 1px solid #f57c32;
        height: 18px;
        width: 18px;
        border-radius: 25px;
        background: #a1d0ff;
        cursor: pointer;
        -webkit-appearance: none;
        appearance: none;
        margin-top: -7px;
    }

    input[type="range"]::-moz-range-track {
        width: 100%;
        height: 5px;
        cursor: pointer;
        animate: 0.2s;
        background: #f57c32;
        border-radius: 1px;
        box-shadow: none;
        border: 0;
    }

    input[type="range"]::-moz-range-thumb {
        z-index: 2;
        position: relative;
        box-shadow: 0px 0px 0px #000;
        border: 1px solid #f57c32;
        height: 18px;
        width: 18px;
        border-radius: 25px;
        background: #a1d0ff;
        cursor: pointer;
    }

    input[type="range"]::-ms-track {
        width: 100%;
        height: 5px;
        cursor: pointer;
        animate: 0.2s;
        background: transparent;
        border-color: transparent;
        color: transparent;
    }

    input[type="range"]::-ms-fill-lower,
    input[type="range"]::-ms-fill-upper {
        background: #f57c32;
        border-radius: 1px;
        box-shadow: none;
        border: 0;
    }

    input[type="range"]::-ms-thumb {
        z-index: 2;
        position: relative;
        box-shadow: 0px 0px 0px #000;
        border: 1px solid #f57c32;
        height: 18px;
        width: 18px;
        border-radius: 25px;
        background: #fff;
        cursor: pointer;
    }

    #main-display {
        position: absolute;
        background-color: #FFF;
        width: 430px !important;
        right: -25px;
        border-radius: 1.5rem;
        box-shadow: 2px 2px 10px silver;
        margin-top: 10px;
        z-index: 3;
        display: none;
    }

    .outer-div {
        width: 7.3rem;
        height: 2.8rem;
        background-color: rgb(255, 255, 255);
        box-shadow: silver 0px 1px 4px 0px;
        border-radius: 4rem;
        cursor: pointer;
        display: flex;
        -webkit-box-align: center;
        align-items: center;
        justify-content: space-around;
    }

    .minsign {
        font-size: 1.5rem;
        color: #1e3d73;
    }

    .count {
        font-weight: 500;
        font-size: 1.1rem;
        color: #1e3d73;
    }

    .select2-container--default .select2-selection--single {
        height: 35px !important;
        padding: 3px 8px;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow b {
        top: 65% !important;
    }

    #ui-datepicker-div {
        width: auto !important;
        min-width: 400px !important;
        transform: translateX(-100px);
    }

    .ui-datepicker-calendar td a[data-custom] {
        position: relative;
        transition: none;
        color: #351976;
    }

    .ui-datepicker-calendar td a[data-custom]::after {
        content: '₹' attr(data-custom);
        display: block;
        font-size: 8px;
        color: green;
        font-weight: bold;
    }

    .ui-datepicker-today {
        background-color: blue !important;
        color: #FFF !important;
    }

    .ui-state-default,
    .ui-widget-content .ui-state-default,
    .ui-widget-header .ui-state-default {
        min-height: 55px !important;
        min-width: 45px !important;
    }

    .ui-datepicker table td.ui-datepicker-today .ui-state-highlight {
        color: black !important;
    }

    .ui-datepicker table td.ui-datepicker-current-day .ui-state-active {
        color: black !important;
    }

    .card {
        display: flex;
        flex-direction: column;
        flex-basis: 300px;
        flex-shrink: 0;
        flex-grow: 0;
        max-width: 100%;
        background-color: #fff;
        box-shadow: 0 5px 10px 0 rgba(0, 0, 0, 0.15);
        margin-bottom: 10px;
    }

    .card-body {
        padding: 0.8rem;
    }

    .card-title {
        font-size: 1.25rem;
        line-height: 1.33;
        font-weight: 700;
    }

    .card-title.skeleton {
        min-height: 28px;
        border-radius: 4px;
    }

    .card-intro {
        margin-top: 0.75rem;
        line-height: 1.5;
    }

    .card-intro.skeleton {
        min-height: 72px;
        border-radius: 4px;
    }

    .skeleton {
        background-color: #e2e5e7;
        background-image: linear-gradient(90deg, rgba(255, 255, 255, 0), rgba(255, 255, 255, 0.5), rgba(255, 255, 255, 0));
        background-size: 40px 100%;
        background-repeat: no-repeat;
        background-position: left -40px top 0;
        -webkit-animation: shine 1s ease infinite;
        animation: shine 1s ease infinite;
    }

    @-webkit-keyframes shine {
        to {
            background-position: right -40px top 0;
        }
    }

    @keyframes shine {
        to {
            background-position: right -40px top 0;
        }
    }
</style>
</head>

<!-- <body> -->

<div id="loading">
    <div id="loading-center">
    </div>
</div>

<div class="wrapper">
    <div class="row mt-2">
        <div class="col-sm-12 col-md-12 col-lg-12">
            <div class="iq-card border">
                <div class="iq-card-header d-flex justify-content-between">
                    <div class="iq-header-title">
                        <h4 class="card-title text-primary fw-bold">Flight Booking Service</h4>
                    </div>
                </div>
                <div class="iq-card-body">
                    <form id="form-wizard1">
                        <input type="hidden" name="type" value="fetch">
                        <input type="hidden" name="airlinefilter" value="">
                        <input type="hidden" name="inventoryType" value="0">
                        <!-- fieldsets -->
                        <div class="row">
                            <div class="col-lg-12 p-1">
                                <div class="form-group">
                                    <div class="form-check">
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" id="oneway" name="bookingType" value="0" class="custom-control-input typebox" onchange="handleChange()">
                                            <label class="custom-control-label ptag" for="oneway">
                                                One-way</label>
                                        </div>
                                        <div class="custom-control custom-radio custom-control-inline ">
                                            <input type="radio" id="roundtrip" name="bookingType" value="1" class="custom-control-input" onchange="handleChange()">
                                            <label class="custom-control-label ptag" for="roundtrip">
                                                Round-trip</label>
                                        </div>
                                        <!-- <div class="custom-control custom-radio custom-control-inline ">
                                            <input type="radio" id="specialroundtrip" name="bookingType" value="2" class="custom-control-input" onchange="handleChange()">
                                            <label class="custom-control-label ptag" for="specialroundtrip">
                                                Multi-city</label>
                                        </div> -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row px-3">
                            <div class="col-lg-5">
                                <div class="row">
                                    <div class="col-lg-6 p-1">
                                        <label class="ptag">From :</label><br />
                                        <select id="from" name="origin" style="width: 100%;" class="form-select select2 typebox ptag input-lg from-control select2-container">
                                            <option selected="">Select location</option>
                                        </select>


                                    </div>
                                    <div class="col-lg-6 p-1">
                                        <label class="ptag ">To :</label><br />
                                        <select id="to" name="destination" style="width: 100%;" class="form-select select2 typebox ptag input-lg from-control select2-container">
                                            <option selected="">Select location</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-2 p-1">

                                <label class="ptag ">Departure :</label><br />
                                <input type="text" placeholder="Enter Departure Date" autocomplete="off" class="form-control form-control1 disablebeforedate" id="departure" value="" name="travelDate" />
                            </div>

                            <div class="col-lg-2 p-1" style="display: none;" id="returnDateshow">
                                <label class="ptag ">Return :</label><br />
                                <input type="text" placeholder="Enter Return Date" autocomplete="off" class="form-control form-control1 disablebeforedate" id="return1" value="" name="travelDate" />
                            </div>

                            <div class="col-lg-3 p-1">
                                <div id="select-data-inputs" class="controls form-row">
                                    <div class="col-lg-12">
                                        <label class="ptag">Passenger & Class:</label><br />
                                        <input type="text" id="search-markets" class="input form-control form-control1 bg-white" readonly value="1 Passenger, Economy, Domestic">
                                    </div>
                                </div>
                                <div id="main-display">
                                    <div id="select-markets" onclick="total()" class="row select-filters select-markets-filters px-3">
                                        <div class="row mt-3 px-3">
                                            <div class="col-lg-4">
                                                <label class="ptag my-1">Adult :</label><br />
                                                <div class="outer-div">
                                                    <span class="minsign" onClick="decrement1()"> -
                                                    </span>
                                                    <h4 class="count" id="adult" name="adultCount"> 1 </h4>
                                                    <span class="minsign" onClick="increment1()"> +
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="col-lg-4">
                                                <label class="ptag my-1">Children :</label><br />
                                                <div class="outer-div">
                                                    <span class="minsign" onClick="decrement2()"> -
                                                    </span>
                                                    <h4 class="count" id="children" name="childCount"> 0 </h4>
                                                    <span class="minsign" onClick="increment2()"> +
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="col-lg-4">

                                                <label class="ptag my-1">Infant :</label><br />
                                                <div class="outer-div">
                                                    <span class="minsign" onClick="decrement3()"> -
                                                    </span>
                                                    <h4 class="count" id="infant" name="infantCount"> 0 </h4>
                                                    <span class="minsign" onClick="increment3()"> +
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-12">
                                            <label class="ptag mt-3 px-2 ">Travel Class :</label><br />
                                            <div class="form-group">
                                                <div class="form-check" id="travelclass" onchange="valclass()">
                                                    <div class="custom-control custom-radio custom-control-inline">
                                                        <input type="radio" id="b1" value="0" class="custom-control-input typebox" name="travelClass" checked>
                                                        <label class="custom-control-label ptag" for="b1">
                                                            Economy</label>
                                                    </div>
                                                    <div class="custom-control custom-radio custom-control-inline ">
                                                        <input type="radio" id="b2" value="1" class="custom-control-input" name="travelClass">
                                                        <label class="custom-control-label ptag" for="b2">
                                                            Business</label>
                                                    </div>
                                                    <div class="custom-control custom-radio custom-control-inline ">
                                                        <input type="radio" id="b3" value="2" class="custom-control-input" name="travelClass">
                                                        <label class="custom-control-label ptag" for="b3">
                                                            First Class</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-12">
                                            <label class="ptag px-2">Travel Type :</label><br />
                                            <div class="form-group">
                                                <div class="form-check" id="traveltype" onchange="valtype()">
                                                    <div class="custom-control custom-radio custom-control-inline">
                                                        <input type="radio" id="a1" value="0" class="custom-control-input typebox" name="travelType" checked>
                                                        <label class="custom-control-label ptag" for="a1">
                                                            Domestic</label>
                                                    </div>
                                                    <div class="custom-control custom-radio custom-control-inline ">
                                                        <input type="radio" id="a2" value="1" class="custom-control-input" name="travelType">
                                                        <label class="custom-control-label ptag" for="a2">
                                                            International</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="button-div text-center">
                            <button type="button" class=" btn-primary button-css" id="searchflight" onclick="searchFlight()">SEARCH FLIGHTS</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="wrapper" style="position: -webkit-sticky;position: sticky;top: 0;z-index: 1;">
    <div class="row mt-3">
        <div class="col-12">
            <div class="rondtripDiv">

            </div>
        </div>
    </div>
</div>

<div class="wrapper">
    <div class="row mt-3">
        <div class="col-3">
            <div class="preview-card-side">

            </div>
        </div>
        <div class="col-9">
            <div class="preview-card-body">

            </div>
        </div>

    </div>
</div>


<div class="modal fade bd-example-modal-lg" id="roundtripViewPice" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Fare Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="departureRoundTrip">

            </div>
            <div class="modal-footer">
                <h6 id="totalBaseFare"> </h6>
                <button type="button" class="btn btn-secondary" onclick="bookRoundTripTicket()" disabled id="nextbutton">Next</button>
            </div>
        </div>
    </div>
</div>


@endsection

@push('script')


<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/simple-notify@0.5.5/dist/simple-notify.min.js"></script>

<script>
    $(document).ready(function() {});

    var roundTripOngoing;
    var roundTripReturn;
    var firstArrayRoundTrip;
    var secondArrayRoundTrip;
    var oneWayflightDetails;
    var roundTripSearchKey;
    var searchFlightDetails;
    var airlinesDetails = []
    var finalFilter = []
    var airlinePriceDetails = [];
    var returnairlinePriceDetails = [];
    var priceRange1;
    var priceRange2;
    var returnpriceRange1;
    var returnpriceRange2;
    var roundTripFlightDetails;
    var filterOneWayFlight;
    var airlineLogo;
    var firstArrayRoundTripFare;
    var secondArrayRoundTripFare;

    $(document).ready(function() {
        $('#sidebarhide').addClass('open');
        $('.iq-page-menu-vertical').addClass('sidebar-main');

        $.ajax({
            url: "{{ url('getflight') }}",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                type: 'airlineImage',

            },
            success: function(data) {
                if (data.data) {
                    airlineLogo = data.data
                } else {
                    notify(data.message, "error")
                }
            }
        });
    });

    function showLogo(item) {


        const airline_logo = airlineLogo.filter((data) => {
            return data.airline_code == item.Airline_Code
        })

        return airline_logo[0]?.flight_logo_url;
    }

    function getPessanger(type) {
        switch (type) {
            case 0:
                return "Adult";
                break;
            case 1:
                return "Children";
                break;
            case 2:
                return "Infants";
                break;
        }
    }


    function renderFlightList(data) {

        let i = 0;
        let bookingType = $("input[name='bookingType']:checked").val();

        if (bookingType === '0') {

            filterOneWayFlight = data
            for (let flightdata of data.data.Flight) {
                for (let x of flightdata.Flights) {

                    airlinesDetails.push(x.Segments[0].Airline_Name)
                    airlinePriceDetails.push(x.Fares[0].FareDetails[0].Total_Amount)

                    let days = getDateDiff(x.Segments[0].Departure_DateTime.split(" ")[0], x.Segments[0].Arrival_DateTime.split(" ")[0]);
                    let daysFormated = '';
                    if (days > 0) {
                        daysFormated = `(+ ${days} Days)`
                    }

                    var content = `<div class="iq-card iq-card-block iq-card-stretch iq-card-height iq-mb-1">
                  <div class="card-body">
                
                    <h6 class="card-title"><img src="${showLogo(x.Segments[0])}"  style="height:30px; "/>  ${x.Segments[0].Airline_Name}</h6>
                    <div class="row">
                        <div class="col-3">
                            <span class="card-text" style="font-size: 12px;"><b>${x.Segments[0].Origin}</b><span
                                    class="text-secondary"> ${x.Segments[0].Origin_City}</span></span><br>
                            <h5 class="fw-bold">${x.Segments[0].Departure_DateTime.split(" ")[1]}</h5>
                        </div>

                        <div class="col-2">
                            <span class="card-text" style="font-size: 12px;"><b>Duration <span class="text-danger"> ${daysFormated} </span></b></span><br>
                            <h5 class="fw-bold"> ${diff_hours(x.Segments)}</h5>
                        </div>
                        <div class="col-3">
                            <span class="card-text" style="font-size: 12px;"><b>${x.Segments[x.Segments.length-1].Destination}</b><span
                                    class="text-secondary"> ${x.Segments[x.Segments.length-1].Destination_City}</span></span><br>
                            <h5 class="fw-bold">${x.Segments[x.Segments.length-1].Arrival_DateTime.split(" ")[1]}</h5>
                        </div>
                        <div class="col-2">
                            <span class="card-text" style="font-size: 12px;"></span><br>
                            <h4 class="fw-bold"> ₹ ${x.Fares[0].FareDetails[0].Total_Amount}</h4>
                            <h6 class="fw-bold"> Per ${getPessanger(x.Fares[0].FareDetails[0].PAX_Type)}</h6>
                           
                        </div>

                        <div class="col-2">
                            <span class="card-text" style="font-size: 12px;"></span><br>
                                <a class="btn" data-toggle="collapse" style="background-color: rgb(255, 109, 56); color:rgb(255, 255, 255);padding: 7px 10px;" href="#collapseExample1_${i}" role="button" aria-expanded="false"
                                aria-controls="collapseExample">View Fare</a>
                        </div>
                        <div class="col-3">
                            <h6 class="fw-bold">${x.Fares[0].Refundable == true ? '<span style="color: #28a745 !important;">Refundable</span>':'<span class="text-danger">Non Refundable</span>'}</h6>
                            <h6 class="fw-bold"> Class : ${x.Fares[0].FareDetails[0].FareClasses[0].Class_Code}</h6>
                        </div>

                        <div class="col-6">
                        <h6>${getStop(x.Segments)}</h6> 
                        <h6> ${x.Fares[0].Seats_Available || 0} Seats available</h6>      
                        </div>
                        <div class="col-3" style="text-align: end;">
                            <a data-toggle="collapse" href="#collapseExample_${i}" role="button" aria-expanded="false"
                                aria-controls="collapseExample">View Flight Details</a>
                        </div>
                    </div>
        
    
        <div class="collapse" id="collapseExample_${i}">
            <div class="card card-body">
                <ul class="nav nav-tabs" id="myTab-1" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="flight-tab" data-toggle="tab" href="#flight_details_${i}"
                            role="tab" aria-controls="flight" aria-selected="true">FLIGHT DETAILS</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="fare-tab" data-toggle="tab" href="#fare_details_${i}" role="tab"
                            aria-controls="fare" aria-selected="false">Fare Details</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="cancellation-tab" data-toggle="tab" href="#cancellation_${i}" role="tab"
                            aria-controls="contact" aria-selected="false">Cancellation</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" id="cancellation-tab" data-toggle="tab" href="#dateChange_${i}" role="tab"
                            aria-controls="contact" aria-selected="false">Reschedule Charges</a>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent-2">
                    <div class="tab-pane fade show active" id="flight_details_${i}" role="tabpanel"
                        aria-labelledby="home-tab">`
                    for (let flightDetails of x.Segments) {

                        content += `<div class="iq-card iq-card-block iq-card-stretch iq-card-height iq-mb-3">
                            <div class="card-body">
                                <h6 class="card-title">${flightDetails.Airline_Name} ${flightDetails.Airline_Code} |
                                    ${flightDetails.Flight_Number}</h6>
                                <div class="row">
                                    <div class="col-3">
                                        <h4 class="fw-bold">${flightDetails.Departure_DateTime.split(" ")[1]}</h4>
                                        <h6 class="fw-bold">${flightDetails.Departure_DateTime.split(" ")[0]}</h6>
                                        <span class="card-text" style="font-size: 12px;"><span class="text-secondary">
                                                ${flightDetails.Origin_City}</span></span><br>
                                        <span class="card-text" style="font-size: 14px;">Terminal
                                            ${flightDetails.Origin_Terminal}</span>

                                    </div>

                                    <div class="col-2">
                                        <span class="card-text" style="font-size: 12px;"><b>Duration</b></span><br>
                                        <h5 class="fw-bold">${flightDetails.Duration.split(":")[0]}h
                                            ${flightDetails.Duration.split(":")[1]}m</h5>
                                    </div>
                                    <div class="col-3">
                                        <h4 class="fw-bold">${flightDetails.Arrival_DateTime.split(" ")[1]}</h4>
                                        <h6 class="fw-bold">${flightDetails.Arrival_DateTime.split(" ")[0]}</h6>
                                        <span class="card-text" style="font-size: 12px;"><span class="text-secondary">
                                                ${flightDetails?.Destination_City}</span></span><br>
                                        <span class="card-text" style="font-size: 14px;">Terminal
                                            ${flightDetails?.Destination_Terminal} </span>
                                    </div>
                                    <div class="col-2">
                                        <h5>CHECK IN</h5><br>
                                        <h6 class="fw-bold">
                                            ${x?.Fares[0]?.FareDetails[0]?.Free_Baggage?.Check_In_Baggage} (1 piece
                                            only)</h6>
                                    </div>

                                    <div class="col-2">
                                        <h5>CABIN:</h5><br>
                                        <h6 class="fw-bold">${x?.Fares[0]?.FareDetails[0]?.Free_Baggage?.Hand_Baggage}
                                            (1 piece only)</h6>
                                    </div>
                                </div>
                            </div>
                        </div>`
                    }
                    content += `</div>
                                                        <div class="tab-pane fade" id="fare_details_${i}" role="tabpanel" aria-labelledby="profile-tab">
                                                            <div class="iq-card iq-card-block iq-card-stretch iq-card-height iq-mb-3">
                                                                <div class="card-body">
                                                                    <div class="row">
                                                                        <div class="col-3">Base Fare</div>
                                                                        <div class="col-3">₹ ${x?.Fares[0]?.FareDetails[0]?.Basic_Amount}</div>
                                                                    </div>

                                                                    <div class="row">
                                                                        <div class="col-3">Taxes and Fees </div>
                                                                        <div class="col-3">₹ ${x?.Fares[0]?.FareDetails[0]?.AirportTax_Amount}</div>
                                                                    </div>

                                                                    <div class="row font-weight-bold">
                                                                        <div class="col-3 text -bold">Total</div>
                                                                        <div class="col-3 text -bold">₹ ${x?.Fares[0]?.FareDetails[0]?.Total_Amount}</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="tab-pane fade" id="cancellation_${i}" role="tabpanel" aria-labelledby="contact-tab">
                                                            <div class="iq-card-body">
                                                                <div class="table-responsive">
                                                                    <table class="table">
                                                                        <thead>
                                                                            <tr>
                                                                                <th scope="col">Time frame</th>
                                                                                <th scope="col">Airline Fee</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>`;
                    if (x?.Fares[0]?.FareDetails[0]?.CancellationCharges) {
                        for (let canceldetails of x?.Fares[0]?.FareDetails[0]?.CancellationCharges) {

                            content += `<tr>
                                                                                <th scope="row">${canceldetails?.DurationFrom} ${canceldetails?.DurationTypeFrom =='0'?'Hours':canceldetails?.DurationTypeFrom =='1'?'Days':''} to
                                                                                    ${canceldetails?.DurationTo} ${canceldetails?.DurationTypeTo =='0'?'Hours':canceldetails?.DurationTypeTo =='1'?'Days':''}</th>
                                                                                <td>${getPessanger(canceldetails?.PassengerType)} : ₹
                                                                                    ${canceldetails?.Value}</td>
                                                                            </tr>`;
                        }
                    }

                    content += `</tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="tab-pane fade" id="dateChange_${i}" role="tabpanel" aria-labelledby="contact-tab">
                                                            <div class="iq-card-body">
                                                                <div class="table-responsive">
                                                                    <table class="table">
                                                                        <thead>
                                                                            <tr>
                                                                                <th scope="col">Time frame</th>
                                                                                <th scope="col">Airline Fee</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>`;
                    if (x?.Fares[0]?.FareDetails[0]?.RescheduleCharges) {
                        for (let dateChange of x?.Fares[0]?.FareDetails[0]?.RescheduleCharges) {

                            content += `<tr>
                                                                                <th scope="row">${dateChange?.DurationFrom} ${dateChange?.DurationTypeFrom =='0'?'Hours':dateChange?.DurationTypeFrom =='1'?'Days':''} to
                                                                                    ${dateChange?.DurationTo} ${dateChange?.DurationTypeTo =='0'?'Hours':dateChange?.DurationTypeTo =='1'?'Days':''}</th>
                                                                                <td>${getPessanger(dateChange?.PassengerType)} : ₹ ${dateChange?.Value}</td>
                                                                            </tr>`;
                        }
                    }

                    content += `</tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="collapse" id="collapseExample1_${i}">
                                                <div class="card card-body">
                                                
                                                <div class="iq-card iq-card-block iq-card-stretch iq-card-height iq-mb-3">
                                                                <div class="card-body">
                                                                <div class="table-responsive">
                                                                <table class="table mb-0">
                                                                    <thead class="thead-light">
                                                                        <tr>
                                                                        <th scope="col">FARES</th>
                                                                        <th scope="col">CABIN BAG</th>
                                                                        <th scope="col">CHECK-IN</th>
                                                                        <th scope="col">Seats Available</th>
                                                                        <th scope="col"></th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>`
                    let selectedFare = 0;
                    for (let fareDetails of x.Fares) {

                        content += ` <tr>
                                <td>${fareDetails?.FareDetails[0]?.FareClasses[0]?.Class_Desc}</td>
                                <td>${fareDetails?.FareDetails[0]?.Free_Baggage?.Hand_Baggage}</td>
                                <td>${fareDetails?.FareDetails[0]?.Free_Baggage?.Check_In_Baggage}</td>
                                <td>${fareDetails.Seats_Available} seats</td>
                                <td> ₹ ${fareDetails?.FareDetails[0]?.Total_Amount}<br>
                                <button class="btn btn-info" type="button" onclick="bookTicket(${i}, ${selectedFare})">Book Now</button>
                                </td>
                            </tr>`
                        selectedFare++;
                    }
                    content += ` </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                            </div>
                                            </div>
                                        </div>
                                    </div>`;
                    i++

                    $(".preview-card-body").append(content)
                }
            }

        }


        if (bookingType === '1') {

            if (data?.data?.Flight) {
                let days = getDateDiff();
                let daysFormated = '';
                if (days > 0) {
                    daysFormated = `(+ ${days} Days)`
                }

                var content = `<div class="row">`
                content += `<div class="col-6">`

                roundTripOngoing = data.data.Flight[0]
                roundTripReturn = data.data.Flight[1]

                for (let x of data.data.Flight[0].Flights) {

                    console.log(x)

                    airlinePriceDetails.push(x.Fares[0].FareDetails[0].Total_Amount)
                    airlinesDetails.push(x.Segments[0].Airline_Name)

                    content += `<div class="row">
                            <div class="col-12">
                                    <div class="iq-card iq-card-block iq-card-stretch iq-card-height iq-mb-1">
    <div class="card-body">
        <h6 class="card-title"> <img src="${showLogo(x.Segments[0])}"  style="height:30px; "/>  ${x.Segments[0].Airline_Name}</h6>
        <div class="row">
            <div class="col-3">
                <span class="card-text" style="font-size: 12px;"><b>${x.Segments[0].Origin}</b><span
                        class="text-secondary"> ${x.Segments[0].Origin_City}</span></span><br>
                <h5 class="fw-bold">${x.Segments[0].Departure_DateTime.split(" ")[1]}</h5>
            </div>

            <div class="col-3">
                <span class="card-text" style="font-size: 12px;"><b>Duration <span class="text-danger"> ${daysFormated} </span></b></span><br>
                <h5 class="fw-bold"> ${diff_hours(x.Segments)} </h5>
            </div>
            <div class="col-3">
                <span class="card-text" style="font-size: 12px;"><b>${x.Segments[x.Segments.length-1].Destination}</b><span
                        class="text-secondary"> ${x.Segments[x.Segments.length-1].Destination_City}</span></span><br>
                <h5 class="fw-bold">${x.Segments[x.Segments.length-1].Arrival_DateTime.split(" ")[1]}</h5>
            </div>
            
            <div class="col-3">
            <div class="custom-control custom-radio custom-radio-color custom-control-inline">
                <input type="radio" id="customRadio05_${i}" name="customRadio-11" class="custom-control-input bg-primary"  onchange="getTripid(${i}, 'ongoing')" >
                <label class="custom-control-label" for="customRadio05_${i}">  </label>
             </div>
            <h4 class="fw-bold"> ₹ ${x.Fares[0].FareDetails[0].Total_Amount}</h4>
             <h6 class="fw-bold"> Per ${getPessanger(x.Fares[0].FareDetails[0].PAX_Type)}</h6>
            </div>
            <div class="col-3">
                <h6 class="fw-bold">${x.Fares[0].Refundable == true ? '<span style="color: #28a745 !important;">Refundable</span>':'<span class="text-danger">Non Refundable</span>'}</h6>
                <h6 class="fw-bold"> Class : ${x.Fares[0].FareDetails[0].FareClasses[0].Class_Code}</h6>
            </div>

            <div class="col-4">
            <h6>${getStop(x.Segments)}</h6>  
            <h6> ${x.Fares[0].Seats_Available ||0} Seats available</h6>
            </div>
            <div class="col-5" style="text-align: end;">
            
                <a data-toggle="collapse" href="#tripid0_${i}" role="button" aria-expanded="false"
                    aria-controls="collapseExample">View Flight Details</a>
            </div>
        </div>
        
        <div class="collapse" id="tripid0_${i}">
            <div class="card card-body">
                <ul class="nav nav-tabs" id="myTab-1" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="flight-tab" data-toggle="tab" href="#flight_details_${i}"
                            role="tab" aria-controls="flight" aria-selected="true">FLIGHT DETAILS</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="fare-tab" data-toggle="tab" href="#fare_details_${i}" role="tab"
                            aria-controls="fare" aria-selected="false">Fare Details</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="cancellation-tab" data-toggle="tab" href="#cancellation_${i}" role="tab"
                            aria-controls="contact" aria-selected="false">Cancellation</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" id="cancellation-tab" data-toggle="tab" href="#dateChange_${i}" role="tab"
                            aria-controls="contact" aria-selected="false">Reschedule Charges</a>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent-2">
                    <div class="tab-pane fade show active" id="flight_details_${i}" role="tabpanel"
                        aria-labelledby="home-tab">`
                    for (let flightDetails of x.Segments) {
                        content += `<div class="iq-card iq-card-block iq-card-stretch iq-card-height iq-mb-3">
                                <div class="card-body">
                                <h6 class="card-title">${flightDetails.Airline_Name} ${flightDetails.Airline_Code} |
                                    ${flightDetails.Flight_Number}</h6>
                                <div class="row">
                                    <div class="col-4">
                                        <h6 class="fw-bold">${flightDetails.Departure_DateTime.split(" ")[1]}</h6>
                                        
                                        <span class="card-text" style="font-size: 12px;"><span class="text-secondary">
                                                ${flightDetails.Origin_City}</span></span><br>
                                        <span class="card-text" style="font-size: 14px;">Terminal
                                            ${flightDetails.Origin_Terminal}</span>

                                    </div>

                                    <div class="col-3">
                                        
                                        <h6 class="fw-bold">${flightDetails.Duration.split(":")[0]}h
                                            ${flightDetails.Duration.split(":")[1]}m</h6>
                                    </div>
                                    <div class="col-4">
                                        <h6 class="fw-bold">${flightDetails.Arrival_DateTime.split(" ")[1]}</h6>
                                        <span class="card-text" style="font-size: 12px;"><span class="text-secondary">
                                                ${flightDetails?.Destination_City}</span></span><br>
                                        <span class="card-text" style="font-size: 14px;">Terminal
                                            ${flightDetails?.Destination_Terminal} </span>
                                    </div>
                                </div>
                            </div>
                        </div>`
                    }
                    content += `</div>
                                 <div class="tab-pane fade" id="fare_details_${i}" role="tabpanel" aria-labelledby="profile-tab">
                                                            <div class="iq-card iq-card-block iq-card-stretch iq-card-height iq-mb-3">
                                                                <div class="card-body">
                                                                    <div class="row">
                                                                        <div class="col-3">Base Fare</div>
                                                                        <div class="col-3">₹ ${x?.Fares[0]?.FareDetails[0]?.Basic_Amount}</div>
                                                                    </div>

                                                                    <div class="row">
                                                                        <div class="col-3">Taxes and Fees </div>
                                                                        <div class="col-3">₹ ${x?.Fares[0]?.FareDetails[0]?.AirportTax_Amount}</div>
                                                                    </div>

                                                                    <div class="row font-weight-bold">
                                                                        <div class="col-3 text -bold">Total</div>
                                                                        <div class="col-3 text -bold">₹ ${x?.Fares[0]?.FareDetails[0]?.Total_Amount}</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="tab-pane fade" id="cancellation_${i}" role="tabpanel" aria-labelledby="contact-tab">
                                                            <div class="iq-card-body">
                                                                <div class="table-responsive">
                                                                    <table class="table">
                                                                        <thead>
                                                                            <tr>
                                                                                <th scope="col">Time frame</th>
                                                                                <th scope="col">Airline Fee</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>`;
                    if (x?.Fares[0]?.FareDetails[0]?.CancellationCharges) {

                        for (let canceldetails of x?.Fares[0]?.FareDetails[0]?.CancellationCharges) {

                            content += `<tr>
                                                                                <th scope="row">${canceldetails?.DurationFrom} ${canceldetails?.DurationTypeFrom =='0'?'Hours':canceldetails?.DurationTypeFrom =='1'?'Days':''} to
                                                                                    ${canceldetails?.DurationTo} ${canceldetails?.DurationTypeTo =='0'?'Hours':canceldetails?.DurationTypeTo =='1'?'Days':''}</th>
                                                                                <td>${getPessanger(canceldetails?.PassengerType)} : ₹
                                                                                    ${canceldetails?.Value}</td>
                                                                            </tr>`;
                        }

                    }

                    content += `</tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="tab-pane fade" id="dateChange_${i}" role="tabpanel" aria-labelledby="contact-tab">
                                                            <div class="iq-card-body">
                                                                <div class="table-responsive">
                                                                    <table class="table">
                                                                        <thead>
                                                                            <tr>
                                                                                <th scope="col">Time frame</th>
                                                                                <th scope="col">Airline Fee</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>`;
                    if (x?.Fares[0]?.FareDetails[0]?.RescheduleCharges) {
                        for (let dateChange of x?.Fares[0]?.FareDetails[0]?.RescheduleCharges) {

                            content += `<tr>
                                                                                <th scope="row">${dateChange?.DurationFrom} ${dateChange?.DurationTypeFrom =='0'?'Hours':dateChange?.DurationTypeFrom =='1'?'Days':''} to
                                                                                    ${dateChange?.DurationTo} ${dateChange?.DurationTypeTo =='0'?'Hours':dateChange?.DurationTypeTo =='1'?'Days':''}</th>
                                                                                <td>${getPessanger(dateChange?.PassengerType)} : ₹ ${dateChange?.Value}</td>
                                                                            </tr>`;
                        }
                    }

                    content += `</tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="collapse" id="collapseExample1_${i}">
                                                <div class="card card-body">
                                                
                                                <div class="iq-card iq-card-block iq-card-stretch iq-card-height iq-mb-3">
                                                                <div class="card-body">
                                                                <div class="table-responsive">
                                                                <table class="table mb-0">
                                                                    <thead class="thead-light">
                                                                        <tr>
                                                                        <th scope="col">FARES</th>
                                                                        <th scope="col">CABIN BAG</th>
                                                                        <th scope="col">CHECK-IN</th>
                                                                        
                                                                        <th scope="col"></th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>`
                    for (let fareDetails of x.Fares) {

                        content += ` <tr>
                                                                        <td>${fareDetails?.FareDetails[0]?.FareClasses[0]?.Class_Desc}</td>
                                                                        <td>${fareDetails?.FareDetails[0]?.Free_Baggage?.Hand_Baggage}</td>
                                                                        <td>${fareDetails?.FareDetails[0]?.Free_Baggage?.Check_In_Baggage}</td>
                                                                        
                                                                        <td> ₹ ${fareDetails?.FareDetails[0]?.Total_Amount}<br>
                                                                        <button class="btn btn-info">Book Now</button>
                                                                        </td>
                                                                        </tr>`
                    }
                    content += ` </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                            </div>
                                            </div>
                                        </div>
                                        </div>
                                        </div>
                                        </div>`

                    i++
                }

                content += ` </div>`


                content += `<div class="col-6">`

                let returnFlightIndex = 0;

                // ${returnFlightIndex == 0?'checked=""':""} 
                for (let x of data.data.Flight[1].Flights) {
                    returnairlinePriceDetails.push(x.Fares[0].FareDetails[0].Total_Amount)
                    content += `<div class="row">
                            <div class="col-12">
                                    <div class="iq-card iq-card-block iq-card-stretch iq-card-height iq-mb-1">
    <div class="card-body">
        <h6 class="card-title"><img src="${showLogo(x.Segments[0])}"  style="height:30px; "/>  ${x.Segments[0].Airline_Name}</h6>
        <div class="row">
            <div class="col-3">
                <span class="card-text" style="font-size: 12px;"><b>${x.Segments[0].Origin}</b><span
                        class="text-secondary"> ${x.Segments[0].Origin_City}</span></span><br>
                <h5 class="fw-bold">${x.Segments[0].Departure_DateTime.split(" ")[1]}</h5>
            </div>

            <div class="col-3">
                <span class="card-text" style="font-size: 12px;"><b>Duration <span class="text-danger"> ${daysFormated} </span></b></span><br>
                <h5 class="fw-bold">${diff_hours(x.Segments)} </h5>
            </div>
            <div class="col-3">
                <span class="card-text" style="font-size: 12px;"><b>${x.Segments[x.Segments.length-1].Destination}</b><span
                        class="text-secondary"> ${x.Segments[x.Segments.length-1].Destination_City}</span></span><br>
                <h5 class="fw-bold">${x.Segments[x.Segments.length-1].Arrival_DateTime.split(" ")[1]}</h5>
            </div>
         
            <div class="col-3">
            <div class="custom-control custom-radio custom-radio-color custom-control-inline">
                              <input type="radio" id="customRadio06_${returnFlightIndex}" name="customRadio-12" class="custom-control-input bg-primary" onchange="getTripid(${returnFlightIndex}, 'return')" >
                              <label class="custom-control-label" for="customRadio06_${returnFlightIndex}">  </label>
                           </div>
                           <h4 class="fw-bold"> ₹ ${x.Fares[0].FareDetails[0].Total_Amount}</h4>
                           <h6 class="fw-bold"> Per ${getPessanger(x.Fares[0].FareDetails[0].PAX_Type)}</h6>
            </div>
            <div class="col-3">
                <h6 class="fw-bold">${x.Fares[0].Refundable == true ? '<span style="color: #28a745 !important;">Refundable</span>':'<span class="text-danger">Non Refundable</span>'}</h6>
                <h6 class="fw-bold"> Class : ${x.Fares[0].FareDetails[0].FareClasses[0].Class_Code}</h6>
            </div>

            <div class="col-4">
            <h6>${getStop(x.Segments)}</h6>    
           <h6> ${x.Fares[0].Seats_Available || 0} Seats available</h6>
            </div>
            <div class="col-5" style="text-align: end;">
                <a data-toggle="collapse" href="#tripid1_${i}" role="button" aria-expanded="false"
                    aria-controls="collapseExample">View Flight Details</a>
            </div>
        </div>
        
    
        <div class="collapse" id="tripid1_${i}">
            <div class="card card-body">
                <ul class="nav nav-tabs" id="myTab-1" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="flight-tab" data-toggle="tab" href="#flight_details_${i}"
                            role="tab" aria-controls="flight" aria-selected="true">FLIGHT DETAILS</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="fare-tab" data-toggle="tab" href="#fare_details_${i}" role="tab"
                            aria-controls="fare" aria-selected="false">Fare Details</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="cancellation-tab" data-toggle="tab" href="#cancellation_${i}" role="tab"
                            aria-controls="contact" aria-selected="false">Cancellation</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" id="cancellation-tab" data-toggle="tab" href="#dateChange_${i}" role="tab"
                            aria-controls="contact" aria-selected="false">Reschedule Charges</a>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent-2">
                    <div class="tab-pane fade show active" id="flight_details_${i}" role="tabpanel"
                        aria-labelledby="home-tab">`
                    for (let flightDetails of x.Segments) {

                        content += `<div class="iq-card iq-card-block iq-card-stretch iq-card-height iq-mb-3">
                            <div class="card-body">
                                <h6 class="card-title">${flightDetails.Airline_Name} ${flightDetails.Airline_Code} |
                                    ${flightDetails.Flight_Number}</h6>
                                <div class="row">
                                    <div class="col-4">
                                        <h6 class="fw-bold">${flightDetails.Departure_DateTime.split(" ")[1]}</h6>
                                        
                                        <span class="card-text" style="font-size: 12px;"><span class="text-secondary">
                                                ${flightDetails.Origin_City}</span></span><br>
                                        <span class="card-text" style="font-size: 14px;">Terminal
                                            ${flightDetails.Origin_Terminal}</span>

                                    </div>

                                    <div class="col-3">
                                        
                                        <h6 class="fw-bold">${flightDetails.Duration.split(":")[0]}h
                                            ${flightDetails.Duration.split(":")[1]}m</h6>
                                    </div>
                                    <div class="col-4">
                                        <h6 class="fw-bold">${flightDetails.Arrival_DateTime.split(" ")[1]}</h6>
                                        <span class="card-text" style="font-size: 12px;"><span class="text-secondary">
                                                ${flightDetails?.Destination_City}</span></span><br>
                                        <span class="card-text" style="font-size: 14px;">Terminal
                                            ${flightDetails?.Destination_Terminal} </span>
                                    </div>
                                </div>
                            </div>
                        </div>`
                    }
                    content += `</div>
                                                        <div class="tab-pane fade" id="fare_details_${i}" role="tabpanel" aria-labelledby="profile-tab">
                                                            <div class="iq-card iq-card-block iq-card-stretch iq-card-height iq-mb-3">
                                                                <div class="card-body">
                                                                    <div class="row">
                                                                        <div class="col-3">Base Fare</div>
                                                                        <div class="col-3">₹ ${x?.Fares[0]?.FareDetails[0]?.Basic_Amount}</div>
                                                                    </div>

                                                                    <div class="row">
                                                                        <div class="col-3">Taxes and Fees </div>
                                                                        <div class="col-3">₹ ${x?.Fares[0]?.FareDetails[0]?.AirportTax_Amount}</div>
                                                                    </div>

                                                                    <div class="row font-weight-bold">
                                                                        <div class="col-3 text -bold">Total</div>
                                                                        <div class="col-3 text -bold">₹ ${x?.Fares[0]?.FareDetails[0]?.Total_Amount}</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="tab-pane fade" id="cancellation_${i}" role="tabpanel" aria-labelledby="contact-tab">
                                                            <div class="iq-card-body">
                                                                <div class="table-responsive">
                                                                    <table class="table">
                                                                        <thead>
                                                                            <tr>
                                                                                <th scope="col">Time frame</th>
                                                                                <th scope="col">Airline Fee</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>`;
                    if (x?.Fares[0]?.FareDetails[0]?.CancellationCharges) {

                        for (let canceldetails of x?.Fares[0]?.FareDetails[0]?.CancellationCharges) {

                            content += `<tr>
                                                                                <th scope="row">${canceldetails?.DurationFrom} ${canceldetails?.DurationTypeFrom =='0'?'Hours':canceldetails?.DurationTypeFrom =='1'?'Days':''} to
                                                                                    ${canceldetails?.DurationTo} ${canceldetails?.DurationTypeTo =='0'?'Hours':canceldetails?.DurationTypeTo =='1'?'Days':''}</th>
                                                                                <td>${getPessanger(canceldetails?.PassengerType)} : ₹
                                                                                    ${canceldetails?.Value}</td>
                                                                            </tr>`;
                        }
                    }
                    content += `</tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="tab-pane fade" id="dateChange_${i}" role="tabpanel" aria-labelledby="contact-tab">
                                                            <div class="iq-card-body">
                                                                <div class="table-responsive">
                                                                    <table class="table">
                                                                        <thead>
                                                                            <tr>
                                                                                <th scope="col">Time frame</th>
                                                                                <th scope="col">Airline Fee</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>`;
                    if (x?.Fares[0]?.FareDetails[0]?.RescheduleCharges) {
                        for (let dateChange of x?.Fares[0]?.FareDetails[0]?.RescheduleCharges) {

                            content += `<tr>
                                                                                <th scope="row">${dateChange?.DurationFrom} ${dateChange?.DurationTypeFrom =='0'?'Hours':dateChange?.DurationTypeFrom =='1'?'Days':''} to
                                                                                    ${dateChange?.DurationTo} ${dateChange?.DurationTypeTo =='0'?'Hours':dateChange?.DurationTypeTo =='1'?'Days':''}</th>
                                                                                <td>${getPessanger(dateChange?.PassengerType)} : ₹ ${dateChange?.Value}</td>
                                                                            </tr>`;
                        }
                    }
                    content += `</tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="collapse" id="collapseExample1_${i}">
                                                <div class="card card-body">
                                                
                                                <div class="iq-card iq-card-block iq-card-stretch iq-card-height iq-mb-3">
                                                                <div class="card-body">
                                                                <div class="table-responsive">
                                                                <table class="table mb-0">
                                                                    <thead class="thead-light">
                                                                        <tr>
                                                                        <th scope="col">FARES</th>
                                                                        <th scope="col">CABIN BAG</th>
                                                                        <th scope="col">CHECK-IN</th>
                                                                        
                                                                        <th scope="col"></th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>`
                    for (let fareDetails of x.Fares) {

                        content += ` <tr>
                                                                        <td>${fareDetails?.FareDetails[0]?.FareClasses[0]?.Class_Desc}</td>
                                                                        <td>${fareDetails?.FareDetails[0]?.Free_Baggage?.Hand_Baggage}</td>
                                                                        <td>${fareDetails?.FareDetails[0]?.Free_Baggage?.Check_In_Baggage}</td>
                                                                        
                                                                        <td> ₹ ${fareDetails?.FareDetails[0]?.Total_Amount}<br>
                                                                        <button class="btn btn-info">Book Now</button>
                                                                        </td>
                                                                        </tr>`
                    }
                    content += ` </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                            </div>
                                            </div>
                                        </div>
                                        </div>
                                        </div>
                                        </div>`
                    i++
                    returnFlightIndex++
                }

                content += ` </div>`


                content += ` </div>`;


                $(".preview-card-body").append(content)

            }
        }

    }

    function fliterflightSearch(type) {

        let bookingType = $("input[name='bookingType']:checked").val();

        $(".preview-card-body").children().remove();
        var selectedairline = []
        var selectedStop = []
        var selectedReturnStop = []

        $('.flight_checkbox').each(function() {
            if (this.checked == true) {
                selectedairline.push(this.value);
            }
        })

        $('.stop_checkbox').each(function() {
            if (this.checked == true) {
                selectedStop.push(parseInt(this.value));
            }
        })

        $('.returnstop_checkbox').each(function() {
            if (this.checked == true) {
                selectedReturnStop.push(parseInt(this.value));
            }
        })


        const rangeS = document.querySelectorAll('input[type="range"]')

        rangeS.forEach((el) => {
            el.oninput = () => {
                let slide1 = parseFloat(rangeS[0].value),
                    slide2 = parseFloat(rangeS[1].value);

                if (slide1 > slide2) {
                    [slide1, slide2] = [slide2, slide1];
                }
                priceRange1 = slide1;
                priceRange2 = slide2;
                $("#price1").html(`₹ ${slide1}`)
                $("#price2").html(`₹ ${slide2}`)
            }
        });

        const ReturnrangeS = document.querySelectorAll('input[name="returnpricerange"]')

        ReturnrangeS.forEach((el) => {
            el.oninput = () => {
                let slide1 = parseFloat(ReturnrangeS[0].value),
                    slide2 = parseFloat(ReturnrangeS[1].value);

                if (slide1 > slide2) {
                    [slide1, slide2] = [slide2, slide1];
                }
                returnpriceRange1 = slide1;
                returnpriceRange2 = slide2;
                $("#returnprice1").html(`₹ ${slide1}`)
                $("#returnprice2").html(`₹ ${slide2}`)
            }
        });

        if (bookingType === '0') {

            let filteredData = oneWayflightDetails.Flight[0].Flights.filter((item) => {
                const filteredAirlines = selectedairline.length === 0 || selectedairline.includes(item.Segments[0].Airline_Name)
                const filteredStop = selectedStop.length === 0 || selectedStop.includes(item.Segments.length)
                const filteredPrice = priceRange1 <= item.Fares[0].FareDetails[0].Total_Amount && priceRange2 >= item.Fares[0].FareDetails[0].Total_Amount

                return filteredAirlines && filteredStop && filteredPrice;
            });

            const data = {
                data: {
                    Flight: [{

                        Flights: filteredData
                    }]
                }
            }
            renderFlightList(data)

        }


        if (bookingType === '1') {

            let onwardfilteredData = roundTripFlightDetails[0].Flights.filter((item) => {
                const filteredAirlines = selectedairline.length === 0 || selectedairline.includes(item.Segments[0].Airline_Name)
                const filteredStop = selectedStop.length === 0 || selectedStop.includes(item.Segments.length)
                const filteredPrice = priceRange1 <= item.Fares[0].FareDetails[0].Total_Amount && priceRange2 >= item.Fares[0].FareDetails[0].Total_Amount

                return filteredAirlines && filteredStop && filteredPrice;
            });


            let returnfilteredData = roundTripFlightDetails[1].Flights.filter((item) => {
                const filteredAirlines = selectedairline.length === 0 || selectedairline.includes(item.Segments[0].Airline_Name)
                const filteredStop = selectedReturnStop.length === 0 || selectedReturnStop.includes(item.Segments.length)
                const filteredPrice = returnpriceRange1 <= item.Fares[0].FareDetails[0].Total_Amount && returnpriceRange2 >= item.Fares[0].FareDetails[0].Total_Amount

                return filteredAirlines && filteredStop && filteredPrice;
            });


            const data = {
                data: {
                    Flight: [{

                            Flights: onwardfilteredData
                        },
                        {

                            Flights: returnfilteredData
                        }
                    ]
                }
            }

            renderFlightList(data)

        }


    }


    function notify(text, status) {
        new Notify({
            status: status,
            title: null,
            text: text,
            effect: 'fade',
            customClass: null,
            customIcon: null,
            showIcon: true,
            showCloseButton: true,
            autoclose: true,
            autotimeout: 2000,
            gap: 20,
            distance: 15,
            type: 1,
            position: 'right top'
        })
    }

    function bookTicket(row, selectedFare) {

        let searchKey = oneWayflightDetails.Search_Key;
        let flightKey = filterOneWayFlight.data.Flight[0].Flights[row].Flight_Key;
        let fareId = filterOneWayFlight.data.Flight[0].Flights[row].Fares[selectedFare].Fare_Id
        let fareDetails = filterOneWayFlight.data.Flight[0].Flights[row].Fares[selectedFare]
        let requestId = oneWayflightDetails.Request_Id
        let bookingType = $("input[name='bookingType']:checked").val();
        let adultCount = parseInt($('#adult').html() || 0);
        let childCount = parseInt($('#children').html() || 0);
        let infantCount = parseInt($('#infant').html() || 0);
        let adultBasicAmount = 0;
        let childBasicAmount = 0;
        let infantBasicAmount = 0;
        let totalAdultFlightAmount = 0;
        let totalChildFlightAmount = 0;
        let totalInfantFlightAmount = 0;
        let adultTax = 0;
        let childTax = 0;
        let infantTax = 0;

        $.ajax({
            url: "{{ url('getflight') }}",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                type: 'reprice',
                searchKey: searchKey,
                flightSearch: [{
                    fareId: fareId,
                    flightKey: flightKey,
                }],
                requestId: requestId
            },

            beforeSend: function() {
                swal({
                    title: 'Wait!',
                    text: 'Please wait, we are fetching  details',
                    onOpen: () => {
                        swal.showLoading()
                    },
                    allowOutsideClick: () => !swal.isLoading()
                });
            },

            success: function(data) {
                swal.close()
                if (data.statuscode == "TXN" && data.data.Flight) {
                    for (let basicAmount of data.data.Flight[0].Flight.Fares[0].FareDetails) {
                        if (basicAmount.PAX_Type === 0) {
                            adultBasicAmount = basicAmount.Basic_Amount
                            totalAdultFlightAmount = basicAmount.Total_Amount
                            adultTax = basicAmount.AirportTax_Amount
                        }
                        if (basicAmount.PAX_Type === 1) {
                            childBasicAmount = basicAmount.Basic_Amount
                            totalChildFlightAmount = basicAmount.Total_Amount
                            childTax = basicAmount.AirportTax_Amount
                        }
                        if (basicAmount.PAX_Type === 2) {
                            infantBasicAmount = basicAmount.Basic_Amount
                            totalInfantFlightAmount = basicAmount.Total_Amount
                            infantTax = basicAmount.AirportTax_Amount
                        }
                    }
                    let selectedFlightDetails = {
                        searchKey: searchKey,
                        adultCount: adultCount,
                        childCount: childCount,
                        infantCount: infantCount,
                        flightKey: data.data.Flight[0].Flight.Flight_Key,
                        fareId: data.data.Flight[0].Flight.Fares[0].Fare_Id,
                        adultFlightAmount: adultBasicAmount,
                        childFlightAmount: childBasicAmount,
                        infantFlightAmount: infantBasicAmount,
                        baseAmount: adultCount * adultBasicAmount + childCount * childBasicAmount + infantCount * infantBasicAmount,
                        tax: adultCount * adultTax + childCount * childTax + infantCount * infantTax,
                        repriceData: data,
                        travelClass: $("input[name='travelClass']:checked").val(),
                        travelType: $("input[name='travelType']:checked").val(),
                        bookingType: $("input[name='bookingType']:checked").val(),
                        total_amount: adultCount * totalAdultFlightAmount + childCount * totalChildFlightAmount + infantCount * totalInfantFlightAmount,
                        flightId: data.data.Flight[0].Flight.Flight_Id,
                        Request_Id: data.data.Request_Id,
                        airlineLogo: airlineLogo,
                        travelInfo: [{
                            "Origin": $('#from').val(),
                            "Destination": $('#to').val(),
                            "TravelDate": $('#departure').val(),
                            "Trip_Id": 0
                        }]
                    }
                    localStorage.setItem("selectedFlightDetails", JSON.stringify(selectedFlightDetails));

                    if (fareDetails.FareDetails[0].Total_Amount === data.data.Flight[0].Flight.Fares[0].FareDetails[0].Total_Amount) {
                        window.open(`{{url('/bookflight')}}`, "_self");
                    } else {

                        Swal.fire({
                            title: 'Are you sure?',
                            text: `Selected Flight Booking Amount has been updated, Now the Current Amount is : ${totalAdultFlightAmount}`,
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.open(`{{url('/bookflight')}}`, "_self")
                            }
                        })
                    }
                } else {
                    notify(data.message, "error")
                }
            }
        })
    }

    function bookRoundTripTicket(row, selectedFare) {

        let searchKey = roundTripSearchKey.Search_Key;
        let requestId = roundTripSearchKey.Request_Id
        let adultCount = parseInt($('#adult').html() || 0);
        let childCount = parseInt($('#children').html() || 0);
        let infantCount = parseInt($('#infant').html() || 0)
        let singleFlightAmount;
        let total_passanger = adultCount + childCount + infantCount;
        let adultBasicAmount = 0;
        let childBasicAmount = 0;
        let infantBasicAmount = 0;
        let totalAdultFlightAmount = 0;
        let totalChildFlightAmount = 0;
        let totalInfantFlightAmount = 0;
        let adultTax = 0;
        let childTax = 0;
        let infantTax = 0;
        let returnAdultBasicAmount = 0;
        let returnChildBasicAmount = 0;
        let returnInfantBasicAmount = 0;
        let returnTotalAdultFlightAmount = 0;
        let returnTotalChildFlightAmount = 0;
        let returnTotalInfantFlightAmount = 0;
        let returnAdultTax = 0;
        let returnChildTax = 0;
        let returnInfantTax = 0;
        let onGoingbaseAmount;
        let returnBaseAmount;
        let onGoingbasetax;
        let returntax;
        let onGoingtotal_amount;
        let returntotal_amount;
        let adultFlightAmount;
        let childFlightAmount;
        let infantFlightAmount;
        let adultTotal_amount;
        let childTotal_amount;
        let InfantTotal_amount;


        $.ajax({
            url: "{{ url('getflight') }}",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },

            data: {
                type: 'reprice',
                searchKey: searchKey,
                flightSearch: [{
                        fareId: firstArrayRoundTripFare.Fare_Id,
                        flightKey: firstArrayRoundTrip.Flight_Key,
                    },
                    {
                        fareId: secondArrayRoundTripFare.Fare_Id,
                        flightKey: secondArrayRoundTrip.Flight_Key,
                    }
                ],


                requestId: requestId
            },
            beforeSend: function() {
                swal({
                    title: 'Wait!',
                    text: 'Please wait, we are fetching  details',
                    onOpen: () => {
                        swal.showLoading()
                    },
                    allowOutsideClick: () => !swal.isLoading()
                });
            },


            success: function(data) {
                swal.close();

                if (data.statuscode && data.data.Flight) {

                    for (let basicAmount of data.data.Flight[0].Flight.Fares[0].FareDetails) {

                        if (basicAmount.PAX_Type === 0) {
                            adultBasicAmount = basicAmount.Basic_Amount
                            totalAdultFlightAmount = basicAmount.Total_Amount
                            adultTax = basicAmount.AirportTax_Amount
                        }
                        if (basicAmount.PAX_Type === 1) {
                            childBasicAmount = basicAmount.Basic_Amount
                            totalChildFlightAmount = basicAmount.Total_Amount
                            childTax = basicAmount.AirportTax_Amount
                        }
                        if (basicAmount.PAX_Type === 2) {
                            infantBasicAmount = basicAmount.Basic_Amount
                            totalInfantFlightAmount = basicAmount.Total_Amount
                            infantTax = basicAmount.AirportTax_Amount
                        }
                    }

                    for (let returnBasicAmount of data.data.Flight[1].Flight.Fares[0].FareDetails) {

                        if (returnBasicAmount.PAX_Type === 0) {
                            returnAdultBasicAmount = returnBasicAmount.Basic_Amount
                            returnTotalAdultFlightAmount = returnBasicAmount.Total_Amount
                            returnAdultTax = returnBasicAmount.AirportTax_Amount
                        }
                        if (returnBasicAmount.PAX_Type === 1) {
                            returnChildBasicAmount = returnBasicAmount.Basic_Amount
                            returnTotalChildFlightAmount = returnBasicAmount.Total_Amount
                            returnChildTax = returnBasicAmount.AirportTax_Amount
                        }
                        if (returnBasicAmount.PAX_Type === 2) {
                            returnInfantBasicAmount = returnBasicAmount.Basic_Amount
                            returnTotalInfantFlightAmount = returnBasicAmount.Total_Amount
                            returnInfantTax = returnBasicAmount.AirportTax_Amount
                        }
                    }

                    onGoingbaseAmount = adultBasicAmount + childBasicAmount + infantBasicAmount;
                    returnBaseAmount = returnAdultBasicAmount + returnChildBasicAmount + returnInfantBasicAmount
                    onGoingbasetax = adultTax + childTax + infantTax
                    returntax = returnAdultTax + returnChildTax + returnInfantTax
                    adultTotal_amount = totalAdultFlightAmount + returnTotalAdultFlightAmount;
                    childTotal_amount = totalChildFlightAmount + returnTotalChildFlightAmount;
                    InfantTotal_amount = totalInfantFlightAmount + returnTotalInfantFlightAmount
                    adultFlightAmount = adultBasicAmount + returnAdultBasicAmount
                    childFlightAmount = childBasicAmount + returnChildBasicAmount
                    infantFlightAmount = infantBasicAmount + returnInfantBasicAmount

                    let selectedFlightDetails = {
                        searchKey: searchKey,
                        flightKey: data.data.Flight[0].Flight.Flight_Key,
                        returnflightKey: data.data.Flight[1].Flight.Flight_Key,
                        fareId: data.data.Flight[0].Flight.Fares[0].Fare_Id,
                        returnfareId: data.data.Flight[1].Flight.Fares[0].Fare_Id,
                        onGoingbaseAmount: onGoingbaseAmount,
                        returnBaseAmount: returnBaseAmount,
                        adultFlightAmount: adultFlightAmount,
                        childFlightAmount: childFlightAmount,
                        infantFlightAmount: infantFlightAmount,
                        onGoingbasetax: onGoingbasetax,
                        returntax: returntax,
                        baseAmount: adultCount * adultFlightAmount + childCount * childFlightAmount + infantCount * infantFlightAmount,
                        tax: adultCount * (adultTax + returnAdultTax) + childCount * (childTax + returnChildTax) + infantCount * (infantTax + returnInfantTax),
                        flightId: data.data.Flight[0].Flight.Flight_Id,
                        returnflightId: data.data.Flight[1].Flight.Flight_Id,
                        repriceData: data,
                        adultCount: parseInt($('#adult').html() || 0),
                        childCount: parseInt($('#children').html() || 0),
                        infantCount: parseInt($('#infant').html() || 0),
                        travelClass: $("input[name='travelClass']:checked").val(),
                        travelType: $("input[name='travelType']:checked").val(),
                        bookingType: $("input[name='bookingType']:checked").val(),
                        onGoingtotal_amount: onGoingtotal_amount,
                        returntotal_amount: returntotal_amount,
                        total_amount: adultCount * adultTotal_amount + childCount * childTotal_amount + infantCount * InfantTotal_amount,
                        airlineLogo: airlineLogo,
                        Request_Id: data.data.Request_Id,
                        travelInfo: [{
                                "Origin": $('#from').val(),
                                "Destination": $('#to').val(),
                                "TravelDate": $('#departure').val(),
                                "Trip_Id": 0
                            },
                            {
                                "Origin": $('#to').val(),
                                "Destination": $('#from').val(),
                                "TravelDate": $('#return1').val(),
                                "Trip_Id": 1
                            }
                        ]
                    }

                    localStorage.setItem("selectedFlightDetails", JSON.stringify(selectedFlightDetails));

                    if (firstArrayRoundTripFare.FareDetails[0].Total_Amount === data.data.Flight[0].Flight.Fares[0].FareDetails[0].Total_Amount && secondArrayRoundTripFare.FareDetails[0].Total_Amount === data.data.Flight[1].Flight.Fares[0].FareDetails[0].Total_Amount) {
                        window.open(`{{url('/bookflight')}}`, "_self");
                    } else {

                        Swal.fire({
                            title: 'Are you sure?',
                            text: `
                            ${ firstArrayRoundTripFare.FareDetails[0].Total_Amount !== data.data.Flight[0].Flight.Fares[0].FareDetails[0].Total_Amount && `Selected Departure flight Booking Amount has been updated Now the Current Amount is : ${data.data.Flight[0].Flight.Fares[0].FareDetails[0].Total_Amount}`}
                            ${ secondArrayRoundTripFare.FareDetails[0].Total_Amount !== data.data.Flight[1].Flight.Fares[0].FareDetails[0].Total_Amount && `Selected Return Flight Booking Amount has been updated Now the Current Amount is : ${data.data.Flight[1].Flight.Fares[0].FareDetails[0].Total_Amount}`}
                            `,
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                Swal.fire(
                                    window.open(`{{url('/bookflight')}}`, "_self")
                                )
                            }
                        })
                    }

                } else {
                    notify(data.message, "error")
                }
            }
        })
    }


    function getTripid(index, type) {

        if (type == 'ongoing') {
            firstArrayRoundTrip = roundTripOngoing.Flights[index]
        }

        if (type == 'return') {
            secondArrayRoundTrip = roundTripReturn.Flights[index]
        }
        showRoundTripSelectedFlight()
    }

    function getRoundTripFare(index, type) {

        if (type == 'ongoing') {
            firstArrayRoundTripFare = firstArrayRoundTrip.Fares[index]
        }

        if (type == 'return') {
            secondArrayRoundTripFare = secondArrayRoundTrip.Fares[index]
        }


        if (firstArrayRoundTripFare && secondArrayRoundTripFare) {
            $('#nextbutton').attr('disabled', false).addClass('btn-primary').removeClass('btn-secondary');
            $('#totalBaseFare').html(`Total Round Trip Base Fare for 1 Traveller :  <b style="font-size:18px;"> ₹ ${firstArrayRoundTripFare.FareDetails[0].Total_Amount + secondArrayRoundTripFare.FareDetails[0].Total_Amount } </b>`)
        }

    }

    function activeTab(targetEle) {

        $('.class_for_remove').removeClass('show active');
        $(`#${targetEle}`).addClass('show active');
    }

    function viewRoundTripFare() {
        var content = `
<ul class="nav nav-tabs" id="myTab-1" role="tablist">
   <li class="nav-item">
      <a onclick="activeTab('departure1')" class="nav-link active" id="home-tab" data-toggle="tab" href="#departure1" role="tab" aria-controls="home" aria-selected="true">Departure</a>
   </li>
   <li class="nav-item">
      <a onclick="activeTab('roundTripreturn')" class="nav-link" id="profile-tab" data-toggle="tab" href="#roundTripreturn" role="tab" aria-controls="profile" aria-selected="false">Return</a>
   </li>
</ul>
<div class="tab-content" id="myTabContent-2">
   <div class="tab-pane class_for_remove fade show active" role="tabpanel" id="departure1" aria-labelledby="home-tab">
      <div class="table-responsive">
         <table class="table mb-0 text-center">
            <thead class="thead-light">
               <tr>
                  <th scope="col">FARES</th>
                  <th scope="col">CABIN BAG</th>
                  <th scope="col">CHECK-IN</th>
                  <th scope="col">Seats Available</th>
                  <th scope="col">Fare</th>
               </tr>
            </thead>
            <tbody>
               `
        let roundTripdeparture = 0;
        for (let fareDetails of firstArrayRoundTrip.Fares) {
            content += ` 
                <tr >
                  <td>
                  <div class="badge badge-pill badge-success"><b>${fareDetails?.FareDetails[0]?.FareClasses[0]?.Class_Desc}</b></div>
                  </td>
                  <td>${fareDetails?.FareDetails[0]?.Free_Baggage?.Hand_Baggage}</td>
                  <td>${fareDetails?.FareDetails[0]?.Free_Baggage?.Check_In_Baggage}</td>
                  <td>${fareDetails?.Seats_Available || 0} Seats</td>
                  <td> 
                  <div class="custom-control custom-radio custom-radio-color custom-control-inline" style="margin-left:12px;">
                <input type="radio" id="customRadio08_${roundTripdeparture}" name="roundTripdeparture" class="custom-control-input bg-primary" onclick="getRoundTripFare(${roundTripdeparture},'ongoing')" >
                <label class="custom-control-label" for="customRadio08_${roundTripdeparture}">  </label>
                </div><br/>
                <b style="margin-right:12px;">₹ ${fareDetails?.FareDetails[0]?.Total_Amount}</b>
                </td>
               </tr>
               `
            roundTripdeparture++;
        }
        content += ` 
            </tbody>
         </table>
      </div>
   </div>
   <div class="tab-pane class_for_remove fade" id="roundTripreturn" role="tabpanel" aria-labelledby="profile-tab">
     <div class="table-responsive">
         <table class="table mb-0 text-center">
            <thead class="thead-light">
               <tr>
                  <th scope="col">FARES</th>
                  <th scope="col">CABIN BAG</th>
                  <th scope="col">CHECK-IN</th>
                  <th scope="col">Seats Available</th>
                  <th scope="col">Fare</th>
               </tr>
            </thead>
            <tbody>
               `
        let returnRoundTripFare = 0;
        for (let fareDetails of secondArrayRoundTrip.Fares) {
            content += ` 
               <tr class="text-center">
                  <td>
                  <div class="badge badge-pill badge-success"><b>${fareDetails?.FareDetails[0]?.FareClasses[0]?.Class_Desc}</b></div></td>
                  <td>${fareDetails?.FareDetails[0]?.Free_Baggage?.Hand_Baggage}</td>
                  <td>${fareDetails?.FareDetails[0]?.Free_Baggage?.Check_In_Baggage}</td>
                  <td>${fareDetails?.Seats_Available || 0} Seats</td>
                  <td>                   
                     <div class="custom-control custom-radio custom-radio-color custom-control-inline" style="margin-left:12px;">
                <input type="radio" id="customRadio07_${returnRoundTripFare}" name="returnRoundTripFare" class="custom-control-input bg-primary" onclick="getRoundTripFare(${returnRoundTripFare}, 'return')" >
                <label class="custom-control-label" for="customRadio07_${returnRoundTripFare}">  </label>
                </div><br/>
                <b style="margin-right:12px;">₹ ${fareDetails?.FareDetails[0]?.Total_Amount}</b>
                  </td>
               </tr>
               `
            returnRoundTripFare++;
        }
        content += ` 
            </tbody>
         </table>
      </div>
   </div>
</div>
`
        $("#departureRoundTrip").html(content)
        $("#roundtripViewPice").modal('show');
    }

    function showRoundTripSelectedFlight() {

        $(".rondtripDiv").children().remove();

        if (firstArrayRoundTrip && secondArrayRoundTrip) {

            var content = `
                <div class="container bg-dark text-white "rounded">
            <div class="row pt-4">
                <div class="col-5">
                <div class="iq-card iq-card-block iq-card-stretch iq-card-height">
        <div class="card-body">
                    <div class="row">
                        <div class="col-3">
                            <span class="card-text" style="font-size: 12px;"><b style="color:black;">${firstArrayRoundTrip.Segments[0].Origin}</b><span
                                    class="text-secondary"> ${firstArrayRoundTrip.Segments[0].Origin_City}</span></span><br>
                            <h5 class="fw-bold">${firstArrayRoundTrip.Segments[0].Departure_DateTime.split(" ")[1]}</h5>
                        </div>

                        <div class="col-3">
                            <span class="card-text" style="font-size: 12px;"><b style="color:black;">Duration <span class="text-danger">
                                        </span></b></span><br>
                            <h5 class="fw-bold">${firstArrayRoundTrip.Segments[0].Duration.split(":")[0]}h
                                ${firstArrayRoundTrip.Segments[0].Duration.split(":")[1]}m </h5>
                        </div>
                        <div class="col-3">
                            <span class="card-text"
                                style="font-size: 12px;"><b style="color:black;">${firstArrayRoundTrip.Segments[firstArrayRoundTrip.Segments.length-1].Destination}</b><span
                                    class="text-secondary">
                                    ${firstArrayRoundTrip.Segments[firstArrayRoundTrip.Segments.length-1].Destination_City}</span></span><br>
                            <h5 class="fw-bold">${firstArrayRoundTrip.Segments[firstArrayRoundTrip.Segments.length-1].Arrival_DateTime.split(" ")[1]}</h5>
                        </div>

                        <div class="col-3">
                            <h4 class="fw-bold"> ₹ ${firstArrayRoundTrip.Fares[0].FareDetails[0].Total_Amount}</h4>
                        </div>
                        <div class="col-5">
                            <h6>${getStop(firstArrayRoundTrip.Segments)}</h6>
                        </div>
                    </div>
                </div>
                </div>
                </div>
                <div class="col-1 text-white text-center" style="max-width:4rem !important;">
                   <i class="ri-arrow-right-line" style="font-size:30px"></i>
                   <i class="ri-arrow-left-line" style="font-size:30px"></i>
                </div>
                <div class="col-5">
                <div class="iq-card iq-card-block iq-card-stretch iq-card-height">
        <div class="card-body">
                    <div class="row">
                        <div class="col-3">
                            <span class="card-text" style="font-size: 12px;"><b style="color:black;">${secondArrayRoundTrip.Segments[0].Origin}</b><span
                                    class="text-secondary"> ${secondArrayRoundTrip.Segments[0].Origin_City}</span></span><br>
                            <h5 class="fw-bold">${secondArrayRoundTrip.Segments[0].Departure_DateTime.split(" ")[1]}</h5>
                        </div>

                        <div class="col-3">
                            <span class="card-text" style="font-size: 12px;"><b style="color:black;">Duration <span class="text-danger">
                                        </span></b></span><br>
                            <h5 class="fw-bold">${secondArrayRoundTrip.Segments[0].Duration.split(":")[0]}h
                                ${secondArrayRoundTrip.Segments[0].Duration.split(":")[1]}m </h5>
                        </div>
                        <div class="col-3">
                            <span class="card-text"
                                style="font-size: 12px;"><b style="color:black;">${secondArrayRoundTrip.Segments[secondArrayRoundTrip.Segments.length-1].Destination}</b><span
                                    class="text-secondary">
                                    ${secondArrayRoundTrip.Segments[secondArrayRoundTrip.Segments.length-1].Destination_City}</span></span><br>
                            <h5 class="fw-bold">${secondArrayRoundTrip.Segments[secondArrayRoundTrip.Segments.length-1].Arrival_DateTime.split(" ")[1]}</h5>
                        </div>


                        <div class="col-3">
                            <h4 class="fw-bold"> ₹ ${secondArrayRoundTrip.Fares[0].FareDetails[0].Total_Amount}</h4>
                        </div>
                        <div class="col-5">
                            <h6>${getStop(secondArrayRoundTrip.Segments)}</h6>
                        </div>
                    
                    </div>
                    </div>
                    </div>
                </div>
                <div class="col-1 text-center mt-3 p-0" style="margin-left:12px !important;">
                <h4 class="fw-bold text-white"> ₹ ${firstArrayRoundTrip.Fares[0].FareDetails[0].Total_Amount + secondArrayRoundTrip.Fares[0].FareDetails[0].Total_Amount}</h4>
                
                <button class="btn btn-primary mt-1 shadow-sm" style="background-color: #dd7b55;color: #fff;" id="button1" onclick="viewRoundTripFare()">Book Now</button>
               
                </div>
            </div>
            </div>`

            $(".rondtripDiv").append(content)
        }
    }

    function getDateDiff(to, from) {
        const date1 = new Date(to);
        const date2 = new Date(from);
        const diffTime = Math.abs(date2 - date1);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        return diffDays;
    }

    function diff_hours(item) {
        if (item.length < 2) {
            return `${item[0].Duration.split(":")[0]}h ${item[0].Duration.split(":")[1]}m`
        } else {
            dt1 = new Date(item[0].Departure_DateTime);
            dt2 = new Date(item[1].Arrival_DateTime);

            var diff = (dt2.getTime() - dt1.getTime()) / 1000;
            diff /= (60 * 60);
            let hoursdiff = Math.abs(Math.round(diff));
            return `${hoursdiff}h`
        }


    }

    function getStop(data) {
        if (data.length == '1' && (data.Stop_Over == null || data.Stop_Over == [''])) {
            return '<span class="text-success">Non-Stop</span>'
        }

        if (data.length == '2') {

            return `<span class="text-danger">1-change</span><br>
                <span> Via ${data[data.length-1].Origin_City}</span>
                `
        }

    }

    function handleChange() {
        let bookingType = $("input[name='bookingType']:checked").val();

        if (bookingType == '1' || bookingType == '2') {
            $("#returnDateshow").css("display", "block")
        } else {
            $("#returnDateshow").css("display", "none")
        }

    }


    function searchFlight() {

        $(".preview-card-body").children().remove();
        $(".preview-card-side").children().remove();
        $(".rondtripDiv").children().remove();
        let bookingType = $("input[name='bookingType']:checked").val();
        let from = $('#from').val()
        let to = $('#to').val()
        let travel_class = $("input[name='travelClass']:checked").val();
        let travel_type = $("input[name='travelType']:checked").val();
        let departure = $('#departure').val()
        let return1 = $('#return1').val()
        let adult = $('#adult').html()
        let children = $('#children').html()
        let infant = $('#infant').html()

        let tripInfo = [];
        if (bookingType == '0') {
            tripInfo = [{
                origin: from,
                destination: to,
                travelDate: departure,
                tripId: 0
            }]
        }

        if (bookingType == '1') {
            tripInfo = [{
                    origin: from,
                    destination: to,
                    travelDate: departure,
                    tripId: 0
                },
                {
                    origin: to,
                    destination: from,
                    travelDate: return1,
                    tripId: 1
                }
            ]
        }


        $.ajax({
            url: "{{ url('getflight') }}",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                type: 'fetch',
                travelInfo: tripInfo,
                adultCount: adult,
                childCount: children,
                infantCount: infant,
                travelClass: travel_class,
                airlinefilter: '',
                inventoryType: '0',
                travelType: travel_type,
                bookingType: bookingType,
            },

            beforeSend: function() {
                let skelet = '';
                skelet += `
                     <div class="card">
                            <div class="card-body">
                                <h2 class="card-title skeleton">
                                </h2>
                                <p class="card-intro skeleton">
                                </p>
                                <h2 class="card-title skeleton">
                                </h2>
                            </div>
                        </div>`;
                $('.preview-card-side').html(skelet);
                $('.preview-card-body').html(skelet);

                // swal({
                //     title: 'Wait!',
                //     text: 'Please wait, we are fetching  details',
                //     onOpen: () => {
                //         swal.showLoading()
                //     },
                //     allowOutsideClick: () => !swal.isLoading()
                // });
            },

            success: function(data) {
                swal.close();
                $('.preview-card-side').html('');
                $('.preview-card-body').html('');
                let i = 0;

                if (data.statuscode == "TXN" && data?.data?.Flight) {
                    renderFlightList(data)
                    if (data.statuscode == "TXN") {
                        if (bookingType === '0') {
                            oneWayflightDetails = data.data

                            var sidemenu = `<div class="iq-card border">
                        <div class="iq-card-header p-2">
                            <div class="row p-2">
                                <div class="col-sm-6 my-1">
                                <h5 class="text-start fw-bold text-primary">Filters</h5>
                                </div>
                                <div class="col-6 my-1 text-right">
                                    <small>Reset all</small>
                                </div>
                              
                            </div>
                        </div>
                        <div class="iq-card-header p-2">
                            <div class="row p-2">
                                <div class="col-sm-12 ">
                                    <h6 class="text-start text-primary">Departure</h6>
                                </div>
                            </div>
                            <div class="row p-2">
                                <div class="col-6 my-1">
                                    <button type="button" class="btn btn-light w-100"> Before 6AM</button>
                                </div>
                                <div class="col-6 my-1">
                                    <button type="button" class="btn btn-light w-100">6AM - 12PM</button>
                                </div>
                                <div class="col-6 my-1">
                                    <button type="button" class="btn btn-light w-100">12AM - 6PM</button>
                                </div>
                                <div class="col-6 my-1">
                                    <button type="button" class="btn btn-light w-100">After 6PM</button>
                                </div>
                            </div>
                        </div>
                        <div class="iq-card-header p-2">
                            <div class="row p-2">
                                <div class="col-sm-12 ">
                                    <h6 class="text-start text-primary fw-bold">Stops</h6>
                                </div>
                            </div>
                            <div class="row p-2">
                                <div class="col-12 my-1">
                                    <div class="custom-control custom-checkbox custom-control-inline">
                                        <input type="checkbox" class="custom-control-input stop_checkbox" id="direct" name="direct" value="1" onclick="fliterflightSearch('checkStop')">
                                        <label class="custom-control-label" for="direct">Direct</label>
                                    </div>
                                </div>
                                <div class="col-12 my-1">
                                    <div class="custom-control custom-checkbox custom-control-inline">
                                        <input type="checkbox" class="custom-control-input stop_checkbox" id="stop_1" name="stop_1" value="2" onclick="fliterflightSearch('checkStop')">
                                        <label class="custom-control-label" for="stop_1">1 Stop</label>
                                    </div>
                                </div>
                                <div class="col-12 my-1">
                                    <div class="custom-control custom-checkbox custom-control-inline">
                                        <input type="checkbox" class="custom-control-input stop_checkbox" id="stop_2" name="stop_2" value="3" onclick="fliterflightSearch('checkStop')">
                                        <label class="custom-control-label" for="stop_2">2+ Stop</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="iq-card-header p-2">
                            <div class="row p-2">
                                <div class="col-sm-6 my-1">
                                    <h6 class="text-start text-primary fw-bold">Price</h6>
                                </div>
                                <div class="col-6 my-1 text-right">
                                    <small>Reset</small>
                                </div>
                                <div class="col-12">`

                            let uniquePrice = Array.from(new Set(airlinePriceDetails))
                            priceRange1 = Math.min(...uniquePrice);
                            priceRange2 = Math.max(...uniquePrice);

                            sidemenu += `
                            <div class="row">
                            <div class="col-8"> <span id="price1">₹ ${priceRange1} </span> </div>
                            <div class="col-4">  <span id="price2">₹ ${priceRange2} </span></div>
                            </div>
                            <div class="range-slider">
                                    <input value="${priceRange1}" min="${priceRange1}" max="${priceRange2}" step="1" type="range" onclick="fliterflightSearch('pricerange')">
                                    <input value="${priceRange2}" min="${priceRange1}" max="${priceRange2}" step="1" type="range" onclick="fliterflightSearch('pricerange')">
                                 
                                </div>`
                            sidemenu += `</div>

                            </div>
                        </div>
                     
                        <div class="iq-card-header p-2">
                            <div class="row p-2">
                                <div class="col-sm-6 my-1">
                                    <h6 class="text-start text-primary fw-bold">Preferred Airlines
                                    </h6>
                                </div>
                                <div class="col-6 my-1 text-right">
                                    <small>Reset</small>
                                </div>`
                            let showairline = Array.from(new Set(airlinesDetails))
                            for (let airline of showairline) {
                                sidemenu += `<div class="col-12 my-1">
                                    <div class="custom-control custom-checkbox custom-control-inline">
                                        <input type="checkbox" class="custom-control-input flight_checkbox" id="${airline}" name="${airline}" value="${airline}" onclick="fliterflightSearch('airlineSearch')">
                                       
                                        <label class="custom-control-label" for="${airline}">${airline}</label>
                                    </div>
                                </div>`
                            }

                            sidemenu += `</div>
                        </div>

                       
                    </div>`;

                            $(".preview-card-side").html(sidemenu);


                        }

                        if (bookingType === '1') {
                            if (data?.data?.Flight) {
                                let days = getDateDiff();
                                let daysFormated = '';
                                if (days > 0) {
                                    daysFormated = `(+ ${days} Days)`
                                }

                                roundTripFlightDetails = data.data.Flight;
                                roundTripSearchKey = data.data;

                                var sidemenu = `<div class="iq-card border">
                        <div class="iq-card-header p-2">
                            <div class="row p-2">
                                <div class="col-sm-6 my-1">
                                    <h6 class="text-start">Filters</h6>
                                  
                                </div>
                                <div class="col-6 my-1 text-right">
                                    <small>Reset all</small>
                                </div>
                              
                            </div>
                        </div>
                        <div class="iq-card-header p-2">
                            <div class="row p-2">
                                <div class="col-sm-12 ">
                                    <h6 class="text-start">Departure</h6>
                                </div>
                            </div>
                            <div class="row p-2">
                                <div class="col-6 my-1">
                                    <button type="button" class="btn btn-light w-100"> Before 6AM</button>
                                </div>
                                <div class="col-6 my-1">
                                    <button type="button" class="btn btn-light w-100">6AM - 12PM</button>
                                </div>
                                <div class="col-6 my-1">
                                    <button type="button" class="btn btn-light w-100">12AM - 6PM</button>
                                </div>
                                <div class="col-6 my-1">
                                    <button type="button" class="btn btn-light w-100">After 6PM</button>
                                </div>
                            </div>
                        </div>
                        <div class="iq-card-header p-2">
                            <div class="row p-2">
                                <div class="col-sm-12 ">
                                    <h6 class="text-start">Stops</h6>
                                </div>
                            </div>
                            <div class="row p-2">
                                <div class="col-12 my-1">
                                    <div class="custom-control custom-checkbox custom-control-inline">
                                        <input type="checkbox" class="custom-control-input stop_checkbox" id="direct" name="direct" value="1" onclick="fliterflightSearch('checkStop')">
                                        <label class="custom-control-label" for="direct">Direct</label>
                                    </div>
                                </div>
                                <div class="col-12 my-1">
                                    <div class="custom-control custom-checkbox custom-control-inline">
                                        <input type="checkbox" class="custom-control-input stop_checkbox" id="stop_1" name="stop_1" value="2" onclick="fliterflightSearch('checkStop')">
                                        <label class="custom-control-label" for="stop_1">1 Stop</label>
                                    </div>
                                </div>
                                <div class="col-12 my-1">
                                    <div class="custom-control custom-checkbox custom-control-inline">
                                        <input type="checkbox" class="custom-control-input stop_checkbox" id="stop_2" name="stop_2" value="3" onclick="fliterflightSearch('checkStop')">
                                        <label class="custom-control-label" for="stop_2">2+ Stop</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="iq-card-header p-2">
                            <div class="row p-2">
                                <div class="col-sm-12 ">
                                    <h6 class="text-start">Return Stops</h6>
                                </div>
                            </div>
                            <div class="row p-2">
                              

                                <div class="col-12 my-1">
                                    <div class="custom-control custom-checkbox custom-control-inline">
                                        <input type="checkbox" class="custom-control-input returnstop_checkbox" id="return_direct" name="direct" value="1" onclick="fliterflightSearch('checkStop')">
                                        <label class="custom-control-label" for="return_direct">Direct</label>
                                    </div>
                                </div>
                                <div class="col-12 my-1">
                                    <div class="custom-control custom-checkbox custom-control-inline">
                                        <input type="checkbox" class="custom-control-input returnstop_checkbox" id="return_stop_1" name="stop_1" value="2" onclick="fliterflightSearch('checkStop')">
                                        <label class="custom-control-label" for="return_stop_1">1 Stop</label>
                                    </div>
                                </div>
                                <div class="col-12 my-1">
                                    <div class="custom-control custom-checkbox custom-control-inline">
                                        <input type="checkbox" class="custom-control-input returnstop_checkbox" id="return_stop_2" name="stop_2" value="3" onclick="fliterflightSearch('checkStop')">
                                        <label class="custom-control-label" for="return_stop_2">2+ Stop</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="iq-card-header p-2">
                            <div class="row p-2">
                                <div class="col-sm-6 my-1">
                                    <h6 class="text-start">Price</h6>
                                </div>
                                <div class="col-6 my-1 text-right">
                                    <small>Reset</small>
                                </div>
                                <div class="col-12">`

                                let uniquePrice = Array.from(new Set(airlinePriceDetails))

                                priceRange1 = Math.min(...uniquePrice);
                                priceRange2 = Math.max(...uniquePrice);

                                sidemenu += `
                            <div class="row">
                            <div class="col-8"> <span id="price1">₹ ${priceRange1} </span> </div>
                            <div class="col-4">  <span id="price2">₹ ${priceRange2} </span></div>
                            </div>
                            <div class="range-slider">
                                    <input value="${priceRange1}" min="${priceRange1}" max="${priceRange2}" step="1" type="range" onclick="fliterflightSearch('pricerange')">
                                    <input value="${priceRange2}" min="${priceRange1}" max="${priceRange2}" step="1" type="range" onclick="fliterflightSearch('pricerange')">
                                 
                                </div>`
                                sidemenu += `</div>

                            </div>
                        </div>
                     
                        <div class="iq-card-header p-2">
                            <div class="row p-2">
                                <div class="col-sm-6 my-1">
                                    <h6 class="text-start">Return Price</h6>
                                </div>
                                <div class="col-6 my-1 text-right">
                                    <small>Reset</small>
                                </div>
                                <div class="col-12">`

                                let returnuniquePrice = Array.from(new Set(returnairlinePriceDetails))


                                returnpriceRange1 = Math.min(...returnuniquePrice);
                                returnpriceRange2 = Math.max(...returnuniquePrice);
                                sidemenu += `
                            <div class="row">
                            <div class="col-8"> <span id="returnprice1">₹ ${returnpriceRange1} </span> </div>
                            <div class="col-4"> <span id="returnprice2">₹ ${returnpriceRange2} </span></div>
                            </div>
                            <div class="range-slider">
                                    <input value="${returnpriceRange1}" min="${returnpriceRange1}" max="${returnpriceRange2}" name="returnpricerange" step="1" type="range" onclick="fliterflightSearch('pricerange')">
                                    <input value="${returnpriceRange2}" min="${returnpriceRange1}" max="${returnpriceRange2}" name="returnpricerange" step="1" type="range" onclick="fliterflightSearch('pricerange')">
                                 
                                </div>`
                                sidemenu += `</div>

                            </div>
                        </div>

                        <div class="iq-card-header p-2">
                            <div class="row p-2">
                                <div class="col-sm-6 my-1">
                                    <h6 class="text-start">Preferred Airlines
                                    </h6>
                                </div>
                                <div class="col-6 my-1 text-right">
                                    <small>Reset</small>
                                </div>`
                                let showairline = Array.from(new Set(airlinesDetails))
                                for (let airline of showairline) {
                                    sidemenu += `<div class="col-12 my-1">
                                    <div class="custom-control custom-checkbox custom-control-inline">
                                        <input type="checkbox" class="custom-control-input flight_checkbox" id="${airline}" name="${airline}" value="${airline}" onclick="fliterflightSearch('airlineSearch')">
                                        <label class="custom-control-label" for="${airline}">${airline}</label>
                                    </div>
                                </div>`
                                }

                                sidemenu += `</div>
                        </div>     
                    </div>`;

                                $(".preview-card-side").html(sidemenu);
                            }
                        }
                    }
                } else {
                    if (data.message) {
                        notify(data.message, 'error')
                    }
                }
            }
        })

    }
</script>

<!-- flight scripts -->

<script>
    $(document).ready(function() {
        $('#search-markets').focus(function() {
            $("#main-display").css("display", "block");
            $('div.select-markets-filters').css('display', 'flex');
            $(document).bind('focusin.select-markets-filters click.select-markets-filters', function(e) {
                if ($(e.target).closest('.select-markets-filters, #search-markets').length) return;
                $(document).unbind('.select-markets-filters');
                $('div.select-markets-filters').slideUp(10);
            });
        });
        $("#main-display").hide();
    })
</script>
<script src="//code.jquery.com/ui/1.9.2/jquery-ui.min.js"></script>

<script>
    $(document).ready(function() {
        function getMonthNumberFromName(monthName) {
            return new Date(`${monthName} 1, 2022`).getMonth() + 1;
        }

        var fromLocation, toLocation, lowFarePrice, lowFarePrice1;

        $(function() {
            $("#departure").datepicker({
                minDate: new Date(),
                beforeShow: addCustomInformation,
                beforeShowDay: function(date) {
                    return [true, date.getDay() === 5 || date.getDay() === 6 ? "weekend" : "weekday"];
                },
                onChangeMonthYear: addCustomInformation,
                onSelect: addCustomInformation
            });
            $("#return1").datepicker({
                minDate: new Date(),
                beforeShow: addCustomInformation1,
                beforeShowDay: function(date) {
                    return [true, date.getDay() === 5 || date.getDay() === 6 ? "weekend" : "weekday"];
                },
                onChangeMonthYear: addCustomInformation1,
                onSelect: addCustomInformation1
            });
        });

        // Return date custom function
        function addCustomInformation1() {

            if (fromLocation !== undefined && toLocation !== undefined) {

                const tempData = Array();

                const tempAmt = {
                    amt: ''
                };

                var dayCounter = 0;

                setTimeout(function() {
                    $(".ui-datepicker-calendar td").filter(function() {
                        var date = $(this).text();
                        return /\d/.test(date);
                    }).find("a").attr('data-custom', () => {
                        if (lowFarePrice1) {
                            var d = new Date();
                            d.setDate(d.getDate() + dayCounter);
                            var dayDate = d.getDate()
                            var month = getMonthNumberFromName($('.ui-datepicker-month').html().trim());
                            month = String(month).padStart(2, '0')
                            var year = $('.ui-datepicker-year').html().trim()
                            var fullDateString = `${year}${month}${String(dayDate).padStart(2, '0')}`

                            let tmpAmt = lowFarePrice1.filter((row) => {
                                if (fullDateString === row.travelDateStamp) {
                                    return row;
                                }
                            })

                            dayCounter++;

                            if (tmpAmt.length > 0) {
                                return tmpAmt[0].amount;
                            }
                        }
                        return null;
                    });
                }, 0);
            }
        }


        function addCustomInformation() {

            if (fromLocation !== undefined && toLocation !== undefined) {

                const tempData = Array();

                const tempAmt = {
                    amt: ''
                };

                var dayCounter = 0;

                setTimeout(function() {
                    $(".ui-datepicker-calendar td").filter(function() {
                        var date = $(this).text();
                        return /\d/.test(date);
                    }).find("a").attr('data-custom', () => {
                        if (lowFarePrice) {
                            var d = new Date();
                            d.setDate(d.getDate() + dayCounter);
                            var dayDate = d.getDate()
                            var month = getMonthNumberFromName($('.ui-datepicker-month').html().trim());
                            month = String(month).padStart(2, '0')
                            var year = $('.ui-datepicker-year').html().trim()
                            var fullDateString = `${year}${month}${String(dayDate).padStart(2, '0')}`

                            let tmpAmt = lowFarePrice.filter((row) => {
                                if (fullDateString === row.travelDateStamp) {
                                    return row;
                                }
                            })

                            dayCounter++;

                            if (tmpAmt.length > 0) {
                                return tmpAmt[0].amount;
                            }
                        }

                        return null;
                    });
                }, 0);
            }
        }

        function getLowFare() {
            if (fromLocation !== undefined && toLocation !== undefined) {

                $.ajax({
                    url: "{{ url('getflight') }}",
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    data: {
                        type: 'lowFare',
                        destination: toLocation,
                        origin: fromLocation,
                        month: 09,
                        year: 2023
                    },
                    success: function(response) {
                        swal.close();


                        lowFarePrice = ''
                        if (response.statuscode == 'TXN') {
                            lowFarePrice = response.data.c_resp;

                        } else {
                            console.log('lowFarePrice', response.message)

                        }
                    }
                })
            }
        }

        function getLowFare1() {
            if (fromLocation !== undefined && toLocation !== undefined) {

                $.ajax({
                    url: "{{ url('getflight') }}",
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    data: {
                        type: 'lowFare',
                        destination: fromLocation,
                        origin: toLocation,
                        month: 09,
                        year: 2023
                    },
                    success: function(response) {
                        swal.close();
                        lowFarePrice1 = ''
                        if (response.statuscode == 'TXN') {
                            lowFarePrice1 = response.data.c_resp;

                        } else {

                            console.log('lowFarePrice1', response.message)

                        }
                    }
                })
            }
        }


        $('#from').on('change', function() {
            fromLocation = $(this).val();
            getLowFare();
            getLowFare1();

        });


        $('#to').on('change', function() {
            toLocation = $(this).val();
            getLowFare();
            getLowFare1();

        });

    });
</script>



<script type="text/javascript">
    $(document).ready(function() {

        // function select1TextMaker(item) {
        //     const names1 = {
        //         LKO: 'Amausi Airport',
        //         AYO: 'Maryada Purushottam Shri Ram International Airport',
        //         DEL: 'Indira Gandhi International Airport',
        //         BOM: 'Chhatrapati Shivaji International Airport',
        //     };

        //     let name1 = (names1[item.id]) ? names1[item.id] : '';

        //     var $returnString = $('<div><b class="text-primary">' + item.text + '<b><br/><small class="text-dark">' + name1 + '</small></div>');
        //     return $returnString;
        // }

        // $("#from").select2({
        //     templateResult: select1TextMaker
        // });


        // function select2TextMaker(item) {
        //     const names2 = {
        //         LKO: 'Amausi Airport',
        //         AYO: 'Maryada Purushottam Shri Ram International Airport',
        //         DEL: 'Indira Gandhi International Airport',
        //         BOM: 'Chhatrapati Shivaji International Airport',
        //     };

        //     let name2 = (names2[item.id]) ? names2[item.id] : '';
        //     var $returnString = $('<div><b class="text-primary">' + item.text + '<b><br/><small class="text-dark">' + name2 + '</small></div>');
        //     return $returnString;
        // }

        // $("#to").select2({
        //     templateResult: select2TextMaker
        // });

        $('#from').select2({
            ajax: {
                url: "{{ url('/flight') }}",
                type: 'post',
                minimumInputLength: 2,
                data: function(params) {
                    var query = {
                        city: params.term,
                        page: params.page || 1,
                        _token: `{{csrf_token()}}`

                    }
                    return query;
                },
                processResults: function(item, params) {
                    let citylist = [];

                    if (item.data) {
                        for (let data of item.data) {

                            citylist.push({
                                "id": data.airport_code,
                                "text": data.city
                            })

                        }
                    }

                    return {
                        results: citylist,
                    };
                },
                cache: true
            }
        });


        $('#to').select2({
            ajax: {
                url: "{{ url('/flight') }}",
                type: 'post',
                minimumInputLength: 2,
                data: function(params) {
                    var query = {
                        city: params.term,
                        page: params.page || 1,
                        _token: `{{csrf_token()}}`

                    }
                    return query;
                },
                processResults: function(item, params) {
                    let citylist = [];

                    if (item.data) {
                        for (let data of item.data) {

                            citylist.push({
                                "id": data.airport_code,
                                "text": data.city
                            })

                        }
                    }


                    return {
                        results: citylist,
                    };
                },
                cache: true
            }
        });

    });
</script>

<script type="text/javascript">
    var passengers = 1;
    var travelClass = '';
    var travelType = '';

    function increment1() {
        var value = parseInt(document.getElementById('adult').innerHTML, 10);
        value = isNaN(value) ? 1 : value;
        value++;
        document.getElementById('adult').innerHTML = value;
    }

    function decrement1() {
        var value = parseInt(document.getElementById('adult').innerHTML, 10);
        if (value === 1) {
            value = "1";
        } else {
            value = isNaN(value) ? 1 : value;
            value--;
        }
        document.getElementById('adult').innerHTML = value;
    }

    function increment2() {
        var value = parseInt(document.getElementById('children').innerHTML, 10);
        value = isNaN(value) ? 0 : value;
        value++;
        document.getElementById('children').innerHTML = value;
    }

    function decrement2() {
        var value = parseInt(document.getElementById('children').innerHTML, 10);
        if (value === 0) {
            value = "0";
        } else {
            value = isNaN(value) ? 0 : value;
            value--;
        }
        document.getElementById('children').innerHTML = value;
    }

    function increment3() {
        var value = parseInt(document.getElementById('infant').innerHTML, 10);
        value = isNaN(value) ? 0 : value;
        value++;
        document.getElementById('infant').innerHTML = value;
    }

    function decrement3() {
        var value = parseInt(document.getElementById('infant').innerHTML, 10);
        if (value === 0) {
            value = "0";
        } else {
            value = isNaN(value) ? 0 : value;
            value--;
        }
        document.getElementById('infant').innerHTML = value;
    }
</script>

<script type="text/javascript">
    function valtype() {
        var radio1value = document.querySelector('input[name="travelType"]:checked');

        if (radio1value != null) {
            // document.getElementById("search-markets").value = radio1value.value;
            if (radio1value.value == "0") {
                travelType = "Domestic";
            } else if (radio1value.value == "1") {
                travelType = "International";
            }
            document.getElementById("search-markets").value = `${passengers} Passenger, ${travelClass}, ${travelType}`
        }
    }
</script>

<script type="text/javascript">
    function valclass() {
        var radio2value = document.querySelector('input[name="travelClass"]:checked');
        if (radio2value != null) {

            if (radio2value.value == "0") {
                travelClass = "Economy";
            } else if (radio2value.value == "1") {
                travelClass = "Business";
            } else if (radio2value.value == "2") {
                travelClass = "First Class";
            }
            document.getElementById("search-markets").value = `${passengers} Passenger, ${travelClass}, ${travelType}`
        }
    }
</script>


<script type="text/javascript">
    function total() {
        var tot = parseInt(document.getElementById('search-markets').value);
        tot = parseInt(document.getElementById('adult').innerHTML) + parseInt(document.getElementById('children').innerHTML) + parseInt(document.getElementById('infant').innerHTML);
        passengers = tot;
        document.getElementById('search-markets').value = `${passengers} Passenger, ${travelClass}, ${travelType}`
    }
</script>

@endpush