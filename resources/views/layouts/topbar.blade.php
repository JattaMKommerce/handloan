<div class="iq-top-navbar mb-5">
   <div class="iq-navbar-custom">
   
      <nav class="navbar navbar-expand-lg navbar-light p-0">
         <div class="iq-menu-bt d-flex align-items-center">
            <div class="wrapper-menu">
               <div class="main-circle"><i class="ri-menu-line"></i></div>
               <div class="hover-circle"><i class="ri-close-fill"></i></div>
            </div>
            <div class="iq-navbar-logo d-flex justify-content-between ml-3">
             
            @if (Auth::user()->company->logo)
            <a class="header-logo" href="{{route('home')}}">
                <img src="{{asset('')}}/logos/{{Auth::user()->company->logo}}"
                 class=" img-fluid rounded" alt="">
            </a>

        @else

               <a href="{{route('home')}}" class="header-logo">
                  <img src="" class="img-fluid rounded" alt="">
                  <span>AmtechPe</span>
               </a>
               @endif
            </div>
         </div>

         <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-label="Toggle navigation">
            <i class="ri-menu-3-line"></i>
         </button>

        
         <div class="collapse navbar-collapse" id="navbarSupportedContent">

          @if ($mydata['news'] != '' && $mydata['news'] != null)
                <h4 class="col-md-9 text-danger"><marquee style="height: 25px" onmouseover="this.stop();" onmouseout="this.start();">{{$mydata['news']}}</marquee></h4>
                @endif
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
                                 <div class="ri-wallet-line">
                                    {{-- <img class="avatar-40 rounded" src="{{asset('')}}theme/images/user/01.jpg" alt=""> --}}
                                 </div>
                                 <div class="media-body ml-3">
                                    <h6 class="mb-0 ">Main Wallet</h6>

                                    <p class="mb-0" id="mainwallet"> &#8377; {{Auth::user()->mainwallet}} /-</p>
                                 </div>
                                 
                              </div>
                           </a>
                           <a href="#" class="iq-sub-card">
                              <div class="media align-items-center">
                                 <div class="ri-wallet-line">
                                    {{-- <img class="avatar-40 rounded" src="{{asset('')}}theme/images/user/02.jpg" alt=""> --}}
                                 </div>
                                 <div class="media-body ml-3">
                                    <h6 class="mb-0 ">AEPS Wallet</h6>

                                    <p class="mb-0" id="aepswallet"> &#8377; {{Auth::user()->aepsbalance}} /-</p>
                                 </div>
                              </div>
                           </a>

                           <!--<a href="#" class="iq-sub-card">-->
                           <!--   <div class="media align-items-center">-->
                           <!--      <div class="ri-paypal-line">-->
                           <!--         {{-- <img class="avatar-40 rounded" src="{{asset('')}}theme/images/user/02.jpg" alt=""> --}}-->
                           <!--      </div>-->
                           <!--      <div class="media-body ml-3">-->
                           <!--         <h6 class="mb-0 ">Investment Wallet</h6>-->

                           <!--         <p class="mb-0"> &#8377; {{Auth::user()->investment_wallet}} /-</p>-->
                           <!--      </div>-->
                           <!--   </div>-->
                           <!--</a>-->
                        </div>
                     </div>
                  </div>
               </li>
                <li class="nav-item nav-icon">
                    
                         <a href="javascript::void(0)" onclick="getbalance()" class="search-toggle iq-waves-effect bg-primary rounded"><i class="ri-restart-line"></i></a>
                    
                   
                </li>
               {{-- <li class="nav-item nav-icon">
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
               </li> --}}

            </ul>
         </div>
         <ul class="navbar-list">
            <li class="line-height">
               <a href="#" class="search-toggle iq-waves-effect d-flex align-items-center">
                  <img src="{{asset('')}}kyc/{{ Auth::user()->profile }}" class="img-fluid rounded mr-3" alt="user">
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