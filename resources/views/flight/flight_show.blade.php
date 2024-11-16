<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AM techPe</title>
    <link rel="shortcut icon" href="{{asset('')}}theme/images/favicon.ico" />
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="{{asset('')}}theme/css/bootstrap.min.css">
    <!-- Typography CSS -->
    <link rel="stylesheet" href="{{asset('')}}theme/css/typography.css">
    <!-- Style CSS -->
    <link rel="stylesheet" href="{{asset('')}}theme/css/style.css">
    <!-- Responsive CSS -->
    <link rel="stylesheet" href="{{asset('')}}theme/css/responsive.css">

</head>

<body>

    @php
    $i = 1;
    @endphp



    <div class="row">
        <div class="col-4"></div>
        <div class="col-8">
            @foreach ($flight as $row)
            <div class="row">
                <div class="col-12">
                    <div class="iq-card iq-card-block iq-card-stretch iq-card-height iq-mb-1">
                        <div class="card-body">
                            <h6 class="card-title">{{$row->Segments[0]->Airline_Name}}</h6>
                            <div class="row">
                                <div class="col-3">
                                    <span class="card-text" style="font-size: 12px;"><b>{{$row->Segments[0]->Origin}}</b><span class="text-secondary"> {{$row->Segments[0]->Origin_City}}, India</span></span><br>
                                    <h5 class="fw-bold">{{$row->Segments[0]->Departure_DateTime}}</h5>
                                </div>

                                <div class="col-2">
                                    <span class="card-text" style="font-size: 12px;"><b>Duration</b></span><br>
                                    <h5 class="fw-bold">{{$row->Segments[0]->Duration}}
                                    </h5>
                                </div>
                                <div class="col-3">
                                    <span class="card-text" style="font-size: 12px;"><b>{{$row->Segments[0]->Destination}}</b><span class="text-secondary"> {{$row->Segments[0]->Destination_City}}, India</span></span><br>
                                    <h5 class="fw-bold">{{$row->Segments[0]->Arrival_DateTime}}</h5>
                                </div>
                                <div class="col-2">
                                    <span class="card-text" style="font-size: 12px;"></span><br>
                                    <h4 class="fw-bold"> ₹ {{$row->Fares[0]->FareDetails[0]->Total_Amount}}</h4>
                                </div>

                                <div class="col-2">
                                    <span class="card-text" style="font-size: 12px;"></span><br>
                                    <button class="btn" style="background-color: rgb(255, 109, 56); color:rgb(255, 255, 255);padding: 7px 10px;">View
                                        Fares</button>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-12 text-end">
                                    <a data-toggle="collapse" href="#collapseExample_{{$i}}" role="button" aria-expanded="false" aria-controls="collapseExample"> View Flight Details</a>
                                </div>
                            </div>

                            <div class="collapse" id="collapseExample_{{$i}}">
                                <div class="card card-body">
                                    <ul class="nav nav-tabs" id="myTab-1" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="flight-tab" data-toggle="tab" href="#flight_details_{{$i}}" role="tab" aria-controls="flight" aria-selected="true">FLIGHT DETAILS</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="fare-tab" data-toggle="tab" href="#fare_details_{{$i}}" role="tab" aria-controls="fare" aria-selected="false">Fare Details</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="cancellation-tab" data-toggle="tab" href="#cancellation_{{$i}}" role="tab" aria-controls="contact" aria-selected="false">Cancellation</a>
                                        </li>

                                        <li class="nav-item">
                                            <a class="nav-link" id="cancellation-tab" data-toggle="tab" href="#dateChange_{{$i}}" role="tab" aria-controls="contact" aria-selected="false">Reschedule Charges</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content" id="myTabContent-2">
                                        <div class="tab-pane fade show active" id="flight_details_{{$i}}" role="tabpanel" aria-labelledby="home-tab">
                                            <div class="iq-card iq-card-block iq-card-stretch iq-card-height iq-mb-3">
                                                <div class="card-body">
                                                    <h6 class="card-title">{{$row->Segments[0]->Airline_Name}} {{$row->Segments[0]->Airline_Code}} |
                                                        {{$row->Segments[0]->Flight_Number}}
                                                    </h6>
                                                    <div class="row">
                                                        <div class="col-3">
                                                            <h4 class="fw-bold">{{$row->Segments[0]->Departure_DateTime}}</h4>
                                                            <h6 class="fw-bold">{{$row->Segments[0]->Arrival_DateTime}}</h6>
                                                            <span class="card-text" style="font-size: 12px;"><span class="text-secondary">
                                                                    {{$row->Segments[0]->Origin_City}}, India</span></span><br>
                                                            <span class="card-text" style="font-size: 14px;">Terminal
                                                                {{$row->Segments[0]->Origin_Terminal}}</span>

                                                        </div>

                                                        <div class="col-2">
                                                            <span class="card-text" style="font-size: 12px;"><b>Duration</b></span><br>
                                                            <h5 class="fw-bold">{{$row->Segments[0]->Duration}}
                                                                {{$row->Segments[0]->Duration}}
                                                            </h5>
                                                        </div>
                                                        <div class="col-3">
                                                            <h4 class="fw-bold">{{$row->Segments[0]->Arrival_DateTime}}</h4>
                                                            <h6 class="fw-bold">{{$row->Segments[0]->Arrival_DateTime}}</h6>
                                                            <span class="card-text" style="font-size: 12px;"><span class="text-secondary">
                                                                    {{$row->Segments[0]->Destination_City}}, India</span></span><br>
                                                            <span class="card-text" style="font-size: 14px;">Terminal
                                                                {{$row->Segments[0]->Destination_Terminal}}</span>

                                                        </div>
                                                        <div class="col-2">
                                                            <h5>CHECK IN</h5><br>
                                                            @if(!empty($row->Fares[0]->FareDetails[0]->Free_Baggage->Check_In_Baggage))
                                                            <h6 class="fw-bold">
                                                                {{$row->Fares[0]->FareDetails[0]->Free_Baggage->Check_In_Baggage}} (1 piece
                                                                only)
                                                            </h6>

                                                            @endif

                                                        </div>

                                                        <div class="col-2">
                                                            <h5>CABIN:</h5><br>
                                                            @if(!empty($row->Fares[0]->FareDetails[0]->Free_Baggage->Hand_Baggage))
                                                            <h6 class="fw-bold">{{$row->Fares[0]->FareDetails[0]->Free_Baggage->Hand_Baggage}}
                                                                (1 piece only)</h6>

                                                            @endif
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="fare_details_{{$i}}" role="tabpanel" aria-labelledby="profile-tab">
                                            <div class="iq-card iq-card-block iq-card-stretch iq-card-height iq-mb-3">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-3">Base Fare</div>
                                                        <div class="col-3">₹ {{$row->Fares[0]->FareDetails[0]->Basic_Amount}}</div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-3">Taxes and Fees </div>
                                                        <div class="col-3">₹ {{$row->Fares[0]->FareDetails[0]->AirportTax_Amount}}</div>
                                                    </div>
                                                    <div class="row font-weight-bold">
                                                        <div class="col-3 text -bold">Total</div>
                                                        <div class="col-3 text -bold">₹ {{$row->Fares[0]->FareDetails[0]->Total_Amount}}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="cancellation_{{$i}}" role="tabpanel" aria-labelledby="contact-tab">
                                            <div class="iq-card-body">
                                                <div class="table-responsive">
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                <th scope="col">Time frame</th>
                                                                <th scope="col">Airline Fee + MMT Fee</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>

                                                            @foreach($row->Fares[0]->FareDetails[0]->CancellationCharges as $canceldetails)

                                                            <tr>
                                                                <th scope="row">{{$canceldetails->DurationFrom}} to
                                                                    {{$canceldetails->DurationTo}}
                                                                </th>
                                                                <td> ₹ {{$canceldetails->Value}}</td>
                                                            </tr>
                                                            @endforeach

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-pane fade" id="dateChange_{{$i}}" role="tabpanel" aria-labelledby="contact-tab">
                                            <div class="iq-card-body">
                                                <div class="table-responsive">
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                <th scope="col">Time frame</th>
                                                                <th scope="col">Airline Fee + MMT Fee</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>

                                                            @foreach($row->Fares[0]->FareDetails[0]->RescheduleCharges as $dateChange)
                                                            <tr>
                                                                <th scope="row">{{$dateChange->DurationFrom}} to
                                                                    {{$dateChange->DurationTo}}
                                                                </th>
                                                                <td>₹ {{$dateChange->Value}}</td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @php
            $i++;
            @endphp
            @endforeach
        </div>
    </div>




    <script src="{{asset('')}}theme/js/jquery.min.js"></script>
    <script src="{{asset('')}}theme/js/popper.min.js"></script>
    <script src="{{asset('')}}theme/js/bootstrap.min.js"></script>
    <script src="{{asset('')}}theme/js/jquery.appear.js"></script>
    <script src="{{asset('')}}theme/js/countdown.min.js"></script>
    <script src="{{asset('')}}theme/js/waypoints.min.js"></script>
    <script src="{{asset('')}}theme/js/jquery.counterup.min.js"></script>
    <script src="{{asset('')}}theme/js/wow.min.js"></script>
    <script src="{{asset('')}}theme/js/apexcharts.js"></script>
    <script src="{{asset('')}}theme/js/lottie.js"></script>
    <script src="{{asset('')}}theme/js/slick.min.js"></script>
    <script src="{{asset('')}}theme/js/select2.min.js"></script>
    <script src="{{asset('')}}theme/js/owl.carousel.min.js"></script>
    <script src="{{asset('')}}theme/js/charts.js"></script>
    <script src="{{asset('')}}theme/js/jquery.magnific-popup.min.js"></script>
    <script src="{{asset('')}}theme/js/smooth-scrollbar.js"></script>
    <script src="{{asset('')}}theme/js/style-customizer.js"></script>
    <script src="{{asset('')}}theme/js/chart-custom.js"></script>
    <script src="{{asset('')}}theme/js/custom.js"></script>
    <script src="{{asset('')}}assets/js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
    <script type="text/javascript" src="{{asset('')}}assets/js/core/jquery.validate.min.js"></script>
    <script type="text/javascript" src="{{asset('')}}assets/js/core/jquery.form.min.js"></script>
    <script type="text/javascript" src="{{asset('')}}assets/js/core/sweetalert2.min.js"></script>
    <script type="text/javascript" src="{{asset('')}}assets/js/plugins/forms/selects/select2.min.js"></script>
    <script src="{{asset('')}}assets/js/core/snackbar.js"></script>
    <script src="{{asset('')}}theme/js/worldLow.js"></script>
    <script src="{{asset('')}}theme/js/kelly.js"></script>
    <script src="{{asset('')}}theme/js/maps.js"></script>
    <script src="{{asset('')}}theme/js/core.js"></script>
    <script src="{{asset('')}}theme/js/animated.js"></script>
    <script>

    </script>
</body>

</html>