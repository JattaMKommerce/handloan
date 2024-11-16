<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">    
    
    <title>Delete Account Request </title>
    <style>
        *,*::before,*::after {
  box-sizing: border-box;
}
body {
  margin: 0;
  padding: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #eee;
  color: #333333;
  min-height: 90vh;
  font-size: 14px;
  font-family: Helvetica, Arial, sans-serif;
}
form {
  background: #fff;
  padding: 20px;
  border-radius: 6px;
}
h1 {
  font-size: 22px;
  margin: 0;
}
p {
  margin: 15px 0 0 0;
}
fieldset {
  border: none;
  padding: 0;
}
label {
  display: block;
  font-size: 16px;
  margin-top: 20px;
  color: #5d5d5d;
  font-weight: bold;
}
input {
  display: block;
  width: 100%;
  padding: 10px;
  border: 2px solid #d0d0d0;
  margin-top: 5px;
  border-radius: 3px;
}
button {
  font-size: 16px;
  margin: 30px 0 0;
  padding: 10px 22px;
  cursor: pointer;
  border: 0;
  border-radius: 3px;
  background: #5d5d5d;
  color: #fff;
  &:hover {
    background: #444444;
  }
}

.wrapper {
  position: relative;
  display: inline-block;
}

video {
  position: absolute;
  pointer-events: none;
  width: 35vw;
  transform: translate(-50%, -50%) scale(0);
  transition: 0.15s ease-out transform;
  &.is-visible {
    transform: translate(-50%, -50%) scale(1);
  }
}
    </style>
    <script type="text/javascript" src="{{asset('')}}assets/js/plugins/loaders/pace.min.js"></script>
    <script type="text/javascript" src="{{asset('')}}assets/js/core/libraries/jquery.min.js"></script>
    <script type="text/javascript" src="{{asset('')}}assets/js/core/libraries/bootstrap.min.js"></script>
    <script type="text/javascript" src="{{asset('')}}assets/js/plugins/loaders/blockui.min.js"></script>
    <!-- /core JS files -->

    <!-- Theme JS files -->
  
    <script type="text/javascript" src="{{asset('')}}assets/js/core/jquery.validate.min.js"></script>
    <script type="text/javascript" src="{{asset('')}}assets/js/core/jquery.form.min.js"></script>
    <script type="text/javascript" src="{{asset('')}}assets/js/core/sweetalert2.min.js"></script>
    <script type="text/javascript" src="{{asset('')}}assets/js/plugins/forms/selects/select2.min.js"></script>
    <script src="{{asset('')}}assets/js/core/snackbar.js"></script>    
</head>
<body>

<form  action="{{route('unsubscriberequest')}}" method="POST" class="login-form">
  {{ csrf_field() }}
  <img src="https://login.amtechpe.in/public/theme/images/logo-full3.png" class="logo_img" style="width: 260px;height: 100px;" >      
  <h1>Delete Account Request</h1>
  <p>We're sorry to see you go! Enter your email address to delete account request from Iyda Payments.</p>
   <p style="color:red"><b class="errorText"></b></p>
   <p style="color:teal"><b class="successText"></b></p>
  <fieldset>
    <label for="email">Email Address</label>
    <input name="email" type="email" id="email" required placeholder="Enter your regiter email">
  </fieldset>
  <fieldset>
    <label for="mobile">Mobile Number</label>
    <input type="text"  name="mobile" id="mobile" required placeholder="Enter your regiter mobile number">
  </fieldset>
  <div class="wrapper">
    <button>Unsubscribe</button>
  </div>
</form>

    <div id="message"></div>

    <script src="unsubscribe.js"></script>
</body>

<script>
  $( document ).ready(function() {
  $( ".login-form" ).validate({
                rules: {
                    mobile: {
                        required: true,
                        minlength: 10,
                        number : true,
                        maxlength: 11
                    },
                    email: {
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
                errorPlacement: function ( error, element ) {
                    if ( element.prop("tagName").toLowerCase() === "select" ) {
                        error.insertAfter( element.closest( ".form-group" ).find(".select2") );
                    } else {
                        error.insertAfter( element );
                        $
                    }
                },
                submitHandler: function () {
                    var form = $('.login-form');
                    form.ajaxSubmit({
                        dataType:'json',
                        beforeSubmit:function(){
                            swal({
                                title: 'Wait!',
                                text: 'We are checking your login credential',
                                onOpen: () => {
                                    swal.showLoading()
                                },
                                allowOutsideClick: () => !swal.isLoading()
                            });
                        },
                        success:function(data){
                            swal.close();
                            if(data.status == "success"){
                                swal({
                                    type: 'success',
                                    title : 'Success',
                                    text: 'Request Successfully Submitted.',
                                    showConfirmButton: false,
                                    timer: 2000,
                                    onClose: () => {
                                        window.location.reload();
                                    },
                                });
                            }
                        },
                        error: function(errors) {
                            swal.close();
                            if(errors.status == '400'){
                                $('b.errorText').text(errors.responseJSON.status);
                                setTimeout(function(){
                                    $('b.errorText').text('');
                                }, 5000);
                            }else{
                                $('b.errorText').text('Something went wrong, try again later.');
                                setTimeout(function(){
                                    $('b.errorText').text('');
                                }, 5000);
                            }
                        }
                    });
                }
            });
  });   
</script>

</html>
