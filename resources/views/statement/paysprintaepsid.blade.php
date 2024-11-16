@extends('layouts.app')
@section('title', "Paysprint Aeps Agents List")
@section('pagetitle', "Paysprint Aeps Agent List")

@php
$table = "yes";
//$export= "aepsagentstatement";
$status['type'] = "Id";
$status['data'] = [
"success" => "Success",
"pending" => "Pending",
"failed" => "Failed",
"approved" => "Approved",
"rejected" => "Rejected",
];
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
                                    <th>#</th>
                                    <th>User Details</th>
                                    <th>Agent Details</th>
                                    <th>Agent Ids</th>
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
</div>

<div id="viewFullDataModal" class="modal fade right" role="dialog" data-backdrop="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Agent Details</h4>
            </div>
            <div class="modal-body p-0">
                <table class="table table-bordered table-striped ">
                    <tbody>
                        <tr>
                            <th>Bc Id</th>
                            <td class="bc_id"></td>
                        </tr>
                        <tr>
                            <th>Bbps Agent Id</th>
                            <td class="bbps_agent_id"></td>
                        </tr>
                        <tr>
                            <th>Bbps Id</th>
                            <td class="bbps_id"></td>
                        </tr>
                        <tr>
                            <th>Bc Name</th>
                            <td><span class="bc_f_name"></span> <span class="bc_l_name"></span></td>
                        </tr>
                        <tr>
                            <th>Bc Mailid</th>
                            <td class="emailid"></td>
                        </tr>
                        <tr>
                            <th>Phone 1</th>
                            <td class="phone1"></td>
                        </tr>
                        <tr>
                            <th>Phone 2</th>
                            <td class="phone2"></td>
                        </tr>
                        <tr>
                            <th>Shopname</th>
                            <td class="shopname"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-hidden="true">Close</button>
            </div>
        </div>
    </div>
</div><!-- /.modal -->

<div class="modal fade" id="viewFullDataModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Report</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <div class="modal-body p-0">
                <table class="table table-bordered table-striped ">
                    <tbody>
                        <tr>
                            <th>Bc Id</th>
                            <td class="bc_id"></td>
                        </tr>
                        <tr>
                            <th>Bbps Agent Id</th>
                            <td class="bbps_agent_id"></td>
                        </tr>
                        <tr>
                            <th>Bbps Id</th>
                            <td class="bbps_id"></td>
                        </tr>
                        <tr>
                            <th>Bc Name</th>
                            <td><span class="bc_f_name"></span> <span class="bc_l_name"></span></td>
                        </tr>
                        <tr>
                            <th>Bc Mailid</th>
                            <td class="emailid"></td>
                        </tr>
                        <tr>
                            <th>Phone 1</th>
                            <td class="phone1"></td>
                        </tr>
                        <tr>
                            <th>Phone 2</th>
                            <td class="phone2"></td>
                        </tr>
                        <tr>
                            <th>Shopname</th>
                            <td class="shopname"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-hidden="true">Close</button>
            </div>
            </div>
        
        </div>
    </div>
</div>

@if (Myhelper::can('aepsid_statement_edit'))

<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Report</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editUtiidForm" action="{{route('statementUpdate')}}" method="post">
                <div class="modal-body">
                    <input type="hidden" name="id">
                    <input type="hidden" name="actiontype" value="raepsid">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label>Agent Id</label>
                        <input type="text" name="merchantLoginId" class="form-control" placeholder="Enter id" required="" readonly="true">
                    </div>

                    <div class="form-group">
                        <label> Pin</label>
                        <input type="text" name="merchantLoginPin" class="form-control" placeholder="Enter id" required="" readonly="true">
                    </div>
                    <div class="form-group">
                        <label>Select BANK1</label>
                        <select name="bank1" class="form-control" id="select" required>
                            <option value="">Select Status</option>
                            <option value="pending">Pending</option>
                            <option value="active">Active</option>

                        </select>
                    </div>
                    <div class="form-group">
                        <label>Select BANK2</label>
                        <select name="bank2" class="form-control select" id="select" required>
                            <option value="">Select Status</option>
                            <option value="pending">Pending</option>
                            <option value="active">Active</option>

                        </select>
                    </div>
                    <div class="form-group">
                        <label>Select BANK3</label>
                        <select name="bank3" class="form-control select" id="select" required>
                            <option value="">Select Status</option>
                            <option value="pending">Pending</option>
                            <option value="active">Active</option>

                        </select>
                    </div>
                    <div class="form-group">
                        <label>Select Action</label>
                        <select name="status" class="form-control select" id="select" required>
                            <option value="">Select Status</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="submit" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Updating">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection

@push('style')

@endpush

@push('script')
<script type="text/javascript">
    $(document).ready(function() {

        $("#editUtiidForm").validate({
            rules: {
                bbps_agent_id: {
                    required: true,
                },
            },
            messages: {
                bbps_agent_id: {
                    required: "Please enter id",
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
                var form = $('#editUtiidForm');
                var id = form.find('[name="id"]').val();
                form.ajaxSubmit({
                    dataType: 'json',
                    beforeSubmit: function() {
                        form.find('button[type="submit"]').button('loading');
                    },
                    success: function(data) {
                        if (data.status == "success") {
                            if (id == "new") {
                                form[0].reset();
                            }
                            form.find('button[type="submit"]').button('reset');
                            notify("Task Successfully Completed", 'success');
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

        var url = "{{url('statement/fetch')}}/raepsagentstatement/{{$id}}";
        var onDraw = function() {};
        var options = [{
                "data": "name",
                render: function(data, type, full, meta) {
                    return `<div>
                            <span class='text-inverse m-l-10'><b>` + full.id + `</b> </span>
                            <div class="clearfix"></div>
                        </div><span style='font-size:13px' class="pull=right">` + full.created_at + `</span>`;
                }
            },
            {
                "data": "username"
            },
            {
                "data": "bank",
                render: function(data, type, full, meta) {
                    return `Mobile - ` + full.merchantPhoneNumber + `<br>BC Name - <a href="javascript:void(0)" onclick="viewFullData(` + full.id + `)">` + full.merchantName + `</a>`;
                }
            },
            {
                "data": "bank",
                render: function(data, type, full, meta) {
                    return `Agent Id - ` + full.merchantLoginId + `<br> Bank1 ` + full.bank1 + `<br> Bank2 ` + full.bank2 + ` <br> Bank3 ` + full.bank3;
                }
            },
            {
                "data": "status",
                render: function(data, type, full, meta) {

                    var menu = `<li><a href="javascript:void(0)" class="dropdown-item" onclick="editUtiid(` + full.id + `,'` + full.merchantLoginId + `','` + full.merchantLoginPin + `','` + full.status + `')"><i class="icon-pencil5"></i> Edit</a></li>`;
                    @if(Myhelper::can('aepsid_statement_edit'))
                    menu += `<li class="dropdown-header">Setting</li>`;
                    menu += `<a href="javascript:void(0)" class="dropdown-item" onclick="status('` + full.id + `','ragentstatus')""><i class="icon-refresh"></i> Status Bank 1</a>`;
                    menu += `<a href="javascript:void(0)" class="dropdown-item" onclick="status('` + full.id + `','ragentstatus2')""><i class="icon-refresh"></i> Status Bank 2</a>`;
                    menu += `<a href="javascript:void(0)" class="dropdown-item" onclick="status('` + full.id + `','ragentstatus3')""><i class="icon-refresh"></i> Status Bank 3</a>`;
                    @endif


                    return `<div class="btn-group" role="group">
                                    <span id="btnGroupDrop1" class="badge ${full.status=='success'? 'badge-success' : full.status=='pending'? 'badge-warning':full.status=='approved'? 'badge-success':'badge-danger'} dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
    });

    function viewFullData(id) {
        $.ajax({
                url: `{{url('statement/fetch')}}/aepsagentstatement/` + id + `/view`,
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
                $('#viewFullDataModal').modal();
            })
            .fail(function(errors) {
                notify('Oops', errors.status + '! ' + errors.statusText, 'warning');
            });
    }

    function editUtiid(id, bbps_agent_id, bbps_id, status) {
        $('#editModal').find('[name="id"]').val(id);
        $('#editModal').find('[name="merchantLoginId"]').val(bbps_agent_id);
        $('#editModal').find('[name="merchantLoginPin"]').val(bbps_id);
        $('#editModal').find('[name="status"]').select2().val(status).trigger('change');
        $('#editModal').modal('show');
    }
</script>
@endpush