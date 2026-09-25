@php
    $name = explode(" ", Auth::user()->name);
@endphp

@extends('layouts.app')
@section('title', "Aeps Service")
@section('pagetitle', "Aeps Service")
@php
    $table = "yes";
@endphp

@section('content')
<div class="content">
    @if(!$agent || $agent->status == "rejected")
        <div class="row">
            <div class="col-sm-8 col-md-offset-2">
                <div class="card iq-card iq-mb-3">
                    
                    <div class="iq-card-body">
                        <h4 class="card-title">User Onboarding Form</h4>
                        <form action="{{route('raepskyc')}}" method="post" id="transactionForm" target="_blank"> 
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>Name </label>
                                    <input type="text" class="form-control" autocomplete="off" name="merchantName" placeholder="Enter Your Name" value="{{isset($name[0]) ? $name[0] : ''}}" required>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Shopname</label>
                                    <input type="text" class="form-control" autocomplete="off" name="merchantShopname" value="{{Auth::user()->shopname}}" placeholder="Enter Your Shopname" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>Email </label>
                                    <input type="email" class="form-control" autocomplete="off" name="merchantEmail" placeholder="Enter Your Email" value="{{Auth::user()->email}}" required>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Mobile</label>
                                    <input type="text" pattern="[0-9]*" maxlength="10" minlength="10" class="form-control" name="merchantPhoneNumber" autocomplete="off" placeholder="Enter Your Mobile" value="{{Auth::user()->mobile}}" required>
                                </div>
                            </div>
                            @if(isset($error) && $error != "nul")
                                <p class="text-danger">{{$error}}</p>
                            @endif
                            <div class="form-group text-center">
                                <button type="submit" class="btn bg-teal-400 btn-labeled btn-rounded legitRipple btn-lg" data-loading-text="<b><i class='fa fa-spin fa-spinner'></i></b> Submitting"><b><i class=" icon-paperplane"></i></b> Submit</button>
                            </div>
                        </form>
                    </div> 
                </div>
            </div>
        </div>
    @elseif($agent->status == "pending") 
     <div class="row">
        <div class="col-sm-4 col-md-offset-4">
            <div class="card iq-card iq-mb-3">
                
                <div class="iq-card-body">
                     <h4 class="card-title">Aeps KYC Status</h4>
                    <table class="table table-bordered">
                        <tr><td>Agent ID</td><td>{{$agent->merchantLoginId ?? " "}}</td></tr>
                        <tr><td>Agent Mobile</td><td>{{$agent->merchantPhoneNumber ?? ""}}</td></tr>
                         <tr><td>Remark</td><td>{{$agent->remark ?? "Your KYC is under pending"}}</td></tr>
                    </tbody></table>
                </div>
                <div class="panel-footer text-center">
                  <a  onclick="activate()" class="btn bg-teal-400  btn-rounded legitRipple text-center" data-loading-text="<b><i class='fa fa-spin fa-spinner'></i></b> Checking..">Activate the user</a>
                        <a  onclick="status('{{$agent->id}}','ragentstatus')" class="btn bg-teal-400  btn-rounded legitRipple text-center" data-loading-text="<b><i class='fa fa-spin fa-spinner'></i></b> Checking.."> Check Status</a>
                   </div>
                @if(isset($error))
                    <div class="panel-footer text-center text-danger">
                        Error - {{$error}}
                    </div>
                @endif
            </div>
        </div>
    </div>
    @else
        <div class="row">
            <div class="col-sm-12 col-sm-offset-2">
                <div class="tabbable">
                    <div class="iq-card">
                         <div class="panel-heading p-0">
                            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                <li class="nav-item">
                                    <a href="#cw-tab" onclick="AEPSTAB('BE')"class="nav-link" data-toggle="tab" class="legitRipple active" id="BE" aria-expanded="false">Balance Enquiry</a>
                                </li>
                                <li class="nav-item">
                                    <a href="#cw-tab" onclick="AEPSTAB('MS')" class="nav-link" id="MS" data-toggle="tab" class="legitRipple" aria-expanded="false">Mini Statement</a>
                                </li>
                                <li class="nav-item">
                                    <a href="#cw-tab" id="CW" onclick="AEPSTAB('CW')" class="nav-link" data-toggle="tab" class="legitRipple" aria-expanded="false">Cash Withdrawal</a>
                                </li>
                                <li class="nav-item">
                                    <a href="#cw-tab" onclick="AEPSTAB('M')" id="M" class="nav-link" data-toggle="tab" class="legitRipple" aria-expanded="false">Aadhaar Pay</a>
                                </li>
                            </ul>
                        </div>

                        <div class="tab-content">
                            <div class="tab-pane active" id="cw-tab">
                                <form action="{{route('raepspay')}}" method="POST" id="aepsTransactionForm" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    <div class="iq-card-body">
                                        <input type="hidden" name="transactionType" id="transactionType" value="BE">
                                        <input type="hidden" name="aeps" value="">
                                        <div class="row">
                                            
                                              <div class="form-group col-md-12">
                                                <label>Device Type :</label>
                                                <div class="row mb-20">
                                                    <div class="col-md-2">
                                                        <div class="md-radio m-b-0">
                                                            <input autocomplete="off" type="radio" value="MORPHO_PROTOBUF" id="MORPHO_PROTOBUF" name="device" >
                                                            <label for="MORPHO_PROTOBUF" style="padding: 0 25px">MORPHO</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="md-radio m-b-0">
                                                            <input autocomplete="off" type="radio" value="MANTRA_PROTOBUF" id="MANTRA_PROTOBUF" name="device">
                                                            <label for="MANTRA_PROTOBUF" style="padding: 0 25px">MANTRA</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="md-radio m-b-0">
                                                            <input autocomplete="off" type="radio" value="SECUGEN_PROTOBUF" id="SECUGEN_PROTOBUF" name="device" >
                                                            <label for="SECUGEN_PROTOBUF" style="padding: 0 25px">SECUGEN</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="md-radio m-b-0">
                                                            <input autocomplete="off" type="radio" value="TATVIK_PROTOBUF" id="TATVIK_PROTOBUF" name="device" >
                                                            <label for="TATVIK_PROTOBUF" style="padding: 0 25px">TATVIK</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="md-radio m-b-0">
                                                            <input autocomplete="off" type="radio" value="STARTEK_PROTOBUF" id="STARTEK_PROTOBUF" name="device" >
                                                            <label for="STARTEK_PROTOBUF" style="padding: 0 25px">STARTEK</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="md-radio m-b-0">
                                                            <input autocomplete="off" type="radio" value="PB100_PROTOBUF" id="PB100_PROTOBUF" name="device" >
                                                            <label for="PB100_PROTOBUF" style="padding: 0 25px">PB100</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            
                                            
                                                    
                                            </div>
                                            <input type="hidden" id="txtPidData" name="txtPidData" value="" class="form-control">
                                            
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label>Mobile Number :</label>
                                                <input type="text"  class="form-control" name="mobileNumber" id="mobileNumber" maxlength="10"  autocomplete="off" placeholder="Enter mobile number" required>
                                                
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Aadhaar Number :</label>
                                                <input type="text" class="form-control" name="adhaarNumber" id="adhaarNumber" maxlength="12" minlength="12" autocomplete="off" pattern="[0-9]*"  placeholder="Enter aadhar number" required="">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label>Bank :</label>
                                                <select name="bankid" class="form-control select" required="">
                                                    <option value="">Select Bank</option>         
                                                    @foreach ($bankName as $bank)
                                                        <option value="{{$bank->iinno}}">{{$bank->bankName}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                             <div class="form-group col-md-6">
                                                <label>Select Pipe :</label>
                                                 <select name="pipe" class="form-control select" required="">
                                                  <option value="bank2">Bank2</option>
                                                   <option value="bank3">Bank3</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-6 transactionAmount">
                                                
                                            </div>
                                            
                                       </div>
                                       <div class="row">
                                          
                                       </div>
                                <div class="row">
                                    <div class="panel-footer text-center">
                                    @if($agent->status == "approved")
                                        <button type="submit" class="btn bg-slate-800 btn-lg btn-raised legitRipple" data-loading-text="<b><i class='fa fa-spin fa-spinner'></i></b> Proceeding...">Scan & Submit</button>
                                    @else
                                        <h4 class="text-danger">Useronboard is {{$agent->status}}</h4>
                                    @endif
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<div id="receipt" class="modal fade" data-backdrop="false" data-keyboard="false">
    <div class="modal-dialog">
  
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header bg-slate">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Receipt</h4>
            </div>
            <div class="modal-body p-0">
                <div class="panel panel-primary">
                    <div class="panel-body">
                        <div class="clearfix">
                            <div class="pull-left">
                                <h4>
                                    @if (Auth::user()->company->logo)
                                        <img src="{{asset('')}}public/logos/{{Auth::user()->company->logo}}" class=" img-responsive" alt="" style="width: 220px;height: 40px;">
                                    @else
                                        {{Auth::user()->company->companyname}}
                                    @endif
                                </h4>
                            </div>
                            <div class="pull-right">
                                <h4><span class="receptTitle"></span> Invoice</h4>
                            </div>
                        </div>
                        <hr class="m-t-10 m-b-10">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="pull-left m-t-10">
                                    <address class="m-b-10">
                                        <strong>{{Auth::user()->name}}</strong><br>
                                        {{Auth::user()->company->companyname}}<br>
                                        Phone : {{Auth::user()->mobile}}
                                    </address>
                                </div>
                                <div class="pull-right m-t-10">
                                    <address class="m-b-10">
                                        <strong>Date: </strong> <span class="created_at"></span><br>
                                        <strong>Order ID: </strong> <span class="id"></span><br>
                                        <strong>Status: </strong> <span class="status"></span><br>
                                    </address>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <h4 class="title"></h4>
                                    <table class="table m-t-10 table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Bank</th>
                                                <th>Aadhar Number</th>
                                                <th>Ref No.</th>
                                                <th class="cash">Amount</th>
                                                <th>Account Balance</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="bank"></td>
                                                <td class="aadhar"></td>
                                                <td class="rrn"></td>
                                                <td class="amount cash"></td>
                                                <td class="balance"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="border-radius: 0px;">
                            <div class="col-md-6 col-md-offset-6">
                                <h4 class="text-right cash">Withdrawal Amount : <span class="amount"></span></h4>
                            </div>
                        </div>
                        <hr>
                        <div class="hidden-print">
                            <div class="pull-right">
                                <a href="javascript:void(0)"  id="print" class="btn btn-inverse waves-effect waves-light"><i class="fa fa-print"></i></a>
                                <button type="button" class="btn btn-warning waves-effect waves-light" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="ministatement" class="modal fade" data-backdrop="false" data-keyboard="false">
    <div class="modal-dialog">
  
        <!-- Modal content-->
        <div class="modal-content">
             <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal" style="margin-top: -17px;">&times;</button>
                <h6 class="modal-title bgtor">Mini Statement</h6>
            </div>
            <div class="modal-body p-0">
                <div class="iq-card panel-primary">
                    <div class="iq-card-body">
                        <div class="clearfix">
                            
                            <div class="pull-right">
                                <h4><span class="receptTitle"></span> Invoice</h4>
                            </div>
                        </div>
                        <hr class="m-t-10 m-b-10">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="pull-left m-t-10">
                                    <address class="m-b-10">
                                        <strong>{{Auth::user()->name}}</strong><br>
                                        {{Auth::user()->company->companyname}}<br>
                                        Phone : {{Auth::user()->mobile}}
                                    </address>
                                </div>
                                <div class="pull-right m-t-10">
                                    <address class="m-b-10">
                                        <strong>Bank : </strong> <span class="bank"></span><br>
                                        <strong>Acc. Bal. : </strong> <span class="balance"></span><br>
                                        <strong>Bank Rrn: </strong> <span class="rrn"></span><br>
                                    </address>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <h4 class="title"></h4>
                                    <table class="table m-t-10 table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Type</th>
                                                <th>Narration</th>
                                                <th>Amount (Rs)</th>
                                            </tr>
                                        </thead>
                                        <tbody class="statementData">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="hidden-print">
                            <div class="pull-right">
                                <a href="javascript:void(0)"  id="statementprint" class="btn btn-inverse waves-effect waves-light"><i class="fa fa-print"></i></a>
                                <button type="button" class="btn btn-warning waves-effect waves-light" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--<div id="activateuser" class="modal " role="dialog" tabindex="-1">-->
<!--    <div class="modal-dialog modal-md">-->
<!--        <div class="modal-content">-->
<!--            <div class="modal-header bg-primary">-->
<!--                <button type="button" class="close" data-dismiss="modal" style="margin-top: -17px;">&times;</button>-->
<!--                <h6 class="modal-title bgtor">2nd Facer Authentication</h6>-->
<!--            </div>-->
<!--            <div class="modal-body p-0">-->
<!--                <div class="panel panel-primary">-->
<!--                    <div class="panel-body p-5">-->
<!--                        <div class="row">-->
<!--                            <div class="col-md-2 p-0">-->
<!--                                <img src="{{asset('public/assets/fingericon.png') }}" width="100%">-->
<!--                            </div>-->
<!--                            <div class="col-md-10">-->
<!--                                <b>Instructions:</b><br>-->
<!--                                <p>As per regulatory guidelines, this is mandatory for Aadhaar related services, confirm-->
<!--                                    your identify daily for once.-->
<!--                                    नियामक दिशानिर्देशों के अनुसार आपको अपनी पहचान की पुष्टि प्रतिदिन एकबार करनी होगी |-->
<!--                                </p>-->
<!--                            </div>-->

<!--                        </div>-->

<!--                        <form action="{{route('serviceActive')}}" method="POST" id="aepsAuthForm"-->
<!--                            enctype="multipart/form-data">-->
<!--                            {{ csrf_field() }}-->
<!--                            <input type="hidden" name="mybiodata" class="form-control" required />-->
<!--                            <div class="row">-->
<!--                                <div class="form-group col-md-12">-->
<!--                                    <label>Aadhar Number :</label>-->
<!--                                    <input type="text" class="form-control" name="adhaarNumber"-->
<!--                                        value="{{Auth::user()->aadharcard}}" id="adhaarNumber" maxlength="12"-->
<!--                                        minlength="12" autocomplete="off" pattern="[0-9]*"-->
<!--                                        placeholder="Enter aadhar number" readonly required="">-->

<!--                                </div>-->
                                
<!--                                <div class="form-group col-md-12">-->
<!--                                    <label>DOB :</label>-->
<!--                                    <input type="date" class="form-control" name="dob"-->
<!--                                       id="adhaarNumber"  required="">-->

<!--                                </div>-->

<!--                                <div class="form-group col-md-12">-->
<!--                                    <label>Select Device :</label>-->
<!--                                    <select name="device" class="form-control" required>-->
<!--                                        <option value="">Select Device</option>-->
<!--                                        <option value="MANTRA_PROTOBUF" selected>Mantra Device</option>-->
<!--                                        <option value="MORPHO_PROTOBUF">Other Device</option>-->
<!--                                    </select>-->
<!--                                </div>-->

<!--                            </div>-->

<!--                            <div class="row">-->
<!--                                <div class="form-group col-md-12">-->
<!--                                    <button type="submit" class="btn btn-success"><span-->
<!--                                            id="2fabtn">Proceed</span></button>-->
<!--                                </div>-->
<!--                        </form>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->
<div id="twostepauthmodal" class="modal " role="dialog" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal" style="margin-top: -17px;">&times;</button>
                <h6 class="modal-title bgtor">2nd Facer Authentication</h6>
            </div>
            <div class="modal-body p-0">
                <div class="panel panel-primary">
                    <div class="panel-body p-5">
                       <div class="row">
                           <div class="col-md-2 p-0">
                               <img src="{{asset('public/assets/fingericon.png') }}" width="100%">
                           </div>
                           <div class="col-md-10">
                               <b>Instructions:</b><br>
                               <p>As per regulatory guidelines, this is mandatory for Aadhaar related services, confirm your identify daily for once.
                                    नियामक दिशानिर्देशों के अनुसार आपको अपनी पहचान की पुष्टि प्रतिदिन एकबार करनी होगी |</p>
                           </div>
                           
                       </div>
                       
                        <form action="{{route('fingpay2fa')}}" method="POST" id="aepsAuthForm" enctype="multipart/form-data">
                           {{ csrf_field() }}
                           <input type="hidden" name="mybiodata"  class="form-control" required />
                           <div class="row">
                               <div class="form-group col-md-12">
                                    <label>Aadhar Number :</label>
                                    <input type="text" class="form-control" name="adhaarNumber" value="{{Auth::user()->aadharcard}}" id="adhaarNumber" maxlength="12" minlength="12" autocomplete="off" pattern="[0-9]*"  placeholder="Enter aadhar number" readonly required="">
                            
                               </div>
                               
                               <div class="form-group col-md-12">
                                    <label>Select Device :</label>
                                    <select name="device" class="form-control" required>
                                        <option value="">Select Device</option>
                                        <option value="MANTRA_PROTOBUF" selected>Mantra Device</option>
                                        <option value="MORPHO_PROTOBUF">Other Device</option>
                                    </select>
                               </div>
                               
                               <div class="form-group col-md-12">
                                    <label>Select Bank :</label>
                                    <select name="bankpipe" class="form-control" required>
                                        <option value="">Select</option>
                                       
                                        <option value="bank2">bank2</option>
                                        <option value="bank3">bank3</option>
                                    </select>
                               </div>
                            </div>
                            
                            <div class="row">
                               <div class="form-group col-md-12">
                                   <button type="submit" class="btn btn-success"><span id="2fabtn">Proceed</span></button>
                                </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

@push('script')
<script type="text/javascript">
    $(document).ready(function () {

        @if($agent && $agent->status == "approved" && ($isdoneaepsauth2 == 'false' || $isdoneaepsauth3 == 'false' || $isdoneaepsauth5 == 'false'))
            $('#twostepauthmodal').modal();  
        @endif

        $('.mydatepic').datepicker({
            'autoclose':true,
            'clearBtn':true,
            'todayHighlight':true,
            'format':'dd-mm-yyyy',
        });

        $( "#aepsTransactionForm" ).validate({
            rules: {
                mobileNumber: {
                    required: true,
                    minlength: 10,
                    number : true,
                    maxlength: 11
                },
                adhaarNumber: {
                    required: true,
                    number: true,
                    minlength: 12,
                    maxlength: 12
                },
                bankName1: 'required',
                device: 'required'
            },
            messages: {
                mobileNumber: {
                    required: "Please enter mobile number",
                    number: "Mobile number should be numeric",
                    minlength: "Your mobile number must be 10 digit",
                    maxlength: "Your mobile number must be 10 digit"
                },
                adhaarNumber: {
                    required: "Please enter aadhar number",
                    number: "Aadhar number should be numeric",
                    minlength: "Your aadhar number must be 12 digit",
                    maxlength: "Your aadhar number must be 12 digit"
                },
                transactionAmount: {
                    required: "Please enter amount",
                    number: "Transaction amount should be numeric",
                    min : "Minimum transaction amount should be 10"
                },
                bankName1 : "Please select bank",
                device : "Please select device"
            },
            errorElement: "p",
            errorPlacement: function ( error, element ) {
                if ( element.prop( "name" ) === "bankId" ) {
                    error.insertAfter( element.closest( ".form-group" ).find("span.select2"));
                } else {
                    error.insertAfter( element );
                }
            },
            submitHandler: function (element) {
                var form = $("#aepsTransactionForm" );
                var scan = form.find('[name="txtPidData"]').val();
                if(scan != ''){
                    form.ajaxSubmit({
                        dataType:'json',
                        beforeSubmit:function(){
                            form.find('button[type="submit"]').button('loading');
                        },
                        success:function(data){
                            console.log(data);
                            form.find('button[type="submit"]').button('reset');
                            form.find('[name="txtPidData"]').val('');
                            if(data.status == "Success" || data.status == "Pending"){
                                form[0].reset();
                                form.find('select').select2().val(null).trigger('change');
                                getbalance();
                                form.find('button[type="submit"]').button('reset');
                                if(data.status == "Success" || data.status == "Pending"){
                                    if(data.transactionType != "MS"){
                                        form[0].reset();
                                        swal({
                                            title: data.title,
                                            text:  data.message + ", Remaining Balance - "+data.balance,
                                            type: 'success',
                                            showCancelButton: true,
                                            confirmButtonColor: '#3085d6',
                                            cancelButtonColor: '#456b8c',
                                            confirmButtonText: 'Print Invoice',
                                            cancelButtonText: 'Close',
                                            allowOutsideClick : false,
                                            allowEscapeKey : false,
                                            allowEnterKey : false
                                        }).then((result) => {
                                            if(result.value){
                                                if(data.transactionType == "CW"){
                                                    $(".cash").show();
                                                }else{
                                                    $('.cash').hide();
                                                }
                                                $('#receipt').find('.created_at').text(data.created_at);
                                                $('#receipt').find('.amount').text(data.amount);
                                                $('#receipt').find('.rrn').text(data.rrn);
                                                $('#receipt').find('.aadhar').text(data.aadhar);
                                                $('#receipt').find('.id').text(data.id);
                                                $('#receipt').find('.status').text(data.status);
                                                $('#receipt').find('.bank').text(data.bank);
                                                $('#receipt').find('.balance').text(data.balance);
                                                $('#receipt').find('.title').text(data.title);
                                                $('#receipt').modal();
                                            }
                                        });
                                    }else{
                                        $('#ministatement').find('.rrn').text(data.rrn);
                                        $('#ministatement').find('.bank').text(data.bank);
                                        $('#ministatement').find('.balance').text(data.balance);
                                        $('#ministatement').find('.title').text(data.title);
                                        var trdata = '';
                                        $.each(data.data, function(index, val) {
                                        
                                                trdata += `<tr>
                                                        <td>`+val.date+`</td>
                                                        <td>`+val.txnType+`</td>
                                                        <td>`+val.narration+`</td>
                                                        <td>`+val.amount+`</td>
                                                        <td></td>
                                                    </tr>`;
                                
                                        });
                                        $('#ministatement').find('.statementData').html(trdata);
                                        $('#ministatement').modal();
                                    }
                                }else{
                                    swal('Failed', data.message, 'error');
                                }
                            }else{
                                form.find('[name="txtPidData"]').val('');
                                swal({
                                    title:'Failed', 
                                    text : data.message, 
                                    type : 'error'
                                });
                            }
                        },
                        error: function(errors) {
                            form.find('[name="txtPidData"]').val('');
                            showError(errors, form);
                        }
                    });
                }else{
                    scandata();
                }
            }
        });
         $( "#aepsAuthForm" ).validate({
            rules: {
                adhaarNumber: {
                    required: true,
                    number: true,
                    minlength: 12,
                    maxlength: 12
                },
                device: 'required'
            },
            messages: {
                adhaarNumber: {
                    required: "Please enter aadhar number",
                    number: "Aadhar number should be numeric",
                    minlength: "Your aadhar number must be 12 digit",
                    maxlength: "Your aadhar number must be 12 digit"
                },
                device : "Please select device"
            },
            errorElement: "p",
            errorPlacement: function ( error, element ) {
                if ( element.prop( "name" ) === "bankId" ) {
                    error.insertAfter( element.closest( ".form-group" ).find("span.select2"));
                } else {
                    error.insertAfter( element );
                }
            },
            submitHandler: function (element) {
                var form = $("#aepsAuthForm" );
                var scan = form.find('[name="mybiodata"]').val();
               
                if(scan != ''){
                    form.ajaxSubmit({
                        dataType:'json',
                        beforeSubmit:function(){
                            form.find('button[type="submit"]').button('loading');
                        },
                        success:function(data){
                            form.find('button[type="submit"]').button('reset');
                            form.find('[name="mybiodata"]').val('');
                            if(data.status == "TXN"){
                                swal({
                                    title:'Suceess', 
                                    text : data.message, 
                                    type : 'success',
                                    onClose: () => {
                                        window.location.reload();
                                    }
                                });
                            }else if(data.status == "TUP"){
                                $("#2fabtn").text("Submit");
                                swal({
                                    title:'Suceess', 
                                    text : data.message, 
                                    type : 'success'
                                });
                            }else{
                                swal({
                                    title:'Failed', 
                                    text : data.message, 
                                    type : 'error'
                                });
                            }
                            
                        },
                        error: function(errors) {
                            form.find('[name="mybiodata"]').val('');
                            showError(errors, form);
                        }
                    });
                }else{
                    
                    scandataforauth();
                }
            }
        });
    });

function activate(){
       $('#activateuser').modal();
}
    function scandata() {
        var device = $( "#aepsTransactionForm" ).find('[name="device"]:checked').val();
        rdservice(device, "11100",type="none");
    }
    
    function scandataforauth() {
        var device = $( "#aepsAuthForm" ).find('select[name="device"]').val();
        rdservice(device, '11100',type="authentication");
           
    }
            
    function rdservice(device, port,type='none')
    {
        var primaryUrl = "http://127.0.0.1:"+port;

        $.ajax({
            type: "RDSERVICE",
            async: true,
            crossDomain: true,
            url: primaryUrl,
            processData: false,
            beforeSend: function(){
            },
            success: function (data) {
                var $doc = $.parseXML(data);
                var CmbData1 =  $($doc).find('RDService').attr('status');
                var CmbData2 =  $($doc).find('RDService').attr('info');
                
                if(!CmbData1){
                    var CmbData1 =  $(data).find('RDService').attr('status');
                    var CmbData2 =  $(data).find('RDService').attr('info');
                }
                
                if(CmbData1 == "READY"){
                    capture(device, port,type);
                }else if(CmbData1 == "NOTREADY" && CmbData2 == "Mantra Authentication Vendor Device Manager"){
                    rdservice(device, "11101",type);
                }else{
                    notify("Device : "+CmbData1, 'warning');
                }
            },
            error: function (jqXHR, ajaxOptions, thrownError) {
                $('#aepsTransactionForm').unblock();
                if(port == "11100"){
                    rdservice(device, "11101",type);
                }else{
                    notify("Oops! Device not working correctly, please try again", 'warning');
                }
            },
        });
    }

    function capture(device, port,type){
        var primaryUrl = "http://127.0.0.1:"+port;
        
        if(device == "MANTRA_PROTOBUF"){
            var url = primaryUrl+"/rd/capture";
        }else{
            var url = primaryUrl+"/capture";
        }

      
        if(device == "MANTRA_PROTOBUF"){
            var XML='<?php echo '<?xml version="1.0"?>'; ?> <PidOptions ver="1.0"> <Opts fCount="1" fType="2" iCount="0" pCount="0" pgCount="2" format="0"   pidVer="2.0" timeout="10000" pTimeout="20000" posh="UNKNOWN" env="P" /> <CustOpts><Param name="mantrakey" value="" /></CustOpts> </PidOptions>';
        }else{
            var XML='<PidOptions ver=\"1.0\">' + '<Opts fCount=\"1\" fType=\"2\" iCount=\"\" iType=\"\" pCount=\"\" pType=\"\" format=\"0\" pidVer=\"2.0\" timeout=\"10000\" otp=\"\" posh=\"\"/>' + '</PidOptions>'; 
        }
    
        
        
        $.ajax({
            type: "CAPTURE",
            async: true,
            crossDomain: true,
            url: url,
            data:XML,
            contentType: "text/xml; charset=utf-8",
            processData: false,
            beforeSend: function(){
            },
            success: function (data) {
                if(device == "MANTRA_PROTOBUF"){
                    var $doc = $.parseXML(data);
                    var errorInfo =  $($doc).find('Resp').attr('errInfo');
                    const dotsRemoved = errorInfo.replaceAll('.', '');
                    if(dotsRemoved == 'Success' && type == 'authentication'){
                        notify("Fingerprint Captured Successfully", "success");
                        
                        $('[name="mybiodata"]').val(data);
                        $('#aepsAuthForm').submit();
                    
                    }else if(dotsRemoved == 'Success'){
                        notify("Fingerprint Captured Successfully", "success");
                        $('[name="txtPidData"]').val(data);
                       $('#aepsTransactionForm').submit();
                    }else{
                        notify("Oops! Device not working correctly, please try again", "warning");
                    }
                }else{
                    var errorInfo =  $(data).find('Resp').attr('errInfo');
                    var errorCode =  $(data).find('Resp').attr('errCode');
                    var mydata =  $(data).find('PidData').html();
                    if(errorCode == '0' && type == 'authentication'){
                        notify("Fingerprint Captured Successfully", "success");
                       
                        $('[name="mybiodata"]').val("<PidData>"+mydata+"</PidData>");
                        $('#aepsAuthForm').submit();
                    
                    }else if(errorCode == '0'){
                        notify("Fingerprint Captured Successfully", "success");
                       
                        $('[name="txtPidData"]').val("<PidData>"+mydata+"</PidData>");
                        $('#aepsTransactionForm').submit();
                    }else{
                        notify("Oops! Device not working correctly, please try again", "warning");
                    }
                }
            },
            error: function (jqXHR, ajaxOptions, thrownError) {
                notify("Oops! Device not working correctly, please try again", "warning");
            },
        });
    }
    
    function AEPSTAB(type){
        
        if(type == "CW" || type == "M"){
            $('.transactionAmount').html(`
                <label>Amount :</label>
                <input type="text" class="form-control" name="transactionAmount" pattern="[0-9]*" id="amount" autocomplete="off" placeholder="Enter Amount">
            `);   
        }else{
            $('.transactionAmount').html('');
        }
        
        if(type != "M"){
            $('#bankId1').show();
            $('#bankId2').hide();
        }else{
            $('#bankId2').show();
            $('#bankId1').hide();
        }
        $("#aepsTransactionForm" ).find('[name="transactionType"]').val(type)
    }
</script>
@endpush