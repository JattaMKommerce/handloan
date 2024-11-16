@extends('layouts.app')
@section('title', "Investment Fund Request")
@section('pagetitle', "Investment Fund Request")

@php
$agentfilter = "hide";
$table = "yes";
// $export = "fund";
@endphp

@section('content')

<div class="content">
    <div class="row">
        <div class="col-sm-12">
            <div class="iq-card">

                <div class="iq-card-body">
                    <div class="table-responsive">
                        <table class="table" id="datatable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Requested By</th>
                                    <th>Deposit Bank Details</th>
                                    <th>Refrence Details</th>
                                    <th>Amount</th>
                                    <th>Remark</th>
                                    <th>Receipt</th>
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


<div class="modal fade" tabindex="-1" id="transferModal" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Investment Fund Request Update</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="transferForm" action="{{route('investmentRequestUpdate')}}" method="post">
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" name="id">
                        <input type="hidden" name="type" value="requestview">
                        <input type="hidden" name="provider_id" value="0">
                        <input type="hidden" name="product" value="investment">
                        {{ csrf_field() }}
                        <div class="form-group col-md-12">
                            <label>Select Action</label>
                            <select name="status" class="form-control" required>
                                <option value="">Select Status</option>
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label>Remark</label>
                            <textarea name="remark" class="form-control" rows="3" placeholder="Enter Remark"></textarea>
                        </div>
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

        var url = "{{url('statement/fetch')}}/investfundrequestview/0";
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
                    return `Name - ` + full.fundbank.name + `<br>Account No. - ` + full.fundbank.account + `<br>Branch - ` + full.fundbank.branch;
                }
            },
            {
                "data": "bank",
                render: function(data, type, full, meta) {
                    var slip = '';
                    if (full.payslip) {
                        var slip = `<a target="_blank" href="{{ asset('deposit_slip') }}/${full.payslip}"><u>Pay Slip</u></a>`;
                    }
                    return `Ref No. - ` + full.ref_no +`<br>Number of Share -`+full.numberOfshares + `<br>Paydate - ` + full.paydate + `<br>Paymode - ` + full.paymode + ` ( ` + slip + ` )`;
                }
            },
            {
                "data": "amount"
            },
            {
                "data": "remark"
            },
            {
                    "data": "remak",
                    render: function(data, type, full, meta) {
                        var ot = '';
                        if (full.payslip != "" || full.payslip != undefined) {
                            ot +=`<a href="{{ asset('deposit_slip') }}/${full.payslip}"><u>Receipt</u></a>`;
                        }
                        return ot;
                    }
                },
            {
                "data": "action",
                render: function(data, type, full, meta) {
                    var menu = ``;

                    @if(Myhelper::can(['fund_request']))
                    menu += `<li class="dropdown-header">Action</li><li><a href="javascript:void(0)" class="dropdown-item" onclick="transfer(` + full.id + `, '` + full.remark + `', '` + full.status + `')"><i class="icon-wallet"></i> Update Request</a></li>`;
                    @endif

                    return `<div class="btn-group" role="group">
                                    <span id="btnGroupDrop1" class="badge ${full.status=='approved'? 'badge-success' : full.status=='pending'? 'badge-warning':'badge-danger'} dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    ` + full.status + `
                                    </span>
                                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                       ` + menu + `
                                    </div>
                                 </div>`;

                }
            }
        ];

        datatableSetup(url, options, onDraw);

        $("#transferForm").validate({
            rules: {
                status: {
                    required: true
                },
            },
            messages: {
                fundbank_id: {
                    required: "Please select request status",
                }
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
                var form = $('#transferForm');
                form.ajaxSubmit({
                    dataType: 'json',
                    beforeSubmit: function() {
                        form.find('button:submit').html('loading').attr('disabled', true).addClass('btn-secondary');
                    },
                    complete: function() {
                        form.find('button:submit').button('Submit').attr('disabled', false).removeClass('btn-secondary');
                    },
                    success: function(data) {
                        if (data.status == "success") {
                            form.closest('.modal').modal('hide');
                            notify("Fund Request Updated Successfully", 'success');
                            $('#datatable').dataTable().api().ajax.reload();
                           
                        } else {
                            notify(data.status, 'warning');
                        }
                    },
                    error: function(errors) {
                        showError(errors, form);
                    }
                });
            }
        });
    });

    function transfer(id, remark, status) {
        $('#transferForm').find('[name="id"]').val(id);
        $('#transferForm').find('[name="status"]').val(status).trigger('change');
        $('#transferForm').find('[name="remark"]').val(remark);
        $('#transferModal').modal();
    }
</script>
@endpush