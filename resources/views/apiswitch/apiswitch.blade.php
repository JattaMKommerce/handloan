@extends('layouts.app')
@section('title', 'Api Switch List')
@section('pagetitle', 'Api Switch List')
@php
$table = "yes";
$agentfilter = "hide";
$product['type'] = "Operator";
foreach ($providers as $provider){
$product['data'][$provider->id] = $provider->name;
}
asort($product['data']);
$status['type'] = "Operator";
$status['data'] = [
"1" => "Active",
"0" => "De-active"
];

//dd($state);
@endphp

<style>
    .dropdown-menu.show {
        width: 300px !important;
        padding:10px;
    }
 .btn-group{
    width: 100% !important;
 }
</style>

@section('content')
<div class="content">
    <div class="row">
        <div class="col-sm-12">
            <div class="card iq-card iq-mb-3">
                <div class="card-header border-bottom-0 bg-white d-flex justify-content-between">
                    <div class="iq-header-title">
                        <h4 class="card-title">{{ucfirst($type)}} Wise Api Switch List</h4>
                    </div>
                    <button type="button" class="btn btn-sm btn-primary" onclick="addSetup()">
                        <i class="icon-plus2"></i> Add New
                    </button>
                </div>
                <div class="panel-body">
                </div>
                <table class="table table-bordered table-striped table-hover" id="datatable" style="font-size:14px;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th width="200px">Name</th>
                            <th>Users</th>
                            <th>Value</th>
                            <th>Api</th>
                            <th>Status</th>
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
                <h5 class="modal-title"><span class="msg">Add</span> Api Switch</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="setupManager" action="{{route('apiswitchupdate')}}" method="post">
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" name="id">
                        <input type="hidden" name="actiontype" value="apiswitch">
                        <input type="hidden" name="type" value="{{$type}}">
                        {{ csrf_field() }}
                        <div class="form-group col-md-6">
                            <label>Provider</label>
                            <select name="provider_id" class="form-control">
                                <option value="">Select Provider</option>
                                @foreach ($providers as $providers)
                                <option value="{{$providers->id}}">{{$providers->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Api</label>
                            <select name="api_id" class="form-control">
                                <option value="">Select Api</option>
                                @foreach ($apis as $api)
                                <option value="{{$api->id}}">{{$api->product}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-12">
                            <label class="w-100">Users</label>
                            <div class="multi-select-full form-control-select form-control p-0 pt-1">
                                <select name="user_id[]" class="multiselect form-control" multiple="multiple" data-placeholder="Select Users...">
                                    @foreach ($users as $users)
                                    <option value="{{$users->id}}" class="w-100"><small>{{$users->name}}({{$users->mobile}})</small></option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group col-md-6">
                            @if ($type == "amount")
                            <label>Amount</label>
                            <input type="text" name="value" class="form-control" placeholder="Enter value">
                            @elseif ($type == "state")
                            <label>State</label>
                            <select name="value" class="form-control">
                                <option value="">Select State</option>
                                @foreach ($circle as $circlesss)
                                <option value="{{$circlesss->id}}">{{$circlesss->state}}</option>
                                @endforeach
                            </select>
                            @endif
                        </div>
                    </div>
                    @if ($type == "amount")
                    Note - Single amount value be like 49, multiple amount value be like 49,59,109 & Amount range value be like 49-59, 109-399
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-hidden="true">Close</button>
                    <button class="btn btn-primary" type="submit" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Submitting">Submit</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="dataModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Data</h6>
                <button type="button" class="close" data-dismiss="modal">&times;</button>

            </div>
            <div class="modal-body" style="word-break: break-all;">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-hidden="true">Close</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endsection

@push('script')
<script type="text/javascript" src="{{asset('')}}assets/js/plugins/forms/styling/uniform.min.js"></script>
<script type="text/javascript" src="{{asset('')}}assets/js/plugins/forms/selects/bootstrap_multiselect.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        var url = "{{url('statement/fetch')}}/apiswitch{{$type}}/0";

        $(".styled, .multiselect-container input").uniform({
            radioClass: 'choice'
        });
        $('.multiselect').multiselect({
            includeSelectAllOption: true,
            enableFiltering: true,
            templates: {
                filter: '<li class="multiselect-item multiselect-filter"><i class="icon-search4"></i> <input class="form-control" type="text"></li>'
            },
            onSelectAll: function() {
                $.uniform.update();
            }
        });

        var onDraw = function() {};

        var options = [{
                "data": "id"
            },
            {
                "data": "providername"
            },
            {
                "data": "name",
                render: function(data, type, full, meta) {
                    return `<a href="javascript:void(0)" onclick="showData('` + full.user_id + `')" data-original-title="` + full.providername + `">` + full.user_id.substring(0, 10) + `...</a>`;
                }
            },
            {
                "data": "name",
                render: function(data, type, full, meta) {

                    if (full.type != 'user') {
                        return `<a href="javascript:void(0)" onclick="showData('` + full.stateval + `')">` + full.stateval.substring(0, 10) + `...</a>`;
                    } else {
                        return `<a href="javascript:void(0)" onclick="showData('` + full.user_id + `')">` + full.user_id.substring(0, 10) + `...</a>`;
                    }
                }
            },
            {
                "data": 'apiname'
            },
            {
                "data": "name",
                render: function(data, type, full, meta) {
                    var option = ['active', 'inactive', 'block'];
                    var out = "";
                    out += `<select class="form-control select" required="" onchange="apiUpdate(this, ` + full.id + `)">`;

                    $.each(option, function(index, val) {

                        if (full.action == val) {
                            out += `<option value="` + val + `" selected="">` + val + `</option>`;
                        } else {
                            out += `<option value="` + val + `">` + val + `</option>`;
                        }

                    });
                    out += `</select>`;
                    return out;
                }
            },
            {
                "data": "action",
                render: function(data, type, full, meta) {
                    return `<button type="button" class="btn btn-primary btn-xs ml-15" onclick='editSetup(` + full.id + `, \`` + full.api_id + `\`, \`` + full.value + `\`, \`` + full.user_id + `\`, \`` + full.provider_id + `\`, \`` + full.type + `\`)'> Change</button> <button type="button" class="btn btn-danger btn-xs ml-15" onclick='deleteSlide(` + full.id + `)'> Delete</button>`;
                }
            }
        ];
        datatableSetup(url, options, onDraw);

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
        $('[name="user_id[]"]').multiselect('rebuild');
        //$.uniform.update();
        $('#setupModal').modal('show');
    }

    function editSetup(id, api, value, user_id, provider_id, type) {
        $('#setupModal').find('.msg').text("Edit");
        $('#setupModal').find('input[name="id"]').val(id);
        var users = user_id.split(",");
        $.each(users, function(index, val) {
            $('[name="user_id[]"]').multiselect('select', val);
        });
        $('#setupModal').find('[name="provider_id"]').val(provider_id).trigger('change');
        $('#setupModal').find('[name="api_id"]').val(api).trigger('change');
        if (type == "state") {
            $('#setupModal').find('[name="value"]').val(value).trigger('change');
        } else {
            $('#setupModal').find('input[name="value"]').val(value);
        }

        $('#setupModal').modal('show');
    }

    function setSwitchData() {
        var type = $("[name='type']").val();

        if (type == "amount") {
            $('.switchData').html(`
                <label>Amount</label>
                <input type="text" name="value" class="form-control" placeholder="Enter value">
            `);
        } else if (type == "state") {
            $('.switchData').html(`
                <label>State</label>
                <select name="value" class="form-control select">
                    <option value="">Select State</option>
                    @foreach ($circle as $circles)
                        <option value="{{$circles->id}}">{{$circles->state}}</option>
                    @endforeach
                </select>
            `);
        } else {
            $('.switchData').html(``);
        }
    }

    function showData(data) {
        $('#dataModal').find(".modal-body").text(data);
        $('#dataModal').modal();
    }

    function apiUpdate(ele, id) {
        var status = $(ele).val();
        if (status != "") {
            $.ajax({
                    url: '{{ route("apiswitchupdate")}}',
                    type: 'post',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    data: {
                        'id': id,
                        'action': status,
                        "actiontype": "apiswitch"
                    }
                })
                .done(function(data) {
                    if (data.status == "success") {
                        notify("Record Updated", 'success');
                    } else {
                        notify("Something went wrong, Try again.", 'warning');
                    }
                    $('#datatable').dataTable().api().ajax.reload();
                })
                .fail(function(errors) {
                    showError(errors, "withoutform");
                });
        }
    }

    function deleteSlide(id) {
        $.ajax({
                url: '{{route("statementDelete")}}',
                type: 'post',
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    "id": id,
                    'type': 'apiswitch'
                },
                beforeSend: function() {
                    swal({
                        title: 'Wait!',
                        text: 'Please wait, we are deleting slides',
                        onOpen: () => {
                            swal.showLoading()
                        },
                        allowOutsideClick: () => !swal.isLoading()
                    });
                }
            })
            .success(function(data) {
                swal.close();
                $('#datatable').dataTable().api().ajax.reload();
                notify("Successfully Deleted", 'success');
            })
            .fail(function() {
                swal.close();
                notify('Somthing went wrong', 'warning');
            });
    }
</script>
@endpush