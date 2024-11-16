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

            <div class="col-lg-12">
                <div class="row mb-2">
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        @if (isset($Booking_RefNo) && ($Booking_RefNo != null || $Booking_RefNo != ''))

                            <div class="iq-card iq-card-block iq-card-stretch iq-card-height batm_custom-card">
                                <div class="card-body flight_card">
                                    <h4 class="card-title">Booking Details</h4>
                                </div>



                                <div class="row" style="padding: 20px;">
                                    {{-- <div class="col-6"> --}}
                                    <div class="form-group">
                                        <label for="exampleInputdate">Booking Ref No</label>
                                        {{-- @dd($Booking_RefNo) --}}
                                        <input type="email" class="form-control" id="refNo"
                                            value="{{ $Booking_RefNo }}" name="refNo" readonly>
                                    </div>
                                    {{-- @dd($Airline_Details); --}}

                                    @foreach (json_decode($Airline_Details) as $row)
                                        @foreach ($row->AirlinePNRs as $key => $val)
                                            <div class="form-group">
                                                <label for="exampleInputdate">Airline Code</label>
                                                {{-- @dd($Booking_RefNo) --}}
                                                <input type="text" class="form-control" id="airlinecode"
                                                    value="{{ $val->Airline_Code }}" name="airlinecode" readonly>
                                            </div>

                                            <div class="form-group">
                                                <label for="exampleInputdate">Airline PNR</label>
                                                {{-- @dd($Booking_RefNo) --}}
                                                <input type="text" class="form-control" id="airlinepnr"
                                                    value="{{ $val->Airline_PNR }}" name="airlinepnr" readonly>
                                            </div>

                                            <div class="form-group">
                                                <label for="exampleInputdate">Status</label>
                                                {{-- @dd($Booking_RefNo) --}}
                                                <input type="text" class="form-control" id="status"
                                                    value="{{ $row->Status_Id == 4 ? 'success' : 'pending' }}"
                                                    name="status" readonly>
                                            </div>
                                        @endforeach
                                    @endforeach



                                    {{-- </div> --}}

                                </div>
                            </div>
                        @else
                            <div class="iq-card iq-card-block iq-card-stretch iq-card-height batm_custom-card">
                                <div class="card-body flight_card">
                                    <h4 class="card-title">Check Booking Details</h4>
                                </div>


                                <form id="checkStatus" method="post" action="{{ url('getflight') }}">
                                    {!! csrf_field() !!}



                                    <div class="row" style="padding: 50px;">
                                        {{-- <div class="col-6"> --}}
                                        <div class="form-group" style="padding-right: 50px;">
                                            <label for="exampleInputdate">Booking Ref No</label>
                                            <input type="text" class="form-control" id="Booking_RefNo"
                                                name="Booking_RefNo">
                                        </div>

                                        <div class="form-group">
                                            <label for="exampleInputdate">Airline PNR</label>
                                            <input type="text" class="form-control" id="airlinepnr" name="airlinepnr">
                                        </div>

                                        <input type="hidden" name="type" value="reprint">

                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal" aria-hidden="true">Close</button>
                                        <button class="btn btn-primary" type="submit"
                                            data-loading-text="<i class='fa fa-spin fa-spinner'></i> Submitting">Check
                                            Status</button>
                                    </div>

                                </form>
                            </div>
                            <button class="btn btn-primary" type="submit"
                                data-loading-text="<i class='fa fa-spin fa-spinner'></i> Submitting">View
                                Details</button>


                            {{-- <div id= class="iq-card iq-card-block iq-card-stretch iq-card-height batm_custom-card">
                                <div class="card-body flight_card">
                                    <h4 class="card-title">Booking Details</h4>
                                </div>

                                <div class="row" style="padding: 20px;">
                            <div class="form-group">
                                <label for="exampleInputdate">Biller Id</label>
                                <input type="text" class="form-control" id="billerId" value="{{ $Biller_Id }}"
                                    name="billerId" readonly>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputdate">Booking RefNo</label>
                                <input type="text" class="form-control" id="bookingRefNo" value="{{ $Booking_RefNo }}"
                                    name="bookingRefNo" readonly>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputdate">Booking Type</label>
                                <input type="text" class="form-control" id="bookingType" value="{{ $Booking_Type }}"
                                    name="bookingType" readonly>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputdate">Travel Class</label>
                                <input type="text" class="form-control" id="travelClass" value="{{ $Class_of_Travel }}"
                                    name="travelClass" readonly>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputdate">Invoice Number</label>
                                <input type="text" class="form-control" id="invoiceNumber"
                                    value="{{ $Invoice_Number }}" name="invoiceNumber" readonly>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputdate">Travel Type</label>
                                <input type="text" class="form-control" id="invoiceNumber"
                                    value="{{ $Travel_Type }}" name="invoiceNumber" readonly>
                            </div>









                    </div> --}}

                            <div class="row mt-5">
                                <div class="col-4"></div>
                                <div class="col-8">
                                    <div class="preview-card-body" id="checkStatus">
                                        <button class="btn btn-primary" type="submit"
                                            data-loading-text="<i class='fa fa-spin fa-spinner'></i> Submitting">View
                                            Details</button>


                                    </div>
                                </div>

                            </div>


                        @endif



                    </div>

                </div>
            </div>


            <div id="cancelmodal" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Invoice</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        {{-- <div style="width: 725px;margin: 0px auto;border:1px solid grey;"> --}}
                        <div class="modal-body" id="flightInvoice">
                            {{-- <p>Modal body text goes here.</p> --}}
                        </div>
                        {{-- </div> --}}
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" id="print" class="btn btn-primary">Print</button>
                        </div>
                    </div>
                </div>
            </div>

        @endsection

        @push('script')
            <script src="{{ asset('/assets/js/core/jQuery.print.js') }}"></script>

            <script>
                // $('#print').click(function() {
                //     $('#flightInvoice').print();
                // });

                $(document).ready(function() {
                    $("#checkStatus").validate({
                        rules: {
                            Booking_RefNo: {
                                required: true,
                            },
                            airlinepnr: {
                                required: true,
                            },

                        },
                        messages: {
                            Booking_RefNo: {
                                required: "Please enter Booking Ref No.",
                            },
                            airlinepnr: {
                                required: "Please enter Airline PNR",

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
                            var form = $('#checkStatus');
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

                                        // console.log(data)
                                        // console.log(data.data.Biller_Id)
                                        // console.log(data.data.BookingPaymentDetail[0].Currency_Code,
                                        //     data.data.BookingPaymentDetail[0].Payment_Amount)

                                        var cont = ` <div style="width: 725px;margin: 0px auto;border:1px solid grey;">
                                        <div>

                                            <div>
                                                <table style="width: 100%;" cellspacing="0">
                                                    <thead>
                                                        <tr>
                                                            <td colspan="2">
                                                                <h3 style="padding-left: 15px;">
                                                                    TAX INVOICE
                                                                </h3>
                                                            </td>`
                                                            // <td style="text-align: center;">
                                                            //     <img src="https://login.amtechpe.in/public/theme/images/logo-full3.png" height="15%" width="30%"/>
                                                            
                                                            // </td>
                                                      cont+=  `</tr>
                                                    </thead>

                                                    <tbody style="color:grey">
                                                        <tr>

                                                            <td style="padding-left: 20px;width:40%">
                                                                <b class="text-content"> BOOKING ID:</b>
                                                                <div class="text-content">${ data.data.Biller_Id }</div>
                                                                <b class="text-content"> DATE :</b>
                                                                <div class="text-content">${ data.data.Booking_DateTime }</div>
                                                                <b class="text-content">DOCUMENT TYPE:</b>
                                                                <div class="text-content">INVOICE</div>
                                                                <b class="text-content">PLACE OF SUPPLY</b>
                                                                <div class="text-content">Choolaimedu</div>
                                                            </td>
                                                            <td style="width: 30%;">
                                                                <b class="text-content">INVOICE NO. :</b>
                                                                <div class="text-content">${ data.data?.Invoice_Number }</div>
                                                                <b class="text-content"> TRANSACTIONAL TYPE/CATEGORY: - </b>
                                                                <div class="text-content">B2C/REG</div>
                                                                <b class="text-content">TANSACTION DETAIL:</b>
                                                                <div class="text-content">RG</div>
                                                            </td>
                                                            <td style="text-align: center;width: 30%;">

                                                                <img src="https://login.amtechpe.in/public/theme/images/logo-full3.png"
                                                                    width="70%" height="70%" />

                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <hr style="text-align: center; width: 95%;" />

                                            <div>
                                                <table style="width: 100%;margin-top: 5px;margin-left: 17px;padding-left: 20px;" cellspacing="0">
                                                    <thead>
                                                        <tr>
                                                            <td colspan="4">
                                                                <b>
                                                                    INVOICE ISSUED FOR FLIGHT
                                                                </b>
                                                            </td>
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        `
                                        for (let x of data.data.AirPNRDetails) {
                                            for (let y of x.Flights) {

                                                cont +=
                                                    `<tr>
                                                    <td style="width:30%">
                                                                <b class="text-content1" style="font-size: 18px;"> ${x.Airline_Code} </b> ${x.Airline_Code} - ${y.Segments[0].Flight_Number}
                                                            </td>
                                                            <td style="width: 80%;">
                                                                
                                                                <b class="text-content">${y.Origin}- ${y.Destination}</b>(${y.TravelDate})<br />`
                                            }
                                        }

                                        // console.log(data.data?.AirPNRDetails);


                                        for (let x of data.data.AirPNRDetails) {
                                            // console.log(x);
                                            for (let y of x.PAXTicketDetails) {
                                                // console.log(y);
                                                cont +=
                                                    `<div class="text-content1">${y.Title} ${y.First_Name}  ${y.Last_Name} (PNR: ${x.Airline_PNR})(Ticket No: ${y.TicketDetails[0].Ticket_Number} )</div>`


                                            }
                                        }

                                        cont += `</td>

                                                        </tr>
                                                        <tr>

                                                            <td style="width:100%" colspan="2">
                                                                <b class="text-content">CUSTOMER NAME/MOBILE</b>
                                                                <div class="text-content1">${ data.data.CustomerDetail.Customer_Name }/${ data.data.CustomerDetail.Customer_Mobile }</div>
                                                                <b class="text-content">BOOKED BY</b>
                                                                <div class="text-content1">${ data.data.CustomerDetail.Customer_Name }/${ data.data.CustomerDetail.Customer_Mobile }</div>
                                                            </td>

                                                        </tr>

                                                    </tbody>
                                                </table>
                                            </div>

                                            <div style="padding-left: 20px;padding-right: 15px;">
                                                <table style="width: 100%;margin-top: 5px; border: 1px solid silver;border-radius: 5px;"
                                                    cellspacing="0">
                                                    <thead>
                                                        <tr>
                                                            <td colspan="4" style="padding-top: 8px;">
                                                                <b  style="margin-left: 10px;">PAYMENT BREAKUP </b>
                                                                <hr style="width: 100%;" />
                                                            </td>
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        <tr>
                                                            <td style="width: 80%;">
                                                                <b class="text-content" style="margin-left: 10px;">*Fare charges</b>
                                                                <div class="text-content" style="margin-left: 10px;">(including applicable flight taxes collected on behalf of
                                                                    airline & other ancillary
                                                                    charges)</div>
                                                            </td>
                                                            <td style="width:30%;text-align:center;">`
                                        // for(let q of data.data.BookingPaymentDetail){

                                        cont +=
                                            `<b class="text-content" >${data.data.BookingPaymentDetail[0].Currency_Code} ${data.data.BookingPaymentDetail[0].Payment_Amount}</b>`
                                        // }
                                        //td>
                                        //</tr>
                                        // <tr>
                                        //     <td style="width: 80%;">
                                        //         <b class="text-content"  style="margin-left: 10px;">AmTechPe Service</b>
                                        //         <div class="text-content" style="margin-left: 10px;">Fees</div>
                                        //     </td>
                                        //     <td style="width:30%;text-align:center;">`
                            //         <b class="text-content"> INR 357.63</b>
                            //     `</td>
                                        // </tr>
                                        // <tr>
                                        //     <td style="width: 80%;">
                                        //         <div class="text-content"  style="margin-left: 10px;">CGST @ 9%</div>
                                        //     </td>
                                        //     <td style="width:30%;text-align:center;">
                                        //         <b class="text-content"> INR 32.19</b>
                                        //     </td>
                                        // </tr>
                                        // <tr>
                                        //     <td style="width: 80%;">
                                        //         <div class="text-content"  style="margin-left: 10px;">SGST @ 9%</div>
                                        //     </td>
                                        //     <td style="width:30%;text-align:center;">
                                        //         <b class="text-content"> INR 32.19</b>
                                        //     </td>
                                        // </tr>

                                        cont += `<tr>

                                                            <td style="width: 80%;">
                                                                <hr style="width: 100%;" />

                                                                <b class="text-content" style="margin-left: 10px;">Total Booking Amount</b>
                                                            </td>
                                                            <td style="width:30%;text-align:center;">
                                                                <hr style="width: 100%;" />
                                                                <b class="text-content"> ${data.data.BookingPaymentDetail[0].Currency_Code} ${data.data.BookingPaymentDetail[0].Payment_Amount}</b>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                    <tfoot>
                                                        <tr style="text-align: center;color: rgb(105, 102, 102);">
                                                            <td colspan="4" style="padding-bottom: 5px;">
                                                                <hr style="width: 100%;" />
                                                                <b >
                                                                    This is a computer generated Invoice and does not require Signature/Stamp.
                                                                </b>
                                                            </td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>

                                            <div style="width: 100%;margin-top:10px;padding-left: 20px; color:grey">
                                                <p style="width: 97%;font-size: 14px;">GST credit charged by the airline operator is only available against the invoice issued by the
                                                    respective airline operator. If you
                                                    are looking for the airline GST invoice, please check the airline website & download it from there.
                                                </p>
                                                <p style="width: 97%;font-size: 14px;">
                                                    Whether the tax is Payable on reverse charge basis: No<br />
                                                    This is not a valid travel document
                                                </p>
                                            </div>

                                            <div>
                                                <table style="width: 100%;margin-top: 5px;margin-left: 17px;padding-left: 20px;" cellspacing="0">
                                                    <thead>
                                                        <tr>
                                                            <td colspan="4">
                                                                <b>
                                                                    Invoice issued by AmTechPe India Pvt. Ltd.
                                                                </b>
                                                            </td>
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        <tr>

                                                            <td style="width:30%">
                                                                <b class="text-content"> PAN:</b>
                                                                <div class="text-content">ABEPE2949K</div>
                                                                <b class="text-content"> GSTN :</b>
                                                                <div class="text-content">33ABZFA1574H1Z5</div>
                                                            </td>
                                                            <td style="width: 20%;">
                                                                <b class="text-content">HSN/SAC:</b>
                                                                <div class="text-content"> - </div>
                                                                <b class="text-content"> CIN:</b>
                                                                <div class="text-content"> - </div>
                                                            </td>
                                                            <td style="width: 30%;">
                                                                <b class="text-content">SERVICE DESCRIPTION:</b>
                                                                <div class="text-content">Reservation Services For Air
                                                                    Transportation</div>
                                                            </td>
                                                            <td style="width: 20%;">
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <div>
                                                <table style="width: 100%;margin-top:10px;margin-left: 17px;margin-bottom: 5px;padding-left: 20px;" cellspacing="0">
                                                    <tr>
                                                        <td style="width: 100%;">
                                                            <b class="text-content">REGISTERED OFFICE</b>
                                                            <div class="text-content1">242, Choolaimedu High Rd, Thiruvenkatapuram, Choolaimedu, CHENNAI,TAMILNADU,600094
                                                            </div>
                                                        </td>
                                                    </tr>

                                                </table>
                                            </div>
                                            </div>
                                            </div>`





                                        $('#flightInvoice').html(cont);

                                        $('#print').click(function() {
                                            $('#flightInvoice').print();
                                        });
                                        // console.log(data.data);
                                        // data =   '${json_decode(data.data)}';
                                        // Show the modal
                                        $('#invoicemodal').modal('show');


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
                });
            </script>
        @endpush
