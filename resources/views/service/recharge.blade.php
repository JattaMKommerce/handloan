@extends('layouts.app')
@section('title', ucfirst($type) . ' Recharge')
@section('pagetitle', ucfirst($type) . ' Recharge')
@php
$table = 'yes';
@endphp

@section('content')
<div class="content">
    <div class="row">
        <div class="col-sm-12">
            <div class="card iq-card iq-mb-3">
                <div class="card-body">
                    <h4 class="card-title">{{ ucfirst($type) }} Recharge</h4>

                    <form id="rechargeForm" action="{{ route('rechargepay') }}" method="post">
                        {{ csrf_field() }}
                        <input type="hidden" name="type" value="{{ $type }}">
                        <div class="row">
                            {{-- <div class="panel-body"> --}}
                            <div class="form-group col-sm-12 col-md-3 col-lg-3">
                                <label>{{ ucfirst($type) }} Number</label>
                                <input type="text" name="number" id="mobile_no" class="form-control" placeholder="Enter {{ $type }} number" required="">
                            </div>
                            <div class="form-group col-sm-12 col-md-3 col-lg-3">
                                <label>{{ ucfirst($type) }} Operator</label>
                                <select id="provider_id" name="provider_id" class="form-control" required="">
                                    <option value="">Select Operator</option>
                                    @foreach ($providers as $provider)
                                    <option value="{{ $provider->id }}">{{ $provider->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @if($type == "mobile")
                            <div class="form-group col-sm-12 col-md-3 col-lg-3">
                                <label>Circle</label>
                                <select name="circle" class="form-control" id="circle" required>
                                    <option value="">Select Circle</option>
                                    @foreach ($circles as $circle)
                                    <option value="{{$circle->maha_circle_name}}">{{$circle->maha_circle_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                            <div class="form-group col-sm-12 col-md-3 col-lg-3">
                                <label>Recharge Amount</label>
                                <input type="text" name="amount" class="form-control" placeholder="Enter {{ $type }} amount" required="">
                                {{-- <label class="label label-primary planlable btn-raised" onclick="getplan()">PLAN</label> --}}
                                {{-- <label class="label label-primary planlable btn-raised" onclick="roffer()">R-Offer</label>
                                @if ($type != 'mobile')
                                    <label class="label label-primary planlable btn-raised" onclick="custinfo()">DTH
                                        Customer Info</label>
                                @endif --}}
                            </div>
                            <div class="form-group col-sm-12 col-md-3 col-lg-3">
                                <label>T-Pin</label>
                                <input type="password" name="pin" class="form-control" placeholder="Enter transaction pin" required="">
                                <a href="{{ url('profile/view?tab=pinChange') }}" target="_blank" class="text-primary pull-right">Generate Or Forgot Pin??</a>
                            </div>
                        </div>
                        <div class="col-12 text-center">

                            <button type="submit" class="btn btn-primary" id="paynow" data-loading-text="<b><i class='fa fa-spin fa-spinner'></i></b> Paying"><b><i class=" icon-paper"></i></b> Pay Now</button>
                            <button type="button" class="btn submit-button btn-success" onclick="getplan()">GET
                                Plan</button>
                        </div>


                        {{-- <button type="submit" class="btn bg-teal-400 btn-labeled btn-rounded legitRipple btn-lg" data-loading-text="<b><i class='fa fa-spin fa-spinner'></i></b> Paying"><b><i class=" icon-paperplane"></i></b> Pay Now</button> --}}
                </div>
                </form>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 ">
            <div class="card iq-card iq-mb-3">
                <div class="card-body">
                    <h4 class="card-title">Recent {{ ucfirst($type) }} Recharge</h4>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="datatable">
                        <thead class="thead-light">
                            <tr>
                                <th>Order ID</th>
                                <th>Recharge Details</th>
                                <th>Amount/Commission</th>
                                <th>Status</th>
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
<div id="planModal" class="modal fade right" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">

                <h5 class="modal-title msg">Recharge Plans</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-0 planData">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-hidden="true">Close</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endsection

@push('script')
<script type="text/javascript">
    $(document).ready(function() {

        // $('#mobile_no').keyup(function() {
        //     var $mob = $('#mobile_no').val();
        //     if ($mob.length >= 8) {
        //         $.ajax({
        //             url: "{{ route('rechargeprovider') }}",
        //             type: "POST",
        //             data: {
        //                 type: '{{ $type }}',
        //                 Mobileno: $mob,
        //                 _token: '{{ csrf_token() }}'
        //             },

        //             success: function(data) {
        //                 if (data.statuscode == 'TXN')

        //                     $('#provider_id').empty();

        //                 $('#provider_id').append('<option value="' + data.provider.id +
        //                     '">' + data.provider.name + '</option>');


        //             }
        //         })
        //     }


        // });


        var url = "{{ url('statement/fetch') }}/rechargestatement/0";

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
                "data": "bank",
                render: function(data, type, full, meta) {
                    return `Number - ` + full.number + `<br>Operator - ` + full.providername;
                }
            },
            {
                "data": "bank",
                render: function(data, type, full, meta) {
                    return `Amount - <i class="fa fa-inr"></i> ` + full.amount +
                        `<br>Profit - <i class="fa fa-inr"></i> ` + full.profit;
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

        $("#rechargeForm").validate({
            rules: {
                provider_id: {
                    required: true,
                    number: true,
                },
                number: {
                    required: true,
                    number: true,
                    minlength: 8
                },
                amount: {
                    required: true,
                    number: true,
                    min: 10
                },
            },
            messages: {
                provider_id: {
                    required: "Please select {{ $type }} operator",
                    number: "Operator id should be numeric",
                },
                number: {
                    required: "Please enter {{ $type }} number",
                    number: "Mobile number should be numeric",
                    min: "Mobile number length should be atleast 8",
                },
                amount: {
                    required: "Please enter {{ $type }} amount",
                    number: "Amount should be numeric",
                    min: "Min {{ $type }} amount value rs 10",
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
                var form = $('#rechargeForm');
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
                        $('#paynow').html('Pay Now');
                        form.find('button[type="submit"]').button('reset');
                        if (data.status == "success" || data.status == "pending") {
                            getbalance();
                            form[0].reset();
                            form.find('select').val(null).trigger('change')
                            form.find('button[type="submit"]').button('reset');
                            notify("Recharge Successfully Submitted", 'success');
                            $('#datatable').dataTable().api().ajax.reload();
                        } else {
                            notify("Recharge " + data.status + "! " + data.refno,
                                'warning');
                        }
                    },
                    error: function(errors) {
                        swal.close();
                        showError(errors, form);
                        $('#paynow').html('Pay Now');
                    }
                });
            }
        });
    });


    function custinfo() {

        var operator = $('[name="provider_id"]').val();
        var number = $('[name="number"]').val();
        if (number != '' && operator != '') {
            $.ajax({
                    url: '{{ route("dthinformation") }}',
                    type: 'post',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        "operator": operator,
                        'number': number
                    },
                    beforeSend: function() {
                        swal({
                            title: 'Wait!',
                            text: 'Please wait, we are fetching commission details',
                            onOpen: () => {
                                swal.showLoading()
                            },
                            allowOutsideClick: () => !swal.isLoading()
                        });
                    }
                })
                .success(function(data) {
                    swal.close();
                    var tabdata = '';
                    if (data.status == "TXN") {

                        tabdata += `
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="150px">Name</th>
                                        <th width="150px">Balance</th>
                                        <th width="150px">Next Recharge</th>
                                        <th>Status</th>
                                        <th>Plan Name </th>
                                        <th>Monthly Rech. </th>
                                    </tr>
                                </thead>

                                <tbody>
                                 <tr>
                                        <td width="150px">` + data.data.customerName + `</td>
                                        <td width="150px">` + data.data.Balance + `</td>
                                        <td width="150px">` + data.data.NextRechargeDate + `</td>
                                        <td>` + data.data.status + `</td>
                                        <td>` + data.data.planname + ` </td>
                                        <td>` + data.data.MonthlyRecharge + ` </td>
                                    </tr>
                                </tbody>
                            </table>`;

                        var htmldata = `
                            <div class="panel panel-default">
                            
                                <div>
                                    ` + tabdata + `
                                </div>
                            </div>`;


                        $('.planData').html(htmldata);
                        $('.msg').empty();
                        $('#planModal').find('.msg').text("Customer Info");
                        $('#planModal').modal();
                    } else {
                        notify(data.message, 'warning');
                    }
                })
                .fail(function() {
                    swal.close();
                    notify('Somthing went wrong', 'warning');
                });
        } else {
            notify('Mobile number and operator field required', 'warning');
        }
    }


    function activeTab(targetEle) {

        $('.class_for_remove').removeClass('show active');
        $(`#${targetEle}`).addClass('show active');
    }

    function getplan() {
        var operator = $('[name="provider_id"]').val();
        var number = $('[name="number"]').val();
        var circle = $('[name="circle"]').val();
        var type = $('[name="type"]').val();

        if (number != '' && operator != '' && circle != '') {
            $.ajax({
                    url: '{{ route("getplan")}}',
                    type: 'post',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        "operator": operator,
                        'number': number,
                        'circle': circle,
                        'type': type
                    },
                    beforeSend: function() {
                        swal({
                            title: 'Wait!',
                            text: 'Please wait, we are fetching commission details',
                            onOpen: () => {
                                swal.showLoading()
                            },
                            allowOutsideClick: () => !swal.isLoading()
                        });
                    }
                })
                .success(function(data) {
                    swal.close();
                    if (data.status == "success") {
                        var head = `<ul class="nav nav-tabs nav-pills nav-tabs-bottom no-margin" role="tablist" id="pills-tab">`;
                        var tabdata = ``;

                        var count = 0;
                        if (data.data.desc != 'Plan Not Available') {
                            $.each(data.data, function(index, val) {
                                count = count + 1;
                                if (count == "1") {
                                    var active = "active";
                                    var isTrue = "true";
                                } else {
                                    var active = "";
                                    var isTrue = "false";
                                }

                                head += `<li class="nav-item">
                            <a onclick="activeTab('${count}-tab')" href="#` + count +
                                    `-tab" data-toggle="pill" class="nav-link ` + active + `" role="tab" aria-controls="pills-` + count + `-tab" aria-selected="${isTrue}">` +
                                    index + ` Plan</a>
                        </li>`;
                                var plandata = ``;
                                $.each(val, function(index, value) {
                                    @if($type == 'mobile')
                                    plandata +=
                                        `<tr><td><button class="btn btn-xs btn-primary" onclick="setAmount('` +
                                        value.rs +
                                        `')" style="width: 70px;padding:2px 0px;font-size: 15px;"><i class="fa fa-inr"></i> ` +
                                        value.rs + `</button></td><td>` + value.validity +
                                        `</td><td>` + value.desc + `</td>
                                    </tr>`;
                                    @else
                                    var rss = '';
                                    var validitys = '';
                                    $.each(value.rs, function(validity, rs) {
                                        rss = rs;
                                        validitys = validity;
                                    });

                                    plandata +=
                                        `<tr><td><button class="btn btn-xs btn-primary" onclick="setAmount('` +
                                        rss +
                                        `')" style="width: 70px;padding:2px 0px;font-size: 15px;"><i class="fa fa-inr"></i> ` +
                                        rss + `</button></td><td>` + validitys + `</td><td>` +
                                        value.desc + `</td>
                                    </tr>`;
                                    @endif
                                });

                                tabdata += `<div class="tab-pane class_for_remove fade show ` + active + `" id="` + count + `-tab" role="tabpanel" aria-labelledby="pills-` + count + `-tab" >
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th width="250px">Amount</th>
                                                        <th width="250px">Validity</th>
                                                        <th>Description</th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    ` + plandata + `
                                                </tbody>
                                            </table>
                                    </div>`;
                            });
                        } else {
                            tabdata += `<h3 class='text-center'> Plan Not Available </h3>`;
                        }
                        head += '</ul>';

                        var htmldata = `<div class="tabbable">
                            <div class="panel panel-default">
                                <div class="panel-heading p-0">
                                    ` + head + `
                                </div>

                                <div class="tab-content" id="pills-tabContent-2">
                                    ` + tabdata + `
                                </div>
                            </div>
                            </div>`;


                        $('.planData').html(htmldata);
                        $('#planModal').modal();
                    } else {
                        notify(data.message, 'warning');
                    }
                })
                .fail(function() {
                    swal.close();
                    notify('Somthing went wrong', 'warning');
                });
        } else {
            notify('Mobile number and operator field required', 'warning');
        }
    }

    function roffer() {
        var operator = $('[name="provider_id"]').val();
        var number = $('[name="number"]').val();
        if (number != '' && operator != '') {
            $.ajax({
                    url: '{{ route("roffer") }}',
                    type: 'post',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        "operator": operator,
                        'number': number
                    },
                    beforeSend: function() {
                        swal({
                            title: 'Wait!',
                            text: 'Please wait, we are fetching commission details',
                            onOpen: () => {
                                swal.showLoading()
                            },
                            allowOutsideClick: () => !swal.isLoading()
                        });
                    }
                })
                .success(function(data) {
                    console.log(data.data);
                    swal.close();
                    if (data.status == "success") {
                        var head = `<ul class="nav nav-tabs nav-pills nav-tabs-bottom no-margin" role="tablist" id="pills-tab">`;
                        var tabdata = ``;

                        var count = 0;
                        var active = "active";
                        var plandata = ``;

                        $.each(data.data, function(index, val) {

                            count = count + 1;
                            if (count == "1") {
                                var active = "active";
                                var isTrue = "true";

                            } else {
                                var active = "";
                                var isTrue = "false";
                            }
                            @if($type == 'dth')

                            head += `<li class="nav-item">
                            <a onclick="activeTab('${count}-tab')" href="#` + count +
                                `-tab" data-toggle="pill" class="nav-link ` + active + `" role="tab" aria-controls="pills-` + count + `-tab" aria-selected="${istrue}">` +
                                index + ` Plan</a>
                        </li>`;
                            @endif
                            var plandata = ``;

                            @if($type == 'mobile')
                            $.each(data.data, function(index, val) {

                                plandata +=
                                    `<tr><td><button class="btn btn-xs btn-primary" onclick="setAmount('` +
                                    val.rs +
                                    `')" style="width: 70px;padding:2px 0px;font-size: 15px;"><i class="fa fa-inr"></i> ` +
                                    val.rs + `</button></td><td>` + val.desc + `</td>
                           
                                     </tr>`;
                            });
                            @else

                            $.each(data.data, function(index, val) {
                                var rss = '';
                                var validitys = '';
                                plandata +=
                                    `<tr><td><button class="btn btn-xs btn-primary" onclick="setAmount('` +
                                    val.rs +
                                    `')" style="width: 70px;padding:2px 0px;font-size: 15px;"><i class="fa fa-inr"></i> ` +
                                    val.rs + `</button></td><td>` + validitys + `</td><td>` +
                                    val.desc + `</td>
                                    </tr>`;

                            });
                            @endif
                            // head += `<li class="`+active+`">
                            //     <a href="#`+count+`-tab" data-toggle="tab" class="legitRipple" aria-expanded="false">`+index+` Plan</a>
                            // </li>`;
                            // var plandata = ``;
                            // $.each(val, function(index, value) {
                            //      @if ($type == 'mobile')
                            //      console.log(value);
                            //         plandata += `<tr><td><button class="btn btn-xs btn-primary" onclick="setAmount('`+value.rs+`')" style="width: 70px;padding:2px 0px;font-size: 15px;"><i class="fa fa-inr"></i> `+value.rs+`</button></td><td>`+value.desc+`</td>
                            //             </tr>`;
                            //     @else
                            //         var rss = '';

                            //         $.each(value.rs, function( validity, rs) {
                            //             rss = rs;

                            //         });

                            //         plandata += `<tr><td><button class="btn btn-xs btn-primary" onclick="setAmount('`+rss+`')" style="width: 70px;padding:2px 0px;font-size: 15px;"><i class="fa fa-inr"></i> `+rss+`</button></td><td>`+value.desc+`</td>
                            //             </tr>`;
                            //     @endif
                            // });




                            tabdata += `<div class="tab-pane class_for_remove fade show ` + active + `" id="` + count + `-tab" role="tabpanel" aria-labelledby="pills-` + count + `-tab">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th width="150px">Amount</th>
                                                <th width="150px">Validity</th>
                                                    
                                                </tr>
                                            </thead>

                                            <tbody>
                                                ` + plandata + `
                                            </tbody>
                                        </table>
                                </div>`;
                        });
                        head += '</ul>';

                        var htmldata = `<div class="tabbable">
                        <div class="panel panel-default">
                            <div class="panel-heading p-0">
                                ` + head + `
                            </div>

                            <div class="tab-content" id="pills-tabContent-1">
                                ` + tabdata + `
                            </div>
                        </div>
                    </div>`;


                        $('.planData').html(htmldata);
                        $('#planModal').modal();
                    } else {
                        notify(data.message, 'warning');
                    }
                })
                .fail(function() {
                    swal.close();
                    notify('Somthing went wrong', 'warning');
                });
        } else {
            notify('Mobile number and operator field required', 'warning');
        }
    }

    function setAmount(amount) {
        $("[name='amount']").val(amount);
        $('#planModal').modal('hide');
    }
</script>
@endpush