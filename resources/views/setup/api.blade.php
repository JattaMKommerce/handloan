@extends('layouts.app')
@section('title', 'Api Manager')
@section('pagetitle', 'Api Manager')
@php
$table = "yes";


$agentfilter = "hide";
$status['type'] = "Api";
$status['data'] = [
"1" => "Active",
"0" => "De-active"
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
                                    <th>Product Name</th>
                                    <th>Display Name</th>
                                    <th>Api Code</th>
                                    <th>Credentials</th>
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
</div>

<div class="modal fade" id="setupModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title msg" id="exampleModalLabel">Add Api</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="setupManager" action="{{route('setupupdate')}}" method="post">
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" name="id">
                        <input type="hidden" name="actiontype" value="api">
                        {{ csrf_field() }}
                        <div class="form-group col-md-6">
                            <label>Product Name</label>
                            <input type="text" name="product" class="form-control" placeholder="Enter value" required="">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Display Name</label>
                            <input type="text" name="name" class="form-control" placeholder="Enter value" required="">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Url</label>
                            <input type="text" name="url" class="form-control" placeholder="Enter url">
                        </div>

                        <div class="form-group col-md-6">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control" placeholder="Enter Value">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Password</label>
                            <input type="text" name="password" class="form-control" placeholder="Enter url">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Optional1</label>
                            <input type="text" name="optional1" class="form-control" placeholder="Enter Value">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Api Code</label>
                            <input type="text" name="code" class="form-control" placeholder="Enter url" required="">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Commission/Charge</label>
                            <input type="text" name="commissionCharge" class="form-control" placeholder="Commission or Charge" required="">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Product Type</label><br>
                            <select name="type" class="form-control" required>
                                <option value="">Select Type</option>
                                <option value="recharge">Recharge</option>
                                <option value="bill">Bill Payment</option>
                                <option value="money">Money transfer</option>
                                <option value="pancard">Pancard</option>
                                <option value="fund">Fund</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Commission Type</label><br>
                            <select name="commissiontype" class="form-control" required>
                                <option value="">Select Type</option>
                                <option value="percent">Percent</option>
                                <option value="flat">Flat</option>
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
@endsection

@push('script')
<script type="text/javascript">
    $(document).ready(function() {
        var url = "{{url('statement/fetch')}}/setup{{$type}}/0";

        var onDraw = function() {
            $('[data-popup="popover"]').popover({
                template: '<div class="popover border-teal-400"><div class="arrow"></div><h3 class="popover-title bg-teal-400"></h3><div class="popover-content"></div></div>'
            });

            $('input.apiStatusHandler').on('click', function(evt) {
                evt.stopPropagation();
                var ele = $(this);
                var id = $(this).val();
                var status = "0";
                if ($(this).prop('checked')) {
                    status = "1";
                }

                $.ajax({
                        url: `{{ route('setupupdate') }}`,
                        type: 'post',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: 'json',
                        data: {
                            'id': id,
                            'status': status,
                            "actiontype": "api"
                        }
                    })
                    .done(function(data) {
                        if (data.status == "success") {
                            notify("Api Status Updated", 'success');
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
                "data": "product"
            },
            {
                "data": "name"
            },
            {
                "data": "code"
            },
            {
                "data": "action",
                render: function(data, type, full, meta) {
                    return `<a href="javascript:void(0)" data-popup="popover" data-placement="top" title="" data-html="true" data-trigger="hover" data-content="Url - ` + full.url + `<br>Username - ` + full.username + `<br>Password - ` + full.password + `<br>Optional - ` + full.optional1 + `" data-original-title="` + full.product + `">Api Credentials</a>`;
                }
            },
            {
                "data": "name",
                render: function(data, type, full, meta) {
                    var check = "";
                    if (full.status == "1") {
                        check = "checked='checked'";
                    }

                    return `<div class="custom-control custom-switch custom-control-inline">
                              <input type="checkbox" class="custom-control-input apiStatusHandler" id="apiStatus_${full.id}" ${check} value="` + full.id + `" actionType="` + type + `">
                              <label class="custom-control-label" for="apiStatus_${full.id}"></label>
                           </div>`;
                }
            },
            {
                "data": "action",
                render: function(data, type, full, meta) {
                    return `<button type="button" class="btn btn-primary" onclick="editSetup(` + full.id + `, \`` + full.product + `\`, \`` + full.name + `\`, \`` + full.url + `\`, \`` + full.username + `\`, \`` + full.password + `\`, \`` + full.optional1 + `\`, \`` + full.code + `\`, \`` + full.type + `\`,\`` + full.commissiontype + `\`,\`` + full.commissionCharge + `\`)"> Edit</button>`;
                }
            },
        ];
        datatableSetup(url, options, onDraw);

        $("#setupManager").validate({
            rules: {
                name: {
                    required: true,
                },
                product: {
                    required: true,
                },
                code: {
                    required: true,
                },
            },
            messages: {
                name: {
                    required: "Please enter display name",
                },
                product: {
                    required: "Please enter product name",
                },
                url: {
                    required: "Please enter api url",
                },
                code: {
                    required: "Please enter api code",
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
                        swal.close();
                        if (data.status == "success") {
                            if (id == "new") {
                                form[0].reset();
                            }
                            form.find('button[type="submit"]').button('reset');
                            notify("Task Successfully Completed", 'success');
                            $('#datatable').dataTable().api().ajax.reload();
                            $('#setupModal').modal('hide');
                        } else {
                            notify(data.status, 'warning');
                        }
                    },
                    error: function(errors) {
                        swal.close();
                        showError(errors, form);
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

    function editSetup(id, product, name, url, username, password, optional1, code, type, commissiontype, commissionCharge) {
        $('#setupModal').find('.msg').text("Edit");
        $('#setupModal').find('input[name="id"]').val(id);
        $('#setupModal').find('input[name="product"]').val(product);
        $('#setupModal').find('input[name="name"]').val(name);
        $('#setupModal').find('input[name="url"]').val(url);
        $('#setupModal').find('input[name="username"]').val(username);
        $('#setupModal').find('input[name="password"]').val(password);
        $('#setupModal').find('input[name="optional1"]').val(optional1);
        $('#setupModal').find('input[name="code"]').val(code);
        $('#setupModal').find('[name="type"]').select2().val(type).trigger('change');
        $('#setupModal').find('[name="commissiontype"]').select2().val(commissiontype).trigger('change');
        $('#setupModal').find('input[name="commissionCharge"]').val(commissionCharge);
        $('#setupModal').modal('show');

    }
</script>
@endpush