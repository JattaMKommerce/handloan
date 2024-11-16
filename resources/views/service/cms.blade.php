@extends('layouts.app')
@section('title', "CMS")
@section('pagetitle', "CMS")

@php
    $table = "yes";
@endphp


@push('css')
<link rel="stylesheet" type="text/css" href="https://admin.pixelstrap.com/viho/assets/css/datatables.css">
<link rel="stylesheet" type="text/css" href="https://admin.pixelstrap.com/viho/assets/css/datatable-extension.css">
@endpush

    @section('content')
      
      @yield('breadcrumb-list')

          <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 col-xl-12">
                    <div class="card card-absolute">
                        <div class="card-header bg-primary">
                            <h5 class="text-white">Account</h5>
                        </div>
                        <div class="card-body">
                            <form action="" method="post" id="transactionForm"> 
                                {{ csrf_field() }}
                                

                                <div class="row">
                                    
                                   
                                    <div class="form-group col-md-6">
                                       <button type="button" class="btn btn-warning btn-lg" data-loading-text="<b><i class='fa fa-spin fa-spinner'></i></b> Searching"><b><i class="icon-search4"></i></b> Generate URL</button>
                                    </div>
                                    <div class="form-group col-md-6">
                                    <div id="mydiv">
                            
                                    </div>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>

   
          </div>




@push('script')
<script type="text/javascript">
    $(document).ready(function () {

        $('.mydatepic').datepicker({
            'autoclose':true,
            'clearBtn':true,
            'todayHighlight':true,
            'format':'dd-mm-yyyy',
        });
        
         $('button').on('click', function () {
       
    $.ajax({
                url: "{{route('cmstransaction')}}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType:'json',
                
                beforeSend:function(){
                    swal({
                        title: 'Wait!',
                        text: 'We are processing your request.',
                        allowOutsideClick: () => !swal.isLoading(),
                        onOpen: () => {
                            swal.showLoading()
                        }
                    });
                },
                success: function(data){
                    swal.close();
                    //console.log(data)
                    if(data.statuscode == "TXN"){
                        var link = data.url;
                        
                      var html = [
                              
                              '<a href=' + link + ' target="_blank" class="btn btn-info btn-rounded legitRipple btn-lg">CLICK HERE</a>'
                              
                            ].join('\n');
                     
                          document.getElementById("mydiv").innerHTML = html;
                       
                    }else{
                        notify(data.message, 'danger');
                    }
                },
                error: function(error){
                    swal.close();
                    notify("Something went wrong", 'danger');
                }
            });
  
});

       
        
         $('form#fingkycForm').submit(function() {
            var form= $(this);
            var type = form.find('[name="type"]');
            $(this).ajaxSubmit({
                dataType:'json',
                beforeSubmit:function(){
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
                    
                    switch(data.statuscode){
                        case 'TXN':
                           
                            swal({
                                title:'Suceess', 
                                text : data.message, 
                                type : 'success',
                                onClose: () => {
                                    window.location.reload();
                                }
                            });
                            break;
                        case 'TXF':
                            notify(data.message, 'danger');
                            break;    
                        
                        default:
                            notify(data.message, 'danger');
                            break;
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
            return false;
        });
    });

    function getDistrict(ele){
        $.ajax({
            url:  "{{route('dmt1pay')}}",
            type: "POST",
            dataType:'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend:function(){
                swal({
                    title: 'Wait!',
                    text: 'We are fetching district.',
                    allowOutsideClick: () => !swal.isLoading(),
                    onOpen: () => {
                        swal.showLoading()
                    }
                });
            },
            data: {'type':"getdistrict", 'stateid':$(ele).val()},
            success: function(data){
                swal.close();
                var out = `<option value="">Select District</option>`;
                $.each(data.message, function(index, value){
                    out += `<option value="`+value.districtid+`">`+value.districtname+`</option>`;
                });

                $('[name="bc_district"]').html(out);
            }
        });
    }
</script>
    @endpush
@endsection