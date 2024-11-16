@extends('layouts.app')
@section('title', ucwords($user->name) . " Profile")
@section('bodyClass', "has-detached-left")
@section('pagetitle', ucwords($user->name) . " Profile")

@section('content')

<div class="row">
    <div class="col-sm-12 col-lg-12">

        <div class="iq-card">
            <div class="iq-card-header d-flex justify-content-between">
                <div class="iq-header-title">
                    <h4 class="card-title">My Profile</h4>
                </div>
            </div>
            <div class="iq-card-body">
                <ul class="nav nav-pills nav-tabs mb-3 nav-fill" id="pills-tab-1" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="pills-home-tab-fill" data-toggle="pill" href="#profile" role="tab" aria-controls="pills-home" aria-selected="true">Profile Details</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="pills-profile-tab-fill" data-toggle="pill" href="#kycdata" role="tab" aria-controls="pills-profile" aria-selected="false">KYC Details</a>
                    </li>
                    @if ((Auth::id() == $user->id && Myhelper::can('password_reset')) || Myhelper::can('member_password_reset'))
                    <li class="nav-item">
                        <a class="nav-link" id="pills-contact-tab-fill" data-toggle="pill" href="#settings" role="tab" aria-controls="pills-contact" aria-selected="false">Password Manager</a>
                    </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link" id="pills-contact-tab-fill" data-toggle="pill" href="#pinChange" role="tab" aria-controls="pills-contact" aria-selected="false">PIN Manager</a>
                    </li>

                    @if (\Myhelper::hasRole('admin'))
                    <li class="nav-item">
                        <a class="nav-link" id="pills-contact-tab-fill" data-toggle="pill" href="#bankdata" role="tab" aria-controls="pills-contact" aria-selected="false">Bank Details</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="pills-contact-tab-fill" data-toggle="pill" href="#rolemanager" role="tab" aria-controls="pills-contact" aria-selected="false">Role Manager</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="pills-contact-tab-fill" data-toggle="pill" href="#mapping" role="tab" aria-controls="pills-contact" aria-selected="false">Mapping Manager</a>
                    </li>
                    @endif
                </ul>
                <div class="tab-content" id="pills-tabContent-1">
                    <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="pills-home-tab-fill">
                        <form id="profileForm" action="{{route('profileUpdate')}}" method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <input type="hidden" name="id" value="{{$user->id}}">
                            <input type="hidden" name="actiontype" value="profile">

                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label>Name</label>
                                    <input type="text" name="name" class="form-control" value="{{$user->name}}" required="" placeholder="Enter Value">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Mobile</label>
                                    <input type="number" {{ Myhelper::hasNotRole('admin') ? 'disabled=""' :'name=mobile'}} required="" value="{{$user->mobile}}" class="form-control" placeholder="Enter Value">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control" value="{{$user->email}}" value="" required="" placeholder="Enter Value">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label>State</label>
                                    <select name="state" class="form-control" required="">
                                        <option value="">Select State</option>
                                        @foreach ($state as $state)
                                        <option value="{{$state->state}}">{{$state->state}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>City</label>
                                    <input type="text" name="city" class="form-control" value="{{$user->city}}" required="" placeholder="Enter Value">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>PIN Code</label>
                                    <input type="number" name="pincode" class="form-control" value="{{$user->pincode}}" required="" maxlength="6" minlength="6" placeholder="Enter Value">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label>Gender</label>
                                    <select name="gender" class="form-control" required="">
                                        <option value="">Select Gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Address</label>
                                    <input type="text" name="address" class="form-control" rows="3" required="" placeholder="Enter Value" value="{{$user->address}}"></input>
                                </div>
                            </div>

                            @if(Myhelper::hasRole('admin'))
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label>Security PIN</label>
                                    <input type="password" name="mpin" autocomplete="off" class="form-control" required="">
                                </div>
                            </div>
                            @endif

                            @if ((Auth::id() == $user->id && Myhelper::can('profile_edit')) || Myhelper::can('member_profile_edit'))
                            <div class="row">
                                <div class="col-sm-8"></div>
                                <div class="col-sm-4">
                                    <button class="btn btn-primary pull-right mb-5" type="submit" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Updating...">Update Profile</button>

                                </div>
                            </div>

                            @endif

                        </form>
                    </div>

                    <div class="tab-pane fade" id="kycdata" role="tabpanel" aria-labelledby="pills-profile-tab-fill">
                        <form id="kycForm" action="{{route('profileUpdate')}}" method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <input type="hidden" name="id" value="{{$user->id}}">
                            <input type="hidden" name="actiontype" value="profile">
                            <div class="row">
                                <div class="form-group col-md-3">
                                    <label>Shop Name</label>
                                    <input type="text" name="shopname" class="form-control" value="{{$user->shopname}}" required="" placeholder="Enter Value">
                                </div>

                                <div class="form-group col-md-3">
                                    <label>GST Number</label>
                                    <input type="text" name="gstin" class="form-control" value="{{$user->gstin}}" placeholder="Enter Value">
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Aadhaar Card Number</label>
                                    <input type="text" name="aadharcard" class="form-control" value="{{$user->aadharcard}}" required="" placeholder="Enter Value" maxlength="12" minlength="12" @if (Myhelper::hasNotRole('admin') && $user->kyc == "verified")
                                    disabled=""
                                    @endif
                                    >
                                </div>
                                <div class="form-group col-md-3">
                                    <label>PAN Card Number</label>
                                    <input type="text" name="pancard" class="form-control" value="{{$user->pancard}}" required="" placeholder="Enter Value" @if (Myhelper::hasNotRole('admin') && $user->kyc == "verified")
                                    disabled=""
                                    @endif
                                    >
                                </div>
                            </div>

                            <div class="row">
                                @if ($user->kyc != "verified")
                                <div class="form-group col-md-4">
                                    <label>PAN Card Picture</label>
                                    <input type="file" name="pancardpics" class="form-control" value="" placeholder="Enter Value">
                                </div>

                                <div class="form-group col-md-4">
                                    <label>Aadhaar Card Picture</label>
                                    <input type="file" name="aadharcardpics" class="form-control" value="" placeholder="Enter Value">
                                </div>
                                @endif
                            </div>
                            @if(Myhelper::hasRole('admin'))
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label>Security PIN</label>
                                    <input type="password" name="mpin" autocomplete="off" class="form-control" required="">
                                </div>
                            </div>
                            @endif

                            @if ((Auth::id() == $user->id && Myhelper::can('profile_edit')) || Myhelper::can('member_profile_edit'))

                            <div class="row">
                                <div class="col-sm-8"></div>
                                <div class="col-sm-4">
                                    <button class="btn btn-primary pull-right" type="submit" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Updating...">Update Profile</button>

                                </div>
                            </div>
                            @endif

                            <div class="row">

                                <ul class="profile-img-gallary d-flex flex-wrap p-0 m-0">
                                    @if($user->pancardpic)
                                    <li class="col-md-4 col-6 pl-2 pr-0 pb-3">
                                        <a href="{{asset('kyc')}}/{{$user->pancardpic}}">
                                            <img src="{{asset('kyc')}}/{{$user->pancardpic}}" style="width:100px !important;height: 80px;" alt="Pan Card" class="img-fluid w-100" /></a>
                                        <h6 class="mt-2">Pan Card</h6>
                                    </li>
                                    @endif

                                    @if($user->aadharcardpic)
                                    <li class="col-md-4 col-6 pl-2 pr-0 pb-3">
                                        <a href="{{asset('kyc')}}/{{$user->aadharcardpic}}"><img src="{{asset('kyc')}}/{{$user->aadharcardpic}}" style="width:100px !important;height: 80px;" alt="Aadhaar Card" class="img-fluid w-100" /></a>
                                        <h6 class="mt-2">Adhaar Card</h6>
                                    </li>
                                    @endif

                                    @if($user->livepic)
                                    <li class="col-md-4 col-6 pl-2 pr-0 pb-3">
                                        <a href="{{asset('kyc')}}/{{$user->livepic}}"><img src="{{asset('kyc')}}/{{$user->livepic}}" alt="LivePic" style="width:100px !important;height: 80px;" class="img-fluid w-100" /></a>
                                        <h6 class="mt-2">Live Photo</h6>
                                    </li>
                                    @endif

                                    @if($user->profile)
                                    <li class="col-md-4 col-6 pl-2 pr-0 pb-3">
                                        <a href="{{asset('kyc')}}/{{$user->profile}}"><img src="{{asset('kyc')}}/{{$user->profile}}" alt="Profile Photo" style="width:100px !important;height: 80px;" class="img-fluid w-100" /></a>
                                        <h6 class="mt-2">Passport Size Photo</h6>
                                    </li>
                                    @endif

                                    @if($user->shop_photo)
                                    <li class="col-md-4 col-6 pl-2 pr-0 pb-3">
                                        <a href="{{asset('kyc')}}/{{$user->shop_photo}}"><img src="{{asset('kyc')}}/{{$user->shop_photo}}" alt="Shop Photo" style="width:100px !important;height: 80px;" class="img-fluid w-100" /></a>
                                        <h6 class="mt-2">Shop Picture</h6>
                                    </li>
                                    @endif

                                    @if($user->signature)
                                    <li class="col-md-4 col-6 pl-2 pr-0 pb-3">
                                        <a href="{{asset('kyc')}}/{{$user->signature}}"><img src="{{asset('kyc')}}/{{$user->signature}}" alt="Signature" style="width:100px !important;height: 80px;" class="img-fluid w-100" /></a>
                                        <h6 class="mt-2">Signature Picture </h6>
                                    </li>
                                    @endif

                                </ul>


                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="settings" role="tabpanel" aria-labelledby="pills-contact-tab-fill">
                        <form id="passwordForm" action="{{route('profileUpdate')}}" method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <input type="hidden" name="id" value="{{$user->id}}">
                            <input type="hidden" name="actiontype" value="password">
                            <div class="panel panel-default">
                                <!-- <div class="panel-heading">
                                            <h3 class="panel-title pull-left">Password Reset</h3>
                                            @if(Myhelper::hasRole('admin'))
                                            <p class="pull-right">Current Password - {{$user->passwordold}}</p>
                                            @endif
                                            <div class="clearfix"></div>
                                        </div> -->
                                <div class="panel-body p-b-0">
                                    <div class="row">
                                        @if (Auth::id() == $user->id || (Myhelper::hasNotRole('admin') && !Myhelper::can('member_password_reset')))
                                        <div class="form-group col-md-4">
                                            <label>Old Password</label>
                                            <input type="password" name="oldpassword" class="form-control" required="" placeholder="Enter Value">
                                        </div>
                                        @endif

                                        <div class="form-group col-md-4">
                                            <label>New Password</label>
                                            <input type="password" name="password" id="password" class="form-control" required="" placeholder="Enter Value">
                                        </div>
                                        @if (Auth::id() == $user->id || (Myhelper::hasNotRole('admin') && !Myhelper::can('member_password_reset')))
                                        <div class="form-group col-md-4">
                                            <label>Confirmed Password</label>
                                            <input type="password" name="password_confirmation" class="form-control" required="" placeholder="Enter Value">
                                        </div>
                                        @endif
                                    </div>
                                    @if(Myhelper::hasRole('admin'))
                                    <div class="row">
                                        <div class="form-group col-md-4">
                                            <label>Security PIN</label>
                                            <input type="password" name="mpin" autocomplete="off" class="form-control" required="">
                                        </div>
                                    </div>
                                    @endif
                                </div>

                                <div class="row">
                                    <div class="col-sm-8"></div>
                                    <div class="col-sm-4">
                                        <button class="btn btn-primary pull-right" type="submit" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Resetting...">Password Reset</button>

                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="tab-pane fade" id="pinChange" role="tabpanel" aria-labelledby="pills-contact-tab-fill">
                        <form id="pinForm" action="{{route('setpin')}}" method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <input type="hidden" name="id" value="{{$user->id}}">
                            <input type="hidden" name="mobile" value="{{$user->mobile}}">
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label>New PIN</label>
                                    <input type="password" name="pin" id="pin" class="form-control" required="" placeholder="Enter Value">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Confirmed PIN</label>
                                    <input type="password" name="pin_confirmation" class="form-control" required="" placeholder="Enter Value">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>OTP</label>
                                    <input type="password" name="otp" class="form-control" Placeholder="Otp" required>
                                    <a href="javascript:void(0)" onclick="OTPRESEND()" class="text-primary pull-right fw-bloder">Send OTP</a>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-sm-8"></div>
                                <div class="col-sm-4">
                                    <button class="btn btn-primary float-right" type="submit" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Resetting...">Password Reset</button>

                                </div>
                            </div>
                        </form>

                    </div>

                    @if (\Myhelper::hasRole('admin'))
                    <div class="tab-pane fade" id="bankdata" role="tabpanel" aria-labelledby="pills-contact-tab-fill">
                        <form id="bankForm" action="{{route('profileUpdate')}}" method="post">
                            {{ csrf_field() }}
                            <input type="hidden" name="id" value="{{$user->id}}">
                            <input type="hidden" name="actiontype" value="bankdata">
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label>Account Number</label>
                                    <input type="text" name="account" class="form-control" value="{{$user->account}}" required="" placeholder="Enter Value">
                                </div>

                                <div class="form-group col-md-4">
                                    <label>Bank Name</label>
                                    <input type="text" name="bank" class="form-control" value="{{$user->bank}}" placeholder="Enter Value">
                                </div>

                                <div class="form-group col-md-4">
                                    <label>IFSC Code</label>
                                    <input type="text" name="ifsc" class="form-control" value="{{$user->ifsc}}" required="" placeholder="Enter Value">
                                </div>
                            </div>

                            @if(Myhelper::hasRole('admin'))
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label>Security PIN</label>
                                    <input type="password" name="mpin" autocomplete="off" class="form-control" required="">
                                </div>
                            </div>
                            @endif

                            <div class="row">
                                <div class="col-sm-8"></div>
                                <div class="col-sm-4">
                                    <button class="btn btn-primary pull-right" type="submit" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Changing...">Change</button>

                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="tab-pane fade" id="rolemanager" role="tabpanel" aria-labelledby="pills-contact-tab-fill">
                        <form id="roleForm" action="{{route('profileUpdate')}}" method="post">
                            {{ csrf_field() }}
                            <input type="hidden" name="id" value="{{$user->id}}">
                            <input type="hidden" name="actiontype" value="rolemanager">
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label>Member Role</label>
                                    <select name="role_id" class="form-control" required="">
                                        <option value="">Select Role</option>
                                        @foreach ($roles as $role)
                                        <option value="{{$role->id}}">{{$role->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @if(Myhelper::hasRole('admin'))

                                <div class="form-group col-md-4">
                                    <label>Security PIN</label>
                                    <input type="password" name="mpin" autocomplete="off" class="form-control" required="">
                                </div>

                                @endif
                            </div>

                            <div class="row">
                                <div class="col-sm-8"></div>
                                <div class="col-sm-4">
                                    <button class="btn btn-primary pull-right" type="submit" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Changing...">Change</button>

                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="tab-pane fade" id="mapping" role="tabpanel" aria-labelledby="pills-contact-tab-fill">
                        <form id="memberForm" action="{{route('profileUpdate')}}" method="post">
                            {{ csrf_field() }}
                            <input type="hidden" name="id" value="{{$user->id}}">
                            <input type="hidden" name="actiontype" value="mapping">
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label>Parent Member</label>
                                    <select name="parent_id" class="form-control" required="">
                                        <option value="">Select Member</option>
                                        @foreach ($parents as $parent)
                                        <option value="{{$parent->id}}">{{$parent->name}} ({{$parent->mobile}}) ({{$parent->role->name}})</option>
                                        @endforeach
                                    </select>
                                </div>
                                @if(Myhelper::hasRole('admin'))
                                <div class="form-group col-md-4">
                                    <label>Security PIN</label>
                                    <input type="password" name="mpin" autocomplete="off" class="form-control" required="">
                                </div>
                                @endif
                            </div>
                            <div class="row">
                                <div class="col-sm-8"></div>
                                <div class="col-sm-4">
                                    <button class="btn btn-primary pull-right" type="submit" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Changing...">Change</button>

                                </div>
                            </div>
                        </form>
                    </div>

                    @endif
                </div>
            </div>
        </div>
    </div>

</div>

@endsection

@push('script')

<script type="text/javascript">
    $(document).ready(function() {
        $('[name="state"]').val('{{$user->state}}').trigger('change');
        $('[name="gender"]').val('{{$user->gender}}').trigger('change');
        @if(\Myhelper::hasRole('admin'))
        $('[name="parent_id"]').val('{{$user->parent_id}}').trigger('change');
        $('[name="role_id"]').val('{{$user->role_id}}').trigger('change');
        @endif
        // $('[href="#{{$tab}}"]').trigger('click');
        $("#profileForm").validate({
            rules: {
                name: {
                    required: true,
                },
                mobile: {
                    required: true,
                    minlength: 10,
                    number: true,
                    maxlength: 10
                },
                email: {
                    required: true,
                    email: true
                },
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
                }
            },
            messages: {
                name: {
                    required: "Please enter name",
                },
                mobile: {
                    required: "Please enter mobile",
                    number: "Mobile number should be numeric",
                    minlength: "Your mobile number must be 10 digit",
                    maxlength: "Your mobile number must be 10 digit"
                },
                email: {
                    required: "Please enter email",
                    email: "Please enter valid email address",
                },
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
                var form = $('form#profileForm');
                form.find('span.text-danger').remove();
                $('form#profileForm').ajaxSubmit({
                    dataType: 'json',
                    beforeSubmit: function() {
                        form.find('button:submit').html('loading...').attr("disabled",true).addClass('btn-secondary');
                    },
                    complete: function() {
                        form.find('button:submit').html('Update Profile').attr("disabled",false).removeClass('btn-secondary');
                    },
                    success: function(data) {
                        if (data.status == "success") {
                            notify("Profile Successfully Updated", 'success');
                        } else {
                            notify(data.status, 'warning');
                        }
                    },
                    error: function(errors) {
                        showError(errors, form.find('.panel-body'));
                    }
                });
            }
        });

        $("#kycForm").validate({
            rules: {
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
                }
            },
            messages: {
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
                var form = $('form#kycForm');
                form.find('span.text-danger').remove();
                form.ajaxSubmit({
                    dataType: 'json',
                    beforeSubmit: function() {
                        form.find('button:submit').html('loading...').attr("disabled",true).addClass('btn-secondary');
                    },
                    complete: function() {
                        form.find('button:submit').html('Update Profile').attr("disabled",false).removeClass('btn-secondary');
                    },
                    success: function(data) {
                        if (data.status == "success") {
                            notify("Profile Successfully Updated", 'success');
                        } else {
                            notify(data.status, 'warning');
                        }
                    },
                    error: function(errors) {
                        showError(errors, form.find('.panel-body'));
                    }
                });
            }
        });

        $("#passwordForm").validate({
            rules: {
                @if(Auth::id() == $user -> id || (Myhelper::hasNotRole('admin') && !Myhelper::can('member_password_reset')))
                oldpassword: {
                    required: true,
                },
                password_confirmation: {
                    required: true,
                    minlength: 8,
                    equalTo: "#password"
                },
                @endif
                password: {
                    required: true,
                    minlength: 8,
                }
            },
            messages: {
                @if(Auth::id() == $user -> id || (Myhelper::hasNotRole('admin') && !Myhelper::can('member_password_reset')))
                oldpassword: {
                    required: "Please enter old password",
                },
                password_confirmation: {
                    required: "Please enter confirmed password",
                    minlength: "Your password lenght should be atleast 8 character",
                    equalTo: "New password and confirmed password should be equal"
                },
                @endif
                password: {
                    required: "Please enter new password",
                    minlength: "Your password lenght should be atleast 8 character",
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
                        form.find('button:submit').html('loading...').attr("disabled",true).addClass('btn-secondary');
                    },
                    complete: function() {
                        form.find('button:submit').html('Update Profile').attr("disabled",false).removeClass('btn-secondary');
                    },
                    success: function(data) {
                        if (data.status == "success") {
                            notify("Password Successfully Changed", 'success');
                        } else {
                            notify(data.status, 'warning');
                        }
                    },
                    error: function(errors) {
                        showError(errors, form.find('.panel-body'));
                    }
                });
            }
        });

        $("#memberForm").validate({
            rules: {
                parent_id: {
                    required: true
                }
            },
            messages: {
                parent_id: {
                    required: "Please select parent member"
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
                var form = $('form#memberForm');
                form.find('span.text-danger').remove();
                form.ajaxSubmit({
                    dataType: 'json',
                    beforeSubmit: function() {
                        form.find('button:submit').html('loading...').attr("disabled",true).addClass('btn-secondary');
                    },
                    complete: function() {
                        form.find('button:submit').html('Change').attr("disabled",false).removeClass('btn-secondary');
                    },
                    success: function(data) {
                        if (data.status == "success") {
                            notify("Mapping Successfully Changed", 'success');
                        } else {
                            notify(data.status, 'warning');
                        }
                    },
                    error: function(errors) {
                        showError(errors);
                    }
                });
            }
        });

        $("#roleForm").validate({
            rules: {
                role_id: {
                    required: true
                }
            },
            messages: {
                role_id: {
                    required: "Please select member role"
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
                var form = $('form#roleForm');
                form.find('span.text-danger').remove();
                form.ajaxSubmit({
                    dataType: 'json',
                    beforeSubmit: function() {
                        form.find('button:submit').html('loading...').attr("disabled",true).addClass('btn-secondary');
                    },
                    complete: function() {
                        form.find('button:submit').html('Change').attr("disabled",false).removeClass('btn-secondary');
                    },
                    success: function(data) {
                        if (data.status == "success") {
                            notify("Role Successfully Changed", 'success');
                        } else {
                            notify(data.status, 'warning');
                        }
                    },
                    error: function(errors) {
                        showError(errors);
                    }
                });
            }
        });

        $("#bankForm").validate({
            rules: {
                account: {
                    required: true
                },
                bank: {
                    required: true
                },
                ifsc: {
                    required: true
                }
            },
            messages: {
                account: {
                    required: "Please enter member account"
                },
                bank: {
                    required: "Please enter member bank"
                },
                ifsc: {
                    required: "Please enter bank ifsc"
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
                var form = $('form#bankForm');
                form.find('span.text-danger').remove();
                form.ajaxSubmit({
                    dataType: 'json',
                    beforeSubmit: function() {
                        form.find('button:submit').html('loading...').attr("disabled",true).addClass('btn-secondary');
                    },
                    complete: function() {
                        form.find('button:submit').html('Change').attr("disabled",false).removeClass('btn-secondary');
                    },
                    success: function(data) {
                        if (data.status == "success") {
                            notify("Bank Details Successfully Changed", 'success');
                        } else {
                            notify(data.status, 'warning');
                        }
                    },
                    error: function(errors) {
                        showError(errors, form.find('.panel-body'));
                    }
                });
            }
        });

        $("#pinForm").validate({
            rules: {
                oldpin: {
                    required: true,
                },
                pin_confirmation: {
                    required: true,
                    minlength: 4,
                    equalTo: "#pin"
                },
                pin: {
                    required: true,
                    minlength: 4,
                }
            },
            messages: {
                oldpin: {
                    required: "Please enter old pin",
                },
                pin_confirmation: {
                    required: "Please enter confirmed pin",
                    minlength: "Your pin lenght should be atleast 4 character",
                    equalTo: "New pin and confirmed pin should be equal"
                },
                pin: {
                    required: "Please enter new pin",
                    minlength: "Your pin lenght should be atleast 4 character",
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
                var form = $('form#pinForm');
                form.find('span.text-danger').remove();
                form.ajaxSubmit({
                    dataType: 'json',
                    beforeSubmit: function() {
                        form.find('button:submit').html('loading...').attr("disabled",true).addClass('btn-secondary');
                    },
                    complete: function() {
                        form.find('button:submit').html('Password Reset').attr("disabled",false).removeClass('btn-secondary');
                    },
                    success: function(data) {
                        if (data.status == "success") {
                            form[0].reset();
                            notify("Pin Successfully Changed", 'success');
                        } else {
                            notify('Something went wrong, Please try again', 'warning');
                        }
                    },
                    error: function(errors) {
                        showError(errors, form.find('.panel-body'));
                         notify('Something went wrong, Please try again', 'error');
                    }
                });
            }
        });
    });

    function OTPRESEND() {
        var mobile = "{{Auth::user()->mobile}}";
        if (mobile.length > 0) {
            $.ajax({
                    url: '{{ route("getotp") }}',
                    type: 'post',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        'mobile': mobile
                    },
                    beforeSend: function() {
                        swal({
                            title: 'Wait!',
                            text: 'Please wait, we are working on your request',
                            onOpen: () => {
                                swal.showLoading()
                            }
                        });
                    },
                    complete: function() {
                        swal.close();
                    }
                })
                .done(function(data) {
                    if (data.status == "TXN") {
                        notify("Otp sent successfully", 'success');
                    } else {
                        notify(data.message, 'warning');
                    }
                })
                .fail(function() {
                    notify("Something went wrong, try again", 'warning');
                });
        } else {
            notify("Enter your registered mobile number", 'warning');
        }
    }
</script>
@endpush