@extends('layouts.app')
@section('title', 'Callback Api Integration')
@section('pagetitle', 'Callback Api Integration')

@section('content')
<div class="content row">
    <div class="col-sm-12">
        <div class="card iq-card iq-mb-3">
            <h4 class="card-header bg-white">{{ucfirst($api->name)}} Callback Api Integration</h4>

            <div class="card-body">
                <form class="actionForm" action="{{route('apiswitchupdate')}}" method="post">
                    <input type="hidden" name="actiontype" value="integrationcallback">
                    <input type="hidden" name="api_id" value="{{$id}}">
                    <div class="panel-body p-b-0">

                        <h5>Callback Url - {{url('api/callback')}}/{{$callback->baseurl ?? ''}}</h5>
                        {{ csrf_field() }}
                        <legend><small>Response Parameter Key</small></legend>
                        <div class="row">
                            <div class="form-group col-sm-3">
                                <label>Txn Id Parameter Key</label>
                                <input type="text" name="txnid" class="form-control" placeholder="Enter value" value="{{$callback->txnid ?? ''}}">
                            </div>

                            <div class="form-group col-sm-3">
                                <label>Api Reference Id Parameter Key</label>
                                <input type="text" name="payid" class="form-control" placeholder="Enter value" value="{{$callback->txnid ?? ''}}">
                            </div>

                            <div class="form-group col-sm-3">
                                <label>Api Reference Parameter Key</label>
                                <input type="text" name="refno" class="form-control" placeholder="Enter value" value="{{$callback->refno ?? ''}}">
                            </div>
                            <div class="form-group col-sm-3">
                                <label>Api Mesaage Parameter Key</label>
                                <input type="text" name="message" class="form-control" placeholder="Enter value" value="{{$callback->message ?? ''}}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-sm-3">
                                <label>Response Status Parameter Key</label>
                                <input type="text" name="status" class="form-control" placeholder="Enter value" value="{{$callback->status ?? ''}}">
                            </div>

                            <div class="form-group col-sm-3">
                                <label>For Success Parameter Value</label>
                                <input type="text" name="success" class="form-control" placeholder="Enter value" value="{{$callback->success ?? ''}}">
                            </div>

                            <div class="form-group col-sm-3">
                                <label>For Pending Parameter Value</label>
                                <input type="text" name="pending" class="form-control" placeholder="Enter value" value="{{$callback->pending ?? ''}}">
                            </div>

                            <div class="form-group col-sm-3">
                                <label>For Failed Parameter Value</label>
                                <input type="text" name="failed" class="form-control" placeholder="Enter value" value="{{$callback->failed ?? ''}}">
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
                    form[0].reset();
                    if (data.status == "success") {
                        if (id == "new") {
                            form[0].reset();
                            $('[name="api_id"]').select2().val(null).trigger('change');
                        }
                        form.find('button[type="submit"]').button('reset');
                        notify("Task Successfully Completed", 'success');
                        // $('#datatable').dataTable().api().ajax.reload();
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