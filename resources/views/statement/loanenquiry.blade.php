@extends('layouts.app')
@section('title', "Loan Enquiry")
@section('pagetitle', "Loan Enquiry")

@php
$table = "yes";
$export = "loandata";
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
                                    <th>Order ID</th>
                                    <th>User Details</th>
                                    <th>Ref. Details</th>
                                    <th>Customer Name</th>
                                    <th>Amount</th>
                                    <th>Adharcard no</th>
                                    <th>Pancard no</th>
                                    <th>Address</th>
                                    <th>Pincode</th>
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
        var url = "{{url('statement/fetch')}}/loanenquirystatement/{{$id}}";
        var onDraw = function() {};
        var options = [{
                "data": "id",
                render: function(data, type, full, meta) {
                    return `<div>
                            <span class='text-inverse m-l-10'><b>` + full.id + `</b> </span>
                            <div class="clearfix"></div>
                        </div><span style='font-size:13px' class="pull=right">` + full.created_at + `</span>`;
                }
            },
            {
                "data": "username"
            },
            {
                "data": "ref"
            },
            {
                "data": "name",
                render: function(data, type, full, meta) {
                    return full.c_name;
                }
            },
            {
                "data": "bank",
                render: function(data, type, full, meta) {
                    return `Amount - <i class="fa fa-inr"></i> ` + full.loanamount;
                }
            },
            {
                "data": "adhar"
            },
            {
                "data": "pan"
            },
            {
                "data": "address",
                render: function(data, type, full, meta) {
                    return `Address - ` + full.address + `<br>City-` + full.city + `<br>State-` + full.state;
                }
            },
            {
                "data": "pincode"
            },

        ];

        datatableSetup(url, options, onDraw);


        $("#editUtiModal").on('hidden.bs.modal', function() {
            $('#setupModal').find('form')[0].reset();
        });
    });
</script>
@endpush