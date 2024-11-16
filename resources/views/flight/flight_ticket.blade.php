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
        @else
        <div class="col-lg-12">
            <div class="row mb-2">
                @if (isset($Booking_RefNo) && ($Booking_RefNo != null || $Booking_RefNo != ''))
                <div class="col-sm-12 col-md-12 col-lg-12">
                    <div class="iq-card iq-card-block iq-card-stretch iq-card-height batm_custom-card">
                        <div class="card-header flight_card">
                            <h4 class="card-title ">Booking Details</h4>
                        </div>
                        <div class="row" style="padding: 20px;">

                            <table class="table table-bordered table-striped w-100">
                                <tbody>
                                    <tr>
                                        <th>Booking Ref No</th>
                                        <th>Airline Code</th>
                                        <th>Airline PNR</th>
                                        <th>Record Locator</th>
                                        <th style="display:none;">CRS Code</th>
                                        <th style="display:none;">CRS PNR</th>
                                        <th style="display:none;">Supplier RefNo</th>
                                        <th style="display:none;">Hold Validity</th>
                                        <th>Status/Remark</th>
                                    </tr>
                                    @foreach ($Airline_Details as $row)
                                    @foreach ($row->AirlinePNRs as $key => $val)
                                    <tr>
                                        <td id="refNo">{{ $Booking_RefNo }}</td>
                                        <td id="airlinecode">{{ $val->Airline_Code }}</td>
                                        <td id="airlinepnr">{{ $val->Airline_PNR }}</td>
                                        <td id="recordlocator">{{ $val->Record_Locator }}</td>
                                        <td id="crscode" style="display:none;">{{ $val->CRS_Code }}
                                        </td>
                                        <td id="crspnr" style="display:none;">{{ $val->CRS_PNR }}
                                        </td>
                                        <td id="supplierrefno" style="display:none;">
                                            {{ $val->Supplier_RefNo }}
                                        </td>
                                        <td id="holdvalidity" style="display:none;">
                                            {{ $row->Hold_Validity }}
                                        </td>
                                        <td id="status">
                                            {{ $row->Status_Id == 4 ? 'Confirmed' : 'pending' }}
                                            {{ $row->Failure_Remark }}
                                        </td>
                                    </tr>
                                    @endforeach
                                    @endforeach

                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
                @else
                <div class="col-sm-12 col-md-12 col-lg-12">
                    <div class="iq-card iq-card-block iq-card-stretch iq-card-height batm_custom-card">
                        <div class="card-header flight_card">
                            <h4 class="card-title">Check Booking Details</h4>
                        </div>


                        <form id="checkStatus" method="post" action="{{ url('getflight') }}">
                            {!! csrf_field() !!}



                            <div class="row" style="padding: 20px;margin-left:20px;">
                                {{-- <div class="col-6"> --}}
                                <div class="form-group" style="padding-right: 50px;">
                                    <label for="exampleInputdate">Booking Ref No</label>
                                    <input type="text" class="form-control" id="Booking_RefNo" name="Booking_RefNo">
                                </div>

                                <div class="form-group">
                                    <label for="exampleInputdate">Airline PNR</label>
                                    <input type="text" class="form-control" id="airlinepnr" name="airlinepnr">
                                </div>

                                <input type="hidden" name="type" value="reprint">

                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-hidden="true">Close</button>
                                <button class="btn btn-primary" type="submit" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Submitting">Check
                                    Status</button>
                            </div>

                        </form>
                    </div>
                </div>

                <!-- table portion -->
                <div class="col-sm-12 col-md-12 col-lg-12" id="previewStatus"></div>
                @endif
            </div>
        </div>
        @endif
    </div>

    <div id="invoicemodal" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
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

    <div id="cancalModal" class="modal fade" role="dialog" data-backdrop="false">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Cancellation Booking<span class="payeename text-capitalize"></span> </h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>

                </div>

                <form id="CancelConfirmationForm" method="post" action="{{ url('getflight') }}">
                    <div class="modal-body">
                        {!! csrf_field() !!}
                        <div class="form-group">
                            <label for="exampleInputdate">Booking Ref No.</label>
                            <input type="text" class="form-control" name="Booking_RefNo" value="" readonly>
                        </div>

                        {{-- <div class="form-group">
                                <label for="exampleInputdate">Passenger Id</label>
                                <input type="text" class="form-control" name="passengerId">
                            </div> --}}

                        <div class="form-group">
                            <label for="exampleInputdate">Cancel Reason</label>
                            <input type="text" class="form-control" name="cancelRemarks" required>
                        </div>
                        <div class="form-group">
                            <input type="radio" id="confirmation" name="" value="" required>
                            <label for="exampleInputdate">I Provide my concent to cancel my booking flight</label>


                        </div>

                        <input type="hidden" name="type" value="cancellation">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-hidden="true">Close</button>
                        <button class="btn btn-primary" type="submit" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Submitting">Confirm
                            Cancellation</button>
                    </div>
                </form>
            </div>
        </div>
    </div><!-- /.modal -->


    @endsection
    @push('script')
    <script src="{{ asset('/assets/js/core/jQuery.print.js') }}"></script>

    <script>
        // $('#print').click(function() {
        //     $('#flightInvoice').print();
        // });
        // Show the modal
        function openModal() {
            $('#invoicemodal').modal('show');
        }

        const currentDate = new Date();

        const year = currentDate.getFullYear();
        const month = (currentDate.getMonth() + 1).toString().padStart(2, '0'); // Month is zero-based, so add 1
        const day = currentDate.getDate().toString().padStart(2, '0');
        const formattedDate = `${month}/${day}/${year}`;

        function getDateDiff(to, from) {
            const date1 = new Date(to);
            const date2 = new Date(from);
            const diffTime = Math.abs(date2 - date1);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            return diffDays;
        }

        // console.log(getDateDiff(formattedDate,"09/07/2023"));

        function cancelconfirm(b_RefNo) {
            $('#cancalModal').find('input[name="Booking_RefNo"]').val(b_RefNo);
            // $('#bookingModal').find('input[name="amount"]').val(amount);
            // $('#bookingModal').find('input[name="requestId"]').val(req_id);
            // $('#bookingModal').find('input[name="requestId"]').val(req_id);
            $('#cancalModal').modal();

        }

        $(document).ready(function() {

            var nam = localStorage.getItem('selectedFlightDetails');
            nam = JSON.parse(nam)
            // console.log('Request_Id', nam?.baseAmount);
            var base_amount = nam?.baseAmount;
            var tax_amount = nam?.tax;

            // $(document).ready(function() {

            $("#CancelConfirmationForm").submit(function(event) {
                event.preventDefault(); // Prevent the default form submission

                var form = $('#CancelConfirmationForm');
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
                        $('#cancalModal').modal('hide');

                        if (data.statuscode === "TXN" || data.statuscode === "success") {
                            // swal.close();
                            swal.close();
                            notify("Flight Cancel Successfully, Please check status after sometime to confirmation ",
                                'success');
                        } else {
                            if (!data.message) {
                                notify("Try again later", 'error');
                            } else {
                                notify(data.message, 'error');
                            }
                        }
                    },
                    error: function(xhr, textStatus, errorThrown) {
                        showError(xhr, form);
                        notify("An error occurred. Please try again.", 'error');
                    }
                });
            });


            $("#checkStatus").validate({
                rules: {
                    Booking_RefNo: {
                        required: true,
                    },
                    airlinepnr: {
                        required: false,
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
                            // console.log(data);
                            form.find('button[type="submit"]').button('reset');
                            swal.close();
                            if (data.statuscode == "TXN" || data.statuscode == "success") {

                                let previewData =
                                    `  <div class="iq-card iq-card-block iq-card-stretch iq-card-height batm_custom-card">
                                        <h4 class="card-title mx-1 p-4"><b>Booking Details</b></h4>
                                        <table class="table table-responsive text-center w-100">

                                            <tbody>
                                                <tr>
                                                    <th width="10%">Biller Id</th>
                                                    <th width="10%">Ref No</th>
                                                    <th width="15%">Booking DateTime</th>
                                                    <th width="15%">Booking Type</th>
                                                    <th width="15%">Passenger Email Id</th>
                                                    <th width="10%">Passenger Mobile</th>
                                                    <th width="10%">Travel Type</th>
                                                    <th width="10%">Ticket Status</th>
                                                    <th width="15%">Action</th>
                                                </tr>
                                                <tr>
                                                    <td>${data?.data?.Biller_Id}</td>
                                                    <td> ${data?.data?.Booking_RefNo}</td>
                                                    <td> ${data?.data?.Booking_DateTime}</td>
                                                    <td> ${data?.data?.Booking_Type}</td>
                                                    <td> ${data?.data?.Passenger_EmailId}</td>
                                                    <td> ${data?.data?.Passenger_Mobile}</td>
                                                    <td> ${data?.data?.Travel_Type}</td>
                                                    <td> ${(data?.data?.Ticket_StatusCode == "4" || data?.data?.Ticket_StatusCode == "success")? '<span class="badge badge-success">Confirmed</span>' : '<span class="badge badge-warning">Pending</span>'}</td>
                                                    <td>
                                                    <button class="btn btn-primary btn-xs mb-1" type="button" onclick="openModal()" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Submitting">View</button>`;
                                // console.log(getDateDiff(data?.data?.AirPNRDetails[0]
                                //     .Flights[0].TravelDate, formattedDate));

                                if (getDateDiff(data?.data?.AirPNRDetails[0].Flights[0]
                                        .TravelDate, formattedDate) > 1) {
                                    previewData +=
                                        `<button class="btn btn-secondary btn-xs mt-1" type="button"  onclick="cancelconfirm('${data?.data?.Booking_RefNo}')" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Submitting">Cancel Flight</button>`;
                                }

                                previewData += `</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                </div>`;


                                $('#previewStatus').html(previewData);


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
                                cont += `</tr>
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
                                for (let x of data?.data?.AirPNRDetails) {
                                    for (let y of x.Flights) {

                                        cont +=
                                            `<tr><td style="width:30%">
                                                                <b class="text-content1" style="font-size: 18px;"> ${x.Airline_Code} </b> ${x.Airline_Code} - ${y.Segments[0].Flight_Number}
                                                            </td>
                                                            <td style="width: 70%;">
                                                                
                                                                <b class="text-content">${y.Origin}- ${y.Destination}</b>(${y.TravelDate})<br />`
                                    }
                                    for (let y of x.PAXTicketDetails) {

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
                                                                <div class="text-content1">${ data.data.RetailerDetail.BookedBy_Operator_Name }/${ data.data.RetailerDetail.Retailer_Mobile_Number }</div>
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
                                    `<b class="text-content" >${data.data.BookingPaymentDetail[0].Currency_Code} ${data.data.AirPNRDetails[0].Gross_Amount}</b>`
                                // }
                                // cont +=
                                //     `<b class="text-content" >${base_amount}</b>
                                //                         }
                                //                         td>
                                //                         </tr>`
                                //     <tr>
                                //         <td style="width: 80%;">
                                //             <b class="text-content"  style="margin-left: 10px;">AmTechPe Service</b>
                                //             <div class="text-content" style="margin-left: 10px;">Fees</div>
                                //         </td>
                                //         <td style="width:30%;text-align:center;">
                                //     <b class="text-content"> INR 357.63</b>
                                // </td>
                                //     </tr>
                                // cont += `<tr>
                                //                             <td style="width: 80%;">
                                //                                 <div class="text-content"  style="margin-left: 10px;">Tax Amount</div>
                                //                             </td>
                                //                             <td style="width:30%;text-align:center;">
                                //                                 <b class="text-content"> INR ${tax_amount}</b>
                                //                             </td>
                                //                         </tr>`
                                // <tr>
                                //     <td style="width: 80%;">
                                //         <div class="text-content"  style="margin-left: 10px;">SGST @ 9%</div>
                                //     </td>
                                //     <td style="width:30%;text-align:center;">
                                //         <b class="text-content"> INR 32.19</b>
                                //     </td>
                                // </tr>`

                                cont += `<tr>

                                                                                    <td style="width: 80%;">
                                                                                        <hr style="width: 100%;" />

                                                                                        <b class="text-content" style="margin-left: 10px;">Total Booking Amount</b>
                                                                                    </td>
                                                                                    <td style="width:30%;text-align:center;">
                                                                                        <hr style="width: 100%;" />
                                                                                        <b class="text-content"> ${data.data.BookingPaymentDetail[0].Currency_Code} ${data.data.AirPNRDetails[0].Gross_Amount}</b>
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
                                                                                        <div class="text-content">44225586</div>
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
                                                                    </div>`;

                                $('#flightInvoice').html(cont);

                                $('#print').click(function() {
                                    $('#flightInvoice').print();
                                });
                                // console.log(data.data);
                                // data =   '${json_decode(data.data)}';

                                notify("flight details fetch successfully", 'success');

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

            $('#sidebarhide').addClass('open');
            $('.iq-page-menu-vertical').addClass('sidebar-main');
        });
    </script>
    @endpush