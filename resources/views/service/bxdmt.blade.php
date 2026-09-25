@extends('layouts.app')
@section('title', "P-Money Transfer")
@section('pagetitle', "P-Money Transfer")
@php
    $table = "yes";
@endphp

@section('content')
<div class="content">
    <div class="row">
        <div class="col-sm-4">
            <div class="panel panel-default iq-card p-3">
                <div class="panel-heading">
                    <h4 class="panel-title">P-Money Transfer</h4>
                </div>
                <form id="serachForm" action="{{route('dmt2pay')}}" method="post">
                    {{ csrf_field() }}
                    <input type="hidden" name="type" value="verification">
                    <input type="hidden" id="rname">
                    <input type="hidden" name="rid">
                    <input type="hidden" id="rlimit">
                    <div class="panel-body">

                     <br>
                     <div class="form-group no-margin-bottom">     
                            <label>Mobile Number <span class="text-danger">*</span></label>
                            <input type="number" step="any" name="mobile" class="form-control" placeholder="Enter Mobile Number" required="">
                        </div>
                    <div class="form-group no-margin-bottom beniids">     
                          
                    </div>    
                    </div>
                    <div class="panel-footer text-center">
                        <button type="button" class="btn btn-primary btn-lg" data-loading-text="Loading..." style="display:none"> Loading..</button>
                        <button type="submit" class="btn bg-slate btn-labeled btn-rounded legitRipple btn-lg" data-loading-text="<b><i class='fa fa-spin fa-spinner'></i></b> Searching"><b><i class="icon-search4"></i></b> Search</button>
                        <!--<button type="button" id="fetch" onclick="window.location.reload();" class="btn bg-grey-400 btn-labeled btn-rounded legitRipple btn-lg billfetch" data-loading-text="<b><i class='fa fa-spin fa-spinner'></i></b> Fetching"><b><i class="icon-backward"></i></b>Back</button>-->
                    </div>
                </form>
               
            </div>
            <div class="panel userdetails iq-card" style="display:none">
                    <div class="card-body">
                        <h5 class="content-group no-margin">
                            <span class="label label-flat label-rounded label-icon border-grey text-grey mr-10">
                                <i class="icon-user"></i>
                            </span>
                            <a href="javascript:void(0)" class="text-default name"></a>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <h6 class="text-semibold no-margin-top mobile"></h6>
                                <ul class="list list-unstyled">
                                    <li>Bank 1 Limit : <i class="fa fa-inr"></i> <span class="usedlimit"></span></li>
                                </ul>
                            </div>
    
                            <div class="col-sm-6">
                                <h6 class="text-semibold text-right no-margin-top">Bank 2 Limit : <i class="fa fa-inr"></i> <span class="totallimit"></span></h6>
                                <ul class="list list-unstyled text-right">
                                    <li>Bank 3 Limit: <i class="fa fa-inr"></i> <span class="text-semibold remainlimit"></span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <hr class="no-margin">
                    <div class="panel-footer text-center alpha-grey">
                        <a href="#" data-toggle="modal" data-target="#beneficiaryModal" class="btn bg-slate legitRipple">
                            <i class="icon-plus22 position-left"></i>
                            New Beneficiary
                        </a>
                    </div>
                </div> 


        </div>
        <div class="col-sm-8">
            
        <div class="col-sm-12">
            <div class="panel panel-default iq-card p-3">
                <div class="panel-heading">
                    <h4 class="panel-title">Beneficiary List</h4>
                    <div class="pull-right" style="right:0">
                        <input type="text" width="200" style="float: right;border-radius: 6px;border: thin;position: relative;margin-top: -27px;color: initial;" id="searchBox" placeholder="Search Bene..."> 
                    </div>
                </div>
                <div class="panel-body p-0 " style="">
                    <table class="table table-bordered table-bordered transaction" id="benetablelist" cellspacing="0" width="100%">
                            <thead>
                                <th width="75px">Sr No.</th>
                                <th width="150px">Name</th>
                                <th width="250px">Account Details</th>
                                <th>Action</th>
                            </thead>
                            <tbody>
                            </tbody>
                    </table>
                </div>
            </div>
        </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-sm-4">
            
        </div>
    </div>
</div>

<div id="twostepauthmodal" class="modal " role="dialog" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h6 class="modal-title bgtor">DMT KYC</h6>
                <button type="button" class="close" data-dismiss="modal" style="margin-top: -17px;">&times;</button>
                
            </div>
            
            <div class="modal-body p-0">
                <div class="panel panel-primary">
                    <div class="panel-body p-5">
                       <div class="row">
                           <div class="col-md-2 p-0">
                               <img src="{{asset('assets/fingericon.png') }}" width="100%">
                           </div>
                           <div class="col-md-10">
                               <b>Instructions:</b><br>
                               <p>As per regulatory guidelines, this is mandatory for Aadhaar related services, confirm your identify daily for once.
                                    नियामक दिशानिर्देशों के अनुसार आपको अपनी पहचान की पुष्टि प्रतिदिन एकबार करनी होगी |</p>
                           </div>
                           
                       </div>
                       
                        <form action="{{route('dmt2pay')}}" method="POST" id="aepsAuthForm" enctype="multipart/form-data">
                           {{ csrf_field() }}
                           <input type="hidden" name="mybiodata"  class="form-control" required />
                            <input type="hidden" name="type" value="KYC">
                           <div class="row">
                               <div class="form-group col-md-12">
                                    <label>Aadhar Number :</label>
                                    <input type="text" class="form-control" name="adhaarNumber" value="" id="adhaarNumber" maxlength="12" minlength="12" autocomplete="off" pattern="[0-9]*"  placeholder="Enter aadhar number" required="">
                            
                               </div>
                               <div class="form-group col-md-12">
                                    <label>Mobile Number :</label>
                                    <input type="text" class="form-control" name="mobile" value=""  placeholder="Enter mobile number" required="">
                            
                               </div>
                               
                               <div class="form-group col-md-12">
                                    <label>Select Device :</label>
                                    <select name="device" class="form-control" required>
                                        <option value="">Select Device</option>
                                        <option value="MANTRA_PROTOBUF" selected>Mantra Device</option>
                                        <option value="MORPHO_PROTOBUF">Other Device</option>
                                    </select>
                               </div>
                            </div>
                            
                            <div class="row">
                               <div class="form-group col-md-12">
                                   <button type="submit" class="btn btn-success">Proceed</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="beneficiaryModal" class="modal fade" role="dialog" data-backdrop="false" data-keyboard="false">
    <div class="modal-dialog">
    <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header bg-slate">
                <h4 class="modal-title pull-left">Beneficiary Details Please</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{route('dmt2pay')}}" method="post" id="beneficiaryForm">
                <div class="modal-body">
                    {{ csrf_field() }}
                    <input type="hidden" name="rid">
                    <input type="hidden" name="type" value="addbeneficiary">
                    <input type="hidden" name="mobile">
                    <input type="hidden" name="name">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Bank Name : </label>
                                <select id="bank" name="benebank" class="form-control select">
                                    <option value="">Select Bank</option>
                                    @foreach($banks as $bank)
                                        <option value="{{$bank->bankid}}" ifsc="{{$bank->ifsc}}">{{$bank->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone">IFSC Code:</label>
                                <input type="text" class="form-control" name="beneifsc" placeholder="Bank ifsc code" required="">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone">Bank Account No.:</label>
                                <input type="text" class="form-control" id="account" name="beneaccount" placeholder="Enter account no." required="">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone">Beneficiary Name:</label>
                                <input type="text" class="form-control" name="benename" placeholder="Enter name" required="">
                                <p></p>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <span id="pverify"><button class="btn btn-primary legitRipple" type="button" id="getBenename">Verify</button></span>
                    <button type="button" class="btn btn-default btn-raised legitRipple" data-dismiss="modal" aria-hidden="true">Close</button>
                    <span id="psubmit"><button class="btn bg-slate btn-raised legitRipple" type="submit" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Submitting">Submit</button></span>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="otpModal" class="modal fade" role="dialog" data-backdrop="false" data-keyboard="false">
    <div class="modal-dialog modal-sm">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header bg-slate">
                <h4 class="modal-title pull-left">Otp Verification</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{route('dmt2pay')}}" method="post" id="otpForm">
                {{ csrf_field() }}
                <div class="modal-body">
                    <input type="hidden" name="type" value="beneverify">
                    <input type="hidden" name="mobile">
                    <input type="hidden" name="beneaccount">
                    <input type="hidden" name="benemobile">
                    <div class="form-group">
                        <label>OTP</label>
                        <input type="text" class="form-control" name="otp" placeholder="enter otp" required>
                        <a href="javascript:void(0)" class="pull-right resendOtp" data-loading-text="<i class='fa fa-spinner fa-spin'></i> Sending" type="resendOtpVerification"><i class='fa fa-paper-plane'></i> Resend Otp</a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-raised legitRipple" data-dismiss="modal" aria-hidden="true">Close</button>
                    <button class="btn bg-slate btn-raised legitRipple" type="submit" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Submitting">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>


<div id="transferModal" class="modal fade right" role="dialog" data-backdrop="false" data-keyboard="false">
    <div class="modal-dialog ">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header bg-slate">
                <h4 class="modal-title">Transfer Money</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="#" method="post" id="transferForm">
                {{ csrf_field() }}
                <input type="hidden" name="transactionvia" value="bxdmt">
                <input type="hidden" name="type" value="transfer">
                <input type="hidden" name="mobile">
                <input type="hidden" name="name">
                <input type="hidden" name="benename">
                <input type="hidden" name="beneaccount">
                <input type="hidden" name="benebank">
                <input type="hidden" name="beneifsc">
                <input type="hidden" name="rid">
                <input type="hidden" name="beneid">
                <input type="hidden" name="stateresp">
                <input type="text" name="latitude" id="latitude">
                           <input type="text" name="longitude" id="longitude">
                <div class="modal-body" style="padding-bottom:20px">
                    <ul class="list-group transactionData p-0">
                </ul>
                    <div class="panel border-left-lg border-left-success invoice-grid timeline-content">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <h6 class="text-semibold no-margin-top ">Name - <span class="benename"></span></h6>
                                </div>
                                <div class="col-sm-12">
                                    <h6 class="text-semibold no-margin-top ">Bank - <span class="benebank"></span></h6>
                                </div>
                                <div class="col-sm-12">
                                    <h6 class="text-semibold no-margin-top">Acc - <span class="beneaccount"></span></h6>
                                </div>
                                <div class="col-sm-12">
                                    <h6 class="text-semibold no-margin-top ">Ifsc - <span class="beneifsc"></span></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>Transfer Mode</label>
                            <select name="mode" class="form-control select">
                                <option value="IMPS">IMPS</option>
                                <option value="NEFT">NEFT</option>
                            </select>
                        </div>

                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Amount</label>
                                <input type="number" class="form-control numbertoword onlynumeric" placeholder="Enter amount to be transfer" name="amount" step="any" required>
                                <span class="wordscontainer"></span>
                            </div>
                        </div>
                    </div>
                    
                  
                    <div class="row">
                         <div class="form-group col-md-4">
                                 <label>Pipe <span class="text-danger">*</span></label>
                                 <select name="pipe" class="form-control select">
                                    <option value="bank1">Bank 1</option>
                                         <!--<option value="bank2">Bank 2</option>-->
                                        <option value="bank3">Bank 3</option>
                                </select>
                             
                        </div>
                        <div class="col-md-8">
                            <div class="form-group ">
                                <label>T-Pin <span class="text-danger">*</span></label>
                                <input type="password" name="pin" class="form-control" placeholder="Enter transaction pin" required="">
                                <a href="{{url('profile/view?tab=pinChange')}}" target="_blank" class="text-primary pull-right">Generate Or Forgot Pin??</a>
                            </div>
                        </div>
                         
                    </div>
                     <div  class="row totp">
                    </div>
                </div>
                <div class="modal-footer" style="text-align:left">
                    <button class="btn bg-slate btn-raised legitRipple" type="submit" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Submitting">Transfer</button>
                    <button type="button" class="btn btn-default btn-raised legitRipple" data-dismiss="modal" aria-hidden="true">Close</button>
                </div>
            </form>
        </div>
    
    </div>
</div>

<div id="receipt" class="modal fade" data-backdrop="false" data-keyboard="false">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header bg-slate">
                <h4 class="modal-title">Receipt</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            
            </div>
            <div class="modal-body">
                <ul class="list-group transactionData p-0">
                </ul>
                <div id="receptTable">
                    <div class="clearfix">
                        <div class="pull-left">
                            <h4>Invoice</h4>
                        </div>
                        <div class="pull-right">
                              @if(Auth::user()->company->logo)
                                        <img src="{{asset('')}}logos/{{Auth::user()->company->logo}}" class=" img-responsive" alt="" style="width: 122px;height: 111px;">
                             @else
                                      {{Auth::user()->company->companyname}}
                            @endif 
                        </div>
                    </div>
                    <hr class="m-t-10 m-b-10">
                    <div class="row">
                        <div class="col-md-12">
                            
                            <div class="pull-left m-t-10">
                                <address class="m-b-10">
                                    Agent : <strong class="username">{{Auth::user()->name}}</strong><br>
                                    Shop Name : <span class="company">{{Auth::user()->shopname}}</span><br>
                                    Phone : <span class="">{{Auth::user()->mobile}}</span>
                                    </address>
                            </div>
                            <div class="pull-right m-t-10">
                                <address class="m-b-10">
                                    <strong>Date : </strong> <span class="date">{{date('d M y - h:i A')}}</span><br>
                                    <strong>Name : </strong> <span class="benename"></span><br>
                                    <strong>Account : </strong> <span class="beneaccount"></span><br>
                                    <strong>Bank : </strong> <span class="benebank"></span><br>
                                    <strong>Mobile : </strong> <span class="benemobile"></span>
                                </address>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <h4>Transaction Details :</h4>
                                <table class="table m-t-10">
                                    <thead>
                                        <tr>
                                            <th>TXN Id</th>
                                            <th>Amount</th>
                                            <th>UTR No.</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="id"></td>
                                            <td class="amount"></td>
                                            <td class="refno"></td>
                                            <td class="status"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="border-radius: 0px;">
                        <div class="col-md-6 col-md-offset-6">
                            <h5 class="text-right">Transfer Amount : <span class="samount"></span></h5>
                        </div>
                    </div>
                    <p>* As per RBI guideline, maximum charges allowed is 1.2%.</p>
                    <hr>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-raised legitRipple" data-dismiss="modal" aria-hidden="true">Close</button>
                <button class="btn bg-slate btn-raised legitRipple" type="button" id="print"><i class="fa fa-print"></i></button>
            </div>
        </div>
    </div>
</div>

<div id="registrationModal" class="modal fade" role="dialog" data-backdrop="false" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-slate">
                <h4 class="modal-title pull-left">Remitter Registration</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{route('dmt2pay')}}" method="post" id="registrationForm">
                {{ csrf_field() }}
                <div class="modal-body">
                    <input type="hidden" name="type" value="registration">
                    <input type="hidden" name="mobile">
                    <input type="hidden" name="stateresp">
                    <div  class="row">
                        <div class="form-group col-md-6">
                            <label>First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="firstname" required="" placeholder="Enter last name">
                        </div>

                        <div class="form-group col-md-6">
                            <label>Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="lastname" required="" placeholder="Enter first name">
                        </div>
                    </div>
                    <div  class="row otpdata">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-raised legitRipple" data-dismiss="modal" aria-hidden="true">Close</button>
                    <button class="btn bg-slate btn-raised legitRipple" type="submit" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Submitting">Submit</button>
                </div>
            </form>
        </div>

    </div>
</div>

<div id="EKYCotpModal" class="modal fade" role="dialog" data-backdrop="false" data-keyboard="false">
    <div class="modal-dialog modal-sm">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header bg-slate">
                <h4 class="modal-title pull-left">EKYC Otp Verification</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{route('dmt2pay')}}" method="post" id="ekycotpForm">
                {{ csrf_field() }}
                <div class="modal-body">
                    <input type="hidden" name="type" value="ekycotp">
                    <input type="hidden" name="mobile">
                    <input type="hidden" name="stateresp">
                    <input type="hidden" name="ekyc_id">
                   
                    <div class="form-group">
                        <label>OTP</label>
                        <input type="text" class="form-control" name="otp" placeholder="enter otp" required>
                       
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-raised legitRipple" data-dismiss="modal" aria-hidden="true">Close</button>
                    <button class="btn bg-slate btn-raised legitRipple" type="submit" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Submitting">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('style')
<link href="{{asset('')}}assets/css/jquery-confirm.min.css" rel="stylesheet" type="text/css">
<style type="text/css">
    @media (min-width: 769px){
        .modal-sm {
            width: 834px;
        }
    }
    .btn-xs {
        padding: 13px 18px;
    }
    .btn-danger{
        padding: 2px 8px;
    }
</style>
@endpush

@push('script')
<script src="{{ asset('assets/js/core/jQuery.print.js') }}"></script>
<script type="text/javascript" src="{{asset('')}}assets/js/core/jquery-confirm.min.js"></script>
<script type="text/javascript" src="{{asset('')}}assets/js/core/notify.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        getUserLocation();
        
        function filterTable(searchTerm) {
            var $table1 = $("#benetablelist");
            var $rows = $table1.find("tbody tr");
             $rows.hide();
            $rows.filter(function() {
              // Filter rows based on search input
              var text = $(this).text().toLowerCase();
              return text.indexOf(searchTerm.toLowerCase()) > -1;
            }).show();
          }
        
          // Handle user input
          $("#searchBox").on("input", function() {
            var searchTerm = $(this).val();
            console.log($("#benetablelist tbody tr:visible").length);
            filterTable(searchTerm);
            updateResults($("#benetablelist tbody tr:visible").length);
          });
        
        // $("[name='mobile']").keyup(function(){
        //     $('.userdetails').fadeOut('400');
        //     $('.transaction').find('tbody').html('');

        //     $( "#serachForm" ).submit();
        // });
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
                            swal({
                            title: 'Wait!',
                            text: 'Request Processing...!',
                            onOpen: () => {
                                swal.showLoading()
                            },
                            allowOutsideClick: () => !swal.isLoading()
                        });
                        },
                        success:function(data){
                            swal.close();
                            form.find('button[type="submit"]').button('reset');
                            form.find('[name="mybiodata"]').val('');
                            if(data.status == "TXN"){
                             
                            $('#twostepauthmodal').hide();
                            $('#ekycotpForm').find('[name="mobile"]').val(data.data.mobile);
                            $('#ekycotpForm').find('[name="stateresp"]').val(data.data.stateresp);
                            $('#ekycotpForm').find('[name="ekyc_id"]').val(data.data.ekyc_id);
                            
                            
                            $('#EKYCotpModal').modal();
                            }else{
                                swal({
                                    title:'Failed', 
                                    text : data.message, 
                                    type : 'error'
                                });
                            }
                            
                        },
                        error: function(errors) {
                             swal.close();
                            form.find('[name="mybiodata"]').val('');
                            showError(errors, form);
                        }
                    });
                }else{
                    
                    scandataforauth();
                }
            }
        });
        $('#print').click(function(){
            $('#receptTable').print();
        });

        $('#bank').on('change', function (e) {
            $('input[name="beneifsc"]').val($(this).find('option:selected').attr('ifsc'));
        }); 

        $('a.resendOtp').click(function(){
            var mobile = $(this).closest('form').find('input[name="mobile"]').val();
            var button = $(this);
            var form  = $(this).closest('form');
            $.ajax({
                url: "{{route('dmt2pay')}}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType:'json',
                data: {'mobile':mobile, 'type':"otp"},
                beforeSend:function(){
                    swal({
                        title: 'Wait!',
                        text: 'We are processing your request.',
                        allowOutsideClick: () => !swal.isLoading(),
                        onOpen: () => {
                            swal.showLoading()
                        }
                    });
                },
                success: function(data){
                    swal.close();
                    if(result.statuscode == "TXN"){
                        notify(data.message, 'success', "inline",form);
                    }else{
                        notify(data.message, 'danger', "inline",form);
                    }
                },
                error: function(error){
                    swal.close();
                    notify("Something went wrong", 'danger', "inline",form);
                }
            });
        });

        $( "#serachForm" ).validate({
            rules: {
                mobile: {
                    required: true,
                    number : true,
                    minlength:10,
                    maxlength:10
                },
            },
            messages: {
                mobile: {
                    required: "Please enter mobile number",
                    number: "Mobile number should be numeric",
                    minlenght: "Mobile number length should be 10 digit",
                    maxlenght: "Mobile number length should be 10 digit",
                }
            },
            errorElement: "p",
            errorPlacement: function ( error, element ) {
                if ( element.prop("tagName").toLowerCase() === "select" ) {
                    error.insertAfter( element.closest( ".form-group" ).find(".select2") );
                } else {
                    error.insertAfter( element );
                }
            },
            submitHandler: function () {

                var form = $('#serachForm');
                form.ajaxSubmit({
                    dataType:'json',
                    beforeSubmit:function(){
                        form.find('button[type="submit"]').button('loading');
                        form.find('button[type="submit"]').css('display','none');
                        form.find('button[type="button"]').css('display','inline-block');
                    },
                    success:function(data){
                        form.find('button[type="submit"]').button('reset');
                        form.find('button[type="submit"]').css('display','inline-block');
                            form.find('button[type="button"]').css('display','none');
                        if(data.statuscode == "TXN"){
                            setVerifyData(data);
                            setBeneData(data);
                        }else if(data.statuscode == "RNF"){
                            var mobile = form.find('[name="mobile"]').val();
                            $('#registrationModal').find('[name="mobile"]').val(mobile);
                            $('#registrationModal').find('[name="stateresp"]').val(data.data.stateresp);
                            
                            if(data.data.stateresp != null && data.data.stateresp != "null" ){
                                $("[name='otp']").closest('.form-group').remove();
                                $('.otpdata').append(`<div class="form-group col-md-6">
                                    <label>Otp</label>
                                    <input type="text" class="form-control" name="otp" required="" placeholder="Enter otp">
                                </div>`);
                            }else{
                                $('#registrationModal').find('[name="otp"]').closest('.form-group').remove();
                            }
                            $('#registrationModal').modal();
                        }
                        else if(data.statuscode == "EKYC"){
                           $('#twostepauthmodal').modal(); 
                        }
                        else if(data.statuscode == "EKYCOTP"){
                            $('#ekycotpForm').find('[name="mobile"]').val(data.data.mobile);
                            $('#ekycotpForm').find('[name="stateresp"]').val(data.data.stateResp);
                            $('#ekycotpForm').find('[name="ekyc_id"]').val(data.data.ekyc_id);
                           $('#EKYCotpModal').modal(); 
                        }
                        else{
                            notify(data.message, 'danger', "inline",form);
                        }
                    },
                    error: function(errors) {
                        showError(errors, form);
                    }
                });
            }
        });
        $( "#beneForm" ).validate({
            rules: {
                mobile: {
                    required: true,
                    number : true,
                    minlength:10,
                    maxlength:10
                },
            },
            messages: {
                mobile: {
                    required: "Please enter mobile number",
                    number: "Mobile number should be numeric",
                    minlenght: "Mobile number length should be 10 digit",
                    maxlenght: "Mobile number length should be 10 digit",
                }
            },
            errorElement: "p",
            errorPlacement: function ( error, element ) {
                if ( element.prop("tagName").toLowerCase() === "select" ) {
                    error.insertAfter( element.closest( ".form-group" ).find(".select2") );
                } else {
                    error.insertAfter( element );
                }
            },
            submitHandler: function () {

                var form = $('#beneForm');
                form.ajaxSubmit({
                    dataType:'json',
                    beforeSubmit:function(){
                        form.find('button[type="submit"]').button('loading');
                        form.find('button[type="submit"]').css('display','none');
                        form.find('button[type="button"]').css('display','inline-block');
                    },
                    success:function(data){
                        form.find('button[type="submit"]').button('reset');
                        form.find('button[type="submit"]').css('display','inline-block');
                            form.find('button[type="button"]').css('display','none');
                        if(data.statuscode == "TXN"){
                            setVerifyData(data);
                            setBeneData(data);
                        }else if(data.statuscode == "RNF"){
                            var mobile = form.find('[name="mobile"]').val();
                            $('#registrationModal').find('[name="mobile"]').val(mobile);
                            $('#registrationModal').find('[name="stateresp"]').val(data.data.stateresp);
                            
                            if(data.data.stateresp != null && data.data.stateresp != "null" ){
                                $("[name='otp']").closest('.form-group').remove();
                                $('.otpdata').append(`<div class="form-group col-md-6">
                                    <label>Otp</label>
                                    <input type="text" class="form-control" name="otp" required="" placeholder="Enter otp">
                                </div>`);
                            }else{
                                $('#registrationModal').find('[name="otp"]').closest('.form-group').remove();
                            }
                            $('#registrationModal').modal();
                        }
                        else if(data.statuscode == "EKYC"){
                           $('#twostepauthmodal').modal(); 
                        }
                        else if(data.statuscode == "EKYCOTP"){
                            $('#ekycotpForm').find('[name="mobile"]').val(data.data.mobile);
                            $('#ekycotpForm').find('[name="stateresp"]').val(data.data.stateResp);
                            $('#ekycotpForm').find('[name="ekyc_id"]').val(data.data.ekyc_id);
                           $('#EKYCotpModal').modal(); 
                        }
                        else{
                            notify(data.message, 'danger', "inline",form);
                        }
                    },
                    error: function(errors) {
                        showError(errors, form);
                    }
                });
            }
        });
        $( "#beneficiaryForm" ).validate({
            rules: {
                ifsc: {
                    required: true,
                },
                account: {
                    required: true,
                },
                account_confirmation: {
                    required: true,
                    equalTo : '#account'
                },
                name: {
                    required: true,
                }
            },
            messages: {
                ifsc: {
                    required: "Bank ifsc code is required",
                },
                account: {
                    required: "Beneficiary bank account number is required",
                },
                account_confirmation: {
                    required: "Account number confirmation is required",
                    equalTo : 'Account confirmation is same as account number'
                },
                name: {
                    required: "Beneficiary account name is required",
                }
            },
            errorElement: "p",
            errorPlacement: function ( error, element ) {
                if ( element.prop("tagName").toLowerCase() === "select" ) {
                    error.insertAfter( element.closest( ".form-group" ).find(".select2") );
                } else {
                    error.insertAfter( element );
                }
            },
            submitHandler: function () {
                var form = $('#beneficiaryForm');
                form.ajaxSubmit({
                    dataType:'json',
                    beforeSubmit:function(){
                        form.find('button[type="submit"]').button('loading');
                    },
                    success:function(data){
                        form.find('button[type="submit"]').button('reset');
                        if(data.statuscode == "TXN"){
                            form[0].reset();
                            form.find('select').select2().val(null).trigger('change');
                            form.closest('.modal').modal('hide');
                            notify('Beneficiary Successfully Added.', 'success');
                            // $( "#serachForm" ).submit();
                            setBeneData(data);
                        }else{
                            notify(data.message, 'danger', "inline", form);
                        }
                    },
                    error: function(errors) {
                        showError(errors, form);
                    }
                });
            }
        });

        $( "#otpForm" ).validate({
            rules: {
                otp: {
                    required: true,
                    number : true,
                },
            },
            messages: {
                otp: {
                    required: "Please enter otp number",
                    number: "Otp number should be numeric",
                }
            },
            errorElement: "p",
            errorPlacement: function ( error, element ) {
                if ( element.prop("tagName").toLowerCase() === "select" ) {
                    error.insertAfter( element.closest( ".form-group" ).find(".select2") );
                } else {
                    error.insertAfter( element );
                }
            },
            submitHandler: function () {
                var form = $('#otpForm');
                form.ajaxSubmit({
                    dataType:'json',
                    beforeSubmit:function(){
                        form.find('button[type="submit"]').button('loading');
                    },
                    success:function(data){
                        form.find('button[type="submit"]').button('reset');
                        if(data.statuscode == "TXN"){
                            var type = form.find('[name="type"]').val();
                            form[0].reset();
                            $('#otpModal').find('[name="mobile"]').val("");
                            $('#otpModal').find('[name="beneaccount"]').val("");
                            $('#otpModal').find('[name="benemobile"]').val("");
                            $('#otpModal').modal('hide');
                            if(type == "registrationValidate"){
                                notify('Member successfully registered.', 'success');
                            }else{
                                notify('Beneficiary Successfully verified.', 'success');
                            }
                            $( "#serachForm" ).submit();
                        }else{
                            notify(data.message, 'danger', "inline",form);
                        }
                    },
                    error: function(errors) {
                        showError(errors, form);
                    }
                });
            }
        });

        $( "#transferForm" ).validate({
            rules: {
                amount: {
                    required : true,
                    number   : true,
                    min      : 100
                }
            },
            messages: {
                amount: {
                    required : "Please enter amount",
                    number   : "Amount should be numeric",
                    min      : "Amount value should be greater than 100"
                }
            },
            errorElement: "p",
            errorPlacement: function ( error, element ) {
                if ( element.prop("tagName").toLowerCase() === "select" ) {
                    error.insertAfter( element.closest( ".form-group" ).find(".select2") );
                } else {
                    error.insertAfter( element );
                }
            },
            submitHandler: function () {
                var form = $('#transferForm');
                var type      = form.find('[name="type"]').val();
                var amount      = form.find('[name="amount"]').val();
                var otp      = form.find('[name="otp"]').val();
                var stateresp      = form.find('[name="stateresp"]').val();
                var benename    = form.find('[name="benename"]').val();
                var beneaccount = form.find('[name="beneaccount"]').val();
                var benebank = form.find('[name="benebank"]').val();
                var bankname = form.find('.benebank').text();
                var beneifsc = form.find('[name="beneifsc"]').val();
                var name     = form.find('[name="name"]').val();
                var mobile   = form.find('[name="mobile"]').val();
                var rid   = form.find('[name="rid"]').val();
                var beneid   = form.find('[name="beneid"]').val();
                var mode   = form.find('[name="mode"]').val();
                var tpin  = form.find('[name="tpin"]').val();
                var address  = form.find('[name="address"]').val();
                var pincode  = form.find('[name="pincode"]').val();
                var dob  = form.find('[name="dob"]').val();
                var gst_state  = form.find('[name="gst_state"]').val();
                var pin  = form.find('[name="pin"]').val();
                var pipe  = form.find('[name="pipe"]').val();
                var lat = form.find('[name="latitude"]').val();
                var lon = form.find('[name="longitude"]').val();
                
                swal({
                    title: 'Are you sure ?',
                    text: "Do You want to transfer " + amount,
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: "Yes Transfer",
                    showLoaderOnConfirm: true,
                    allowOutsideClick: () => !swal.isLoading(),
                    preConfirm: () => {
                        return new Promise((resolve) => {
                            $.ajax({
                                url: "{{route('dmt2pay')}}",
                                type: "POST",
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                dataType:'json',
                                data: {
                                    'otp':otp,
                                    'stateresp':stateresp,
                                    'mobile' : mobile,
                                    'type' : type,
                                    "beneaccount" : beneaccount,
                                    "beneifsc" : beneifsc,
                                    "name" : name,
                                    "benebank" : benebank,
                                    "benename" : benename,
                                    "rid" : rid,
                                    "beneid" : beneid,
                                    "amount" : amount,
                                    "mode" : mode,
                                    "tpin" : tpin,
                                    "address" : address,
                                    "pincode" : pincode,
                                    "dob" : dob,
                                    "gst_state" : gst_state,
                                    "pin" : pin,
                                    'pipe' : pipe,
                                    'lat': lat,
                                    'lon': lon
                                },
                                beforeSend:function(){
                                    
                                    swal({
                                        title: 'Wait!',
                                        text: 'Please wait, we are working on your request',
                                        onOpen: () => {
                                            swal.showLoading()
                                        },
                                        allowOutsideClick: () => !swal.isLoading()
                                    });
                                },
                                success: function(data){
                                    swal.close();
                                    if(data.statuscode == "TOTP"){
                                        $("#transferForm" ).find('[name="type"]').val('transfer');
                                        $("#transferForm" ).find('[name="stateresp"]').val(data.stateresp)
                                        $('.totp').append(`<div class="form-group col-md-6">
                                    <label>Otp</label>
                                    <input type="text" class="form-control" name="otp" required="" placeholder="Enter otp">
                                </div>`);
                                    }
                                    else{
                                        if(data.status == 'ERR')
                                    {
                                        var out ="";
                                        out += '<li class="list-group-item alert alert-danger no-margin mb-10"><strong>'+data.status+' </strong><span class="pull-right">'+data.message+'</span></li>';
                                        $('.transactionData').html(out);
                                    }else
                                    {
                                        form.find('button[type="submit"]').button('reset');
                                        form[0].reset();
                                        getbalance();
                                        form.closest('.modal').modal('hide');
                                        $('.totp').html('');
                                        //form.find('[name="type"]').val('send_otp')
                                        var samount = 0;
                                        var out ="";
                                        var tbody = '';
                                        console.log(data);
                                    
                                        $.each(data.data , function(index, val){
                                            if(val.data.statuscode == "TXN" || val.data.statuscode == "TUP"){
                                                samount += parseFloat(val.amount);
                                                out += '<li class="list-group-item alert alert-success no-margin mb-10"><strong>Rs.  '+val.amount+'</strong><span class="pull-right">'+val.data.status+'</span></li>';
                                                tbody += `
                                                    <tr>
                                                        <td>`+val.data.payid+`</td>
                                                        <td>`+val.amount+`</td>
                                                        <td>`+val.data.rrn+`</td>
                                                        <td>`+val.data.status+`</td>
                                                    </tr>        
                                                `;
                                            }else{
                                                out += '<li class="list-group-item alert alert-danger no-margin mb-10"><strong>Rs.  '+val.amount+'</strong><span class="pull-right">'+val.data.status+'</span></li>';
                                            }
                                        });
                                        $('.transactionData').html(out);
                                        if(samount != 0){
                                            $('#receptTable').fadeIn('400');                            
                                            $('.benename').text(benename);
                                            $('.beneaccount').text(beneaccount);
                                            $('.benebank').text(bankname);
                                            $('.benemobile').text(mobile);
                                            $('#receptTable').find('tbody').html(tbody);
                                            $('.samount').text(parseFloat(samount));
                                        }else{
                                            $('#receptTable').fadeOut('400');
                                        }
                                        $('#receipt').modal();
                                    }
                                    
                                }
                                },
                                error: function(errors){
                                    swal.close();
                                    showError(errors, 'withoutform');
                                }
                            });
                        });
                    },
                });
            }
        });

        $( "#registrationForm" ).validate({
            rules: {
                name: {
                    required: true,
                },
                surname: {
                    required: true,
                },
                pincode: {
                    required: true,
                    number : true,
                    minlength:6,
                    maxlength:6
                },
            },
            messages: {
                name: {
                    required: "Please enter firstname",
                },
                surname: {
                    required: "Please enter surname",
                },
                pincode: {
                    required: "Please enter pincode",
                    number: "Pincode should be numeric",
                    minlenght: "Pincode length should be 6 digit",
                    maxlenght: "Pincode length should be 6 digit",
                }
            },
            errorElement: "p",
            errorPlacement: function ( error, element ) {
                if ( element.prop("tagName").toLowerCase() === "select" ) {
                    error.insertAfter( element.closest( ".form-group" ).find(".select2") );
                } else {
                    error.insertAfter( element );
                }
            },
            submitHandler: function () {
                var form = $('#registrationForm');
                var type = form.find('input[name="type"]').val();
                var mobile = form.find('input[name="mobile"]').val();
                form.ajaxSubmit({
                    dataType:'json',
                    beforeSubmit:function(){
                        form.find('button[type="submit"]').button('loading');
                    },
                    success:function(data){
                        form.find('button[type="submit"]').button('reset');
                        if(data.statuscode == "TXN"){
                            form.closest('.modal').modal('hide');
                            $('#serachForm').submit();
                        }else{
                            notify(data.message, 'danger', "inline",form);
                        }
                    },
                    error: function(errors) {
                        showError(errors, form);
                    }
                });
            }
        });
        
         $( "#ekycotpForm" ).validate({
            rules: {
                otp: {
                    required: true,
                }
                
            },
            messages: {
                otp: {
                    required: "Please enter otp",
                }
            },
            errorElement: "p",
            errorPlacement: function ( error, element ) {
                if ( element.prop("tagName").toLowerCase() === "select" ) {
                    error.insertAfter( element.closest( ".form-group" ).find(".select2") );
                } else {
                    error.insertAfter( element );
                }
            },
            submitHandler: function () {
                var form = $('#ekycotpForm');
                var type = form.find('input[name="type"]').val();
                var mobile = form.find('input[name="mobile"]').val();
                form.ajaxSubmit({
                    dataType:'json',
                    beforeSubmit:function(){
                        form.find('button[type="submit"]').button('loading');
                    },
                    success:function(data){
                        form.find('button[type="submit"]').button('reset');
                        if(data.statuscode == "TXN"){
                            form.closest('.modal').modal('hide');
                            $('#twostepauthmodal').hide();
                            // $('#serachForm').submit();
                            setVerifyData(data);
                            //setBeneData(data);
                        }else{
                            notify(data.message, 'danger', "inline",form);
                        }
                    },
                    error: function(errors) {
                        showError(errors, form);
                    }
                });
            }
        });

        $('#getBenename').click(function(){
            var mobile = $(this).closest('form').find("[name='mobile']").val();
            var name = $(this).closest('form').find("[name='name']").val();
            var benebank = $(this).closest('form').find("[name='benebank']").val();
            var beneaccount = $(this).closest('form').find("[name='beneaccount']").val();
            var beneifsc = $(this).closest('form').find("[name='beneifsc']").val();
            var benename = $(this).closest('form').find("[name='benename']").val();
           // console.log('mobile:'+mobile,'name:'+name,'mobile:'+benebank,'mobile:'+beneaccount,'mobile:'+beneifsc,'benename:'+benename);
            if(mobile != '' || name != '' || benebank != '' || beneaccount != '' || beneifsc != '' || benename != ''){
                getBankName(mobile, name, benebank, beneaccount, beneifsc, benename);
            }
        });
    });
    
    
    function setVerifyData(data) {
        //$('.name').text(data.data.fname+' '+data.data.lname);
        $('.mobile').text(data.data.mobile);
        $('.totallimit').text( parseInt(data.data.bank2_limit));
        $('.remainlimit').text( parseInt(data.data.bank3_limit) );
        $('.usedlimit').text( data.data.limit);
        $('[name="mobile"]').val(data.data.mobile);
        $('[name="name"]').val(data.data.fname);
        $('#rname').val(data.name);
        $('#rlimit').val(data.data.bank3_limit);
        $('.userdetails').fadeIn('400');
    }

    function setBeneData(data) {
       
        if(data.benedata.length > 0){
            out = ``;
            $.each(data.benedata , function(index, beneficiary) {
                out += `<tr>
                        <td>`+(index + 1)+`</td>
                        <td>`+beneficiary.name+`</td>`;
                        
                        if(beneficiary.verified == 1){
                            out += `<td>`+beneficiary.accno+` <span class="label label-success">Verified</span> <br> (`+beneficiary.ifsc+`)<br> ( `+beneficiary.bankname+` )</td>`;
                        }else{
                            out += `<td>`+beneficiary.accno+`<br> (`+beneficiary.ifsc+`)<br> ( `+beneficiary.bankname+` )</td>`;
                        }
                 
                out +=`<td><button class="btn bg-slate btn-xs legitRipple" style="font-size:larger;border-radius:10px;" onclick="sendMoney('`+beneficiary.bankid+`', '`+data.data.mobile+`','`+data.data.fname+`','`+beneficiary.bankname+`', '`+beneficiary.accno+`', '`+beneficiary.ifsc+`', '`+beneficiary.name+`', '`+beneficiary.bene_id+`')"><i class="fa fa-paper-plane"></i> Send</button>`;
                out +=`&nbsp;|&nbsp;<button class="btn btn-danger" onclick="benedelete('`+data.data.mobile+`','`+beneficiary.bene_id+`')"><i class="fa fa-trash"></i></button>`;
                
                out +=`</td>
                    </tr>`;
            });
            $('.transaction').find('tbody').html(out);
        }else{
            $('.transaction').find('tbody').html('');
        }
        
    }

    function getBankName(mobile, name, benebank, beneaccount, beneifsc, benename) {
        swal({
            title: 'Are you sure ?',
            text: "You want verify account details, it will charge.",
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: "Yes Verify",
            showLoaderOnConfirm: true,
            allowOutsideClick: () => !swal.isLoading(),
            preConfirm: () => {
                return new Promise((resolve) => {
                    $.ajax({
                        url: "{{route('dmt2pay')}}",
                        type: "POST",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType:'json',
                        data: {
                            'type':"accountverification",
                            'mobile':mobile,
                            "beneaccount":beneaccount,
                            "beneifsc":beneifsc,
                            "name":name,
                            "benebank":benebank,
                            "benename":benename,
                            "bankid":benebank
                        },
                        success: function(data){
                            swal.close();
                            if (data.status == "TXN") {
                                    $( "#pverify" ).css('display','none');
                                    $( "#psubmit" ).css('display','inline-block');
                                    $( "#beneficiaryForm" ).find('input[name="benename"]').val(data.message);
                                    $( "#beneficiaryForm" ).find('input[name="benename"]').blur();
                                    
                                swal(
                                    'Account Verified',
                                    "Account Name is - "+ data.message,
                                    'success'
                                );
                            }else {
                                swal('Oops!', data.message,'error');
                            }
                        },
                        error: function(errors){
                            swal.close();
                            showError(errors, 'withoutform');
                        }
                    });
                });
            },
        });
    }
    
    function searchtype(type){
    //   if(type == 'beniid')
    //   {
    //      $('div.beniids').append(`  <label>Beneficiary id</label>
    //       <input type="text" step="any" name="beneid" class="form-control" placeholder="Enter Benificary id" required=""> `);  
    //         $('input[name="type"]').val('beniverification');
    //   }else{
    //       $('div.beniids').empty() ;
    //       $('input[name="type"]').val('verification');
    //   }
    } 
    
    function sendMoney(rid, mobile, name, benebank, beneaccount, beneifsc, benename, beneid) {
        $('#transferForm').find('input[name="mobile"]').val(mobile);
        $('#transferForm').find('input[name="name"]').val(name);
        $('#transferForm').find('input[name="benebank"]').val(benebank);
        $('#transferForm').find('input[name="beneaccount"]').val(beneaccount);
        $('#transferForm').find('input[name="beneifsc"]').val(beneifsc);
        $('#transferForm').find('input[name="benename"]').val(benename);
        $('#transferForm').find('input[name="rid"]').val(rid);
        $('#transferForm').find('input[name="beneid"]').val(beneid);

        $('#transferForm').find('.benename').text(benename);
        $('#transferForm').find('.beneaccount').text(beneaccount);
        $('#transferForm').find('.beneifsc').text(beneifsc);
        $('#transferForm').find('.benebank').text(benebank);
        getUserLocation();
        $('#transferModal').modal();
    }

    function benedelete(rid, beneid){
        $.ajax({
            url: "{{route('dmt2pay')}}",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType:'json',
            data: {'rid':rid, 'type':"benedelete", "bid":beneid},
            beforeSend:function(){
                swal({
                    title: 'Wait!',
                    text: 'We are processing your request.',
                    allowOutsideClick: () => !swal.isLoading(),
                    onOpen: () => {
                        swal.showLoading()
                    }
                });
            },
            success: function(result){
                swal.close();
                if(result.statuscode == "TXN"){
                    $.alert({
                        title: 'Success',
                        content: "Beneficiary Successfully Deleted",
                        type: 'green'
                    });
                    $( "#serachForm" ).submit();
                   
                }else{
                    $.alert({
                        title: 'Oops!',
                        content: result.message,
                        type: 'red'
                    });
                }
            },
            error: function(error){
                swal.close();
                notify("Something went wrong", 'danger', "inline",form);
            }
        });
    }
         
   function scandataforauth() {
        var device = $( "#aepsAuthForm" ).find('select[name="device"]').val();
        rdservice(device, "11100",'authentication');
    }
 function rdservice(device, port, type="none",useHttps=true)
    {
        //var primaryUrl = "https://127.0.0.1:"+port;
         var primaryUrl = (useHttps ? "https" : "http") + "://127.0.0.1:" + port;
        $.ajax({
            type: "RDSERVICE",
            async: true,
            crossDomain: true,
            url: primaryUrl,
            processData: false,
            beforeSend: function(){
                swal({
                    title: 'Wait!',
                    text: 'We are fetching your RD service...',
                    onOpen: () => {
                        swal.showLoading()
                    },
                    allowOutsideClick: () => !swal.isLoading()
                });
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
                    capture(device, port, type);
                }else if(CmbData1 == "NOTREADY" && CmbData2 == "Mantra Authentication Vendor Device Manager"){
                    rdservice(device, "11101", type);
                }else{
                    notify("Device : "+CmbData1, 'warning');
                }
            },
            error: function (jqXHR, ajaxOptions, thrownError) {
                $('#aepsTransactionForm').unblock();
                 if (useHttps && ajaxOptions === "error") {
                            //console.log("HTTPS failed, retrying with HTTP...");
                            //notify("HTTPS failed, retrying with HTTP...", 'warning');
                            rdservice(device, port, type, false); // Retry with HTTP
                    }else{
                            if(port == "11100"){
                            rdservice(device, "11101", type);
                        }else{
                            notify("Oops! Device not working correctly, please try again", 'warning');
                    }
            }    
            },
        });
    } 
    function capture(device, port, type,useHttps=true){
       
        //var primaryUrl = "https://127.0.0.1:"+port;
        var primaryUrl = (useHttps ? "https" : "http") + "://127.0.0.1:" + port;
        var transactionType = $('#transactionType').val();
        var ftype="0";
        if(transactionType == "M") ftype = "2";

        
        if(device == "MANTRA_PROTOBUF"){
            var url = primaryUrl+"/rd/capture";
       
        }else{
            var url = primaryUrl+"/capture";
            
        }

         
           
        
            if(device == "MANTRA_PROTOBUF"){
                var XML='<PidOptions ver=\"1.0\"><Opts env=\"P\" fCount=\"1\" fType=\"2\" iCount=\"0\" format=\"0\" pidVer=\"2.0\" timeout=\"15000\" wadh=\"18f4CEiXeXcfGXvgWA/blxD+w2pw7hfQPY45JMytkPw=\" posh=\"UNKNOWN\" /></PidOptions>';
            
                 
            }else{
                var XML='<PidOptions ver=\"1.0\">' + '<Opts fCount=\"1\" fType=\"2\" iCount=\"\" iType=\"\" pCount=\"\" pType=\"\" format=\"0\" pidVer=\"2.0\" timeout=\"10000\" otp=\"\" wadh=\"\" posh=\"\"/>' + '</PidOptions>'; 
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
                swal({
                    title: 'Wait!',
                    text: 'Please wait, capturing finger',
                    onOpen: () => {
                        swal.showLoading()
                    },
                    allowOutsideClick: () => !swal.isLoading()
                });
            },
            success: function (data) {
                if(device == "MANTRA_PROTOBUF"){
                    var $doc = $.parseXML(data);
                    var errorInfo =  $($doc).find('Resp').attr('errInfo');
                    const dotsRemoved = errorInfo.replaceAll('.', '');

                    notify("Fingerprint Captured Successfully", "success");
                   
                         $('[name="mybiodata"]').val(data);
                          $('#aepsAuthForm').submit();
                    
                    
                }else{
                    var errorInfo =  $(data).find('Resp').attr('errInfo');
                    var errorCode =  $(data).find('Resp').attr('errCode');
                    var mydata =  $(data).find('PidData').html();
                    if(errorCode == '0'){
                        notify("Fingerprint Captured Successfully", "success");
                       
                            $('[name="mybiodata"]').val("<PidData>"+mydata+"</PidData>");
                              $('#aepsAuthForm').submit();
                        
                    }else{
                        notify("Oops! Device not working correctly, please try again2", "warning");
                    }
                }
            },
            error: function (jqXHR, ajaxOptions, thrownError) {
                
                if (useHttps && ajaxOptions === "error") {
                            //console.log("HTTPS failed, retrying with HTTP...");
                           // notify("HTTPS failed, retrying with HTTP...", 'warning');
                            capture(device, port, type, false); // Retry with HTTP
                        }else{
                         notify("Oops! Device not working correctly, please try again", "warning");   
                        }
                //notify("Oops! Device not working correctly, please try again3", "warning");
            },
        });
    }
    
    function getUserLocation() {
        var $locationText = $(".location");
    
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                geoLocationSuccess,
                geoLocationError,
                { timeout: 10000 }
            );
        } else {
            alert("Your browser doesn't support geolocation");
        }
    
        function geoLocationSuccess(pos) {
            var myLat = pos.coords.latitude,
                myLng = pos.coords.longitude,
                loadingTimeout;
    
            $('#latitude').val(myLat);
            $('#longitude').val(myLng);
    
            var loading = function () {
                $locationText.val("Fetching...");
            };
    
            loadingTimeout = setTimeout(loading, 600);
    
            $.get(
                "https://nominatim.openstreetmap.org/reverse?format=json&lat=" +
                myLat +
                "&lon=" +
                myLng
            )
            .done(function (data) {
                if (loadingTimeout) {
                    clearTimeout(loadingTimeout);
                    loadingTimeout = null;
                    $locationText.val(data.display_name);
                }
            })
            .fail(function () {
                $locationText.val("Unable to fetch location");
            });
        }
    
        function geoLocationError(error) {
            var errors = {
                1: "Permission denied",
                2: "Position unavailable",
                3: "Request timeout"
            };
            alert("Error: " + errors[error.code]);
        }
    }

// Bind the function to the button click


</script>
@endpush