@extends('layouts.app')

@section('title', 'API Log List')

@section('pagetitle', 'API Log List')

@php
    
    $table = 'yes';
    
    // $agentfilter = "hide";
    
@endphp



@section('content')

    {{-- <div class="wrapper">

        <div class="content">

            <div class="row">

                <div class="col-sm-12">

                    <div class="panel panel-default">

                        <div class="panel-heading">

                            <h4 class="panel-title">API Log List</h4>

                            <div class="heading-elements">



                            </div>

                        </div>

                        <div class="panel-body">

                        </div>

                        <table class="table table-bordered table-striped table-hover table-responsive" id="datatable">

                            <thead>

                                <tr>

                                    <th>#</th>









                                </tr>

                            </thead>

                            <tbody>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>
    </div> --}}

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
                                        <th>Txnid</th>
                                        <th>Modal</th>
                                        <th>Url</th>
                                        <th style="width: 99px;">Header</th>
                                        <th>Request</th>
                                        <th>Response</th>
                                        
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



    <div id="setupModal" class="modal fade" data-backdrop="false" data-keyboard="false">

        <div class="modal-dialog">

            <div class="modal-content">

                <div class="modal-header">

                    <button type="button" class="close" data-dismiss="modal">&times;</button>

                    <h6 class="modal-title"><span class="msg">Add</span> Bank</h6>

                </div>

                <form id="setupManager" action="{{ route('setupupdate') }}" method="post">

                    <div class="modal-body">

                        <div class="row">

                            <input type="hidden" name="id">

                            <input type="hidden" name="actiontype" value="bank">

                            {{ csrf_field() }}

                            <div class="form-group col-md-6">

                                <label>Name</label>

                                <input type="text" name="name" class="form-control" placeholder="Enter Bank Name"
                                    required="">

                            </div>

                            <div class="form-group col-md-6">

                                <label>Account Number</label>

                                <input type="text" name="account" class="form-control" placeholder="Enter Account Number"
                                    required="">

                            </div>

                        </div>

                        <div class="row">

                            <div class="form-group col-md-6">

                                <label>Ifsc</label>

                                <input type="text" name="ifsc" class="form-control" placeholder="Enter Ifsc Code"
                                    required="">

                            </div>

                            <div class="form-group col-md-6">

                                <label>Branch</label>

                                <input type="text" name="branch" class="form-control" placeholder="Enter Branch"
                                    required="">

                            </div>

                        </div>

                    </div>

                    <div class="modal-footer">

                        <button type="button" class="btn btn-secondary" data-dismiss="modal"
                            aria-hidden="true">Close</button>

                        <button class="btn btn-primary" type="submit"
                            data-loading-text="<i class='fa fa-spin fa-spinner'></i> Submitting">Submit</button>

                    </div>

                </form>

            </div><!-- /.modal-content -->

        </div><!-- /.modal-dialog -->

    </div><!-- /.modal -->

@endsection



@push('script')
    <script type="text/javascript">
        $(document).ready(function() {

            var url = "{{ url('statement/fetch') }}/setup{{ $type }}/0";

            var onDraw = function() {

                $('.print').click(function(event) {

                    var data = DT.row($(this).parent().parent().parent().parent().parent()).data();

                    $.each(data, function(index, values) {

                        $("." + index).text(values);

                    });

                    $('#receipt').modal();

                });

            };



            var options = [

                {
                    "data": "name",

                    render: function(data, type, full, meta) {

                        return `<div>

                            <span class='text-inverse m-l-10'><b>` + full.id + `</b> </span>

                            <div class="clearfix"></div>

                        </div><span style='font-size:13px' class="pull=right">` + full.created_at + `</span>`;

                    }

                },

                {
                    "data": "txnid"
                },
                {
                    "data": "modal"
                },

                {
                    "data": "url"
                },

                {
                    "data": "header"
                },

                {
                    "data": "request"
                },

                {
                    "data": "response"
                },



            ];

            datatableSetup(url, options, onDraw);





        });
    </script>

    <style>
        .table>tbody>tr>td {

            text-transform: none !important;

        }
    </style>
@endpush
