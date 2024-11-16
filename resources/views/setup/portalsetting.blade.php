@extends('layouts.app')
@section('title', 'Portal Settings')
@section('pagetitle', 'Portal Settings')

@section('content')
<div class="content">
    <div class="row">

        <div class="col-sm-4">
            <div class="card iq-mb-3">
                <div class="card-body">
                    <h4 class="card-title">Wallet Settlement Type</h4>
                    <form class="actionForm" action="{{route('setupupdate')}}" method="post">
                        {{ csrf_field() }}
                        <input type="hidden" name="actiontype" value="portalsetting">
                        <input type="hidden" name="code" value="settlementtype">
                        <input type="hidden" name="name" value="Wallet Settlement Type">
                        <div class="form-group">
                            <label>Settlement Type</label>
                            <select name="value" required="" class="form-control">
                                <option value="">Select Type</option>
                                <option value="auto" {{(isset($settlementtype->value) && $settlementtype->value == "auto") ? "selected=''" : ''}}>Auto</option>
                                <option value="mannual" {{(isset($settlementtype->value) && $settlementtype->value == "mannual") ? "selected=''" : ''}}>Mannual</option>
                            </select>
                        </div>
                        <button class="btn btn-primary" type="submit">Update Info</button>
                    </form>
                </div>
            </div>
        </div>


        <div class="col-sm-4">
            <div class="card iq-mb-3">
                <div class="card-body">
                    <h4 class="card-title">Bank Settlement Type</h4>
                    <form class="actionForm" action="{{route('setupupdate')}}" method="post">
                        {{ csrf_field() }}
                        <input type="hidden" name="actiontype" value="portalsetting">
                        <input type="hidden" name="code" value="banksettlementtype">
                        <input type="hidden" name="name" value="Wallet Settlement Type">
                        <div class="form-group">
                            <label>Settlement Type</label>
                            <select name="value" required="" class="form-control">
                                <option value="">Select Type</option>
                                <option value="auto" {{(isset($banksettlementtype->value) && $banksettlementtype->value == "auto") ? "selected=''" : ''}}>Auto</option>
                                <option value="mannual" {{(isset($banksettlementtype->value) && $banksettlementtype->value == "mannual") ? "selected=''" : ''}}>Mannual</option>
                                <option value="down" {{(isset($banksettlementtype->value) && $banksettlementtype->value == "down") ? "selected=''" : ''}}>Down</option>
                            </select>
                        </div>
                        <button class="btn btn-primary" type="submit">Update Info</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="card iq-mb-3">
                <div class="card-body">
                    <h4 class="card-title">AEPS Bank Settlement API</h4>
                    <form class="actionForm" action="{{route('setupupdate')}}" method="post">
                        {{ csrf_field() }}
                        <input type="hidden" name="actiontype" value="portalsetting">
                        <input type="hidden" name="code" value="settlementapi">
                        <input type="hidden" name="name" value="Wallet Settlement Type">
                        <div class="form-group">
                            <label>Settlement Type</label>
                            <select name="value" required="" class="form-control">
                                <option value="">Select Type</option>
                                <option value="cyruspayout" {{(isset($settlementapi->value) && $settlementapi->value == "cyruspayout") ? "selected=''" : ''}}>Cyrus Payout</option>
                                <option value="runpaisa" {{(isset($settlementapi->value) && $settlementapi->value == "runpaisa") ? "selected=''" : ''}}>RunPaisa Payout</option>
                                <option value="down" {{(isset($settlementapi->value) && $settlementapi->value == "down") ? "selected=''" : ''}}>Down</option>
                            </select>
                        </div>
                        <button class="btn btn-primary" type="submit">Update Info</button>
                    </form>
                </div>
            </div>
        </div>


        <div class="col-sm-4">
            <div class="card iq-mb-3">
                <div class="card-body">
                    <h4 class="card-title">Bank Settlement Charge</h4>
                    <form class="actionForm" action="{{route('setupupdate')}}" method="post">
                        {{ csrf_field() }}
                        <input type="hidden" name="actiontype" value="portalsetting">
                        <input type="hidden" name="code" value="settlementtype">
                        <input type="hidden" name="name" value="Wallet Settlement Type">
                        <div class="form-group">
                            <label>Charge</label>
                            <input type="number" name="value" value="{{$settlementcharge->value ?? ''}}" class="form-control" required="" placeholder="Enter value">
                        </div>
                        <button class="btn btn-primary" type="submit">Update Info</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="card iq-mb-3">
                <div class="card-body">
                    <h4 class="card-title">Bank Settlement Charge Upto 25000</h4>
                    <form class="actionForm" action="{{route('setupupdate')}}" method="post">
                        {{ csrf_field() }}
                        <input type="hidden" name="actiontype" value="portalsetting">
                        <input type="hidden" name="code" value="impschargeupto25">
                        <input type="hidden" name="name" value="Bank Settlement Charge Upto 25000">
                        <div class="form-group">
                            <label>Charge</label>
                            <input type="number" name="value" value="{{$impschargeupto25->value ?? ''}}" class="form-control" required="" placeholder="Enter value">
                        </div>
                        <button class="btn btn-primary" type="submit">Update Info</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="card iq-mb-3">
                <div class="card-body">
                    <h4 class="card-title">Bank Settlement Charge Above 25000</h4>
                    <form class="actionForm" action="{{route('setupupdate')}}" method="post">
                        {{ csrf_field() }}
                        <input type="hidden" name="actiontype" value="portalsetting">
                        <input type="hidden" name="code" value="impschargeabove25">
                        <input type="hidden" name="name" value="Bank Settlement Charge Above 25000">
                        <div class="form-group">
                            <label>Charge</label>
                            <input type="number" name="value" value="{{$impschargeabove25->value ?? ''}}" class="form-control" required="" placeholder="Enter value">
                        </div>
                        <button class="btn btn-primary" type="submit">Update Info</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="card iq-mb-3">
                <div class="card-body">
                    <h4 class="card-title">login with OTP</h4>
                    <form class="actionForm" action="{{route('setupupdate')}}" method="post">
                        {{ csrf_field() }}
                        <input type="hidden" name="actiontype" value="portalsetting">
                        <input type="hidden" name="code" value="otplogin">
                        <input type="hidden" name="name" value="Login required otp">
                        <div class="form-group">
                            <label>Login Type</label>
                            <select name="value" required="" class="form-control">
                                <option value="">Select Type</option>
                                <option value="yes" {{(isset($otplogin->value) && $otplogin->value == "yes") ? "selected=''" : ''}}>With Otp</option>
                                <option value="no" {{(isset($otplogin->value) && $otplogin->value == "no") ? "selected=''" : ''}}>Without Otp</option>
                            </select>
                        </div>
                        <button class="btn btn-primary" type="submit">Update Info</button>
                    </form>
                </div>
            </div>
        </div>


        <div class="col-sm-4">
            <div class="card iq-mb-3">
                <div class="card-body">
                    <h4 class="card-title">Sending mail id for otp</h4>
                    <form class="actionForm" action="{{route('setupupdate')}}" method="post">
                        {{ csrf_field() }}
                        <input type="hidden" name="actiontype" value="portalsetting">
                        <input type="hidden" name="code" value="otpsendmailid">
                        <input type="hidden" name="name" value="Sending mail id for otp">
                        <div class="form-group">
                            <label>Mail Id</label>
                            <input type="text" name="value" value="{{$otpsendmailid->value ?? ''}}" class="form-control" required="" placeholder="Enter value">
                        </div>
                        <button class="btn btn-primary" type="submit">Update Info</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="card iq-mb-3">
                <div class="card-body">
                    <h4 class="card-title">Sending mailer name id for otp</h4>
                    <form class="actionForm" action="{{route('setupupdate')}}" method="post">
                        {{ csrf_field() }}
                        <input type="hidden" name="actiontype" value="portalsetting">
                        <input type="hidden" name="code" value="otpsendmailname">
                        <input type="hidden" name="name" value="Sending mailer name id for otp">
                        <div class="form-group">
                            <label>Mailer Name</label>
                            <input type="text" name="value" value="{{$otpsendmailname->value ?? ''}}" class="form-control" required="" placeholder="Enter value">
                        </div>
                        <button class="btn btn-primary" type="submit">Update Info</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="card iq-mb-3">
                <div class="card-body">
                    <h4 class="card-title">Bc Id for DMT</h4>
                    <form class="actionForm" action="{{route('setupupdate')}}" method="post">
                        {{ csrf_field() }}
                        <input type="hidden" name="actiontype" value="portalsetting">
                        <input type="hidden" name="code" value="bcid">
                        <input type="hidden" name="name" value="Bc Id for dmt">
                        <div class="form-group">
                            <label>Bcid</label>
                            <input type="text" name="value" value="{{$bcid->value ?? ''}}" class="form-control" required="" placeholder="Enter value">
                        </div>
                        <button class="btn btn-primary" type="submit">Update Info</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="card iq-mb-3">
                <div class="card-body">
                    <h4 class="card-title">CP Id For DMT</h4>
                    <form class="actionForm" action="{{route('setupupdate')}}" method="post">
                        {{ csrf_field() }}
                        <input type="hidden" name="actiontype" value="portalsetting">
                        <input type="hidden" name="code" value="cpid">
                        <input type="hidden" name="name" value="CP Id for dmt">
                        <div class="form-group">
                            <label>CP id</label>
                            <input type="text" name="value" value="{{$cpid->value ?? ''}}" class="form-control" required="" placeholder="Enter value">
                        </div>
                        <button class="btn btn-primary" type="submit">Update Info</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="card iq-mb-3">
                <div class="card-body">
                    <h4 class="card-title">Transaction Id Code</h4>
                    <form class="actionForm" action="{{route('setupupdate')}}" method="post">
                        {{ csrf_field() }}
                        <input type="hidden" name="actiontype" value="portalsetting">
                        <input type="hidden" name="code" value="transactioncode">
                        <input type="hidden" name="name" value="Transaction Id Code">
                        <div class="form-group">
                            <label>Code</label>
                            <input type="text" name="value" value="{{$transactioncode->value ?? ''}}" class="form-control" required="" placeholder="Enter value">
                        </div>
                        <button class="btn btn-primary" type="submit">Update Info</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="card iq-mb-3">
                <div class="card-body">
                    <h4 class="card-title">Main Wallet Locked Amount</h4>
                    <form class="actionForm" action="{{route('setupupdate')}}" method="post">
                        {{ csrf_field() }}
                        <input type="hidden" name="actiontype" value="portalsetting">
                        <input type="hidden" name="code" value="mainlockedamount">
                        <input type="hidden" name="name" value="Main Wallet Locked Amount">
                        <div class="form-group">
                            <label>Amount</label>
                            <input type="text" name="value" value="{{$mainlockedamount->value ?? ''}}" class="form-control" required="" placeholder="Enter value">
                        </div>
                        <button class="btn btn-primary" type="submit">Update Info</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="card iq-mb-3">
                <div class="card-body">
                    <h4 class="card-title">Aeps Bank Settlement Locked Amount</h4>
                    <form class="actionForm" action="{{route('setupupdate')}}" method="post">
                        {{ csrf_field() }}
                        <input type="hidden" name="actiontype" value="portalsetting">
                        <input type="hidden" name="code" value="aepslockedamount">
                        <input type="hidden" name="name" value="Aeps Bank Settlement Locked Amount">
                        <div class="form-group">
                            <label>Amount</label>
                            <input type="text" name="value" value="{{$aepslockedamount->value ?? ''}}" class="form-control" required="" placeholder="Enter value">
                        </div>
                        <button class="btn btn-primary" type="submit">Update Info</button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-sm-4">
            <div class="card iq-mb-3">
                <div class="card-body">
                    <h4 class="card-title">Aeps Settlement Time</h4>
                    <form class="actionForm" action="{{route('setupupdate')}}" method="post">
                        {{ csrf_field() }}
                        <input type="hidden" name="actiontype" value="portalsetting">
                        <input type="hidden" name="code" value="aepsslabtime">
                        <input type="hidden" name="name" value="Aeps Settlement Time">
                        <div class="form-group">
                            <label>Time (Comma Seperated)</label>
                            <textarea name="value" class="form-control" required="" placeholder="Enter value">{{$aepsslabtime->value ?? ''}}</textarea>
                        </div>
                        <p class="text-muted">Example - 11:00 Am, 2:00 PM</p>
                        <button class="btn btn-primary" type="submit">Update Info</button>
                    </form>
                </div>
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
                    form.find('button[type="submit"]').html('loading').attr('disabled',true).addClass('btn-secondary');
                },
                success: function(data) {
                    if (data.status == "success") {
                        if (id == "new") {
                            form[0].reset();
                            $('[name="api_id"]').select2().val(null).trigger('change');
                        }
                        form.find('button[type="submit"]').html('Update Info').attr('disabled',false).removeClass('btn-secondary');
                        notify("Task Successfully Completed", 'success');
                        $('#datatable').dataTable().api().ajax.reload();
                    } else {
                        notify(data.status, 'warning');
                    }
                },
                error: function(errors) {
                    showError(errors, form);
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