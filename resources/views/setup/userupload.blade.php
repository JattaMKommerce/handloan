@extends('layouts.app')
@section('title'," Mapping ids")
@section('bodyClass', "has-detached-left")
@section('pagetitle'," Mapping ids")

@section('content')
<style>
   .wrap_side .sidebar-default .navigation li > a {
    color:black;
}
</style>
<div class="content">

                @if (\Myhelper::hasRole(['admin','other']))
                        <form id="mappingForm" action="{{route('setupupdate')}}" method="post">
                            {{ csrf_field() }}
                            <input type="hidden" name="actiontype" value="exceluploade">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title">Change Mapping</h3>
                                </div>
                                <div class="panel-body p-b-0">
                                    <div class="row">
                                          <div class="form-group col-md-6">
                                            <label>Excel Sheet)</label>
                                            <input type="file" name="excel" class="form-control" required>
                                       </div>
                                    </div>
                                </div>
                                <div class="panel-footer">
                                    <button class="btn btn-primary pull-right" type="submit" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Changing...">Change</button>
                                </div>
                            </div>
                        </form>
                    
                @endif
</div>
@endsection

@push('script')
 

<script type="text/javascript">
    $(document).ready(function () {
        
        $('#retailes').select2({
        ajax: {
            url: "{{'/getUserList'}}",
            dataType:'json',
            type:'post',
            minimumInputLength:2,
            data: function (params) {
                var query = {
                    search: params.term,
                    page: params.page || 1,
                    _token:"{{csrf_token()}}"
                }
                return query;
            },
            processResults: function (data, params) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            text: item.text,
                            id: item.id
                            //data: item
                        };
                    })
                };
            }
        }
    });    
           
        
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
      
        $( "#mappingForm" ).validate({
            rules: {
                parent_id: {
                    required: true
                },
             //   "user_ids[]":"required"
                
            },
            messages: {
                parent_id: {
                    required: "Please select parent member"
                },
               // "user_ids[]":"Please select member"
            },
            errorElement: "p",
            errorPlacement: function ( error, element ) {
                if ( element.prop("tagName").toLowerCase().toLowerCase() === "select" ) {
                    error.insertAfter( element.closest( ".form-group" ).find(".select2") );
                } else {
                    error.insertAfter( element );
                }
            },
            submitHandler: function () {
                var form = $('form#mappingForm');
                form.find('span.text-danger').remove();
                form.ajaxSubmit({
                    dataType:'json',
                    beforeSubmit:function(){
                        form.find('button:submit').button('loading');
                    },
                    complete: function () {
                        form.find('button:submit').button('reset');
                    },
                    success:function(data){
                        if(data.status == "success"){
                            notify("Mapping Successfully Changed" , 'success');
                        }else{
                            notify(data.status , 'warning');
                        }
                    },
                    error: function(errors) {
                        showError(errors);
                    }
                });
            }
        });
    });

</script>
@endpush
