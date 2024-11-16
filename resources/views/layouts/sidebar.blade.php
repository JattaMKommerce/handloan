<div class="iq-top-navbar">
   <div class="iq-navbar-custom d-flex align-items-center justify-content-between">
      <div class="iq-menu-bt d-flex align-items-center">
         <div id="sidebarhide" class="wrapper-menu">
            <div class="main-circle"><i class="ri-menu-line"></i></div>
            <div class="hover-circle"><i class="ri-close-fill"></i></div>
         </div>
         <div class="iq-navbar-logo d-flex justify-content-between ml-3">
            <a href="index.html" class="header-logo">
               <img src="images/logo.png" class="img-fluid rounded" alt="">
               <span>FinDash</span>
            </a>
         </div>
      </div>
      <div class="iq-menu-horizontal">
         <nav class="iq-sidebar-menu">
            <ul id="iq-sidebar-toggle" class="iq-menu d-flex">

               @if(Auth::user()->kyc == "verified")
               <li class="active">
                  <a href="{{route('home')}}" class="iq-waves-effect"><span class="ripple rippleEffect"></span><i class="las la-home"></i><span>Dashboard</span></a>
               </li>

               @if (Myhelper::hasNotRole('admin'))
               @if (Myhelper::can(['recharge_service']))
               <!--<li aria-expanded="true">-->
               <!--   <a href="javascript:void(0)" class="iq-waves-effect" data-toggle="collapse" aria-expanded="false"><i class="ri-menu-3-line"></i><span>Utility Recharge</span><i class="ri-arrow-right-s-line iq-arrow-right"></i></a>-->

               <!--   <ul id="menu-design" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">-->
               <!--      @if (Myhelper::can('recharge_service'))-->
               <!--      <li><a href="{{route('recharge' , ['type' => 'mobile'])}}"><i class="ri-git-commit-line"></i>Mobile</a></li>-->
               <!--      <li><a href="{{route('recharge' , ['type' => 'dth'])}}"><i class="ri-text-spacing"></i>DTH</a></li>-->
               <!--      @endif-->
               <!--   </ul>-->
               <!--</li>-->
               @endif

               <!--<li>-->
               <!--   <a href="#Services" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="ri-record-circle-line"></i><span>Services</span><i class="ri-arrow-right-s-line iq-arrow-right"></i></a>-->
               <!--   <ul id="Services" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">-->
               <!--      @if (Myhelper::can(['billpayment_service']))-->
               <!--      <li>-->
               <!--         <a href="#userinfo" class="iq-waves-effect" data-toggle="collapse" aria-expanded="false"><span class="ripple rippleEffect"></span><i class="las la-user-tie"></i><span>Bill Payment</span><i class="ri-arrow-right-s-line iq-arrow-right"></i></a>-->
               <!--         <ul id="userinfo" class="iq-submenu collapse iq-submenu-data">-->
               <!--            @if (Myhelper::can('billpayment_service'))-->
               <!--            <li><a href="{{route('bill' , ['type' => 'electricity'])}}"><i class="las la-id-card-alt"></i>Electricity</a></li>-->
               <!--            <li><a href="{{route('bill' , ['type' => 'postpaid'])}}"><i class="las la-id-card-alt"></i>Postpaid</a></li>-->
               <!--            <li><a href="{{route('bill' , ['type' => 'water'])}}"><i class="las la-id-card-alt"></i>Water</a></li>-->
               <!--            <li><a href="{{route('bill' , ['type' => 'broadband'])}}"><i class="las la-id-card-alt"></i>Broadband</a></li>-->
               <!--            <li><a href="{{route('bill' , ['type' => 'lpg'])}}"><i class="las la-id-card-alt"></i>LPG Gas </a></li>-->
               <!--            <li><a href="{{route('bill' , ['type' => 'gas'])}}"><i class="las la-id-card-alt"></i>Piped Gas</a></li>-->
               <!--            <li><a href="{{route('bill' , ['type' => 'landline'])}}"><i class="las la-id-card-alt"></i>Landline</a></li>-->
                           <!--<li><a href="{{route('bill' , ['type' => 'postpaid'])}}">Postpaid</a></li>-->
               <!--            <li><a href="{{route('bill' , ['type' => 'schoolfees'])}}"><i class="las la-id-card-alt"></i>Education Fees</a></li>-->
               <!--            @endif-->
               <!--         </ul>-->
               <!--      </li>-->
               <!--      @endif-->

               <!--      @if (Myhelper::can(['licbillpay_service']))-->
               <!--      <li>-->
               <!--         <a href="{{route('lic')}}" class="iq-waves-effect"><i class="las la-calendar"></i><span>LIC Billpay</span></a>-->
               <!--      </li>-->
               <!--      @endif-->
               <!--      @if (Myhelper::can(['billpayment_service']))-->
               <!--      <li>-->
               <!--         <a href="{{route('bill' , ['type' => 'fasttag'])}}" class="iq-waves-effect"><i class="las la-calendar"></i><span>FASTag</span></a>-->
               <!--      </li>-->
               <!--      @endif-->

               <!--      <li>-->
               <!--         <a href="#finance" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="las la-mail-bulk"></i><span>Financial & Taxes</span><i class="ri-arrow-right-s-line iq-arrow-right"></i></a>-->
               <!--         <ul id="finance" class="iq-submenu collapse iq-submenu-data">-->
               <!--            @if (Myhelper::can('billpayment_service'))-->
               <!--            <li><a href="{{route('bill' , ['type' => 'loanrepay'])}}"><i class="las la-inbox"></i>Loan Repayment</a></li>-->
               <!--            <li><a href="{{route('bill' , ['type' => 'insurance'])}}"><i class="ri-mail-send-line"></i>LIC/Insurance</a></li>-->
               <!--            <li><a href="{{route('bill' , ['type' => 'muncipal'])}}"><i class="las la-inbox"></i>Municipal Tax</a></li>-->
               <!--            <li><a href="{{route('bill' , ['type' => 'housing'])}}"><i class="ri-mail-send-line"></i>Housing Tax</a></li>-->
               <!--            @endif-->
               <!--         </ul>-->
               <!--      </li>-->

               <!--      @if (Myhelper::can(['utipancard_service', 'nsdl_service']))-->
               <!--      <li>-->
               <!--         <a href="#utiPan" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="lab la-elementor"></i><span>PAN Card</span><i class="ri-arrow-right-s-line iq-arrow-right"></i></a>-->
               <!--         <ul id="utiPan" class="iq-submenu collapse iq-submenu-data">-->
               <!--            @if (Myhelper::can('utipancard_service'))-->
               <!--            <li><a href="{{route('spanacard')}}"><i class="las la-palette"></i>UTI</a></li>-->
               <!--            @endif-->
               <!--         </ul>-->
               <!--      </li>-->
               <!--      @endif-->

               <!--      @if (Myhelper::can(['dmt1_service', 'aeps_service']))-->
               <!--      <li>-->
               <!--         <a href="#bankingService" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="lab la-wpforms"></i><span>Banking Service</span><i class="ri-arrow-right-s-line iq-arrow-right"></i></a>-->
               <!--         <ul id="bankingService" class="iq-submenu collapse iq-submenu-data">-->

               <!--            @if (Myhelper::can('dmt1_service'))-->
               <!--            <li><a href="{{route('dmt1')}}"><i class="las la-book"></i>DMT</a></li>-->
               <!--            @endif-->

               <!--            @if (Myhelper::can('aeps_service'))-->
               <!--            <li><a href="{{route('aeps')}}"><i class="las la-edit"></i>AEPS</a></li>-->
               <!--            @endif-->

               <!--         </ul>-->
               <!--      </li>-->
               <!--      @endif-->
               <!--   </ul>-->
               <!--</li>-->


               <!--<li>-->
               <!--   <a href="#serviceLink" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="ri-archive-drawer-line"></i><span>Service Links</span><i class="ri-arrow-right-s-line iq-arrow-right"></i></a>-->
               <!--   <ul id="serviceLink" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">-->
               <!--      @if(sizeof($mydata['links']) > 0)-->
               <!--      @foreach($mydata['links'] as $link)-->
               <!--      <li><a href="{{$link->value}}" target="_blank"><i class="ri-clockwise-line"></i>{{$link->name}}</a></li>-->
               <!--      @endforeach-->
               <!--      @endif-->
               <!--   </ul>-->
               <!--</li>-->

               @endif

               @if ((Myhelper::can(['company_manager', 'change_company_profile'])) || (Myhelper::hasNotRole('retailer') && isset($mydata['schememanager']) && $mydata['schememanager']->value == "all"))
               <li>
                  <a href="#tables" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="ri-table-line "></i><span>Resources</span><i class="ri-arrow-right-s-line iq-arrow-right"></i></a>
                  <ul id="tables" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                     @if (Myhelper::hasNotRole('retailer') && isset($mydata['schememanager']) && $mydata['schememanager']->value == "all")
                     <li><a href="{{route('resource', ['type' => 'package'])}}"><i class="ri-table-line"></i>Scheme Manager</a></li>
                     @elseif (Myhelper::hasRole('admin'))
                     <li><a href="{{route('resource', ['type' => 'scheme'])}}"><i class="ri-database-line"></i>Scheme Manager</a></li>
                     @endif
                     @if (Myhelper::can('company_manager'))
                     <li><a href="{{route('resource', ['type' => 'company'])}}"><i class="ri-refund-line"></i>Company Manager</a></li>
                     @endif

                     @if (Myhelper::can('change_company_profile'))
                     <li><a href="{{route('resource', ['type' => 'companyprofile'])}}"><i class="ri-refund-line"></i>Company Profile</a></li>
                     @endif

                  </ul>
               </li>
               @endif

               @if (Myhelper::can(['view_whitelable', 'view_md', 'view_distributor', 'view_retailer', 'view_apiuser', 'view_other', 'view_kycpending', 'view_kycsubmitted', 'view_kycrejected']))

               <li>
                  <a href="#member" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="ri-pie-chart-box-line "></i><span>Member</span><i class="ri-arrow-right-s-line iq-arrow-right"></i></a>
                  <ul id="member" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">

                     @if (Myhelper::can(['view_whitelable']))
                     <li><a href="{{route('member', ['type' => 'whitelable'])}}"><i class="ri-file-chart-line"></i>Whitelabel</a></li>
                     @endif
                     @if (Myhelper::can(['view_md']))
                     <li><a href="{{route('member', ['type' => 'md'])}}"><i class="ri-bar-chart-line"></i>Master Distributor</a></li>
                     @endif
                     @if (Myhelper::can(['view_distributor']))
                     <li><a href="{{route('member', ['type' => 'distributor'])}}"><i class="ri-bar-chart-line"></i>Distributor</a></li>
                     @endif
                     @if (Myhelper::can(['view_retailer']))
                     <li><a href="{{route('member', ['type' => 'retailer'])}}"><i class="ri-bar-chart-line"></i>Retailer</a></li>
                     @endif
                     @if (Myhelper::hasRole('admin') || Myhelper::hasRole('subadmin'))
                     <li><a href="{{route('member', ['type' => 'web'])}}"><i class="ri-bar-chart-line"></i>All Member</a></li>
                     @endif
                     @if (Myhelper::hasRole('admin') || Myhelper::hasRole('subadmin'))
                     <li><a href="{{route('member', ['type' => 'kycsubmitted'])}}"><i class="ri-bar-chart-line"></i>Kycsubmited User</a></li>
                     @endif
                     @if (Myhelper::hasRole('admin') || Myhelper::hasRole('subadmin'))
                     <li><a href="{{route('member', ['type' => 'kycrejected'])}}"><i class="ri-bar-chart-line"></i>Kyc Rejected User</a></li>
                     @endif
                     @if (Myhelper::hasRole('admin') || Myhelper::hasRole('subadmin'))
                     <li><a href="{{route('member', ['type' => 'kycpending'])}}"><i class="ri-bar-chart-line"></i>Kyc Pending User</a></li>
                     @endif
                     @if (Myhelper::can(['view_other']))
                     <li><a href="{{route('member', ['type' => 'other'])}}"><i class="ri-bar-chart-line"></i>Other User</a></li>
                     @endif
                     @if (Myhelper::can(['view_employee']))
                     <li><a href="{{route('member', ['type' => 'employee'])}}"><i class="ri-bar-chart-line"></i>Employee</a></li>
                     @endif
                  </ul>
               </li>
               @endif


               <li>
                  <a href="#FundDetails" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="ri-record-circle-line"></i><span>Fund Details</span><i class="ri-arrow-right-s-line iq-arrow-right"></i></a>
                  <ul id="FundDetails" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                     @if (Myhelper::can(['fund_transfer', 'fund_return', 'fund_request_view', 'fund_report', 'fund_request']))
                     <li>
                        <a href="#funds" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="ri-list-check "></i><span>Fund</span><i class="ri-arrow-right-s-line iq-arrow-right"></i></a>
                        <!-- <span class="label bg-danger fundCount {{Myhelper::hasRole('admin')?'' : 'hide'}}">0</span> -->

                        <ul id="funds" class="iq-submenu collapse iq-submenu-data">
                           @if (Myhelper::can(['fund_transfer', 'fund_return']))
                           <li><a href="{{route('fund', ['type' => 'tr'])}}"><i class="ri-stack-line"></i>Transfer/Return</a></li>
                           @endif

                           <!--@if (Myhelper::can(['runpaisa_service']))-->
                           <!--<li><a href="{{route('fund', ['type' => 'runpaisapg'])}}"><i class="ri-stack-line"></i>RUN Paisa PG</a></li>-->
                           <!--@endif-->

                           @if (Myhelper::can(['setup_bank']))
                           <li><a href="{{route('fund', ['type' => 'requestview'])}}"><i class="ri-facebook-fill"></i>Request</a></li>
                           @endif

                           @if (Myhelper::hasNotRole('admin') && Myhelper::can('fund_request'))
                           <li><a href="{{route('fund', ['type' => 'request'])}}"><i class="ri-stack-line"></i>Load Main Wallet</a></li>
                           @endif

                           @if (Myhelper::can(['fund_report']))
                           <li><a href="{{route('fund', ['type' => 'requestviewall'])}}"><i class="ri-stack-line"></i>Request Report</a></li>
                           <li><a href="{{route('fund', ['type' => 'statement'])}}"><i class="ri-stack-line"></i>All Fund Report</a></li>
                           @endif
                        </ul>
                     </li>
                     @endif

                     @if (Myhelper::can(['aeps_fund_request', 'aeps_fund_view', 'aeps_fund_report']))
                     <!--<li>-->
                     <!--   <a href="#aepsfund" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="ri-pages-line "></i><span>AEPS Fund</span><i class="ri-arrow-right-s-line iq-arrow-right"></i></a>-->
                        <!-- <span class="label bg-danger aepsfundCount {{Myhelper::hasRole('admin')?'' : 'hide'}}">0</span> -->
                     <!--   <ul id="aepsfund" class="iq-submenu collapse iq-submenu-data">-->

                     <!--      @if (Myhelper::can(['aeps_fund_request']) && Myhelper::hasNotRole('admin'))-->
                     <!--      <li><a href="{{route('fund', ['type' => 'aeps'])}}"><i class="las la-sign-in-alt"></i>Request</a></li>-->
                     <!--      {{-- <li><a href="{{route('fund', ['type' => 'addaccount'])}}"><i class="las la-sign-in-alt"></i>Add Bank</a></li> --}}-->
                     <!--      @endif-->

                     <!--      @if (Myhelper::can(['aeps_fund_view']))-->
                     <!--      <li><a href="{{route('fund', ['type' => 'aepsrequest'])}}"><i class="las la-sign-in-alt"></i>Pending Request</a>-->
                              <!-- <span class="label bg-blue aepsfundCount {{Myhelper::hasRole('admin')?'' : 'hide'}}">0</span></a> -->
                     <!--      </li>-->
                     <!--      <li><a href="{{route('fund', ['type' => 'payoutrequest'])}}"><i class="las la-sign-in-alt"></i>Pending Payout Request</a>-->
                              <!-- <span class="label bg-blue payoutfundCount {{Myhelper::hasRole('admin')?'' : 'hide'}}">0</span></a> -->
                     <!--      </li>-->
                     <!--      @endif-->

                     <!--      @if (Myhelper::can(['aeps_fund_report']))-->
                     <!--      <li><a href="{{route('fund', ['type' => 'aepsrequestall'])}}"><i class="las la-sign-in-alt"></i>Request Report</a></li>-->
                     <!--      @endif-->
                     <!--   </ul>-->
                     <!--</li>-->
                     @endif

                     <!--@if (Myhelper::can(['microatm_fund_request', 'microatm_fund_view', 'microatm_fund_report']))-->
                     <!--<li>-->
                     <!--   <a href="#matmfund" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="ri-pantone-line "></i><span>MATM Fund </span><i class="ri-arrow-right-s-line iq-arrow-right"></i></a>-->
                        <!-- <span class="label bg-danger microatmfundCount {{Myhelper::hasRole('admin')?'' : 'hide'}}">0</span>     -->
                     <!--   </a>-->
                     <!--   <ul id="matmfund" class="iq-submenu collapse iq-submenu-data">-->

                     <!--      @if (Myhelper::can(['microatm_fund_request']))-->
                     <!--      <li><a href="{{route('fund', ['type' => 'microatm'])}}"><i class="ri-map-pin-time-line"></i>Request</a></li>-->
                     <!--      @endif-->

                     <!--      @if (Myhelper::can(['microatm_fund_view']))-->
                     <!--      <li><a href="{{route('fund', ['type' => 'microatmrequest'])}}"><i class="ri-map-pin-time-line"></i>Pending Request</a>-->
                     <!--      </li>-->
                     <!--      @endif-->

                     <!--      @if (Myhelper::can(['microatm_fund_report']))-->
                     <!--      <li><a href="{{route('fund', ['type' => 'microatmrequestall'])}}"><i class="ri-map-pin-time-line"></i>Request Report</a></li>-->
                     <!--      @endif-->
                     <!--   </ul>-->
                     <!--</li>-->
                     <!--@endif-->
                  </ul>
               </li>

               <!-- @if (Myhelper::can([ 'investment_fund_report', 'investment_fund_request']))-->
               <!--      <li>-->
               <!--         <a href="#invfunds" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="ri-list-check "></i><span>Investment Fund</span><i class="ri-arrow-right-s-line iq-arrow-right"></i></a>-->
                        <!-- <span class="label bg-danger fundCount {{Myhelper::hasRole('admin')?'' : 'hide'}}">0</span> -->

               <!--         <ul id="invfunds" class="iq-submenu collapse iq-submenu-data">-->
  
               <!--            <li><a href="{{url('admin/investment/show')}}"><i class="ri-facebook-fill"></i>Request</a></li>-->
   
               <!--            <li><a href="{{url('admin/investment/statement')}}"><i class="ri-stack-line"></i>All Fund Report</a></li>-->
                           
               <!--         </ul>-->
               <!--      </li>-->
               <!--@endif-->


               <!--@if (\Myhelper::can('invesment'))-->

               <!-- <li>-->
               <!--     <a href="#investment" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="lab la-wpforms iq-arrow-left"></i><span>Invesment Service</span><i class="ri-arrow-right-s-line iq-arrow-right"></i></a>-->
               <!--     <ul id="investment" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">-->

               <!--         <li><a href="{{route('investment')}}"><i class="las la-book"></i>Investment</a></li>-->

               <!--     </ul>-->
               <!-- </li>-->
               <!-- @endif-->
               <li>
                  <a href="#Reports" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="ri-record-circle-line"></i><span>Reports</span><i class="ri-arrow-right-s-line iq-arrow-right"></i></a>
                  <ul id="Reports" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                     <!--@if (Myhelper::can(['utiid_statement', 'aepsid_statement']))-->
                     <!--<li>-->
                     <!--   <a href="#agentList" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="ri-record-circle-line "></i><span>Agent List</span><i class="ri-arrow-right-s-line iq-arrow-right"></i></a>-->
                     <!--   <ul id="agentList" class="iq-submenu collapse iq-submenu-data">-->
                     <!--      @if (Myhelper::can('aepsid_statement'))-->
                           <!--<li><a href="{{route('statement', ['type' => 'aepsid'])}}">AePS </a></li>-->
                     <!--      <li><a href="{{route('fund', ['type' => 'addaccount'])}}"><i class="ri-map-pin-time-line"></i>Agent Bank</a></li>-->
                     <!--      <li><a href="{{route('statement', ['type' => 'paysprintaepsid'])}}"><i class="ri-map-pin-time-line"></i> PS AePS </a></li>-->
                     <!--      <li><a href="{{route('statement', ['type' => 'iciciagent'])}}"><i class="ri-map-pin-time-line"></i>Fing Agent</a></li>-->
                     <!--      @endif-->
                     <!--   </ul>-->
                     <!--</li>-->
                     <!--@endif-->
                     <!--@if (Myhelper::can(['account_statement', 'utiid_statement', 'utipancard_statement', 'recharge_statement', 'billpayment_statement']))-->
                     <!--<li>-->
                     <!--   <a href="#txnreport" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="ri-record-circle-line "></i><span>Transaction Report</span><i class="ri-arrow-right-s-line iq-arrow-right"></i></a>-->
                     <!--   <ul id="txnreport" class="iq-submenu collapse iq-submenu-data">-->
                     <!--      @if (Myhelper::can('aeps_statement'))-->
                     <!--      <li><a href="{{route('statement', ['type' => 'aeps'])}}"><i class="ri-map-pin-time-line"></i>AePS Statement</a></li>-->
                     <!--      @endif-->

                     <!--      @if (Myhelper::can('billpayment_statement'))-->
                     <!--      <li><a href="{{route('statement', ['type' => 'billpay'])}}"><i class="ri-map-pin-time-line"></i>Billpay Statement</a></li>-->
                     <!--      @endif-->
                            <!--
                     <!--      @if (Myhelper::can('money_statement'))-->
                     <!--      <li><a href="{{route('statement', ['type' => 'money'])}}"><i class="ri-map-pin-time-line"></i>DMT Statement</a></li>-->
                     <!--      @endif -->-->

                     <!--      @if (Myhelper::can('matm_fund_report'))-->
                     <!--      <li><a href="{{route('statement', ['type' => 'matm'])}}"><i class="ri-map-pin-time-line"></i>Micro ATM Statement</a></li>-->
                     <!--      @endif-->

                     <!--      @if (Myhelper::can('recharge_statement'))-->
                     <!--      <li><a href="{{route('statement', ['type' => 'recharge'])}}"><i class="ri-map-pin-time-line"></i>Recharge Statement </a></li>-->
                     <!--      @endif-->
                     <!--      <li><a href="{{route('statement', ['type' => 'loanenquiry'])}}"><i class="ri-map-pin-time-line"></i>Loanenquiry Statement </a></li>-->
                     <!--      <li><a href="{{route('statement', ['type' => 'cmsreport'])}}"><i class="ri-map-pin-time-line"></i>CMS Report</a></a></li>-->
                     <!--   </ul>-->
                     <!--</li>-->
                     <!--@endif-->
                     @if (Myhelper::can(['account_statement', 'awallet_statement','iwallet_statement']))
                     <li>
                        <a href="#walletreport" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="ri-record-circle-line "></i><span>Wallet History</span><i class="ri-arrow-right-s-line iq-arrow-right"></i></a>
                        <ul id="walletreport" class="iq-submenu collapse iq-submenu-data">
                           @if (Myhelper::can('account_statement'))
                           <li><a href="{{route('statement', ['type' => 'account'])}}"><i class="ri-map-pin-time-line"></i>Main Wallet</a></li>
                           @endif
                           <!--@if (Myhelper::can('awallet_statement'))-->
                           <!--<li><a href="{{route('statement', ['type' => 'awallet'])}}"><i class="ri-map-pin-time-line"></i>Aeps Wallet</a></li>-->
                           <!--@endif-->
                           <!--@if (Myhelper::can('iwallet_statement'))-->
                           <!--<li><a href="{{route('statement', ['type' => 'iawallet'])}}"><i class="ri-map-pin-time-line"></i>Investment Wallet</a></li>-->
                           <!--@endif-->
                        </ul>
                     </li>
                     @endif
                  </ul>
               </li>


               @if (Myhelper::hasRole('retailer'))

               <!--<li>-->
               <!--   <a href="{{route('loanform')}}" class="iq-waves-effect"><i class="las la-calendar "></i><span>Loan Enquiry</span></a>-->
               <!--</li>-->
               <!--@if(Myhelper::hasNotRole('admin'))-->
               <!--<li><a href="{{route('supportdata')}}"><i class="icon-cog"></i> <span style="color: #2196f3">Support Deatils</span></a></li>-->
               <!--@endif-->
               @endif




               <!-- 
               @if (Myhelper::can(['mapping_manager']))
               <li>
                  <a href="#mappingManager" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="ri-record-circle-line "></i><span>Mapping Manager</span><i class="ri-arrow-right-s-line iq-arrow-right"></i></a>
                  <ul id="mappingManager" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                     <li><a href="{{route('setup', ['type' => 'mappingid'])}}"><i class="las la-calendar"></i>Mapping Manager</a></li>
                  </ul>
               </li>
               @endif -->


               <li>
                  <a href="#Setting" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="ri-record-circle-line"></i><span>Setting</span><i class="ri-arrow-right-s-line iq-arrow-right"></i></a>
                  <ul id="Setting" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                     @if (Myhelper::can(['setup_bank', 'api_manager', 'setup_operator']))
                     <li>
                        <a href="#setuptools" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="ri-record-circle-line "></i><span>Setup Tools</span><i class="ri-arrow-right-s-line iq-arrow-right"></i></a>
                        <ul id="setuptools" class="iq-submenu collapse iq-submenu-data">
                           @if (Myhelper::hasRole('admin') || Myhelper::hasRole('subadmin'))
                           <li><a href="{{route('securedata')}}"><i class="las la-calendar"></i>Mobile User Logout</a></li>
                           @endif
                           @if (Myhelper::can('api_manager'))
                           <li><a href="{{route('setup', ['type' => 'api'])}}"><i class="las la-calendar"></i>API Manager</a></li>
                           @endif
                           @if (Myhelper::can('setup_bank'))
                           <li><a href="{{route('setup', ['type' => 'bank'])}}"><i class="las la-calendar"></i>Bank Account</a></li>
                           @endif
                           @if (Myhelper::can('complaint_subject'))
                           <li><a href="{{route('setup', ['type' => 'complaintsub'])}}"><i class="las la-calendar"></i>Complaint Subject</a></li>
                           @endif
                           @if (Myhelper::can('setup_operator'))
                           <li><a href="{{route('setup', ['type' => 'operator'])}}"><i class="las la-calendar"></i>Operator Manager</a></li>
                           @endif
                           @if (Myhelper::hasRole('admin'))
                           <li><a href="{{route('setup', ['type' => 'portalsetting'])}}"><i class="las la-calendar"></i>Portal Setting</a></li>
                           <li><a href="{{route('setup', ['type' => 'links'])}}"><i class="las la-calendar"></i>Quick Links</a></li>
                           @endif
                        </ul>
                     </li>
                     @endif

                     @if (Myhelper::hasRole('apiuser') && Myhelper::can('apiuser_acc_manager'))
                     <li>
                        <a href="#apiSetting" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="ri-record-circle-line "></i><span>Api Settings</span><i class="ri-arrow-right-s-line iq-arrow-right"></i></a>
                        <ul id="apiSetting" class="iq-submenu collapse iq-submenu-data">
                           <li><a href="{{route('apisetup', ['type' => 'setting'])}}"><i class="las la-calendar"></i>Callback & Token</a></li>
                           <li><a href="{{route('apisetup', ['type' => 'operator'])}}"><i class="las la-calendar"></i>Operator Code</a></li>
                           <!-- {{-- <li><a href="{{route('apisetup', ['type' => 'document'])}}"><i class="las la-calendar"></i>Api Documents</a></li> --}} -->
                     </li>
                     @endif

                     @if (Myhelper::hasRole('admin'))
                     <li>
                        <a href="#roles" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="ri-record-circle-line "></i><span>Roles & Permissions</span><i class="ri-arrow-right-s-line iq-arrow-right"></i></a>
                        <ul id="roles" class="iq-submenu collapse iq-submenu-data">
                           <li><a href="{{route('tools' , ['type' => 'roles'])}}"><i class="las la-calendar"></i>Roles</a></li>
                           <li><a href="{{route('tools' , ['type' => 'permissions'])}}"><i class="las la-calendar"></i>Permission</a></li>

                        </ul>
                     </li>
                     @endif

              
                     <li>
                        <a href="#accountSetting" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="ri-record-circle-line "></i><span>Account Settings</span><i class="ri-arrow-right-s-line iq-arrow-right"></i></a>
                        <ul id="accountSetting" class="iq-submenu collapse iq-submenu-data">
                           <li><a href="{{route('profile')}}"><i class="las la-calendar"></i>Profile Setting</a></li>
                           <li><a href="{{route('certificate')}}"><i class="las la-calendar"></i>Certificate</a></li>
                        </ul>
                     </li>

                     <li>
                        <a href="#driverLink" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="ri-record-circle-line "></i><span>Driver Links</span><i class="ri-arrow-right-s-line iq-arrow-right"></i></a>
                        <ul id="driverLink" class="iq-submenu collapse iq-submenu-data">
                           <li><a href="https://drive.google.com/drive/folders/10RF-h2b9lVoa_d692e5CUVnpi7Gxwr7R?usp=sharing" target="_blank"><i class="las la-calendar"></i>Mantra</a></li>
                           <li><a href="https://drive.google.com/open?id=13FbVSOuplWlJNhwKMjTmKHkyA5CZPkh0" target="_blank"><i class="las la-calendar"></i>Morpho</a></li>
                           <!--<li><a href="https://drive.google.com/open?id=19FZWSM3-vMdyd-_CpggvpyBPLaTSZcZa" target="_blank">Startek</a></li>-->
                           <li><a href="https://drive.google.com/open?id=1-LJfFXIvgE3ZLIm5fmYGjz95IvUnQYk4" target="_blank"><i class="las la-calendar"></i>Tatvik TMF20</a></li>
                        </ul>
                     </li>
                     @if (Myhelper::can('Complaint'))
                     <li>
                        <a href="{{route('complaint')}}" class="iq-waves-effect"><i class="las la-calendar "></i><span>Complaints</span></a>
                     </li>
                     @endif
                  </ul>
               </li>
               @endif


            </ul>
         </nav>
      </div>
      <nav class="navbar navbar-expand-lg navbar-light p-0">

         <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-label="Toggle navigation">
            <i class="ri-menu-3-line"></i>
         </button>
         <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ml-auto navbar-list">


               @if (Myhelper::hasRole('admin'))
               <li class="nav-item nav-icon mt-4">
                  <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#walletLoadModal">Load Wallet</button>

               </li>
               @endif

               <li class="nav-item nav-icon">
                  <a href="#" class="search-toggle iq-waves-effect bg-primary rounded">
                     <i class="ri-wallet-2-line"></i>
                     <span class="bg-danger dots"></span>
                  </a>
                  <div class="iq-sub-dropdown">
                     <div class="iq-card shadow-none m-0">
                        <div class="iq-card-body p-0 ">
                           <div class="bg-primary p-3">
                              <h5 class="mb-0 text-white">Wallet Balance</h5>
                           </div>
                           <a href="#" class="iq-sub-card">
                              <div class="media align-items-center">
                                 <div class="">
                                    <img class="avatar-40 rounded" src="{{asset('')}}theme/images/user/01.jpg" alt="">
                                 </div>
                                 <div class="media-body ml-3">
                                    <h6 class="mb-0 ">Main Wallet</h6>

                                    <p class="mb-0"> &#8377; {{Auth::user()->mainwallet}} /-</p>
                                 </div>
                              </div>
                           </a>
                           <a href="#" class="iq-sub-card">
                              <div class="media align-items-center">
                                 <div class="">
                                    <img class="avatar-40 rounded" src="{{asset('')}}theme/images/user/02.jpg" alt="">
                                 </div>
                                 <div class="media-body ml-3">
                                    <h6 class="mb-0 ">AEPS Wallet</h6>

                                    <p class="mb-0"> &#8377; {{Auth::user()->aepsbalance}} /-</p>
                                 </div>
                              </div>
                           </a>
                           <a href="#" class="iq-sub-card">
                              <div class="media align-items-center">
                                 <div class="">
                                    <img class="avatar-40 rounded" src="{{asset('')}}theme/images/user/02.jpg" alt="">
                                 </div>
                                 <div class="media-body ml-3">
                                    <h6 class="mb-0 ">Investment Wallet</h6>

                                    <p class="mb-0"> &#8377; {{Auth::user()->investment_wallet}} /-</p>
                                 </div>
                              </div>
                           </a>
                        </div>
                     </div>
                  </div>
               </li>

               <li class="nav-item nav-icon">
                  <a href="#" class="search-toggle iq-waves-effect bg-primary rounded">
                     <i class="ri-notification-line"></i>
                     <span class="bg-danger dots"></span>
                  </a>
                  <div class="iq-sub-dropdown">
                     <div class="iq-card shadow-none m-0">
                        <div class="iq-card-body p-0 ">
                           <div class="bg-primary p-3">
                              <h5 class="mb-0 text-white">All Notifications<small class="badge  badge-light float-right pt-1">4</small></h5>
                           </div>
                           <a href="#" class="iq-sub-card">
                              <div class="media align-items-center">
                                 <div class="">
                                    <img class="avatar-40 rounded" src="{{asset('')}}theme/images/user/01.jpg" alt="">
                                 </div>
                                 <div class="media-body ml-3">
                                    <h6 class="mb-0 ">Emma Watson Barry</h6>
                                    <small class="float-right font-size-12">Just Now</small>
                                    <p class="mb-0">95 MB</p>
                                 </div>
                              </div>
                           </a>

                        </div>
                     </div>
                  </div>
               </li>

            </ul>
         </div>
         <ul class="navbar-list">
            <li class="line-height">
               <a href="#" class="search-toggle iq-waves-effect d-flex align-items-center">
                  <img src="{{asset('')}}theme/images/user/03.jpg" class="img-fluid rounded mr-3" alt="user">
                  <div class="caption">
                     <h6 class="mb-0 line-height">{{ explode(' ',ucwords(Auth::user()->name))[0] }}</h6>
                     <p class="mb-0">{{Auth::user()->role->name}}</p>
                  </div>
               </a>
               <div class="iq-sub-dropdown iq-user-dropdown">
                  <div class="iq-card shadow-none m-0">
                     <div class="iq-card-body p-0 ">
                        <div class="bg-primary p-3">
                           <h5 class="mb-0 text-white line-height">Hello {{ Auth::user()->name}}</h5>
                           <span class="text-white font-size-12">UserId - {{Auth::id()}}</span>
                        </div>
                        <a href="{{route('profile')}}" class="iq-sub-card iq-bg-primary-hover">
                           <div class="media align-items-center">
                              <div class="rounded iq-card-icon iq-bg-primary">
                                 <i class="ri-file-user-line"></i>
                              </div>
                              <div class="media-body ml-3">
                                 <h6 class="mb-0 ">My Profile</h6>
                                 <p class="mb-0 font-size-12">View personal profile details.</p>
                              </div>
                           </div>
                        </a>

                        @if (Myhelper::hasNotRole('admin') && Myhelper::can('view_commission'))
                        <a href="{{route('resource', ['type' => 'commission'])}}" class="iq-sub-card iq-bg-primary-hover">
                           <div class="media align-items-center">
                              <div class="rounded iq-card-icon iq-bg-primary">
                                 <i class="ri-profile-line"></i>
                              </div>
                              <div class="media-body ml-3">
                                 <h6 class="mb-0 ">View Commission</h6>
                                 <p class="mb-0 font-size-12">View your personal commission.</p>
                              </div>
                           </div>
                        </a>
                        @endif

                        <div class="d-inline-block w-100 text-center p-3">
                           <a class="bg-primary iq-sign-btn" href="{{route('logout')}}" role="button">Sign out<i class="ri-login-box-line ml-2"></i></a>
                        </div>
                     </div>
                  </div>
               </div>
            </li>
         </ul>
      </nav>
   </div>
</div>