@extends('layouts.app')
@section('title', "Aeps Agents List")
@section('pagetitle', "Aeps Agent List")

@php
$table = "yes";
$export= "aepsagentstatement";
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
                                    <th>Merchant Details</th>
                                    <th>E-Kyc</th>
                                    <th>Remark</th>
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

<div class="modal fade" id="viewFullDataModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table">
                        <tbody>
                            <tr>
                                <th>Merchant Login Id</th>
                                <td class="merchantLoginId"></td>
                                <th>Merchant Login Pin</th>
                                <td class="merchantLoginPin"></td>
                            </tr>
                            <tr>
                                <th>Merchant Name</th>
                                <td class="merchantName"></td>
                                <th>Merchant Mobile</th>
                                <td class="merchantPhoneNumber"></td>
                            </tr>

                            <tr>
                                <th>Merchant Address</th>
                                <td class="merchantAddress"></td>
                                <th>Merchant State</th>
                                <td class="merchantState"></td>
                            </tr>
                            <tr>
                                <th>Merchant City</th>
                                <td class="merchantCityName"></td>
                                <th>Merchant Pincode</th>
                                <td class="merchantPinCode"></td>
                            </tr>
                            <tr>
                                <th>Merchant Aadhar</th>
                                <td class="merchantAadhar"></td>
                                <th>Merchant Pancard</th>
                                <td class="userPan"></td>
                            </tr>
                            <tr>
                                <th>Date Of Birth</th>
                                <td class="dob"></td>
                                <th>Alternate Number</th>
                                <td class="merchantalernativeNumber"></td>
                            </tr>
                            <tr>
                                <th>Father</th>
                                <td class="father"></td>
                                <th>Police Station / Thana </th>
                                <td class="thana"></td>
                            </tr>
                            <tr>
                                <th>Adhaar Pic</th>
                                <td><a href="" download class="aadharPic" target="_blank">Download</a></td>
                                <th>Pancard Pic</th>
                                <td><a href="" download class="pancardPic" target="_blank">Download</a></td>
                            </tr>
                            <tr>
                                <th>Passport Pic</th>
                                <td><a href="" download class="passport" target="_blank">Download</a></td>
                                <th>Shop Pic</th>
                                <td><a href="" download class="shoppic" target="_blank">Download</a></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning btn-raised waves-light waves-effect" data-dismiss="modal" aria-hidden="true">Close</button>
            </div>
        </div>
    </div>
</div>

@if (Myhelper::can('icicagent_statement_edit'))

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
                    <div class="row">
                        <input type="hidden" name="id">
                        <input type="hidden" name="actiontype" value="iciciagent">
                        {{ csrf_field() }}
                        <div class="form-group col-md-6">
                            <label>Status</label>
                            <select name="status" class="form-control" required>
                                <option value="">Select Type</option>
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-12">
                            <label>Remark</label>
                            <input type="text" name="remark" class="form-control" placeholder="Enter Vle Password" required="">

                        </div>
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
        var url = "{{url('statement/fetch')}}/iciciagentstatement/{{$id}}";
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
                "data": "bank",
                render: function(data, type, full, meta) {
                    return full.user.name + ` ( ` + full.user.id + ` )<br>` + full.user.mobile + ` ( ` + full.user.role.name + ` )`;
                }
            },
            {
                "data": "bank",
                render: function(data, type, full, meta) {
                    return `Merchant Name - ` + full.merchantName + `<br>Merchant Number - <a href="javascript:void(0)" class="viewIciciAgent">` + full.merchantPhoneNumber + `</a>`;
                }
            },
            {
                "data": "everify"
            },
            {
                "data": "remark"
            },
            {
                "data": "status",
                render: function(data, type, full, meta) {
                    if (full.status == "success" || full.status == "approved") {
                        var out = `<span class="badge badge-success">Approved</span>`;
                    } else if (full.status == "pending") {
                        var out = `<span class="badge badge-warning">Pending</span>`;
                    } else {
                        var out = `<span class="badge badge-danger">Rejected</span>`;
                    }

                    @if(Myhelper::hasRole('admin'))
                    out += `<br><button type="button" class="btn btn-primary btn-xs kcySubmit mt-5">Onboard</button>`;
                    out += `<br><button type="button" class="btn bg-warning btn-raised legitRipple btn-xs mt-5" onclick="editUtiid(` + full.id + `, '` + full.status + `')">Edit</button>`;
                    @endif

                    return out;
                }
            }
        ];

        DT = datatableSetup(url, options, onDraw);

        $('#datatable').on('click', '.viewIciciAgent', function() {
            var data = DT.row($(this).parent().parent()).data();
            $.each(data, function(index, values) {
                if (index == "aadharPic" || index == "pancardPic" || index == "passport" || index == "shoppic") {
                    if (data.user.role.slug == "apiuser") {
                        $("." + index).attr('href', values);
                    } else {
                        $("." + index).attr('href', "{{ asset('public') }}/" + values);
                    }
                } else {
                    $("." + index).text(values);
                }
            });
            $('#viewFullDataModal').modal();
        });

        $('#datatable').on('click', '.kcySubmit', function() {
            var data = DT.row($(this).parent().parent()).data();
            var inputdata = {
                'id': data.id,
                "transactionType": "useronboarded"
            };

            $.ajax({
                    url: `{{route('iaepstransaction')}}`,
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
                    data: inputdata
                })
                .done(function(data) {
                    swal.close();
                    if (data.status == "TXN") {
                        swal({
                            type: 'success',
                            title: "Success",
                            text: data.message,
                            onClose: () => {
                                $('#datatable').dataTable().api().ajax.reload();
                            },
                        });
                    } else {
                        swal.close();
                        notify('Oops', errors.message, 'warning');
                    }
                })
                .fail(function(errors) {
                    swal.close();
                    notify('Oops', errors.status + '! ' + errors.statusText, 'warning');
                });
        });

        $("#editUtiidForm").validate({
            rules: {
                status: {
                    required: true,
                },
                vleid: {
                    required: true,
                },
                vlepassword: {
                    required: true,
                },
            },
            messages: {
                name: {
                    required: "Please select status",
                },
                vleid: {
                    required: "Please enter vle id",
                },
                vlepassword: {
                    required: "Please enter vle password",
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

        $("#editModal").on('hidden.bs.modal', function() {
            $('#setupModal').find('form')[0].reset();
        });
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

                    if (index == "aadharPic" || index == "pancardPic" || index == "passport" || index == "shoppic") {

                    } else {
                        $("." + index).text(values);
                    }
                });
                $('#viewFullDataModal').modal();
            })
            .fail(function(errors) {
                notify('Oops', errors.status + '! ' + errors.statusText, 'warning');
            });
    }

    function editUtiid(id, status) {
        $('#editModal').find('[name="id"]').val(id);
        $('#editModal').find('[name="status"]').val(status).trigger('change');
        $('#editModal').modal('show');
    }
</script>
@endpush