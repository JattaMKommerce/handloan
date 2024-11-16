@extends('layouts.app')
@section('title', 'Book Flight')
@section('pagetitle', 'Book Flight')
@section('content')

    <style>
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

            @if (isset($statuscode) && ($statuscode == 'TXF' || $statuscode == 'TXF'))
                <script>
                    swal({
                        title: 'Failed',
                        text: `{{ $message }}`,
                        // onOpen: () => {
                        //     swal.showLoading()
                        // },
                        showConfirmButton: true,
                        allowOutsideClick: () => !swal.isLoading()
                    });
                </script>
            @endif


            <div class="col-lg-12">
                <div class="row mb-2">
                    <div class="col-sm-12 col-md-12 col-lg-12">

                        <div class="iq-card iq-card-block iq-card-stretch iq-card-height batm_custom-card">
                            <div class="card-body flight_card">
                                <h4 class="card-title">Flight Booking Service</h4>
                            </div>


                            <form id='pa_details' action="{{ route('getflight') }}" method="post">
                                {{ csrf_field() }}


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
                                            <input type="email" class="form-control" id="passengerEmail" value=""
                                                name="passengerEmail">
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="form-group" type="hidden">
                                            <label for="exampleInputdate">WhatsApp/Mobile Number</label>
                                            <input type="number" class="form-control" id="whatsappMobile" value=""
                                                name="whatsappMobile">
                                        </div>
                                    </div>
                                </div>
                                {{-- <div class="passangerDetails" style="padding: 10px;">

                                <div class="row mt-1">
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label>Title</label>
                                            <select class="form-control mb-3" id="travel_class" name="travelClass">
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
                                            <select class="form-control mb-3" id="travel_type" name="travelType">
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
                                            <select class="form-control mb-3" id="travel_type" name="travelType">
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
                                <div id="passengerDetails" name="passengerDetails[]" style="padding: 10px;"></div>
                                <div class="d-flex align-items-center justify-content-center">
                                    <button type="submit" class="btn btn-primary mb-3 mr-3">Book</button><br>
                                </div>

                            </form>


                            <div class="d-flex align-items-center justify-content-center">
                                {{-- <button type="submit" class="btn btn-primary mb-3 mr-3"
                                    onclick="bookflight()">Book</button><br> --}}
                                <!-- <button type="submit" class="btn btn-primary mb-3">Search</button> -->

                                <button id="addpassenger" type="button" onclick="addnewdetails()"
                                    class="btn btn-primary mb-3"> Add Details
                                    {{-- class="button_plus"> --}}
                                </button>

                            </div>

                        </div>
                    </div>



                </div>

            </div>
        </div>

        <div id="bookingModal" class="modal fade" role="dialog" data-backdrop="false">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header ">
                        <h5 class="modal-title">Make Payment<span class="payeename text-capitalize"></span> </h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>

                    </div>

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
                            <button type="button" class="btn btn-secondary" data-dismiss="modal"
                                aria-hidden="true">Close</button>
                            <button class="btn btn-primary" type="submit"
                                data-loading-text="<i class='fa fa-spin fa-spinner'></i> Submitting">Pay Now</button>
                        </div>
                    </form>
                </div>
            </div>
        </div><!-- /.modal -->


        <div id="confirmBookingModal" class="modal fade" role="dialog" data-backdrop="false">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header ">
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
                            <button type="button" class="btn btn-secondary" data-dismiss="modal"
                                aria-hidden="true">Close</button>
                            <button class="btn btn-primary" type="submit"
                                data-loading-text="<i class='fa fa-spin fa-spinner'></i> Submitting">Confirm
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
            $(document).ready(function() {

                var nam = localStorage.getItem('selectedFlightDetails');

                nam = JSON.parse(nam)
                console.log('Request_Id', nam?.baseAmount);


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

                // Pass the data to the form
                document.getElementById('travelInfo').value = travelInfo;
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

                // console.log(Request_Id,adultCount,bookingType,fareId,flightKey,infantCount,searchKey,total_amount,travelClass,travelType);


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
                                form.find('button[type="submit"]').button('loading');
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
                                form.find('button[type="submit"]').button('reset');
                                swal.close();

                                if (data.statuscode == "TXN" || data.statuscode == "success") {
                                    // console.log(data.data.Booking_RefNo);
                                    // console.log(data);
                                    // console.log(data.data);
                                    payment(data.data.Booking_RefNo, data.data.Amount, data.data
                                        .Request_Id)

                                    notify("Passenger details Successfully Submitted",
                                        'success');
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
            });





            function addnewdetails() {
                var newRowAdd = `
                        <div class="row mt-1"> 
                        <div class="col-2"> 
                        <div class="form-group"> 
                        <label>Title</label> 
                        <select class="form-control mb-3" id="p_type" name="p_type[]"> 
                        <option selected="">Select Title</option> 
                        <option value="Mr">Mr</option> 
                        <option value="Miss">Miss</option> 
                        <option value="Mrs">Mrs</option> 
                        </select> 
                        </div> 
                        </div> 
                        <div class="col-2"> 
                        <div class="form-group"> 
                        <label>Gender</label> 
                        <select class="form-control mb-3" id="gender" name="gender[]"> 
                        <option selected="">Select Gender</option> 
                        <option value="0">Male</option> 
                        <option value="1">Female</option> 
                        </select> 
                        </div> 
                        </div> 

                        <div class="col-2"> 
                        <div class="form-group"> 
                        <label>Traveller Type</label> 
                        <select class="form-control mb-3" id="travel_type" name="travel_type[]"> 
                        <option selected="">Select Type</option> 
                        <option value="0">Adult (Above 12y)</option> 
                        <option value="1">Child (2y-12y)</option> 
                        <option value="2">Infant(below 2 years)</option> 
                        </select> 
                        </div> 
                        </div> 

                        <div class="col-2"> 
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

                        <div class="col-2"> 
                        <div class="form-group"> 
                        <label for="exampleInputdate">Passenger Nationality</label> 
                        <input type="text" class="form-control" id="nationality" name="nationality[]"> 
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
                        <label for="exampleInputdate">Passport Number</label> 
                        <input type="text" class="form-control" id="passportNumber" name="passportNumber[]"> 
                        </div> 
                        </div> 

                        <div class="col-2"> 
                        <div class="form-group"> 
                        <label for="exampleInputdate">Passport Issuing Country</label> 
                        <input type="text" class="form-control" id="passport_issuing_country" name="passport_issuing_country[]"> 
                        </div> 
                        </div> 

                        <div class="col-2"> 
                        <div class="form-group"> 
                        <label for="exampleInputdate">Passport Expiry</label> 
                        <input type="date" class="form-control" id="passport_expiry" name="passport_expiry[]"> 
                        </div> 
                        </div> 

                         
                        </div>`

                $('#passengerDetails').append(newRowAdd);
            };

            function payment(b_RefNo, amount, req_id) {
                // console.log(req_id);
                $('#bookingModal').find('input[name="bookingRefNo"]').val(b_RefNo);
                $('#bookingModal').find('input[name="amount"]').val(amount);
                $('#bookingModal').find('input[name="requestId"]').val(req_id);
                // $('#bookingModal').find('input[name="requestId"]').val(req_id);
                $('#bookingModal').modal();
            }

            function confirmBook(b_RefNo) {
                $('#confirmBookingModal').find('input[name="Booking_RefNo"]').val(b_RefNo);
                // $('#bookingModal').find('input[name="amount"]').val(amount);
                $('#confirmBookingModal').modal();
            }
        </script>
    @endpush
