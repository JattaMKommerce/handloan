@extends('layouts.app')
@section('title', 'Api List')
@section('pagetitle',  'Api List')
@php
    $table = "yes";
    $agentfilter = "hide";
@endphp

@section('content')
<div class="content">
    <div class="row">
        <div class="col-sm-12">
            <div class="card iq-card iq-mb-3">
                    <h4 class="card-title">Api List</h4>
                    <div class="heading-elements">
                        <a href="{{route('apiswitch', ['type' => 'eintegration', "id" => "new"])}}">
                            <button type="button" class="btn btn-sm bg-slate btn-raised heading-btn legitRipple">
                                <i class="icon-plus2"></i> Add New
                            </button>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                </div>
                <table class="table table-bordered table-striped table-hover" id="datatable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th width="250px">Name</th>
                            <th>Username</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script type="text/javascript">
    $(document).ready(function () {
        var url = "{{url('statement/fetch')}}/eintegrationlist/0";
        var options = [
            { "data" : "id"},
            { "data" : "name"},
            { "data" : "usernameval"},
            { "data" : "action",
                render:function(data, type, full, meta){
                    var menu = ``;
                    var out = '';
                    menu += `<li class="dropdown-header">Action</li>
                            <li><a href='{{route("apiswitch", ["type" => "eintegration"])}}/`+full.id+`' target="_blank" class="dropdown-item"><i class="icon-pencil5"></i>Change Api</a></li>`;
                    // menu += `<li><a href='{{route("apiswitch", ["type" => "integrationbalance"])}}/`+full.id+`' target="_blank" class="dropdown-item"><i class="icon-pencil5"></i>Balance</a></li>`;
                    menu += `<li><a href='{{route("apiswitch", ["type" => "integrationstatus"])}}/`+full.id+`' target="_blank" class="dropdown-item"><i class="icon-pencil5"></i>Check Status</a></li>`;
                    menu += `<li><a href='{{route("apiswitch", ["type" => "integrationcallback"])}}/`+full.id+`' target="_blank" class="dropdown-item"><i class="icon-pencil5"></i>Api Callback</a></li>`;
                    // menu += `<li><a href='{{route("apiswitch", ["type" => "integrationcomplain"])}}/`+full.id+`' target="_blank" class="dropdown-item"><i class="icon-pencil5"></i>Complain Api</a></li>`;
                    // menu += `<li><a href='{{route("apiswitch", ["type" => "integrationcomplaincallback"])}}/`+full.id+`' target="_blank" class="dropdown-item"><i class="icon-pencil5"></i>Complain Callback</a></li>`;

                    out +=  `<div class="btn-group" role="group">
                                    <button id="btnGroupDrop1" type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Commission/Charge 
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                       ` + menu + `
                                       
                                    </div>
                                 </div>`;

                    return out;
                }
            }
        ];
        datatableSetup(url, options);

        $( "#setupManager" ).validate({
            rules: {
                user_id: {
                    required: true,
                },
            },
            messages: {
                user_id: {
                    required: "Please select api",
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
                var form = $('#setupManager');
                var id = form.find('[name="id"]').val();
                form.ajaxSubmit({
                    dataType:'json',
                    beforeSubmit:function(){
                        form.find('button[type="submit"]').button('loading');
                        swal({
                        title: 'Wait!',
                        text: 'We are working on your request',
                        onOpen: () => {
                            swal.showLoading()
                        },
                        allowOutsideClick: () => !swal.isLoading()
                    })
                    },
                    success:function(data){
                      swal.close();
                        if(data.status == "success"){
                            form[0].reset();
                            form.find('button[type="submit"]').button('reset');
                            $('#setupModal').modal('hide');
                            notify("Task Successfully Completed", 'success');
                            $('#datatable').dataTable().api().ajax.reload();
                        }else{
                            notify(data.status, 'warning');
                        }
                    },
                    error: function(errors) {
                        showError(errors, form);
                        swal.close();
                    }
                });
            }
        });

        $("#setupModal").on('hidden.bs.modal', function () {
            $('#setupModal').find('.msg').text("Add");
            $('#setupModal').find('form')[0].reset();
        });
    });

    function addSetup(){
        $('#setupModal').find('.msg').text("Add");
        $('#setupModal').find('input[name="id"]').val("new");
        $('#setupModal').modal('show');
    }

    function editSetup(id, api, code, circle_id){
        $('#setupModal').find('.msg').text("Edit");
        $('#setupModal').find('input[name="id"]').val(id);
        $('#setupModal').find('[name="circle_id"]').select2().val(circle_id).trigger('change');
        var apis  = api.split(",");
        var codes = code.split(",");

        if(apis.length>0){
            for (var i = 0; i < apis.length; i++) {
                $('#setupModal').find("input[value='"+apis[i]+"']").closest('.row').find("[name='code[]']").val(codes[i]);
            }
        }

        $('#setupModal').modal('show');
    }    
</script>
@endpush