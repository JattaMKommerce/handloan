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

                @if (\Myhelper::hasRole(['admin','subadmin']))
                        <form id="mappingForm" action="{{route('setupupdate')}}" method="post">
                            {{ csrf_field() }}
                            <input type="hidden" name="actiontype" value="mappingids">
                            
                                    <div class="row">
                                        
                                        <div class="form-group col-md-6">
                                            <label>Parent Member</label>
                                            <select name="parent_id" class="form-control" required="">
                                                <option value="">Select Parent Member</option>
                                                @foreach($parents as $parent)
                                                <option value="{{$parent->id}}">{{$parent->name}} ({{$parent->mobile}}) ({{$parent->role->name}})</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group col-md-6">
                                            <!--<div class="multi-select-full">-->
                                            <label>Select Customer</label>
                                            <!--<select name="user_ids[]" class="form-control multiselect" multiple="multiple" data-placeholder="Select users..." required="">-->
                                                <select name="user_id" class="form-control">
                                                @foreach($alluser as $user)
                                                <option value="{{$user->id}}">{{$user->name}} ({{$user->mobile}})</option>
                                                @endforeach
                                            </select>
                                            <!--</div>-->
                                        </div>
                                        
                                    </div>
                               
                                <div class="panel-footer">
                                    <button class="btn btn-primary pull-right" type="submit" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Changing...">Change</button>
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
        // $(".styled, .multiselect-container input").uniform({ radioClass: 'choice'});
        // $('.multiselect').multiselect({
        //     includeSelectAllOption: true,
        //     enableFiltering: true,
        //     templates: {
        //         filter: '<li class="multiselect-item multiselect-filter"><i class="icon-search4"></i> <input class="form-control" type="text"></li>'
        //     },
        //     onSelectAll: function() {
        //         $.uniform.update();
        //     }
        // });
        
        // $(".js-example-placeholder-multiple").select2({
        //     placeholder: "Select a Customer",
        //     ajax: { 
        //         url:"{{route('searchmappingdata')}}",
        //         headers: {'X-CSRF-TOKEN': CSRF_TOKEN},
        //         type: "post",
        //         dataType: 'json',
        //         delay: 250,
        //         data: function (params) {
        //         return {
        //             searchTerm: params.term // search term
        //             };
        //         },
        //         processResults: function (response) {
        //         return {
        //             results: response
        //         };
        //         },
        //         cache: true
        //         }  
        // });
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
