@extends('layouts.app')
@section('title', "ADD ACCOUNT")
@section('pagetitle', "ADD ACCOUNT")

@php
$table = "yes";
$export = "aepsfundrequest";
$status['type'] = "Fund";
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
                @if(Myhelper::hasNotRole('admin'))
                <div class="iq-card-header d-flex justify-content-between">
                    <div class="iq-header-title">
                    </div>
                    <div>
                        <button type="button" data-toggle="modal" data-target="#fundRequestModal" class="btn btn-primary" data-loading-text="<b><i class='fa fa-spin fa-spinner'></i></b> Searching"><b><i class="icon-plus2"></i></b>ADD ACCOUNT</button>
                    </div>
                </div>
                @endif
                <div class="iq-card-body">
                    <div class="table-responsive">
                        <table class="table" id="datatable">
                              <thead class="thead-light">
                                <tr>
                                    <th width="160px">#</th>
                                    <th>User Details</th>
                                    <th>Bank Details</th>
                                    <th>Document Upload</th>
                                    <th>Account Status</th>
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

<div class="modal fade" id="fundRequestModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">ADD NEW ACCOUNT</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="fundRequestForm" action="{{route('fundtransaction')}}" method="post">
                <div class="modal-body">
                    <input type="hidden" name="user_id">
                    <input type="hidden" name="type" value="addaccount">
                    {{ csrf_field() }}

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>Account Number <span class="text-danger fw-bold">*</span></label>
                            <input type="text" class="form-control" name="account" placeholder="Enter Value" required="" value="">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Ifsc Code <span class="text-danger fw-bold">*</span></label>
                            <input type="text" class="form-control" name="ifsc" placeholder="Enter Value" required="" value="">
                        </div>
                        <div class="form-group col-md-6">
                            <label>User Name <span class="text-danger fw-bold">*</span></label>
                            <input type="text" class="form-control" name="name" placeholder="Enter Value" required="" value="">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Bank <span class="text-danger fw-bold">*</span> </label>
                            <select name="bankid" class="form-control" required="">
                                <option value="">Select Bank</option>
                                @foreach ($bankName as $bank)
                                <option value="{{$bank->bankid}}">{{$bank->bankname}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Type <span class="text-danger fw-bold">*</span></label>
                            <select name="acctype" class="form-control" required="">
                                <option value="">Select Tye</option>

                                <option value="PRIMARY">PRIMARY</option>
                                <option value="RELATIVE">RELATIVE</option>

                            </select>
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

<div class="modal fade" id="docuploadModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">UPLOAD DOUCUMENT</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="docuploadForm" action="{{route('fundtransaction')}}" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="user_id">
                    <input type="hidden" name="bene_id">
                    <input type="hidden" name="type" value="docupload">
                    {{ csrf_field() }}

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>Document type</label>
                            <select name="doctype" id="doctype" class="form-control select" required="">
                                <option value="">Select Documnet</option>
                                <option value="PAN">PAN</option>
                                <option value="AADHAAR">AADHAAR</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Passbook </label>
                            <input type="file" class="form-control" autocomplete="off" accept="image/x-png,image/gif,image/jpeg" name="passbook" placeholder="" required>
                        </div>
                        <div class="form-group col-md-6" id="panimage" style="display:none">
                            <label>Panimage </label>
                            <input type="file" class="form-control" autocomplete="off" accept="image/x-png,image/gif,image/jpeg" name="panimage" placeholder="">
                        </div>
                        <div class="form-group col-md-6" id="aadharfront" style="display:none">
                            <label>Front Aadhar image </label>
                            <input type="file" class="form-control" autocomplete="off" accept="image/x-png,image/gif,image/jpeg" name="front_image" placeholder="">
                        </div>
                        <div class="form-group col-md-6" id="aadharback" style="display:none">
                            <label>Back Aadhar image </label>
                            <input type="file" class="form-control" autocomplete="off" accept="image/x-png,image/gif,image/jpeg" name="back_image" placeholder="">
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
                    <input type="hidden" name="actiontype" value="updateaccount">
                    {{ csrf_field() }}

                    <div class="form-group">
                        <label>Select Action</label>
                        <select name="status" class="form-control" id="select" required>
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
@endsection

@push('style')

@endpush

@push('script')
<script type="text/javascript">
    $(document).ready(function() {
        $('#doctype').on('change', function() {
            var doctype = $(this).val();
            if (doctype == "PAN") {
                $("#panimage").show();
                $("#aadharfront").hide();
                $("#aadharback").hide();
            } else {
                $("#panimage").hide();
                $("#aadharfront").show();
                $("#aadharback").show();
            }

        });
        var url = "{{url('statement/fetch')}}/sprintpayoutusers/0";
        var onDraw = function() {};
        var options = [{
                "data": "name",
                render: function(data, type, full, meta) {
                    var out = '';
                    if (full.api) {
                        out += `<span class='myspan'>` + full.api.api_name + `</span><br>`;
                    }
                    out += `<span class='text-inverse'>` + full.id + `</span><br><span style='font-size:12px'>` + full.created_at + `</span>`;
                    return out;
                }
            },
            {
                "data": "username"
            },
            {
                "data": "bank",
                render: function(data, type, full, meta) {

                    return full.account + " ( " + full.name + " )<br>" + full.ifsc;

                }
            },
            {
                "data": "bank",
                render: function(data, type, full, meta) {

                    return full.doc_upload;

                }
            },

            {
                "data": "action",
                render: function(data, type, full, meta) {
                    
                    var menu = ``;
                    @if(Myhelper::hasRole('admin') || Myhelper::hasRole('employee'))
                    menu += `<a href="javascript:void(0)" class="dropdown-item" onclick="editUtiid(` + full.id + `,'` + full.status + `')"><i class="icon-pencil5"></i> Edit</a>`;
                    @endif
                    // menu += `<li class="dropdown-header">Status
                    //             <a href="javascript:void(0)" onclick="checkstatus(`+full.id+`, 'checkaccstatus')"><i class="icon-info22"></i>Check Status</a>`;
                    menu += `<a href="javascript:void(0)" class="dropdown-item" onclick="uploaddoc(` + full.id + `,'` + full.bene_id + `', 'uploaddoc')"><i class="icon-pencil5"></i>Upload Document</a>`;
                    // if (full.status== 'pending'){

                    //  }
                   return `<div class="btn-group" role="group">
                                    <span id="btnGroupDrop1" class="badge ${full.status=='success'? 'badge-primary' : full.status=='pending'? 'badge-warning': 'badge-danger'} dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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

        $("#fundRequestForm").validate({
            rules: {
                account: {
                    required: true
                },
                ifsc: {
                    required: true
                },
                name: {
                    required: true
                },
                bankid: {
                    required: true
                }

            },
            messages: {
                account: {
                    required: "Please enter account number",
                },
                ifsc: {
                    required: "Please enter ifsc",
                },
                name: {
                    required: "Please enter name",
                },
                bankid: {
                    required: "Please select bank",
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
                var form = $('#fundRequestForm');
                form.ajaxSubmit({
                    dataType: 'json',
                    beforeSubmit: function() {
                        form.find('button:submit').button('loading');
                        swal({
                                title: 'Wait!',
                                text: 'We are working on your request',
                                onOpen: () => {
                                    swal.showLoading()
                                },
                                allowOutsideClick: () => !swal.isLoading()
                            });
                    },
                    complete: function() {
                        form.find('button:submit').button('reset');
                    },
                    success: function(data) {
                        swal.close();
                        if (data.status == "success") {
                            form.closest('.modal').modal('hide');
                            notify(data.message, 'success');
                            $('#datatable').dataTable().api().ajax.reload();
                        } else {
                            form.closest('.modal').modal('hide');
                            notify(data.message, 'warning');
                        }
                    },
                    error: function(errors) {
                        showError(errors, form);
                    }
                });
            }
        });


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

        $("#docuploadForm").validate({
            rules: {
                doctype: {
                    required: true
                },
                passbook: {
                    required: true
                }
            },
            messages: {
                doctype: {
                    required: "Please select doctype",
                },
                passbook: {
                    required: "Please select passbook",
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
                var form = $('#docuploadForm');
                form.ajaxSubmit({
                    dataType: 'json',
                    beforeSubmit: function() {
                        form.find('button:submit').button('loading');
                    },
                    complete: function() {
                        form.find('button:submit').button('reset');
                    },
                    success: function(data) {
                        if (data.status == "success") {
                            form.closest('.modal').modal('hide');
                            notify(data.message, 'success');
                            $('#datatable').dataTable().api().ajax.reload();
                        } else {
                            form.closest('.modal').modal('hide');
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



    function uploaddoc(id, bene_id) {
        $('#docuploadForm').find('input[name="bene_id"]').val(bene_id);
        $('#docuploadModal').modal();
    }

    function checkstatus(id, type) {
        $.ajax({
                url: `{{route('statementStatus')}}`,
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                beforeSend: function() {
                    swal({
                        title: 'Wait!',
                        text: 'Please wait, we are fetching transaction details',
                        onOpen: () => {
                            swal.showLoading()
                        },
                        allowOutsideClick: () => !swal.isLoading()
                    });
                },
                data: {
                    'id': id,
                    "type": type
                }
            })
            .done(function(data) {
                if (data.status == "success") {
                    if (data.refno) {
                        var refno = "Operator Refrence is " + data.refno
                    } else {
                        var refno = data.remark;
                    }
                    swal({
                        type: 'success',
                        title: data.status,
                        text: refno,
                        onClose: () => {
                            $('#datatable').dataTable().api().ajax.reload();
                        },
                    });
                } else {
                    swal({
                        type: 'success',
                        title: data.status,
                        text: "Transaction status is " + data.status,
                        onClose: () => {
                            $('#datatable').dataTable().api().ajax.reload();
                        },
                    });
                }
            })
            .fail(function(errors) {
                swal.close();
                showError(errors, "withoutform");
            });
    }

    function editUtiid(id, status) {
        $('#editModal').find('[name="id"]').val(id);
        $('#editModal').find('[name="status"]').select2().val(status).trigger('change');
        $('#editModal').modal('show');
    }
</script>
@endpush