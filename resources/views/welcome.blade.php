<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login To - {{$mydata['company']->companyname}}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha512-SfTiTlX6kk+qitfevl/7LibUOeJWlt9rbyDn92a1DqWOw9vWG2MFoays0sgObmWazO5BQPiFucnnEAjpAB+/Sw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

</head>

<style>
    .text2 {

        color: #60279C !important;
        font-size: 13px;

    }

    .carousel-item>img {

        width: 896px;
        height: 80vh;
        border-radius: 5px;
    }

    input {

        height: 40px;
    }

    input::placeholder {

        color: rgb(211, 210, 210) !important;
    }

    .text {

        color: #0056b3 !important;
    }

    .btn-primary2 {

        background-color: #0056b3 !important;
        color: white;
    }

    .nav-link.active {

        background-color: #0056b3 !important;
    }

    .error {
        color: red;
    }
</style>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 col-sm-12">
                <nav class="navbar navbar-expand-lg navbar-light bg-light" style="box-shadow: 0 0 16px 4px #cccccc40; background: #f5f5f5;">
                    <div class="container-fluid">
                        <a class="navbar-brand" href="#">
                            @if($mydata['company']->logo)
                            <img src="{{asset('public/logos')}}/{{$mydata['company']->logo}}" style="width:150px; height:80px; margin-left:25px;">
                            @endif
                        </a>
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                            </ul>
                            <div class="d-flex" style="justify-content: end; width: 60%!important;">
                                <div class="image"><a><img src="{{asset('public')}}/login_assets/images/fastag.png" style="width:150px; height:145px; margin-left:25px; float:right;" /></a></div>
                            </div>
                        </div>
                    </div>
                </nav>
            </div>

            <div class="col-lg-12">
                <div class="row" style="padding: 25px;">
                    <div class="col-lg-7 col-sm-12">
                        <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel" style="border-radius: 5px;">
                            <div class="carousel-indicators">
                                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
                                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
                            </div>
                            <div class="carousel-inner">
                                <div class="carousel-item active">
                                    <img src="{{asset('public')}}/login_assets/images/voot.jpg" class="d-block w-100" alt="...">
                                </div>
                                <div class="carousel-item">
                                    <img src="{{asset('public')}}/login_assets/images/sonyliv.jpg" class="d-block w-100" alt="...">
                                </div>
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        </div>
                    </div>

                    <div class="col-lg-5 col-sm-12" style="padding: 30px;">

                        <div class="row" style="margin-top:-32px;">
                            <div class="col-lg-2 image"></div>
                            <div class="col-lg-2 image"><a><img src="{{asset('public')}}/login_assets/images/fb.png" /></a></div>
                            <div class="col-lg-2 image"><img src="{{asset('public')}}/login_assets/images/insta.png" /></div>
                            <div class="col-lg-2 image"><img src="{{asset('public')}}/login_assets/images/lin.png" /></div>
                            <div class="col-lg-2 image"><img src="{{asset('public')}}/login_assets/images/youtube.png" /></div>
                            <div class="col-lg-2 image"></div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-lg-2"></div>
                            <div class="col-lg-4 text">
                                <p style="font-size: 17px; margin-left:5px;"><i class="fa fa-phone" aria-hidden="true"></i> +987465120</p>
                            </div>
                            <div class="col-lg-6 text">
                                <p style="font-size: 16px; margin-left:5px;"><i class="fa fa-envelope-o" aria-hidden="true"></i> support45@gmail.com</p>
                            </div>
                            <!--<div class="col-lg-1"></div>-->
                        </div>

                        <div class="row mt-4">
                            <h5 class="text" style="text-align: center;">Sign in to continue</h5>
                        </div>

                        <div class="row">
                            <div class="col-lg-12 col-sm-12" style="margin-top: 25px; box-shadow: 0 0 16px 4px #cccccc40; padding: 0px;">
                                <ul class="row nav nav-pills" id="pills-tab" role="tablist">
                                    <li class="col-lg-12 col-sm-12 nav-item" role="presentation">
                                        <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true" style="width: 100%; text-align: center;">
                                            <h6>Agent Login</h6>
                                        </button>
                                    </li>
                                    <!--<li class="col-lg-6 col-sm-6 nav-item" role="presentation">-->
                                    <!--  <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false" style="width: 100%; text-align: center;"><h6>Fos Login</h6></button>-->
                                    <!--</li>-->
                                </ul>
                            </div>

                            <div class="tab-content" id="pills-tabContent" style="box-shadow: 0 0 16px 4px #cccccc40;">
                                <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab" style="padding: 20px;">
                                    <form form action="{{route('authCheck')}}" method="POST" class="login-form">
                                        {{ csrf_field() }}
                                        <p style="color:red"><b class="errorText"></b></p>
                                        <p style="color:teal"><b class="successText"></b></p>
                                        <div class="col-lg-12 mb-3">
                                            <input type="number" class="form-control" name="mobile" placeholder="User name" pattern="[0-9]*" maxlength="11" minlength="10" required>
                                        </div>

                                        <div class="col-lg-12 input-group mb-3">
                                            <input type="password" name="password" class="form-control" placeholder="Password" aria-label="Recipient's username" aria-describedby="basic-addon2">
                                            <span class="input-group-text" id="basic-addon2"><i class="fa fa-eye-slash" aria-hidden="true"></i></span>
                                        </div>

                                        <div class="col-lg-12 mb-3">
                                            <a href="javascript:void(0)" onclick="forgetPassword()" style="float: right;">Forgot password ?</a>
                                        </div>

                                        <div class="col-lg-6">
                                            <h5 class="captcha mt-2" style="text-align: center;" id="capcha"></h5>
                                        </div>

                                        <div class="col-lg-6 mb-3">
                                            <input type="number" name="capcha" class="form-control" placeholder="Enter captcha" required>
                                        </div>

                                        <div class="col-lg-12">
                                            <button class="btn btn-primary2" style="width: 100%;">
                                                <h5>Login</h5>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab" style="padding: 20px;">
                                    <form class="row">
                                        <div class="col-lg-12 mb-3">
                                            <input type="number" class="form-control" placeholder="Registerted Mobile No" id="exampleInputEmail1" aria-describedby="emailHelp">
                                        </div>

                                        <div class="col-lg-12 input-group mb-3">
                                            <input type="text" class="form-control" placeholder="Password" aria-label="Recipient's username" aria-describedby="basic-addon2">
                                            <span class="input-group-text" id="basic-addon2"><i class="fa fa-eye-slash" aria-hidden="true"></i></span>
                                        </div>

                                        <div class="col-lg-12 mb-3">
                                            <a href="javascript:void(0);" style="float: right;">Forgot password ?</a>
                                        </div>

                                        <div class="col-lg-6">
                                            <h5 class="captcha mt-2" style="text-align: center;">XBD14</h5>
                                        </div>

                                        <div class="col-lg-6 mb-3">
                                            <input type="number" class="form-control" placeholder="Enter captcha">
                                        </div>

                                        <div class="col-lg-12">
                                            <button class="btn btn-primary2" style="width: 100%;">
                                                <h5>Login</h5>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                        </div>

                        <div class="row mt-3">
                            <div class="col-lg-9 mt-4">
                                <h4 class="my-0 font-weight-bold qr-text mx-2 mt-4 text">Scan to install Finoviti Agent App</h4>
                            </div>
                            <div class="col-lg-3">
                                <img class="qr-img" src="{{asset('public')}}/login_assets/images/qr.jpg" style="width: 150px;">
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    </div>

    <div id="passwordResetModal" class="modal fade" data-backdrop="false" data-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title pull-left">Password Reset Request</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
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
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>

    <div id="passwordModal" class="modal fade" data-backdrop="false" data-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title pull-left">Password Reset</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="alert bg-success alert-styled-left no-margin mb-15">
                        <button type="button" class="close" data-dismiss="alert"><span>×</span><span class="sr-only">Close</span></button>
                        <span class="text-semibold">Success!</span> Your password reset token successfully sent on your registered Mobile number.
                    </div>
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
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>

    <script type="text/javascript" src="{{asset('')}}assets/js/core/libraries/jquery.min.js"></script>
    <script type="text/javascript" src="{{asset('')}}assets/js/core/jquery.validate.min.js"></script>
    <script type="text/javascript" src="{{asset('')}}assets/js/core/jquery.form.min.js"></script>
    <script type="text/javascript" src="{{asset('')}}assets/js/core/sweetalert2.min.js"></script>
    <script type="text/javascript" src="{{asset('')}}assets/js/plugins/forms/selects/select2.min.js"></script>
    <script src="{{asset('')}}assets/js/core/snackbar.js"></script>
    <script>
        $(document).ready(function() {
            var number = 1 + Math.floor(Math.random() * 100000);
            $('#capcha').text(number);
            $(".login-form").validate({
                rules: {
                    mobile: {
                        required: true,
                        minlength: 10,
                        number: true,
                        maxlength: 11
                    },
                    password: {
                        required: true,
                    }
                },
                messages: {
                    mobile: {
                        required: "Please enter mobile number",
                        number: "Mobile number should be numeric",
                        minlength: "Your mobile number must be 10 digit",
                        maxlength: "Your mobile number must be 10 digit"
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
                    }
                },
                messages: {
                    slug: {
                        required: "Please select member type",
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
                                notify(data.message, 'warning');
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
                        number: true
                    },
                    password: {
                        required: true,
                    }
                },
                messages: {
                    mobile: {
                        required: "Please enter reset token",
                        number: "Reset token should be numeric",
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
                                notify(data.message, 'warning');
                            }
                        },
                        error: function(errors) {
                            swal.close();
                            if (errors.status == '400') {
                                notify(errors.responseJSON.status, 'warning');
                            } else if (errors.status == '422') {
                                $.each(errors.responseJSON.errors, function(index, value) {
                                    form.find('[name="' + index + '"]').closest('div.form-group').append('<p class="error">' + value + '</span>');
                                });
                                form.find('p.error').first().closest('.form-group').find('input').focus();
                                setTimeout(function() {
                                    form.find('p.error').remove();
                                }, 5000);
                            } else {
                                notify('Something went wrong, try again later.', 'warning');
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
                                notify(errors.responseJSON.status, 'warning');
                            } else {
                                notify('Something went wrong, try again later.', 'warning');
                            }
                        }
                    });
                }
            });



        });

        // function notify(msg, type="success"){
        //     let snackbar  = new SnackBar;
        //     snackbar.make("message",[
        //         msg,
        //         null,
        //         "bottom",
        //         "right",
        //         "text-"+type
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
                    url: '{{route('
                    authReset ')}}',
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
<footer>
    <div class="row" style="box-shadow: 0 -8px 11px 3px #ccc6; background: #f5f5f5; padding: 15px;">
        <div class="col-md-8" style="display: flex; justify-content: space-evenly; width: 50%;">
            <div class="text2"><a href="">Grievance</a></div>
            <div class="text2"><a href="">Terms & Conditions</a></div>
            <div class="text2"><a href="">FAQs</a></div>
            <div class="text2"><a href="">Privacy Policy</a></div>
            <div class="text2"><a href="">Register Complaint</a></div>

        </div>
        <div class="col-lg-4">
            <a>powered by</a>
        </div>
    </div>

</footer>

</html>