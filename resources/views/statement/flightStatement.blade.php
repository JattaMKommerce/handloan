@extends('layouts.app')
@section('title', 'Flight Statement')
@section('pagetitle', 'Flight Statement')

@php
    $table = 'yes';
    $export = '';
    
    // // $billers = App\Models\Provider::whereIn('type', ['electricity'])->get(['id', 'name']);
    // foreach ($billers as $item){
    // $product['data'][$item->id] = $item->name;
    // }
    // $product['type'] = "Biller";
    
    // $status['type'] = "Report";
    // $status['data'] = [
    // "success" => "Success",
    // "pending" => "Pending",
    // "reversed" => "Reversed",
    // "refunded" => "Refunded",
    // ];
    
@endphp

@section('content')

    <div class="content">
        <div class="row">
            <div class="col-sm-12">
                <div class="iq-card">
                    <div class="iq-card-body">
                        <div class="table-responsive">
                            <table class="table" id="datatable">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Id/Booking DateTime</th>
                                        <th>User Details</th>
                                        <th>Booking Ref No</th>
                                        {{-- <th>Booking Date Time</th> --}}
                                        <th>Booking Type</th>
                                        <th>Passenger EmailId</th>
                                        <th>Passenger Mobile</th>
                                        <th>Passenger Travel_Type</th>
                                        {{-- <th>Ticket Status</th> --}}
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade bd-example-modal-lg" id="invoicemodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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

    <div class="modal fade" id="bookingModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header ">
                    <h4 class="modal-title">Make Payment<span class="payeename text-capitalize"></span>
                    </h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>

                </div>
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

                <form id="paymentForm" method="post" action="{{ url('getflight') }}">
                    <div class="modal-body">
                        {!! csrf_field() !!}
                        <div class="form-group">
                            <label for="exampleInputdate">Booking Ref No.</label>
                            <input type="text" class="form-control" name="bookingRefNo">
                        </div>

                        {{-- <div class="form-group">
                            <label for="exampleInputdate">Amount</label>
                            <input type="text" class="form-control" name="amount" value="" readonly>
                        </div> --}}
                        <input type="hidden" name="type" value="addPayment">
                        <input type="hidden" name="requestId" value="">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"
                            aria-hidden="true">Close</button>
                        <button class="btn btn-primary " type="submit"
                            data-loading-text="<i class='fa fa-spin fa-spinner'></i> Submitting">Pay Now</button>
                    </div>
                </form>
            </div>
        </div>
    </div><!-- /.modal -->

    <div class="modal fade" id="bookingModal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title ">Confirm Booking<span class="payeename text-capitalize"></span>
                    </h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>

                </div>
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

                <form id="bookingForm2" method="post" action="{{ url('getflight') }}">
                    <div class="modal-body">
                        {!! csrf_field() !!}
                        <div class="form-group">
                            <label for="exampleInputdate">Booking Ref No.</label>
                            <input type="text" class="form-control" name="bookingRefNo" value="" readonly>
                        </div>

                        {{-- <div class="form-group">
                            <label for="exampleInputdate">Amount</label>
                            <input type="text" class="form-control" name="amount" value="" readonly>
                        </div> --}}
                        <input type="hidden" name="type" value="ticket">
                        <input type="hidden" name="requestId" value="">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"
                            aria-hidden="true">Close</button>
                        <button class="btn btn-primary " type="submit"
                            data-loading-text="<i class='fa fa-spin fa-spinner'></i> Submitting">Confirm Ticket</button>
                    </div>
                </form>
            </div>
        </div>
    </div><!-- /.modal -->
@endsection

@push('style')
@endpush

@push('script')
    <script type="text/javascript">
        function openModal(b_refno) {
            console.log(b_refno);
            $.ajax({
                url: `{{ url('getflight') }}`,
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                data: {
                    'type': "reprint",
                    "Booking_RefNo": b_refno
                },
                beforeSend: function() {
                    swal({
                        title: 'Wait!',
                        text: 'We are fetching deatils.',
                        allowOutsideClick: () => !swal.isLoading(),
                        onOpen: () => {
                            swal.showLoading()
                        }
                    });
                },
                success: function(data) {
                    swal.close();
                    console.log(data)
                    if (data.statuscode == "TXN" || data.statuscode == "success") {

                        if (data.data.AirPNRDetails[0].Ticket_Status_Id == '4') {

                            let previewData;





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
                                        `<tr>
                                        <td style="width:30%">
                            <b class="text-content1" style="font-size: 18px;"> ${x.Airline_Code} </b> ${x.Airline_Code} - ${y.Segments[0].Flight_Number}
                        </td>
                        <td style="width: 80%;">
                            
                            <b class="text-content">${y.Origin}- ${y.Destination}</b>(${y.TravelDate})<br />`
                                }
                            }

                            // console.log(data.data?.AirPNRDetails);


                            for (let x of data?.data?.AirPNRDetails) {
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

                        cont +=
                            `<tr>

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
                        $('#invoicemodal').modal('show');

                    } else {
                        swal({
                            title: 'Error',
                            text: 'Ticket ' + data.data.AirPNRDetails[0].Ticket_Status_Desc,
                            // onOpen: () => {
                            //     swal.showLoading()
                            // },
                            showConfirmButton: true,
                            allowOutsideClick: () => !swal.isLoading()
                        });

                    }
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


        });

        // .error: function(errors) {
        //     showError(errors, form);
        //     notify("Try after Sometimes1", 'error');
        // };



    }

    $(document).ready(function() {
        var url = "{{ url('statement/fetch') }}/flightStatement/{{ $id }}";
        // $('#print').click(function() {
        //     $('#receptTable').print();
        // });

        var onDraw = function() {
            // $('.print').click(function(event) {
            //     var data = DT.row($(this).parent().parent().parent().parent().parent()).data();
            //     $.each(data, function(index, values) {
            //         $("." + index).text(values);
            //     });
            //     $('#receipt').modal();
            // });
        };

        var options = [{
                "data": "id",
                render: function(data, type, full, meta) {
                    return `<div><span class='text-inverse m-l-10'><b>` +
                        full.id +
                        // (full.id == null || full.id == undefined )? "":
                        `</b> </span><div class="clearfix"></div></div><span style='font-size:13px' class="pull=right">` +
                        full.created_at + `</span>`;
                    // (full.created_at == null || full.created_at == undefined) ?"":
                }
            },
            {
                "data": "username"
            },
            {
                "data": "booking_refno"
            },
            {
                "data": "booking_type",
                render: function(data, type, full, meta) {

                    return checkType('booking', full.booking_type);

                    // console.log(checkType('booking', full.booking_type));
                }
            },
            {
                "data": "passenger_email",

            },
            {
                "data": "passenger_mobile",

            },
            {
                "data": "travel_type",
                render: function(data, type, full, meta) {
                    return checkType('travel', full.travel_type);
                }
            },
            // {
            //     "data": "status",
            //     render: function(data, type, full, meta) {
            //         // return checkType('travel', full.travel_type);
            //         var status;
            //         if (full.status == null || full.status == "") {
            //             status = "";
            //         } else {
            //             status = full.status;
            //         }

            //         return status;
            //     }
            // },
            {
                "data": "name",
                render: function(data, type, full, meta) {
                    // return checkType('travel', full.travel_type);
                    cntbutton =
                        `<button class="btn btn-primary btn-sm mb-1" type="button" onclick="openModal('${full.booking_refno}')" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Submitting" style="margin-right: 6px;">View</button >`;
                    @if (Myhelper::hasNotRole('admin'))
                        if (full.is_payment == '0') {
                            cntbutton +=
                             `<button class="btn btn-primary btn-sm mb-1" type="button" onclick="makepayments('${full.booking_refno}','${full.request_id}')" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Submitting" style="font-size: 13px !important;">Make Payment</button>`;
                        }
                        if (full.is_ticket_booked == '0' && full.is_payment == '1') {
                            cntbutton +=
                                `<button class="btn btn-primary btn-sm mb-1" type="button" onclick="bookflight('${full.booking_refno}','${full.request_id}')" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Submitting" style="font-size: 13px !important;">Confirm Flight</button>`;
                        }
                    @endif
                    return cntbutton;
                }
            },

            // render: function(data, type, full, meta) {



            // return `<div class="btn-group" role="group">
                //             <span id="btnGroupDrop1" class="badge ${full.status=='success'? 'badge-success' : full.status=='pending'? 'badge-warning':full.status=='reversed'? 'badge-info':full.status=='refund'? 'badge-dark':'badge-danger'} dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                //             ` + full.status + `
                //             </span>
                //             <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                //                ` + menu + `
                //             </div>
                //          </div>`;
                // }
                // }
            ];

            var DT = datatableSetup(url, options, onDraw);
        });

        function makepayments(b_refno, req_id) {
            console.log(b_refno);
            $('#bookingModal').find('input[name="bookingRefNo"]').val(b_refno)
            $('#bookingModal').find('input[name="bookingRefNo"]').attr('readonly', 'readonly');
            // $('#bookingModal').find('input[name="amount"]').val(amount);
            $('#bookingModal').find('input[name="requestId"]').val(req_id);
            $('#bookingModal').modal();
        }

        function bookflight(b_refno, req_id) {
            $('#bookingModal2').find('input[name="bookingRefNo"]').val(b_refno);
            // $('#bookingModal').find('input[name="amount"]').val(amount);
            $('#bookingModal2').find('input[name="requestId"]').val(req_id);
            $('#bookingModal2').modal();
        }



        function checkType(type, value) {
            var val;
            switch (type) {
                case "booking":
                    switch (value) {

                        case "0":
                            val = "ONE_WAY";

                            break;
                        case "1":
                            val = "ROUNDTRIP";
                            break;
                        case "2":
                            val = "SPECIALROUNDTRIP";
                            break;
                        default:
                            val = "";
                            break;
                    }
                    break;
                case "class":
                    switch (value) {
                        case "0":
                            val = "ECONOMY";
                            break;
                        case "1":
                            val = "BUSINESS";
                            break;
                        case "2":
                            val = "FIRST";
                            break;
                        case "3":
                            val = "PREMIUM_ECONOMY";
                            break;
                        default:
                            val = "";
                            break;

                    }
                    break;

                case "travel":
                    switch (value) {
                        case "0":
                            val = "DOMESTIC";
                            break;
                        case "1":
                            val = "INTERNATIONAL";
                            break;
                        default:
                            val = "";
                            break;


                    }
                    break;

            }
            return val;

        }
    </script>
@endpush
