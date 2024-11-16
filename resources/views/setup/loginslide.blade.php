@extends('layouts.app')
@section('title', 'Login Slider List')
@section('pagetitle', 'Login Slider List')
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
                            <i class="icon-plus2"></i>Add Banner
                        </button>
                    </div>
                </div>
                <div class="iq-card-body">
                    <div class="table-responsive">
                        <table class="table" id="datatable">
                              <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Page</th>
                                    <th>Slider</th>
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
                <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="dropzone" id="slideupload" action="{{route('setupupdate')}}" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <input type="hidden" name="code" value="slides">
                    <input type="hidden" name="actiontype" value="slides">
                </form>

                <p>Info - Image size should be 1280*720 for better view.</p>
            </div>
           
        </div>
    </div>
</div>
@endsection

@push('script')
{{-- <script type="text/javascript" src="{{asset('')}}assets/js/core/dropzone.js"></script> --}}
<script type="text/javascript">
    $(document).ready(function() {
        var url = "{{url('statement/fetch')}}/loginslide/0";

        var onDraw = function() {
            $('input#bankStatus').on('click', function(evt) {
                evt.stopPropagation();
                var ele = $(this);
                var id = $(this).val();
                var status = "0";
                if ($(this).prop('checked')) {
                    status = "1";
                }

                $.ajax({
                        url: `{{ route('setupupdate') }}`,
                        type: 'post',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: 'json',
                        data: {
                            'id': id,
                            'status': status,
                            "actiontype": "bank"
                        }
                    })
                    .done(function(data) {
                        if (data.status == "success") {
                            notify("Bank Account Updated", 'success');
                            $('#datatable').dataTable().api().ajax.reload();
                        } else {
                            if (status == "1") {
                                ele.prop('checked', false);
                            } else {
                                ele.prop('checked', true);
                            }
                            notify("Something went wrong, Try again.", 'warning');
                        }
                    })
                    .fail(function(errors) {
                        if (status == "1") {
                            ele.prop('checked', false);
                        } else {
                            ele.prop('checked', true);
                        }
                        showError(errors, "withoutform");
                    });
            });
        };

        var options = [{
                "data": "id"
            },
            {
                "data": "name"
            },
            {
                "data": "action",
                "className": "text-center",
                render: function(data, type, full, meta) {

                    return `<a href="{{asset('/')}}/` + full.value + `" target="_blank"><img src="{{asset('/')}}` + full.value + `" width="100px" height="50px"></a>`;
                }
            },
            {
                "data": "action",
                render: function(data, type, full, meta) {
                    return `<button type="button" class="btn btn-primary" onclick="deleteSlide('` + full.value + `')"> Delete</button>`;
                }
            }
        ];
        datatableSetup(url, options, onDraw);

        Dropzone.options.slideupload = {
            paramName: "slides", // The name that will be used to transfer the file
            maxFilesize: 1, // MB
            complete: function(file) {
                this.removeFile(file);
            },
            success: function(file, data) {
                console.log(file);
                if (data.status == "success") {
                    $('#datatable').dataTable().api().ajax.reload();
                    notify("Slide Successfully Uploaded", 'success');
                } else {
                    notify("Something went wrong, please try again.", 'warning');
                }
            }
        };
    });

    function deleteSlide(id) {
        $.ajax({
                url: '{{route("statementDelete")}}',
                type: 'post',
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    "slide": id,
                    'type': 'slide'
                },
                beforeSend: function() {
                    swal({
                        title: 'Wait!',
                        text: 'Please wait, we are deleting slides',
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
                notify("Slide Successfully Deleted", 'success');
            })
            .fail(function() {
                swal.close();
                notify('Somthing went wrong', 'warning');
            });
    }
</script>
@endpush