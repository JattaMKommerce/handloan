@php
    $name = explode(" ", Auth::user()->name);
@endphp

@extends('layouts.app')
@section('title', "Aadhar Banking")
@section('pagetitle', "Aadhar Banking ")
@php
    $table = "yes";
@endphp

@section('content')
<style>
.legitRipple.active{
    background: #000 !important; 
}
</style>
<div class="content">
   
    @if(!$agent) 
        <div class="row">
            <div class="col-sm-12">
                <div class="iq-card">
                    <div class="panel-heading">
                        <h4 class="panel-title">Merchant AePs KYC</h4>
                    </div>
                    <div class="iq-card-body">
                        @if($agent)
                            @if($agent->status == "rejected")
                                <p class="text-danger">Reason - {{$agent->remark}}</p>
                            @endif
                        @endif
                        <form action="{{ route('iaepstransaction') }}" method="post" id="fingkycForm" enctype="multipart/form-data"> 
                            {{ csrf_field() }}
                            <input type="hidden" name="transactionType" id="transactionType" value="useronboard">
                            <div class="row">
                                    <div class="form-group col-md-4">
                                    <label>First Name</label>
                                    <input type="text" class="form-control" autocomplete="off" oninput="this.value = this.value.toUpperCase()" name="merchantFName" placeholder="Enter First Name" value="" required >
                                </div>
                                 <div class="form-group col-md-4">
                                    <label>Middle Name</label>
                                    <input type="text" class="form-control" autocomplete="off" oninput="this.value = this.value.toUpperCase()" name="merchantMName" placeholder="Enter Middle Name" value="" >
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Last Name</label>
                                    <input type="text" class="form-control" autocomplete="off" oninput="this.value = this.value.toUpperCase()" name="merchantLName" placeholder="Enter Last Name" value="" >
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Mobile</label>
                                    <input type="text" pattern="[0-9]*" maxlength="10" minlength="10" class="form-control" oninput="this.value = this.value.toUpperCase()" name="merchantPhoneNumber" autocomplete="off" placeholder="Enter Your Mobile" value="{{Auth::user()->mobile}}" required readonly>
                                </div>
                               
                            </div>
                            <div class="row">
                                
                                
                                <div class="form-group col-md-4">
                                    <label>Aadhaar Number</label>
                                    <input type="text" class="form-control" name="merchantAadhar" pattern="[0-9]*" oninput="this.value = this.value.toUpperCase()" maxlength="12" minlength="12" autocomplete="off" placeholder="Enter Your Aadhaar" value="" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label>Pancard Number</label>
                                    <input type="text" class="form-control" autocomplete="off" maxlength="10" minlength="10" oninput="this.value = this.value.toUpperCase()" name="userPan" placeholder="Enter Your Pancard" value=""  required>
                                </div>
                                
                                <div class="form-group col-md-4">
                                    <label>City</label>
                                    <input type="text" class="form-control" autocomplete="off" name="merchantCityName"  oninput="this.value = this.value.toUpperCase()" value="" placeholder="Enter Your City" required >
                                </div>
                                <div class="form-group col-md-4">
                                    <label>State</label>
                                    <select name="merchantState" class="form-control select" required>
                                        <option value="">Select State</option>
                                        @foreach ($mahastate as $state)
                                        <option value="{{$state->stateid}}">{{$state->statename}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                
                                <div class="form-group col-md-8">
                                    <label>Full Address</label>
                                    <input type="text" class="form-control" autocomplete="off" name="merchantAddress" oninput="this.value = this.value.toUpperCase()" placeholder="Enter Address" value="{{Auth::user()->address}}" required >
                                </div>
                                
                            </div>
                             <div class="row">
                                <div class="form-group col-md-4">
                                    <label>Pin Code </label>
                                    <input type="text" class="form-control" autocomplete="off" name="merchantPinCode"oninput="this.value = this.value.toUpperCase()"  maxlength="6" minlength="6" pattern="[0-9]*"  placeholder="Enter Merchant Pincode" required>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>District Name </label>
                                    <input type="text" class="form-control" autocomplete="off" name="merchantDistrictName" oninput="this.value = this.value.toUpperCase()"    placeholder="Enter merchantDistrictName" required>
                                </div>
                                 <div class="form-group col-md-4">
                                    <label>Pancard Image </label>
                                    <input type="file" class="form-control" autocomplete="off" accept="image/x-png,image/gif,image/jpeg" name="pancardPics" placeholder="" required>
                                    <small style="color:red;">Note: Upload Self attached Pan Card Image.</small>
                                </div>
                                 <div class="form-group col-md-4">
                                    <label>Aadhaarcard Image </label>
                                    <input type="file" class="form-control" accept="image/x-png,image/gif,image/jpeg" autocomplete="off" name="aadharPics" placeholder="" required>
                                    <small style="color:red;">Note: Aadhaar card image should contain front and back.(Self attached)</small>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label>Passport Size Pic </label>
                                    <input type="file" class="form-control" autocomplete="off" accept="image/x-png,image/gif,image/jpeg" name="passports" placeholder="" required>
                                    <small style="color:red;">Note: Passport Size Pic.</small>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Shop/ Workplace Pic  </label>
                                    <input type="file" class="form-control" autocomplete="off" accept="image/x-png,image/gif,image/jpeg" name="shoppics" placeholder="" required>
                                    <small style="color:red;">note : shop/ workplace full photo with merchant</small>
                                </div>
                                <div class="form-group col-md-4">
                                    <label> Bank AccountNumber </label>
                                    <input type="text" pattern="[0-9]*" class="form-control" oninput="this.value = this.value.toUpperCase()" name="companyBankAccountNumber" autocomplete="off" placeholder="Enter Your companyBankAccountNumber"  required >
                                </div>
                                <div class="form-group col-md-4">
                                    <label>bankIfscCode </label>
                                    <input type="text" class="form-control" autocomplete="off"   name="bankIfscCode" placeholder="bankIfscCode" value=""  required>
                                </div>
                            </div>
                               
                    </div>
                </div>
            </div>
             <div class="col-sm-12">
                <div class="iq-card">
                    <div class="panel-heading">
                        <h4 class="panel-title">Company KYC</h4>
                    </div>
                    <div class="iq-card-body">
                       
                            <div class="row">
                                    
                                  <div class="form-group col-md-4">
                                    <label>Company Type</label>
                                    <select name="companyType" class="form-control select" required>
                                        <option value="">Select CompanyType</option>
                                        @foreach ($companyTypes as $companyType)
                                        <option value="{{$companyType['mccCode']}}">{{$companyType['mccDescription']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                               
                            </div>
                            
                            
                         
                                <div class="form-group text-center">
                                    <button type="submit" class="btn bg-teal-400 btn-labeled btn-rounded legitRipple btn-primary" data-loading-text="<b><i class='fa fa-spin fa-spinner'></i></b> Submitting"><b><i class=" icon-paperplane"></i></b> Submit Kyc</button>
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
        </div>
        @elseif($agent->merchant_status == "pending")
        <div class="row">
            <div class="col-sm-12">
                <div class="iq-card">
                    <div class="panel-heading">
                        <h4 class="panel-title">Merchant AePs KYC</h4>
                    </div>
                    <div class="iq-card-body">
                        @if($agent)
                            @if($agent->status == "rejected")
                                <p class="text-danger">Reason - {{$agent->remark}}</p>
                            @endif
                        @endif
                        <form action="{{ route('iaepstransaction') }}" method="post" id="fingkycForm" enctype="multipart/form-data"> 
                            {{ csrf_field() }}
                            <input type="hidden" name="transactionType" id="transactionType" value="useronboard">
                            <div class="row">
                                    <div class="form-group col-md-4">
                                    <label>First Name</label>
                                    <input type="text" class="form-control" autocomplete="off" oninput="this.value = this.value.toUpperCase()" name="merchantFName" placeholder="Enter First Name" value="" required >
                                </div>
                                 <div class="form-group col-md-4">
                                    <label>Middle Name</label>
                                    <input type="text" class="form-control" autocomplete="off" oninput="this.value = this.value.toUpperCase()" name="merchantMName" placeholder="Enter Middle Name" value="" >
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Last Name</label>
                                    <input type="text" class="form-control" autocomplete="off" oninput="this.value = this.value.toUpperCase()" name="merchantLName" placeholder="Enter Last Name" value="" >
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Mobile</label>
                                    <input type="text" pattern="[0-9]*" maxlength="10" minlength="10" class="form-control" oninput="this.value = this.value.toUpperCase()" name="merchantPhoneNumber" autocomplete="off" placeholder="Enter Your Mobile" value="{{Auth::user()->mobile}}" required readonly>
                                </div>
                                <div class="form-group col-md-4">
                                    <label> Alternate Number</label>
                                    <input type="text" pattern="[0-9]*" maxlength="10" minlength="10" class="form-control" oninput="this.value = this.value.toUpperCase()" name="merchantalernativeNumber" autocomplete="off" placeholder="Enter Your Alternative Mobile"  required >
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label>Father Name</label>
                                    <input type="text" class="form-control" autocomplete="off"   name="father" placeholder="Father Name" value=""  required>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Date Of Birth</label>
                                    <input type="date" class="form-control" autocomplete="off"   name="dob"  value=""  required>
                                </div>
                                
                                <div class="form-group col-md-4">
                                    <label>Aadhaar Number</label>
                                    <input type="text" class="form-control" name="merchantAadhar" pattern="[0-9]*" oninput="this.value = this.value.toUpperCase()" maxlength="12" minlength="12" autocomplete="off" placeholder="Enter Your Aadhaar" value="" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label>Pancard Number</label>
                                    <input type="text" class="form-control" autocomplete="off" maxlength="10" minlength="10" oninput="this.value = this.value.toUpperCase()" name="userPan" placeholder="Enter Your Pancard" value=""  required>
                                </div>
                                
                                <div class="form-group col-md-4">
                                    <label>City</label>
                                    <input type="text" class="form-control" autocomplete="off" name="merchantCityName"  oninput="this.value = this.value.toUpperCase()" value="" placeholder="Enter Your City" required >
                                </div>
                                <div class="form-group col-md-4">
                                    <label>State</label>
                                    <select name="merchantState" class="form-control select" required>
                                        <option value="">Select State</option>
                                        @foreach ($mahastate as $state)
                                        <option value="{{$state->stateid}}">{{$state->statename}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label> Police Station/ Thana</label>
                                    <input type="text" class="form-control" autocomplete="off"   name="thana"  value=""  required>
                                </div>
                                <div class="form-group col-md-8">
                                    <label>Full Address</label>
                                    <input type="text" class="form-control" autocomplete="off" name="merchantAddress" oninput="this.value = this.value.toUpperCase()" placeholder="Enter Address" value="{{Auth::user()->address}}" required >
                                </div>
                                
                            </div>
                             <div class="row">
                                <div class="form-group col-md-4">
                                    <label>Pin Code </label>
                                    <input type="text" class="form-control" autocomplete="off" name="merchantPinCode"oninput="this.value = this.value.toUpperCase()"  maxlength="6" minlength="6" pattern="[0-9]*"  placeholder="Enter Merchant Pincode" required>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>District Name </label>
                                    <input type="text" class="form-control" autocomplete="off" name="merchantDistrictName" oninput="this.value = this.value.toUpperCase()"    placeholder="Enter merchantDistrictName" required>
                                </div>
                                 <div class="form-group col-md-4">
                                    <label>Pancard Image </label>
                                    <input type="file" class="form-control" autocomplete="off" accept="image/x-png,image/gif,image/jpeg" name="pancardPics" placeholder="" required>
                                    <small style="color:red;">Note: Upload Self attached Pan Card Image.</small>
                                </div>
                                 <div class="form-group col-md-4">
                                    <label>Aadhaarcard Image </label>
                                    <input type="file" class="form-control" accept="image/x-png,image/gif,image/jpeg" autocomplete="off" name="aadharPics" placeholder="" required>
                                    <small style="color:red;">Note: Aadhaar card image should contain front and back.(Self attached)</small>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label>Passport Size Pic </label>
                                    <input type="file" class="form-control" autocomplete="off" accept="image/x-png,image/gif,image/jpeg" name="passports" placeholder="" required>
                                    <small style="color:red;">Note: Passport Size Pic.</small>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Shop/ Workplace Pic  </label>
                                    <input type="file" class="form-control" autocomplete="off" accept="image/x-png,image/gif,image/jpeg" name="shoppics" placeholder="" required>
                                    <small style="color:red;">note : shop/ workplace full photo with merchant</small>
                                </div>
                                <div class="form-group col-md-4">
                                    <label> Bank AccountNumber </label>
                                    <input type="text" pattern="[0-9]*" class="form-control" oninput="this.value = this.value.toUpperCase()" name="companyBankAccountNumber" autocomplete="off" placeholder="Enter Your companyBankAccountNumber"  required >
                                </div>
                                <div class="form-group col-md-4">
                                    <label>bankIfscCode </label>
                                    <input type="text" class="form-control" autocomplete="off"   name="bankIfscCode" placeholder="bankIfscCode" value=""  required>
                                </div>
                            </div>
                               
                    </div>
                </div>
            </div>
             <div class="col-sm-12">
                <div class="iq-card">
                    <div class="panel-heading">
                        <h4 class="panel-title">Company KYC</h4>
                    </div>
                    <div class="iq-card-body">
                       
                            <div class="row">
                                    
                                  <div class="form-group col-md-4">
                                    <label>Company Type</label>
                                    <select name="companyType" class="form-control select" required>
                                        <option value="">Select CompanyType</option>
                                        @foreach ($companyTypes as $companyType)
                                        <option value="{{$companyType['mccCode']}}">{{$companyType['mccDescription']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                               
                            </div>
                            
                            
                         
                                <div class="form-group text-center">
                                    <button type="submit" class="btn bg-teal-400 btn-labeled btn-rounded legitRipple btn-primary" data-loading-text="<b><i class='fa fa-spin fa-spinner'></i></b> Submitting"><b><i class=" icon-paperplane"></i></b> Submit Kyc</button>
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
                                <form action="{{route('iaepstransaction')}}" method="POST" id="aepsTransactionForm" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    <div class="iq-card-body">
                                        <input type="hidden" name="transactionType" id="transactionType" value="BE">
                                        <input type="hidden" name="aeps" value="">
                                        <div class="">
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
                                            <input type="hidden" id="txtPidData" name="txtPidData" value="" class="form-control">
                                            
                                        <div class="row">
                                            <div class="col">
                                                <label>Mobile Number :</label>
                                                <input type="text"  class="form-control" name="mobileNumber" id="mobileNumber" maxlength="10"  autocomplete="off" placeholder="Enter mobile number" required>
                                                
                                            </div>
                                            <div class="col">
                                                <label>Aadhar Number :</label>
                                                <input type="text" class="form-control" name="adhaarNumber" id="adhaarNumber" maxlength="12" minlength="12" autocomplete="off" pattern="[0-9]*"  placeholder="Enter aadhar number" required="">
                                            </div>
                                            <div class="col">
                                                <label>Bank :</label>
                                                <div id="bankId1">
                                                    <select name="bankName1" id="bankName1" class="form-control select" required="">
                                                        <option value="">Select Bank</option>         
                                                        @foreach ($bankName as $bank)
                                                            <option value="{{$bank->iinno}}">{{$bank->bankName}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div id="bankId2"  style="display: none;">
                                                    <select name="bankName2"  class="form-control select">
                                                        <option value="">Select Bank</option>
                                                        @foreach ($bankName1 as $bank)
                                                            <option value="{{$bank->iinno}}">{{$bank->bankName}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            
                                            <div class="form-group col-md-6 transactionAmount">
                                                
                                            </div>
                                       </div>
                                <!--<div class="row">-->
                                    <!--<div class="panel-footer text-center">-->
                                    
                                    <!--</div>-->
                                <!--</div>-->
                            </div>
                            @if($agent->status == "approved")
                                        <button type="submit" class="btn btn-primary" data-loading-text="<b><i class='fa fa-spin fa-spinner'></i></b> Proceeding...">Scan & Submit</button>
                                        @if($agent->everify == "pending")
                                            <button type="button" class="btn btn-primary everify">Click Here To Complete E-Kyc</button>
                                        @endif
                                    @elseif($agent->status == "approved" && $agent->everify == "pending"))
                                        <button type="button" class="btn btn-danger everify">Click Here To Complete E-Kyc</button>
                                    @else
                                        <h4 class="text-danger">Kyc is {{$agent->status}}</h4>
                                    @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
   <!-- <div class="">
        <h5>Powered By </h5>
        <img src="{{asset('assets/icicilogo.png')}}" class="img-responsive" width="200px">
    </div>-->
</div>



<div id="receipt" class="modal " role="dialog" tabindex="-1" data-backdrop="false">
    
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal" style="margin-top: -17px;">&times;</button>
                <h6 class="modal-title bgtor">Receipt</h6>
            </div>
            <div id="printreceipt" class="modal-body p-0">
            <div class="modal-body p-0">
                <div class="panel panel-primary">
                    <div class="iq-card-body">
                        <div class="clearfix">
                            <div class="pull-left">
                                <h4>
                                    @if (Auth::user()->company->logo)
                                        <img src="{{asset('')}}logos/{{Auth::user()->company->logo}}" class=" img-responsive" alt="" style="width: 220px;height: 40px;">
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
                                        <strong>Date: </strong> <span class="created_at">{{date('Y-m-d H:i:s')}}</span><br>
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
</div>

<div id="ministatement" class="modal " role="dialog" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal" style="margin-top: -17px;">&times;</button>
                <h6 class="modal-title bgtor">Mini Statement</h6>
            </div>
            <div id="printmini" class="modal-body p-0">
                <div class="panel panel-primary">
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
                                                <th>Narrartion</th>
                                                <th>Credit (Rs)</th>
                                                <th>Debit (Rs)</th>
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
                                <a href="javascript:void(0)"  id="statementprint" class="btn btn-primary waves-effect waves-light"><i class="fa fa-print"></i></a>
                                <button type="button" class="btn btn-warning waves-effect waves-light" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="twostepauthmodal" class="modal " role="dialog" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal" style="margin-top: -17px;">&times;</button>
                <h6 class="modal-title bgtor">2nd Facer Authentication</h6>
            </div>
            
            <div class="modal-body p-0">
                <div class="panel panel-primary">
                    <div class="iq-card-body p-5">
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
                       
                        <form action="{{route('iaepstransaction')}}" method="POST" id="aepsAuthForm" enctype="multipart/form-data">
                           {{ csrf_field() }}
                           <input type="hidden" id="txtPidData" name="txtPidData" value="" class="form-control">
                          <input type="hidden" name="transactionType" id="transactionType" value="2fa">
                            <input type="hidden" name="aeps" value="">
                           <div class="row">
                               <div class="form-group col-md-12">
                                    <label>Aadhar Number :</label>
                                    <input type="text" class="form-control" name="adhaarNumber" value="{{Auth::user()->aadharcard}}" id="adhaarNumber" maxlength="12" minlength="12" autocomplete="off" pattern="[0-9]*"  placeholder="Enter aadhar number" required="">
                            
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


</div>
@endif

@endsection

@push('script')
<link href="{{asset('')}}assets/css/jquery-confirm.min.css" rel="stylesheet" type="text/css">
<style type="text/css">
    .error{
    color: red;
  }
</style>
<script type="text/javascript" src="{{asset('')}}assets/js/core/jquery-confirm.min.js"></script>
<script type="text/javascript" src="{{asset('')}}assets/js/core/notify.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        @if(isset($isdoneaepsauth) && $isdoneaepsauth == 'false' && !empty($agent->everify) && $agent->everify == "success")
            $('#twostepauthmodal').modal();  
         @endif
        
        $('#print').click(function(){
            var ghtml = $('#printreceipt').html();
            var newWin=window.open('','Print-Window');
                newWin.document.open();
                newWin.document.open();
                newWin.document.write('<html><body onload="window.print()">'+ghtml+'</body></html>');
                newWin.document.close();
              setTimeout(function(){newWin.close();},10);
        });
        
        $('#statementprint').click(function(){
           var ghtml = $('#printmini').html();
            var newWin=window.open('','Print-Window');
                newWin.document.open();
                newWin.document.open();
                newWin.document.write('<html><body onload="window.print()">'+ghtml+'</body></html>');
                newWin.document.close();
              setTimeout(function(){newWin.close();},10);
        });
                    
        $( "#fingkycForm" ).validate({
            rules: {
                merchantFName: {
                    required: true
                },
                merchantAddress: {
                    required: true
                },
                merchantState: {
                    required: true
                },
                merchantPhoneNumber: {
                    required: true,
                    number: true,
                    minlength: 10,
                    maxlength: 10
                },
                merchantAadhar: {
                    required: true,
                    number: true,
                    minlength: 12,
                    maxlength: 12
                },
                userPan: {
                    required: true
                },
                merchantPinCode: {
                    required: true,
                    number: true,
                    minlength: 6,
                    maxlength: 6
                }
            },
            messages: {
                merchantName: {
                    required: "Please enter value",
                },
                merchantAddress: {
                    required: "Please enter value",
                },
                merchantState: {
                    required: "Please enter value",
                },
                merchantPhoneNumber: {
                    required: "Please enter value",
                    nnumber: "Aadhar number should be numeric",
                    minlength: "Your aadhar number must be 10 digit",
                    maxlength: "Your aadhar number must be 10 digit"
                },
                merchantAadhar: {
                    required: "Please enter value",
                    nnumber: "Aadhar number should be numeric",
                    minlength: "Your aadhar number must be 12 digit",
                    maxlength: "Your aadhar number must be 12 digit"
                },
                userPan: {
                    required: "Please enter value",
                },
                merchantPinCode: {
                    required: "Please enter value",
                    nnumber: "Aadhar number should be numeric",
                    minlength: "Your aadhar number must be 6 digit",
                    maxlength: "Your aadhar number must be 6 digit"
                }
            },
            errorElement: "p",
            errorPlacement: function ( error, element ) {
                if ( element.prop( "name" ) === "bank" ) {
                    error.insertAfter( element.closest( ".form-group" ).find(".select2") );
                } else {
                    error.insertAfter( element );
                }
            },
            submitHandler: function (form) {
                var form = $('#fingkycForm');
                
                form.ajaxSubmit({
                    dataType:'json',
                    beforeSubmit:function(){
                        form.find('button[type="submit"]').button('loading');
                    },
                    success:function(data){
                        form.find('button[type="submit"]').button('reset');
                        if(data.status == "TXN"){
                            swal({
                                title:'Suceess', 
                                text : data.message, 
                                type : 'success',
                                onClose: () => {
                                    window.location.reload();
                                }
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
                        form.find('[name="txtPidData"]').val('');
                        showError(errors, form);
                    }
                });
            }
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
                            form.find('button[type="submit"]').button('reset');
                            form.find('[name="txtPidData"]').val('');
                            if(data.status == "success" || data.status == "pending"){
                                form[0].reset();
                                form.find('select').select2().val(null).trigger('change');
                                getbalance();
                                form.find('button[type="submit"]').button('reset');
                                if(data.status == "success"|| data.status == "pending"){
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
                                            if(val.txnType == "Cr"){
                                                trdata += `<tr>
                                                        <td>`+val.date+`</td>
                                                        <td>`+val.narration+`</td>
                                                        <td>`+val.amount+`</td>
                                                        <td></td>
                                                    </tr>`;
                                            }else{
                                                trdata += `<tr>
                                                        <td>`+val.date+`</td>
                                                        <td>`+val.narration+`</td>
                                                        <td></td>
                                                        <td>`+val.amount+`</td>
                                                    </tr>`;
                                            }
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
                var scan = form.find('[name="txtPidData"]').val();
               
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
                            form.find('button[type="submit"]').button('reset');
                            form.find('[name="txtPidData"]').val('');
                            if(data.status == "success"){
                                swal({
                                    title:'Suceess', 
                                    text : data.message, 
                                    type : 'success',
                                    onClose: () => {
                                        window.location.reload();
                                    }
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
                            form.find('[name="txtPidData"]').val('');
                            showError(errors, form);
                        }
                    });
                }else{
                    
                    scandataforauth();
                }
            }
        });
        
       
        
        $('.everify').click(function (){
            SYSTEM.AJAX("{{route('iaepstransaction')}}", "POST", {"transactionType" : "useronboardotp"}, function(data){
            if(!data.statusText){
                if(data.status == "TXNOTP"){
                    var otpConfirm = $.confirm({
                        lazyOpen: true,
                        title: 'Otp Verification',
                        content: '' +
                        '<form action="javascript:void(0)" class="formName">' +
                        '<div class="form-group">' +
                        '<label>Otp</label>' +
                        '<input type="hidden" name="transactionType" value="useronboardvalidate"  class="form-control" required />' +
                        '<input type="hidden" name="primaryKeyId" value="'+data.primaryKeyId+'"  class="form-control" required />' +
                        '<input type="hidden" name="encodeFPTxnId" value="'+data.encodeFPTxnId+'"  class="form-control" required />' +
                        '<input type="text" placeholder="Enter Otp" name="otp" class="name form-control" required />' +
                        '</div>' +
                        '</form>',
                        buttons: {
                            formSubmit: {
                                text: 'Submit',
                                btnClass: 'btn-blue',
                                action: function () {
                                    var otp = this.$content.find('[name="otp"]').val();
                                    var primaryKeyId  = this.$content.find('[name="primaryKeyId"]').val();
                                    var encodeFPTxnId = this.$content.find('[name="encodeFPTxnId"]').val();
                                    if(!otp){
                                        $.alert({
                                            title: 'Oops!',
                                            content: 'Provide a valid otp',
                                            type: 'red'
                                        });
                                        return false;
                                    }

                                    SYSTEM.AJAX("{{route('iaepstransaction')}}", "POST", { "transactionType" : "useronboardvalidate", "otp" : otp, "primaryKeyId" : primaryKeyId, 'encodeFPTxnId' : encodeFPTxnId}, function(data){
                                        if(!data.statusText){
                                            if(data.status == "TXN"){
                                                otpConfirm.close();
                                                var kycConfirm = $.confirm({
                        lazyOpen: true,
                        title: 'E-Kyc',
                        content: '' +
                        '<form action="javascript:void(0)" class="formName">' +
                        '<div class="form-group">' +
                        '<label>Device</label>' +
                        '<input type="hidden" name="transactionType" value="useronboardekyc"  class="form-control" required />' +
                        '<input type="hidden" name="primaryKeyId" value="'+primaryKeyId+'"  class="form-control" required />' +
                        '<input type="hidden" name="encodeFPTxnId" value="'+encodeFPTxnId+'"  class="form-control" required />' +
                        '<input type="hidden" name="mybiodata"  class="form-control" required />' +
                        `<select name="device" class="form-control" required>
                            <option value="">Select Device</option>
                            <option value="MANTRA_PROTOBUF">Mantra Device</option>
                            <option value="MORPHO_PROTOBUF">Other Device</option>
                        </select>
                        ` +
                        '</div>' +
                        '</form>',
                        buttons: {
                            scan: {
                                text: 'Scan',
                                btnClass: 'btn-blue',
                                keys: ['enter', 'shift'],
                                action: function(){
                                    var device = this.$content.find('[name="device"]').val();
                                    if(!device){
                                        $.alert({
                                            title: 'Oops!',
                                            content: 'Please select device',
                                            type: 'red'
                                        });
                                        return false;
                                    }
                                    rdservice(device, "11100",'kyc');
                                    return false;
                                }
                            },
                            formSubmit: {
                                text: 'Submit',
                                btnClass: 'btn-blue',
                                action: function () {
                                    var device = this.$content.find('[name="device"]').val();
                                    var mybiodata  = this.$content.find('[name="mybiodata"]').val();
                                    var primaryKeyId  = this.$content.find('[name="primaryKeyId"]').val();
                                    var encodeFPTxnId = this.$content.find('[name="encodeFPTxnId"]').val();
                                    if(!device){
                                        $.alert({
                                            title: 'Oops!',
                                            content: 'Please select device',
                                            type: 'red'
                                        });
                                        return false;
                                    }
                                    
                                    if(!mybiodata){
                                        $.alert({
                                            title: 'Oops!',
                                            content: 'Please scan your finger',
                                            type: 'red'
                                        });
                                        return false;
                                    }
                                    
                                    SYSTEM.AJAX("{{route('iaepstransaction')}}", "POST", { "transactionType" : "useronboardekyc", "biodata" : mybiodata, "primaryKeyId" : primaryKeyId, 'encodeFPTxnId' : encodeFPTxnId,'device' : device}, function(data){
                                        if(!data.statusText){
                                            if(data.status == "TXN"){
                                                kycConfirm.close();
                                                $.alert({
                                                    icon: 'fa fa-check',
                                                    theme: 'modern',
                                                    animation: 'scale',
                                                    type: 'green',
                                                    title : "Success",
                                                    content : data.message,
                                                    buttons: {
                                                        somethingElse: {
                                                            text: 'Ok',
                                                            btnClass: 'btn-primary',
                                                            action: function(){
                                                                location.reload();
                                                            }
                                                        }
                                                    }
                                                });
                                            }else{
                                                this.$content.find('[name="mybiodata"]').val(null);
                                                if(data.status == 400){
                                                    $.alert({
                                                        title: 'Oops!',
                                                        content: data.responseJSON.message,
                                                        type: 'red'
                                                    });
                                                }else{
                                                    if(data.message){
                                                        $.alert({
                                                            title: 'Oops!',
                                                            content: data.message,
                                                            type: 'red'
                                                        });
                                                    }else{
                                                        $.alert({
                                                            title: 'Oops!',
                                                            content: data.statusText,
                                                            type: 'red'
                                                        });
                                                    }
                                                }
                                            }
                                        }
                                            
                                    });
                                                
                                    return false;
                                }
                            },
                            cancel: function () {
                            }
                        }
            });  
                    kycConfirm.open();
                                            }else{
                                                if(data.status == 400){
                                                    $.alert({
                                                        title: 'Oops!',
                                                        content: data.responseJSON.message,
                                                        type: 'red'
                                                    });
                                                }else{
                                                    if(data.message){
                                                        $.alert({
                                                            title: 'Oops!',
                                                            content: data.message,
                                                            type: 'red'
                                                        });
                                                    }else{
                                                        $.alert({
                                                            title: 'Oops!',
                                                            content: data.statusText,
                                                            type: 'red'
                                                        });
                                                    }
                                                }
                                            }
                                        }
                                    }, $('.jconfirm-box-container'), "Please Wait");
                                    return false;
                                }
                            },
                            cancel: function () {
                            },
                        }
            });  
                    otpConfirm.open();
                }else{
                    SYSTEM.SHOWERROR(data, $("#aepsTransactionForm"));
                }
            }else{
                SYSTEM.SHOWERROR(data, $("#aepsTransactionForm"));
            }
        }, '#aepsTransactionForm', 'Please Wait');
    });
    });
     var SYSTEM = {
    AJAX: function(url, method, data, callback, errorElement, waitMessage) {
         var csrfToken = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
            url: url,
            type: method,
            data: data,
             headers: {
                'X-CSRF-TOKEN': csrfToken  // Include the CSRF token in the headers
            },
            beforeSend: function() {
                if (waitMessage) {
                    $(errorElement).html(waitMessage);
                }
            },
            success: function(response) {
                callback(response);
            },
            error: function(jqXHR) {
                if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
                    SYSTEM.SHOWERROR(jqXHR.responseJSON, $(errorElement));
                } else {
                    SYSTEM.SHOWERROR(jqXHR, $(errorElement));
                }
            }
        });
    },
    SHOWERROR: function(data, element) {
        $(element).html(`<div class="alert alert-danger">${data.message || data.statusText}</div>`);
    }
};
    function scandata() {
        var device = $( "#aepsTransactionForm" ).find('[name="device"]:checked').val();
        rdservice(device, "11100");
    }
    
    
    function scandataforauth() {
        var device = $( "#aepsAuthForm" ).find('select[name="device"]').val();
        rdservice(device, "11100",'authentication');
    }
    
    
    function scandataforapauth() {
        var device = $( "#apAuthForm" ).find('select[name="device"]').val();
        rdservice(device, "11100",'apauthentication');
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

          if(type == "kyc"){
            
            var XML = '<?php echo'<?xml version="1.0"?>';?> <PidOptions ver="1.0"> <Opts fCount="1" fType="2" iCount="0" pCount="0" pgCount="2" format="0"   pidVer="2.0" timeout="10000" pTimeout="20000" posh="UNKNOWN" env="P" wadh="E0jzJ/P8UopUHAieZn8CKqS4WPMi5ZSYXgfnlfkWjrc="/> <CustOpts><Param name="mantrakey" value="" /></CustOpts> </PidOptions>';
        
            
        }else if(type == "authentication"){
            
            var XML='<?php echo '<?xml version="1.0"?>'; ?> <PidOptions ver="1.0"> <Opts fCount="1" fType="2" iCount="0" pCount="0" pgCount="2" format="0"   pidVer="2.0" timeout="10000" pTimeout="20000" posh="UNKNOWN" env="P" wadh=""/> <CustOpts><Param name="mantrakey" value="" /></CustOpts> </PidOptions>';
            
            
        }else{
            if(device == "MANTRA_PROTOBUF"){
                var XML='<?php echo '<?xml version="1.0"?>'; ?> <PidOptions ver="1.0"> <Opts fCount="1" fType="2" iCount="0" pCount="0" pgCount="2" format="0"   pidVer="2.0" timeout="10000" pTimeout="20000" posh="UNKNOWN" env="P" wadh=""/> <CustOpts><Param name="mantrakey" value="" /></CustOpts> </PidOptions>';
            
                 
            }else{
                var XML='<PidOptions ver=\"1.0\">' + '<Opts fCount=\"1\" fType=\"2\" iCount=\"\" iType=\"\" pCount=\"\" pType=\"\" format=\"0\" pidVer=\"2.0\" timeout=\"10000\" otp=\"\" wadh=\"\" posh=\"\"/>' + '</PidOptions>'; 
            }
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
                    if(type == "authentication"){
                         $('[name="txtPidData"]').val(data);
                          $('#aepsAuthForm').submit();
                    }else if(type == "apauthentication"){
                         $('[name="mybiodata"]').val(data);
                          $('#apAuthForm').submit();
                    }else if(type == "kyc"){
                        $('[name="mybiodata"]').val(data);
                    }else{
                        $('[name="txtPidData"]').val(data);
                        $('#aepsTransactionForm').submit();
                    }
                    
                }else{
                    var errorInfo =  $(data).find('Resp').attr('errInfo');
                    var errorCode =  $(data).find('Resp').attr('errCode');
                    var mydata =  $(data).find('PidData').html();
                    if(errorCode == '0'){
                        notify("Fingerprint Captured Successfully", "success");
                        if(type == "authentication"){
                            $('[name="txtPidData"]').val("<PidData>"+mydata+"</PidData>");
                              $('#aepsAuthForm').submit();
                        }else if(type == "apauthentication"){
                            $('[name="mybiodata"]').val("<PidData>"+mydata+"</PidData>");
                              $('#apAuthForm').submit();
                        }else if(type == "kyc"){
                            $('[name="mybiodata"]').val("<PidData>"+mydata+"</PidData>");
                        }else{
                            $('[name="txtPidData"]').val("<PidData>"+mydata+"</PidData>");
                            $('#aepsTransactionForm').submit();
                        }
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
        
        if(type == "M"){
             @if(isset($isdoneapauth) && $isdoneapauth == 'false')
                $('#aptwostepauthmodal').modal();  
             @endif
        }
        $("#aepsTransactionForm" ).find('[name="transactionType"]').val(type)
    }
</script>
@endpush