@extends('layouts.app')
@section('title', 'Investment List')
@section('pagetitle', 'Investment List')
@php
$table = "yes";
$agentfilter = "hide";
@endphp

@section('content')

<div class="content">
    <div class="row">
        <div class="col-sm-12">
            <div class="iq-card">
                <div class="iq-card-header d-flex justify-content-between">
                    <div class="iq-header-title">

                    </div>
                    <div>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#frontslideModal">
                            <i class="icon-plus2"></i>Add Investment
                        </button>
                    </div>
                </div>
                <div class="iq-card-body">
                    <div class="table-responsive">
                        <table class="table" id="datatable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Banner</th>
                                    <th>Start Date </th>
                                    <th>End Date </th>
                                    <th>Mature Amount </th>
                                    <th>Maturity Amount </th>
                                    <th>Amount </th>
                                    <th>Status </th>
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

<div class="modal fade" id="frontslideModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Investment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{route('investmentStore')}}" method="post" id="walletLoadForms">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>Banner:</label>
                            <select class="form-control" name="banner_id" id="banner_id">
                                @foreach($investmentData['banner'] as $val)
                                <option value="{{$val->id}}">{{$val->title}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Video:</label>
                            <select class="form-control" name="video_id" id="video_id">
                                @foreach($investmentData['video'] as $val)
                                <option value="{{$val->id}}">{{$val->title}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Title:</label>
                            <input type="text" name="title" class="form-control" placeholder="Enter title" />
                        </div>

                        <div class="form-group col-md-6">
                            <label>From:</label>
                            <input type="datetime-local" name="start_date" class="form-control" placeholder="Enter start date" />
                        </div>


                        <div class="form-group col-md-6">
                            <label>To:</label>
                            <input type="datetime-local" name="end_date" class="form-control" placeholder="Enter end date" />
                        </div>
                        <div class="form-group col-md-6">
                            <label>Mature Amount:</label>
                            <input type="number" name="mature_amount" id="mature_amount" class="form-control" placeholder="Enter mature amount" />
                        </div>
                        {{-- onchange="calculateAmount() --}}
                        {{-- onchange="calculateAmount()" --}}

                        {{-- <div class="form-group col-md-6"> --}}
                            {{-- <label>Number of shares:</label> --}}
                            <input type="hidden" name="totalNoOfShare" id="totalNoOfShare" class="form-control" placeholder="Enter total number of shares" value="0" />
                        {{-- </div> --}}


                        <div class="form-group col-md-6">
                            <label>Maturity At:</label>
                            <input type="date" name="maturity_at" class="form-control" placeholder="Enter maturity at" />
                        </div>
                        <div class="form-group col-md-6">
                            <label>Amount:</label>
                            <input type="number" name="amount" class="form-control" placeholder="Enter amount"/>
                        </div>

                    </div>
                    <button type="button" onclick="invesmentFun()" class="btn btn-primary">Add</button>

                </form>

            </div>

        </div>
    </div>
</div>
@endsection

@push('script')

<script type="text/javascript">
    function calculateAmount() {
        let mature_amount = $('#mature_amount').val()
        let numberOfShare = $('#totalNoOfShare').val()
        const totAmt =  (mature_amount/numberOfShare).toFixed(2)
        $('#walletLoadForms').find('[name="amount"]').val(totAmt);
    }

    function invesmentFun() {
        var form = $('#walletLoadForms');
        form.ajaxSubmit({
            dataType: 'json',
            data: form.serialize(),

            beforeSubmit: function() {
                form.find('button:submit').button('loading');
            },
            complete: function() {
                form.find('button:submit').button('reset');
            },
            success: function(data) {
                if (data.status) {
                    form[0].reset();
                    form.closest('.modal').modal('hide');
                    notify("Invesment added successfully", 'success');
                    $('#datatable').dataTable().api().ajax.reload();
                } else {
                    notify(data.status, 'warning');
                }
            },
            error: function(errors) {
                showError(errors, form);
            }
        });
    }

    $(document).ready(function() {
        var url = "{{url('statement/fetch')}}/investment/0";

        var onDraw = function() {

        };

        var options = [{
                "data": "id"
            },

            {
                "data": "action",
                "className": "text-center",
                render: function(data, type, full, meta) {
                    if (full.banner != null)
                        return `<a href="{{asset('/banner/')}}/` + full.banner.slides + `" target="_blank"><img src="{{asset('/banner/')}}/` + full.banner.slides + `" width="100px" height="50px"></a>`;
                    else
                        return `Banner Deleted`;
                }
            },
            {
                "data": "start_date"
            },
            {
                "data": "end_date"
            },
            {
                "data": "mature_amount"
            },
            {
                "data": "maturity_at"
            },
            {
                "data": "amount"
            },
            {
                "data": "status",
                render: function(data, type, full, meta) {
                    if (full.status == 'inactive') {
                        return `<span class="badge badge-danger">Inactive</span>`;
                    } else {
                        return `<span class="badge badge-success">Active</span>`;
                    }

                }
            },
            {
                "data": "action",
                render: function(data, type, full, meta) {
                    return `<button type="button" class="btn btn-primary btn-sm" onclick="statusChange('` + full.id + `')"> Status Change</button>`;
                }
            }
        ];
        datatableSetup(url, options, onDraw);


    });

    function statusChange(id) {
        $.ajax({
                url: '{{route("statementDelete")}}',
                type: 'post',
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    "slide": id,
                    'type': 'invesment_status'
                },
                beforeSend: function() {
                    swal({
                        title: 'Wait!',
                        text: 'Please wait, we are status change',
                        onOpen: () => {
                            swal.showLoading()
                        },
                        allowOutsideClick: () => !swal.isLoading()
                    });
                }
            })
            .success(function(data) {
                swal.close();
                $('#datatable').dataTable().api().ajax.reload();
                notify("Status changed Successfully", 'success');
            })
            .fail(function() {
                swal.close();
                notify('Somthing went wrong', 'warning');
            });
    }
</script>
@endpush