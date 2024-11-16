@extends('layouts.app')
@section('title', 'Electricity Integration')
@section('pagetitle', 'Electricity Integration')

@section('content')
<div class="content row">
    <div class="col-sm-12">
        <div class="card iq-card iq-mb-3">
            <h5 class="card-title">Electricity Integration</h5>

            <div class="card-body ">
                <form class="actionForm" action="{{route('apiswitchupdate')}}" method="post">
                    <input type="hidden" name="actiontype" value="Integration">
                    <input type="hidden" name="type" value="bill">
                    <input type="hidden" name="id" value="{{$id}}">
                    <div class="p-b-0 pb-2">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label>Name</label>
                                <input type="text" name="name" class="form-control" onblur="setCode()" placeholder="Enter value" value="{{$api->name ?? ''}}">
                            </div>

                            <div class="form-group col-sm-4">
                                <label>Username/Api Key/ Token Value</label>
                                <input type="text" name="usernameval" class="form-control" onblur="setCode()" placeholder="Enter value" value="{{$api->usernameval ?? ''}}">
                            </div>

                            <div class="form-group col-sm-4">
                                <label>Password Value (If Any)</label>
                                <input type="text" name="passwordval" class="form-control" onblur="setCode()" placeholder="Enter value" value="{{$api->passwordval ?? ''}}">
                            </div>

                        </div>

                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label>Recharge Url</label>
                                <input type="text" name="baseurl" class="form-control" onblur="setCode()" placeholder="Enter value" value="{{$api->baseurl ?? ''}}">
                            </div>

                            <div class="form-group col-md-4">
                                <label>Form Method</label>
                                <select name="method" class="form-control" onchange="setCode()">
                                    <option value="">Select Method</option>
                                    <option value="GET">GET</option>
                                    <option value="POST">POST</option>
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <label>Request Type</label>
                                <select name="requesttype" class="form-control" onchange="setCode()">
                                    <option value="">Select Type</option>
                                    <option value="json">Json</option>
                                    <option value="query">Query String</option>
                                </select>
                            </div>
                        </div>

                        <legend><small>Mendatory Body Parameter Key</small></legend>
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label>Username/Api Key/ Token Parameter Key</label>
                                <input type="text" name="username" class="form-control" onblur="setCode()" placeholder="Enter value" value="{{$api->username ?? ''}}">
                            </div>

                            <div class="form-group col-sm-4">
                                <label>Password (If Required) Parameter Key</label>
                                <input type="text" name="password" class="form-control" onblur="setCode()" placeholder="Enter value" value="{{$api->password ?? ''}}">
                            </div>

                            <div class="form-group col-sm-4">
                                <label>Consumer Number Parameter Key</label>
                                <input type="text" name="mobile" class="form-control" onblur="setCode()" placeholder="Enter value" value="{{$api->mobile ?? ''}}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label>Operator Code Parameter Key</label>
                                <input type="text" name="operator" class="form-control" onblur="setCode()" placeholder="Enter value" value="{{$api->operator ?? ''}}">
                            </div>

                            <div class="form-group col-sm-4">
                                <label>Amount Parameter Key</label>
                                <input type="text" name="amount" class="form-control" onblur="setCode()" placeholder="Enter value" value="{{$api->amount ?? ''}}">
                            </div>

                            <div class="form-group col-sm-4">
                                <label>Unique Transaction Id Parameter Key</label>
                                <input type="text" name="txnid" class="form-control" onblur="setCode()" placeholder="Enter value" value="{{$api->txnid ?? ''}}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label>Extra Param Parameter Key</label>
                                <input type="text" name="extraparam" class="form-control" onblur="setCode()" placeholder="Enter value" value="{{$api->extraparam ?? ''}}">
                            </div>

                            <div class="form-group col-sm-4">
                                <label>Circle Parameter Key</label>
                                <input type="text" name="state" class="form-control" onblur="setCode()" placeholder="Enter value" value="{{$api->state ?? ''}}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label>Other Parameter</label>
                                <input type="text" name="other" class="form-control" onblur="setCode()" value="{{$api->other ?? ''}}" placeholder="Ex - format=json&field1=field1&field2=field2">
                                <i class="fa fa-info text-info me-2"></i>Eg- format=json&field1=field1&field2=field2
                            </div>
                        </div>

                        <legend><small>Api Preview</small></legend>
                        <div class="row border-rounded">
                            <div class="col-sm-12">
                                <b>Url -</b> <span class="url" style="word-break: break-all;"></span>
                            </div>

                            <div class="col-sm-12">
                                <b>Header -</b> <span class="header"></span>
                            </div>

                            <div class="col-sm-12">
                               <b>Body -</b> <span class="body" style="word-break: break-all;"></span>
                            </div>
                        </div>

                        <legend><small>Response Parameter Key</small></legend>

                        <div class="row">
                            <div class="form-group col-md-3">
                                <label>Response Type</label>
                                <select name="responsetype" class="form-control" onchange="setCode()">
                                    <option value="">Select Type</option>
                                    <option value="json">Json</option>
                                    <option value="xml">Xml</option>
                                    <option value="csv">Csv</option>
                                </select>
                            </div>

                            <div class="form-group col-sm-3">
                                <label>Operator Reference Parameter Key</label>
                                <input type="text" name="refno" class="form-control" onblur="setCode()" placeholder="Enter value" value="{{$api->refno ?? ''}}">
                            </div>

                            <div class="form-group col-sm-3">
                                <label>Operator Provider Id Parameter Key</label>
                                <input type="text" name="payid" class="form-control" onblur="setCode()" placeholder="Enter value" value="{{$api->payid ?? ''}}">
                            </div>


                            <div class="form-group col-sm-3">
                                <label>Operator Mesaage Parameter Key</label>
                                <input type="text" name="message" class="form-control" onblur="setCode()" placeholder="Enter value" value="{{$api->message ?? ''}}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-sm-3">
                                <label>Response Status Parameter Key</label>
                                <input type="text" name="status" class="form-control" onblur="setCode()" placeholder="Enter value" value="{{$api->status ?? ''}}">
                            </div>

                            <div class="form-group col-sm-3">
                                <label>For Success Parameter Value</label>
                                <input type="text" name="success" class="form-control" onblur="setCode()" placeholder="Enter value" value="{{$api->success ?? ''}}">
                            </div>

                            <div class="form-group col-sm-3">
                                <label>For Pending Parameter Value</label>
                                <input type="text" name="pending" class="form-control" onblur="setCode()" placeholder="Enter value" value="{{$api->pending ?? ''}}">
                            </div>

                            <div class="form-group col-sm-3">
                                <label>For Failed Parameter Value</label>
                                <input type="text" name="failed" class="form-control" onblur="setCode()" placeholder="Enter value" value="{{$api->failed ?? ''}}">
                            </div>
                            
                            <div class="form-group col-sm-3">
                                <label>Low Balance Message</label>
                                <input type="text" name="balance" class="form-control" onblur="setCode()" placeholder="Enter value" value="{{$api->balance ?? ''}}">
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
            $("[name='method']").val("{{$api->method ?? ''}}").trigger('change');
            $("[name='requesttype']").val("{{$api->requesttype ?? ''}}").trigger('change');
            $("[name='responsetype']").val("{{$api->responsetype ?? ''}}").trigger('change');
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
    });

    function setCode() {
        var url = $("[name='baseurl']").val();
        var usernameval = $("[name='usernameval']").val();
        var passwordval = $("[name='passwordval']").val();

        var method = $("[name='method']").val();
        var username = $("[name='username']").val();
        var password = $("[name='password']").val();
        var mobile = $("[name='mobile']").val();
        var operator = $("[name='operator']").val();
        var txnid = $("[name='txnid']").val();
        var state = $("[name='state']").val();
        var other = $("[name='other']").val();

        var requesttype = $("[name='requesttype']").val();

        personObj = new Object();
        personObj[username] = usernameval;
        personObj[password] = passwordval;
        personObj[mobile] = mobile;
        personObj[operator] = operator;
        personObj[txnid] = txnid;
        if (other.state > 0) {
            personObj[state] = state;
        }

        if (other.length > 0) {
            var others = other.split('&');

            $.each(others, function(index, val) {
                var param = val.split('=');
                personObj[param[0]] = param[1];
            });
        }

        if (requesttype == "json") {
            var header = ["content-type: application/json"];
            var query = JSON.stringify(personObj);
        } else {
            var header = [];
            var query = $.param(personObj);
            url = url + "?" + query;
            if (method == "GET") {
                var query = '';
            }
        }

        $('.url').text(url);
        $('.header').text(header);
        $('.body').text(query);
    }
</script>
@endpush