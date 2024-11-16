@extends('layouts.app')
@section('title', 'Investment Fund Statement')
@section('pagetitle', 'Investment Fund Statement')

@php
    $table = 'yes';
    // $export = "fund";
    $status['type'] = 'Fund';
    $status['data'] = [
        'success' => 'Success',
        'pending' => 'Pending',
        'failed' => 'Failed',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
    ];
    
    $product['type'] = 'Fund Type';
    $product['data'] = [
        'transfer' => 'Transfer',
        'return' => 'Return',
        'request' => 'Request',
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
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>User Details</th>
                                        <th>Refrence Details</th>
                                        <th>Amount/Number of Share</th>
                                        <th>Remark</th>
                                        <th>Receipt</th>
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
@endsection

@push('style')
@endpush

@push('script')
    <script type="text/javascript">
        $(document).ready(function() {

            var url = "{{ url('statement/fetch') }}/investfundrequest/0";
            var onDraw = function() {};
            var options = [{
                    "data": "name",
                    render: function(data, type, full, meta) {
                        return `<span class='text-inverse m-l-10'><b>` + full.id + `</b> </span><br>
                            <span style='font-size:13px'>` + full.updated_at + `</span>`;
                    }
                },
                {
                    "data": "username",
                    // render: function(data, type, full, meta) {
                    //     // var uid = "{{ Auth::id() }}";
                    //     if (full.credited_by == uid) {
                    //         return full.username;
                    //     } else {
                    //         return full.sendername;
                    //     }
                    // }
                },
                {
                    "data": "bank",
                    render: function(data, type, full, meta) {
                        // if (full.product == "fund request") {
                        return `Name - ` + full.fundbank.name + `<br>Account No. - ` + full.fundbank
                            .account + `<br>Ref - ` + full.ref_no;
                        // } else {
                        //     return full.refno + `<br>` + full.product;
                        // }
                    }
                },
                {
                    "data": "amount",
                    render: function(data, type, full, meta) {
                        return `Amount - ` + full.amount + `<br>No. Of Share - ` + full.numberOfshares
                       
                    }

                    
                },
                {
                    "data": "remark"
                },
                {
                    "data": "remak",
                    render: function(data, type, full, meta) {
                        var ot = '';
                        if (full.payslip != "" || full.payslip != undefined) {
                            ot +=`<a target="_blank" href="{{ asset('deposit_slip') }}/${full.payslip}"><u>Receipt</u></a>`;
                        }
                        return ot;
                    }
                },
                {
                    "data": "action",
                    render: function(data, type, full, meta) {
                        var out = '';
                        if (full.status == "approved" || full.status == "success") {
                            out += `<span class="badge badge-success">` + full.status + `</span>`;
                        } else if (full.status == "pending") {
                            out += `<span class="badge badge-warning">` + full.status + `</span>`;
                        } else {
                            out += `<span class="badge badge-danger">` + full.status + `</span>`;
                        }

                        return out;
                    }
                }
            ];

            datatableSetup(url, options, onDraw);
        });
    </script>
@endpush
