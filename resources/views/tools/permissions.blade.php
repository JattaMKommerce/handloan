@extends('layouts.app')
@section('title', 'Permissions')
@section('pagetitle', 'Permissions List')
@php
$table = "yes";
@endphp

@section('content')
<div class="content">
    <div class="row">
        <div class="col-sm-12">
            <div class="iq-card">
                <div class="iq-card-header d-flex justify-content-between">
                    <div class="iq-header-title">
                        <h4 class="card-title">Permissions List</h4>

                    </div>
                    <button type="button" class="btn btn-sm btn-primary" onclick="addrole()">
                        <i class="icon-plus2"></i> Add New
                    </button>
                </div>
                <div class="iq-card-body">
                    <div class="table-responsive">
                        <table id="datatable" class="table">
                              <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Display Name</th>
                                    <th>Type</th>
                                    <th>Last Update</th>
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
    </div>
</div>

<div class="modal fade" id="permissionModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><span class="msg">Add</span> Permission</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            <form id="permissionManager" action="{{route('toolsstore', ['type'=>'permission'])}}" method="post">
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" name="id">
                        {{ csrf_field() }}
                        <div class="form-group col-md-6">
                            <label>Name</label>
                            <input type="text" name="slug" class="form-control" placeholder="Enter Permission Name" required="">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Display Name</label>
                            <input type="text" name="name" class="form-control" placeholder="Enter Display Name" required="">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label>Type</label>
                            <input type="text" name="type" class="form-control" placeholder="Enter Permission Type" required="">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                   <button class="btn btn-primary" type="submit" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Submitting">Submit</button>
                </div>
            </form>
            </div>
           
        </div>
    </div>
</div>

@endsection

@push('script')
<script type="text/javascript">
    $(document).ready(function() {
        var url = "{{url('statement/fetch/permissions/0')}}";
        var onDraw = function() {};
        var options = [{
                "data": "id"
            },
            {
                "data": "slug"
            },
            {
                "data": "name"
            },
            {
                "data": "type"
            },
            {
                "data": "updated_at"
            },
            {
                "data": "action",
                render: function(data, type, full, meta) {
                    return `<button type="button" class="btn btn-primary" onclick="editRole(this)"> Edit</button>`;
                }
            },
        ];
        datatableSetup(url, options, onDraw);

        $("#permissionManager").validate({
            rules: {
                slug: {
                    required: true,
                },
                name: {
                    required: true,
                },
            },
            messages: {
                mobile: {
                    required: "Please enter role slug",
                },
                name: {
                    required: "Please enter role name",
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
                var form = $('#permissionManager');
                var id = $('#permissionManager').find("[name='id']").val();
                form.ajaxSubmit({
                    dataType: 'json',
                    beforeSubmit: function() {
                        form.find('button[type="submit"]').html('loading...').attr('disabled',true).addClass('btn-secondary');
                    },
                    success: function(data) {
                        if (data.status == "success") {
                            if (id == "new") {
                                form[0].reset();
                            }
                            form.find('button[type="submit"]').html('Submit').attr('disabled',false).removeClass('btn-secondary');
                            notify("Task Successfully Completed", 'success');
                            $('#datatable').dataTable().api().ajax.reload();
                            $('#permissionModal').modal('hide');
                        } else {
                            notify(data.status, 'warning');
                        }
                    },
                    error: function(errors) {
                        showError(errors, form);
                    }
                });
            }
        });

        $("#permissionModal").on('hidden.bs.modal', function() {
            $('#permissionModal').find('.msg').text("Add");
            $('#permissionModal').find('form')[0].reset();
        });
    });

    function addrole() {
        $('#permissionModal').find('.msg').text("Add");
        $('#permissionModal').find('input[name="id"]').val("new");
        $('#permissionModal').modal('show');
    }

    function editRole(ele) {
        var id = $(ele).closest('tr').find('td').eq(0).text();
        var slug = $(ele).closest('tr').find('td').eq(1).text();
        var name = $(ele).closest('tr').find('td').eq(2).text();
        var type = $(ele).closest('tr').find('td').eq(3).text();

        $('#permissionModal').find('.msg').text("Edit");
        $('#permissionModal').find('input[name="id"]').val(id);
        $('#permissionModal').find('input[name="slug"]').val(slug);
        $('#permissionModal').find('input[name="name"]').val(name);
        $('#permissionModal').find('input[name="type"]').val(type);
        $('#permissionModal').modal('show');
    }
</script>
@endpush