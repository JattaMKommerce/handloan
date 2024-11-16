@extends('layouts.app')
@section('title', 'Book Flight')
@section('pagetitle', 'Book Flight')
@section('content')

<style>
    .bg-skyblue {
        background-color: #dd7b55;
        color: #fff;
    }

    .bg-image {

        background-repeat: no-repeat;
        background-size: 100% 100%;
        margin-top: 5px;
        padding-left: 2px;
        color: rgb(39, 39, 133);
        box-shadow: 0px 0px 1px silver;
    }

    .button_plus {
        position: absolute;
        width: 35px;
        height: 35px;
        background: #fff;
        cursor: pointer;
        border: 2px solid #095776;

        /* Mittig */
        top: 20%;
        left: 95%;
    }

    .button_plus:after {
        content: '';
        position: absolute;
        transform: translate(-50%, -50%);
        height: 4px;
        width: 50%;
        background: #095776;
        top: 50%;
        left: 50%;
    }

    .button_plus:before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: #095776;
        height: 50%;
        width: 4px;
    }

    .button_plus:hover:before,
    .button_plus:hover:after {
        background: #fff;
        transition: 0.2s;
    }

    .button_plus:hover {
        background-color: #095776;
        transition: 0.2s;
    }



    .steps {
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: relative;
        margin-bottom: -30px;
    }

    .step-button {
        position: relative;
        top: -25px;
        background: #fff;
        border: none;
        color: currentColor;
        padding: 15px;
    }

    .fare-text {
        font-size: 1rem !important;
        font-weight: 600;
    }

    .fare-text1 {
        font-size: 1.2rem !important;
        font-weight: 600;
    }

    .step-item {
        z-index: 10;
        text-align: center;
    }

    #progress {
        position: absolute;
        width: 90%;
        z-index: 5;
        height: 0px;
        border: 1px dashed silver;
        margin-left: 30px;
        margin-bottom: 48px;
    }

    table {
        margin-top: 10px;
        margin-bottom: 10px;
        border-radius: 5px;
        box-shadow: 2px 2px 5px grey;
        overflow: hidden;
    }

    td {
        border: 1px solid silver;
    }

    tr {
        border: 1px solid silver;
    }

    tr:nth-child(even) {
        background: #f1f3f4;
    }
</style>


<div id="loading">
    <div id="loading-center">
    </div>
</div>
<!-- loader END -->
<!-- Wrapper Start -->
<div class="wrapper">

    <!-- Page Content  -->


    <div class="row">
        @if (isset($statuscode) && ($statuscode == 'TXF' || $statuscode == 'ERR'))
        <script>
            swal({
                title: 'Failed',
                text: `{{ $message }}`,
                // onOpen: () => {
                //     swal.showLoading()
                // },
                showConfirmButton: true,
                allowOutsideClick: () => !swal.isLoading()
            }).then(function() {
                window.location.href = '/bookingStatus';
            });
        </script>
        @endif
        <div class="col-lg-12">

            <div class="row mb-2">

                <div class="col-sm-9 col-md-9 col-lg-9">
                    <div id="repriceData">

                    </div>
                    <div class="iq-card" id="flightlist1">

                    </div>
                    <div class="iq-card" id="flightlist2">

                    </div>
                </div>
                <div class="col-sm-3 col-md-3 col-lg-3">
                    <div class="iq-card border">
                        <div class="card-header bg-white d-flex justify-content-between">
                            <div class="iq-header-title">
                                <h6 class="text-primary fare-text"><b>FARE SUMMARY</b></h6>
                            </div>
                        </div>
                        <div class="iq-card-body" id="sidecard1">

                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-sm-12 col-md-12 col-lg-12">

                    <div class="iq-card iq-card-block iq-card-stretch iq-card-height batm_custom-card">
                        <div class="card-header bg-white flight_card">
                            <h4 class="fare-text1 text-primary">Passenger Details</h4>
                        </div>


                        <form id='pa_details' action="{{ route('getflight') }}" method="post">
                            {{ csrf_field() }}

                            <input type="hidden" id="returnflightKey" name="returnflightKey" value="">
                            <input type="hidden" id="type" name="type" value="tempBook">
                            <input type="hidden" id="requestId" name="requestId" value="">
                            <input type="hidden" id="flightId" name="flightId" value="">
                            <input type="hidden" id="base_amount" name="base_amount" value="">
                            <input type="hidden" id="tax_amount" name="tax_amount" value="">
                            <input type="hidden" id="travelInfo" name="travelInfo" value="">
                            <input type="hidden" id="adultCount" name="adultCount" value="">
                            <input type="hidden" id="bookingType" name="bookingType" value="">
                            <input type="hidden" id="childCount" name="childCount" value="">
                            <input type="hidden" id="fareId" name="fareId" value="">
                            <input type="hidden" id="flightKey" name="flightKey" value="">
                            <input type="hidden" id="infantCount" name="infantCount" value="">
                            <input type="hidden" id="searchKey" name="searchKey" value="">
                            <input type="hidden" id="total_amount" name="total_amount" value="">
                            <input type="hidden" id="travelClass" name="travelClass" value="">
                            <input type="hidden" id="travelType" name="travelType" value="">
                            {{-- <input type="hidden" name="inventoryType" value="0"> --}}
                            <div class="row" style="padding: 20px;">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="exampleInputdate">Email Id</label>
                                        <input type="email" class="form-control" id="passengerEmail" value="" name="passengerEmail">
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="form-group" type="hidden">
                                        <label for="exampleInputdate">WhatsApp/Mobile Number</label>
                                        <input type="number" class="form-control" id="whatsappMobile" value="" name="whatsappMobile" maxlength='11'>
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="passangerDetails" style="padding: 10px;">
                                    <div class="row">
                                        <div class="col-2">
                                            <div class="form-group">
                                                <label>Title</label>
                                                <select class="form-control mb-2" id="travel_class" name="travelClass">
                                                    <option selected="">Select Title</option>
                                                    <option value="Mr">Mr</option>
                                                    <option value="Miss">Miss</option>
                                                    <option value="Mrs">Mrs</option>
                                                    <option value="3">Other</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-2">
                                            <div class="form-group">
                                                <label>Gender</label>
                                                <select class="form-control mb-2" id="travel_type" name="travelType">
                                                    <option selected="">Select Gender</option>
                                                    <option value="Male">Male</option>
                                                    <option value="Female">Female</option>
                                                    <option value="Others">Other</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-2">
                                            <div class="form-group">
                                                <label>Traveler Type</label>
                                                <select class="form-control mb-2" id="travel_type" name="travelType">
                                                    <option selected="">Select Type</option>
                                                    <option value="0">Adult (Above 12y)</option>
                                                    <option value="1">Child (2y-12y)</option>
                                                    <option value="2">Infant(below 2 years)</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-2">
                                            <div class="form-group">
                                                <label for="exampleInputdate">Date Of Birth</label>
                                                <input type="date" class="form-control" id="dob" value=""
                                                    name="dob">
                                            </div>
                                        </div>

                                        <div class="col-2">
                                            <div class="form-group">
                                                <label for="exampleInputdate">First Name</label>
                                                <input type="text" class="form-control" id="firstName" value=""
                                                    name="firstName">
                                            </div>
                                        </div>

                                        <div class="col-2">
                                            <div class="form-group">
                                                <label for="exampleInputdate">Last Name</label>
                                                <input type="text" class="form-control" id="lastName" value=""
                                                    name="lastName">
                                            </div>
                                        </div>

                                        <div class="col-2">
                                            <div class="form-group">
                                                <label for="exampleInputdate">Passport Number</label>
                                                <input type="text" class="form-control" id="passportNumber" value=""
                                                    name="passportNumber">
                                            </div>
                                        </div>

                                        <div class="col-2">
                                            <div class="form-group">
                                                <label for="exampleInputdate">Passport Issuing Country</label>
                                                <input type="text" class="form-control" id="passport_issuing_country"
                                                    value="" name="passportNumber">
                                            </div>
                                        </div>

                                        <div class="col-2">
                                            <div class="form-group">
                                                <label for="exampleInputdate">Passport Expiry</label>
                                                <input type="text" class="form-control" id="passport_expiry"
                                                    value="" name="passportNumber">
                                            </div>
                                        </div>

                                        <div class="col-2">
                                            <div class="form-group">
                                                <label for="exampleInputdate">Passenger Nationality</label>
                                                <input type="text" class="form-control" id="nationality" value=""
                                                    name="passportNumber">
                                            </div>
                                        </div>

                                    </div>
                                </div> --}}

                            <div id="passengerDetails" name="passengerDetails[]" style="padding: 10px;">
                                <div class="row mt-1">
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Title</label>
                                            <select class="form-control mb-2" id="p_type" name="p_type[]">
                                                <option selected="">Select Title</option>
                                                <option value="Mr">Mr</option>
                                                <option value="Miss">Miss</option>
                                                <option value="Mrs">Mrs</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-2">
                                        <div class="form-group">
                                            <label for="exampleInputdate">First Name</label>
                                            <input type="text" class="form-control" id="firstName" name="firstName[]">
                                        </div>
                                    </div>

                                    <div class="col-2">
                                        <div class="form-group">
                                            <label for="exampleInputdate">Last Name</label>
                                            <input type="text" class="form-control" id="lastName" name="lastName[]">
                                        </div>
                                    </div>

                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Gender</label>
                                            <select class="form-control mb-2" id="gender" name="gender[]">
                                                <option selected="">Select Gender</option>
                                                <option value="0">Male</option>
                                                <option value="1">Female</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Traveller Type</label>
                                            <select class="form-control mb-2" id="travel_type" name="travel_type[]">
                                                <option selected="">Select Type</option>
                                                <option value="0">Adult (Above 12y)</option>
                                                <option value="1">Child (2y-12y)</option>
                                                <option value="2">Infant(below 2 years)</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-2" style="display:none;">
                                        <div class="form-group">
                                            <label for="exampleInputdate">Age</label>
                                            <input type="number" class="form-control" id="age" name="age[]">
                                        </div>
                                    </div>

                                    <div class="col-2">
                                        <div class="form-group">
                                            <label for="exampleInputdate">Date Of Birth</label>
                                            <input type="date" class="form-control" id="dob" name="dob[]">
                                        </div>
                                    </div>

                                    <div class="col-2" style="display:none;">
                                        <div class="form-group">
                                            <label for="exampleInputdate">Passenger Nationality</label>
                                            <input type="text" class="form-control" id="nationality" name="nationality[]">
                                        </div>
                                    </div>

                                    <div class="col-2" style="display:none;">
                                        <div class="form-group">
                                            <label for="exampleInputdate">Passport Number</label>
                                            <input type="text" class="form-control" id="passportNumber" name="passportNumber[]">
                                        </div>
                                    </div>

                                    <div class="col-2" style="display:none;">
                                        <div class="form-group">
                                            <label for="exampleInputdate">Passport Issuing Country</label>
                                            <input type="text" class="form-control" id="passport_issuing_country" name="passport_issuing_country[]">
                                        </div>
                                    </div>

                                    <div class="col-2" style="display:none;">
                                        <div class="form-group">
                                            <label for="exampleInputdate">Passport Expiry</label>
                                            <input type="date" class="form-control" id="passport_expiry" name="passport_expiry[]">
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class=" d-flex align-items-center justify-content-center">
                                <button type="submit" class="btn btn-primary mb-3 mr-3">Book</button><br>
                                <!-- <button id="addpassenger" type="button" onclick="addnewdetails()" class="btn btn-primary mb-3"> Add Details</button> -->
                            </div>
                        </form>
                    </div>
                </div>



            </div>

        </div>
    </div>

    <div class="modal fade" id="cancelFlight" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">CANCELLATION & DATE CHANGE POLICY</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="fareruledata">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="bookingModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel">Make Payment<span class="payeename text-capitalize"></span>
                    </h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>

                </div>
                @if (isset($statuscode) && ($statuscode == 'TXF' || $statuscode == 'ERR'))
                <script>
                    swal({
                        title: 'Failed',
                        text: `{{ $message }}`,
                        showConfirmButton: true,
                        allowOutsideClick: () => !swal.isLoading()
                    }).then(function() {
                        window.location.href = '/flight';
                    });
                </script>
                @endif

                <form id="paymentForm" method="post" action="{{ url('getflight') }}">
                    <div class="modal-body">
                        {!! csrf_field() !!}
                        <div class="form-group">
                            <label for="exampleInputdate">Booking Ref No.</label>
                            <input type="text" class="form-control" name="bookingRefNo" value="" readonly>
                        </div>

                        <div class="form-group">
                            <label for="exampleInputdate">Amount</label>
                            <input type="text" class="form-control" name="amount" value="" readonly>
                        </div>
                        <input type="hidden" name="type" value="addPayment">
                        <input type="hidden" name="requestId" value="">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-hidden="true">Close</button>
                        <button class="btn btn-primary " type="submit" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Submitting">Pay Now</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade" id="confirmBookingModal" role="dialog" data-backdrop="false">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Confirm Booking<span class="payeename text-capitalize"></span> </h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>

                </div>

                <form id="confirmForm" method="post" action="{{ url('getflight') }}">
                    <div class="modal-body">
                        {!! csrf_field() !!}
                        <div class="form-group">
                            <label for="exampleInputdate">Booking Ref No.</label>
                            <input type="text" class="form-control" name="Booking_RefNo" value="" readonly>
                        </div>

                        <div class="form-group">
                            {{-- <label for="exampleInputdate">Amount</label> --}}
                            <input type="hidden" class="form-control" name="ticketType" value="1" readonly>
                        </div>
                        <input type="hidden" name="type" value="ticket">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-hidden="true">Close</button>
                        <button class="btn btn-primary" type="submit" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Submitting">Confirm
                            Booking</button>
                    </div>
                </form>
            </div>
        </div>
    </div><!-- /.modal -->

    @endsection

    @push('script')
    {{-- <script type="text/javascript" src="http://ajax.aspnetcdn.com/ajax/jquery/jquery-1.4.4.min.js"></script> --}}
    {{-- <script type="text/javascript" src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.7/jquery.validate.min.js"></script> --}}
    <script>
        // Function to check if the page is being refreshed
        function isPageRefreshed() {
            // Check if the performance navigation type is '1' (PAGE_RELOAD)
            return performance.navigation.type === 1;
        }

        // Function to redirect if the page is refreshed
        function redirectOnRefresh() {
            if (isPageRefreshed()) {
                // Redirect to the desired URL
                window.location.href = '/flight';
            }
        }

        // Call the function on page load
        window.onload = redirectOnRefresh;
    </script>
    <script>
        // $(document).ready(function() {
        //     $('#pa_details').on('submit', function(event) {

        //         event.preventDefault();

        //         // const formData = new FormData(this)


        //         let type = $("input[name='type']").val();
        //         let p_email = $("input[name='passengerEmail']").val();
        //         let p_mobile = $("input[name='whatsappMobile']").val();
        //         let p_details = $("input[name='passengerDetails']").serialize();


        //         var formData = new FormData();
        //         let TotalFiles = $('input[name=passengerDetails]')[0].length; //Total files
        //         console.log(TotalFiles);
        //         let files = $('input[name=passengerDetails]')[0];
        //         for (let i = 0; i < TotalFiles; i++) {
        //             formData.append('files' + i, files.files[i]);
        //         }
        //         formData.append('TotalFiles', TotalFiles);



        //         $.ajax({
        //                 url: `{{ url('getflight') }}`,
        //                 type: 'post',
        //                 headers: {
        //                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //                 },
        //                 dataType: 'json',
        //                 data: formData
        //             })
        //             .done(function(data) {
        //                 console.log(data);
        //             })
        //             .fail(function(errors) {
        //                 notify('Oops', errors.status + '! ' + errors.statusText, 'warning');
        //             });
        //     });
        // });

        $(document).ready(function() {
            var nam = localStorage.getItem('selectedFlightDetails');
            nam = JSON.parse(nam)
            // console.log('data', nam?.repriceData?.data?.Flight[1]?.Flight);
            var Request_Id = nam?.Request_Id;
            var adultCount = nam?.adultCount;
            var childCount = nam?.childCount;
            var bookingType = nam?.bookingType;
            var fareId = nam?.fareId;
            var flightKey = nam?.flightKey;
            var infantCount = nam?.infantCount;
            var searchKey = nam?.searchKey;
            var total_amount = nam?.total_amount;
            var travelClass = nam?.travelClass;
            var travelType = nam?.travelType;
            var base_amount = nam?.baseAmount;
            var tax_amount = nam?.tax;
            var travelInfo = nam?.travelInfo;
            var flightId = nam?.flightId;
            var returnflightKey = nam?.returnflightKey;
            var repriceDatago = nam?.repriceData?.data?.Flight[0]?.Flight;
            var repriceDataback = nam?.repriceData?.data?.Flight[1]?.Flight;
            var airlineLogo = nam?.airlineLogo;

            // Pass the data to the form
            document.getElementById('returnflightKey').value = returnflightKey;
            document.getElementById('travelInfo').value = JSON.stringify(travelInfo);
            document.getElementById('flightId').value = flightId;
            document.getElementById('requestId').value = Request_Id;
            document.getElementById('base_amount').value = base_amount;
            document.getElementById('tax_amount').value = tax_amount;
            document.getElementById('childCount').value = childCount;
            document.getElementById('adultCount').value = adultCount;
            document.getElementById('bookingType').value = bookingType;
            document.getElementById('fareId').value = fareId;
            document.getElementById('flightKey').value = flightKey;
            document.getElementById('infantCount').value = infantCount;
            document.getElementById('searchKey').value = searchKey;
            document.getElementById('total_amount').value = total_amount;
            document.getElementById('travelClass').value = travelClass;
            document.getElementById('travelType').value = travelType;

            // One way flight list
            function showLogo(item) {

                const airline_logo = airlineLogo.filter((data) => {

                    return data.airline_code == item
                })

                // console.log('1', airline_logo);
                return airline_logo[0]?.flight_logo_url;
            }

            if (bookingType == "0") {
                var dataflight1 = '';

                dataflight1 += `<div class="iq-card">
                <div class="col-sm-12 col-md-12 col-lg-12">  
                <div class="row iq-card-body">                        
                    <h6 class="text-primary w-100 fare-text"><b>${repriceDatago?.Segments[0].Origin_City} to ${repriceDatago?.Segments[0].Destination_City}</b>
                    <a class="float-right" href="javascript:void(0)" onclick="cancelFlightCharge('departure')" ><u>Fare Rule</u></a>
                    </h6>
                `;

                for (let flightDetails of repriceDatago?.Segments) {
                    dataflight1 += `
                        <div class="border rounded mt-2" >
                            
                            <div class="card-body row">
                                        <div class="col-12 px-3 pb-1">
                                            <h6 class="text-primary fare-text">
                                            
                                            <img src="${showLogo(flightDetails.Airline_Code)}" style="height:35px;"/>

                                            <b>${flightDetails.Airline_Name} ${flightDetails.Airline_Code} |
                                                ${flightDetails.Flight_Number}</b></h6>
                                        </div>
                                        <div class="col-6">
                                        <h6 class="fw-bold text-left fare-text">
                                        <span class="bg-skyblue px-2 rounded ">Start On - ${flightDetails.Departure_DateTime.split(" ")[0]} </span>
                                        </h6>
                                        </div>
                                        <div class="col-6">
                                        <h6 class="fw-bold text-right fare-text">
                                        <span class="bg-skyblue px-2 rounded ">Arive On - ${flightDetails.Departure_DateTime.split(" ")[0]} </span>
                                        </h6>
                                        </div>
                                        <div class="col-12 container mt-4">
                                            <div class="accordion">
                                                <div class="steps">
                                                    <progress id="progress" value=0 max=100 ></progress>
                                                    <div class="step-item">
                                                        <button class="step-button fare-text1" type="button" disabled>   
                                                    <h6>${flightDetails.Departure_DateTime.split(" ")[1]}</h6>
                                                        </button> 
                                                    
                                                    </div>
                                                    
                                                    <div class="step-item">
                                                        <button class="step-button fare-text1" type="button" disabled>
                                                        <h6 class="fw-bold">${flightDetails.Duration.split(":")[0]}h 
                                                        ${flightDetails.Duration.split(":")[1]}m</h6>
                                                        </button>
                                                    
                                                    </div>
                                                    <div class="step-item">
                                                        <button class="step-button fare-text1" type="button" disabled>
                                                        <h6> ${flightDetails.Arrival_DateTime.split(" ")[1]}</h6>
                                                    </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4 text-left" >
                                            <b class="card-text" style="font-size: 12px;">
                                                    ${flightDetails.Origin_City}</b><br>
                                            <b class="text-primary" style="font-size: 12px;">Terminal
                                                ${flightDetails.Origin_Terminal}</b>
                                        </div>

                                        <div class="col-4 text-center">
                                           <b> Duration</b>
                                        </div>
                                        <div class="col-4 text-right">
                                        
                                            <b class="card-text" style="font-size: 12px;">
                                                    ${flightDetails?.Destination_City}</b><br>
                                            <b class="text-primary" style="font-size: 12px;">Terminal
                                                ${flightDetails?.Destination_Terminal} </b>
                                        </div>
                             </div>
                            <div class="card-footer bg-white">
                                <b>
                                        Baggage - ${repriceDatago?.Fares[0]?.FareDetails[0]?.Free_Baggage?.Hand_Baggage}
                                                (1 piece only) Cabin,  &nbsp;
                                                ${repriceDatago?.Fares[0]?.FareDetails[0]?.Free_Baggage?.Check_In_Baggage} 
                                                (1 piece only) Check-in 
                                </b>
                            </div>
                        </div>
                    `;
                }

                dataflight1 += `</div></div></div>`;

                $("#repriceData").append(dataflight1);

            } else if (bookingType == "1") {
                var dataflight2 = '';
                dataflight2 += `<div class="iq-card">
                <div class="col-sm-12 col-md-12 col-lg-12">
                <div class="row ">
                   <div class="col-12 mb-2" >                          
                    <h6 class="text-primary fare-text mt-2"><button class="btn btn-primary btn-xs pt-0 px-1 pb-1" type="button">
                    <small> <b>Departure</b> </small></button> &nbsp; <b>${repriceDatago?.Segments[0].Origin_City} to ${repriceDataback?.Segments[0].Origin_City}
                    </b>
                        <a class="float-right" href="javascript:void(0)" onclick="cancelFlightCharge('departure')" ><u>Fare Rule</u></a></h6>
                    
                `;
                for (let flightDetails1 of repriceDatago?.Segments) {
                    dataflight2 += `
                    <div class="border rounded mt-2" >             
                            <div class="card-body row">
                                        <div class="col-12 px-3 pb-1">
                                        
                                            <h6 class="text-primary fare-text">
                                            <img src="${showLogo(flightDetails1.Airline_Code)}" style="height:35px;"/>
                                            <b>${flightDetails1.Airline_Name} ${flightDetails1.Airline_Code} |
                                                ${flightDetails1.Flight_Number}</b></h6>
                                        </div>
                                        <div class="col-6">
                                        <h6 class="fw-bold text-left fare-text">
                                        <span class="bg-skyblue px-2 rounded ">Start On - ${flightDetails1.Departure_DateTime.split(" ")[0]} </span>
                                        </h6>
                                        </div>
                                        <div class="col-6">
                                        <h6 class="fw-bold text-right fare-text">
                                        <span class="bg-skyblue px-2 rounded ">Arive On - ${flightDetails1.Departure_DateTime.split(" ")[0]} </span>
                                        </h6>
                                        </div>
                                        <div class="col-12 container mt-4">
                                            <div class="accordion">
                                                <div class="steps">
                                                    <progress id="progress" value=0 max=100 ></progress>
                                                    <div class="step-item">
                                                        <button class="step-button fare-text1" type="button" disabled>   
                                                    <h6>${flightDetails1.Departure_DateTime.split(" ")[1]}</h6>
                                                        </button> 
                                                    
                                                    </div>
                                                    
                                                    <div class="step-item">
                                                        <button class="step-button fare-text1" type="button" disabled>
                                                        <h6 class="fw-bold">${flightDetails1.Duration.split(":")[0]}h 
                                                        ${flightDetails1.Duration.split(":")[1]}m</h6>
                                                        </button>
                                                    
                                                    </div>
                                                    <div class="step-item">
                                                        <button class="step-button fare-text1" type="button" disabled>
                                                        <h6> ${flightDetails1.Arrival_DateTime.split(" ")[1]}</h6>
                                                    </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4 text-left" >
                                            <b class="card-text" style="font-size: 12px;">
                                                    ${flightDetails1.Origin_City}</b><br>
                                            <b class="text-primary" style="font-size: 12px;">Terminal
                                                ${flightDetails1.Origin_Terminal}</b>
                                        </div>

                                        <div class="col-4 text-center">
                                           <b> Duration</b>
                                        </div>
                                        <div class="col-4 text-right">
                                        
                                            <b  class="card-text" style="font-size: 12px;">
                                                    ${flightDetails1?.Destination_City}</b><br>
                                            <b class="text-primary" style="font-size: 12px;">Terminal
                                                ${flightDetails1?.Destination_Terminal} </b>
                                        </div>
                            </div>
                            <div class="card-footer bg-white">
                                <b >
                                        Baggage - ${repriceDatago?.Fares[0]?.FareDetails[0]?.Free_Baggage?.Hand_Baggage}
                                                (1 piece only) Cabin,  &nbsp;
                                                ${repriceDatago?.Fares[0]?.FareDetails[0]?.Free_Baggage?.Check_In_Baggage} 
                                                (1 piece only) Check-in 
                                </b>
                            </div>
                    </div>
                    `;
                }
                dataflight2 += `</div></div></div></div>`;
                $("#flightlist1").append(dataflight2);

                var dataflight3 = '';
                dataflight3 += `<div class="iq-card">
                <div class="col-sm-12 col-md-12 col-lg-12">      
                <div class="row">
                   <div class="col-12 mb-2" >                    
                    <h6 class="text-primary fare-text mt-3"><button class="btn btn-primary btn-xs pt-0 px-1 pb-1"  type="button"><small><b>Return</b></small></button> &nbsp;<b>${repriceDataback?.Segments[0].Origin_City} to ${repriceDatago?.Segments[0].Origin_City}
                    </b>
                    <a class="float-right" href="javascript:void(0)" onclick="cancelFlightCharge('return')" ><u>Fare Rule</u></a></h6>
                `;
                for (let flightDetails2 of repriceDataback?.Segments) {
                    dataflight3 += `
                    <div class="border rounded mt-2" >             
                                    <div class="card-body row">
                                        <div class="col-12 px-3 pb-1">
                                      
                                            <h6 class="text-primary fare-text">
                                            <img src="${showLogo(flightDetails2.Airline_Code)}"  style="height:35px;"/>
                                            <b>${flightDetails2.Airline_Name} ${flightDetails2.Airline_Code} |
                                                ${flightDetails2.Flight_Number}</b></h6>
                                        </div>
                                        <div class="col-6">
                                        <h6 class="fw-bold text-left fare-text">
                                        <span class="bg-skyblue px-2 rounded ">Start On - ${flightDetails2.Departure_DateTime.split(" ")[0]} </span>
                                        </h6>
                                        </div>
                                        <div class="col-6">
                                        <h6 class="fw-bold text-right fare-text">
                                        <span class="bg-skyblue px-2 rounded ">Arive On - ${flightDetails2.Departure_DateTime.split(" ")[0]} </span>
                                        </h6>
                                        </div>
                                        <div class="col-12 container mt-4">
                                            <div class="accordion">
                                                <div class="steps">
                                                    <progress id="progress" value=0 max=100 ></progress>
                                                    <div class="step-item">
                                                        <button class="step-button fare-text1" type="button" disabled>   
                                                    <h6>${flightDetails2.Departure_DateTime.split(" ")[1]}</h6>
                                                        </button> 
                                                    
                                                    </div>
                                                    
                                                    <div class="step-item">
                                                        <button class="step-button fare-text1" type="button" disabled>
                                                        <h6 class="fw-bold">${flightDetails2.Duration.split(":")[0]}h 
                                                        ${flightDetails2.Duration.split(":")[1]}m</h6>
                                                        </button>
                                                    
                                                    </div>
                                                    <div class="step-item">
                                                        <button class="step-button fare-text1" type="button" disabled>
                                                        <h6> ${flightDetails2.Arrival_DateTime.split(" ")[1]}</h6>
                                                    </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4 text-left" >
                                            <b class="card-text" style="font-size: 12px;">
                                                    ${flightDetails2.Origin_City}</b><br>
                                            <b class=" text-primary" style="font-size: 12px;">Terminal
                                                ${flightDetails2.Origin_Terminal}</b>
                                        </div>

                                        <div class="col-4 text-center">
                                           <b> Duration</b>
                                        </div>
                                        <div class="col-4 text-right">
                                        
                                            <b class="card-text" style="font-size: 12px;">
                                                    ${flightDetails2?.Destination_City}</b><br>
                                            <b class=" text-primary" style="font-size: 12px;">Terminal
                                                ${flightDetails2?.Destination_Terminal} </b>
                                        </div>
                                    </div>
                            <div class="card-footer bg-white">
                                <b>
                                        Baggage - ${repriceDatago?.Fares[0]?.FareDetails[0]?.Free_Baggage?.Hand_Baggage}
                                                (1 piece only) Cabin,  &nbsp;
                                                ${repriceDatago?.Fares[0]?.FareDetails[0]?.Free_Baggage?.Check_In_Baggage} 
                                                (1 piece only) Check-in 
                                </b>
                            </div>
                    </div>
                    `;
                }
                dataflight3 += `</div></div></div></div>`;
                $("#flightlist2").append(dataflight3);
            }
            // Ens two way flight list

            // Side Amount List
            var grandamount1 = `
                            <div>
                                <div class="row">
                                    <div class="col-sm-6 ">
                                        <h6 class="text-start fare-text">Base Fare</h6>
                                        <small><b> Adult (${nam?.adultCount} x ₹${nam?.adultFlightAmount})</b></small></br>
                                        <small><b> Child (${nam?.childCount} x ₹${nam?.childFlightAmount})</b></small><br/>
                                        <small><b> Infant (${nam?.infantCount} x ₹${nam?.infantFlightAmount})</b></small>
                                    </div>
                                    <div class="col-6 text-right text-primary">
                                        <small class="fare-text">₹ ${nam?.baseAmount}</small>
                                    </div>
                                </div>
                                <center>
                                    <hr width="95%" />
                                </center>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <b class="text-start fare-text">Tax Surcharge</b>
                                    </div>
                                    <div class="col-6 text-right text-primary">
                                        <small class="fare-text">₹ ${nam?.tax}</small>
                                    </div>
                                </div>
                                <center>
                                    <hr width="100%" />
                                </center>
                                <div class="row">
                                    <div class="col-sm-6 ">
                                        <b class="text-start text-danger fare-text">Grand Total</b>
                                    </div>
                                    <div class="col-6 text-right">
                                        <b class="text-info fare-text">₹ ${nam?.total_amount}</b>
                                    </div>
                                </div>
                            </div>`;
            $("#sidecard1").html(grandamount1);
            // End Side Amount list

            $("#pa_details").validate({
                rules: {
                    passengerEmail: {
                        required: true,
                    },
                    whatsappMobile: {
                        required: true,
                        number: true,
                        minlength: 9,
                        maxlength: 11
                    },

                },
                messages: {
                    passengerEmail: {
                        required: "Please enter email id",
                    },
                    whatsappMobile: {
                        required: "Please enter mobile number",
                        number: "Mobile number should be numeric",
                        min: "Mobile number length should be atleast 9",
                        max: "Mobile number length should be less than 11",
                    },
                },
                errorElement: "p",
                errorPlacement: function(error, element) {
                    if (element.prop("tagName").toLowerCase() === "select") {
                        error.insertAfter(element.closest(".form-group"));
                    } else {
                        error.insertAfter(element);
                    }
                },
                submitHandler: function() {
                    var form = $('#pa_details');
                    form.ajaxSubmit({
                        dataType: 'json',

                        beforeSubmit: function() {
                            form.find('button[type="submit"]').html('loading').attr('disabled', true).addClass('btn-secondary');
                            swal({
                                title: 'Wait!',
                                text: 'We are working on your request',
                                onOpen: () => {
                                    swal.showLoading()
                                },
                                allowOutsideClick: () => !swal.isLoading()
                            });
                        },
                        success: function(data) {
                            form.find('button[type="submit"]').html('Submit').attr('disabled', false).removeClass('btn-secondary');
                            swal.close();

                            if (data.statuscode == "TXN" || data.statuscode == "success") {
                                payment(data.data.Booking_Ref_No, data.data.Amount, data
                                    .data.Request_Id)

                                notify("flight details Successfully Submitted", 'success');
                            } else {
                                if (data.message == null || data.message == "") {
                                    notify("Try after Sometimes", 'error');
                                }
                                notify(data.message, 'error');
                            }
                        },
                        error: function(errors) {
                            showError(errors, form);
                            notify("Try after Sometimes1", 'error');
                        }
                    });
                }
            });

            //     $("#paymentForm").submit(function(event) {
            //         event.preventDefault(); // Prevent the default form submission

            //         var form = $('#paymentForm');

            //         console.log(form);

            //         form.ajaxSubmit({
            //             dataType: 'json',
            //             beforeSubmit: function() {
            //                 form.find('button[type="submit"]').button('loading');
            //                 swal({
            //                         title: 'Wait!',
            //                         text: 'We are working on your request',
            //                         onOpen: () => {
            //                             swal.showLoading()
            //                         },
            //                         allowOutsideClick: () => !swal.isLoading()
            //                     });
            //             },
            //             success: function(data) {
            //                 form.find('button[type="submit"]').button('reset');
            //                 swal.close();

            //                 if (data.statuscode === "TXN" || data.statuscode === "success") {
            //                     $('#bookingModal').modal('hide');
            //                     // const url = {url('/flightConfirm')};
            //                     console.log(data.data.Booking_RefNo);
            //                     console.log(data);
            //                     console.log(data.data);
            //                     window.location.href = payment(data.data.Booking_RefNo);

            //                     // confirmBook(data.data.Booking_RefNo);


            //                     notify("Payment Successfully", 'success');
            //                 } else {
            //                     if (!data.message) {
            //                         notify("Try again later", 'error');
            //                     } else {
            //                         notify(data.message, 'error');
            //                     }
            //                 }
            //             },
            //             error: function(xhr, textStatus, errorThrown) {
            //                 showError(xhr, form);
            //                 notify("An error occurred. Please try again.", 'error');
            //             }
            //         });
            //     });

            // setTimeout(() => {
            // console.log('time hide')
            $('#sidebarhide').addClass('open');
            $('.iq-page-menu-vertical').addClass('sidebar-main');
            // }, 2000);
        });

        $(document).ready(function() {
            var nam2 = JSON.parse(localStorage.getItem('selectedFlightDetails'));
            // console.log('data', nam?.repriceData?.data?.Flight[1]?.Flight);

            var adultCount = nam2?.adultCount;
            var childCount = nam2?.childCount;
            var infantCount = nam2?.infantCount;
            var i;
            let totalpassenger = nam2?.adultCount + nam2?.childCount + nam2?.infantCount;

            for (i = 1; i < totalpassenger; i++) {
                var newRowAdd = `
                        <div class="row mt-1"> 
                        <div class="col-2"> 
                        <div class="form-group"> 
                        <label>Title</label> 
                        <select class="form-control mb-2" id="p_type${i}" name="p_type[]"> 
                        <option selected="">Select Title</option> 
                        <option value="Mr">Mr</option> 
                        <option value="Miss">Miss</option> 
                        <option value="Mrs">Mrs</option> 
                        </select> 
                        </div> 
                        </div> 

                        <div class="col-2"> 
                        <div class="form-group"> 
                        <label for="exampleInputdate">First Name</label> 
                        <input type="text" class="form-control" id="firstName${i}" name="firstName[]"> 
                        </div> 
                        </div> 

                        <div class="col-2"> 
                        <div class="form-group"> 
                        <label for="exampleInputdate">Last Name</label> 
                        <input type="text" class="form-control" id="lastName${i}" name="lastName[]"> 
                        </div> 
                        </div> 

                        <div class="col-2"> 
                        <div class="form-group"> 
                        <label>Gender</label> 
                        <select class="form-control mb-2" id="gender${i}" name="gender[]"> 
                        <option selected="">Select Gender</option> 
                        <option value="0">Male</option> 
                        <option value="1">Female</option> 
                        </select> 
                        </div> 
                        </div> 

                        <div class="col-2"> 
                        <div class="form-group"> 
                        <label>Traveller Type</label> 
                        <select class="form-control mb-2" id="travel_type${i}" name="travel_type[]"> 
                        <option selected="">Select Type</option> 
                        <option value="0">Adult (Above 12y)</option> 
                        <option value="1">Child (2y-12y)</option> 
                        <option value="2">Infant(below 2 years)</option> 
                        </select> 
                        </div> 
                        </div> 

                        <div class="col-2" style="display:none;"> 
                        <div class="form-group"> 
                        <label for="exampleInputdate">Age</label> 
                        <input type="number" class="form-control" id="age${i}" name="age[]"> 
                        </div> 
                        </div> 

                        <div class="col-2"> 
                        <div class="form-group"> 
                        <label for="exampleInputdate">Date Of Birth</label> 
                        <input type="date" class="form-control" id="dob${i}" name="dob[]"> 
                        </div> 
                        </div> 

                        <div class="col-2" style="display:none;"> 
                        <div class="form-group"> 
                        <label for="exampleInputdate">Passenger Nationality</label> 
                        <input type="text" class="form-control" id="nationality${i}" name="nationality[]"> 
                        </div> 
                        </div>

                        <div class="col-2" style="display:none;"> 
                        <div class="form-group"> 
                        <label for="exampleInputdate">Passport Number</label> 
                        <input type="text" class="form-control" id="passportNumber${i}" name="passportNumber[]"> 
                        </div> 
                        </div> 

                        <div class="col-2" style="display:none;"> 
                        <div class="form-group"> 
                        <label for="exampleInputdate">Passport Issuing Country</label> 
                        <input type="text" class="form-control" id="passport_issuing_country${i}" name="passport_issuing_country[]"> 
                        </div> 
                        </div> 

                        <div class="col-2" style="display:none;"> 
                        <div class="form-group"> 
                        <label for="exampleInputdate">Passport Expiry</label> 
                        <input type="date" class="form-control" id="passport_expiry${i}" name="passport_expiry[]"> 
                        </div> 
                        </div> 
                    </div>`

            $('#passengerDetails').append(newRowAdd);
        
            }
        });


        function payment(b_RefNo, amount, req_id) {
            $('#bookingModal').find('input[name="bookingRefNo"]').val(b_RefNo);
            $('#bookingModal').find('input[name="amount"]').val(amount);
            $('#bookingModal').find('input[name="requestId"]').val(req_id);
            $('#bookingModal').modal();
        }

        function confirmBook(b_RefNo) {
            $('#confirmBookingModal').find('input[name="Booking_RefNo"]').val(b_RefNo);
            // $('#bookingModal').find('input[name="amount"]').val(amount);
            $('#confirmBookingModal').modal();
        }

        function cancelFlightCharge(type) {

            $("#fareruledata").html('');
            let nam1 = JSON.parse(localStorage.getItem('selectedFlightDetails'));

            let fareRule;
            if (type == 'departure') {
                fareRule = {
                    type: 'fareRule',
                    searchKey: nam1?.searchKey,
                    fareId: nam1?.fareId,
                    flightKey: nam1?.flightKey,
                    requestId: nam1?.Request_Id
                }
            }

            if (type == 'return') {
                fareRule = {
                    type: 'fareRule',
                    searchKey: nam1?.searchKey,
                    fareId: nam1?.returnfareId,
                    flightKey: nam1?.returnflightKey,
                    requestId: nam1?.Request_Id
                }
            }

            $.ajax({
                url: "{{ url('getflight') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: fareRule,
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
                    if (data.statuscode == "TXN") {
                        swal.close();
                        // console.log('success', data?.data?.fareRulesdesc);
                        $('#cancelFlight').modal('show');
                        for (let farerule of data?.data?.fareRulesdesc) {
                            console.log('success', farerule);
                            $("#fareruledata").append(farerule?.FareRuleDesc.replaceAll('--------------------------------------------------', '').replaceAll('-------------------------------------------------', ''));
                        }
                    } else {
                        notify('Something went wrong ! Please Try again later', "error");
                        swal.close();
                    }
                }
            })

        }
    </script>
    @endpush