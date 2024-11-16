@extends('layouts.app')
@section('title', ucfirst($type).' Bill Payment')
@section('pagetitle', ucfirst($type).' Bill Payment')
@php
$table = "yes";
@endphp

@section('content')

<div class="row">
    <!-- <div class="col-sm-0 col-lg-3"></div> -->
    <div class="col-sm-12 col-lg-6">
        <div class="iq-card">
            <div class="iq-card-header d-flex justify-content-between">
                <div class="iq-header-title">
                    <h4 class="card-title">Bill Payment</h4>
                </div>
            </div>
            <div class="iq-card-body">
                @if($mydata['billnotice'] != null && $mydata['billnotice'] != '')
                <b class="text-danger" style="font-size:16px;">
                    <marquee>{{$mydata['billnotice']}}</marquee>
                </b>
                @endif
                <form id="billpayForm" action="{{route('billpay')}}" method="post">

                    {{ csrf_field() }}
                    <input type="hidden" name="type" value="getbilldetails">
                    <input type="hidden" name="operatorType" value="{{$type}}">
                    <input type="hidden" name="TransactionId">
                    <input type="hidden" name="mode" value="online">

                    <div class="form-group">
                        <label>{{ucfirst($type)}} Operator</label>
                        <select class="form-control mb-3" name="provider_id" required="" onchange="SETTITLE()">
                            <option selected="">Select Operator</option>
                            @foreach ($providers as $provider)
                            <option value="{{$provider->id}}">{{$provider->name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="billdata">

                    </div>

                    <div class="form-group">
                        <label>T-Pin</label>
                        <a href="{{url('profile/view?tab=pinChange')}}" target="_blank" class="float-right">Generate Or Forgot Pin??</a>
                        <input type="password" name="pin" class="form-control" placeholder="Enter transaction pin" required="">
                    </div>

                    <div class="d-flex flex-row justify-content-end">
                        <button type="submit" class="btn btn-lg btn-primary" id="fetch">Fetch</button>
                        <button type="submit" class="btn btn-lg btn-success submit-button mx-2" id="pay">Pay</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-sm-12 col-lg-6">
        <div class="iq-card">
            <img src="https://pwa-cdn.freecharge.in/pwa-static/pwa/images/operators/electricity.svg">

        </div>
    </div>
</div>

@endsection

@push('script')
<script src="{{ asset('/assets/js/core/jQuery.print.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function() {

        var url = "{{url('statement/fetch')}}/{{$type}}statement/0";

        var onDraw = function() {};

        var options = [{
                "data": "name",
                render: function(data, type, full, meta) {
                    return `<div>
                            <span class='text-inverse m-l-10'><b>` + full.id + `</b> </span>
                            <div class="clearfix"></div>
                        </div><span style='font-size:13px' class="pull=right">` + full.created_at + `</span>`;
                }
            },
            {
                "data": "name",
                render: function(data, type, full, meta) {
                    return `
                        <span style='font-size:13px' class="pull=right">` + full.created_at + `</span>`;
                }
            },
            {
                "data": "username"
            },
            {
                "data": "bank",
                render: function(data, type, full, meta) {
                    return `Number - ` + full.number + `<br>Operator - ` + full.providername;
                }
            },
            {
                "data": "bank",
                render: function(data, type, full, meta) {
                    return `Ref No.  - ` + full.refno + `<br>Txnid - ` + full.txnid;
                }
            },
            {
                "data": "bank",
                render: function(data, type, full, meta) {
                    return `Amount - <i class="fa fa-inr"></i> ` + full.amount + `<br>Profit - <i class="fa fa-inr"></i> ` + full.profit;
                }
            },
            {
                "data": "status",
                render: function(data, type, full, meta) {
                    if (full.status == "success") {
                        var out = `<span class="badge badge-success">Success</span>`;
                    } else if (full.status == "pending") {
                        var out = `<span class="badge badge-warning">Pending</span>`;
                    } else if (full.status == "reversed") {
                        var out = `<span class="badge badge-primary">Reversed</span>`;
                    } else {
                        var out = `<span class="badge badge-danger">Failed</span>`;
                    }

                    return out;
                }
            }
        ];

        datatableSetup(url, options, onDraw);

        $('#print').click(function() {
            $('#receipt').find('.modal-body').print();
        });

        $("#billpayForm").validate({
            rules: {
                provider_id: {
                    required: true,
                    number: true,
                },
                amount: {
                    required: true,
                    number: true,
                    min: 10
                },
                biller: {
                    required: true
                },
                duedate: {
                    required: true,
                },
            },
            messages: {
                provider_id: {
                    required: "Please select recharge operator",
                    number: "Operator id should be numeric",
                },
                amount: {
                    required: "Please enter recharge amount",
                    number: "Amount should be numeric",
                    min: "Min recharge amount value rs 10",
                },
                biller: {
                    required: "Please enter biller name",
                },
                duedate: {
                    required: "Please enter biller duedate",
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
                var form = $('#billpayForm');
                var id = form.find('[name="id"]').val();
                var type = form.find('[name="type"]').val();

                form.ajaxSubmit({
                    dataType: 'json',
                    beforeSubmit: function() {
                        form.find('button[type="submit"]').button('loading');

                        if (type == "getbilldetails") {
                            swal({
                                title: 'Wait!',
                                text: 'We are fetching bill details',
                                onOpen: () => {
                                    swal.showLoading()
                                },
                                allowOutsideClick: () => !swal.isLoading()
                            });
                        }
                    },
                    success: function(data) {
                        console.log(data);
                        swal.close();
                        if (data.statuscode == "TXN") {
                            $('#billpayForm').find('[name="type"]').val("payment");
                            $('#billpayForm').find('[name="TransactionId"]').val(data.data.TransactionId);
                            $('#billpayForm').find('[name="mode"]').val(data.data.mode);
                            $('.billdata').append(`
                                <div class="form-group">
                                    <label>Consumer Name</label>
                                    <input type="text" name="biller" value="` + data.data.customername + `" class="form-control" placeholder="Enter name" required="">
                                </div>
                                <div class="form-group">
                                    <label>Due Date</label>
                                    <input type="text" name="duedate" value="` + data.data.duedate + `" class="form-control" placeholder="Enter due date" required="">
                                </div>
                                <div class="form-group">
                                    <label>Amount</label>
                                    <input type="text" name="amount" value="` + data.data.dueamount + `" class="form-control" placeholder="Enter amount" required="">
                            </div>`);

                            $('#fetch').hide();
                            $('#pay').show();

                        } else if (data.status == "success" || data.status == "pending") {
                            console.log('elseif')
                            form[0].reset();
                            $('#billpayForm').find('[name="type"]').val("getbilldetails");
                            form.find('select').select2().val(null).trigger('change');
                            getbalance();
                            notify("Billpayment Successfully Submitted", 'success');

                            $('#receipt').find('.created_at').text(data.data.created_at);
                            $('#receipt').find('.amount').text(data.data.amount);
                            $('#receipt').find('.refno').text(data.data.txnid);
                            $('#receipt').find('.number').text(data.data.number);
                            $('#receipt').find('.order_id').text(data.data.id);
                            $('#receipt').find('.provider').text(data.data.provider.name);
                            $('#receipt').modal();
                            $('#datatable').dataTable().api().ajax.reload();
                        } else {
                            notify(data.description, 'error');
                        }
                    },
                    error: function(errors) {
                        swal.close();
                        showError(errors, form);
                        $('#fetch').html('Fetch');
                        $('#pay').html('Pay');
                    }
                });
            }
        });
    });

    function SETTITLE() {
        var providerid = $('[name="provider_id"]').val();

        if (providerid != '') {
            $.ajax({
                    url: "{{route('getprovider')}}",
                    type: 'post',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function() {
                        swal({
                            title: 'Wait!',
                            text: 'We are fetching bill details',
                            onOpen: () => {
                                swal.showLoading()
                            },
                            allowOutsideClick: () => !swal.isLoading()
                        });
                    },
                    data: {
                        "provider_id": providerid
                    }
                })
                .done(function(data) {
                    swal.close();
                    $('#billpayForm').find('[name="type"]').val("getbilldetails");
                    $('.billdata').empty();

                    $.each(data.paramname, function(i, val) {
                        var html = '<div>';
                        html += '<div class="form-group">';
                        html += '<label>' + data.paramname[i] + '</label>';
                        html += '<input type="text" name="number' + i + '" class="form-control" placeholder="Enter ' + data.paramname[i] + '">';
                        html += '</div>';
                        html += '</div>';


                        // alert(html)
                        $('.billdata').append(html);
                    });
                })
                .fail(function(errors) {
                    swal.close();
                    showError(errors, $('#billpayForm'));
                });
        }
    }
</script>
@endpush