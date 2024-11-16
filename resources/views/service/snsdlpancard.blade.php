@extends('layouts.app')
@section('title', "S-Nsdl Pancard")
@section('pagetitle', "S-Nsdl Pancard")
@php
    $table = "yes";
@endphp

@section('content')
<div class="content">
    
        <div class="rows">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading py-3">
                        <h2 class="panel-title">S-Nsdl Pancard</h2>
                    </div>
                    <div class="panel-body">
                        <form action="{{route('spayment')}}" method="post" id="transactionForm"> 
                        <input type="hidden" name="actiontype" value="snsdlintiate"/>
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label>Title</label>
                                    <select name="title" class="form-control select"  required>
                                        <option value="">Select Title</option>
                                        <option value="Mr">Mr</option>
                                        <option value="Mrs">Mrs</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>First Name </label>
                                    <input type="text" class="form-control" autocomplete="off" name="f_name" placeholder="Enter Your First name" value="" required>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Middle Name </label>
                                    <input type="text" class="form-control" name="m_name" autocomplete="off" placeholder="Enter Your Middle Name" value="" >
                                </div>
                            </div>

                            <div class="row">

                                <div class="form-group col-md-6">
                                    <label>Last Name </label>
                                    <input type="text" class="form-control" name="l_name" autocomplete="off" placeholder="Enter Your Last name" value="" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Email </label>
                                    <input type="email" class="form-control" autocomplete="off" name="emailid" placeholder="Enter Your Email" value="" required>
                                </div>

                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>Gender</label>
                                    <select name="gender" class="form-control select"  required>
                                        <option value="">Select Gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Mode Type</label>
                                    <select name="panmode" class="form-control select"  required>
                                        <option value="">Select Mode</option>
                                        <option value="P">Physical Pan</option>
                                        <option value="E">Electronic Pan</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group text-center">
                                <button type="submit" class="btn bg-teal-400 btn-labeled btn-rounded legitRipple btn-lg" data-loading-text="<b><i class='fa fa-spin fa-spinner'></i></b> Submitting"><b><i class=" icon-paperplane"></i></b> Submit</button>
                            </div>
                        </form>
                    </div> 
                </div>
            </div>
        </div>
    <form method="post" action="URL received from generate url API" id="nsdlsubmit">
  <input type="hidden" id="endata" name="encdata" value="" >
  <input hidden type="submit" value="Submit">
</form>

</div>

<!-- Footer -->
<div class="footer text-muted">
    <div class="row row_title">
        <div class="col-md-6 tnc">
            <h4><strong>Important T&amp;Cs:</strong></h4>
            <ul>
                <li>The fee for processing PAN application is ₹107 inclusive of GST.</li>
                <li>PAN card application can be processed using eKYC or physical documents.</li>
            </ul>
        </div>
        <div class="col-md-6 text-right">
            <div>Powered by</div>
            <img src="{{asset('')}}/assets/images/uti.png" style="position: relative;">
        </div>
    </div>
</div>
<!-- /footer -->

@endsection

@push('script')
	<script type="text/javascript">
    $(document).ready(function () {

    //   $('form#transactionForm').submit(function() {
    //         var form= $(this);
            
    //         $(this).ajaxSubmit({
    //             dataType:'json',
    //             beforeSubmit:function(){
    //                 swal({
    //                     title: 'Wait!',
    //                     text: 'We are working on request.',
    //                     onOpen: () => {
    //                         swal.showLoading()
    //                     },
    //                     allowOutsideClick: () => !swal.isLoading()
    //                 });
    //             },
    //             success:function(data){
    //                 swal.close();
    //                 console.log(data);
    //                 switch(data.statuscode){
    //                     case 'TXN':
    //                         window.open(data.data,'_blank');
    //                         break;
                        
    //                     default:
    //                       swal.close();
    //                         notify(data.message, 'danger');
    //                         break;
    //                 }
    //             },
    //             error: function(errors) {
    //                 swal.close();
    //                 if(errors.status == '400'){
    //                     notify(errors.responseJSON.message, 'danger');
    //                 }else{
    //                     swal(
    //                       'Oops!',
    //                       'Something went wrong, try again later.',
    //                       'error'
    //                     );
    //                 }
    //             }
    //         });
    //         return false;
    //     });
        
        $( "#transactionForm" ).validate({
            rules: {
                provider_id: {
                    required: true,
                    number : true,
                },
                number: {
                    required: true,
                    number : true,
                    minlength: 8
                },
                amount: {
                    required: true,
                    number : true,
                    min: 10
                },
            },
            messages: {
                provider_id: {
                    required: "Please select  operator",
                    number: "Operator id should be numeric",
                },
                number: {
                    required: "Please enter  number",
                    number: "Mobile number should be numeric",
                    min: "Mobile number length should be atleast 8",
                },
                amount: {
                    required: "Please enter  amount",
                    number: "Amount should be numeric",
                    min: "Min  amount value rs 10",
                }
            },
            errorElement: "p",
            errorPlacement: function ( error, element ) {
                if ( element.prop("tagName").toLowerCase() === "select" ) {
                    error.insertAfter( element.closest( ".form-group" ).find(".select2") );
                } else {
                    error.insertAfter( element );
                }
            },
            submitHandler: function () {
                var form = $('#transactionForm');
                var id = form.find('[name="id"]').val();
                form.ajaxSubmit({
                    dataType:'json',
                    beforeSubmit:function(){
                        form.find('button[type="submit"]').button('loading');
                        swal({
                            title: 'Wait!',
                            text: 'We are working on request.',
                            onOpen: () => {
                                swal.showLoading()
                            },
                            allowOutsideClick: () => !swal.isLoading()
                        });
                    },
                    success:function(data){
                        swal.close();
                        form.find('button[type="submit"]').button('reset');
                        if(data.status=="TXN"){
                            console.log(data.data.encdata);
                            $("#endata").val(data.data.encdata);
                            $("textarea#encdata").val(data.data.encdata);
                            $('#nsdlsubmit').attr('action', data.data.url).submit();
                        }else{
                            notify(data.message, 'danger');
                             
                        }

                    },
                    error: function(errors) {
                    swal.close();
                    if(errors.status == '400'){
                        notify(errors.responseJSON.message, 'danger');
                    }else{
                        swal(
                          'Oops!',
                          'Something went wrong, try again later.',
                          'error'
                        );
                    }
                    }
                });
            }
        });
    });
       

</script>
@endpush
<style type="text/css">
  
button.btn.pull-right {
    background-color: red;
    color: white;
}
button.btn.bg-teal-400.btn-labeled.btn-rounded.legitRipple.btn-lg {
    background-color: blue;
    color: white;
   
    border-radius: 30px;
}
a.btn.bg-slate.legitRipple.pull-right {
    background-color: blue;
    color: white;
    margin-bottom: 7px;
    border-radius: 10px;
}
.panel-title{
    font-size:20px;
}
.table thead th {
   
    font-size: 16px!important;
   
}
.table td, .table th{
    font-size: 16px!important;
}
.table .bg-danger {
   background-color: #f5365c !important;
    width: auto;
    color: white;
    margin-left: 0!important;
    text-align: center;
    border-radius: 8px;
    padding: 5px 25px;
}

.table .bg-success {
    background-color: #2dce89 !important;
    padding: 5px 20px;
    border-radius: 8px;
    color: #fff;
}

.table .bg-warning {
    background-color: #fb6340 !important;
    padding: 5px 16px;
    border-radius: 8px;
    color: white;
}

.tnc{
    
    margin-left:15rem!important;
}

</style>