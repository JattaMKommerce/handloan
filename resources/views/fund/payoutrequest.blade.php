@extends('layouts.app')
@section('title', "Payout Pending Request")
@section('pagetitle', "Payout Pending Request")

@php
$table = "yes";
$export = "aepsfundrequestview";
$status['type'] = "Fund";
$status['data'] = [
"success" => "Success",
"pending" => "Pending",
"failed" => "Failed",
"approved" => "Approved",
"rejected" => "Rejected",
];

$product['type'] = "Transaction";
$product['data'] = [
"wallet" => "Move To Wallet",
"bank" => "Move To Bank"
];

@endphp

@section('content')

<div class="content">
    <div class="row">
        <div class="col-sm-12">
            <div class="iq-card">
                <div class="iq-card-body">
                    <div class="table-responsive">
                        <table class="table" id="datatable">
                              <thead class="thead-light">
                                <tr>
                                    <th width="160px">#</th>
                                    <th>User Details</th>
                                    <th>Bank Details</th>
                                    <th>Reference Details</th>
                                    <th>Amount</th>
                                    <th>Remark</th>
                                    <th width="100px">Action</th>
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

@if (Myhelper::hasRole('admin'))

<div class="modal fade" id="transferModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Fund Request Form</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="transferForm" method="post" action="{{ route('statementUpdate') }}">
                <div class="modal-body">
                    {!! csrf_field() !!}
                    <input type="hidden" name="id">
                    <input type="hidden" name="actiontype" value="payout">
                    <div class="form-group">
                        <label>Action Type</label>
                        <select class="form-control" name="status" required="">
                            <option value="">Select Action Type</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Reject</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Remark</label>
                        <textarea name="remark" class="form-control" rows="3" placeholder="Enter Value"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                   <button class="btn btn-primary" type="submit" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Submitting">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection

@push('style')

@endpush

@push('script')
<script type="text/javascript">
    $(document).ready(function() {
        var url = "{{url('statement/fetch')}}/payoutstatement/0";
        var onDraw = function() {};
        var options = [{
                "data": "name",
                render: function(data, type, full, meta) {
                    var out = '';
                    if (full.api) {
                        out += `<span class='myspan'>` + full.api.api_name + `</span><br>`;
                    }
                    out += `<span class='text-inverse'>` + full.id + `</span><br><span style='font-size:12px'>` + full.created_at + `</span>`;
                    return out;
                }
            },
            {
                "data": "account",
                render: function(data, type, full, meta) {
                    return full.username ;
                }
            },
            {
                "data": "bank",
                render: function(data, type, full, meta) {
                    if (full.type == "wallet") {
                        return "Wallet"
                    } else {
                        if (full.option2 != '' && full.option2 != null) {
                            return full?.option2 + " ( " + full?.option4 + " )<br>" + full?.option3;
                        } else {
                            return full?.user?.option2 + " ( " + full?.user?.option4 + " )<br>" + full?.user?.option3;
                        }
                    }
                }
            },
            {
                "data": "account",
                render: function(data, type, full, meta) {
                    return "RRN - " + full?.refno + "<br>Payout Id - " + full?.txnid;
                }
            },
            {
                "data": "description",
                render: function(data, type, full, meta) {
                    return `<span class='text-inverse'><i class="fa fa-rupee"></i> ` + full?.amount;
                }
            },
            {
                "data": "remark"
            },
            {
                "data": "action",
                render: function(data, type, full, meta) {
                    if (full.status == "success") {
                        var btn = `<span class="badge badge-success">${full?.status}</span>`;
                    } else if (full.status == 'pending') {
                        var btn = `<span class="badge badge-warning">${full?.status}</span>`;
                    } else {
                        var btn = `<span class="badge badge-danger">${full?.status}</span>`;
                    }
                    @if(Myhelper::hasRole('admin'))
                    console.log(full?.pay_type);
                    if ( (full?.status == 'pending' || full?.status == 'approved')) {
                        btn += `<br><i class="fa fa-pencil" onclick="transfer('` + full.id + `')"></i>`;
                    }
                    @endif
                    return btn;
                }
            }
        ];

        datatableSetup(url, options, onDraw);

        $('form#transferForm').submit(function() {
            var form = $(this);
            $(this).ajaxSubmit({
                dataType: 'json',
                beforeSubmit: function() {
                    form.find('button:submit').button('loading');
                    swal({
                                title: 'Wait!',
                                text: 'We are working on your request',
                                onOpen: () => {
                                    swal.showLoading()
                                },
                                allowOutsideClick: () => !swal.isLoading()
                            });
                },
                success: function(data) {
                    swal.close();
                     if (data.status == "success") {
                         form[0].reset();
                        notify('Fund request successfully updated', 'success');
                        $('#transferModal').modal('hide');
                        $('#datatable').dataTable().api().ajax.reload();
                    } else {
                        notify('Something went wrong', 'error');
                    }
                },
                error: function(errors) {  
                    notify(errors.statusText, 'error');
                    swal.close();
                }
            });
            return false;
        });

        $("#transferModal").on('hidden.bs.modal', function() {
            $('#transferModal').find('form')[0].reset();
            $('#transferForm').find('input[name="id"]').val('');
            $('#transferModal').find('.payeename').text('');
        });
    });

    function transfer(id, name) {
        $('#transferModal').find('.payeename').text(name);
        $('#transferForm').find('input[name="id"]').val(id);
        $('#transferModal').modal();
    }
</script>
@endpush