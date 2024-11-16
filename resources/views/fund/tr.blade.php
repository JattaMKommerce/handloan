@extends('layouts.app')
@section('title', "Fund Transfer or Return")
@section('pagetitle', "Fund Transfer & Return")

@php
$table = "yes";
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
                                    <th>Name</th>
                                    <th>Parent Details</th>
                                    <th>Company Profile</th>
                                    <th>Wallet Details</th>
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



<div class="modal fade" id="transferModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Fund Transfer / Return</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="transferForm" action="{{route('fundtransaction')}}" method="post">
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" name="user_id">
                        {{ csrf_field() }}
                        <div class="form-group col-md-6">
                            <label>Fund Action</label>
                            <select name="type" class="form-control" id="select" required>
                                <option value="">Select Action</option>
                                @if (Myhelper::can('fund_transfer'))
                                <option value="transfer">Transfer</option>
                                @endif
                                @if (Myhelper::can('fund_return'))
                                <option value="return">Return</option>
                                @endif
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Amount</label>
                            <input type="number" name="amount" step="any" class="form-control numbertoword onlynumeric" placeholder="Enter Amount" required="">
                            <span class="wordscontainer"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label>Remark</label>
                            <input type="text" name="remark" class="form-control"  placeholder="Enter Remark">
                        
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>T-Pin</label>
                            <input type="password" name="pin" class="form-control" placeholder="Enter transaction pin" required="">
                            <a href="{{url('profile/view?tab=pinChange')}}" target="_blank" class="text-primary pull-right">Generate Or Forgot Pin??</a>
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
        var url = "{{url('statement/fetch')}}/tr/0";
        var onDraw = function() {
            $('input#membarStatus').on('click', function(evt) {
                evt.stopPropagation();
                var ele = $(this);
                var id = $(this).val();
                var status = "block";
                if ($(this).prop('checked')) {
                    status = "active";
                }

                $.ajax({
                        url: `{{ route('profileUpdate') }}`,
                        type: 'post',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: 'json',
                        data: {
                            'id': id,
                            'status': status
                        }
                    })
                    .done(function(data) {
                        if (data.status == "success") {
                            notify("Member Updated", 'success');
                        } else {
                            notify("Something went wrong, Try again.", 'warning');
                        }
                    })
                    .fail(function(errors) {
                        if (status == "active") {
                            ele.prop('checked', false);
                        } else {
                            ele.prop('checked', true);
                        }
                        showError(errors, "withoutform");
                    });
            });
        };
        var options = [{
                "data": "name",
                'className': "notClick",
                render: function(data, type, full, meta) {
                    var check = "";
                    if (full.kyc == "pending") {
                        check += `<span class="badge badge-warning">Kyc Pending</span>`;
                    } else {
                        check += `<span class="badge badge-success">Kyc Success</span>`;
                    }
                    return `<div>` + check + `<span class='text-inverse pull-right m-l-10'><b>` + full.id + `</b> </span>
                            <div class="clearfix"></div>
                        </div>
                        <span style='font-size:13px'>` + full.updated_at + `</span>`;
                }
            },
            {
                "data": "name",
                render: function(data, type, full, meta) {
                    return `<span class="name">` + full.name + `</span>` + `<br>` + full.mobile + `<br>` + full.role.name;
                }
            },
            {
                "data": "parents"
            },
            {
                "data": "name",
                render: function(data, type, full, meta) {
                    return `<span class="name">` + full.company.companyname + `</span>` + `<br>` + full.company.website;
                }
            },
            {
                "data": "name",
                render: function(data, type, full, meta) {
                    return `Main - ` + full.mainwallet + " /-<br>Locked - " + full.lockedamount + " /-";
                }
            },
            {
                "data": "action",
                render: function(data, type, full, meta) {
                    return `<button class="btn btn-primary" onclick="transfer('` + full.id + `')">Transfer / Return</button>`;
                }
            }
        ];

        datatableSetup(url, options, onDraw);

        $("#transferForm").validate({
            rules: {
                type: {
                    required: true
                },
                amount: {
                    required: true,
                    min: 1
                }
            },
            messages: {
                type: {
                    required: "Please select transfer action",
                },
                amount: {
                    required: "Please enter amount",
                    min: "Amount value should be greater than 0"
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
                var form = $('#transferForm');
                var type = $('#transferForm').find('[name="type"]').val();
                form.ajaxSubmit({
                    dataType: 'json',
                    beforeSubmit: function() {
                        form.find('button:submit').html('loading').attr('disabled',true).addClass('btn-secondary');
                        
                    },
                    complete: function() {
                        form.find('button:submit').html('Submit').attr('disabled',false).removeClass('btn-secondary');
                    },
                    success: function(data) {
                        swal.close();
                        if (data.status == "success") {
                            getbalance();
                            form.closest('.modal').modal('hide');
                            $('#transferModal').modal('hide');
                            notify("Fund " + type + " Successfull", 'success');
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

    function transfer(id) {
        $('#transferForm').find('[name="user_id"]').val(id);
        $('#transferModal').modal();
    }
</script>
@endpush