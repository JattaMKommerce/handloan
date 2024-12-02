@extends('layouts.app')
@section('title', "Move To Wallet Transfer")
@section('pagetitle',  "Move To Wallet Transfer")

@php
    $table = "yes";
@endphp

@section('content')
  <style>
  

    #transferForm #user-dropdown {
    width: auto;
    }
   #transferForm div#user-dropdown ul {
    margin: 0;
    padding: 0;
}

  #transferForm div#user-dropdown ul li {
    list-style: none;
    border-bottom: 1px solid #a0a2a3;
        cursor: pointer;
}
#transferForm div#user-dropdown ul li:hover {
    background: #eae9e9;

}
    
</style>
<div class="content">
    <div class="row">
        <div class="col-sm-12">
            <div class="iq-card">
                <div class="iq-card-header d-flex justify-content-between">
                    <h4 class="iq-header-title">Move To Wallet</h4>
                    <div class="heading-elements">
                        <button type="button" data-toggle="modal" data-target="#transferModal" class="btn bg-slate btn-xs btn-labeled legitRipple btn-lg" data-loading-text="<b><i class='fa fa-spin fa-spinner'></i></b> Searching"><b><i class="icon-plus2"></i></b>Transfer</button>
                    </div>
                </div>
                <div class="iq-card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover" id="datatable">
                    <thead>
                        <tr>
                            <th width="160px">#</th>
                            <th>User Details</th>
                            <th>Bank Details</th>
                            <th>Refrence Details</th>
                            <th width="200px">Amount</th>
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

<div id="transferModal" class="modal fade" data-backdrop="false" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-slate">
                <h6 class="modal-title">Move to Wallet Transfer</h6>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                
            </div>
            <form id="transferForm" action="{{route('fundtransaction')}}" method="post">
                <div class="modal-body">
                    <div class="row">
                        {{ csrf_field() }}
                    
                        <input type="hidden" name="type" value="wallet">
                        <input type="hidden" name="user_id">
                   
                        <div class="form-group col-md-12">
                            <label>Amount</label>
                            <input type="number" name="amount" step="any" class="form-control" placeholder="Enter Amount" required="">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label>Remark</label>
                            <textarea name="remark" class="form-control" rows="3" placeholder="Enter Remark"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-5">
                                <label>T-Pin <span class="text-danger">*</span></label>
                                <input type="password" name="pin" class="form-control" placeholder="Enter transaction pin" required="">
                                <a href="{{url('profile/view?tab=pinChange')}}" target="_blank" class="text-primary pull-right">Generate Or Forgot Pin??</a>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-raised legitRipple" data-dismiss="modal" aria-hidden="true">Close</button>
                    <button class="btn bg-slate btn-raised legitRipple" type="submit" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Submitting">Submit</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endsection

@push('style')

@endpush

@push('script')
<script type="text/javascript">
    $(document).ready(function () {
        var url = "{{url('statement/fetch')}}/aepsfundrequest/0";
        var onDraw = function() {};
        var options = [
            { "data" : "name",
                render:function(data, type, full, meta){
                    var out = '';
                    if(full.api){
                        out +=  `<span class='myspan'>`+full.api.api_name +`</span><br>`;
                    }
                    out += `<span class='text-inverse'>`+full.id +`</span><br><span style='font-size:12px'>`+full.created_at+`</span>`;
                    return out;
                }
            },
            { "data" : "username"},
            { "data" : "bank",
                render:function(data, type, full, meta){
                    if(full.type == "wallet"){
                        return "Wallet"
                    }else{
                        return full.account +" ( "+full.bank+" )<br>"+full.ifsc;
                    }
                }
            },
            { "data" : "bank",
                render:function(data, type, full, meta){
                    if(full.type == "wallet"){
                        return "Wallet"
                    }else{
                        if(full.pay_type == "payout"){
                            return "Ref - "+full.payoutref +"<br>Txnid - "+full.payoutid;
                        }else{
                            return "Manual";
                        }
                    }
                }
            },
            { "data" : "status",
                render:function(data, type, full, meta){
                    return `<span class='text-inverse'><i class="fa fa-rupee"></i> `+full.amount +`</span> / `+full.type;
                }
            },
            { "data" : "remark"},
            { 
                "data": "status",
                render:function(data, type, full, meta){
                    if(full.status == "approved"){
                        var btn = '<span class="label label-success text-uppercase"><b>'+full.status+'</b></span>';
                    }else if(full.status== 'pending'){
                        var btn = '<span class="label label-warning text-uppercase"><b>'+full.status+'</b></span>';
                    }else{
                        var btn = '<span class="label label-danger text-uppercase"><b>'+full.status+'</b></span>';
                    }
                    return btn;
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

                    // Total over this page
                    pageTotal = api
                        .column(5, {
                            page: 'current'
                        })
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    // Update footer
                    $(api.column(5).footer()).html(
                        pageTotal
                    );
                    
                    // Total over this page
                    pageTotal = api
                        .column(6, {
                            page: 'current'
                        })
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    // Update footer
                    $(api.column(6).footer()).html(
                        pageTotal
                    );
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

        $( "#transferForm").validate({
            rules: {
                type: {
                    required: true
                },
                amount: {
                    required: true,
                    min : 5
                }
            },
            messages: {
                type: {
                    required: "Please select transfer action",
                },
                amount: {
                    required: "Please enter amount",
                    min : "Amount value should be greater or equal than 5"
                },
            },
            errorElement: "p",
            errorPlacement: function ( error, element ) {
                if ( element.prop("tagName").toLowerCase() === "select" ) {
                    error.insertAfter( element.closest( ".form-group" ).find(".select2") );
                } else {
                    error.insertAfter( element );
                }
            },
            submitHandler: function () {
                var form = $('#transferForm');
                var type = $('#transferForm').find('[name="type"]').val();
                form.ajaxSubmit({
                    dataType:'json',
                    beforeSubmit:function(){
                        form.find('button:submit').button('loading');
                    },
                    complete: function () {
                        form.find('button:submit').button('reset');
                    },
                    success:function(data){
                        if(data.status == "success"){
                            getbalance();
                            form.closest('.modal').modal('hide');
                            notify("Fund "+type+" Successfull", 'success');
                            $('#datatable').dataTable().api().ajax.reload();
                        }else{
                            notify(data.status , 'warning');
                        }
                    },
                    error: function(errors) {
                        showError(errors, form);
                    }
                });
            }
        });
        
        
      $(document).on('click', '#user-dropdown ul li', function() {
        var selectedUser = $(this).text();
        var selectedUserIdVal = $(this).attr('data-id');
        $('#user-search').val(selectedUser);
        $('#selectedUserId').val(selectedUserIdVal);
        $('#user-dropdown').hide();
      }); 
        
    });

    function searchUsers() {
      var searchQuery = $('#user-search').val();
        if (searchQuery.length >= 10) {
          $.ajax({
            type: 'POST',
            url: "{{route('getUserviafilter')}}", // Replace with your server-side script URL
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: { searchquery: searchQuery },
            success: function(response) {
              $('#user-dropdown').html(response);
              $('#user-dropdown').show();
              // Show/hide the dropdown based on search query
              /*if (searchQuery === '') {
                $('#user-dropdown').hide();
              } else {
                $('#user-dropdown').show();
              }*/
            }
          });
        }else{
           $('#user-dropdown').html('');
        }  
    }



    function transfer(id){
        $('#transferForm').find('[name="user_id"]').val(id);
        $('#transferModal').modal();
    }
</script>
@endpush