@extends('layouts.app')
@section('title', 'Runpaisa PG Request')
@section('pagetitle', 'Runpaisa PG Request')

@php
    $table = 'yes';
    
    $status['type'] = 'Fund';
    $status['data'] = [
        'success' => 'Success',
        'pending' => 'Pending',
        'failed' => 'Failed',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
    ];
    
@endphp

@section('content')
    <div class="content">

        {{-- <div class="row"> --}}
        <div class="row">
            <div class="col-sm-12">
                <div class="iq-card">
                    <div class="iq-card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            <h4 class="panel-title">Runpaisa PG Request</h4>
                        </div>
                        
                    </div>
                    <div class="iq-card-body">
                        <div class="table-responsive">
                            <div id="datatable_wrapper" class="dataTables_wrapper no-footer">
                                <div class="datatable-scroll">
                                    <table class="table dataTable no-footer" id="datatable" role="grid"
                                        aria-describedby="datatable_info" style="width: 1399px;">
                                        {{-- <table class="table table-bordered table-striped table-hover" id="datatable"> --}}
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>User Details</th>
                                                <th>Refrence Details</th>
                                                <th>Amount</th>
                                                <th>Remark</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                    </table>

                                </div>
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
                var url = "{{ url('statement/fetch') }}/fundrequest/0";
                var onDraw = function() {};
                var options = [{
                        "data": "name",
                        render: function(data, type, full, meta) {
                            return `<span class='text-inverse m-l-10'><b>` + full.id + `</b> </span><br>
                        <span style='font-size:13px'>` + full.updated_at + `</span>`;
                        }
                    },
                    {
                        "data": "username"
                    },
                    {
                        "data": "bank",
                        render: function(data, type, full, meta) {

                            return `Ref No. - ` + full.ref_no + `<br>Paymode - ` + full.paymode;
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
                            if (full.status == "success" || full.status == "approved") {
                                out += `<span class="badge badge-success">Approved</span>`;
                            } else if (full.status == "pending") {
                                out += `<span class="badge badge-warning">Pending</span>`;
                            } else if (full.status == "failed") {
                                out += `<span class="badge badge-danger">Rejected</span>`;
                            }

                            return out;
                        }
                    }
                ];

                datatableSetup(url, options, onDraw);

                $("#pgRequestForm").validate({
                    rules: {

                        amount: {
                            required: true
                        }

                    },
                    messages: {

                        amount: {
                            required: "Please enter request amount",
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
                        var form = $('#pgRequestForm');
                        form.ajaxSubmit({
                            dataType: 'json',
                            beforeSubmit: function() {
                                form.find('button:submit').button('loading');
                            },
                            complete: function() {
                                form.find('button:submit').button('reset');
                            },
                            success: function(data) {
                                console.log(data.data);
                                if (data.status == "TXN") {

                                    window.open(data.data, '_blank');

                                } else {
                                    notify(data.status, 'warning');
                                }
                            },
                            error: function(errors) {
                                showError(errors, form);
                            }
                        });
                    }
                });

                $("#linkRequestForm").validate({
                    rules: {

                        amount: {
                            required: true
                        }

                    },
                    messages: {

                        amount: {
                            required: "Please enter request amount",
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
                        var form = $('#linkRequestForm');
                        form.ajaxSubmit({
                            dataType: 'json',
                            beforeSubmit: function() {
                                form.find('button:submit').button('loading');
                            },
                            complete: function() {
                                form.find('button:submit').button('reset');
                            },
                            success: function(data) {
                                console.log(data.data);
                                if (data.status == "TXN") {

                                    // window.open(data.data,'_blank');
                                    // swal({
                                    //   title: "Link Genrated</small>",
                                    //   text:   "<p>" +data.data + "</p>",
                                    //   html: true
                                    // });
                                    myFunction(data.data)
                                    swal({
                                        title: "Link Genrated",
                                        text: "A custom <span style='color:#F8BB86'>html<span> message.",
                                        html: "<span id='myInput'>" + data.data +
                                            "</span> <button><i class='fa fa-copy'></i></button>"
                                    });
                                } else {
                                    notify(data.status, 'warning');
                                }
                            },
                            error: function(errors) {
                                showError(errors, form);
                            }
                        });
                    }
                });

            });


            function fundRequest(id = "none") {
                if (id != "none") {
                    $('#fundRequestForm').find('[name="fundbank_id"]').select2().val(id).trigger('change');
                }
                $('#fundRequestModal').modal();
            }

            function myFunction(copyText) {

                navigator.clipboard.writeText(copyText);

            }
        </script>
    @endpush
