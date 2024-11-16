@extends('layouts.app')
@section('title', "Company Profile")
@section('pagetitle', "Company Profile")
@section('bodyClass', "has-detached-left")

<style>
    .dz-preview .dz-success-mark {
        display: none !important;
    }

    .dz-preview .dz-error-mark {
        display: none !important;
    }
</style>

@section('content')
<div class="row">

    <div class="col-sm-12 col-lg-12">
        <div class="iq-card">
            <div class="iq-card-header d-flex justify-content-between">
                <div class="iq-header-title">
                    <h4 class="card-title">Company Setting</h4>
                </div>
            </div>
            <div class="iq-card-body">

                <ul class="nav nav-pills nav-tabs mb-3" id="pills-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#profile" role="tab" aria-controls="pills-home" aria-selected="true">Company Details</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#logo" role="tab" aria-controls="pills-profile" aria-selected="false">Company Logo</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#news" role="tab" aria-controls="pills-contact" aria-selected="false">Company News</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#notice" role="tab" aria-controls="pills-contact" aria-selected="false">Company Notice</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#support" role="tab" aria-controls="pills-contact" aria-selected="false">Company Support Details</a>
                    </li>
                </ul>
                <div class="tab-content" id="pills-tabContent-2">
                    <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="pills-home-tab">
                        <form id="profileForm" action="{{route('resourceupdate')}}" method="post">
                            {{ csrf_field() }}
                            <input type="hidden" name="id" value="{{$company->id}}">
                            <input type="hidden" name="actiontype" value="company">
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label>Company Name</label>
                                    <input type="text" name="companyname" class="form-control" value="{{$company->companyname}}" required="" placeholder="Enter Value">
                                </div>
                                <div class="form-group  col-md-4">
                                    <label>Company Website</label>
                                    <input type="text" name="website" class="form-control" value="{{$company->website}}" required="" placeholder="Enter Value">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-8"></div>
                                <div class="col-sm-4">
                                    <button class="btn btn-primary pull-right" type="submit" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Updating...">Update Info</button>

                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="logo" role="tabpanel" aria-labelledby="pills-profile-tab">
                        <div class="companyprofilelogo text-center">
                            <form class="dropzone" id="logoupload" action="{{route('resourceupdate')}}" method="post" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <input type="hidden" name="actiontype" value="company">

                                <input type="hidden" name="id" value="{{$company->id}}">
                                <p>Note : Prefered image size is 260px * 56px</p>
                            </form>

                        </div>
                    </div>
                    <div class="tab-pane fade" id="news" role="tabpanel" aria-labelledby="pills-contact-tab">
                        <form id="newsForm" action="{{route('resourceupdate')}}" method="post">
                            {{ csrf_field() }}
                            <input type="hidden" name="id" value="{{$companydata->id ?? 'new'}}">
                            <input type="hidden" name="company_id" value="{{$company->id}}">
                            <input type="hidden" name="actiontype" value="companydata">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>News</label>
                                    <textarea name="news" class="form-control" cols="30" rows="3" placeholder="Enter News">{{$companydata->news ?? ""}}</textarea>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Bill Notice</label>
                                    <textarea name="billnotice" class="form-control" cols="30" rows="3" placeholder="Enter News">{{$companydata->billnotice ?? ""}}</textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-8"></div>
                                <div class="col-sm-4">
                                    <button class="btn btn-primary pull-right" type="submit" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Updating...">Update Info</button>

                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="notice" role="tabpanel" aria-labelledby="pills-contact-tab">
                        <form id="noticeForm" action="{{route('resourceupdate')}}" method="post">
                            {{ csrf_field() }}
                            <input type="hidden" name="id" value="{{$companydata->id ?? 'new'}}">
                            <input type="hidden" name="company_id" value="{{$company->id}}">
                            <input type="hidden" name="actiontype" value="companydata">
                            <input type="hidden" name="notice">

                            <div class="form-group summernote">
                                {!! nl2br($companydata->notice ?? '') !!}
                            </div>

                            <div class="row">
                                <div class="col-sm-8"></div>
                                <div class="col-sm-4">
                                    <button class="btn btn-primary pull-right" type="submit" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Updating...">Update Info</button>

                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="support" role="tabpanel" aria-labelledby="pills-contact-tab">
                        <form id="supportForm" action="{{route('resourceupdate')}}" method="post">
                            {{ csrf_field() }}
                            <input type="hidden" name="company_id" value="{{$company->id}}">
                            <input type="hidden" name="actiontype" value="companydata">

                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>Contact Number</label>
                                    <textarea name="number" class="form-control" cols="30" rows="3" placeholder="Enter Value" required="">{{$companydata->number ?? ""}}</textarea>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Contact Email</label>
                                    <textarea name="email" class="form-control" cols="30" rows="3" placeholder="Enter Value" required="">{{$companydata->email ?? ""}}</textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-8"></div>
                                <div class="col-sm-4">
                                    <button class="btn btn-primary pull-right" type="submit" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Updating...">Update Info</button>

                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('style')
<style>
    .dropzone {
        min-height: 127px;
    }

    .dropzone .dz-default.dz-message:before {
        font-size: 50px;
        top: 60px;
    }

    .dropzone .dz-default.dz-message span {
        font-size: 18px;
        margin-top: 100px;
    }
</style>
@endpush

@push('script')

<script type="text/javascript">
    $(document).ready(function() {
        $("#profileForm").validate({
            rules: {
                companyname: {
                    required: true,
                }
            },
            messages: {
                companyname: {
                    required: "Please enter name",
                }
            },
            errorElement: "p",
            errorPlacement: function(error, element) {
                if (element.prop("tagName").toLowerCase().toLowerCase() === "select") {
                    error.insertAfter(element.closest(".form-group").find(".select2"));
                } else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function() {
                var form = $('form#profileForm');
                form.find('span.text-danger').remove();
                $('form#profileForm').ajaxSubmit({
                    dataType: 'json',
                    beforeSubmit: function() {
                        form.find('button:submit').html('loading...').attr("disabled", true).addClass('btn-secondary');
                    },
                    complete: function() {},
                    success: function(data) {
                        if (data.status == "success") {
                            notify("Company Profile Successfully Updated", 'success');
                            form.find('button:submit').html('Update Info').attr("disabled", false).removeClass('btn-secondary');
                        } else {
                            notify(data.status, 'warning');
                        }
                    },
                    error: function(errors) {
                        showError(errors, form.find('.panel-body'));
                    }
                });
            }
        });

        $("#newsForm").validate({
            rules: {
                company_id: {
                    required: true,
                }
            },
            messages: {
                company_id: {
                    required: "Please enter id",
                }
            },
            errorElement: "p",
            errorPlacement: function(error, element) {
                if (element.prop("tagName").toLowerCase().toLowerCase() === "select") {
                    error.insertAfter(element.closest(".form-group").find(".select2"));
                } else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function() {
                var form = $('form#newsForm');
                form.find('span.text-danger').remove();
                form.ajaxSubmit({
                    dataType: 'json',
                    beforeSubmit: function() {
                        form.find('button:submit').html('loading...').attr("disabled", true).addClass('btn-secondary');
                    },
                    complete: function() {
                        form.find('button:submit').button('reset');
                    },
                    success: function(data) {
                        form.find('button:submit').html('Update Info').attr("disabled", false).removeClass('btn-secondary')
                        if (data.status == "success") {
                            notify("Company News Successfully Updated", 'success');
                        } else {
                            notify(data.status, 'warning');
                        }
                    },
                    error: function(errors) {
                        showError(errors, form.find('.panel-body'));
                    }
                });
            }
        });

        $("#supportForm").validate({
            rules: {
                number: {
                    required: true,
                },
                email: {
                    required: true,
                }
            },
            messages: {
                number: {
                    required: "Number value is required",
                },
                email: {
                    required: "Email value is required",
                }
            },
            errorElement: "p",
            errorPlacement: function(error, element) {
                if (element.prop("tagName").toLowerCase().toLowerCase() === "select") {
                    error.insertAfter(element.closest(".form-group").find(".select2"));
                } else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function() {
                var form = $('form#supportForm');
                form.find('span.text-danger').remove();
                form.ajaxSubmit({
                    dataType: 'json',
                    beforeSubmit: function() {
                        form.find('button:submit').html('loading...').attr("disabled", true).addClass('btn-secondary');
                    },
                    complete: function() {
                        form.find('button:submit').button('reset');
                    },
                    success: function(data) {
                        form.find('button:submit').html('Update Info').attr("disabled", false).removeClass('btn-secondary')
                        if (data.status == "success") {
                            notify("Company Support Details Successfully Updated", 'success');
                        } else {
                            notify(data.status, 'warning');
                        }
                    },
                    error: function(errors) {
                        showError(errors, form.find('.panel-body'));
                    }
                });
            }
        });

        $("#noticeForm").validate({
            rules: {
                news: {
                    required: true,
                }
            },
            messages: {
                news: {
                    required: "Please enter name",
                }
            },
            errorElement: "p",
            errorPlacement: function(error, element) {
                if (element.prop("tagName").toLowerCase().toLowerCase() === "select") {
                    error.insertAfter(element.closest(".form-group").find(".select2"));
                } else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function() {
                var form = $('form#noticeForm');
                $('input[name="notice"]').val($('.note-editable').html());
                form.find('span.text-danger').remove();
                form.ajaxSubmit({
                    dataType: 'json',
                    beforeSubmit: function() {
                        form.find('button:submit').html('loading...').attr("disabled", true).addClass('btn-secondary');
                    },
                    complete: function() {
                        form.find('button:submit').button('reset');
                    },
                    success: function(data) {
                        form.find('button:submit').html('Update Info').attr("disabled", false).removeClass('btn-secondary')
                        if (data.status == "success") {
                            notify("Company Notice Successfully Updated", 'success');
                        } else {
                            notify(data.status, 'warning');
                        }
                    },
                    error: function(errors) {
                        showError(errors, form.find('.panel-body'));
                    }
                });
            }
        });

        $('.summernote').summernote({
            height: 350, // set editor height
            minHeight: null, // set minimum height of editor
            maxHeight: null, // set maximum height of editor
            focus: false // set focus to editable area after initializing summernote
        });

        Dropzone.options.logoupload = {
            paramName: "logos", // The name that will be used to transfer the file
            maxFilesize: .5, // MB
            complete: function(file) {
                this.removeFile(file);
            },
            success: function(file, data) {
                if (data.status == "success") {
                    notify("Company Logo Successfully Uploaded", 'success');
                    location.reload();
                } else {
                    notify("Something went wrong, please try again.", 'warning');
                }
            }
        };
    });
</script>
@endpush