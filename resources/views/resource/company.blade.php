@extends('layouts.app')
@section('title', 'Company Manager')
@section('pagetitle', 'Company Manager')
@php
$table = "yes";
$agentfilter = "hide";

$status['type'] = "Company";
$status['data'] = [
"1" => "Active",
"0" => "De-active"
];
@endphp

@section('content')

<div class="row">
    <div class="col-sm-12">
        <div class="iq-card">
            <div class="iq-card-header d-flex justify-content-end">
                <div class="iq-header-title">
                    <!-- <h4 class="card-title">User List</h4> -->
                    <button type="button" class="btn btn-sm btn-primary " onclick="addSetup()">
                        Add New
                    </button>
                </div>
            </div>
            <div class="iq-card-body">
                <div class="table-responsive">

                    <table id="datatable" class="table table-striped table-bordered mt-4" role="grid" aria-describedby="user-list-page-info">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Domain</th>
                                <th>Status</th>
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



<div class="modal fade" id="setupModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Company</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="setupManager" action="{{route('resourceupdate')}}" method="post">
                    <div class="modal-body">
                        <div class="row">
                            <input type="hidden" name="id">
                            <input type="hidden" name="actiontype" value="company">
                            {{ csrf_field() }}
                            <div class="form-group col-md-12">
                                <label>Name</label>
                                <input type="text" name="companyname" class="form-control" placeholder="Enter Bank Name" required="">
                            </div>
                            <div class="form-group col-md-12">
                                <label>Website</label>
                                <input type="text" name="website" class="form-control" placeholder="Enter Bank Name" required="">
                            </div>
                            <div class="form-group col-md-12">
                                <label>Senderid</label>
                                <input type="text" name="senderid" class="form-control" placeholder="Enter Sms Senderid">
                            </div>
                            <div class="form-group col-md-12">
                                <label>Smsuser</label>
                                <input type="text" name="smsuser" class="form-control" placeholder="Enter Sms Username">
                            </div>
                            <div class="form-group col-md-12">
                                <label>Smspwd</label>
                                <input type="text" name="smspwd" class="form-control" placeholder="Enter Sms Password">
                            </div>
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('script')
<script type="text/javascript">
    $(document).ready(function() {
        var url = "{{url('statement/fetch')}}/resource{{$type}}/0";

        var onDraw = function() {
            $('input.companyStatusHandler').on('click', function(evt) {
                evt.stopPropagation();
                var ele = $(this);
                var id = $(this).val();
                var status = "0";
                if ($(this).prop('checked')) {
                    status = "1";
                }

                $.ajax({
                        url: `{{route('resourceupdate')}}`,
                        type: 'post',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: 'json',
                        data: {
                            'id': id,
                            'status': status,
                            "actiontype": "company"
                        }
                    })
                    .done(function(data) {
                        if (data.status == "success") {
                            notify("Company Updated", 'success');
                            $('#datatable').dataTable().api().ajax.reload();
                        } else {
                            if (status == "1") {
                                ele.prop('checked', false);
                            } else {
                                ele.prop('checked', true);
                            }
                            notify("Something went wrong, Try again.", 'warning');
                        }
                    })
                    .fail(function(errors) {
                        if (status == "1") {
                            ele.prop('checked', false);
                        } else {
                            ele.prop('checked', true);
                        }
                        showError(errors, "withoutform");
                    });
            });
        };

        var options = [{
                "data": "id"
            },
            {
                "data": "companyname"
            },
            {
                "data": "website"
            },
            {
                "data": "name",
                render: function(data, type, full, meta) {
                    var check = "";
                    if (full.status == "1") {
                        check = "checked='checked'";
                    }

                    return `<div class="custom-control custom-switch custom-control-inline">
                              <input type="checkbox" class="custom-control-input companyStatusHandler" id="companyStatus_${full.id}" ${check} value="` + full.id + `" actionType="` + type + `">
                              <label class="custom-control-label" for="companyStatus_${full.id}"></label>
                           </div>`;
                }
            },
            {
                "data": "action",
                render: function(data, type, full, meta) {
                    var menu = ``;
                    menu += `<li class="dropdown-header">Setting</li>
                    <a class="dropdown-item" href="javascript:void(0)" onclick="editSetup('` + full.id + `', '` + full.companyname + `', '` + full.website + `', '` + full.senderid + `', '` + full.smsuser + `', '` + full.smspwd + `')">Edit</a>`;
                    var out = `<div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                                 <div class="btn-group" role="group">
                                    <button id="btnGroupDrop1" type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Action
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                    ` + menu + `
                                    </div>
                                 </div>
                              </div>`;

                    return out;
                }
            },
        ];
        datatableSetup(url, options, onDraw);

        $("#setupManager").validate({
            rules: {
                name: {
                    required: true,
                }
            },
            messages: {
                name: {
                    required: "Please enter bank name",
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
                var form = $('#setupManager');
                var id = form.find('[name="id"]').val();
                form.ajaxSubmit({
                    dataType: 'json',
                    beforeSubmit: function() {
                        form.find('button[type="submit"]').html('loading').attr('disabled', true).addClass('btn-secondary');
                    },
                    success: function(data) {
                        form.find('button[type="submit"]').html('Submit').attr('disabled', false).removeClass('btn-secondary');
                        if (data.status == "success") {
                            if (id == "new") {
                                form[0].reset();
                            }
                            form.find('button[type="submit"]').html('Submit').attr('disabled', false).removeClass('btn-secondary');
                            notify("Task Successfully Completed", 'success');
                            $('#datatable').dataTable().api().ajax.reload();
                            $('#setupModal').modal('hide');
                        } else {
                            notify(data.status, 'warning');
                        }
                    },
                    error: function(errors) {
                        showError(errors, form);
                        form.find('button[type="submit"]').html('Submit').attr('disabled', false).removeClass('btn-secondary');
                    }
                });
            }
        });

        $("#setupModal").on('hidden.bs.modal', function() {
            $('#setupModal').find('.msg').text("Add");
            $('#setupModal').find('form')[0].reset();
        });

    });

    function addSetup() {
        $('#setupModal').find('.msg').text("Add");
        $('#setupModal').find('input[name="id"]').val("new");
        $('#setupModal').modal('show');
    }

    function editSetup(id, companyname, website, senderid, smsuser, smspwd) {
        $('#setupModal').find('.msg').text("Edit");
        $('#setupModal').find('input[name="id"]').val(id);
        $('#setupModal').find('input[name="companyname"]').val(companyname);
        $('#setupModal').find('input[name="website"]').val(website);
        $('#setupModal').find('input[name="senderid"]').val(senderid);
        $('#setupModal').find('input[name="smsuser"]').val(smsuser);
        $('#setupModal').find('input[name="smspwd"]').val(smspwd);
        $('#setupModal').modal('show');
    }
</script>
@endpush