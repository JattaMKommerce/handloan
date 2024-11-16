@extends('layouts.app')
@section('title', "Complaints")
@section('pagetitle', "Complaints")

@php
$table = "yes";

$product['data'] = array(
'recharge' => 'Recharge',
'billpay' => 'Billpay',
'dmt' => 'Dmt',
'aeps' => 'Aeps',
'utipancard' => 'Utipancard'
);
$product['type'] = "Service";
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
                                    <th>Complain Id</th>
                                    <th>User Details</th>
                                    <th>Transaction Details</th>
                                    <th>Subject</th>
                                    <th>Query Details</th>
                                    <th>Solution Details</th>
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

<div class="modal fade" id="utiidModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Transaction Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped ">
                        <tbody>
                            <tr>
                                <th>Provider</th>
                                <td class="providername"></td>
                            </tr>
                            <tr>
                                <th>BC Id</th>
                                <td class="aadhar"></td>
                            </tr>
                            <tr>
                                <th>Number</th>
                                <td class="number"></td>
                            </tr>
                            <tr>
                                <th>Mobile</th>
                                <td class="mobile"></td>
                            </tr>
                            <tr>
                                <th>Txnid</th>
                                <td class="txnid"></td>
                            </tr>
                            <tr>
                                <th>Payid</th>
                                <td class="payid"></td>
                            </tr>
                            <tr>
                                <th>Refno</th>
                                <td class="refno"></td>
                            </tr>
                            <tr>
                                <th>Amount</th>
                                <td class="amount"></td>
                            </tr>
                            <tr>
                                <th>Charge</th>
                                <td class="charge"></td>
                            </tr>
                            <tr>
                                <th>Gst</th>
                                <td class="gst"></td>
                            </tr>
                            <tr>
                                <th>Tds</th>
                                <td class="tds"></td>
                            </tr>
                            <tr>
                                <th>Remark</th>
                                <td class="remark"></td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal" aria-hidden="true">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="complaintEditModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Report</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="complaintEditForm" action="{{route('complaintstore')}}" method="post">
                <div class="modal-body">
                    <input type="hidden" name="id">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="">Select Status</option>
                            <option value="pending">Pending</option>
                            <option value="resolved">Resolved</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Solution</label>
                        <textarea name="solution" cols="30" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                   <button class="btn btn-primary" type="submit" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Updating">Update</button>
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
        var url = "{{url('statement/fetch')}}/complaints/0";
        var onDraw = function() {};
        var options = [{
                "data": "bank",
                render: function(data, type, full, meta) {
                    return `<div>
                            <span class='text-inverse m-l-10'><b>` + full.id + `</b> </span>
                            <div class="clearfix"></div>
                        </div><span style='font-size:13px' class="pull=right">` + full.created_at + `</span>`;
                }

            },
            {
                "data": "bank",
                render: function(data, type, full, meta) {
                    return full.user.name + ` ( ` + full.user.id + ` )<br>` + full.user.mobile + ` <br>` + full.user.role.name;
                }
            },
            {
                "data": "bank",
                render: function(data, type, full, meta) {
                    return `<a href="javascript:void(0)" class="label label-info" style="font-size:15px" onclick="viewData('` + full.transaction_id + `', '` + full.product + `')">` + full.product + ` ( ` + full.transaction_id + ` )` + `</a>`;
                }
            },
            {
                "data": "bank",
                render: function(data, type, full, meta) {
                    return full.subject;
                }
            },
            {
                "data": "bank",
                render: function(data, type, full, meta) {
                    return full.description;
                }
            },
            {
                "data": "bank",
                render: function(data, type, full, meta) {
                    if (full.resolver) {
                        return 'Resolved By - ' + full.resolver.name + '(' + full.resolver.id + ')<br>' + full.solution;
                    } else {
                        return full.solution;
                    }
                }
            },
            {
                "data": "status",
                render: function(data, type, full, meta) {
                   
                    var menu = ``;
                    @if(Myhelper::can('complaint_edit'))
                    menu += `<li class="dropdown-item"><a href="javascript:void(0)" onclick="editComplaint(` + full.id + `, '` + full.status + `', '` + full.solution + `')"><i class="icon-pencil5"></i> Edit</a></li>`;
                    @endif

                    return `<div class="btn-group" role="group">
                                    <span id="btnGroupDrop1" class="badge ${full.status=='resolved'? 'badge-success' :'badge-warning'} dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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

        $("#complaintEditForm").validate({
            rules: {
                status: {
                    required: true,
                },
                solution: {
                    required: true,
                }
            },
            messages: {
                status: {
                    required: "Please select status",
                },
                solution: {
                    required: "Please enter your solution",
                },
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
                var form = $('#complaintEditForm');
                form.ajaxSubmit({
                    dataType: 'json',
                    beforeSubmit: function() {
                        form.find('button:submit').html('loading').attr('disabled',true).addClass('btn-secondary');
                    },
                    complete: function() {
                        form.find('button:submit').html('Update').attr('disabled',false).removeClass('btn-secondary');
                    },
                    success: function(data) {
                        if (data.status) {
                            form[0].reset();
                            form.find('select').val(null).trigger('change');
                            form.closest('.modal').modal('hide');
                            $('#datatable').dataTable().api().ajax.reload();
                            notify("Complaint successfully updated", 'success');
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

    function viewData(id, product) {
        var statement = "";
        if (product == "aeps") {
            statement = "aepsstatement";
        }

        $.ajax({
                url: `{{url('statement/fetch')}}/` + statement + `/` + id + `/single`,
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json'
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

    function editComplaint(id, status, solution) {
        $('#complaintEditModal').find('[name="id"]').val(id);
        $('#complaintEditModal').find('[name="solution"]').val(solution);
        $('#complaintEditModal').find('[name="status"]').val(status).trigger('change');
        $('#complaintEditModal').modal('show');
    }
</script>
@endpush