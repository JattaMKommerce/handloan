@extends('layouts.app')
@section('title', 'Wallet Load Request')
@section('pagetitle', 'Runpaisa PG Request')

@php
$table = 'yes';

$status['type'] = 'Fund';
$status['data'] = [
'success' => 'Success',
'pending' => 'Pending',
'failed' => 'Failed',
'approved' => 'Approved',
'rejected' => 'Rejected',
];
$mode = 'PROD';
if ($mode == 'PROD') {
$url = 'https://pay.easebuzz.in/payment/initiateLink';
} else {
$url = 'https://pay.easebuzz.in/payment/initiateLink';
}
@endphp

@section('content')
<div class="content">

    {{-- <div class="row"> --}}
    <div class="row">
        <div class="col-sm-12">
            <div class="iq-card">
                <div class="iq-card-header d-flex justify-content-between">
                    <h4 class="iq-header-title">
                        Runpaisa PG Request
                    </h4>
                    <div>
                        @if (Myhelper::hasNotRole('admin'))
                        <button type="button" data-toggle="modal" data-target="#fundRequestModal" class="btn btn-primary" data-loading-text="<b><i class='fa fa-spin fa-spinner'></i></b> Searching"><b><i class="icon-plus2"></i></b> New Request</button>
                        <button type="button" data-toggle="modal" data-target="#linkRequestModal" class="btn btn-primary" data-loading-text="<b><i class='fa fa-spin fa-spinner'></i></b> Searching"><b><i class="icon-plus2"></i></b> Get Link</button>
                        @endif

                    </div>
                </div>
                <div class="iq-card-body">
                    <div class="table-responsive">
                        <div id="datatable_wrapper" class="dataTables_wrapper no-footer">
                            <div class="datatable-scroll">
                                <table class="table dataTable no-footer" id="datatable" role="grid" aria-describedby="datatable_info" style="width: 1399px;">
                                    {{-- <table class="table table-bordered table-striped table-hover" id="datatable"> --}}
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>User Details</th>
                                            <th>Refrence Details</th>
                                            <th>Amount</th>
                                            <th>Remark</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade bd-example-modal-lg" id="fundRequestModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Wallet Fund Request</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="pgRequestForm" action="{{ route('runpaisaTransaction') }}" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="user_id"  value="{{Auth::user()->id}}">
                        <input type="hidden" name="type" value="pgdirect">
                        {{ csrf_field() }}
                        <div class="row">

                            <div class="form-group col-md-4">
                                <label>Amount</label>
                                <input type="number" name="amount" step="any" class="form-control" placeholder="Enter Amount" required="">
                            </div>
                            <div class="form-group col-md-4">
                                <label>Mobile</label>
                                <input type="number" name="mobile" step="any" class="form-control" placeholder="Enter Mobile" required="">
                            </div>
                            <div class="form-group col-md-4">
                                <label>Email</label>
                                <input type="text" name="email" step="any" class="form-control" placeholder="Enter Email" required="">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-12">
                                <label>Remark</label>
                                <textarea name="remark" class="form-control" rows="2" placeholder="Enter Remark" required=""></textarea>
                            </div>
                        </div>


                        <div class="mydiv">

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

    <div class="modal fade bd-example-modal-lg" id="linkRequestModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Get Payment Links</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="linkRequestForm" action="{{ route('runpaisaTransaction') }}" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="user_id" value="{{Auth::user()->id}}">
                        <input type="hidden" name="type" value="pgdirect">
                        {{ csrf_field() }}
                        <div class="row">

                            <div class="form-group col-md-4">
                                <label>Amount</label>
                                <input type="number" name="amount" step="any" class="form-control" placeholder="Enter Amount" required="">
                            </div>
                            <div class="form-group col-md-4">
                                <label>Mobile</label>
                                <input type="number" name="mobile" step="any" class="form-control" placeholder="Enter Mobile" required="">
                            </div>
                            <div class="form-group col-md-4">
                                <label>Email</label>
                                <input type="text" name="email" step="any" class="form-control" placeholder="Enter Email" required="">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-12">
                                <label>Remark</label>
                                <textarea name="remark" class="form-control" rows="2" placeholder="Enter Remark" required=""></textarea>
                            </div>
                        </div>

                        <div class="mydiv">

                        </div>
                    </div>
                    <div class="modal-footer">
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
            var url = "{{ url('statement/fetch') }}/fundrequest/0";
            var onDraw = function() {};
            var options = [{
                    "data": "name",
                    render: function(data, type, full, meta) {
                        return `<span class='text-inverse m-l-10'><b>` + full.id + `</b> </span><br>
                        <span style='font-size:13px'>` + full.updated_at + `</span>`;
                    }
                },
                {
                    "data": "username"
                },
                {
                    "data": "bank",
                    render: function(data, type, full, meta) {

                        return `Ref No. - ` + full.ref_no + `<br>Paymode - ` + full.paymode;
                    }
                },
                {
                    "data": "amount"
                },
                {
                    "data": "remark"
                },
                {
                    "data": "action",
                    render: function(data, type, full, meta) {
                        var out = '';
                        if (full.status == "success" || full.status == "approved") {
                            out += `<span class="badge badge-success">Approved</span>`;
                        } else if (full.status == "pending") {
                            out += `<span class="badge badge-warning">Pending</span>`;
                        } else if (full.status == "failed") {
                            out += `<span class="badge badge-danger">Rejected</span>`;
                        }

                        return out;
                    }
                }
            ];

            datatableSetup(url, options, onDraw);

            $("#pgRequestForm").validate({
                rules: {

                    amount: {
                        required: true
                    }

                },
                messages: {

                    amount: {
                        required: "Please enter request amount",
                    }
                },
                errorElement: "p",
                errorPlacement: function(error, element) {
                    if (element.prop("tagName").toLowerCase() === "select") {
                        error.insertAfter(element.closest(".form-group").find(".select2"));
                    } else {
                        error.insertAfter(element);
                    }
                },
                submitHandler: function() {
                    var form = $('#pgRequestForm');
                    form.ajaxSubmit({
                        dataType: 'json',
                        beforeSubmit: function() {
                            form.find('button:submit').html('loading...').attr('disabled',true).addClass('btn-secondary');
                        },
                        complete: function() {
                            form.find('button:submit').html('Submit').attr('disabled',false).removeClass('btn-secondary');
                        },
                        success: function(data) {
                            console.log(data);
                            if (data.status == "TXN") {
                                window.open(data.data, '_blank');
                                $('#fundRequestModal').modal('hide');
                            } else {
                                notify(data.message, 'warning');
                            }
                        },
                        error: function(errors) {
                            showError(errors, form);
                        }
                    });
                }
            });

            $("#linkRequestForm").validate({
                rules: {

                    amount: {
                        required: true
                    }

                },
                messages: {

                    amount: {
                        required: "Please enter request amount",
                    }
                },
                errorElement: "p",
                errorPlacement: function(error, element) {
                    if (element.prop("tagName").toLowerCase() === "select") {
                        error.insertAfter(element.closest(".form-group").find(".select2"));
                    } else {
                        error.insertAfter(element);
                    }
                },
                submitHandler: function() {
                    var form = $('#linkRequestForm');
                    form.ajaxSubmit({
                        dataType: 'json',
                        beforeSubmit: function() {
                            form.find('button:submit').html('loading...').attr('disabled',true).addClass('btn-secondary');
                        },
                        complete: function() {
                            form.find('button:submit').html('Submit').attr('disabled',false).removeClass('btn-secondary');
                        },
                        success: function(data) {

                            if (data.status == 'TXN') {
                                swal({
                                    title: "Link Genrated",
                                    text: "A custom <span style='color:#F8BB86'>html<span> message.",
                                    html: "<span id='myInput'>" + data.data +
                                        `</span><button id="clickableText"><i class='fa fa-copy'></i></button>`
                                })

                                $(document).on('click', '#clickableText', function() {
                                    myFunction(data.data);
                                });


                            } else {
                                notify(data.message, 'warning');
                            }
                        },
                        error: function(errors) {
                            showError(errors, form);
                        }
                    });
                }
            });

        });

   

        function fundRequest(id = "none") {
            if (id != "none") {
                $('#fundRequestForm').find('[name="fundbank_id"]').select2().val(id).trigger('change');
            }
            $('#fundRequestModal').modal();
        }

        function myFunction(copyText) {

            navigator.clipboard.writeText(copyText);
            notify('Payment link Copied', 'success');

        }
    </script>
    @endpush