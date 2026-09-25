@extends('layouts.app')
@section('title', "Fund Statement")
@section('pagetitle', "Fund Statement")

@php
$table = "yes";
$export = "fund";
$status['type'] = "Fund";
$status['data'] = [
"success" => "Success",
"pending" => "Pending",
"failed" => "Failed",
"approved" => "Approved",
"rejected" => "Rejected",
];

$product['type'] = "Fund Type";
$product['data'] = [
"fund transfer" => "Transfer",
"fund return" => "Return",
"fund request" => "Request"
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
                                    <th>#</th>
                                    <th>User Details</th>
                                    <th>Refrence Details</th>
                                    <th>Amount</th>
                                    <th>Remark</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                            <tfoot>
                        <tr>
                            <th colspan="3" style="text-align:right">Total:</th>
                            <th id="totalCreditAmount"></th> 
                            <th></th>
                            <th></th>
                       </tr>
                    </tfoot>
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

        var url = "{{url('statement/fetch')}}/fundstatement/0";
        var onDraw = function() {};
        var options = [{
                "data": "name",
                render: function(data, type, full, meta) {
                    return `<span class='text-inverse m-l-10'><b>` + full.id + `</b> </span><br>
                            <span style='font-size:13px'>` + full.updated_at + `</span>`;
                }
            },
            {
                "data": "bank",
                render: function(data, type, full, meta) {
                    var uid = "{{Auth::id()}}";
                    if (full.credited_by == uid) {
                        return full?.username;
                    } else {
                        return full?.sendername;
                    }
                }
            },
            {
                "data": "bank",
                render: function(data, type, full, meta) {
                    if (full.product == "fund request") {
                        return `Name - ` + full?.fundbank?.name + `<br>Account No. - ` + full?.fundbank?.account + `<br>Ref - ` + full?.refno + `(` + full?.product + `)`;
                    } else {
                        return full?.refno + `<br>` + full?.product;
                    }
                }
            },
            {
                "data": "amount"
            },
            {
                "data": "remark"
            },
            {
                "data": "action",
                render: function(data, type, full, meta) {
                    var out = '';
                    if (full?.status == "approved" || full?.status == "success") {
                        out += `<span class="badge badge-success">` + full.status + `</span>`;
                    } else if (full?.status == "pending") {
                        out += `<span class="badge badge-warning">Pending</span>`;
                    } else {
                        out += `<span class="badge badge-danger">` + full?.status + `</span>`;
                    }

                    return out;
                }
            }
        ];

        datatableSetup(url, options, onDraw);
        function datatableSetup(urls, datas, onDraw=function () {}, ele="#datatable", element={}) {
            var options = {
                dom: '<"datatable-scroll"t><"datatable-footer"ip>',
                processing: true,
                serverSide: true,
                ordering: false,
                stateSave: true,
                paging: true,
                pageLength : 10,
                searching: false,
                lengthMenu: [ [10, 25, 50,100,200, -1], [10, 25, 50,100,200, 'All']],
                dom: 'Blfrtip',
                columnDefs: [{
                    orderable: false,
                    width: '130px',
                    targets: [ 0 ]
                }], 
                language: {
                    paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' }
                },
                drawCallback: function () {
                    $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup');
                },
                preDrawCallback: function() {
                    $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').removeClass('dropup');
                },
                "footerCallback": function(row, data, start, end, display) {
                    var api = this.api(),
                        data;
                    // Remove the formatting to get integer data for summation
                    var intVal = function(i) {
                        return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '') * 1 :
                            typeof i === 'number' ?
                            i : 0;
                    };


                    // Initialize variables to store debit and credit sums
                    var debitSum = 0;
                    var creditSum = 0;
                    
                    // Iterate over each row in the DataTable
                    api.rows().every(function() {
                        var data = this.data();
                        // Check if it's a debit transaction
                        if (data.trans_type === "debit") {
                            // Add the amount to the debit sum
                            debitSum += parseFloat(data.amount);
                        }
                        // Check if it's a credit transaction
                        else if (data.trans_type === "credit") {
                            // Add the amount to the credit sum
                            //creditSum += parseFloat(data.amount);
                        }
                        creditSum += parseFloat(data.amount);
                    });
                    
                    // Update footer for debit column (assuming column index 5 for debit)
                    //$(api.column(5).footer()).html(debitSum);
                    
                    // Update footer for credit column (assuming column index 4 for credit)
                    $(api.column(3).footer()).html(creditSum);
                },
                ajax:{
                    url : urls,
                    type: "post",
                    data:function( d )
                        {
                            d._token = $('meta[name="csrf-token"]').attr('content');
                            d.fromdate = $('#searchForm').find('[name="from_date"]').val();
                            d.todate = $('#searchForm').find('[name="to_date"]').val();
                            d.searchtext = $('#searchForm').find('[name="searchtext"]').val();
                            d.agent = $('#searchForm').find('[name="agent"]').val();
                            d.status = $('#searchForm').find('[name="status"]').val();
                            d.product = $('#searchForm').find('[name="product"]').val();
                        },
                    beforeSend: function(){
                    },
                    complete: function(){
                        $('#searchForm').find('button:submit').button('reset');
                        $('#formReset').button('reset');
                    },
                    error:function(response) {
                    }
                },
                columns: datas
            };

            $.each(element, function(index, val) {
                options[index] = val; 
            });

            var DT = $(ele).DataTable(options).on('draw.dt', onDraw);
            return DT;
        }
      
     
        
    
    });
</script>
@endpush