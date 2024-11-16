@extends('layouts.app')
@section('title', 'Banner List')
@section('pagetitle', 'Banner List')
@php
$table = "yes";
$agentfilter = "hide";
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
                            <i class="icon-plus2"></i>Add Banner
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
                                    <th>Image</th>
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
                <div class="companyprofilelogo text-center">
                    <form class="dropzone" id="slideupload" action="{{route('bannerstore')}}" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <input type="text" name="title" value="Title" class="form-control" placeholder="Enter Title" />
                        <br />
                        <br />
                        <p>Info - Image size should be 1280*720 for better view.</p>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script type="text/javascript">
    $(document).ready(function() {
        var url = "{{url('statement/fetch')}}/banner/0";

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

                    return `<a href="{{asset('/banner/')}}/` + full.slides + `" target="_blank"><img src="{{asset('/banner/')}}/` + full.slides + `" width="100px" height="50px"></a>`;
                }
            },
            {
                "data": "action",
                render: function(data, type, full, meta) {
                    return `<button type="button" class="btn btn-primary" onclick="deleteSlide('` + full.id + `')"> Status Change</button>`;
                }
            }
        ];
        datatableSetup(url, options, onDraw);

        Dropzone.options.slideupload = {
            paramName: "slides", // The name that will be used to transfer the file
            maxFilesize: 10, // MB
            acceptedFiles: ".jpeg,.jpg,.png",

            complete: function(file) {
                this.removeFile(file);
                $('#frontslideModal').modal('hide');
            },

            success: function(file, data) {
                $('#datatable').dataTable().api().ajax.reload();
                $('#frontslideModal').modal('hide');
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
                    'type': 'banner'
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
                notify("Banner status changed successfully", 'success');
            })
            .fail(function() {
                swal.close();
                notify('Somthing went wrong', 'warning');
            });
    }
</script>
@endpush