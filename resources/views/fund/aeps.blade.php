@extends('layouts.app')
@section('title', "Aeps Fund Request")
@section('pagetitle', "Aeps Fund Request")

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
                <div class="iq-card-header d-flex justify-content-between">
                    <div class="iq-header-title">

                    </div>
                    <div>
                     <!--  <button type="button" data-toggle="modal" data-target="#fundRequestModal" class="btn btn-primary" data-loading-text="<b><i class='fa fa-spin fa-spinner'></i></b> Searching"><b><i class="icon-plus2"></i></b> New Request</button>
                        <button type="button" data-toggle="modal" data-target="#fundRequestModals" class="btn btn-primary" data-loading-text="<b><i class='fa fa-spin fa-spinner'></i></b> Searching"><b><i class="icon-plus2"></i></b> Cyrus Payout</button> -->
                        <button type="button" data-toggle="modal" data-target="#fundRequestModalsRunpaisa" class="btn btn-primary" data-loading-text="<b><i class='fa fa-spin fa-spinner'></i></b> Searching"><b><i class="icon-plus2"></i></b>Payout</button>
                       
                        
                    </div>
                </div>
                <div class="iq-card-body">
                    <div class="table-responsive">
                        <table class="table" id="datatable">
                              <thead class="thead-light">
                                <tr>
                                    <th width="160px">#</th>
                                    <th>User Details</th>
                                    <th>Bank Details</th>
                                    <th>Refrence Details</th>
                                    <th width="200px">Amount</th>
                                    <th>Remark</th>
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
 <!--   
<div class="modal fade" id="fundRequestModals" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabels" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Cyrus Payout</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
           
            <form id="fundRequestForms" action="{{route('cyrustxn')}}" method="post">
                <div class="modal-body">
                    @if(Auth::user()->bank != '' && Auth::user()->ifsc != '' && Auth::user()->account != '')
                    <table class="table table-bordered p-b-15" cellspacing="0" style="margin-bottom: 30px">
                          <thead class="thead-light">
                            <tr>
                                <th>Select bank</th>
                                <th>Bank</th>
                                <th>Account</th>
                                <th>Ifsc</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach($userbanks as $payoutuser)
                              @if(!empty($payoutuser['account']))
                                <tr>
                                    <td><input type="radio" id="banktype" name="bankacccount" value="{{$payoutuser['account']}}-{{$payoutuser['ifsc']}}-{{$payoutuser['bank']}}"></td>
                                    <td>{{$payoutuser['bank']}}</td>
                                    <td>{{$payoutuser['account']}}</td>
                                    <td>{{$payoutuser['ifsc']}}</td>
                            
                                </tr>
                              @endif
                            @endforeach
                        </tbody>
                    </table>
                    @endif

                    <input type="hidden" name="user_id">
                    {{ csrf_field() }}
                    <div class="row">
                    @if(Auth::user()->bank == '' && Auth::user()->ifsc == '' && Auth::user()->account == '')

                    <div class="form-group col-md-6">
                        <label>Account Number</label>
                        <input type="text" class="form-control" name="account" placeholder="Enter Value" required="" value="{{Auth::user()->account}}">
                    </div>
                    <div class="form-group col-md-6">
                        <label>IFSC Code</label>
                        <input type="text" class="form-control" name="ifsc" placeholder="Enter Value" required="" value="{{Auth::user()->ifsc}}">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Bank Name</label>
                        <input type="text" class="form-control" name="bank" placeholder="Enter Value" required="" value="{{Auth::user()->bank}}">
                    </div>

                    @endif
                        <div class="form-group col-md-6">
                            <label>Wallet Type</label>
                            <select name="type" class="form-control" required>
                                <option value="">Select Wallet</option>
                                <option value="bank">Move To Bank</option>
                                <option value="wallet">Move To Wallet</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Amount</label>
                            <input type="number" class="form-control" name="amount" placeholder="Enter Value" required="">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Comments</label>
                            <input type="text" class="form-control" name="comments" placeholder="Enter Value" required="">
                        </div>
                        <div class="form-group col-md-6">
                            <label>T- PIN</label>
                            <input type="password" name="pin" class="form-control" placeholder="Enter transaction pin" required="">
                            <a href="{{url('profile/view?tab=pinChange')}}" target="_blank" class="text-primary pull-right">Generate or Forgot PIN?</a>
                        </div>
                    </div>
                    <p class="text-danger">Note - If you want to change bank details, please send mail with account details to update your bank details.</p>

                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Submitting">Submit</button>
                    </div>
            </form>

        </div>
    </div>
</div> -->
<div class="modal fade" id="fundRequestModalsRunpaisa" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabels" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Payout</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
           
            <form id="fundRequestFormsRunpaisa" action="{{route('runpaisatxn')}}" method="post">
                <div class="modal-body">
                    @if(Auth::user()->bank != '' && Auth::user()->ifsc != '' && Auth::user()->account != '')
                    <table class="table table-bordered p-b-15" cellspacing="0" style="margin-bottom: 30px">
                          <thead class="thead-light">
                            <tr>
                                <th>Select bank</th>
                                <th>Bank</th>
                                <th>Account</th>
                                <th>Ifsc</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach($userbanks as $payoutuser)
                              @if(!empty($payoutuser['account']))
                                <tr>
                                    <td><input type="radio" id="banktype" name="bankacccount" value="{{$payoutuser['account']}}-{{$payoutuser['ifsc']}}-{{$payoutuser['bank']}}"></td>
                                    <td>{{$payoutuser['bank']}}</td>
                                    <td>{{$payoutuser['account']}}</td>
                                    <td>{{$payoutuser['ifsc']}}</td>
                            
                                </tr>
                              @endif
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                    

                    <input type="hidden" name="user_id">
                    {{ csrf_field() }}
                    <div class="row">
                    @if(Auth::user()->bank == '' && Auth::user()->ifsc == '' && Auth::user()->account == '')

                    <div class="form-group col-md-6">
                        <label>Account Number <span class="text-danger fw-bold">*</span></label>
                        <input type="text" class="form-control" name="account" placeholder="Enter Value" required="" value="{{Auth::user()->account}}">
                    </div>
                    <div class="form-group col-md-6">
                        <label>IFSC Code <span class="text-danger fw-bold">*</span></label>
                        <input type="text" class="form-control" name="ifsc" placeholder="Enter Value" required="" value="{{Auth::user()->ifsc}}">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Bank Name <span class="text-danger fw-bold">*</span></label>
                        <input type="text" class="form-control" name="bank" placeholder="Enter Value" required="" value="{{Auth::user()->bank}}">

                    </div>

                    @endif
                        <div class="form-group col-md-6">
                            <label>Wallet Type <span class="text-danger fw-bold">*</span></label>
                            <select name="type" class="form-control" required>
                                <option value="">Select Wallet</option>
                                <option value="bank">Move To Bank</option>
                                <option value="wallet">Move To Wallet</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Amount <span class="text-danger fw-bold">*</span></label>
                            <input type="number" class="form-control" name="amount" placeholder="Enter Value" required="">
                        </div>
                        {{-- <div class="form-group col-md-6">
                            <label>Comments <span class="text-danger fw-bold">*</span></label>
                            <input type="text" class="form-control" name="comments" placeholder="Enter Value" required="">
                        </div> --}}
                        <div class="form-group col-md-6">
                            <label>T- PIN <span class="text-danger fw-bold">*</span></label>
                            <input type="password" name="pin" class="form-control" placeholder="Enter transaction pin" required="">
                            <a href="{{url('profile/view?tab=pinChange')}}" target="_blank" class="text-primary pull-right">Generate or Forgot PIN?</a>
                        </div>
                    </div>
                    {{-- <p class="text-danger">Note - If you want to change bank details, please send mail with account details to update your bank details.</p> --}}

                    <div class="modal-footer">
                        @if(empty($userbanks[2]['account']) || empty($userbanks[1]['account']))
                        <button onclick="addbank()"  class="btn btn-primary" type="button" data-loading-text="<i class='fa fa-spin fa-spinner'></i>">Add Bank</button>
                        @endif
                        <button class="btn btn-primary" type="submit" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Submitting">Submit</button>
                    </div>
            </form>

        </div>
    </div>
</div>
</div>

<div class="modal fade" id="addbankAccountmodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabels" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Bank Account</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
           
            <form id="addbankAcc" action="{{route('addbankAcc')}}" method="post">
                <div class="modal-body">
                    <input type="hidden" name="user_id">
                    {{ csrf_field() }}
                    <div class="row">
                    <div class="form-group col-md-6">
                        <label>Account Number <span class="text-danger fw-bold">*</span></label>
                        <input type="text" class="form-control" name="account" placeholder="Enter Value" required="" value="">
                    </div>
                    <div class="form-group col-md-6">
                        <label>IFSC Code <span class="text-danger fw-bold">*</span></label>
                        <input type="text" class="form-control" name="ifsc" placeholder="Enter Value" required="" value="">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Bank Name <span class="text-danger fw-bold">*</span></label>
                        <input type="text" class="form-control" name="bankname" placeholder="Enter Value" required="" value="">
                    </div>
                        
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Submitting">Submit</button>
                    </div>
            </form>

        </div>
    </div>
</div>


<div class="modal fade" id="fundRequestModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Aeps Fund Request</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="fundRequestForm" action="{{route('fundtransaction')}}" method="post">
                <div class="modal-body">
                    @if(Auth::user()->bank != '' && Auth::user()->ifsc != '' && Auth::user()->account != '')
                    <table class="table table-bordered p-b-15" cellspacing="0" style="margin-bottom: 30px">
                          <thead class="thead-light">
                            <tr>
                                <th>Select bank</th>
                                <th>Account</th>
                                <th>Name</th>
                                <th>Bank</th>
                                <th>Ifsc</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payoutusers as $payoutuser)
                            <tr>
                                <td><input type="radio" id="banktype" name="beniid" value="{{$payoutuser->bene_id}}"></td>
                                <td>{{$payoutuser->account}}</td>
                                <td>{{$payoutuser->account}}</td>
                                <td>{{$payoutuser->bankname}}</td>
                                <td>{{$payoutuser->ifsc}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif

                    <input type="hidden" name="user_id">
                    {{ csrf_field() }}
                    <div class="row">
                        @if(Auth::user()->bank == '' && Auth::user()->ifsc == '' && Auth::user()->account == '')

                        <div class="form-group col-md-6">
                            <label>Account Number</label>
                            <input type="text" class="form-control" name="account" placeholder="Enter Value" required="" value="{{Auth::user()->account}}">
                        </div>
                        <div class="form-group col-md-6">
                            <label>IFSC Code</label>
                            <input type="text" class="form-control" name="ifsc" placeholder="Enter Value" required="" value="{{Auth::user()->ifsc}}">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Bank Name</label>
                            <input type="text" class="form-control" name="bank" placeholder="Enter Value" required="" value="{{Auth::user()->bank}}">
                        </div>

                        @endif

                        <div class="form-group col-md-6">
                            <label>Wallet Type</label>
                            <select name="type" class="form-control" required>
                                <option value="">Select Wallet</option>
                                <option value="bank">Move To Bank</option>
                                <option value="wallet">Move To Wallet</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Amount</label>
                            <input type="number" class="form-control" name="amount" placeholder="Enter Value" required="">
                        </div>
                        <div class="form-group col-md-6">
                            <label>T- PIN</label>
                            <input type="password" name="pin" class="form-control" placeholder="Enter transaction pin" required="">
                            <a href="{{url('profile/view?tab=pinChange')}}" target="_blank" class="text-primary pull-right">Generate or Forgot PIN?</a>
                        </div>
                    </div>
                    <p class="text-danger">Note - If you want to change bank details, please send mail with account details to update your bank details.</p>

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
        var url = "{{url('statement/fetch')}}/aepsfundrequest/0";
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
                    if (full.type == "wallet") {
                        return "Wallet"
                    } else {
                        return full.account + " ( " + full.bank + " )<br>" + full.ifsc;
                    }
                }
            },
            {
                "data": "bank",
                render: function(data, type, full, meta) {
                    if (full.type == "wallet") {
                        return "Wallet"
                    } else {
                        if (full.pay_type == "payout") {
                            return "Ref - " + full.payoutref + "<br>Txnid - " + full.payoutid;
                        } else {
                            return "Manual";
                        }
                    }
                }
            },
            {
                "data": "description",
                render: function(data, type, full, meta) {
                    return `<span class='text-inverse'><i class="fa fa-rupee"></i> ` + full.amount + `</span> / ` + full.type;
                }
            },
            {
                "data": "remark"
            },
            {
                "data": "action",
                render: function(data, type, full, meta) {
                    if (full.status == "approved") {
                        var btn = '<span class="badge badge-success text-uppercase"><b>' + full.status + '</b></span>';
                    } else if (full.status == 'pending') {
                        var btn = '<span class="badge badge-warning text-uppercase"><b>' + full.status + '</b></span>';
                    } else {
                        var btn = '<span class="badge badge-danger text-uppercase"><b>' + full.status + '</b></span>';
                    }
                    return btn;
                }
            }
        ];

        datatableSetup(url, options, onDraw);


        $("#addbankAcc").validate({
                rules: {
                    account: {
                        required: true,
                        number:true,
                    },
                    bank: {
                        required: true,
                    },
                    ifsc: {
                        required: true,
                    },

                },
                messages: {
                    account: {
                        required: "Please enter account number",
                        number: "Mobile number should be numeric",

                    },
                    bank: {
                        required: "Please enter bank name",
                    },
                    ifsc: {
                        required: "Please enter ifsc number",
                    },
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
                    var form = $('#addbankAcc');
                    form.ajaxSubmit({
                        dataType: 'json',
                        beforeSubmit: function() {
                            form.find('button[type="submit"]').html('loading').attr('disabled', true).addClass('btn-secondary');
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
                            form.find('button:submit').html('Submit').attr('disabled', false).removeClass('btn-secondary');
                        },
                        success: function(data) {
                            swal.close();
                            if (data.statuscode == "TXN" || data.statuscode == "success") {
                                notify(data.message, 'success');
                            } else {
                                if (data.message == null || data.message == "") {
                                    notify("Try after Sometimes", 'error');
                                }
                                notify(data.message, 'error');
                            }
                             $('#addbankAccountmodal').modal('hide');
                             location.reload();

                        },
                        error: function(errors) {
                            showError(errors, form);
                            notify("Try after Sometimes1", 'error');
                        }
                    });
                }
            });

        $("#fundRequestForm").validate({
            rules: {
                amount: {
                    required: true
                },
                type: {
                    required: true
                },
            },
            messages: {
                amount: {
                    required: "Please enter request amount",
                },
                type: {
                    required: "Please select request type",
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
                        form.find('button:submit').html('loading').attr('disabled',true).addClass('btn-secondary');
                    },
                    complete: function() {
                        form.find('button:submit').html('Submit').attr('disabled',false).removeClass('btn-secondary');
                    },
                    success: function(data) {
                        if (data.status == "success") {
                            form.closest('.modal').modal('hide');
                            notify("Fund Request submitted Successfull", 'success');
                            $('#datatable').dataTable().api().ajax.reload();
                            $('#fundRequestModal').modal('hide');
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

        $("#fundRequestForms").validate({
            rules: {
                amount: {
                    required: true
                },
                type: {
                    required: true
                },
            },
            messages: {
                amount: {
                    required: "Please enter request amount",
                },
                type: {
                    required: "Please select request type",
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
                var form = $('#fundRequestForms');
                form.ajaxSubmit({
                    dataType: 'json',
                    beforeSubmit: function() {
                        form.find('button:submit').html('loading').attr('disabled',true).addClass('btn-secondary');
                    },
                    complete: function() {
                        form.find('button:submit').html('reset').attr('disabled',false).removeClass('btn-secondary');
                    },
                    success: function(data) {
                        if (data.status == "success") {
                            form.closest('.modal').modal('hide');
                            notify("Fund Request submitted Successfull", 'success');
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
        $("#fundRequestFormsRunpaisa").validate({
            rules: {
                amount: {
                    required: true
                },
                type: {
                    required: true
                },
            },
            messages: {
                amount: {
                    required: "Please enter request amount",
                },
                type: {
                    required: "Please select request type",
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
                var form = $('#fundRequestFormsRunpaisa');
                form.ajaxSubmit({
                    dataType: 'json',
                    beforeSubmit: function() {
                        form.find('button:submit').html('loading').attr('disabled',true).addClass('btn-secondary');
                    },
                    complete: function() {
                        form.find('button:submit').html('Submit').attr('disabled',false).removeClass('btn-secondary');
                    },
                    success: function(data) {
                        if (data.status == "success") {
                            form.closest('.modal').modal('hide');
                            notify("Fund Request submitted Successfull", 'success');
                            $('#datatable').dataTable().api().ajax.reload();
                            $('#fundRequestModalsRunpaisa').modal('hide');
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

    function addbank(){
        $('#fundRequestModalsRunpaisa').modal('hide');
        $('#addbankAccountmodal').on('hidden.bs.modal', function () {
        $('#addbankAcc').trigger('reset');
})
        $('#addbankAccountmodal').modal();
    }
</script>
@endpush