@extends('layouts.app')
@section('title', "PG Statement")
@section('pagetitle', "PG Statement")

@php
$table = "yes";
$export = "pg";

$table = "yes";

$status['type'] = "Report";
$status['data'] = [
"success" => "Success",
"pending" => "Pending",
"reversed" => "Reversed",
];
@endphp

@section('content')
<div class="content">
    <div class="row">
        <div class="col-sm-12">
            <div class="iq-card card-default">
                <div class="card-header bg-white">
                    <h4>PG Statement</h4>
                </div>
                <div class="card-body pb-5">
                    <table class="table table-bordered table-striped table-hover" id="datatable">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>User Detail</th>
                                <th>Beneficiary Details</th>
                                <th>Txnid</th>
                                <th>Refrence Details</th>
                                <th>Amount <br />/Commission</th>
                                <th>Status</th>
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

<div id="receipt" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabels" aria-hidden="true">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Receipt</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div id="receptTable">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table m-t-10 table-bordered" width="100%">
                                    <thead class="border  text-center">
                                        <tr>
                                            <th width="50%" style="padding: 10px 0px">#Invoice</th>
                                            <th width="50%" style="padding: 10px 0px;">
                                                @if(Auth::user()->company->logo)
                                                <img src="{{asset('')}}logos/{{Auth::user()->company->logo}}" class=" img-responsive" alt="">
                                                @else
                                                {{Auth::user()->company->companyname}}
                                                @endif
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="padding: 10px">
                                                <address class="m-b-10">
                                                    
                                                    <strong> Agent : </strong><span class="inner_name"></span><br>
                                                    <strong> Shop Name :</strong> <span class = "inner_shpname" ></span><br> 
                                                    <strong>Phone : </strong><span class="inner_usrmobile"></span><br />
                                                    <strong>Remitter : </strong><span class="option1"></span><br />
                                                </address>

                                            </td>
                                            <td style="padding: 10px">
                                                <address class="m-b-10">
                                                    <strong>Date : </strong> <span class="created_at"></span><br>
                                                    <strong>Name : </strong> <span class="option2"></span><br>
                                                    <strong>Account : </strong> <span class="number"></span><br>
                                                    <strong>Bank : </strong> <span class="option3"></span>
                                                </address>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <h5 class="my-2">Transaction Details :</h5>
                                <table class="table m-t-10 table-bordered text-center" width="100%">
                                    <thead>
                                        <tr>
                                            <th style="padding: 10px 0px" width="25%">Order Id</th>
                                            <th style="padding: 10px 0px" width="25%">Amount</th>
                                            <th style="padding: 10px 0px" width="25%">UTR No.</th>
                                            <th style="padding: 10px 0px" width="25%">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="id" style="padding: 10px 0px"></td>
                                            <td class="amount" style="padding: 10px 0px"></td>
                                            <td class="txnid" style="padding: 10px 0px"></td>
                                            <td class="status" style="padding: 10px 0px"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="border-radius: 0px;">
                        <div class="col-md-6 col-md-offset-6">
                            <h5>Transfer Amount : <span class="amount"></span></h5>
                        </div>
                    </div>
                    <p><span class="text-danger">*</span> <b>As per RBI guideline, maximum charges allowed is 2%.</b></p>
                    <hr>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-hidden="true">Close</button>
                <button class="btn btn-primary" type="button" id="print"><i class="fa fa-print"></i></button>
            </div>
        </div>
    </div>
</div>

<div id="otpModal" class="modal fade" role="dialog" data-backdrop="false" data-keyboard="false">
    <div class="modal-dialog modal-sm">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title pull-left">Refund Via Otp</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{route('dmt2pay')}}" method="post" id="otpForm">
                {{ csrf_field() }}
                <div class="modal-body">
                    <input type="hidden" name="type" value="getrefund">
                    <input type="hidden" name="transid">
                    <div class="form-group">
                        <label>OTP</label>
                        <input type="text" class="form-control" name="otp" placeholder="enter otp" required>
                        <a href="javascript:void(0)" class="pull-right resendOtp" data-loading-text="<i class='fa fa-spinner fa-spin'></i> Sending" type="resendOtpVerification"><i class='fa fa-paper-plane'></i> Resend Otp</a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-hidden="true">Close</button>
                    <button class="btn btn-primary" type="submit" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Submitting">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('style')

@endpush

@push('script')
<script type="text/javascript">
    $(document).ready(function() {
        var url = "{{url('statement/fetch')}}/pgstatement/{{$id}}";

        $('#print').click(function() {
            $('#receptTable').print();
        });

        var onDraw = function() {
            $('.print').click(function(event) {
                var data = DT.row($(this).parent().parent().parent().parent().parent()).data();
                // console.log('data', data)
                $.each(data, function(index, values) {
                    $("." + index).text(values);

                    if(index === 'usershop'){
                        $.each(values, function(idx, val) {
                            $(".inner_" + idx).text(val);
                        });
                    }
                });
                $('#receipt').modal();
            });
        };
        var options = [{
                "data": "name",
                render: function(data, type, full, meta) {
                    return `<div>
                            <span class='text-inverse m-l-10'><b>` + full.id + `</b> </span>
                            <div class="clearfix"></div>
                        </div><span style='font-size:13px' class="pull=right">` + full.created_at + `</span>
                         </div><span style='font-size:13px' class="pull=right">` + full.apiname + `</span>`;

                }
            },
            {
                "data": "username",
            },
            {
                "data": "bank",
                render: function(data, type, full, meta) {
                    return `Name - ` + full.option2 + `<br>Account - ` + full.number + `<br>Bank - ` + full.option3;
                }
            },
            {
                "data": "txnid"
            },
            {
                "data": "bank",
                render: function(data, type, full, meta) {
                    return `Remitter - ` + full.option1 + ` (` + full.mobile + `)<br>` + full.refno + `<br>Payid - ` + full.payid;
                }
            },
            {
                "data": "bank",
                render: function(data, type, full, meta) {
                    return `Amount - <i class="fa fa-inr"></i> ` + full.amount + `<br>Charge - <i class="fa fa-inr"></i> ` + full.charge + `<br>Profit - <i class="fa fa-inr"></i> ` + parseFloat(full.profit + full.gst) + `<br>Gst - <i class="fa fa-inr"></i> ` + full.gst
                }
            },
            {
                "data": "status",
                render: function(data, type, full, meta) {
                    if (full.status == "success") {
                        var out = `<span class="badge badge-success">Success</span><br/>`;
                    } else if (full.status == "pending") {
                        var out = `<span class="badge badge-warning">Pending</span><br/>`;
                    } else if (full.status == "reversed") {
                        var out = `<span class="badge bg-primary">Reversed</span><br/>`;
                    } else {
                        var out = `<span class="badge badge-danger">` + full.status + `</span><br/>`;
                    }

                    // var menu = ``;
                    // menu += `<a class="dropdown-item print" href="javascript:void(0)"><i class="icon-info22"></i>Print Invoice</a>`;

                    // if (full.status == "refund" || full.status == "success") {
                    //     // menu += `<li class="dropdown-header">Get Refund</li>
                    //     //     <li><a href="javascript:void(0)" onclick="getrefund(`+full.id+`)"><i class="icon-info22"></i>Get Refund</a></li>`;
                    // }

                    // @if(Myhelper::can('money_status'))
                    // menu += `<a class="dropdown-item" href="javascript:void(0)" onclick="status(` + full.id + `, 'payoutstatus')"><i class="icon-info22"></i>Check Status</a>`;
                    // @endif

                    // @if(Myhelper::can('money_statement_edit'))
                    // if (full.status == "pending" || full.status == "success") {
                    //     menu += `<a class="dropdown-item" href="javascript:void(0)" onclick="editReport(` + full.id + `,'` + full.refno + `','` + full.txnid + `','` + full.payid + `','` + full.remark + `', '` + full.status + `', 'payoutstatus')"><i class="fa fa-pencil"></i> Edit</a>`;
                    // }
                    // @endif

                    // @if(Myhelper::hasNotRole('admin'))
                    // menu += `<a class="dropdown-item" href="javascript:void(0)" onclick="complaint(` + full.id + `, 'dmt')"><i class="fa fa-file"></i> Complaint</a>`;
                    // @endif

                    // out += `
                    //         <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                    //              <div class="btn-group" role="group">
                    //                 <button id="btnGroupDrop1" type="button" class="btn btn-primary p-1 btn-sm mt-1 dropdown-toggle btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    //                 <small>Action</small>
                    //                 </button>
                    //                 <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                    //                 ` + menu + `
                    //                 </div>
                    //              </div>
                    //         </div>`;

                    return out;
                }
            },
            // {
            //     "data":"name",
            //     render: function(data, type, full, meta) {
            //         return full.usershop.name + full.usershop.usrmobile + full.usershop.shpname
            //     }

            // }


        ];

        var DT = datatableSetup(url, options, onDraw);
    });

    function viewUtiid(id) {
        $.ajax({
                url: `{{url('statement/fetch')}}/utiidstatement/` + id,
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                data: {
                    'scheme_id': id
                }
            })
            .done(function(data) {
                $.each(data, function(index, values) {
                    $("." + index).text(values);
                });
                $('#utiidModal').modal();
            })
            .fail(function(errors) {
                notify('Oops', errors.status + '! ' + errors.statusText, 'warning');
            });
    }

    function getrefund(id, type = "none") {
        $.ajax({
                url: `{{route('dmt2pay')}}`,
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                data: {
                    'id': id,
                    "type": "refundotp"
                },
                beforeSend: function() {
                    swal({
                        title: 'Wait!',
                        text: 'We are processing your request.',
                        allowOutsideClick: () => !swal.isLoading(),
                        onOpen: () => {
                            swal.showLoading()
                        }
                    });
                },
            })
            .done(function(data) {

                swal.close();
                if (type == "none") {
                    if (data.statuscode == "TXN") {
                        $('#otpModal').find('[name="transid"]').val(id);
                        $('#otpModal').modal('show');
                    } else {

                        notify(data.message, 'warning');
                    }
                } else {

                    if (data.statuscode == "TXN") {
                        notify(data.message, 'success', "inline", $('#otpForm'));
                    } else {

                        notify(data.message, 'danger', "inline", $('#otpForm'));
                    }
                    $('#datatable').dataTable().api().ajax.reload();
                }
            })
            .fail(function(errors) {
                swal.close();
            });
    }
</script>
@endpush