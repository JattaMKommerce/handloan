@php
    $name = explode(" ", Auth::user()->name);
@endphp

@extends('layouts.app')
@section('title', "UPI Service")
@section('pagetitle', "UPI Service")
@php
    $table = "yes";
@endphp

@section('content')
<div class="content">
    @if(!$agent)
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">Merchant Onboard</h4>
                    </div>
                    <div class="panel-body">
                        <form action="{{route('upipay')}}" method="post" id="transactionForm">
                            <input type="hidden" name="type" value="addvpa"> 
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="form-group col-md-3">
                                    <label>Merchant Business Name </label>
                                    <input type="text" class="form-control" autocomplete="off" name="merchantBusinessName" placeholder="Enter Your merchantBusinessName" value="{{isset($name[0]) ? $name[0] : ''}}" required>
                                </div>

                                <div class="form-group col-md-3">
                                    <label>VPA (@yesbank)</label>
                                    <input type="text" class="form-control" name="merchantVirtualAddress" onchange="verifyUpi()" autocomplete="off" placeholder="Enter Your VPA@yesbank" value="" required>
                                </div>
                                
                                <div class="form-group col-md-3">
                                    <label>Pancard</label>
                                    <input type="text" class="form-control" name="panNo" autocomplete="off" placeholder="Enter Your Pancard" value="{{Auth::user()->pancard}}" required>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Email </label>
                                    <input type="email" class="form-control" autocomplete="off" name="contactEmail" placeholder="Enter Your Email" value="{{Auth::user()->email}}" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="form-group col-md-3">
                                    <label>Merchant BusinessType </label>
                                    <select name="merchantBusinessType" class="form-control select"  required>
                                        <option value="">Select merchantBusinessType</option>
                                        <option value="1">Individual- HUF</option>
                                        <option value="2">Partnership</option>
                                        <option value="3">Companies registered under AcT</option>
                                        <option value="4">Govt/ Govt Undertakings</option>
                                        <option value="41">Proprietor</option>
                                        <option value="42">Individuals / Professionals</option>
                                        <option value="44">Regd Trusts</option>
                                        <option value="45">LLPs</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-3">
                                    <label>Mobile</label>
                                    <input type="text" pattern="[0-9]*" maxlength="10" minlength="10" class="form-control" name="mobile" autocomplete="off" placeholder="Enter Your Mobile" value="{{Auth::user()->mobile}}" required>
                                </div>

                                <div class="form-group col-md-3">
                                    <label>Gstn </label>
                                    <input type="text" class="form-control" autocomplete="off" name="gstn"  placeholder="Enter Your GST No">
                                </div>
                                <div class="form-group col-md-3">
                                    <label>State</label>
                                    <select name="state" class="form-control select"  required>
                                        <option value="">Select State</option>
                                        @foreach ($mahastate as $state)
                                        <option value="{{$state->stateid}}">{{$state->statename}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>City</label>
                                    <input type="text" class="form-control" autocomplete="off" name="city"  value="{{Auth::user()->city}}" placeholder="Enter Your City" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-3">
                                    <label>Pincode </label>
                                    <input type="text" class="form-control" autocomplete="off" name="pinCode" placeholder="Enter Your Pincode" pattern="[0-9]*" value="{{Auth::user()->pincode}}" maxlength="6" minlength="6" required>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Address </label>
                                    <input type="text" class="form-control" autocomplete="off" name="address" placeholder="Enter Your Address" value="{{Auth::user()->address}}" required>
                                </div>
                            </div>
                            
                            <div class="form-group text-center">
                                <button type="submit" class="btn bg-info bg-teal-400 btn-labeled btn-rounded legitRipple btn-lg" data-loading-text="<b><i class='fa fa-spin fa-spinner'></i></b> Submitting"><b><i class=" icon-paperplane"></i></b> Submit</button>
                            </div>
                        </form>
                    </div> 
                </div>
            </div>
        </div>
    @else
        <div class="row ">
            <!--<div class="col-6">-->
            <!--    <div class="panel panel-default">-->
            <!--        <div class="panel-heading">-->
            <!--            <h4 class="panel-title">Upi Collection</h4>-->
            <!--        </div>-->
            <!--        <form action="{{route('upipay')}}" method="post" id="upiTransactionForm">-->
            <!--            {{ csrf_field() }}-->
            <!--            <input type="hidden" name="type" value="collect">-->
            <!--            <div class="panel-body">-->
            <!--                <div class="form-group">-->
            <!--                    <label>UPI ID </label>-->
            <!--                    <input type="text" class="form-control" autocomplete="off" name="vpa" placeholder="Enter Your vpa" value="" required>-->
            <!--                </div>-->

            <!--                <div class="form-group">-->
            <!--                    <label>Amount</label>-->
            <!--                    <input type="text" class="form-control" name="amount" autocomplete="off" placeholder="Enter Your amount" value="" required>-->
            <!--                </div>-->
                            
            <!--                <div class="form-group">-->
            <!--                    <label>Remark</label>-->
            <!--                    <input type="text" class="form-control" name="txnNote" autocomplete="off" placeholder="Enter Your remark" value="" required>-->
            <!--                </div>-->
            <!--            </div>-->
            <!--            <div class="card-footer text-center">-->
            <!--                <button type="submit" class="btn btn-primary ">Proceed</button>-->
            <!--            </div>-->
            <!--        </form>-->
            <!--    </div>-->
            <!--</div>-->

            <div class="col-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">QrCode Scan & Pay</h4>
                    </div>
                    <div class="panel-body canvas_div_pdf" id="receptTable" style="text-align: center;">
                        <h4>{{\Auth::user()->company->companyname}} QrCode</h4><br>
                        <div class="qrimage"></div><br>
                        <h4>{{$agent->merchantBusinessName ?? ''}}</h4>
                        <h5>{{$agent->vpaaddress ?? ''}}</h5>
                    </div>
                    
                    <div class="panel-footer" style="text-align: center;">
                        <button type="button" class="btn btn-primary" onclick="getPDF('{{$agent->vpaaddress ?? ''}}')">Download</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('script')
<script src="{{ asset('/assets/js/core/jQuery.print.js') }}"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.min.js"></script>
<script type="text/javascript" src="https://html2canvas.hertzen.com/dist/html2canvas.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.qrcode/1.0/jquery.qrcode.min.js"></script> 
<script type="text/javascript">
    $(document).ready(function () {
        $('.mydatepic').datepicker({
            'autoclose':true,
            'clearBtn':true,
            'todayHighlight':true,
            'format':'dd-mm-yyyy',
        });
        
        $('#print').click(function(){
            $('#receptTable').print();
        });

        var vpa="{{$agent->vpaaddress ?? ''}}";
        var merchantBusinessName="{{$agent->merchantBusinessName ?? ''}}";
        var vpastring='upi://pay?pa='+vpa+'&pn='+merchantBusinessName+'&tr=EZV2021101113322400027817&am=&cu=INR';
        jQuery(".qrimage").qrcode({
            width  : 250,
            height : 250,
            text: vpastring
        });

        $( "#transactionForm" ).validate({
            rules: {
                merchantBusinessName: {
                    required: true
                   
                },
                merchantVirtualAddress: {
                    required: true
                    
                },
                panNo: {
                    required: true
                    
                },
                contactEmail: {
                    required: true
                    
                },
                merchantBusinessType: {
                    required: true
                    
                },
                mobile: {
                    required: true,
                    number : true,
                    min: 10
                },
                panNo: {
                    required: true
                    
                },
                state: {
                    required: true
                    
                },
                city: {
                    required: true
                
                },
                picode: {
                    required: true,
                    number : true,
                    min: 6
                },
                address: {
                    required: true
                   
                },
            },
            messages: {
                merchantBusinessName: {
                    required: "Please enter merchantBusinessName",
                    
                },
                merchantVirtualAddress: {
                    required: "Please enter merchantVirtualAddress"
                   
                },
                panNo: {
                    required: "Please enter panNo"
                    
                },
                contactEmail: {
                    required: "Please enter contactEmail"
                    
                },
                mobile: {
                    required: "Please enter mobile",
                    number : "Mobile should be numeric",
                    min: "minimum lenght should be 10 digit"
                    
                },
                state: {
                    required: "Please enter state"
                    
                },
               
                picode: {
                    required: "Please enter picode",
                    number:"Pincode should be numeric",
                    min: "Minimum length should be 6"
                    
                },
                address: {
                    required: "Please enter address"
                    
                },
                city: {
                    required: "Please enter city"
                    
                },
                
                
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
                    },
                    success:function(data){
                        form.find('button[type="submit"]').button('reset');
                        if(data.status == "TXN"){
                            notify("VPA Successfully Created", 'success');
                            
                            setTimeout(function(){
                                window.location.reload();
                            }, 2000);
                        }else{
                            notify(data.message, 'warning');
                        }
                    },
                    error: function(errors) {
                        showError(errors, form);
                    }
                });
            }
        });
        
        $( "#upiTransactionForm" ).validate({
            rules: {
                vpa: {
                    required: true
                   
                },
                amount: {
                    required: true
                    
                },
                txnNote: {
                    required: true
                }
            },
            messages: {
                vpa: {
                    required: "Please enter value",
                    
                },
                amount: {
                    required: "Please enter value"
                   
                },
                txnNote: {
                    required: "Please enter value"
                    
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
                var form = $('#upiTransactionForm');
                var id = form.find('[name="id"]').val();
                form.ajaxSubmit({
                    dataType:'json',
                    beforeSubmit:function(){
                        form.find('button[type="submit"]').button('loading');
                    },
                    success:function(data){
                        form.find('button[type="submit"]').button('reset');
                        if(data.status == "TXN"){
                            notify("Collect Request Submitted Successfully", 'success');
                            
                        }else{
                            notify(data.message, 'warning');
                        }
                    },
                    error: function(errors) {
                        showError(errors, form);
                    }
                });
            }
        });
    });
    
    function verifyUpi(ele){
        var upi = $("[name='merchantVirtualAddress']").val();
        
        if(upi != ""){
            $.ajax({
                url:  "{{route('upipay')}}",
                type: "POST",
                dataType:'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {'upi':upi, 'type' : "vpaverify"},
                success: function(data){
                    if(data.status != "TXN"){
                        $( "#transactionForm" ).find('[name="merchantVirtualAddress"]').closest('div.form-group').append('<p class="error">'+data.message+'</span>');
                        $( "#transactionForm" ).find('p.error').first().closest('.form-group').find('input').focus();
                        setTimeout(function () {
                            $( "#transactionForm" ).find('p.error').remove();
                        }, 5000);
                    }else{
                        notify("VPA is available", 'success');
                    }
                }
            });
        }
    }
    
    function getPDF(upi){

		var HTML_Width = $(".canvas_div_pdf").width();
		var HTML_Height = $(".canvas_div_pdf").height();
		var top_left_margin = 15;
		var PDF_Width = HTML_Width+(top_left_margin*2);
		var PDF_Height = (PDF_Width*1.5)+(top_left_margin*2);
		var canvas_image_width = HTML_Width;
		var canvas_image_height = HTML_Height;
		
		var totalPDFPages = Math.ceil(HTML_Height/PDF_Height)-1;
		

		html2canvas($(".canvas_div_pdf")[0],{allowTaint:true}).then(function(canvas) {
			canvas.getContext('2d');
			
			console.log(canvas.height+"  "+canvas.width);
			
			
			var imgData = canvas.toDataURL("image/jpeg", 1.0);
			var pdf = new jsPDF('p', 'pt',  [PDF_Width, PDF_Height]);
		    pdf.addImage(imgData, 'JPG', top_left_margin, top_left_margin,canvas_image_width,canvas_image_height);
			
			
			for (var i = 1; i <= totalPDFPages; i++) { 
				pdf.addPage(PDF_Width, PDF_Height);
				pdf.addImage(imgData, 'JPG', top_left_margin, -(PDF_Height*i)+(top_left_margin*4),canvas_image_width,canvas_image_height);
			}
			
		    pdf.save(upi+".pdf");
        });
	};
</script>
@endpush