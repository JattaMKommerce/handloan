@extends('layouts.app')
@section('title', 'Balance Api Integration')
@section('pagetitle', 'Balance Api Integration')

@section('content')
<div class="content row">
    <div class="col-sm-12">
        <div class="card iq-card iq-mb-3">

            <h4 class="card-header bg-white">{{ucfirst($api->name)}} Balance Api Integration</h4>

            <div class="card-body">
                <form class="actionForm" action="{{route('apiswitchupdate')}}" method="post">
                    <input type="hidden" name="actiontype" value="integrationbalance">
                    <input type="hidden" name="api_id" value="{{$id}}">
                    <div class="card-body p-b-0">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label>Username/Api Key/ Token Value</label>
                                <input type="text" name="usernameval" class="form-control" placeholder="Enter value" value="{{$status->usernameval ?? ''}}">
                            </div>

                            <div class="form-group col-sm-4">
                                <label>Password Value (If Any)</label>
                                <input type="text" name="passwordval" class="form-control" placeholder="Enter value" value="{{$status->passwordval ?? ''}}">
                            </div>

                            <div class="form-group col-sm-4">
                                <label>Status Url</label>
                                <input type="text" name="baseurl" class="form-control" placeholder="Enter value" value="{{$status->baseurl ?? ''}}">
                            </div>

                            <div class="form-group col-md-4">
                                <label>Form Method</label>
                                <select name="method" class="form-control">
                                    <option value="">Select Method</option>
                                    <option value="GET">GET</option>
                                    <option value="POST">POST</option>
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <label>Request Type</label>
                                <select name="requesttype" class="form-control">
                                    <option value="">Select Type</option>
                                    <option value="json">Json</option>
                                    <option value="query">Query String</option>
                                </select>
                            </div>
                        </div>

                        <legend><small>Mandatory body Parameter Key</small></legend>
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label>Username/Api Key/ Token Parameter Key</label>
                                <input type="text" name="username" class="form-control" placeholder="Enter value" value="{{$status->username ?? ''}}">
                            </div>

                            <div class="form-group col-sm-4">
                                <label>Password (If Required) Parameter Key</label>
                                <input type="text" name="password" class="form-control" placeholder="Enter value" value="{{$status->password ?? ''}}">
                            </div>

                            <div class="form-group col-sm-4">
                                <label>Other Parameter</label>
                                <input type="text" name="other" class="form-control" value="{{$status->other ?? ''}}" placeholder="Ex - format=json&field1=field1&field2=field2">
                                <i class="fa fa-info text-info me-1"></i> Eg- format=json&field1=field1&field2=field2
                            </div>
                        </div>

                        <legend><small>Response Parameter Key</small></legend>
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label>Response Type</label>
                                <select name="responsetype" class="form-control">
                                    <option value="">Select Type</option>
                                    <option value="json">Json</option>
                                    <option value="xml">Xml</option>
                                    <option value="csv">Csv</option>
                                </select>
                            </div>

                            <div class="form-group col-sm-4">
                                <label>Balance Reference Parameter Key</label>
                                <input type="text" name="balance" class="form-control" placeholder="Enter value" value="{{$status->refno ?? ''}}">
                            </div>
                            <div class="form-group col-sm-4">
                                <label>Response Status Parameter Key</label>
                                <input type="text" name="status" class="form-control" placeholder="Enter value" value="{{$status->status ?? ''}}">
                            </div>

                            <div class="form-group col-sm-4">
                                <label>For Success Parameter Value</label>
                                <input type="text" name="success" class="form-control" placeholder="Enter value" value="{{$status->success ?? ''}}">
                            </div>

                            <div class="form-group col-sm-4">
                                <label>For Failed Parameter Value</label>
                                <input type="text" name="failed" class="form-control" placeholder="Enter value" value="{{$status->failed ?? ''}}">
                            </div>

                            <div class="form-group col-sm-12">
                                Note : If Failed Parameter value is greaterthan 1 than enter value with comma seperation like value1,value2,value3
                            </div>
                        </div>
                        <button class="btn btn-primary pull-right" type="submit" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Updating...">Add Api</button>
                    
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
        $(window).load(function() {
            $("[name='method']").val("{{$status->method ?? ''}}").trigger('change');
            $("[name='requesttype']").val("{{$status->requesttype ?? ''}}").trigger('change');
            $("[name='responsetype']").val("{{$status->responsetype ?? ''}}").trigger('change');
        });

        $('.actionForm').submit(function(event) {
            var form = $(this);
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
                        if (id == "new") {
                            form[0].reset();
                            $('[name="api_id"]').select2().val(null).trigger('change');
                        }
                        form.find('button[type="submit"]').button('reset');
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
            return false;
        });

        $("#setupModal").on('hidden.bs.modal', function() {
            $('#setupModal').find('.msg').text("Add");
            $('#setupModal').find('form')[0].reset();
        });

        $('')
    });
</script>
@endpush