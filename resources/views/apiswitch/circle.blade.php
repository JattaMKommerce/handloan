@extends('layouts.app')
@section('title', 'Api Circle List')
@section('pagetitle', 'Api Circle List')
@php
$table = "yes";
$agentfilter = "hide";
$product['type'] = "State";
foreach ($circles as $circle){
$product['data'][$circle->id] = $circle->state;
}
asort($product['data']);
@endphp

@section('content')
<div class="content">
    <div class="row">
        <div class="col-sm-12">
            <div class="card iq-card iq-mb-3">
                <div class="card-header border-bottom-0 bg-white d-flex justify-content-between">
                    <div class="iq-header-title">
                        <h4 class="card-title">Api Circle List</h4>
                    </div>
                    <button type="button" class="btn btn-sm btn-primary" onclick="addSetup()">
                        <i class="icon-plus2"></i> Add New
                    </button>
                </div>
                <table class="table table-bordered table-striped table-hover" id="datatable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th width="250px">Name</th>
                            <th>Circle Codes</th>
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

<div id="setupModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><span class="msg">Add</span> Api Switch</h6>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="setupManager" action="{{route('apiswitchupdate')}}" method="post">
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" name="id">
                        <input type="hidden" name="actiontype" value="circle">
                        {{ csrf_field() }}
                        <div class="form-group col-md-12">
                            <label class="w-100">Circles</label>
                            <select name="circle_id" class="form-select form-control">
                                <option value="">Select Circle</option>
                                @foreach ($circles as $circle)
                                <option value="{{$circle->id}}">{{$circle->state}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    @foreach($apis as $api)
                    <div class="row">
                        <div class="form-group col-md-6">
                            <input type="hidden" name="api[]" value="{{$api->id}}">
                            <label>Api</label>
                            <input type="text" class="form-control" value="{{$api->name}}" readonly="">
                        </div>

                        <div class="form-group col-md-6">
                            <label>Circle Code</label>
                            <input type="text" class="form-control" name="code[]" placeholder="Enter Value" required="">
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-hidden="true">Close</button>
                    <button class="btn btn-primary" type="submit" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Submitting">Submit</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endsection

@push('script')
<script type="text/javascript">
    $(document).ready(function() {
        var url = "{{url('statement/fetch')}}/apiswitchcircle/0";
        var options = [{
                "data": "id"
            },
            {
                "data": "circlename"
            },
            {
                "data": "name",
                render: function(data, type, full, meta) {
                    var out = "";
                    if (full.apis.length > 0) {
                        for (var i = 0; i < full.apis.length; i++) {
                            out += `<label>` + full.apis[i].name + `</label> - ` + full.codes[i] + "<br>";
                        }
                    }

                    return out;
                }
            },
            {
                "data": "action",
                render: function(data, type, full, meta) {
                    return `<button type="button" class="btn btn-primary btn-xs ml-15" onclick='editSetup(` + full.id + `, \`` + full.api + `\`, \`` + full.code + `\`, \`` + full.circle_id + `\`)'> Change</button>`;
                }
            }
        ];
        datatableSetup(url, options);

        $("#setupManager").validate({
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
            errorPlacement: function(error, element) {
                if (element.prop("tagName").toLowerCase() === "select") {
                    error.insertAfter(element.closest(".form-group").find(".select2"));
                } else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function() {
                var form = $('#setupManager');
                var id = form.find('[name="id"]').val();
                form.ajaxSubmit({
                    dataType: 'json',
                    beforeSubmit: function() {
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
                    success: function(data) {
                        swal.close();
                        if (data.status == "success") {
                            form[0].reset();
                            form.find('button[type="submit"]').button('reset');
                            $('#setupModal').modal('hide');
                            notify("Task Successfully Completed", 'success');
                            $('#datatable').dataTable().api().ajax.reload();
                        } else {
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

        $("#setupModal").on('hidden.bs.modal', function() {
            $('#setupModal').find('.msg').text("Add");
            $('#setupModal').find('form')[0].reset();
        });
    });

    function addSetup() {
        $('#setupModal').find('.msg').text("Add");
        $('#setupModal').find('input[name="id"]').val("new");
        $('#setupModal').modal('show');
    }

    function editSetup(id, api, code, circle_id) {
        $('#setupModal').find('.msg').text("Edit");
        $('#setupModal').find('input[name="id"]').val(id);
        $('#setupModal').find('[name="circle_id"]').val(circle_id).trigger('change');
        var apis = api.split(",");
        var codes = code.split(",");

        if (apis.length > 0) {
            for (var i = 0; i < apis.length; i++) {
                $('#setupModal').find("input[value='" + apis[i] + "']").closest('.row').find("[name='code[]']").val(codes[i]);
            }
        }

        $('#setupModal').modal('show');
    }
</script>
@endpush