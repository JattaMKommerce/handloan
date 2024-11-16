@php
    $name = explode(" ", Auth::user()->name);
@endphp

@extends('layouts.app')
@section('title', "Pancard Service")
@section('pagetitle', "Pancard Service")
@php
    $table = "yes";
@endphp

@section('content')
<div class="content">
    @if(!$agent || $agent->status != "approved")
        <div class="row">
            <div class="col-sm-8 iq-card p-3 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">Aeps Service Registration</h4>
                    </div>
                    <div class="panel-body">
                        <form action="{{route('raepskyc')}}" method="post" id="transactionForm" target="_blank"> 
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>Name <span class="text-danger fw-bold">*</span></label>
                                    <input type="text" class="form-control" autocomplete="off" name="merchantName" placeholder="Enter Your Name" value="{{isset($name[0]) ? $name[0] : ''}}" required>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Shopname <span class="text-danger fw-bold">*</span></label>
                                    <input type="text" class="form-control" autocomplete="off" name="merchantShopname" value="{{Auth::user()->shopname}}" placeholder="Enter Your Shopname" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>Email <span class="text-danger fw-bold">*</span></label>
                                    <input type="email" class="form-control" autocomplete="off" name="merchantEmail" placeholder="Enter Your Email" value="{{Auth::user()->email}}" required>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Mobile <span class="text-danger fw-bold">*</span></label>
                                    <input type="text" pattern="[0-9]*" maxlength="10" minlength="10" class="form-control" name="merchantPhoneNumber" autocomplete="off" placeholder="Enter Your Mobile" value="{{Auth::user()->mobile}}" required>
                                </div>
                            </div>
                            @if(isset($error) && $error != "nul")
                                <p class="text-danger">{{$error}}</p>
                            @endif
                            <div class="form-group text-center">
                                <button type="submit" class="btn btn-primary bg-teal-400 btn-labeled btn-rounded legitRipple btn-lg" data-loading-text="<b><i class='fa fa-spin fa-spinner'></i></b> Submitting"><b><i class=" icon-paperplane"></i></b> Submit</button>
                            </div>
                        </form>
                    </div> 
                </div>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-sm-4 col-md-offset-4">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">Uti Pancard</h4>
                    </div>
                    <div class="panel-body p-0">
                        <table class="table table-bordered">
                            <tr><td>Agent Id</td><td>{{$agent->merchantLoginId}}</td></tr>
                            <tr><td>Name</td><td>{{$agent->merchantName}}</td></tr>
                            <tr><td>Email</td><td>{{$agent->merchantEmail}}</td></tr>
                            <tr><td>Phone</td><td>{{$agent->merchantPhoneNumber}}</td></tr>
                        </table>
                    </div>
                    <form action="{{route('utipay')}}" method="post" id="transactionForm">
                        {{ csrf_field() }}
                        <div class="panel-footer text-center">
                            <button type="submit" class="btn bg-teal-400 btn-labeled btn-rounded legitRipple btn-lg" data-loading-text="<b><i class='fa fa-spin fa-spinner'></i></b> Paying"><b><i class=" icon-paperplane"></i></b> Initiate Transaction</button>
                        </div>
                    </form>

                    @if(isset($error))
                        <div class="panel-footer text-center text-danger">
                            Error - {{$error}}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <form action="" method="post" target="_blank" id="panOpen">
            <textarea name="encdata" style="display:none"></textarea>
        </form>
    @endif
</div>
@endsection

@push('script')
<script type="text/javascript">
    $(document).ready(function () {
        $('form#transactionForm').submit(function() {
            var form= $(this);
            $(this).ajaxSubmit({
                dataType:'json',
                beforeSubmit:function(){
                    swal({
                        title: 'Wait!',
                        text: 'We are working on request.',
                        onOpen: () => {
                            swal.showLoading()
                        },
                        allowOutsideClick: () => !swal.isLoading()
                    });
                },
                success:function(data){
                    swal.close();
                    switch(data.statuscode){
                        case 'TXN':
                            $('#panOpen').attr('action', data.data.url);
                            $('#panOpen').find("[name='encdata']").val(data.data.encdata);
                            
                            $('#panOpen').submit();
                            break;
                        
                        default:
                            notify(data.message, 'danger');
                            break;
                    }
                },
                error: function(errors) {
                    swal.close();
                    if(errors.status == '400'){
                        notify(errors.responseJSON.message, 'danger');
                    }else{
                        swal(
                          'Oops!',
                          'Something went wrong, try again later.',
                          'error'
                        );
                    }
                }
            });
            return false;
        });
    });
</script>
@endpush