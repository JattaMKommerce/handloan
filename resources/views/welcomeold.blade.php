 <!DOCTYPE html>
 <html lang="en">

 <head>
     <meta charset="utf-8">
     <meta http-equiv="X-UA-Compatible" content="IE=edge">
     <meta name="viewport" content="width=device-width, initial-scale=1">
     <meta name="csrf-token" content="{{ csrf_token() }}">
     <title>Login To - {{$mydata['company']->companyname}}</title>
     <!-- Page Icons -->
     <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
     <link rel="icon" href="favicon.ico" type="image/x-icon">

     <!-- Page Title -->
     <link href="{{asset('')}}assets/css/bootstrap.css" rel="stylesheet" type="text/css">
     <link href="{{asset('')}}assets/css/icons/icomoon/styles.css" rel="stylesheet" type="text/css">
     <link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">


     {{-- <link href="{{asset('')}}assets/css/core.css" rel="stylesheet" type="text/css">
     <link href="{{asset('')}}assets/css/components.css" rel="stylesheet" type="text/css">
     <link href="{{asset('')}}assets/css/colors.css" rel="stylesheet" type="text/css"> --}}
     <link href="{{asset('')}}assets/css/snackbar.css" rel="stylesheet">

     <!-- Stylesheets -->
     <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@300;400;600;700&amp;display=swap">
     {{-- <link rel="stylesheet" href="{{asset('')}}assets/newlogin/css/bootstrap.min.css"> --}}
     {{-- <link rel="stylesheet" href="{{asset('')}}assets/newlogin/css/all.css"> --}}
     <link rel="stylesheet" href="{{asset('')}}assets/newlogin/css/style.min.css">


     <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.14.0/css/all.css">
     <!-- 
<link rel="stylesheet" href="css/fontawsome.css">
<link rel="stylesheet" href="css/fontawsome.min.css"> -->


     <link rel="stylesheet" href="{{asset('')}}assets/newlogin/css/style.min.css">
     <link rel="stylesheet" href="{{asset('')}}assets/newlogin/css/login.min.css">

     <style type="text/css">
         .modal-dialog {
             margin-top: 100px;
         }

         .error {
             color: red
         }

         input.form-control {
             font-size: 19px;
         }

         .btn.primary-btn,
         .btn.btn-primary {
             color: #fff;
             background: #ff80aa;
         }

         .header__logo img {
             max-width: 25rem;
         }

         .footer {

             padding: 0rem 0 1rem !important;

         }

         .header {

             padding-top: 0rem !important;

         }

         .footer__copyright {
             font-size: 1.8rem;

         }

         .footer__links a {
             font-size: 1.8rem;

         }

         html,
         body {
             height: 100%;
         }

         body {
             margin: 0px;
             padding: 0px;
         }

         .register {

             margin-top: 40px;
             padding-top: 150px;
             margin-bottom: 20px;

         }

         .select {
             height: 38px !important;
             font-size: 17px;
         }

         .textarea {
             font-size: 17px;
         }

         .select,
         .input {
             margin-top: 5px;
             margin-bottom: 5px;
         }

         .states {

             margin-top: 50px;
         }

         #registerModal {
             overflow-x: hidden !important;
             overflow-y: auto !important;
         }
     </style>

     <style type="text/css">
         @keyframes tawkMaxOpen {
             0% {
                 opacity: 0;
                 transform: translate(0, 30px);
                 ;
             }

             to {
                 opacity: 1;
                 transform: translate(0, 0px);
             }
         }

         @-moz-keyframes tawkMaxOpen {
             0% {
                 opacity: 0;
                 transform: translate(0, 30px);
                 ;
             }

             to {
                 opacity: 1;
                 transform: translate(0, 0px);
             }
         }

         @-webkit-keyframes tawkMaxOpen {
             0% {
                 opacity: 0;
                 transform: translate(0, 30px);
                 ;
             }

             to {
                 opacity: 1;
                 transform: translate(0, 0px);
             }
         }

         #NsumiMp-1608839283413 {
             outline: none !important;
             visibility: visible !important;
             resize: none !important;
             box-shadow: none !important;
             overflow: visible !important;
             background: none !important;
             opacity: 1 !important;
             filter: alpha(opacity=100) !important;
             -ms-filter: progid:DXImageTransform.Microsoft.Alpha(Opacity1) !important;
             -moz-opacity: 1 !important;
             -khtml-opacity: 1 !important;
             top: auto !important;
             right: 10px !important;
             bottom: 90px !important;
             left: auto !important;
             position: fixed !important;
             border: 0 !important;
             min-height: 0 !important;
             min-width: 0 !important;
             max-height: none !important;
             max-width: none !important;
             padding: 0 !important;
             margin: 0 !important;
             -moz-transition-property: none !important;
             -webkit-transition-property: none !important;
             -o-transition-property: none !important;
             transition-property: none !important;
             transform: none !important;
             -webkit-transform: none !important;
             -ms-transform: none !important;
             width: auto !important;
             height: auto !important;
             display: none !important;
             z-index: 2000000000 !important;
             background-color: transparent !important;
             cursor: auto !important;
             float: none !important;
             border-radius: unset !important;
             pointer-events: auto !important
         }

         #rEV5Vmf-1608839283416.open {
             animation: tawkMaxOpen .25s ease !important;
         }
     </style>
     <!-- Core JS files -->
     <script type="text/javascript" src="{{asset('')}}assets/js/core/libraries/jquery.min.js"></script>
     <script type="text/javascript" src="{{asset('')}}assets/js/core/libraries/bootstrap.min.js"></script>
     <!--<script type="text/javascript" src="{{asset('')}}assets/js/core/app.js"></script>-->
     <script type="text/javascript" src="{{asset('')}}assets/js/core/jquery.validate.min.js"></script>
     <script type="text/javascript" src="{{asset('')}}assets/js/core/jquery.form.min.js"></script>
     <script type="text/javascript" src="{{asset('')}}assets/js/core/sweetalert2.min.js"></script>
     <script type="text/javascript" src="{{asset('')}}assets/js/plugins/forms/selects/select2.min.js"></script>
     <script src="{{asset('')}}assets/js/core/snackbar.js"></script>
     <script>
         $(document).ready(function() {

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

         //  function notify(msg, type = "success") {
         //      let snackbar = new SnackBar;
         //      snackbar.make("message", [
         //          msg,
         //          null,
         //          "bottom",
         //          "right",
         //          "text-" + type
         //      ], 5000);
         //  }




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
 </head>

 <body class="border border-dark" data-new-gr-c-s-check-loaded="14.990.0" data-gr-ext-installed="">
     <div class="container shape-bg " style="padding-right: 0px; height:95%">
         <header class="header">
             <div class="header__logo">

                 @if($mydata['company']->logo)
                 <img src="{{asset('public/logos')}}/{{$mydata['company']->logo}}" style="width:240px; height: 105px;">
                 @endif

             </div>

             <div class="header__menu">
                 <a href="javascript:;">Aeps</a>
                 <span class="divider">|</span>
                 <a href="javascript:;">Recharge</a>
                 <span class="divider">|</span>
                 <a href="javascripe:;">Bill Payment</a>
                 <span class="divider">|</span>
                 <a href="javascript:;">DMT</a>
             </div>
         </header>

         <section id="signInForm" class="formPage">
             <div class="formPage__card animated appeared fadeInUp visible" data-animation="appeared fadeInUp" data-animation-delay="200">
                 <h2 class="title">Sign In</h2>
                 <form form action="{{route('authCheck')}}" method="POST" class="login-form">
                     {{ csrf_field() }}
                     <p style="color:red"><b class="errorText"></b></p>
                     <p style="color:teal"><b class="successText"></b></p>
                     <div class="form-group">
                         <label for="" class="label">Mobile Number</label>
                         <input type="text" class="form-control" name="mobile" placeholder="User name" pattern="[0-9]*" maxlength="11" minlength="10" required>
                     </div>
                     <div class="form-group">
                         <label for="" class="label">Password</label>
                         <input type="password" class="form-control" name="password" placeholder="Password" required>
                         <div class="formdata">

                         </div>
                         <a href="javascript:void(0)" onclick="forgetPassword()">Forgot password?</a>
                     </div>
                     <div class="form-group margin-bottom-20 padding-top-30">
                         <button type="submit" class="btn btn-primary btn-block">Sign in <i class="icon-circle-right2 position-right" style="margin-top:4px; margin-left:10px;"></i></button>
                     </div>
                     <div class="form-group text-center">
                         <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#registerModal"><i class="icon-plus2"></i> New Customer </button>
                     </div>
                 </form>

                 <div class="backBlock">
                     <!--<a href="#" class="backBlock__button">-->
                     <!--    <i class="fas fa-long-arrow-alt-left"></i>-->
                     <!--    <span>Back to Home</span>-->
                     <!--</a>-->
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

             <div id="registerModal" class="modal fade center register" data-backdrop="false" data-keyboard="false">
                 <div class="modal-dialog modal-lg">
                     <div class="modal-content" style="padding-top:20px!Important">
                         <div class="modal-header pt-3">
                             <h2 class="modal-title pull-left text-white">Member Registration</h5>
                                 <button type="button" class="close" data-dismiss="modal" style="color:black; font-size:22px;">&times;</button>
                         </div>
                         <div class="modal-body" style="padding:15px">
                             <form id="registerForm" action="{{route('register')}}" method="post">
                                 {{ csrf_field() }}
                                 <p style="color:red"><b class="errorText1"></b></p>
                                 <p style="color:teal"><b class="successText1"></b></p>
                                 <legend style="font-size:20px; margin-top:-20px">Member type</legend>
                                 <div class="row">
                                     <div class="form-group col-md-4">
                                         <label>Member Type</label>
                                         <select name="slug" class="form-control select" required="">
                                             <option value="">Select Member Type</option>
                                             @foreach ($roles as $role)
                                             <option value="{{$role->slug}}">{{$role->name}}</option>
                                             @endforeach
                                         </select>
                                     </div>
                                 </div>

                                 <legend style="font-size:20px;">Personal Details</legend>
                                 <div class="row">
                                     <div class="form-group col-md-4">
                                         <label for="exampleInputEmail1" class="text-uppercase">Name</label>
                                         <input type="text" name="name" class="form-control" placeholder="Enter your name" required>
                                     </div>
                                     <div class="form-group col-md-4">
                                         <label for="exampleInputPassword1" class="text-uppercase">Email</label>
                                         <input type="text" name="email" class="form-control" placeholder="Enter your email id" required>
                                     </div>
                                     <div class="form-group col-md-4">
                                         <label for="exampleInputPassword1" class="text-uppercase">Mobile</label>
                                         <input type="text" pattern="[0-9]*" maxlength="10" minlength="10" name="mobile" class="form-control" placeholder="Enter your mobile" required>
                                     </div>
                                 </div>
                                 <div class="row">
                                     <div class="form-group col-md-4">
                                         <label>State</label>
                                         <select name="state" class="form-control state select" required="">
                                             <option value="">Select State</option>
                                             @foreach ($state as $state)
                                             <option class="states" value="{{$state->state}}">{{$state->state}}</option>
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
                                         <textarea name="address" class="form-control textarea" rows="4" required="" placeholder="Enter Value"></textarea>
                                     </div>
                                 </div>

                                 <legend style="font-size:20px;">Kyc Information</legend>
                                 <div class="row">
                                     <div class="form-group col-md-4">
                                         <label>Shop Name</label>
                                         <input type="text" name="shopname" class="form-control" value="" required="" placeholder="Enter Value">
                                     </div>
                                     <div class="form-group col-md-4">
                                         <label>Pancard</label>
                                         <input type="text" id="pancard" name="pancard" class="form-control" value="" required="" placeholder="Enter Value">
                                     </div>
                                     <div class="form-group col-md-4">
                                         <label>Aadhar</label>
                                         <input type="text" id="aadharcard" name="aadharcard" required="" class="form-control" placeholder="Enter Value" pattern="[0-9]*" maxlength="12" minlength="12">
                                     </div>
                                 </div>
                                 <div class="text-center form-group">
                                     <button type="submit" class="btn btn-lg btn-primary">Submit</button>
                                     <!--<button type="button" class="close" data-dismiss="modal" style="color:black; font-size:22px;">Close</button>-->
                                 </div>
                             </form>
                         </div>
                     </div><!-- /.modal-content -->
                 </div><!-- /.modal-dialog -->
             </div>
             <div id="passwordModal" class="modal fade" data-backdrop="false" data-keyboard="false">
                 <div class="modal-dialog">
                     <div class="modal-content">
                         <div class="modal-header ">
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
                                 <div class="form-group">
                                     <button class="btn btn-primary btn-block text-uppercase waves-effect waves-light" type="submit" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Resetting">Reset Password</button>
                                 </div>
                             </form>
                         </div>
                     </div><!-- /.modal-content -->
                 </div><!-- /.modal-dialog -->
             </div>
         </section>





         <!--<footer class="footer">-->
         <!--    <div class="container">-->
         <!--        <div class="footer__grid">-->
         <!--            <div class="footer__copyright">-->
         <!--                © Gram Sathi 2021  All right Reserved.-->
         <!--            </div>-->
         <!--            <div class="footer__links">-->
         <!--                 <a href="javascript:;">Aeps</a>-->
         <!--                <a href="javascript:;">Recharge</a>-->
         <!--                <a href="javascripe:;">Bill Payment</a>-->
         <!--                <a href="javascript:;">DMT</a>-->
         <!--            </div>-->
         <!--        </div>-->
         <!--    </div>-->
         <!--</footer>-->

         <!--<div class="siteLoaderWrap" style="display: none;">-->
         <!--    <div class="siteLoaderWrap__container">-->
         <!--        <div class="spinner1"></div>-->
         <!--        <div class="spinner2"></div>-->
         <!--        <div class="spinner3"></div>-->
         <!--        <div class="spinner4"></div>-->
         <!--        <div class="spinner5"></div>-->
         <!--    </div>-->
         <!--</div>-->

         <!-- Javascripts -->
     </div>
     <footer>
         <div class="row">
             <!--<div class="col-md-6">-->
             <!--    <div class="footer__copyright text-center">-->
             <!--        © Gram Sathi 2021  All right Reserved.-->
             <!--     </div>-->
             <!--</div>-->

             <!--<div class="col-md-6">-->
             <!--    <div class="footer__links text-center">-->
             <!--         <a href="javascript:;">Aeps</a>-->
             <!--        <a href="javascript:;">Recharge</a>-->
             <!--        <a href="javascripe:;">Bill Payment</a>-->
             <!--        <a href="javascript:;">DMT</a>-->
             <!--    </div>-->
             <!--</div>-->
         </div>

     </footer>


     <div id="modal" class="modal fade" style="z-index:99999999 !important;" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false" aria-labelledby="modalLabel" aria-hidden="true"></div>




 </body>

 </html>