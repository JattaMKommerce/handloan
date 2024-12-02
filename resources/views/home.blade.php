@extends('layouts.app')
@section('title', 'Dashboard')
@section('pagetitle', 'Dashboard')
@section('content')

<style>
   .bg-image {

      background-repeat: no-repeat;
      background-size: 100% 100%;
      margin-top: 5px;
      padding-left: 2px;
      color: rgb(39, 39, 133);
      box-shadow: 0px 0px 1px silver;
   }

   @media only screen and (max-width: 600px) {
      .bg-image {
         width: 30rem;
      }
   }

   #noticeModal p {
      margin-top: 0;
      margin-bottom: 0rem;
   }
</style>


<div id="loading">
   <div id="loading-center">
   </div>
</div>
<!-- loader END -->
<!-- Wrapper Start -->
<div class="wrapper">

   <!-- Page Content  -->

   <div class="row">

      <div class="col-lg-12">
         <div class="row mb-2">
            <div class="col-lg-8"></div>
            <div class="col-lg-4">
               <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                  <i class="fa fa-calendar"></i>&nbsp;
                  <span></span> <i class="fa fa-caret-down"></i>
               </div>
            </div>
         </div>

         <div class="row mb-2">

            <div class="col-lg-4 col-sm-6">
               <div class="iq-card iq-card-block iq-card-stretch iq-card-height batm_custom-card">
                  <div class="card-body">
                     <div class="product-container">
                        <div class="iq-bg-primary p-2 rounded p-50">
                           <span class="avatar-content">
                              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-package font-medium-4">
                                 <line x1="16.5" y1="9.4" x2="7.5" y2="4.21"></line>
                                 <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                 <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                                 <line x1="12" y1="22.08" x2="12" y2="12"></line>
                              </svg>
                           </span>
                        </div>

                        <div class="product-title">
                           <h5>AEPS</h5>
                        </div>
                     </div>
                     <div class="seperator"></div>
                     <div class="summary_amount">
                        <h5 class="fw-bolder mb-75 successTxn">
                           <span id="aeps_successCount"><span data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="140">0</span></span> | <span id="aeps_success"><span data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="₹485375">₹ 0</span></span>
                        </h5>
                        <h5 class="fw-bolder mb-75 pendingTxn">
                           <span id="aeps_pendingCount"><span data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="0">0</span></span> | <span id="aeps_pending"><span data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="₹0">₹ 0</span></span>
                        </h5>
                        <h5 class="fw-bolder mb-75 text-danger">
                           <span id="aeps_failedCount"><span data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="98">0</span></span> | <span id="aeps_failed"><span data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="₹290884">₹ 0</span></span>
                        </h5>
                     </div>
                  </div>
               </div>
            </div>


            <div class="col-lg-4 col-sm-6">
               <div class="iq-card iq-card-block iq-card-stretch iq-card-height batm_custom-card">
                  <div class="card-body">
                     <div class="product-container">
                        <div class="iq-bg-info p-2 rounded p-50">
                           <span class="avatar-content">
                              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-credit-card font-medium-4">
                                 <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                                 <line x1="1" y1="10" x2="23" y2="10"></line>
                              </svg>
                           </span>
                        </div>

                        <div class="product-title">
                           <h5>BBPS</h5>
                        </div>
                     </div>
                     <div class="seperator"></div>
                     <div class="summary_amount">
                        <h5 class="fw-bolder mb-75 successTxn">
                           <span id="bbps_successCount"><span data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="0">0</span></span> | <span id="bbps_success"><span data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="₹ 0">₹ 0</span></span>
                        </h5>
                        <h5 class="fw-bolder mb-75 pendingTxn">
                           <span id="bbps_pendingCount"><span data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="0">0</span></span> | <span id="bbps_pending"><span data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="₹ 0">₹ 0</span></span>
                        </h5>
                        <h5 class="fw-bolder mb-75 text-danger">
                           <span id="bbps_failedCount"><span data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="0">0</span></span> | <span id="bbps_failed"><span data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="₹ 0">₹ 0</span></span>
                        </h5>
                     </div>
                  </div>
               </div>
            </div>


            <div class="col-lg-4 col-sm-6">
               <div class="iq-card iq-card-block iq-card-stretch iq-card-height batm_custom-card">
                  <div class="card-body">
                     <div class="product-container">
                        <div class="iq-bg-danger p-2 rounded p-50">
                           <span class="avatar-content">
                              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-send font-medium-4">
                                 <line x1="22" y1="2" x2="11" y2="13"></line>
                                 <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                              </svg>
                           </span>
                        </div>

                        <div class="product-title">
                           <h5>DMT</h5>
                        </div>
                     </div>
                     <div class="seperator"></div>
                     <div class="summary_amount">
                        <h5 class="fw-bolder mb-75 successTxn">
                           <span id="money_successCount"><span data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="0">0</span></span> | <span id="money_success"><span data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="₹0">₹ 0</span></span>
                        </h5>
                        <h5 class="fw-bolder mb-75 pendingTxn">
                           <span id="money_pendingCount"><span data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="0">0</span></span> | <span id="money_pending"><span data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="₹0">₹ 0</span></span>
                        </h5>
                        <h5 class="fw-bolder mb-75 text-danger">
                           <span id="money_failedCount"><span data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="0">0</span></span> | <span id="money_failed"><span data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="₹0" aria-describedby="tooltip212659">₹ 0</span></span>
                        </h5>
                     </div>
                  </div>
               </div>
            </div>


            <div class="col-lg-4 col-sm-6">
               <div class="iq-card iq-card-block iq-card-stretch iq-card-height batm_custom-card">
                  <div class="card-body">
                     <div class="product-container">
                        <div class="iq-bg-success p-2 rounded p-50">
                           <span class="avatar-content">
                              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-smartphone font-medium-4">
                                 <rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect>
                                 <line x1="12" y1="18" x2="12.01" y2="18"></line>
                              </svg>
                           </span>
                        </div>

                        <div class="product-title">
                           <h5>Recharge</h5>
                        </div>
                     </div>
                     <div class="seperator"></div>
                     <div class="summary_amount">
                        <h5 class="fw-bolder mb-75 successTxn">
                           <span id="recharge_successCount"><span data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="0">0</span></span> | <span id="recharge_success"><span data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="₹0">₹ 0</span></span>
                        </h5>
                        <h5 class="fw-bolder mb-75 pendingTxn">
                           <span id="recharge_pendingCount"><span data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="0">0</span></span> | <span id="recharge_pending"><span data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="₹0">₹ 0</span></span>
                        </h5>
                        <h5 class="fw-bolder mb-75 text-danger">
                           <span id="recharge_failedCount"><span data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="0">0</span></span> | <span id="recharge_failed"><span data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="₹0">₹ 0</span></span>
                        </h5>
                     </div>
                  </div>
               </div>
            </div>

            <div class="col-lg-4 col-sm-6">
               <div class="iq-card iq-card-block iq-card-stretch iq-card-height batm_custom-card">
                  <div class="card-body">
                     <div class="product-container">
                        <div class="p-50 iq-bg-danger p-2 rounded">
                           <span class="avatar-content">
                              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-briefcase font-medium-4">
                                 <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                                 <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                              </svg>
                           </span>
                        </div>

                        <div class="product-title">
                           <h5>UTI PAN</h5>
                        </div>
                     </div>
                     <div class="seperator"></div>
                     <div class="summary_amount">
                        <h5 class="fw-bolder mb-75 successTxn">
                           <span id="utipancard_successCount"><span data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="0">0</span></span> | <span id="utipancard_success"><span data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="₹0">₹ 0</span></span>
                        </h5>
                        <h5 class="fw-bolder mb-75 pendingTxn">
                           <span id="utipancard_pendingCount"><span data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="0">0</span></span> | <span id="utipancard_pending"><span data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="₹0">₹ 0</span></span>
                        </h5>
                        <h5 class="fw-bolder mb-75 text-danger">
                           <span id="utipancard_failedCount"><span data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="0">0</span></span> | <span id="utipancard_failed"><span data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="₹1175449.3">₹ 0</span></span>
                        </h5>
                     </div>
                  </div>
               </div>
            </div>


            <div class="col-lg-4 col-sm-6">
               <div class="iq-card iq-card-block iq-card-stretch iq-card-height batm_custom-card">
                  <div class="card-body">
                     <div class="product-container">
                        <div class="iq-bg-primary p-2 rounded p-50">
                           <span class="avatar-content">
                              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-package font-medium-4">
                                 <line x1="16.5" y1="9.4" x2="7.5" y2="4.21"></line>
                                 <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                 <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                                 <line x1="12" y1="22.08" x2="12" y2="12"></line>
                              </svg>
                           </span>
                        </div>

                        <div class="product-title">
                           <h5>MATM</h5>
                        </div>
                     </div>
                     <div class="seperator"></div>
                     <div class="summary_amount">
                        <h5 class="fw-bolder mb-75 successTxn">
                           <span id="matm_successCount"><span data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="0">0</span></span> | <span id="matm_success"><span data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="₹0">₹ 0</span></span>
                        </h5>
                        <h5 class="fw-bolder mb-75 pendingTxn">
                           <span id="matm_pendingCount"><span data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="0">0</span></span> | <span id="matm_pending"><span data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="₹0">₹ 0</span></span>
                        </h5>
                        <h5 class="fw-bolder mb-75 text-danger">
                           <span id="matm_failedCount"><span data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="0">0</span></span> | <span id="matm_failed"><span data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="₹0">₹ 0</span></span>
                        </h5>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>

      <div class="col-sm-4">
         <div class="iq-card iq-card-block iq-card-stretch iq-card-height">
            <div class="iq-card-header d-flex justify-content-between">
               <div class="iq-header-title">
                  <h4 class="card-title">Balances</h4>
               </div>
            </div>
            <div class="iq-card-body">
               <ul class="suggestions-lists m-0 p-0">
                  <li class="d-flex mb-4 align-items-center">
                     <div class="profile-icon bg-primary"><span><i class="ri-wallet-line"></i></span></div>
                     <div class="media-support-info ml-3">
                        <h6>Main Wallet Balance</h6>
                     </div>
                     <div class="media-support-amount text-center ml-3">
                        <h6 class="text-info mainwallet">{{Auth::user()->mainwallet}}</h6>
                     </div>
                  </li>
                  <li class="d-flex mb-4 align-items-center">
                     <div class="profile-icon bg-primary"><span><i class="ri-wallet-line"></i></span></div>
                     <div class="media-support-info ml-3">
                        <h6>AePS Balance</h6>
                     </div>
                     <div class="media-support-amount text-center ml-3">
                        <h6 class="text-primary aepsbalance">{{Auth::user()->aepsbalance}}</h6>
                     </div>
                  </li>
                  <li class="d-flex mb-4 align-items-center">
                     <div class="profile-icon bg-primary"><span><i class="ri-paypal-line"></i></span></div>
                     <div class="media-support-info ml-3">
                        <h6>Investment Balance</h6>
                     </div>
                     <div class="media-support-amount text-center ml-3">
                        <h6 class="text-primary investmentbalance">{{Auth::user()->investment_wallet}}</h6>
                     </div>
                  </li>
                  @if (in_array(Auth::user()->role->slug, ['admin']))
                  <li class="d-flex mb-4 align-items-center">
                     <div class="profile-icon bg-primary"><span><i class="ri-arrow-down-circle-line"></i></span></div>
                     <div class="media-support-info ml-3">
                        <h6>Downline Balance</h6>
                     </div>
                     <div class="media-support-amount text-center ml-3">
                        <h6 class="text-primary downlinebalance">0</h6>
                     </div>
                  </li>
                  @endif

                  @if (in_array(Auth::user()->role->slug, ['admin']))
                  <li class="d-flex mb-4 align-items-center">
                     <div class="profile-icon bg-primary"><span><i class="ri-wallet-line"></i></span></div>
                     <div class="media-support-info ml-3">
                        <h6>API Balance</h6>
                     </div>
                     <div class="media-support-amount text-center ml-3">
                        <h6 class="text-primary apibalance">0</h6>
                     </div>
                  </li>
                  @endif

                  @if (in_array(Auth::user()->role->slug, ['admin']))
                  <li class="d-flex mb-4 align-items-center">
                     <div class="profile-icon bg-primary"><span><i class="ri-wallet-line"></i></span></div>
                     <div class="media-support-info ml-3">
                        <h6>Etrav API Balance</h6>
                     </div>
                     <div class="media-support-amount text-center ml-3">
                        <h6 class="text-primary flightBalance">{{Auth::user()->flightBalance}}</h6>
                     </div>
                  </li>
                  @endif
               </ul>
            </div>
         </div>
      </div>

      @if (in_array(Auth::user()->role->slug, ['whitelable', 'md', 'distributor', 'admin']))
      <div class="col-sm-4">
         <div class="iq-card iq-card-block iq-card-stretch iq-card-height">
            <div class="iq-card-header d-flex justify-content-between">
               <div class="iq-header-title">
                  <h4 class="card-title">User Counts</h4>
               </div>
            </div>
            <div class="iq-card-body">
               <ul class="suggestions-lists m-0 p-0">
                  @if (in_array(Auth::user()->role->slug, ['admin']))
                  <li class="d-flex mb-4 align-items-center">
                     <div class="profile-icon bg-primary"><span><i class="ri-refresh-line"></i></span></div>
                     <div class="media-support-info ml-3">
                        <h6>White Label</h6>
                     </div>
                     <div class="media-support-amount text-center ml-3">
                        <h6 class="text-info">{{$whitelable}}</h6>
                     </div>
                  </li>
                  @endif
                  @if (in_array(Auth::user()->role->slug, ['admin', 'whitelable']))
                  <li class="d-flex mb-4 align-items-center">
                     <div class="profile-icon bg-primary"><span><i class="ri-paypal-line"></i></span></div>
                     <div class="media-support-info ml-3">
                        <h6>Master Distributor</h6>
                     </div>
                     <div class="media-support-amount text-center ml-3">
                        <h6 class="text-primary">{{$md}}</h6>
                     </div>
                  </li>
                  @endif
                  @if (in_array(Auth::user()->role->slug, ['admin', 'whitelable', 'md']))
                  <li class="d-flex mb-4 align-items-center">
                     <div class="profile-icon bg-primary"><span><i class="ri-check-line"></i></span></div>
                     <div class="media-support-info ml-3">
                        <h6>Distributor</h6>
                     </div>
                     <div class="media-support-amount text-center ml-3">
                        <h6 class="text-primary">{{$distributor}}</h6>
                     </div>
                  </li>
                  @endif

                  @if (in_array(Auth::user()->role->slug, ['admin', 'whitelable', 'md', 'distributor']))
                  <li class="d-flex mb-4 align-items-center">
                     <div class="profile-icon bg-primary"><span><i class="ri-check-line"></i></span></div>
                     <div class="media-support-info ml-3">
                        <h6>Retailer</h6>
                     </div>
                     <div class="media-support-amount text-center ml-3">
                        <h6 class="text-primary">{{$retailer}}</h6>
                     </div>
                  </li>
                  @endif
               </ul>
            </div>
         </div>
      </div>
      @endif

      <div class="col-md-4">

         <div class="iq-card iq-card-block iq-card-stretch iq-card-height">

            <div class="iq-card-body text-center">
               <div>
                  <img src="https://www.livermoreschools.org/cms/lib/CA50000061/Centricity/Domain/72/HelpDesk%20White%20C.png" class="img-responsive mb-10" style="margin: auto; width: 200px">
               </div>

               <a href="#">
                  <img src="https://static.vecteezy.com/system/resources/previews/001/991/656/original/customer-service-flat-design-concept-illustration-icon-support-call-center-help-desk-hotline-operator-abstract-metaphor-can-use-for-landing-page-mobile-app-free-vector.jpg" class="img-responsive mb-10" style="margin: auto; width: 150px">
               </a>
               <div class="mt-1">
                  <b>Timing - 10 AM to 7 PM</b>
               </div>

               <div class="form-group mb-3">
                  <span class="text-primary text-semibold">
                     <h5><i class="fa fa-phone"></i></h5>{{$mydata['supportnumber']}}
                  </span>
               </div>

               <div class="form-group mb-3">

                  <span class="text-primary text-semibold">
                     <h5><i class="fa fa-envelope"></i></h5>{{$mydata['supportemail']}}
                  </span>
               </div>
            </div>
         </div>

      </div>

   </div>

   @if (Myhelper::hasNotRole('admin'))
   @if (Auth::user()->kyc == "pendinggg" || Auth::user()->kyc == "rejected")

   <div class="modal fade" id="kycModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="exampleModalLabel">Complete your profile with kyc</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
            </div>

            @if (Auth::user()->kyc == "rejected")
            <div class="alert text-white bg-danger" role="alert">
               <div class="iq-alert-text">Kyc Rejected! —{{ Auth::user()->remark }}</div>
               <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <i class="ri-close-line"></i>
               </button>
            </div>
            @endif

            <form id="kycForm" action="{{route('profileUpdate')}}" method="post" enctype="multipart/form-data">
               <div class="modal-body">
                  <input type="hidden" name="id" value="{{Auth::id()}}">
                  <input type="hidden" name="type" value="kycdata">
                  <input type="hidden" name="kyc" value="submitted">
                  {{ csrf_field() }}
                  <div class="row">
                     <div class="form-group col-md-12">
                        <label>Address</label>
                        <textarea name="address" class="form-control" rows="2" required="" placeholder="Enter Value">{{ Auth::user()->address}}</textarea>
                     </div>
                  </div>
                  <div class="row">
                     <div class="form-group col-md-4">
                        <label>State</label>
                        <select name="state" class="form-control select" required="">
                           <option value="">Select State</option>
                           @foreach ($state as $state)
                           <option value="{{$state->state}}" {{ (Auth::user()->state == $state->state)? 'selected=""': '' }}>{{$state->state}}</option>
                           @endforeach
                        </select>
                     </div>
                     <div class="form-group col-md-4">
                        <label>City</label>
                        <input type="text" name="city" class="form-control" required="" placeholder="Enter Value" value="{{Auth::user()->city}}">
                     </div>
                     <div class="form-group col-md-4">
                        <label>Pincode</label>
                        <input type="number" name="pincode" value="{{ Auth::user()->pincode}}" class="form-control" value="" required="" maxlength="6" minlength="6" placeholder="Enter Value">
                     </div>
                  </div>
                  <div class="row">
                     <div class="form-group col-md-4">
                        <label>Shop Name</label>
                        <input type="text" name="shopname" value="{{ Auth::user()->shopname}}" class="form-control" value="" required="" placeholder="Enter Value">
                     </div>

                     <div class="form-group col-md-4">
                        <label>Pancard Number</label>
                        <input type="text" name="pancard" value="{{ Auth::user()->pancard}}" class="form-control" value="" required="" placeholder="Enter Value">
                     </div>

                     <div class="form-group col-md-4">
                        <label>Adhaarcard Number</label>
                        <input type="text" name="aadharcard" value="{{ Auth::user()->aadharcard}}" class="form-control" value="" required="" placeholder="Enter Value" maxlength="12" minlength="12">
                     </div>
                  </div>
                  <div class="row">
                     <div class="form-group col-md-6">
                        <label>Pancard Pic</label>
                        <input type="file" name="pancardpics" class="form-control" value="" placeholder="Enter Value" required="">
                     </div>

                     <div class="form-group col-md-6">
                        <label>Adhaarcard Pic</label>
                        <input type="file" name="aadharcardpics" class="form-control" value="" placeholder="Enter Value" required="">
                     </div>
                  </div>
               </div>
               <div class="modal-footer">
                  <button class="btn btn-primary" type="submit" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Submitting">Complete Profile</button>
               </div>
            </form>
         </div>
      </div>
   </div>
   @endif

   @if (Auth::user()->resetpwd == "default")
   <div class="modal fade" id="pwdModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="exampleModalLabel">Change Password</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <form id="passwordForm" action="{{route('profileUpdate')}}" method="post">
               <div class="modal-body">
                  <input type="hidden" name="id" value="{{Auth::id()}}">
                  <input type="hidden" name="actiontype" value="password">
                  {{ csrf_field() }}

                  <div class="row">
                     <div class="form-group col-md-6  ">
                        <label>Old Password</label>
                        <input type="password" name="oldpassword" class="form-control" required="" placeholder="Enter Value">
                     </div>
                     <div class="form-group col-md-6  ">
                        <label>New Password</label>
                        <input type="password" name="password" id="password" class="form-control" required="" placeholder="Enter Value">
                     </div>
                  </div>
                  <div class="row">
                     <div class="form-group col-md-6  ">
                        <label>Confirmed Password</label>
                        <input type="password" name="password_confirmation" class="form-control" required="" placeholder="Enter Value">
                     </div>
                  </div>

               </div>
               <div class="modal-footer">
                  <button class="btn btn-primary" type="submit" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Submitting">Change Password</button>
               </div>
            </form>
         </div>
      </div>
   </div>
   @endif
   @endif

   <div class="modal fade bd-example-modal-xl" id="noticeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-xl" role="document">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="exampleModalLabel">Investment Scheme</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <div class="modal-body">
               {!! nl2br($mydata['notice']) !!}

              
               </div>
               <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

               </div>

            </div>
         </div>


         <div class="modal fade" id="fundRequestModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
               <div class="modal-content">
                  <div class="modal-header">
                     <h5 class="modal-title" id="exampleModalLabel">Wallet Fund Request</h5>
                     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                     </button>
                  </div>
                  <form id="invfundRequestForm" action="{{route('invfundtransaction')}}" method="post">
                     <div class="modal-body">
                        <input type="hidden" name="user_id">
                        <input type="hidden" name="type" value="request">
                        <input type="hidden" name="investment_title" value="">
                        <input type="hidden" name="scheme_id" value="">
                        <input type="hidden" name="investAmount" value="">

                        {{ csrf_field() }}
                        <div class="row">
                           <div class="form-group col-md-6">
                              <label>Deposit Bank</label>
                              <select name="fundbank_id" class="form-control" id="select" required>
                                 <option value="">Select Bank</option>
                                 @foreach ($banks as $bank)
                                 <option value="{{$bank->id}}">{{$bank->name}} ( {{$bank->account}} )</option>
                                 @endforeach
                              </select>
                           </div>
                           <div class="form-group col-md-6">
                              <label>No Of shares</label>
                              <input type="number" name="numberOfshares"  onkeyup="calculateAmount()" class="form-control"  placeholder="Enter share" required="">
                             
                           </div>
                           <div class="form-group col-md-6">
                              <label>Amount</label>
                              <input type="number" name="amount" step="any" class="form-control" placeholder="Enter Amount" required="">
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
                           <div class="form-group col-md-6">
                              <label>Pay Date</label>
                              <input type="text" name="paydate" class="form-control mydate" placeholder="Select date">
                           </div>
                           <div class="form-group col-md-6">
                              <label>Ref No.</label>
                              <input type="text" name="ref_no" class="form-control" placeholder="Enter Refrence Number" required="">
                           </div>
                           <div class="form-group col-md-6">
                              <label>Pay Slip</label>
                              <input type="file" name="payslips" class="form-control">
                           </div>
                           <div class="form-group col-md-12">
                              <label>Remark</label>
                              <textarea name="remark" class="form-control" rows="2" placeholder="Enter Remark"></textarea>
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
         <script type="text/javascript" src="{{asset('')}}assets/js/plugins/forms/selects/select2.min.js"></script>
         <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
         <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
         <script>
            $(document).ready(function() {
               @if($mydata['notice'] == '')
               $('#noticeModal').modal('hide');
               @if(\Myhelper::can('invesment_show') && !Myhelper::hasRole('admin'))
               $('#noticeModal').modal('show');
               @endif
               @else
               $('#noticeModal').modal('show');
               @endif

               @if(Myhelper::hasNotRole('admin'))
               @if(Auth::user() -> kyc == "pending" || Auth::user() -> kyc == "rejected")
               $('#kycModal').modal();
               @endif
               @endif

               @if(Myhelper::hasNotRole('admin') && Auth::user() -> resetpwd == "default")
               $('#pwdModal').modal();
               @endif

               $("#searchbydate").validate({
                  rules: {
                     fromdate: {
                        required: true,
                     },
                     todate: {
                        required: true,
                     }
                  },
                  messages: {
                     fromdate: {
                        required: "Please select fromdate",
                     },
                     todate: {
                        required: "Please select fromdate",
                     },
                  },
                  errorElement: "p",
                  errorPlacement: function(error, element) {
                     if (element.prop("tagName").toLowerCase().toLowerCase() === "select") {
                        error.insertAfter(element.closest(".form-group").find(".select2"));
                     } else {
                        error.insertAfter(element);
                     }
                  },
                  submitHandler: function() {
                     var form = $('form#searchbydate');
                     form.find('span.text-danger').remove();
                     form.ajaxSubmit({
                        dataType: 'json',
                        beforeSubmit: function() {
                           form.find('button:submit').button('loading');
                        },
                        complete: function() {
                           form.find('button:submit').button('reset');
                        },
                        success: function(data) {

                           $.each(data, function(index, value) {
                              $('.' + index).text(value);
                           });
                        },
                        error: function(errors) {
                           showError(errors, form.find('.modal-body'));
                        }
                     });
                  }
               });

               $("#kycForm").validate({
                  rules: {
                     state: {
                        required: true,
                     },
                     city: {
                        required: true,
                     },
                     pincode: {
                        required: true,
                        minlength: 6,
                        number: true,
                        maxlength: 6
                     },
                     address: {
                        required: true,
                     },
                     aadharcard: {
                        required: true,
                        minlength: 12,
                        number: true,
                        maxlength: 12
                     },
                     pancard: {
                        required: true,
                     },
                     shopname: {
                        required: true,
                     },
                     pancardpics: {
                        required: true,
                     },
                     aadharcardpics: {
                        required: true,
                     }
                  },
                  messages: {
                     state: {
                        required: "Please select state",
                     },
                     city: {
                        required: "Please enter city",
                     },
                     pincode: {
                        required: "Please enter pincode",
                        number: "Mobile number should be numeric",
                        minlength: "Your mobile number must be 6 digit",
                        maxlength: "Your mobile number must be 6 digit"
                     },
                     address: {
                        required: "Please enter address",
                     },
                     aadharcard: {
                        required: "Please enter aadharcard",
                        number: "Mobile number should be numeric",
                        minlength: "Your mobile number must be 12 digit",
                        maxlength: "Your mobile number must be 12 digit"
                     },
                     pancard: {
                        required: "Please enter pancard",
                     },
                     shopname: {
                        required: "Please enter shop name",
                     },
                     pancardpics: {
                        required: "Please upload pancard pic",
                     },
                     aadharcardpics: {
                        required: "Please upload aadharcard pic",
                     }
                  },
                  errorElement: "p",
                  errorPlacement: function(error, element) {
                     if (element.prop("tagName").toLowerCase().toLowerCase() === "select") {
                        error.insertAfter(element.closest(".form-group").find(".select2"));
                     } else {
                        error.insertAfter(element);
                     }
                  },
                  submitHandler: function() {
                     var form = $("#kycForm");
                     form.find('span.text-danger').remove();
                     form.ajaxSubmit({
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
                              notify("Profile Successfully Updated, wait for kyc approval", 'success');
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

               $("#passwordForm").validate({
                  rules: {
                     @if(!Myhelper::can('member_password_reset'))
                     oldpassword: {
                        required: true,
                        minlength: 6,
                     },
                     password_confirmation: {
                        required: true,
                        minlength: 8,
                        equalTo: "#password"
                     },
                     @endif
                     password: {
                        required: true,
                        minlength: 8
                     }
                  },
                  messages: {
                     @if(!Myhelper::can('member_password_reset'))
                     oldpassword: {
                        required: "Please enter old password",
                        minlength: "Your password lenght should be atleast 6 character",
                     },
                     password_confirmation: {
                        required: "Please enter confirmed password",
                        minlength: "Your password lenght should be atleast 8 character",
                        equalTo: "New password and confirmed password should be equal"
                     },
                     @endif
                     password: {
                        required: "Please enter new password",
                        minlength: "Your password lenght should be atleast 8 character"
                     }
                  },
                  errorElement: "p",
                  errorPlacement: function(error, element) {
                     if (element.prop("tagName").toLowerCase().toLowerCase() === "select") {
                        error.insertAfter(element.closest(".form-group").find(".select2"));
                     } else {
                        error.insertAfter(element);
                     }
                  },
                  submitHandler: function() {
                     var form = $('form#passwordForm');
                     form.find('span.text-danger').remove();
                     form.ajaxSubmit({
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
                              form.closest('.modal').modal('hide');
                              notify("Password Successfully Changed", 'success');
                           } else {
                              notify(data.status, 'warning');
                           }
                        },
                        error: function(errors) {
                           showError(errors, form.find('.modal-body'));
                        }
                     });
                  }
               });
            });

            const getDashboardData = (start, end) => {

               $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));

               $.ajax({
                  url: "{{route('home')}}",
                  type: "POST",
                  headers: {
                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  },
                  data: {

                     fromDate: start?.format('YYYY-MM-DD') || '',
                     toDate: end.format('YYYY-MM-DD') || '',
                  },
                  success: function(resp) {

                     $(`#aeps_success`).html(`<span data-bs-toggle="tooltip" data-bs-placement="top" title="">` + '₹' + resp.aeps.success.toFixed(2) + `</span>`);
                     $(`#aeps_successCount`).html(`<span data-bs-toggle="tooltip" data-bs-placement="top" title="">` + resp.aeps.successCount + `</span>`);
                     $(`#aeps_pending`).html(`<span data-bs-toggle="tooltip" data-bs-placement="top" title="">` + '₹' + resp.aeps.pending.toFixed(2) + `</span>`);
                     $(`#aeps_pendingCount`).html(`<span data-bs-toggle="tooltip" data-bs-placement="top" title="">` + resp.aeps.pendingCount + `</span>`);
                     $(`#aeps_failed`).html(`<span data-bs-toggle="tooltip" data-bs-placement="top" title="">` + '₹' + resp.aeps.failed.toFixed(2) + `</span>`);
                     $(`#aeps_failedCount`).html(`<span data-bs-toggle="tooltip" data-bs-placement="top" title="">` + resp.aeps.failedCount + `</span>`);

                     $(`#bbps_success`).html(`<span data-bs-toggle="tooltip" data-bs-placement="top" title="">` + '₹' + resp.billpayment.success.toFixed(2) + `</span>`);
                     $(`#bbps_successCount`).html(`<span data-bs-toggle="tooltip" data-bs-placement="top" title="">` + resp.billpayment.successCount + `</span>`);
                     $(`#bbps_pending`).html(`<span data-bs-toggle="tooltip" data-bs-placement="top" title="">` + '₹' + resp.billpayment.pending.toFixed(2) + `</span>`);
                     $(`#bbps_pendingCount`).html(`<span data-bs-toggle="tooltip" data-bs-placement="top" title="">` + resp.billpayment.pendingCount + `</span>`);
                     $(`#bbps_failed`).html(`<span data-bs-toggle="tooltip" data-bs-placement="top" title="">` + '₹' + resp.billpayment.failed.toFixed(2) + `</span>`);
                     $(`#bbps_failedCount`).html(`<span data-bs-toggle="tooltip" data-bs-placement="top" title="">` + resp.billpayment.failedCount + `</span>`);

                     $(`#money_success`).html(`<span data-bs-toggle="tooltip" data-bs-placement="top" title="">` + '₹' + resp.dmt.success.toFixed(2) + `</span>`);
                     $(`#money_successCount`).html(`<span data-bs-toggle="tooltip" data-bs-placement="top" title="">` + resp.dmt.successCount + `</span>`);
                     $(`#money_pending`).html(`<span data-bs-toggle="tooltip" data-bs-placement="top" title="">` + '₹' + resp.dmt.pending.toFixed(2) + `</span>`);
                     $(`#money_pendingCount`).html(`<span data-bs-toggle="tooltip" data-bs-placement="top" title="">` + resp.dmt.pendingCount + `</span>`);
                     $(`#money_failed`).html(`<span data-bs-toggle="tooltip" data-bs-placement="top" title="">` + '₹' + resp.dmt.failed.toFixed(2) + `</span>`);
                     $(`#money_failedCount`).html(`<span data-bs-toggle="tooltip" data-bs-placement="top" title="">` + resp.dmt.failedCount + `</span>`);

                     $(`#matm_success`).html(`<span data-bs-toggle="tooltip" data-bs-placement="top" title="">` + '₹' + resp.matm.success.toFixed(2) + `</span>`);
                     $(`#matm_successCount`).html(`<span data-bs-toggle="tooltip" data-bs-placement="top" title="">` + resp.matm.successCount + `</span>`);
                     $(`#matm_pending`).html(`<span data-bs-toggle="tooltip" data-bs-placement="top" title="">` + '₹' + resp.matm.pending.toFixed(2) + `</span>`);
                     $(`#matm_pendingCount`).html(`<span data-bs-toggle="tooltip" data-bs-placement="top" title="">` + resp.matm.pendingCount + `</span>`);
                     $(`#matm_failed`).html(`<span data-bs-toggle="tooltip" data-bs-placement="top" title="">` + '₹' + resp.matm.failed.toFixed(2) + `</span>`);
                     $(`#matm_failedCount`).html(`<span data-bs-toggle="tooltip" data-bs-placement="top" title="">` + resp.matm.failedCount + `</span>`);

                     $(`#recharge_success`).html(`<span data-bs-toggle="tooltip" data-bs-placement="top" title="">` + '₹' + resp.recharge.success.toFixed(2) + `</span>`);
                     $(`#recharge_successCount`).html(`<span data-bs-toggle="tooltip" data-bs-placement="top" title="">` + resp.recharge.successCount + `</span>`);
                     $(`#recharge_pending`).html(`<span data-bs-toggle="tooltip" data-bs-placement="top" title="">` + '₹' + resp.recharge.pending.toFixed(2) + `</span>`);
                     $(`#recharge_pendingCount`).html(`<span data-bs-toggle="tooltip" data-bs-placement="top" title="">` + resp.recharge.pendingCount + `</span>`);
                     $(`#recharge_failed`).html(`<span data-bs-toggle="tooltip" data-bs-placement="top" title="">` + '₹' + resp.recharge.failed.toFixed(2) + `</span>`);
                     $(`#recharge_failedCount`).html(`<span data-bs-toggle="tooltip" data-bs-placement="top" title="">` + resp.recharge.failedCount + `</span>`);

                     $(`#utipancard_success`).html(`<span data-bs-toggle="tooltip" data-bs-placement="top" title="">` + '₹' + resp.utipancard.success.toFixed(2) + `</span>`);
                     $(`#utipancard_successCount`).html(`<span data-bs-toggle="tooltip" data-bs-placement="top" title="">` + resp.utipancard.successCount + `</span>`);
                     $(`#utipancard_pending`).html(`<span data-bs-toggle="tooltip" data-bs-placement="top" title="">` + '₹' + resp.utipancard.pending.toFixed(2) + `</span>`);
                     $(`#utipancard_pendingCount`).html(`<span data-bs-toggle="tooltip" data-bs-placement="top" title="">` + resp.utipancard.pendingCount + `</span>`);
                     $(`#utipancard_failed`).html(`<span data-bs-toggle="tooltip" data-bs-placement="top" title="">` + '₹' + resp.utipancard.failed.toFixed(2) + `</span>`);
                     $(`#utipancard_failedCount`).html(`<span data-bs-toggle="tooltip" data-bs-placement="top" title="">` + resp.utipancard.failedCount + `</span>`);

                  }
               });

            }

            $(function() {

               var start = moment();
               var end = moment();

               $('#reportrange').daterangepicker({
                  startDate: start,
                  endDate: end,
                  ranges: {
                     'Today': [moment(), moment()],
                     'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                     'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                     'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                     'This Month': [moment().startOf('month'), moment().endOf('month')],
                     'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                  }
               }, getDashboardData);

               getDashboardData(start, end);

            });

            function buyInves(id) {
               $.ajax({
                     url: '{{route("investNow")}}',
                     type: 'post',
                     dataType: 'json',
                     headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                     },
                     data: {
                        "investment_id": id
                     },
                     beforeSend: function() {
                        swal({
                           title: 'Wait!',
                           text: 'Please wait, we are status change',
                           onOpen: () => {
                              swal.showLoading()
                           },
                           allowOutsideClick: () => !swal.isLoading()
                        });
                     }
                  })
                  .success(function(data) {
                     swal.close();
                     console.log(id);
                     if (data.errors != undefined) {
                        $('#errormessage').text(data.errors[0]);
                     }
                     $('.investcls-' + id).removeAttr('onclick');

                     $('.investcls-' + id).removeClass('btn-warning').addClass('btn-success');
                     $('.investcls-' + id).text('Invested');
                     // notify("Banner status changed successfully", 'success');
                  })
                  .fail(function(error) {
                     swal.close();

                     notify('Somthing went wrong', 'warning');
                  });
            }


            $(document).ready(function() {
               $("#invfundRequestForm").validate({
                  rules: {

                     amount: {
                        required: true
                     },
                     paymode: {
                        required: true
                     },
                     ref_no: {
                        required: true
                     },
                     title: {
                        required: true
                     },
                     payslips: {
                        required: true
                     }
                  },
                  messages: {

                     amount: {
                        required: "Please enter request amount",
                     },
                     paymode: {
                        required: "Please select payment mode",
                     },
                     ref_no: {
                        required: "Please enter transaction refrence number",
                     },
                     title: {
                        required: "Please enter title",
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
                     var form = $('#invfundRequestForm');
                     form.ajaxSubmit({
                        dataType: 'json',
                        beforeSubmit: function() {
                           form.find('button:submit').html('loading').attr('disabled', true).addClass('btn-secondary');
                        },
                        complete: function() {
                           form.find('button:submit').html('Submit').attr('disabled', false).removeClass('btn-secondary');
                        },
                        success: function(data) {
                           if (data.status == "success") {
                              form.closest('.modal').modal('hide');
                              notify("Fund Request submitted Successfull", 'success');
                              //  $('#datatable').dataTable().api().ajax.reload();
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
            })

            function investmentModel(value) {

               value = JSON.parse(value);
               console.log(value)

               $('#fundRequestModal').find('[name="investment_title"]').val(value.title);
               $('#fundRequestModal').find('[name="investAmount"]').val(value.amount);
               $('#fundRequestModal').find('[name="scheme_id"]').val(value.investment_id);
               $('#fundRequestModal').modal('show');
            }


            function calculateAmount() {

               const amount = $(`[name="investAmount"]`).val();
               const share = $(`[name="numberOfshares"]`).val();
               const totAmt = (amount * share).toFixed(2);
               $('#fundRequestModal').find('[name="amount"]').val(totAmt);
               console.log(`${amount},${share},${amount*share}`)
            }
         </script>


         @endpush