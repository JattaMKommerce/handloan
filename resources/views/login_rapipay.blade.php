<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>UjjwalWorlPay</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{asset('')}}theme/images/favicon.ico" />
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{asset('')}}theme/css/bootstrap.min.css">
    <!-- Typography CSS -->
    <link rel="stylesheet" href="{{asset('')}}theme/css/typography.css">
    <!-- Style CSS -->
    <link rel="stylesheet" href="{{asset('')}}theme/css/style.css">
    <!-- Responsive CSS -->
    <link rel="stylesheet" href="{{asset('')}}theme/css/responsive.css">

    <style>
        .form-group p {
            color: red;
        }
    </style>
</head>

<body>
    <!-- loader Start -->
    <!-- <div id="loading">
        <div id="loading-center">
        </div>
    </div> -->
    <!-- loader END -->
    <!-- Sign in Start -->
    <section class="sign-in-page">
        <div id="container-inside">
            <div class="cube"></div>
            <div class="cube"></div>
            <div class="cube"></div>
            <div class="cube"></div>
            <div class="cube"></div>
        </div>
        <div class="container p-0">
            <div class="row no-gutters height-self-center">
                <div class="col-sm-12 align-self-center bg-primary rounded">
                    <div class="row m-0">
                        <div class="col-md-5 bg-white sign-in-page-data">
                            <div class="sign-in-from">
                                <h1 class="mb-0 text-center">Sign in</h1>
                                <p class="text-center text-dark"><b>Enter your Username and Password to access the panel</b></p>
                                <form action="{{route('authCheck')}}" method="POST" class="login-form">
                                    <p style="color:red"><b class="errorText"></b></p>
                                    <p style="color:teal"><b class="successText"></b></p>
                                    {{ csrf_field() }}
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><b>Username</b></label>
                                        <input type="text" id="" class="form-control" name="mobile" placeholder="User name" required style="border:1px solid #c1c1c1 !important;">
                                    </div>


                                    <label for="exampleInputPassword1"><b>Password</b></label>
                                    <div class="input-group " style="border: 1px solid #c1c1c1;  border-radius: 5px; overflow: hidden; ">
                                        <input type="password" name="password" class="form-control" style="border:0px" placeholder="Password" aria-label="Recipient's username" aria-describedby="basic-addon2" style="border:1px solid #c1c1c1 !important;">
                                        <div class="input-group-append">
                                            <span class="input-group-text bg-white" id="basic-addon2 "><i class="fa fa-eye" id="passwordView" aria-hidden="true"></i></span>
                                        </div>
                                    </div>
                                    <a href="javascript:void(0)" onclick="forgetPassword()" class="float-right my-3"><u>Forgot password?</u></a>

                                    <div class="formdata">

                                    </div>
                                    <div class="row form-group" style="margin-top:10px">
                                    <div class="col-lg-6" style="background-color: green;padding: 10px;border-radius: 10px;">
                                         <input type="hidden" name="capchaConfirm" id="capchaConfirm" value="{{$cptcha}}" />
                                      <h5 class="captcha mt-2 text" style="text-align: center; color:white" >{{$cptcha}}</h5>
                                    </div>
        
                                    <div class="col-lg-6 mb-3" >
                                      <input type="text" name="capcha" class="form-control shadow-none"  placeholder="Enter captcha"  ondrop="return false;" onpaste="return false;" required>
                                    
                                    </div>
                                    </div>

                                    <div class="sign-info text-center">
                                        <button class="btn btn-primary d-block w-100 mb-2">Sign in</button>
                                        <span class="text-dark dark-color d-inline-block line-height-2"><b>Don't have an account? <a href="#" data-toggle="modal" data-target="#registerModal">Sign
                                                    up</a></b></span>
                                    </div>
                                </form>

                            </div>
                        </div>
                        <div class="col-md-7 text-center sign-in-page-image">
                            <div class="sign-in-detail text-white">
                                <a class="sign-in-logo mb-5" href="#"><img src="{{asset('')}}theme/images/logo.jpg" class="img-fluid" alt="logo" style="border-radius: 10px;"></a>
                                <div class="owl-carousel" data-autoplay="true" data-loop="true" data-nav="false" data-dots="true" data-items="1" data-items-laptop="1" data-items-tab="1" data-items-mobile="1" data-items-mobile-sm="1" data-margin="0">
                                    <div class="item">
                                        <img src="{{asset('')}}theme/images/login/1.png" class="img-fluid mb-4" alt="logo">
                                        <h4 class="mb-1 text-white">Find new friends</h4>
                                        <p>It is a long established fact that a reader will be distracted by the readable content.</p>
                                    </div>
                                    <div class="item">
                                        <img src="{{asset('')}}theme/images/login/1.png" class="img-fluid mb-4" alt="logo">
                                        <h4 class="mb-1 text-white">Connect with the world</h4>
                                        <p>It is a long established fact that a reader will be distracted by the readable content.</p>
                                    </div>
                                    <div class="item">
                                        <img src="{{asset('')}}theme/images/login/1.png" class="img-fluid mb-4" alt="logo">
                                        <h4 class="mb-1 text-white">Create new events</h4>
                                        <p>It is a long established fact that a reader will be distracted by the readable content.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="passwordResetModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Forgot Password</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="passwordRequestForm" action="{{route('authReset')}}" method="post">
                        <b>
                            <p class="text-danger"></p>
                        </b>
                        <input type="hidden" name="type" value="request">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label>Mobile</label>
                            <input type="text" name="mobile" class="form-control" placeholder="Enter Mobile Number" required="">
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary btn-block text-uppercase waves-effect waves-light" type="submit" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Resetting">Reset Request</button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="passwordModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Forgot Password</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="passwordForm" action="{{route('authReset')}}" method="post">
                        <b>
                            <p class="text-danger"></p>
                        </b>
                        <input type="hidden" name="mobile">
                        <input type="hidden" name="type" value="reset">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label>Reset Token</label>
                            <input type="text" name="token" class="form-control" placeholder="Enter OTP" required="">
                        </div>
                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Enter New Password" required="">
                        </div>
                        <div class="form-group mt-3">
                            <button class="btn btn-primary btn-block text-uppercase waves-effect waves-light" type="submit" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Resetting">Reset Password</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade bd-example-modal-lg" id="registerModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">New Member Registration</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="registerForm" action="{{ route('register') }}" method="post">
                        {{ csrf_field() }}

                        <div class="row">

                            <div class="form-group col-md-4">
                                <label>Member Type</label>
                                <select name="slug" class="form-control select" required>
                                    <option value="">Select Member Type</option>
                                    @foreach ($roles as $role)
                                    <option value="{{ $role->slug }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>

                        <h5 class="mb-3">Personal Details</h5>

                        <div class="row">


                            <div class="form-group col-md-4">

                                <label for="exampleInputEmail1" class="text-uppercase">Name</label>

                                <input type="text" name="name" class="form-control" placeholder="Enter your name" required>

                            </div>

                            <div class="form-group col-md-4">

                                <label for="exampleInputPassword1" class="text-uppercase">Email</label>

                                <input type="text" name="email" class="form-control" placeholder="Enter your email id" required>

                                <div class="alert-message" id="emailError"></div>

                            </div>

                            <div class="form-group col-md-4">

                                <label for="exampleInputPassword1" class="text-uppercase">Mobile</label>

                                <input type="text" name="mobile" class="form-control" placeholder="Enter your mobile" required>

                                <div class="alert-message" id="mobileError"></div>

                            </div>

                        </div>

                        <div class="row">

                            <div class="form-group col-md-4">

                                <label>State</label>

                                <select name="state" class="form-control state" required>
                                    <option value="">Select State</option>
                                    @foreach ($state as $state)
                                    <option value="{{ $state->state }}">{{ $state->state }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-4">

                                <label>City</label>

                                <input type="text" name="city" class="form-control" value="" required="" placeholder="Enter Value">

                            </div>

                            <div class="form-group col-md-4">

                                <label>Pincode</label>

                                <input type="text" name="pincode" class="form-control" value="" required="" maxlength="6" minlength="6" placeholder="Enter Value" pattern="[0-9]*">

                            </div>

                        </div>

                        <div class="row">

                            <div class="form-group col-md-12">

                                <label>Address</label>

                                <textarea name="address" class="form-control" rows="3" required="" placeholder="Enter Value"></textarea>

                            </div>

                        </div>

                        <h5 class="mb-3">Kyc Information</h5>
                        <div class="row">

                            <div class="form-group col-md-4">

                                <label>Shop Name</label>

                                <input type="text" name="shopname" class="form-control" value="" required="" placeholder="Enter Value">

                                <div class="alert-message" id="shopnameError"></div>

                            </div>

                            <div class="form-group col-md-4">

                                <label>Pancard</label>

                                <input type="text" name="pancard" class="form-control" value="" id="pancard" required="" placeholder="Enter Value">

                                <div class="alert-message" id="pancardError"></div>

                            </div>

                            <div class="form-group col-md-4">

                                <label>Aadhar</label>

                                <input type="text" name="aadharcard" required="" class="form-control" id="aadharcard" placeholder="Enter Value" pattern="[0-9]*" maxlength="12" minlength="12">


                                <div class="alert-message" id="aadharcardError"></div>

                            </div>

                        </div>

                        <div class="text-center form-group">

                            <button type="submit" class="btn btn-primary">Submit</button>

                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>



    <div class="iq-colorbox color-fix">
        <div class="buy-button"> <a class="color-full" href="#"><i class="fa fa-spinner fa-spin"></i></a> </div>
        <div class="clearfix color-picker">
            <h3 class="iq-font-black">FinDash Awesome Color</h3>
            <p>This color combo available inside whole template. You can change on your wish, Even you can create your own with limitless possibilities! </p>
            <ul class="iq-colorselect clearfix">
                <li class="color-1 iq-colormark" data-style="color-1"></li>
                <li class="color-2" data-style="iq-color-2"></li>
                <li class="color-3" data-style="iq-color-3"></li>
                <li class="color-4" data-style="iq-color-4"></li>
                <li class="color-5" data-style="iq-color-5"></li>
                <li class="color-6" data-style="iq-color-6"></li>
                <li class="color-7" data-style="iq-color-7"></li>
                <li class="color-8" data-style="iq-color-8"></li>
                <li class="color-9" data-style="iq-color-9"></li>
                <li class="color-10" data-style="iq-color-10"></li>
                <li class="color-11" data-style="iq-color-11"></li>
                <li class="color-12" data-style="iq-color-12"></li>
                <li class="color-13" data-style="iq-color-13"></li>
                <li class="color-14" data-style="iq-color-14"></li>
                <li class="color-15" data-style="iq-color-15"></li>
                <li class="color-16" data-style="iq-color-16"></li>
                <li class="color-17" data-style="iq-color-17"></li>
                <li class="color-18" data-style="iq-color-18"></li>
                <li class="color-19" data-style="iq-color-19"></li>
                <li class="color-20" data-style="iq-color-20"></li>
            </ul>
            <a target="_blank" class="btn btn-primary d-block mt-3" href="">Purchase Now</a>
        </div>
    </div>

    <script src="{{asset('')}}theme/js/jquery.min.js"></script>
    <script src="{{asset('')}}theme/js/popper.min.js"></script>
    <script src="{{asset('')}}theme/js/bootstrap.min.js"></script>
    <script src="{{asset('')}}theme/js/jquery.appear.js"></script>
    <script src="{{asset('')}}theme/js/countdown.min.js"></script>
    <script src="{{asset('')}}theme/js/waypoints.min.js"></script>
    <script src="{{asset('')}}theme/js/jquery.counterup.min.js"></script>
    <script src="{{asset('')}}theme/js/wow.min.js"></script>
    <script src="{{asset('')}}theme/js/apexcharts.js"></script>
    <script src="{{asset('')}}theme/js/lottie.js"></script>
    <script src="{{asset('')}}theme/js/slick.min.js"></script>
    <script src="{{asset('')}}theme/js/select2.min.js"></script>
    <script src="{{asset('')}}theme/js/owl.carousel.min.js"></script>
    <script src="{{asset('')}}theme/js/charts.js"></script>
    <script src="{{asset('')}}theme/js/jquery.magnific-popup.min.js"></script>
    <script src="{{asset('')}}theme/js/smooth-scrollbar.js"></script>
    <script src="{{asset('')}}theme/js/style-customizer.js"></script>
    <script src="{{asset('')}}theme/js/chart-custom.js"></script>
    <script src="{{asset('')}}theme/js/custom.js"></script>
    <script src="{{asset('')}}assets/js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
    <script type="text/javascript" src="{{asset('')}}assets/js/core/jquery.validate.min.js"></script>
    <script type="text/javascript" src="{{asset('')}}assets/js/core/jquery.form.min.js"></script>
    <script type="text/javascript" src="{{asset('')}}assets/js/core/sweetalert2.min.js"></script>
    <script type="text/javascript" src="{{asset('')}}assets/js/plugins/forms/selects/select2.min.js"></script>
    <script src="{{asset('')}}assets/js/core/snackbar.js"></script>
    <script src="{{asset('')}}theme/js/worldLow.js"></script>
    <script src="{{asset('')}}theme/js/kelly.js"></script>
    <script src="{{asset('')}}theme/js/maps.js"></script>
    <script src="{{asset('')}}theme/js/core.js"></script>
    <script src="{{asset('')}}theme/js/animated.js"></script>
    <script>
        $(document).ready(function() {

            $('#useridinput').on('keyup', function(e) {
                // let val = e.target.value;
                // console.log('val', val);

                e.target.value = e.target.value.replace(/\D/g, "")
            });

            $('#passwordView').click(function() {

                var passwordType = $(this).closest('form').find('[name="password"]').attr('type');
                $(this).toggleClass("fa-eye fa-eye-slash")
                if (passwordType == "password") {
                    $(this).closest('form').find('[name="password"]').attr('type', "text");
                    // $(this).find('i').addClass('fa-eye-slash');
                } else {
                    $(this).closest('form').find('[name="password"]').attr('type', "password");
                    // $(this).find('i').removeClass('fa-eye-slash');
                }
            });
            var number = 1 + Math.floor(Math.random() * 100000);
            $('#capcha').text(number);
            $(".login-form").validate({
                rules: {
                    mobile: {
                        required: true,
                        
                    },
                    password: {
                        required: true,
                    },
                    capchaConfirm: {
                        required: true,
                    },
                    capcha: {
                        required: true,
                        minlength: 6,
                        equalTo: "#capchaConfirm"
                    },
                },
                messages: {
                    mobile: {
                        required: "Please enter mobile number",
                        
                    },
                    capcha: {
                        required: "Please enter captcha",
                        number: "Captcha should be numeric",
                        equalTo: "Invalid Captcha",
                        minlength: "Your captcha  must be 6 digit",

                    },
                    password: {
                        required: "Please enter password",
                    },
                    capchaConfirm: {
                        required: "Please enter password",
                    }
                },
                errorElement: "p",
                errorPlacement: function(error, element) {
                    if (element.prop("tagName").toLowerCase() === "select") {
                        error.insertAfter(element.closest(".form-group").find(".select2"));
                    } else {
                        error.insertAfter(element);
                        $
                    }
                },
                submitHandler: function() {
                    var form = $('.login-form');
                    form.ajaxSubmit({
                        dataType: 'json',
                        beforeSubmit: function() {
                            swal({
                                title: 'Wait!',
                                text: 'We are checking your login credential',
                                onOpen: () => {
                                    swal.showLoading()
                                },
                                allowOutsideClick: () => !swal.isLoading()
                            });
                        },
                        success: function(data) {
                            swal.close();
                            if (data.status == "Login") {
                                swal({
                                    type: 'success',
                                    title: 'Success',
                                    text: 'Successfully logged in.',
                                    showConfirmButton: false,
                                    timer: 2000,
                                    onClose: () => {
                                        window.location.reload();
                                    },
                                });
                            } else if (data.status == "otpsent" || data.status == "preotp") {
                                $('div.formdata').append(`<div class="form-group has-feedback has-feedback-left mt-5">
                                <input type="password" class="form-control" placeholder="Enter Otp" name="otp" required>
                                <div class="form-control-feedback">
                                    <i class="icon-lock2 text-muted"></i>
                                </div>
                                <a href="javascript:void(0)" onclick="OTPRESEND()" class="text-primary pull-right">Resend Otp</a>
                                <div class="clearfix"></div>
                            </div> `);

                                if (data.status == "preotp") {
                                    $('b.successText').text('Please use previous otp sent on your mobile.');
                                    setTimeout(function() {
                                        $('b.successText').text('');
                                    }, 5000);
                                }
                            }
                        },
                        error: function(errors) {
                            swal.close();
                            if (errors.status == '400') {
                                $('b.errorText').text(errors.responseJSON.status);
                                setTimeout(function() {
                                    $('b.errorText').text('');
                                }, 5000);
                            } else {
                                $('b.errorText').text('Something went wrong, try again later.');
                                setTimeout(function() {
                                    $('b.errorText').text('');
                                }, 5000);
                            }
                        }
                    });
                }
            });

            $("#registerForm").validate({
                rules: {
                    slug: {
                        required: true
                    },
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
                    },
                    aadharcard: {
                        required: true,
                        minlength: 12,
                        number: true,
                        maxlength: 12
                    },
                    pancard: {
                        required: true,
                        minlength: 10,
                        maxlength: 10
                    },
                    shopname: {
                        required: true,
                    }

                },
                messages: {
                    slug: {
                        required: "Please select member type",
                    },
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
                        minlength: "Your pincode number must be 6 digit",
                        maxlength: "Your pincode number must be 6 digit"
                    },
                    address: {
                        required: "Please enter address",
                    },
                    aadharcard: {
                        required: "Please enter aadharcard",
                        number: "Aadhar should be numeric",
                        minlength: "Your aadhar number must be 12 digit",
                        maxlength: "Your aadhar number must be 12 digit"
                    },
                    pancard: {
                        required: "Please enter pancard",
                        minlength: "Your pancard number must be 10 digit",
                        maxlength: "Your pancard number must be 10 digit"
                    },
                    shopname: {
                        required: "Please enter shopname"

                    },
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
                    var form = $('#registerForm');
                    form.ajaxSubmit({
                        dataType: 'json',
                        beforeSubmit: function() {
                            swal({
                                title: 'Wait!',
                                text: 'We are working on your request',
                                onOpen: () => {
                                    swal.showLoading()
                                },
                                allowOutsideClick: () => !swal.isLoading()
                            });
                        },
                        success: function(data) {
                            swal.close();
                            if (data.status == "TXN") {
                                $('#registerModal').modal('hide');
                                swal({
                                    type: 'success',
                                    title: 'Welcome',
                                    text: 'Your request has been submitted successfully, please wait for confirmation',
                                    showConfirmButton: true
                                });
                            } else {
                                console.log(data);
                                $('b.errorText1').text(data.message);
                                notify(data.message, 'error');
                            }
                        },
                        error: function(errors) {

                            swal.close();
                            if (errors.status == '422') {
                                // notify(errors.responseJSON.errors[0], 'warning');
                                $('#emailError').text(errors.responseJSON.errors.email);
                                $('#mobileError').text(errors.responseJSON.errors.mobile);
                                $('#shopnameError').text(errors.responseJSON.errors.shopname);
                                $('#pancardError').text(errors.responseJSON.errors.pancard);
                                $('#aadharcardError').text(errors.responseJSON.errors.aadharcard);

                            } else {
                                swal("Oh No!", "Something went wrong, try again later!", "error");
                                //  notify('Something went wrong, try again later.', 'warning');
                            }
                        }
                    });
                }
            });

            $("#passwordForm").validate({
                rules: {
                    token: {
                        required: true,
                        
                    },
                    password: {
                        required: true,
                    }
                },
                messages: {
                    mobile: {
                        required: "Please enter reset token",
                        
                    },
                    password: {
                        required: "Please enter password",
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
                    var form = $('#passwordForm');
                    form.ajaxSubmit({
                        dataType: 'json',
                        beforeSubmit: function() {
                            swal({
                                title: 'Wait!',
                                text: 'We are checking your login credential',
                                onOpen: () => {
                                    swal.showLoading()
                                },
                                allowOutsideClick: () => !swal.isLoading()
                            });
                        },
                        success: function(data) {
                            if (data.status == "TXN") {
                                $('#passwordModal').modal('hide');
                                swal({
                                    type: 'success',
                                    title: 'Reset!',
                                    text: 'Password Successfully Changed',
                                    showConfirmButton: true
                                });
                            } else {
                                notify(data.message, 'error');
                            }
                        },
                        error: function(errors) {
                            swal.close();
                            if (errors.status == '400') {
                                notify(errors.responseJSON.status, 'error');
                            } else if (errors.status == '422') {
                                $.each(errors.responseJSON.errors, function(index, value) {
                                    form.find('[name="' + index + '"]').closest('div.form-group').append('<p class="error">' + value + '</span>');
                                });
                                form.find('p.error').first().closest('.form-group').find('input').focus();
                                setTimeout(function() {
                                    form.find('p.error').remove();
                                }, 5000);
                            } else {
                                notify('Something went wrong, try again later.', 'error');
                            }
                        }
                    });
                }
            });



            $("#otpForm").validate({
                rules: {
                    otp: {
                        required: true,
                        number: true
                    }

                },
                messages: {
                    otp: {
                        required: "Please enter otp",
                        number: "Reset otp should be numeric",
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
                    var form = $('#otpForm');
                    form.ajaxSubmit({
                        dataType: 'json',
                        beforeSubmit: function() {
                            swal({
                                title: 'Wait!',
                                text: 'We are checking your details',
                                onOpen: () => {
                                    swal.showLoading()
                                },
                                allowOutsideClick: () => !swal.isLoading()
                            });
                        },
                        success: function(data) {
                            swal.close();
                            if (data.status == "TXN") {
                                $('#otpModal').modal('hide');

                                // $('#registerForm').find(':input[type=submit]').removeAttr('disabled');
                                $('#registerForm').find('[name="address"]').val(data.address);
                                $("#address").prop('readonly', true);
                                $('#registerForm').find('[name="name"]').val(data.full_name);
                                $("#name").prop('readonly', true);
                                $('#registerForm').find('[name="city"]').val(data.city);
                                $("#city").prop('readonly', true);
                                $('#registerForm').find('[name="pincode"]').val(data.pin);
                                $("#pincode").prop('readonly', true);
                                $('#registerForm').find('[name="state"]').select2().val(data.state).trigger('change');
                                $("state").prop('readonly', true);
                                // $('#registerForm').find('[name="state"]').val();
                                swal("Verified", "Your Adhar Card is Verified " + data.full_name, "success");

                            } else {
                                $('#aadharcard').val('');
                                swal({
                                    type: 'warning',
                                    title: '!ERROR',
                                    text: data.message,
                                    showConfirmButton: true
                                });
                            }
                        },
                        error: function(errors) {
                            swal.close();
                            if (errors.status == '400') {
                                notify(errors.responseJSON.status, 'error');
                            } else {
                                notify('Something went wrong, try again later.', 'error');
                            }
                        }
                    });
                }
            });



        });

        // function notify(msg, type = "success") {
        //     let snackbar = new SnackBar;
        //     snackbar.make("message", [
        //         msg,
        //         null,
        //         "bottom",
        //         "right",
        //         "text-" + type
        //     ], 5000);
        // }


        function notify(text, status) {
            new Notify({
                status: status,
                title: null,
                text: text,
                effect: 'fade',
                customClass: null,
                customIcon: null,
                showIcon: true,
                showCloseButton: true,
                autoclose: true,
                autotimeout: 2000,
                gap: 20,
                distance: 15,
                type: 1,
                position: 'right top'
            })
        }




        function forgetPassword() {
            var mobile = $('.login-form').find('[name="mobile"]').val();

            if (mobile != '') {

                $.ajax({
                    url: `{{route('authReset')}}`,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    data: {
                        'type': 'request',
                        "mobile": mobile
                    },
                    beforeSend: function() {
                        swal({
                            title: 'Wait!',
                            text: 'We are processing your request',
                            onOpen: () => {
                                swal.showLoading()
                            },
                            allowOutsideClick: () => !swal.isLoading()
                        });
                    }
                }).done(function(data) {
                    swal.close();
                    if (data.status == "TXN") {
                        $('#passwordResetModal').modal('hide');
                        $('#passwordForm').find('input[name="mobile"]').val(mobile);
                        $('#passwordModal').modal('show');
                    } else {
                        $('b.errorText').text(data.message);
                        setTimeout(function() {
                            $('b.errorText').text('');
                        }, 5000);
                    }
                }).fail(function(errors) {
                    swal.close();
                    if (errors.status == '400') {
                        $('b.errorText').text(errors.responseJSON.message);
                        setTimeout(function() {
                            $('b.errorText').text('');
                        }, 5000);
                    } else {
                        $('b.errorText').text("Something went wrong, try again later.");
                        setTimeout(function() {
                            $('b.errorText').text('');
                        }, 5000);
                    }
                });

            } else {
                $('b.errorText').text('Enter your registered mobile number');
                setTimeout(function() {
                    $('b.errorText').text('');
                }, 5000);
            }
        }

        function OTPRESEND() {
            var mobile = $('input[name="mobile"]').val();
            var password = $('input[name="password"]').val();
            if (mobile.length > 0) {
                $.ajax({
                        url: '{{ route("authCheck") }}',
                        type: 'post',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            'mobile': mobile,
                            'password': password,
                            'otp': "resend"
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
                        if (data.status == "otpsent") {
                            $('b.successText').text('Otp sent successfully');
                            setTimeout(function() {
                                $('b.successText').text('');
                            }, 5000);
                        } else {
                            $('b.errorText').text(data.message);
                            setTimeout(function() {
                                $('b.errorText').text('');
                            }, 5000);
                        }
                    })
                    .fail(function() {
                        $('b.errorText').text('Something went wrong, try again');
                        setTimeout(function() {
                            $('b.errorText').text('');
                        }, 5000);
                    });
            } else {
                $('b.errorText').text('Enter your registered mobile number');
                setTimeout(function() {
                    $('b.errorText').text('');
                }, 5000);
            }
        }
    </script>

</body>

</html>