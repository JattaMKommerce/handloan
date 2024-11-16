@extends('layouts.app')
@section('title', 'Video List')
@section('pagetitle', 'Video List')
@php
    $table = 'yes';
    $agentfilter = 'hide';
@endphp

<style>
    .dz-preview .dz-success-mark {
        display: none !important;
    }

    .dz-preview .dz-error-mark {
        display: none !important;
    }
</style>

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
                                <i class="icon-plus2"></i>Add Video
                            </button>
                        </div>
                    </div>
                    <div class="iq-card-body">
                        <div class="table-responsive">
                            <table class="table" id="datatable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Title</th>
                                        <th>Status</th>
                                        <th>Video</th>
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

    <div class="modal fade" id="frontslideModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form class="dropzone" id="slideupload" action="{{ route('storeVideo') }}" method="post"
                        enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <span id="messageShow" style="color:red; font-weight:bold"></span>
                        <input type="text" name="title" value="Title" class="form-control"
                            placeholder="Enter Title" />
                        <br />
                        <br />


                    </form>
                    <p>Info - Video size should be between 2-3 MB and format is MP4</p>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('script')
    {{-- <script type="text/javascript" src="{{ asset('') }}assets/js/core/dropzone.js"></script> --}}
    <script type="text/javascript">
        $(document).ready(function() {
            var url = "{{ url('statement/fetch') }}/video/0";

            var onDraw = function() {

            };

            var options = [{
                    "data": "id"
                },
                {
                    "data": "title"
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
                    "className": "text-center",
                    render: function(data, type, full, meta) {

                        return `<a href="{{ asset('/banner/') }}/` + full.slides +
                            `" target="_blank"><img src="{{ asset('/banner/') }}/video.jpg" width="100px" height="50px"></a>`;
                    }
                },
                {
                    "data": "action",
                    render: function(data, type, full, meta) {
                        return `<button type="button" class="btn btn-primary" onclick="deleteSlide('` + full
                            .id + `')"> Status Change</button>`;
                    }
                }
            ];
            datatableSetup(url, options, onDraw);

            Dropzone.options.slideupload = {
                paramName: "video", // The name that will be used to transfer the file
                maxFilesize: 10, // MB
                acceptedFiles: ".mp4,.gif",

                complete: function(file) {
                    this.removeFile(file);
                    $('#frontslideModal').modal('hide');
                    // location.reload();

                },
                success: function(file, data) {
                    $('#datatable').dataTable().api().ajax.reload();
                    $('#frontslideModal').modal('hide');
                    if (data.status == "success") {
                        notify("Slide Successfully Uploaded", 'success');
                        $('#datatable').dataTable().api().ajax.reload();
                    } else {
                        notify("Something went wrong, please try again.", 'warning');
                    }
                }
            };
        });

        function deleteSlide(id) {
            $.ajax({
                    url: '{{ route('statementDelete')}}',
                    type: 'post',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        "slide": id,
                        'type': 'video'
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
                    notify("Video status changed successfully", 'success');
                })
                .fail(function() {
                    swal.close();
                    notify('Somthing went wrong', 'warning');
                });
        }
    </script>
@endpush
