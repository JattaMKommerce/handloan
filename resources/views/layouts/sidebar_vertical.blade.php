<div class="iq-sidebar">
    <div class="iq-navbar-logo d-flex justify-content-between">


        @if (Auth::user()->company->logo)
            <a class="header-logo" href="{{ route('home') }}">
                <img src="{{ asset('') }}logos/{{ Auth::user()->company->logo }}" class=" img-fluid rounded"
                    alt="">
            </a>
        @else
            <a href="{{ route('home') }}" class="header-logo">
                <img src="" class="img-fluid rounded" alt="">
                <span>UjjwalPay</span>
            </a>
        @endif
        <div class="iq-menu-bt align-self-center">
            <div id="sidebarhide" class="wrapper-menu">
                <div class="main-circle"><i class="ri-menu-line"></i></div>
                <div class="hover-circle"><i class="ri-close-fill"></i></div>
            </div>
        </div>
    </div>
    <div id="sidebar-scrollbar">
        <nav class="iq-sidebar-menu">
            <ul id="iq-sidebar-toggle" class="iq-menu">
                @if (Auth::user()->kyc == 'verified')
                    <li class="{{ request()->routeIs('home') ? 'active' : '' }}">
                        <a href="{{ route('home') }}" class="iq-waves-effect"><span
                                class="ripple rippleEffect"></span><i
                                class="las la-home text-danger iq-arrow-left"></i><b>Dashboard</b></a>
                    </li>

                    @if (Myhelper::hasNotRole('admin'))
                        
                        @if (Myhelper::can(['recharge_service']))
                            <li class="{{ Request::is('recharge/*') ? 'active' : '' }}">
                                <a href="#menu-design" class="iq-waves-effect" data-toggle="collapse"
                                    aria-expanded="{{ Request::is('recharge/*') ? 'true' : 'false' }}"><i
                                        class="las la-charging-station text-success iq-arrow-left"></i><b>Utility
                                        Recharge</b><i class="ri-arrow-right-s-line iq-arrow-right"></i></a>

                                <ul id="menu-design"
                                    class="iq-submenu collapse {{ Request::is('recharge/*') ? 'show' : '' }}"
                                    data-parent="#iq-sidebar-toggle">
                                    @if (Myhelper::can('recharge_service'))
                                        <li class="{{ Request::is('recharge/mobile') ? 'active' : '' }}"><a
                                                href="{{ route('recharge', ['type' => 'mobile']) }}"><i
                                                    class="las la-phone text-danger"></i><b>Mobile</b></a></li>
                                        <li class="{{ Request::is('recharge/dth') ? 'active' : '' }}"><a
                                                href="{{ route('recharge', ['type' => 'dth']) }}"><i
                                                    class="las la-satellite-dish text-info"></i><b>DTH</b></a></li>
                                    @endif
                                </ul>
                            </li>
                        @endif
                        @if (Myhelper::can(['cms_service']))
                        <li class="{{ Request::is('cms/*') ? 'active' : '' }}"><a href="{{route('cms')}}" class="iq-waves-effect"><i class="fa fa-address-card text-warning iq-arrow-left"></i> <span>CMS Service</span></a></li>
                        @endif

                        <!--@if (Myhelper::can(['billpayment_service']))-->
                        <!--    <li class="{{ Request::is('billpay/*') ? 'active' : '' }}">-->
                        <!--        <a href="#userinfo" class="iq-waves-effect" data-toggle="collapse"-->
                        <!--            aria-expanded="{{ Request::is('billpay/*') ? 'true' : 'false' }}"><span-->
                        <!--                class="ripple rippleEffect"></span><i-->
                        <!--                class="las la-file-invoice-dollar text-danger iq-arrow-left"></i><b>Bill-->
                        <!--                Payment</b><i class="ri-arrow-right-s-line iq-arrow-right"></i></a>-->
                        <!--        <ul id="userinfo"-->
                        <!--            class="iq-submenu collapse {{ Request::is('billpay/*') ? 'show' : '' }}"-->
                        <!--            data-parent="#iq-sidebar-toggle" style="">-->
                        <!--            @if (Myhelper::can('billpayment_service'))-->
                        <!--                <li class="{{ Request::is('billpay/electricity') ? 'active' : '' }}"><a-->
                        <!--                        href="{{ route('bill', ['type' => 'electricity']) }}"><i-->
                        <!--                            class="las la-charging-station text-danger"></i><b>Electricity</b></a></li>-->
                        <!--                <li class="{{ Request::is('billpay/postpaid') ? 'active' : '' }}"><a-->
                        <!--                        href="{{ route('bill', ['type' => 'postpaid']) }}"><i-->
                        <!--                            class="fa fa-rupee text-success"></i><b>Postpaid</b></a></li>-->
                        <!--                <li class="{{ Request::is('billpay/water') ? 'active' : '' }}"><a-->
                        <!--                        href="{{ route('bill', ['type' => 'water']) }}"><i-->
                        <!--                            class="las la-water text-dark"></i><b>Water</b></a></li>-->
                        <!--                <li class="{{ Request::is('billpay/broadband') ? 'active' : '' }}"><a-->
                        <!--                        href="{{ route('bill', ['type' => 'broadband']) }}"><i-->
                        <!--                            class="las la-calendar text-info"></i><b>Broadband</b></a></li>-->
                        <!--                <li class="{{ Request::is('billpay/lpg') ? 'active' : '' }}"><a-->
                        <!--                        href="{{ route('bill', ['type' => 'lpggas']) }}"><i-->
                        <!--                            class="las la-oil-can text-warning"></i><b>LPG Gas </b></a></li>-->
                        <!--                <li class="{{ Request::is('billpay/gas') ? 'active' : '' }}"><a-->
                        <!--                        href="{{ route('bill', ['type' => 'gasutility']) }}"><i-->
                        <!--                            class="las la-gas-pump text-danger"></i><b>Gas utility</b></a></li>-->
                        <!--                <li class="{{ Request::is('billpay/landline') ? 'active' : '' }}"><a-->
                        <!--                        href="{{ route('bill', ['type' => 'landline']) }}"><i-->
                        <!--                            class="fa fa-phone text-success"></i><b>Landline</b></a></li>-->
                                        <!--<li><a href="{{ route('bill', ['type' => 'postpaid']) }}">Postpaid</b></a></li>-->
                        <!--                <li class="{{ Request::is('billpay/schoolfees') ? 'active' : '' }}"><a-->
                        <!--                        href="{{ route('bill', ['type' => 'schoolfees']) }}"><i-->
                        <!--                            class="fa fa-codepen text-warning"></i><b>Education Fees</b></a></li>-->
                        <!--                <li class="{{ Request::is('billpay/loanrepay') ? 'active' : '' }}"><a-->
                        <!--                        href="{{ route('bill', ['type' => 'loanrepay']) }}"><i-->
                        <!--                            class="las la-inbox text-danger"></i><b>Loan Repayment</b></a></li>-->
                        <!--                <li class="{{ Request::is('billpay/fasttag') ? 'active' : '' }}">-->
                        <!--                    <a href="{{ route('bill', ['type' => 'fasttag']) }}"><i-->
                        <!--                            class="las la-calendar text-warning"></i><b>FASTag</b></a></li>-->
                        <!--                <li class="{{ Request::is('billpay/insurance') ? 'active' : '' }}"><a-->
                        <!--                        href="{{ route('bill', ['type' => 'insurance']) }}"><i-->
                        <!--                            class="ri-mail-send-line text-success"></i><b>LIC/Insurance</b></a></li>-->
                        <!--                <li class="{{ Request::is('billpay/muncipal') ? 'active' : '' }}"><a-->
                        <!--                        href="{{ route('bill', ['type' => 'muncipal']) }}"><i-->
                        <!--                            class="las la-inbox text-warning"></i><b>Municipal Tax</b></a></li>-->
                        <!--                <li class="{{ Request::is('billpay/housing') ? 'active' : '' }}"><a-->
                        <!--                        href="{{ route('bill', ['type' => 'housing']) }}"><i-->
                        <!--                            class="fa fa-home text-info"></i><b>Housing Tax</b></a></li>-->
                        <!--            @endif-->
                        <!--        </ul>-->
                        <!--    </li>-->
                        <!--@endif-->

                        <!--@if (Myhelper::can(['flight_service']))-->
                        <!--    <li class="{{ Request::is('flight/*') ? 'active' : '' }}">-->
                        <!--        <a href="#userinfoflight" class="iq-waves-effect" data-toggle="collapse"-->
                        <!--            aria-expanded="{{ Request::is('flight/*') ? 'true' : 'false' }}"><span-->
                        <!--                class="ripple rippleEffect"></span><i-->
                        <!--                class="las la-file-invoice-dollar text-danger iq-arrow-left"></i><b>Flight-->
                        <!--                Ticket</b><i class="ri-arrow-right-s-line iq-arrow-right"></i></a>-->
                        <!--        <ul id="userinfoflight"-->
                        <!--            class="iq-submenu collapse {{ Request::is('flight/*') ? 'show' : '' }}"-->
                        <!--            data-parent="#iq-sidebar-toggle" style="">-->
                        <!--            @if (Myhelper::can('flight_service'))-->
                        <!--                <li class="{{ Request::is('flight') ? 'active' : '' }}"><a-->
                        <!--                        href="{{ url('flight') }}"><i-->
                        <!--                            class="las la-charging-station text-danger"></i><b>Book Flight</b></a>-->
                        <!--                </li>-->
                        <!--                <li class="{{ Request::is('flight') ? 'active' : '' }}"><a-->
                        <!--                        href="{{ url('bookingStatus') }}"><i-->
                        <!--                            class="fa fa-rupee text-success"></i><b>Check Booking Details</b></a></li>-->
                        <!--            @endif-->
                        <!--        </ul>-->
                        <!--    </li>-->
                        <!--@endif-->

                        <!-- @if (Myhelper::can(['billpayment_service']))
<li class="{{ Request::is('billpay/fasttag') ? 'active' : '' }}">
                    <a href="{{ route('bill', ['type' => 'fasttag']) }}" class="iq-waves-effect"><i class="las la-calendar text-warning iq-arrow-left"></i><span>FASTag</span></a>
                </li>
@endif -->

                        <!-- <li class="{{ Request::is('billpay/*') ? 'active' : '' }}">
                    <a href="#finance" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="{{ Request::is('billpay/*') ? 'true' : 'false' }}"><i class="las la-mail-bulk text-info iq-arrow-left"></i><span>Financial & Taxes</span><i class="ri-arrow-right-s-line iq-arrow-right"></i></a>
                    <ul id="finance" class="iq-submenu collapse {{ Request::is('billpay/*') ? 'show' : '' }}" data-parent="#iq-sidebar-toggle">
                        @if (Myhelper::can('billpayment_service'))
<li class="{{ Request::is('billpay/loanrepay') ? 'active' : '' }}"><a href="{{ route('bill', ['type' => 'loanrepay']) }}"><i class="las la-inbox text-danger"></i>Loan Repayment</a></li>
                        <li class="{{ Request::is('billpay/insurance') ? 'active' : '' }}"><a href="{{ route('bill', ['type' => 'insurance']) }}"><i class="ri-mail-send-line text-success"></i>LIC/Insurance</a></li>
                        <li class="{{ Request::is('billpay/muncipal') ? 'active' : '' }}"><a href="{{ route('bill', ['type' => 'muncipal']) }}"><i class="las la-inbox text-warning"></i>Municipal Tax</a></li>
                        <li class="{{ Request::is('billpay/housing') ? 'active' : '' }}"><a href="{{ route('bill', ['type' => 'housing']) }}"><i class="fa fa-home text-info"></i>Housing Tax</a></li>
@endif
                    </ul>
                </li> -->

                        <!--@if (Myhelper::can(['utipancard_service', 'nsdl_service']))-->
                        <!--    <li class="{{ Request::is('pancard/*') ? 'active' : '' }}">-->
                        <!--        <a href="#utiPan" class="iq-waves-effect collapsed" data-toggle="collapse"-->
                        <!--            aria-expanded="{{ Request::is('pancard/*') ? 'true' : 'false' }}"><i-->
                        <!--                class="lab la-elementor text-danger iq-arrow-left"></i><b>PAN Card</b><i-->
                        <!--                class="ri-arrow-right-s-line iq-arrow-right"></i></a>-->
                        <!--        <ul id="utiPan"-->
                        <!--            class="iq-submenu collapse {{ Request::is('pancard/*') ? 'show' : '' }}"-->
                        <!--            data-parent="#iq-sidebar-toggle">-->
                        <!--            @if (Myhelper::can('utipancard_service'))-->
                        <!--                <li class="{{ Request::is('pancard/uti') ? 'active' : '' }}"><a-->
                        <!--                        href="{{ route('pancard', ['type' => 'uti']) }}"><i-->
                        <!--                            class="las la-palette"></i><b>UTI</b></a></li>-->
                        <!--            @endif-->
                        <!--        </ul>-->
                        <!--    </li>-->
                        <!--@endif-->

                        @if (Myhelper::can(['dmt1_service', 'aeps_service']))
                            <li class="{{ Request::is('dmt') || Request::is('iaeps') ? 'active' : '' }}">
                                <a href="#bankingService" class="iq-waves-effect collapsed" data-toggle="collapse"
                                    aria-expanded="{{ Request::is('dmt') || Request::is('iaeps') ? 'true' : 'false' }}"><i
                                        class="lab la-wpforms text-danger iq-arrow-left"></i><b>Banking
                                        Service</b><i class="ri-arrow-right-s-line iq-arrow-right"></i></a>
                                <ul id="bankingService"
                                    class="iq-submenu collapse {{ Request::is('dmt') || Request::is('iaeps') ? 'show' : '' }}"
                                    data-parent="#iq-sidebar-toggle">

                                    @if (Myhelper::can('dmt1_service'))
                                        <li class="{{ Request::is('dmt') ? 'active' : '' }}"><a
                                                href="{{ route('dmt2') }}"><i
                                                    class="las la-book text-danger"></i><b>DMT</b></a></li>
                                    @endif
                                     @if (Myhelper::can('dmt3_service'))
                                        <li class="{{ Request::is('dmt') ? 'active' : '' }}"><a
                                                href="{{ route('dmt3') }}"><i
                                                    class="las la-book text-danger"></i><b>PP-DMT</b></a></li>
                                    @endif
                                    @if (Myhelper::can('dmt2_service'))
                                        <li class="{{ Request::is('pdmt') ? 'active' : '' }}"><a
                                                href="{{ route('xdmt') }}"><i
                                                    class="las la-book text-danger"></i><b>XDMT</b></a></li>
                                    @endif
                                    @if (Myhelper::can('m2_payout'))
                                        <li class="{{ Request::is('pdmt') ? 'active' : '' }}"><a
                                            href="{{ route('fund', ['type' => 'payout']) }}"><i
                                                class="fa fa-question-circle text-warning"></i><b>KYC DMT</b></a></li>
                                    @endif

                                    @if (Myhelper::can('aeps_service'))
                                        <li class="{{ Request::is('iaeps') ? 'active' : '' }}"><a
                                                href="{{route('iaeps')}}"><i
                                                    class="las la-edit text-success"></i><b>AEPS</b></a></li>
                                    @endif

                                    @if (Myhelper::can('cyrus_payout_service'))
                                        <!--<li class="{{ Request::is('dmt') ? 'active' : '' }}"><a-->
                                        <!--        href="{{ url('cpayout', ['type' => 'cyruspayout']) }}"><i-->
                                        <!--            class="las la-book text-danger"></i><b>Cyrus Payout</b></a></li>-->
                                    @endif

                                    @if (Myhelper::can('runpaisa_payout_service'))
                                        <!--<li class="{{ Request::is('dmt') ? 'active' : '' }}"><a-->
                                        <!--        href="{{ url('cpayout', ['type' => 'runpaisa']) }}"><i-->
                                        <!--            class="las la-book text-danger"></i><b>Runpaisa Payout</b></a></li>-->
                                    @endif

                                </ul>
                            </li>
                        @endif
                        <!--<li>-->
                        <!--    <a href="#serviceLink" class="iq-waves-effect collapsed" data-toggle="collapse"-->
                        <!--        aria-expanded="false"><i-->
                        <!--            class="ri-archive-drawer-line text-warning iq-arrow-left"></i><b>Service-->
                        <!--            Links</b><i class="ri-arrow-right-s-line iq-arrow-right"></i></a>-->
                        <!--    <ul id="serviceLink" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">-->
                        <!--        @if (sizeof($mydata['links']) > 0)-->
                        <!--            @foreach ($mydata['links'] as $link)-->
                        <!--                <li><a href="{{ $link->value }}" target="_blank"><i-->
                        <!--                            class="ri-clockwise-line text-danger"></i><b>{{ $link->name }}</b></a>-->
                        <!--                </li>-->
                        <!--            @endforeach-->
                        <!--        @endif-->
                        <!--    </ul>-->
                        <!--</li>-->

                    @endif

                    @if (Myhelper::can(['company_manager', 'change_company_profile']) ||
                            (Myhelper::hasNotRole('retailer') &&
                                isset($mydata['schememanager']) &&
                                $mydata['schememanager']->value == 'all'))
                        <li class="{{ Request::is('resources/*') ? 'active' : '' }}">
                            <a href="#resources" class="iq-waves-effect" data-toggle="collapse"
                                aria-expanded="{{ Request::is('resources/*') ? 'true' : 'false' }}"><i
                                    class="ri-table-line iq-arrow-left text-success"></i><b>Resources</b><i
                                    class="ri-arrow-right-s-line iq-arrow-right"></i></a>
                            <ul id="resources"
                                class="iq-submenu collapse {{ Request::is('resources/*') ? 'show' : '' }}"
                                data-parent="#iq-sidebar-toggle">
                                @if (Myhelper::hasNotRole('retailer') && isset($mydata['schememanager']) && $mydata['schememanager']->value == 'all')
                                    <li class="{{ Request::is('resources/package') ? 'active' : '' }}"><a
                                            href="{{ route('resource', ['type' => 'package']) }}"><i
                                                class="ri-table-line text-danger"></i><b>Scheme Manager</b></a></li>
                                @elseif (Myhelper::hasRole('admin'))
                                    <li class="{{ Request::is('resources/scheme') ? 'active' : '' }}"><a
                                            href="{{ route('resource', ['type' => 'scheme']) }}"><i
                                                class="ri-database-line text-success"></i><b>Scheme Manager</b></a></li>
                                @endif
                                @if (Myhelper::can('company_manager'))
                                    <li class="{{ Request::is('resources/company') ? 'active' : '' }}"><a
                                            href="{{ route('resource', ['type' => 'company']) }}"><i
                                                class="ri-refund-line text-warning"></i><b>Company Manager</b></a></li>
                                @endif

                                @if (Myhelper::can('change_company_profile'))
                                    <li class="{{ Request::is('resources/companyprofile') ? 'active' : '' }}"><a
                                            href="{{ route('resource', ['type' => 'companyprofile']) }}"><i
                                                class="fa fa-user-o text-danger"></i><b>Company Profile</b></a></li>
                                @endif

                            </ul>
                        </li>
                    @endif

                    @if (Myhelper::can([
                            'view_whitelable',
                            'view_md',
                            'view_distributor',
                            'view_retailer',
                            'view_apiuser',
                            'view_other',
                            'view_kycpending',
                            'view_kycsubmitted',
                            'view_kycrejected',
                        ]))

                        <li class="{{ Request::is('member/*') ? 'active' : '' }}">
                            <a href="#member" class="iq-waves-effect collapsed" data-toggle="collapse"
                                aria-expanded="{{ Request::is('member/*') ? 'true' : 'false' }}"><i
                                    class="fa fa-address-book text-info iq-arrow-left"></i><b>Member</b><i
                                    class="ri-arrow-right-s-line iq-arrow-right"></i></a>
                            <ul id="member"
                                class="iq-submenu collapse {{ Request::is('member/*') ? 'show' : '' }}"
                                data-parent="#iq-sidebar-toggle">

                                @if (Myhelper::can(['view_whitelable']))
                                    <li class="{{ Request::is('member/whitelable') ? 'active' : '' }}"><a
                                            href="{{ route('member', ['type' => 'whitelable']) }}"><i
                                                class="ri-file-chart-line text-warning"></i><b>Whitelabel</b></a></li>
                                @endif
                                @if (Myhelper::can(['view_md']))
                                    <li class="{{ Request::is('member/md') ? 'active' : '' }}"><a
                                            href="{{ route('member', ['type' => 'md']) }}"><i
                                                class="fa fa-user-o text-danger"></i><b>Master Distributor</b></a></li>
                                @endif
                                @if (Myhelper::can(['view_distributor']))
                                    <li class="{{ Request::is('member/distributor') ? 'active' : '' }}"><a
                                            href="{{ route('member', ['type' => 'distributor']) }}"><i
                                                class="fa fa-users text-success"></i><b>Distributor</b></a></li>
                                @endif
                                @if (Myhelper::can(['view_retailer']))
                                    <li class="{{ Request::is('member/retailer') ? 'active' : '' }}"><a
                                            href="{{ route('member', ['type' => 'retailer']) }}"><i
                                                class="fa fa-user text-warning"></i><b>Retailer</b></a></li>
                                @endif
                                @if (Myhelper::hasRole('admin') || Myhelper::hasRole('subadmin'))
                                    <li class="{{ Request::is('member/web') ? 'active' : '' }}"><a
                                            href="{{ route('member', ['type' => 'web']) }}"><i
                                                class="fa fa-user-o text-danger"></i><b>All Member</b></a></li>
                                @endif
                                @if (Myhelper::hasRole('admin') || Myhelper::hasRole('subadmin'))
                                    <li class="{{ Request::is('member/kycsubmitted') ? 'active' : '' }}"><a
                                            href="{{ route('member', ['type' => 'kycsubmitted']) }}"><i
                                                class="fa fa-address-book text-success"></i><b>Kycsubmited User</b></a></li>
                                @endif
                                @if (Myhelper::hasRole('admin') || Myhelper::hasRole('subadmin'))
                                    <li class="{{ Request::is('member/kycrejected') ? 'active' : '' }}"><a
                                            href="{{ route('member', ['type' => 'kycrejected']) }}"><i
                                                class="ri-bar-chart-line text-info"></i><b>Kyc Rejected User</b></a></li>
                                @endif
                                @if (Myhelper::hasRole('admin') || Myhelper::hasRole('subadmin'))
                                    <li class="{{ Request::is('member/kycpending') ? 'active' : '' }}"><a
                                            href="{{ route('member', ['type' => 'kycpending']) }}"><i
                                                class="fa fa-user-o text-warning"></i><b>Kyc Pending User</b></a></li>
                                @endif
                                <!-- @if (Myhelper::can(['view_other']))
<li class="{{ Request::is('member/other') ? 'active' : '' }}"><a href="{{ route('member', ['type' => 'other']) }}"><i class="fa fa-users text-danger"></i><b>Other User</b></a></li>
@endif
                        @if (Myhelper::can(['view_employee']))
<li class="{{ Request::is('member/employee') ? 'active' : '' }}"><a href="{{ route('member', ['type' => 'employee']) }}"><i class="fa fa-address-book text-success"></i><b>Employee</b></a></li>
@endif -->
                            </ul>
                        </li>
                    @endif

                    @if (Myhelper::can(['fund_transfer', 'fund_return', 'fund_request_view', 'fund_report', 'fund_request','payout']))
                        <li
                            class="{{ Request::is('fund/tr') || Request::is('fund/requestview') || Request::is('fund/request') || Request::is('fund/requestviewall') || Request::is('fund/statement') ? 'active' : '' }}">
                            <a href="#funds" class="iq-waves-effect collapsed" data-toggle="collapse"
                                aria-expanded="{{ Request::is('fund/tr') || Request::is('fund/requestview') || Request::is('fund/request') || Request::is('fund/requestviewall') || Request::is('fund/statement') ? 'true' : 'false' }}"><i
                                    class="ri-list-check text-danger iq-arrow-left"></i><b>Fund</b><i
                                    class="ri-arrow-right-s-line iq-arrow-right"></i></a>
                            <!-- <span class="label bg-danger fundCount {{ Myhelper::hasRole('admin') ? '' : 'hide' }}">0</span> -->

                            <ul id="funds"
                                class="iq-submenu collapse {{ Request::is('fund/tr') || Request::is('fund/requestview') || Request::is('fund/request') || Request::is('fund/requestviewall') || Request::is('fund/statement') ? 'show' : '' }}"
                                data-parent="#iq-sidebar-toggle">
                                @if (Myhelper::can(['fund_transfer', 'fund_return']))
                                    <li class="{{ Request::is('fund/tr') ? 'active' : '' }}"><a
                                            href="{{ route('fund', ['type' => 'tr']) }}"><i
                                                class="las la-mail-bulk text-danger"></i><b>Transfer/Return</b></a></li>
                                @endif

                                <!--@if (Myhelper::can(['runpaisa_service']))-->
                                <!--    <li class="{{ Request::is('fund/runpaisapg') ? 'active' : '' }}"><a-->
                                <!--            href="{{ route('fund', ['type' => 'runpaisapg']) }}"><i-->
                                <!--                class="fa fa-file text-danger"></i><b>RUN Paisa PG</b></a></li>-->
                                <!--@endif-->
                                @if (Myhelper::can(['payout']))
                                    <li class="{{ Request::is('fund/*') ? 'active' : '' }}">
                                        <a
                                            href="{{ route('fund', ['type' => 'payout']) }}"><i
                                                class="fa fa-question-circle text-warning"></i><b>Payout</b></a>
                                    </li>
                                     <li class="{{ Request::is('fund/payoutrequest') ? 'active' : '' }}">
                                        <a
                                            href="{{ route('fund', ['type' => 'payoutrequest']) }}"><i
                                                class="fa fa-question-circle text-warning"></i><b>Payout Request</b></a>
                                    </li>
                                @endif
                                @if (Myhelper::can(['setup_bank']))
                                    <li class="{{ Request::is('fund/requestview') ? 'active' : '' }}"><a
                                            href="{{ route('fund', ['type' => 'requestview']) }}"><i
                                                class="fa fa-question-circle text-warning"></i><b>Request</b></a></li>
                                @endif

                                @if (Myhelper::hasNotRole('admin') && Myhelper::can('fund_request'))
                                    <li class="{{ Request::is('fund/request') ? 'active' : '' }}"><a
                                            href="{{ route('fund', ['type' => 'request']) }}"><i
                                                class="fa fa-google-wallet text-success"></i><b>Load Main Wallet</b></a></li>
                                @endif

                                @if (Myhelper::can(['fund_report']))
                                    <li class="{{ Request::is('fund/requestviewall') ? 'active' : '' }}"><a
                                            href="{{ route('fund', ['type' => 'requestviewall']) }}"><i
                                                class="fa fa-file text-info"></i><b>Request Report</b></a></li>
                                    <li class="{{ Request::is('fund/statement') ? 'active' : '' }}"><a
                                            href="{{ route('fund', ['type' => 'statement']) }}"><i
                                                class="ri-stack-line text-danger"></i><b>All Fund Report</b></a></li>
                                @endif
                                @if (Myhelper::can(['wallettowallet']))
                                 <li class="{{ Request::is('fund/wallettowallet') ? 'active' : '' }}"><a href="{{route('fund', ['type' => 'wallettowallet'])}}"><i
                                                class="fa fa-file text-info"></i><b>Move to Wallet</b></a></li>
                                 @endif
                            </ul>
                        </li>
                    @endif


                    <!--@if (Myhelper::can(['investment_fund_report', 'investment_fund_request']))-->
                    <!--    <li>-->
                    <!--        <a href="#invfunds" class="iq-waves-effect collapsed" data-toggle="collapse"-->
                    <!--            aria-expanded="false"><i-->
                    <!--                class="lab la-wpforms text-warning iq-arrow-left"></i><b>Investment-->
                    <!--                Funds</b><i class="ri-arrow-right-s-line iq-arrow-right"></i></a>-->
                    <!--        <ul id="invfunds" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">-->
                    <!--            @if (Myhelper::hasRole('admin'))-->
                    <!--                <li><a href="{{ url('admin/investment/show') }}"><i-->
                    <!--                            class="ri-facebook-fill text-danger"></i><b>Request</b></a></li>-->

                    <!--                <li><a href="{{ url('admin/investment/statement') }}"><i-->
                    <!--                            class="ri-stack-line text-success"></i><b>All Fund Report</b></a></li>-->
                    <!--            @endif-->

                    <!--            @if (Myhelper::can(['investment_fund_request']) && !Myhelper::hasRole('admin'))-->
                    <!--                <li><a href="{{ url('investment/fund_req') }}"><i-->
                    <!--                            class="ri-facebook-fill text-info"></i><b>Request</b></a></li>-->
                    <!--            @endif-->

                    <!--        </ul>-->
                    <!--    </li>-->
                    <!--@endif-->


                    <!--@if (Myhelper::hasRole('admin'))-->
                    <!--    <li>-->
                    <!--        <a href="#apiintegration" class="iq-waves-effect collapsed" data-toggle="collapse"-->
                    <!--            aria-expanded="false"><i class="lab la-wpforms text-info iq-arrow-left"></i><b>Api Integration Tools</b><i class="ri-arrow-right-s-line iq-arrow-right"></i></a>-->
                    <!--        <ul id="apiintegration" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">-->
                    <!--        <li class="navigation-header" style="border: none;margin-left:16px;"><span>Api Switches</span> <i class="icon-menu" title="" data-original-title="Main pages"></i></li>-->
                                
                    <!--            <li class="{{ Request::is('api_switch') ? 'active' : '' }}"><a href="{{route('apiswitch', ['type' => 'amount'])}}"><i class="las la-book text-danger"></i><b>Amount Wise</b></a></li>-->
                    <!--            <li class="{{ Request::is('api_switch') ? 'active' : '' }}"><a href="{{route('apiswitch', ['type' => 'state'])}}"><i class="las la-book text-danger"></i><b>State Wise</b></a></li>-->
                    <!--            <li class="{{ Request::is('api_switch') ? 'active' : '' }}"><a href="{{route('apiswitch', ['type' => 'user'])}}"><i class="las la-book text-danger"></i><b>User Wise</b></a></li>-->
                    <!--        <li class="navigation-header" style="border: none;margin-left:15px;"><span>Integration</span> <i class="icon-menu" title="" data-original-title="Main pages"></i></li>-->
                                
                    <!--            <li class="{{ Request::is('api_switch') ? 'active' : '' }}"><a href="{{route('apiswitch', ['type' => 'rintegration'])}}"><i class="las la-book text-danger"></i><b>Recharge Api Integration</b></a></li>-->
                    <!--            <li class="{{ Request::is('api_switch') ? 'active' : '' }}"><a href="{{route('apiswitch', ['type' => 'operator'])}}"><i class="las la-book text-danger"></i><b>Api Operator</b></a></li>-->
                    <!--            <li class="{{ Request::is('api_switch') ? 'active' : '' }}"><a href="{{route('apiswitch', ['type' => 'circle'])}}"><i class="las la-book text-danger"></i><b>Api Circle</b></a></li>-->

                               
                    <!--        </ul>-->
                    <!--    </li>-->
                    <!--@endif-->


                    <!-- @if (\Myhelper::can('invesment_show') && !Myhelper::hasRole('admin'))
<li>
                    <a href="#investment" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="lab la-wpforms text-danger iq-arrow-left"></i><span>Investment Service</span><i class="ri-arrow-right-s-line iq-arrow-right"></i></a>
                    <ul id="investment" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">

                        <li><a href="{{ url('investment/show') }}"><i class="las la-book text-danger"></i>Investment</a></li>

                    </ul>
                </li>
@endif -->

                    <!--@if (Myhelper::hasRole('admin'))-->
                    <!--    <li>-->
                    <!--        <a href="#investment" class="iq-waves-effect collapsed" data-toggle="collapse"-->
                    <!--            aria-expanded="false"><i-->
                    <!--                class="lab la-wpforms text-info iq-arrow-left"></i><b>Investment-->
                    <!--                Service</b><i class="ri-arrow-right-s-line iq-arrow-right"></i></a>-->
                    <!--        <ul id="investment" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">-->

                    <!--            <li><a href="{{ route('banner') }}"><i-->
                    <!--                        class="las la-book text-danger"></i><b>Banner</b></a></li>-->
                    <!--            <li><a href="{{ route('video') }}"><i class="las la-book text-warning"></i><b>Video</b></a>-->
                    <!--            </li>-->
                    <!--            <li><a href="{{ route('investment') }}"><i-->
                    <!--                        class="las la-book text-success"></i><b>Investment</b></a></li>-->
                    <!--        </ul>-->
                    <!--    </li>-->
                    <!--@endif-->
                    <!--@if (Myhelper::can(['aeps_fund_request', 'aeps_fund_view', 'aeps_fund_report']))-->
                    <!--    <li-->
                    <!--        class="{{ Request::is('fund/aeps') || Request::is('fund/addaccount') || Request::is('fund/aepsrequest') || Request::is('fund/payoutrequest') || Request::is('fund/aepsrequestall') ? 'active' : '' }}">-->
                    <!--        <a href="#aepsfund" class="iq-waves-effect collapsed " data-toggle="collapse"-->
                    <!--            aria-expanded="{{ Request::is('fund/aeps') || Request::is('fund/addaccount') || Request::is('fund/aepsrequest') || Request::is('fund/payoutrequest') || Request::is('fund/aepsrequestall') ? 'true' : 'false' }}"><i-->
                    <!--                class="ri-pages-line text-success iq-arrow-left"></i><b>AEPS Fund</b><i-->
                    <!--                class="ri-arrow-right-s-line iq-arrow-right"></i></a>-->
                            <!-- <span class="label bg-danger aepsfundCount {{ Myhelper::hasRole('admin') ? '' : 'hide' }}">0</span> -->
                    <!--        <ul id="aepsfund"-->
                    <!--            class="iq-submenu collapse {{ Request::is('fund/aeps') || Request::is('fund/addaccount') || Request::is('fund/aepsrequest') || Request::is('fund/payoutrequest') || Request::is('fund/aepsrequestall') ? 'show' : '' }}"-->
                    <!--            data-parent="#iq-sidebar-toggle">-->

                    <!--            @if (Myhelper::can(['aeps_fund_request']) && Myhelper::hasNotRole('admin'))-->
                    <!--                <li class="{{ Request::is('fund/aeps') ? 'active' : '' }}"><a-->
                    <!--                        href="{{ route('fund', ['type' => 'aeps']) }}"><i-->
                    <!--                            class="fa fa-question-circle text-danger"></i><b>Request</b></a></li>-->
                    <!--                {{-- <li class="{{Request::is('fund/addaccount') ? 'active' : '' }}"><a href="{{route('fund', ['type' => 'addaccount'])}}"><i class="fa fa-bank text-success"></i><b>Add Bank</b></a></li> --}}-->
                    <!--            @endif-->

                    <!--            @if (Myhelper::can(['aeps_fund_view']))-->
                    <!--                <li class="{{ Request::is('fund/aepsrequest') ? 'active' : '' }}"><a-->
                    <!--                        href="{{ route('fund', ['type' => 'aepsrequest']) }}"><i-->
                    <!--                            class="fa fa-exclamation-circle text-warning"></i><b>Pending Request</b></a>-->
                                        <!-- <span class="label bg-blue aepsfundCount {{ Myhelper::hasRole('admin') ? '' : 'hide' }}">0</span></a> -->
                    <!--                </li>-->
                    <!--                <li class="{{ Request::is('fund/payoutrequest') ? 'active' : '' }}"><a-->
                    <!--                        href="{{ route('fund', ['type' => 'payoutrequest']) }}"><i-->
                    <!--                            class="fa fa-tasks text-info"></i><b>Pending Payout Request</b></a>-->
                                        <!-- <span class="label bg-blue payoutfundCount {{ Myhelper::hasRole('admin') ? '' : 'hide' }}">0</span></a> -->
                    <!--                </li>-->
                    <!--            @endif-->

                    <!--            @if (Myhelper::can(['aeps_fund_report']))-->
                    <!--                <li class="{{ Request::is('fund/aepsrequestall') ? 'active' : '' }}"><a-->
                    <!--                        href="{{ route('fund', ['type' => 'aepsrequestall']) }}"><i-->
                    <!--                            class="fa fa-file text-danger"></i><b>Request Report</b></a></li>-->
                    <!--            @endif-->
                    <!--        </ul>-->
                    <!--    </li>-->
                    <!--@endif-->

                    <!--@if (Myhelper::can(['microatm_fund_request', 'microatm_fund_view', 'microatm_fund_report']))-->
                    <!--    <li-->
                    <!--        class="{{ Request::is('fund/microatm') || Request::is('fund/microatmrequest') || Request::is('fund/microatmrequestall') ? 'active' : '' }}">-->
                    <!--        <a href="#matmfund" class="iq-waves-effect collapsed" data-toggle="collapse"-->
                    <!--            aria-expanded="{{ Request::is('fund/microatm') || Request::is('fund/microatmrequest') || Request::is('fund/microatmrequestall') ? 'true' : 'false' }}"><i-->
                    <!--                class="ri-pantone-line text-warning iq-arrow-left"></i><b>MATM Fund </b><i-->
                    <!--                class="ri-arrow-right-s-line iq-arrow-right"></i></a>-->
                            <!-- <span class="label bg-danger microatmfundCount {{ Myhelper::hasRole('admin') ? '' : 'hide' }}">0</span>     -->
                    <!--        </a>-->
                    <!--        <ul id="matmfund"-->
                    <!--            class="iq-submenu collapse {{ Request::is('fund/microatm') || Request::is('fund/microatmrequest') || Request::is('fund/microatmrequestall') ? 'show' : '' }}"-->
                    <!--            data-parent="#iq-sidebar-toggle">-->

                    <!--            @if (Myhelper::can(['microatm_fund_request']))-->
                    <!--                <li class="{{ Request::is('fund/microatm') ? 'active' : '' }}"><a-->
                    <!--                        href="{{ route('fund', ['type' => 'microatm']) }}"><i-->
                    <!--                            class="fa fa-question-circle text-danger"></i><b>Request</b></a></li>-->
                    <!--            @endif-->

                    <!--            @if (Myhelper::can(['microatm_fund_view']))-->
                    <!--                <li class="{{ Request::is('fund/microatmrequest') ? 'active' : '' }}"><a-->
                    <!--                        href="{{ route('fund', ['type' => 'microatmrequest']) }}"><i-->
                    <!--                            class="fa fa-exclamation-circle text-warning"></i><b>Pending Request</b></a></li>-->
                    <!--            @endif-->

                    <!--            @if (Myhelper::can(['microatm_fund_report']))-->
                    <!--                <li class="{{ Request::is('fund/microatmrequestall') ? 'active' : '' }}"><a-->
                    <!--                        href="{{ route('fund', ['type' => 'microatmrequestall']) }}"><i-->
                    <!--                            class="fa fa-tasks text-info"></i><b>Request Report</b></a></li>-->
                    <!--            @endif-->
                    <!--        </ul>-->
                    <!--    </li>-->
                    <!--@endif-->

                    @if (Myhelper::can(['utiid_statement', 'aepsid_statement']))
                        <li
                            class="{{ Request::is('statement/fingagentid') || Request::is('statement/utiid') ? 'active' : '' }}">
                            <a href="#agentList" class="iq-waves-effect collapsed" data-toggle="collapse"
                                aria-expanded="{{ Request::is('statement/fingagentid') || Request::is('statement/utiid') ? 'true' : 'false' }}"><i
                                    class="fa fa-group text-info iq-arrow-left"></i><b>Agent List</b><i
                                    class="ri-arrow-right-s-line iq-arrow-right"></i></a>
                            <ul id="agentList"
                                class="iq-submenu collapse {{ Request::is('statement/fingagentid') || Request::is('statement/utiid') ? 'show' : '' }}"
                                data-parent="#iq-sidebar-toggle">
                                @if (Myhelper::can('aepsid_statement'))
                                    <li class="{{ Request::is('statement/fingagentid') ? 'active' : '' }}"><a
                                            href="{{ route('statement', ['type' => 'fingagentid']) }}"><i
                                                class="fa fa-user text-warning"></i><b>Aeps </b></a></li>
                                @endif

                                <!--@if (Myhelper::can('utiid_statement'))-->
                                <!--    <li class="{{ Request::is('statement/utiid') ? 'active' : '' }}"><a-->
                                <!--            href="{{ route('statement', ['type' => 'utiid']) }}"><i-->
                                <!--                class="fa fa-rupee text-danger"></i><b>UTI</b></a></li>-->
                                <!--@endif-->
                            </ul>
                        </li>
                    @endif
                    @if (Myhelper::can([
                            'account_statement',
                            'utiid_statement',
                            'utipancard_statement',
                            'recharge_statement',
                            'billpayment_statement',
                        ]))
                        <li
                            class="{{ Request::is('statement/aeps') || Request::is('statement/billpay') || Request::is('statement/money') || Request::is('statement/matm') || Request::is('statement/recharge') || Request::is('statement/utipancard') || Request::is('statement/loanenquiry') || Request::is('statement/cmsreport') ? 'active' : '' }}">
                            <a href="#txnreport" class="iq-waves-effect collapsed" data-toggle="collapse"
                                aria-expanded="{{ Request::is('statement/aeps') || Request::is('statement/billpay') || Request::is('statement/money') || Request::is('statement/matm') || Request::is('statement/recharge') || Request::is('statement/utipancard') || Request::is('statement/loanenquiry') || Request::is('statement/cmsreport') ? 'true' : 'false' }}"><i
                                    class="fa fa-files-o text-danger iq-arrow-left"></i><b>Transaction
                                    Report</b><i class="ri-arrow-right-s-line iq-arrow-right"></i></a>
                            <ul id="txnreport"
                                class="iq-submenu collapse {{ Request::is('statement/aeps') || Request::is('statement/billpay') || Request::is('statement/money') || Request::is('statement/matm') || Request::is('statement/recharge') || Request::is('statement/utipancard') || Request::is('statement/loanenquiry') || Request::is('statement/cmsreport') ? 'show' : '' }}"
                                data-parent="#iq-sidebar-toggle">
                                @if (Myhelper::can('aeps_statement'))
                                    <li class="{{ Request::is('statement/aeps') ? 'active' : '' }}"><a
                                            href="{{ route('statement', ['type' => 'aeps']) }}"><i
                                                class="fa fa-tasks text-danger"></i><b>Aeps Statement</b></a></li>
                                @endif

                                

                                @if (Myhelper::can('billpayment_statement'))
                                    <li class="{{ Request::is('statement/billpay') ? 'active' : '' }}"><a
                                            href="{{ route('statement', ['type' => 'billpay']) }}"><i
                                                class="fa fa-tasks text-success"></i><b>Billpay Statement</b></a></li>
                                @endif

                                @if (Myhelper::can('runpaisa_payout_service') || Myhelper::can('cyrus_payout_service') )
                                    <li class="{{ Request::is('statement/billpay') ? 'active' : '' }}"><a
                                            href="{{ route('statement', ['type' => 'payout']) }}"><i
                                                class="fa fa-tasks text-success"></i><b>Payout Statement</b></a></li>
                                @endif

                                <!--@if (Myhelper::can('runpaisa_payout_service') || Myhelper::can('cyrus_payout_service') )-->
                                <!--    <li class="{{ Request::is('statement/billpay') ? 'active' : '' }}"><a-->
                                <!--            href="{{ route('statement', ['type' => 'pg']) }}"><i-->
                                <!--                class="fa fa-tasks text-success"></i><b>PG Statement</b></a></li>-->
                                <!--@endif-->
                                
                                @if (Myhelper::can('money_statement'))
                                    <li class="{{ Request::is('statement/money') ? 'active' : '' }}"><a
                                            href="{{ route('statement', ['type' => 'money']) }}"><i
                                                class="fa fa-file-text text-warning"></i><b>DMT Statement</b></a></li>
                                @endif 

                                <!--@if (Myhelper::can('matm_fund_report'))-->
                                <!--    <li class="{{ Request::is('statement/matm') ? 'active' : '' }}"><a-->
                                <!--            href="{{ route('statement', ['type' => 'matm']) }}"><i-->
                                <!--                class="fa fa-floppy-o text-info"></i><b>Micro ATM Statement</b></a></li>-->
                                <!--@endif-->

                                @if (Myhelper::can('recharge_statement'))
                                    <li class="{{ Request::is('statement/recharge') ? 'active' : '' }}"><a
                                            href="{{ route('statement', ['type' => 'recharge']) }}"><i
                                                class="fa fa-rupee text-danger"></i><b>Recharge Statement </b></a></li>
                                @endif

                                <!--@if (Myhelper::can('utipancard_statement'))-->
                                <!--    <li class="{{ Request::is('statement/utipancard') ? 'active' : '' }}"><a-->
                                <!--            href="{{ route('statement', ['type' => 'utipancard']) }}"><i-->
                                <!--                class="fa fa-rupee text-danger"></i><b>Uti Pancard-->
                                <!--            Statement</b></a></li>-->
                                <!--@endif-->

                                {{-- @if (Myhelper::hasRole('admin'))
                        <li class="{{Request::is('statement/runpaisa') ? 'active' : '' }}"><a href="{{ route('statement', ['type' => 'runpaisa']) }}"><i class="fa fa-rupee text-danger"></i><b>RUN Paisa PG
                                Statement</b></a></li>
                        @endif --}}

                                {{-- <li class="{{Request::is('statement/loanenquiry') ? 'active' : '' }}"><a href="{{route('statement', ['type' => 'loanenquiry'])}}"><i class="fa fa-rupee text-success"></i><b>Loanenquiry Statement </b></a></li>
                        <li class="{{Request::is('statement/cmsreport') ? 'active' : '' }}"><a href="{{route('statement', ['type' => 'cmsreport'])}}"><i class="ri-map-pin-time-line text-warning"></i><b>CMS Report</a></b></a></li> --}}
                            </ul>
                        </li>
                    @endif
                    @if (Myhelper::can(['account_statement', 'awallet_statement']))
                        <li
                            class="{{ Request::is('statement/account') || Request::is('statement/awallet') ? 'active' : '' }}">
                            <a href="#walletreport" class="iq-waves-effect collapsed" data-toggle="collapse"
                                aria-expanded="{{ Request::is('statement/account') || Request::is('statement/awallet') ? 'true' : 'false' }}"><i
                                    class="fa fa-history text-success iq-arrow-left"></i><b>Wallet History</b><i
                                    class="ri-arrow-right-s-line iq-arrow-right"></i></a>
                            <ul id="walletreport"
                                class="iq-submenu collapse {{ Request::is('statement/account') || Request::is('statement/awallet') ? 'show' : '' }}"
                                data-parent="#iq-sidebar-toggle">
                                @if (Myhelper::can('account_statement'))
                                    <li class="{{ Request::is('statement/account') ? 'active' : '' }}"><a
                                            href="{{ route('statement', ['type' => 'account']) }}"><i
                                                class="fa fa-briefcase text-danger"></i><b>Main Wallet</b></a></li>
                                @endif
                                <!--@if (Myhelper::can('awallet_statement'))-->
                                <!--    <li class="{{ Request::is('statement/awallet') ? 'active' : '' }}"><a-->
                                <!--            href="{{ route('statement', ['type' => 'awallet']) }}"><i-->
                                <!--                class="fa fa-google-wallet text-success"></i><b>Aeps Wallet</b></a></li>-->
                                <!--@endif-->
                                <!--@if (Myhelper::can('iwallet_statement'))-->
                                <!--    <li class="{{ Request::is('statement/iwallet') ? 'active' : '' }}"><a-->
                                <!--            href="{{ route('statement', ['type' => 'iwallet']) }}"><i-->
                                <!--                class="fa fa-google-wallet text-success"></i><b>Investment Wallet</b></a></li>-->
                                <!--@endif-->
                            </ul>
                        </li>
                    @endif

                    @if (Myhelper::can('Complaint'))
                        <li class="{{ Request::is('complaint') ? 'active' : '' }}">
                            <a href="{{ route('complaint') }}" class="iq-waves-effect"><i
                                    class="fa fa-address-card text-warning iq-arrow-left"></i><b>Complaints</b></a>
                        </li>
                    @endif

                    <!-- @if (Myhelper::hasRole('retailer'))
<li class="{{ Request::is('loanform') ? 'active' : '' }}">
                    <a href="{{ route('loanform') }}" class="iq-waves-effect"><i class="fa fa-fax text-danger iq-arrow-left"></i><span>Loan Enquiry</span></a>
                </li>
@endif -->


                    @if (Myhelper::can(['setup_bank', 'api_manager', 'setup_operator']))
                        <li class="{{ Request::is('setup/*') ? 'active' : '' }}">
                            <a href="#setuptools" class="iq-waves-effect collapsed" data-toggle="collapse"
                                aria-expanded="{{ Request::is('setup/*') ? 'true' : 'false' }}"><i
                                    class="fa fa-suitcase text-info iq-arrow-left"></i><b>Setup Tools</b><i
                                    class="ri-arrow-right-s-line iq-arrow-right"></i></a>
                            <ul id="setuptools"
                                class="iq-submenu collapse {{ Request::is('setup/*') ? 'show' : '' }}"
                                data-parent="#iq-sidebar-toggle">
                                @if (Myhelper::hasRole('admin') || Myhelper::hasRole('subadmin'))
                                    <li class="{{ Request::is('securedata') ? 'active' : '' }}"><a
                                            href="{{ route('securedata') }}"><i
                                                class="fa fa-sign-out text-warning"></i><b>Mobile User Logout</b></a></li>
                                @endif
                                @if (Myhelper::can('api_manager'))
                                    <li class="{{ Request::is('setup/api') ? 'active' : '' }}"><a
                                            href="{{ route('setup', ['type' => 'api']) }}"><i
                                                class="las la-calendar text-info"></i><b>API Manager</b></a></li>
                                @endif
                                @if (Myhelper::can('setup_bank'))
                                    <li class="{{ Request::is('setup/bank') ? 'active' : '' }}"><a
                                            href="{{ route('setup', ['type' => 'bank']) }}"><i
                                                class="fa fa-bank text-danger"></i><b>Bank Account</b></a></li>
                                @endif
                                @if (Myhelper::can('complaint_subject'))
                                    <li class="{{ Request::is('setup/complaintsub') ? 'active' : '' }}"><a
                                            href="{{ route('setup', ['type' => 'complaintsub']) }}"><i
                                                class="las la-calendar text-warning"></i><b>Complaint Subject</b></a></li>
                                @endif
                                @if (Myhelper::can('setup_operator'))
                                    <li class="{{ Request::is('setup/operator') ? 'active' : '' }}"><a
                                            href="{{ route('setup', ['type' => 'operator']) }}"><i
                                                class="fa fa-user text-success"></i><b>Operator Manager</b></a></li>
                                @endif
                                @if (Myhelper::hasRole('admin'))
                                    <li class="{{ Request::is('setup/portalsetting') ? 'active' : '' }}"><a
                                            href="{{ route('setup', ['type' => 'portalsetting']) }}"><i
                                                class="fa fa-cog text-info"></i><b>Portal Setting</b></a></li>
                                    <li class="{{ Request::is('setup/links') ? 'active' : '' }}"><a
                                            href="{{ route('setup', ['type' => 'links']) }}"><i
                                                class="fa fa-link text-danger"></i><b>Quick Links</b></a></li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    <!--
                @if (Myhelper::can(['mapping_manager']))
<li >
                    <a href="#mappingManager" class="iq-waves-effect collapsed" data-toggle="collapse" aria-expanded="false"><i class="fa fa-user-circle-o text-warning iq-arrow-left"></i><span>Mapping Manager</span><i class="ri-arrow-right-s-line iq-arrow-right"></i></a>
                    <ul id="mappingManager" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                        <li><a href="{{ route('setup', ['type' => 'mappingid']) }}"><i class="fa fa-user-circle-o text-danger"></i>Mapping Manager</a></li>
                    </ul>
                </li>
@endif -->

                    <li class="{{ Request::is('profile') || Request::is('certificate') ? 'active' : '' }}">
                        <a href="#accountSetting" class="iq-waves-effect collapsed" data-toggle="collapse"
                            aria-expanded="{{ Request::is('profile') || Request::is('certificate') ? 'true' : 'false' }}"><i
                                class="fa fa-gear text-danger iq-arrow-left"></i><b>Account Settings</b><i
                                class="ri-arrow-right-s-line iq-arrow-right"></i></a>
                        <ul id="accountSetting"
                            class="iq-submenu collapse {{ Request::is('profile') || Request::is('certificate') ? 'show' : '' }}"
                            data-parent="#iq-sidebar-toggle">
                            <li class="{{ Request::is('profile') ? 'active' : '' }}"><a
                                    href="{{ route('profile') }}"><i
                                        class="fa fa-address-card text-danger"></i><b>Profile Setting</b></a></li>
                            <li class="{{ Request::is('certificate') ? 'active' : '' }}"><a
                                    href="{{ route('certificate') }}"><i
                                        class="fa fa-file text-success"></i><b>Certificate</b></a></li>
                        </ul>
                    </li>
                    @if (Myhelper::hasRole('apiuser') && Myhelper::can('apiuser_acc_manager'))
                        <li class="{{ Request::is('apisetup/*') ? 'active' : '' }}">
                            <a href="#apiSetting" class="iq-waves-effect collapsed" data-toggle="collapse"
                                aria-expanded="{{ Request::is('apisetup/*') ? 'true' : 'false' }}"><i
                                    class="ri-record-circle-line text-info iq-arrow-left"></i><b>Api
                                    Settings</b><i class="ri-arrow-right-s-line iq-arrow-right"></i></a>
                            <ul id="apiSetting"
                                class="iq-submenu collapse {{ Request::is('apisetup/*') ? 'show' : '' }}"
                                data-parent="#iq-sidebar-toggle">
                                <li class="{{ Request::is('apisetup/setting') ? 'active' : '' }}"><a
                                        href="{{ route('apisetup', ['type' => 'setting']) }}"><i
                                            class="las la-calendar text-danger"></i><b>Callback & Token</b></a></li>
                                <li class="{{ Request::is('apisetup/operator') ? 'active' : '' }}"><a
                                        href="{{ route('apisetup', ['type' => 'operator']) }}"><i
                                            class="las la-calendar text-success"></i><b>Operator Code</b></a></li>
                                {{-- <li class="{{Request::is('apisetup/document') ? 'active' : '' }}"><a href="{{route('apisetup', ['type' => 'document'])}}"><i class="las la-calendar text-warning"></i><b>Api Documents</b></a>
                </li> --}}
                                <!-- </ul> -->
                        </li>
                    @endif

                    <li>
                        <a href="#driverLink" class="iq-waves-effect collapsed" data-toggle="collapse"
                            aria-expanded="false"><i class="fa fa-link text-info iq-arrow-left"></i><b>Driver
                                Links</b><i class="ri-arrow-right-s-line iq-arrow-right"></i></a>
                        <ul id="driverLink" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                            <li><a href="https://drive.google.com/drive/folders/10RF-h2b9lVoa_d692e5CUVnpi7Gxwr7R?usp=sharing"
                                    target="_blank"><i class="fa fa-meetup text-danger"></i><b>Mantra</b></a></li>
                            <li><a href="https://drive.google.com/open?id=13FbVSOuplWlJNhwKMjTmKHkyA5CZPkh0"
                                    target="_blank"><i class="fa fa-medium text-success"></i><b>Morpho</b></a></li>
                            <!--<li><a href="https://drive.google.com/open?id=19FZWSM3-vMdyd-_CpggvpyBPLaTSZcZa" target="_blank"><b>Startek</b></a></li>-->
                            <li><a href="https://drive.google.com/open?id=1-LJfFXIvgE3ZLIm5fmYGjz95IvUnQYk4"
                                    target="_blank"><i class="fa fa-print text-warning"></i><b>Tatvik TMF20</b></a></li>
                        </ul>
                    </li>

                    @if (Myhelper::hasRole('admin'))
                        <li class="{{ Request::is('tools/*') ? 'active' : '' }}">
                            <a href="#roles" class="iq-waves-effect collapsed" data-toggle="collapse"
                                aria-expanded="{{ Request::is('tools/*') ? 'true' : 'flase' }}"><i
                                    class="ri-record-circle-line iq-arrow-left text-danger"></i><b>Roles &
                                    Permissions</b><i class="ri-arrow-right-s-line iq-arrow-right"></i></a>
                            <ul id="roles"
                                class="iq-submenu collapse {{ Request::is('tools/*') ? 'show' : '' }}"
                                data-parent="#iq-sidebar-toggle">
                                <li class="{{ Request::is('tools/roles') ? 'active' : '' }}"><a
                                        href="{{ route('tools', ['type' => 'roles']) }}"><i
                                            class="fa fa-user-o text-info"></i><b>Roles</b></a></li>
                                <li class="{{ Request::is('tools/permissions') ? 'active' : '' }}"><a
                                        href="{{ route('tools', ['type' => 'permissions']) }}"><i
                                            class="fa fa-address-book text-warning"></i><b>Permission</b></a></li>

                            </ul>
                        </li>
                    @endif
                @endif
            </ul>
        </nav>
        <div class="p-3"></div>
    </div>
</div>
