@extends('layouts.app')
@section('title', "Payout Request")
@section('pagetitle', "Payout Request")

@php
$table = "yes";
$export = "payoutrequestview";
$status['type'] = "Fund";
$status['data'] = [
"success" => "Success",
"pending" => "Pending",
"failed" => "Failed",
"approved" => "Approved",
"rejected" => "Rejected",
];

$product['type'] = "Transaction";
$product['data'] = [
"wallet" => "Move To Wallet",
"bank" => "Move To Bank"
];

@endphp

@section('content')

<div class="content">
    <div class="row">
        <div class="col-sm-12">
            <div class="iq-card">
                <div class="iq-card-body">
                    <div class="table-responsive">
                        <!--<span class="table-add float-right mb-3 mr-2">-->
                        <!--      <button class="btn btn-sm submit-button text-white" data-toggle="modal" data-target="#payoutRequestModal"><i class="ri-add-fill"><span class="pl-1">Add New Payout Request</span></i>-->
                        <!--      </button>-->
                        <!--</span>-->
                        <span class="table-add float-right mb-3 mr-2">
                              <button class="btn btn-sm submit-button text-white" data-toggle="modal" data-target="#beneModal"><i class="ri-add-fill"><span class="pl-1">Add New Beneficiary</span></i>
                              </button>
                        </span>
                        <table class="table" id="datatable">
                              <thead class="thead-light">
                                <tr>
                                    <th width="160px">#</th>
                                    <th>User Details</th>
                                    <th>Account Number</th>
                                    <th>IFSC</th>
                                   <th>Bank</th>
                                    <th width="100px">Action</th>
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
<div class="modal fade" id="beneModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add New Bene</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="beneForm" action="{{route('fundtransaction')}}" method="post">
                <div class="modal-body">
                    <input type="hidden" name="user_id">
                    <input type="hidden" name="type" value="addbene">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label> Account Holder Name</label>
                            <input type="text" class="form-control" name="name" required="">
                        </div>
                        <div class="form-group col-md-6">
                            <label> Bank</label>
                            <select name="bank" class="form-control " required="">
                                <option value="">Select Bank</option>
                                @foreach($banks as $val)
                                    <option value="{{$val->name}}">{{$val->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Account Number</label>
                            <input type="number" name="account_number" step="any" class="form-control" placeholder="Enter Account Number" required="">
                        </div>
                        
                        <div class="form-group col-md-6">
                            <label>IFSC</label>
                            <input type="text" name="ifsc" class="form-control " placeholder="Enter IFSC" required="">
                        </div>
                        <div class="form-group col-md-6">
                            <label>TPIN</label>
                            <input type="password" name="pin" class="form-control " placeholder="Enter TPIN" required="">
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
<div class="modal fade" id="payoutRequestModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Wallet Fund Request</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="payoutRequestForm" action="{{route('fundtransaction')}}" method="post">
                <div class="modal-body">
                    <input type="hidden" name="user_id">
                    <input type="hidden" name="bene_id">
                    <input type="hidden" name="type" value="payoutTransfer">
                    {{ csrf_field() }}
                    <div class="row">
                        
                        <div class="form-group col-md-6">
                            <label>Amount</label>
                            <input type="number" name="amount" step="any" class="form-control onlynumeric numbertoword" placeholder="Enter Amount" required="">
                            <span class="wordscontainer"></span>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Payment Mode</label>
                            <select name="paymode" class="form-control" id="select" required>
                                <option value="">Select Paymode</option>
                                @foreach ($paymodes as $paymode)
                                <option value="{{$paymode->name}}">{{$paymode->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        
                        <div class="form-group col-md-12">
                            <label>Remark</label>
                            <textarea name="remark" class="form-control" rows="2" placeholder="Enter Remark"></textarea>
                        </div>
                        <div class="form-group col-md-6">
                            <label>TPIN</label>
                            <input type="password" name="pin" class="form-control " placeholder="Enter TPIN" required="">
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
@if (Myhelper::hasRole('admin'))

<div class="modal fade" id="transferModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Fund Request Form</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="transferForm" method="post" action="{{ route('statementUpdate') }}">
                <div class="modal-body">
                    {!! csrf_field() !!}
                    <input type="hidden" name="id">
                    <input type="hidden" name="actiontype" value="payout">
                    <div class="form-group">
                        <label>Action Type</label>
                        <select class="form-control" name="status" required="">
                            <option value="">Select Action Type</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Reject</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Ref No</label>
                        <input text="text" name="payoutref" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Remark</label>
                        <textarea name="remark" class="form-control" rows="3" placeholder="Enter Value"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                   <button class="btn btn-primary" type="submit" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Submitting">Submit</button>
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
                "data": "account",
                render: function(data, type, full, meta) {
                    return full.username ;
                }
            },
            {
                "data": "account",
                
            },
            {
                "data": "ifsc",
                
            },
            {
                "data": "bankname",
                
            },
            
            {
                "data": "action",
                render: function(data, type, full, meta) {
                    if (full.status == "success") {
                        var btn = `<span class="badge badge-success">${full?.status}</span>`;
                    } else if (full.status == 'pending') {
                        var btn = `<span class="badge badge-warning">${full?.status}</span>`;
                    } else {
                        var btn = `<span class="badge badge-danger">${full?.status}</span>`;
                    }
                    btn += `<br><a href="javascript::void(0)" data-toggle="modal" data-target="#payoutRequestModal" class="btn btn-primary" onclick="transfer('` + full.id + `')">Transfer</a>`;
                 
                    return btn;
                }
            }
        ];

        datatableSetup(url, options, onDraw);

        $('form#transferForm').submit(function() {
            var form = $(this);
            $(this).ajaxSubmit({
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
                success: function(data) {
                    swal.close();
                     if (data.status == "success") {
                         form[0].reset();
                        notify('Fund request successfully updated', 'success');
                        $('#transferModal').modal('hide');
                        $('#datatable').dataTable().api().ajax.reload();
                    } else {
                        notify('Something went wrong', 'error');
                    }
                },
                error: function(errors) {  
                    notify(errors.statusText, 'error');
                    swal.close();
                }
            });
            return false;
        });

        $("#transferModal").on('hidden.bs.modal', function() {
            $('#transferModal').find('form')[0].reset();
            $('#transferForm').find('input[name="id"]').val('');
            $('#transferModal').find('.payeename').text('');
        });
         $("#beneForm").validate({
            rules: {
                name: {
                    required: true,
                },
                
            },
            messages: {
                name: {
                    required: "Please enter name",
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
                var form = $('form#beneForm');
                form.find('span.text-danger').remove();
                $('form#beneForm').ajaxSubmit({
                    dataType: 'json',
                    beforeSubmit: function() {
                        form.find('button:submit').button('loading');
                    },
                    complete: function() {
                        form.find('button:submit').button('reset');
                    },
                    success: function(data) {
                        if (data.status == "success") {
                            form[0].reset();
                            $('select').val('');
                            $('select').trigger('change');
                            notify("Record Successfully Created", 'success');
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
        $("#payoutRequestForm").validate({
            rules: {
                amount: {
                    required: true,
                },
                paymode:{
                    required: true,
                },
                pin:{
                    required: true,
                }
                
            },
            messages: {
                amount: {
                    required: "Please enter amount",
                },
                paymode:{
                    required: "Please select mode",
                },
                pin:{
                    required: "Please enter pin",
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
                var form = $('form#payoutRequestForm');
                form.find('span.text-danger').remove();
                $('form#payoutRequestForm').ajaxSubmit({
                    dataType: 'json',
                    beforeSubmit: function() {
                        form.find('button[type="submit"]').button('loading');
                        form.find('button[type="submit"]').css('display','none');
                    },
                    complete: function() {
                        form.find('button[type="submit"]').button('reset');
                        form.find('button[type="submit"]').css('display','block');
                    },
                    success: function(data) {
                        if (data.status == "success") {
                            form[0].reset();
                            $('select').val('');
                            $('select').trigger('change');
                            notify("Record Successfully Created", 'success');
                            //window.location.href = "{{url('fund/payoutrequest')}}";
                        } else {
                            notify(data.status, 'warning');
                            //window.location.href = "{{url('fund/payoutrequest')}}";
                        }
                    },
                    error: function(errors) {
                        showError(errors, form);
                    }
                });
            }
        });
    });

    function transfer(id, name) {
        
        $('#payoutRequestForm').find('input[name="bene_id"]').val(id);
        //$('#transferModal').modal();
    }
    
   
</script>
@endpush