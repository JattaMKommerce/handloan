@extends('layouts.app')
@section('title', 'Account Statement')
@section('pagetitle', 'Account Statement')

@php
    $table = 'yes';
    // $export = 'wallet';
@endphp

@section('content')

    <div class="content">
        <div class="row">
            <div class="col-sm-12">
                <div class="iq-card">
                    <div class="iq-card-body">
                        <div class="table-responsive">
                            <table class="table text-center" id="datatable">
                                <thead class="thead-light ">
                                    <tr>
                                        <th class="w-75">#</th>
                                        <th width="150px">Refrence Details</th>
                                        <th>Product</th>
                                        <th>Txnid</th>
                                        <th>Number</th>
                                        <th width="100px">ST Type</th>
                                        <th>Status</th>
                                        <th width="130px">Opening Bal.</th>
                                        <th width="130px">Amount</th>
                                        <th width="130px">Charge</th>
                                        <th>Commission /Profit</th>
                                        <th >Closing Bal.</th>
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
@endsection

@push('style')
@endpush

@push('script')
    <script type="text/javascript">
        $(document).ready(function() {
            var url = "{{ url('statement/fetch') }}/investmentwalletstatement/{{ $id }}";
            var onDraw = function() {
                $('[data-popup="tooltip"]').tooltip();
                $('[data-popup="popover"]').popover({
                    template: '<div class="popover border-teal-400"><div class="arrow"></div><h3 class="popover-title bg-teal-400"></h3><div class="popover-content"></div></div>'
                });
            };
            var options = [{
                    "data": "name",
                    render: function(data, type, full, meta) {
                        return `<div><span class='text-inverse m-l-10'><b>` +
                            full.id +
                            // (full.id == null || full.id == undefined )? "":
                            `</b> </span><div class="clearfix"></div></div><span style='font-size:13px' class="pull=right">` +
                            full.created_at + `</span>`;
                        // (full.created_at == null || full.created_at == undefined) ?"":
                    }
                },
                {
                    "data": "full.username",
                    render: function(data, type, full, meta) {
                        var uid = "{{ Auth::id() }}";
                        if (full.credited_by == uid) {
                            var name = full.username;
                        } else {
                            var name = full.sendername;
                        }
                        return name;
                    }
                },
                {
                    "data": "product"
                },
                {
                    "data": "txnid"
                },
                {
                    "data": "number"
                },
                {
                    "data": "rtype"
                },
                {
                    "data": "status",
                    render: function(data, type, full, meta) {
                        if (full.status == 'inactive') {
                            return `<span class="badge badge-danger">failed</span>`;
                        } else {
                            return `<span class="badge badge-success">success</span>`;
                        }
                    }
                },
                {
                    "data": "bank",
                    render: function(data, type, full, meta) {
                        return `<i class="fa fa-inr"></i> ` + full.balance;
                    }
                },
                {
                    "data": "bank",
                    render: function(data, type, full, meta) {
                        return `<i class="fa fa-inr"></i> ` + full.amount;
                        //   if(full.status == "pending" || full.status == "success" || full.status == "reversed" || full.status == "refunded"){
                        //     if(full.trans_type == "credit"){
                        //         return `<i class="fa fa-inr"></i> `+ (parseFloat(full.amount) + parseFloat(full.charge) - parseFloat(full.profit));
                        //     }else if(full.trans_type == "debit"){
                        //         return `<i class="fa fa-inr"></i> `+ (parseFloat(full.amount) - parseFloat(full.charge) - parseFloat(full.profit));
                        //     }else if(full.trans_type == "none"){
                        //         return `<i class="fa fa-inr"></i> `+ (parseFloat(full.amount) + parseFloat(full.charge) - parseFloat(full.profit));
                        //     }
                        // }else{
                        //    return `<i class="fa fa-inr"></i> `+full.balance;
                        //}
                    }
                },
                {
                    "data": "charge",
                    render: function(data, type, full, meta) {
                        if (full.charge > 0) {
                            return `<i class="fa fa-inr"></i> ` + full.charge;
                        } else {
                            return 0;
                        }
                    }
                },
                {
                    "data": "profit",
                    render: function(data, type, full, meta) {
                        if (full.profit > 0) {
                            return `<i class="fa fa-inr"></i> ` + full.profit
                        } else {
                            return `<i class="fa fa-inr"></i> ` + full.profit
                        }
                    }
                },
                {
                    "data": "bank",
                    render: function(data, type, full, meta) {
                        if (full.status == "pending" || full.status == "success" || full.status ==
                            "reversed" || full.status == "refunded") {
                            if (full.trans_type == "credit") {
                                return `<i class="fa fa-inr"></i> ` + (parseFloat(full.balance) +
                                    parseFloat(parseFloat(full.amount) + parseFloat(full.charge) -
                                        parseFloat(full.profit))).toFixed(2);
                            } else if (full.trans_type == "debit") {
                                return `<i class="fa fa-inr"></i> ` + (parseFloat(full.balance) -
                                    parseFloat(parseFloat(full.amount) + parseFloat(full.charge) -
                                        parseFloat(full.profit))).toFixed(2);
                            } else if (full.trans_type == "none") {
                                return `<i class="fa fa-inr"></i> ` + (parseFloat(full.balance) -
                                    parseFloat(parseFloat(full.amount) + parseFloat(full.charge) -
                                        parseFloat(full.profit))).toFixed(2);
                            }
                        } else {
                            return `<i class="fa fa-inr"></i> ` + full.balance;
                        }
                    }
                },
            ];

            datatableSetup(url, options, onDraw, '#datatable', {
                columnDefs: [{
                    orderable: false,
                    width: '80px',
                    targets: [0]
                }]
            });
        });
    </script>
@endpush
