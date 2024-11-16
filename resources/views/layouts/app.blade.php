<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - {{Auth::user()->company->companyname}}</title>

    <!-- Global stylesheets -->
    <!-- <link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
    <link href="{{asset('')}}assets/css/icons/icomoon/styles.css" rel="stylesheet" type="text/css">
    <link href="{{asset('')}}assets/css/icons/fontawesome/styles.min.css" rel="stylesheet" type="text/css">
    <link href="{{asset('')}}assets/css/bootstrap.css" rel="stylesheet" type="text/css">
    <link href="{{asset('')}}assets/css/core.css" rel="stylesheet" type="text/css">
    <link href="{{asset('')}}assets/css/components.css" rel="stylesheet" type="text/css">

    <link href="{{asset('')}}assets/css/colors.css" rel="stylesheet" type="text/css">
    <link href="{{asset('')}}assets/css/snackbar.css" rel="stylesheet"> -->

    <!-- CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/simple-notify@0.5.5/dist/simple-notify.min.css" />

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/simple-notify@0.5.5/dist/simple-notify.min.js"></script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
    <link rel="stylesheet" href="{{asset('')}}theme/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{asset('')}}theme/css/typography.css">
    <link rel="stylesheet" href="{{asset('')}}theme/css/style.css">
    <link rel="stylesheet" href="{{asset('')}}theme/css/responsive.css">
    <link rel='stylesheet' href="{{asset('')}}theme/fullcalendar/core/main.css" />
    <link href="{{asset('')}}theme/fullcalendar/daygrid/main.css" rel='stylesheet' />
    <link href="{{asset('')}}theme/fullcalendar/timegrid/main.css" rel='stylesheet' />
    <link href="{{asset('')}}theme/fullcalendar/list/main.css" rel='stylesheet' />
    <link rel="stylesheet" href="{{asset('')}}theme/css/flatpickr.min.css">
    <link href="{{asset('')}}assets/css/components.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.min.css" integrity="sha512-34s5cpvaNG3BknEWSuOncX28vz97bRI59UnVtEEpFX536A7BtZSJHsDyFoCl8S7Dt2TPzcrCEoHBGeM4SUBDBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
    <link href="{{asset('style.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('custom.css')}}" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/snackbarjs/1.1.0/snackbar.css" integrity="sha512-X3jcmfsWau6LnAjqe0EJhFnyEtT3OJtWg9D/x4iroC6x8XarBaWTS5mSKwxd697eyLV0w8f29PPirMcsw4xE4Q==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    @stack('style')
    <!-- Core JS files -->
    <!-- <script type="text/javascript" src="{{asset('')}}assets/js/plugins/loaders/pace.min.js"></script>
    <script type="text/javascript" src="{{asset('')}}assets/js/core/libraries/jquery.min.js"></script>
    <script type="text/javascript" src="{{asset('')}}assets/js/core/libraries/bootstrap.min.js"></script>
    <script type="text/javascript" src="{{asset('')}}assets/js/plugins/loaders/blockui.min.js"></script>
    <script type="text/javascript" src="{{asset('')}}assets/js/core/jquery.validate.min.js"></script>
    <script type="text/javascript" src="{{asset('')}}assets/js/core/jquery.form.min.js"></script>
    <script type="text/javascript" src="{{asset('')}}assets/js/core/sweetalert2.min.js"></script>
    <script type="text/javascript" src="{{asset('')}}assets/js/plugins/forms/selects/select2.min.js"></script>
    <script src="{{asset('')}}/assets/js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>-->
    <script src="{{asset('')}}assets/js/core/snackbar.js"></script>

    <script src="{{asset('')}}theme/js/jquery.min.js"></script>
    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.20/dist/sweetalert2.all.min.js"></script>

    <script src="{{asset('')}}theme/js/popper.min.js"></script>
    <script src="{{asset('')}}theme/js/bootstrap.min.js"></script>
    <script src="{{asset('')}}theme/js/jquery.appear.js"></script>
    <script src="{{asset('')}}theme/js/countdown.min.js"></script>
    <script src="{{asset('')}}theme/js/waypoints.min.js"></script>
    <script src="{{asset('')}}theme/js/jquery.counterup.min.js"></script>
    <script src="{{asset('')}}theme/js/wow.min.js"></script>
    <script src="{{asset('')}}theme/js/apexcharts.js"></script>
    <script src="{{asset('')}}theme/js/lottie.js"></script>
    <script src="{{asset('')}}theme/js/slick.min.js"></script>
    <script src="{{asset('')}}theme/js/select2.min.js"></script>
    <script src="{{asset('')}}theme/js/owl.carousel.min.js"></script>
    <script src="{{asset('')}}theme/js/jquery.magnific-popup.min.js"></script>
    <script src="{{asset('')}}theme/js/smooth-scrollbar.js"></script>
    <script src="{{asset('')}}theme/js/style-customizer.js"></script>
    <script src="{{asset('')}}theme/js/core.js"></script>
    <script src="{{asset('')}}theme/js/chart-custom.js"></script>
    <script src="{{asset('')}}theme/js/custom.js"></script>
    <script src="{{asset('')}}assets/js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
    <script type="text/javascript" src="{{asset('')}}assets/js/core/jquery.validate.min.js"></script>
    <script type="text/javascript" src="{{asset('')}}assets/js/core/jquery.form.min.js"></script>
    <script type="text/javascript" src="{{asset('')}}assets/js/core/sweetalert2.min.js"></script>
    <script type="text/javascript" src="{{asset('')}}assets/js/plugins/forms/selects/select2.min.js"></script>

    <!-- <script src="{{asset('')}}assets/js/core/snackbar.js"></script> -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/snackbarjs/1.1.0/snackbar.min.js" integrity="sha512-Knad2JJvcddGNKm03ySDgeTKXQfBjH0XPrFqUyzM0BuJhKkVfLzoOK5Ii9jsLmbZTXM8YNqn42suHyIEbQXboQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

    @if (isset($table) && $table == "yes")
    <script type="text/javascript" src="{{asset('')}}assets/js/plugins/tables/datatables/datatables.min.js"></script>
    @endif

    <script type="text/javascript" src="{{asset('')}}assets/js/core/app.js"></script>
    <script type="text/javascript" src="{{asset('')}}assets/js/core/dropzone.js"></script>
    <script src="{{ asset('/assets/js/core/jQuery.print.js') }}"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/38.1.1/classic/ckeditor.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            $('.select').select2();

            $('#profileImg').hover(function() {
                $('span.changePic').show('400');
            });

            $('.changePic').hover(function() {
                $('span.changePic').show('400');
            }, function() {
                $('span.changePic').hide('400');
            });

            $(document).ready(function() {
                $(".sidebar-default a").each(function() {
                    if (this.href == window.location.href) {
                        $(this).addClass("active");
                        $(this).parent().addClass("active");
                        $(this).parent().parent().prev().addClass("active");
                        $(this).parent().parent().prev().click();
                    }
                });
            });

            $('#reportExport').click(function() {
                var type = $(this).attr('product');
                var fromdate = $('#searchForm').find('input[name="from_date"]').val();
                var todate = $('#searchForm').find('input[name="to_date"]').val();
                var searchtext = $('#searchForm').find('input[name="searchtext"]').val();
                var agent = $('#searchForm').find('input[name="agent"]').val();
                var status = $('#searchForm').find('[name="status"]').val();
                var product = $('#searchForm').find('[name="product"]').val();

                @if(isset($id))
                agent = "{{$id}}";
                @endif

                window.location.href = "{{ url('statement/export') }}/" + type + "?fromdate=" + fromdate + "&todate=" + todate + "&searchtext=" + searchtext + "&agent=" + agent + "&status=" + status + "&product=" + product;
            });

            Dropzone.options.profileupload = {
                paramName: "profiles", // The name that will be used to transfer the file
                maxFilesize: .5, // MB
                complete: function(file) {
                    this.removeFile(file);
                },
                success: function(file, data) {
                    console.log(file);
                    if (data.status == "success") {
                        $('#profileImg').removeAttr('src');
                        $('#profileImg').attr('src', file.dataURL);
                        notify("Profile Successfully Uploaded", 'success');
                    } else {
                        notify("Something went wrong, please try again.", 'warning');
                    }
                }
            };

            $('.mydate').datepicker({
                'autoclose': true,
                'clearBtn': true,
                'todayHighlight': true,
                'format': 'yyyy-mm-dd'
            });

            $('input[name="from_date"]').datepicker("setDate", new Date());
            $('input[name="to_date"]').datepicker('setStartDate', new Date());

            $('input[name="to_date"]').focus(function() {
                if ($('input[name="from_date"]').val().length == 0) {
                    $('input[name="to_date"]').datepicker('hide');
                    $('input[name="from_date"]').focus();
                }
            });

            $('input[name="from_date"]').datepicker().on('changeDate', function(e) {
                $('input[name="to_date"]').datepicker('setStartDate', $('input[name="from_date"]').val());
                $('input[name="to_date"]').datepicker('setDate', $('input[name="from_date"]').val());
            });

            $('form#searchForm').submit(function() {
                var fromdate = $(this).find('input[name="from_date"]').val();
                var todate = $(this).find('input[name="to_date"]').val();
                if (fromdate.length != 0 || todate.length != 0) {
                    $('#datatable').dataTable().api().ajax.reload();
                }
                return false;
            });

            $('#formReset').click(function() {
                $('form#searchForm')[0].reset();
                $('form#searchForm').find('[name="from_date"]').datepicker().datepicker("setDate", new Date());
                $('form#searchForm').find('[name="to_date"]').datepicker().datepicker("setDate", null);
                $('form#searchForm').find('select').val(null).trigger('change')
                $('#formReset').find('button[type="submit"]').html('loading').attr('disabled',true).addClass('btn-secondary');
                $('#datatable').dataTable().api().ajax.reload();
            });

            $(".navigation-menu a").each(function() {
                alert();
                // if (this.href == window.location.href) {
                //     alert();
                //     $(this).parent().addClass("active");
                //     $(this).parent().parent().parent().addClass("active");
                // }
            });

            $('select').change(function(event) {
                var ele = $(this);
                if (ele.val() != '') {
                    $(this).closest('div.form-group').find('p.error').remove();
                }
            });

            $("#editForm").validate({
                rules: {
                    status: {
                        required: true,
                    },
                    txnid: {
                        required: true,
                    },
                    payid: {
                        required: true,
                    },
                    refno: {
                        required: true,
                    }
                },
                messages: {
                    name: {
                        required: "Please select status",
                    },
                    txnid: {
                        required: "Please enter txn id",
                    },
                    payid: {
                        required: "Please enter payid",
                    },
                    refno: {
                        required: "Please enter ref no",
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
                    var form = $('#editForm');
                    var id = form.find('[name="id"]').val();
                    form.ajaxSubmit({
                        dataType: 'json',
                        beforeSubmit: function() {
                            form.find('button[type="submit"]').html('loading..').attr('disabled',true).addClass('btn-secondary');
                        },
                        success: function(data) {
                            if (data.status == "success" || data.statuscode == "TXN") {
                                form.find('button[type="submit"]').html('Submit').attr('disabled',false).removeClass('btn-secondary');
                                notify("Task Successfully Completed", 'success');
                                $('#datatable').dataTable().api().ajax.reload();
                                $('#editModal').modal('hide');
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

            setTimeout(function() {
                sessionOut();
            }, "{{$mydata['sessionOut']}}");

            $(".modal").on('hidden.bs.modal', function() {
                if ($(this).find('form').length) {
                    $(this).find('form')[0].reset();
                }

                if ($(this).find('.select').length) {
                    $(this).find('.select').val(null).trigger('change');
                }
            });

            $("#walletLoadForm").validate({
                rules: {
                    amount: {
                        required: true,
                    }
                },
                messages: {
                    amount: {
                        required: "Please enter amount",
                    },
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
                    var form = $('#walletLoadForm');
                    form.ajaxSubmit({
                        dataType: 'json',
                        beforeSubmit: function() {
                            form.find('button:submit').html('loading..').attr('disabled',true).addClass('btn-secondary');
                        },
                        complete: function() {
                            form.find('button:submit').html('Submit').attr('disabled',false).removeClass('btn-secondary');
                        },
                        success: function(data) {
                            if (data.status) {
                                getbalance();
                                form.closest('.modal').modal('hide');
                                notify("Wallet successfully loaded", 'success');
                                $('#walletLoadModal').modal('hide');
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

            $("#notifyForm").validate({
                rules: {
                    amount: {
                        required: true,
                    }
                },
                messages: {
                    amount: {
                        required: "Please enter amount",
                    },
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
                    var form = $('#notifyForm');
                    form.ajaxSubmit({
                        dataType: 'json',
                        beforeSubmit: function() {
                            form.find('button:submit').html('loading..').attr('disabled',true).addClass('btn-secondary');
                        },
                        complete: function() {
                            form.find('button:submit').html('Submit').attr('disabled',false).removeClass('btn-secondary');
                        },
                        success: function(data) {
                            if (data.status == "success") {
                                getbalance();
                                form.closest('.modal').modal('hide');
                                notify("Send successfully", 'success');
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

            $("#complaintForm").validate({
                rules: {
                    subject: {
                        required: true,
                    },
                    description: {
                        required: true,
                    }
                },
                messages: {
                    subject: {
                        required: "Please select subject",
                    },
                    description: {
                        required: "Please enter your description",
                    },
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
                    var form = $('#complaintForm');
                    form.ajaxSubmit({
                        dataType: 'json',
                        beforeSubmit: function() {
                            form.find('button:submit').html('loading..').attr('disabled',true).addClass('btn-secondary');
                        },
                        complete: function() {
                            form.find('button:submit').html('Submit').attr('disabled',false).removeClass('btn-secondary');
                        },
                        success: function(data) {
                            if (data.status) {
                                form[0].reset();
                                form.closest('.modal').modal('hide');
                                notify("Complaint successfully submitted", 'success');
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

            // $(window).load(function() {
            //     alert('jdkk')
            //     getbalance();
            // });

            // if(typeof(EventSource) !== "undefined") {
            //     var source = new EventSource("{{url('mydata')}}");
            //     source.onmessage = function(event) {
            //         var data = jQuery.parseJSON(event.data);
            //         $('.apibalance').text(data.apibalance);
            //         $('.downlinebalance').text(data.downlinebalance);
            //         $('.fundCount').text(data.fundrequest);
            //         $('.aepsfundCount').text(data.aepsfundrequest);
            //         $('.utiidCount').text(data.utiid);

            //         $('.aeps').text(data.aeps);
            //         $('.utipan').text(data.utipan);
            //         $('.billpay').text(data.billpay);
            //         $('.money').text(data.money);
            //         $('.recharge').text(data.recharge);

            //         $('.transactionCount').text(data.aeps + data.money + data.money + data.utipan + data.recharge);

            //         $('.member').text(data.member);
            //     };                
            // }
        });

        function getbalance() {
            $.ajax({
                url: "{{route('getbalance')}}",
                type: "GET",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function(result) {

                    $.each(result, function(index, value) {

                        $('.' + index).text(value);
                    });
                    $('#mainwallet').text('₹ '+result.mainwallet);
                }
            });

            $.ajax({
                url: "{{url('mydata')}}",
                type: "GET",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    $('.fundCount').text(data.fundrequest);
                    $('.aepsrequestfundCount').text(data.aepsfundrequest);
                    $('.member').text(data.member);
                    $('.aepspayoutfundCount').text(data.aepspayoutrequest);
                    $('.aepsfundCount').text(data.aepsfundrequest + data.aepspayoutrequest);
                   
                }
            });
        }

        // $(document).ready(function(){
        getbalance();
        // });


        @if(isset($table) && $table == "yes")

        function datatableSetup(urls, datas, onDraw = function() {}, ele = "#datatable", element = {}) {
            var options = {
                dom: '<"datatable-scroll"t><"datatable-footer"ip>',
                dom: 'Bfrtip',
                processing: true,
                serverSide: true,
                ordering: false,
                stateSave: true,
                pageLength: 50,
                iDisplayLength: 50,
                columnDefs: [{
                    orderable: false,
                    width: '130px',
                    targets: [0]
                }],
                language: {
                    paginate: {
                        'first': 'First',
                        'last': 'Last',
                        'next': '&rarr;',
                        'previous': '&larr;'
                    }
                },
                lengthMenu: [ [100, 150, 250, -1], [100, 150, 250, "All"] ],
                drawCallback: function() {
                    $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup');
                },
                preDrawCallback: function() {
                    $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').removeClass('dropup');
                },
                ajax: {
                    url: urls,
                    type: "post",
                    data: function(d) {
                        d._token = $('meta[name="csrf-token"]').attr('content');
                        d.fromdate = $('#searchForm').find('[name="from_date"]').val();
                        d.todate = $('#searchForm').find('[name="to_date"]').val();
                        d.searchtext = $('#searchForm').find('[name="searchtext"]').val();
                        d.agent = $('#searchForm').find('[name="agent"]').val();
                        d.status = $('#searchForm').find('[name="status"]').val();
                        d.product = $('#searchForm').find('[name="product"]').val();
                    },
                    beforeSend: function() {
                        
                        $('#searchForm').find('button:submit').html('Loading..').attr("disabled", true).addClass('btn btn-secondary');
                    },
                    complete: function() {
                        $('#searchForm').find('button:submit').html('Submit').attr('disabled',false).removeClass('btn-secondary');
                        $('#formReset').find('button:submit').html('Submit').attr('disabled',false).removeClass('btn-secondary');
                        $('#searchForm').find('button:submit').html('Search').attr("disabled", false).removeClass('btn-secondary');
                    },
                    error: function(response) {}
                },
                columns: datas
            };

            $.each(element, function(index, val) {
                options[index] = val;
            });

            var DT = $(ele).DataTable(options).on('draw.dt', onDraw);

            return DT;
        }
        @endif

        // function notify(msg, type = "success", notitype = "popup", element = "none") {
        //     if (notitype == "popup") {
        //         let snackbar = new SnackBar;
        //         snackbar.make("message", [
        //             msg,
        //             null,
        //             "bottom",
        //             "right",
        //             "text-" + type
        //         ], 5000);
        //     } else {
        //         element.find('div.alert').remove();
        //         element.prepend(`<div class="alert bg-` + type + ` alert-styled-left">
        //             <button type="button" class="close" data-dismiss="alert"><span></span><span class="sr-only">Close</span></button> ` + msg + `
        //         </div>`);

        //         setTimeout(function() {
        //             element.find('div.alert').remove();
        //         }, 5000);
        //     }
        // }

        function showError(errors, form = "withoutform") {
            if (form != "withoutform") {
                form.find('button[type="submit"]').html('Submit').attr('disabled',false).removeClass('btn-secondary');
                $('p.error').remove();
                $('div.alert').remove();
                if (errors.status == 422) {
                    $.each(errors.responseJSON.errors, function(index, value) {
                        form.find('[name="' + index + '"]').closest('div.form-group').append('<p class="error">' + value + '</span>');
                    });
                    form.find('p.error').first().closest('.form-group').find('input').focus();
                    setTimeout(function() {
                        form.find('p.error').remove();
                    }, 5000);
                } else if (errors.status == 400) {
                    if (errors.responseJSON.message) {
                        form.prepend(`<div class="alert bg-danger alert-styled-left">
                            <button type="button" class="close" data-dismiss="alert"><span></span><span class="sr-only">Close</span></button>
                            <span class="text-semibold">Oops !</span> ` + errors.responseJSON.message + `
                        </div>`);
                    } else {
                        form.prepend(`<div class="alert bg-danger alert-styled-left">
                            <button type="button" class="close" data-dismiss="alert"><span></span><span class="sr-only">Close</span></button>
                            <span class="text-semibold">Oops !</span> ` + errors.responseJSON.status + `
                        </div>`);
                    }

                    setTimeout(function() {
                        form.find('div.alert').remove();
                    }, 10000);
                } else {
                    notify(errors.statusText, 'warning');
                }
            } else {
                if (errors.responseJSON.message) {
                    notify(errors.responseJSON.message, 'warning');
                } else {
                    notify(errors.responseJSON.status, 'warning');
                }
            }
        }

        function sessionOut() {
            window.location.href = "{{route('logout')}}";
        }

        function status(id, type) {
            $.ajax({
                    url: `{{route('statementStatus')}}`,
                    type: 'post',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    beforeSend: function() {
                        swal({
                            title: 'Wait!',
                            text: 'Please wait, we are fetching transaction details',
                            onOpen: () => {
                                swal.showLoading()
                            },
                            allowOutsideClick: () => !swal.isLoading()
                        });
                    },
                    data: {
                        'id': id,
                        "type": type
                    }
                })
                .done(function(data) {
                    if (data.status == "success") {
                        if (data.refno) {
                            var refno = "Operator Refrence is " + data.refno
                        } else {
                            var refno = data.remark;
                        }
                        swal({
                            type: 'success',
                            title: data.status,
                            text: refno,
                            onClose: () => {
                                $('#datatable').dataTable().api().ajax.reload();
                            },
                        });
                    } else {
                        swal({
                            type: 'success',
                            title: data.status,
                            text: "Transaction status is " + data.status,
                            onClose: () => {
                                $('#datatable').dataTable().api().ajax.reload();
                            },
                        });
                    }
                })
                .fail(function(errors) {
                    swal.close();
                    showError(errors, "withoutform");
                });
        }

        function editReport(id, refno, txnid, payid, remark, status, actiontype) {
            $('#editModal').find('[name="id"]').val(id);
            $('#editModal').find('[name="status"]').val(status).trigger('change');
            $('#editModal').find('[name="refno"]').val(refno);
            $('#editModal').find('[name="txnid"]').val(txnid);
            if (actiontype == "billpay") {
                $('#editModal').find('[name="payid"]').closest('div.form-group').remove();
            } else {
                $('#editModal').find('[name="payid"]').val(payid);
            }
            $('#editModal').find('[name="remark"]').val(remark);
            $('#editModal').find('[name="actiontype"]').val(actiontype);
            $('#editModal').modal('show');
        }

        function complaint(id, product) {
            $('#complaintModal').find('[name="transaction_id"]').val(id);
            $('#complaintModal').find('[name="product"]').val(product);
            $('#complaintModal').modal('show');
        }

        function notify(text, status) {
            new Notify({
                status: status,
                title: null,
                text: text,
                effect: 'fade',
                customClass: null,
                customIcon: null,
                showIcon: true,
                showCloseButton: true,
                autoclose: true,
                autotimeout: 2000,
                gap: 20,
                distance: 15,
                type: 1,
                position: 'right top'
            })
        }
    </script>
    @stack('script')
</head>

<?php
$themeStyle = 'vertical'; //'vertical','horizontal';
?>


<body class="iq-page-menu-{{$themeStyle}}">

    @if($themeStyle === 'horizontal')
    <div class="wrapper">
        <div class="page-container">
            <div class="page-content">
                @include('layouts.topbar')

                @include('layouts.sidebar')

                <div id="content-page" class="content-page">
                    <div class="container-fluid">

                        @include('layouts.pageheader')
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </div>

    @else

    <div class="page-container">

        <div class="page-content">
            @include('layouts.topbar')
            @include('layouts.sidebar_vertical')

            <div class="content-wrapper">
                <div id="content-page" class="content-page">
                    <div class="container-fluid">
                        @include('layouts.pageheader')
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </div>

    @endif
    <snackbar></snackbar>

    @if (Myhelper::hasRole('admin'))
    <div class="modal fade" id="walletLoadModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header btn btn-primary">
                    <h5 class="modal-title text-white" id="exampleModalLabel">Wallet Load</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="walletLoadForm" action="{{route('fundtransaction')}}" method="post">
                    <div class="modal-body">
                        <div class="row">
                            <input type="hidden" name="type" value="loadwallet">
                            {{ csrf_field() }}
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
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Submitting">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Report</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editForm" action="{{route('statementUpdate')}}" method="post">
                    <div class="modal-body">
                        <div class="row">
                            <input type="hidden" name="id">
                            <input type="hidden" name="actiontype" value="">
                            {{ csrf_field() }}
                            <div class="form-group col-md-6">
                                <label>Status</label>
                                <select name="status" class="form-control" required>
                                    <option value="">Select Type</option>
                                    <option value="pending">Pending</option>
                                    <option value="success">Success</option>
                                    <option value="failed">Failed</option>
                                    <option value="reversed">Reversed</option>
                                </select>
                            </div>

                            <div class="form-group col-md-6">
                                <label>Ref No</label>
                                <input type="text" name="refno" class="form-control" placeholder="Enter Vle id" required="">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>Txn Id</label>
                                <input type="text" name="txnid" class="form-control" placeholder="Enter Vle id" required="">
                            </div>

                            <div class="form-group col-md-6">
                                <label>Pay Id</label>
                                <input type="text" name="payid" class="form-control" placeholder="Enter Vle id" required="">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-12">
                                <label>Remark</label>
                                <textarea rows="3" name="remark" class="form-control" placeholder="Enter Remark"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Updating">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="complaintModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="complaintForm" action="{{route('complaintstore')}}" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="id" value="new">
                        <input type="hidden" name="product">
                        <input type="hidden" name="transaction_id">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label>Subject</label>
                            <select name="subject" class="form-control">
                                <option value="">Select Subject</option>
                                @foreach ($mydata['complaintsubject'] as $item)
                                <option value="{{$item->id}}">{{$item->subject}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" cols="30" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Updating">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="fundRequestModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Wallet Fund Request</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="fundRequestForm" action="{{route('fundtransaction')}}" method="post">
                    <div class="modal-body">
                        @if(Auth::user()->bank != '' && Auth::user()->ifsc != '' && Auth::user()->account != '')
                        <table class="table table-bordered p-b-15" cellspacing="0" style="margin-bottom: 30px">
                            <thead class="thead-light">
                                <tr>
                                    <th>Accoun</th>
                                    <th>Bank</th>
                                    <th>IFSC</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{Auth::user()->account}}</td>
                                    <td>{{Auth::user()->bank}}</td>
                                    <td>{{Auth::user()->ifsc}}</td>
                                </tr>
                            </tbody>
                        </table>
                        @endif

                        <table class="table table-bordered p-b-15" cellspacing="0" style="margin-bottom: 30px">
                            <tbody>
                                <tr>
                                    <th>Settlement Charge</th>
                                    <td></td>
                                </tr>
                                <tr>
                                    <th>Settlement Timing</th>
                                    <td>Bank</td>
                                </tr>
                            </tbody>
                        </table>

                        <input type="hidden" name="user_id">
                        {{ csrf_field() }}
                        @if(Auth::user()->bank == '' && Auth::user()->ifsc == '' && Auth::user()->account == '')
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>Account Number</label>
                                <input type="text" class="form-control" name="account" placeholder="Enter Value" required="" value="{{Auth::user()->account}}">
                            </div>
                            <div class="form-group col-md-6">
                                <label>IFSC Code</label>
                                <input type="text" class="form-control" name="ifsc" placeholder="Enter Value" required="" value="{{Auth::user()->ifsc}}">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Bank Name</label>
                                <input type="text" class="form-control" name="bank" placeholder="Enter Value" required="" value="{{Auth::user()->bank}}">
                            </div>
                        </div>
                        @endif

                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>Wallet Type</label>
                                <select name="type" class="form-control select" required>
                                    <option value="">Select Wallet</option>
                                    <option value="bank">Move To Bank</option>
                                    <option value="wallet">Move To Wallet</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Amount</label>
                                <input type="number" class="form-control" name="amount" placeholder="Enter Value" required="">
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>T- PIN</label>
                                    <input type="password" name="pin" class="form-control" placeholder="Enter transaction pin" required="">
                                    <a href="{{url('profile/view?tab=pinChange')}}" target="_blank" class="text-primary pull-right">Generate or Forgot PIN??</a>
                                </div>
                            </div>
                        </div>
                        <p class="text-danger">Note - If you want to change bank details, please send mail with account
                            details to update your bank details.</p>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Submitting">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="iq-colorbox color-fix">
        <div class="buy-button"> <a class="color-full" href="#"><i class="fa fa-spinner fa-spin"></i></a> </div>
        <div class="clearfix color-picker">
            <h3 class="iq-font-black">Awesome Color</h3>
            <p>This color combo available inside whole template. You can change on your wish, Even you can create your own with limitless possibilities! </p>
            <ul class="iq-colorselect clearfix">
                <li class="color-1 iq-colormark" data-style="color-1"></li>
                <li class="color-2" data-style="iq-color-2"></li>
                <li class="color-3" data-style="iq-color-3"></li>
                <li class="color-4" data-style="iq-color-4"></li>
                <li class="color-5" data-style="iq-color-5"></li>
                <li class="color-6" data-style="iq-color-6"></li>
                <li class="color-7" data-style="iq-color-7"></li>
                <li class="color-8" data-style="iq-color-8"></li>
                <li class="color-9" data-style="iq-color-9"></li>
                <li class="color-10" data-style="iq-color-10"></li>
                <li class="color-11" data-style="iq-color-11"></li>
                <li class="color-12" data-style="iq-color-12"></li>
                <li class="color-13" data-style="iq-color-13"></li>
                <li class="color-14" data-style="iq-color-14"></li>
                <li class="color-15" data-style="iq-color-15"></li>
                <li class="color-16" data-style="iq-color-16"></li>
                <li class="color-17" data-style="iq-color-17"></li>
                <li class="color-18" data-style="iq-color-18"></li>
                <li class="color-19" data-style="iq-color-19"></li>
                <li class="color-20" data-style="iq-color-20"></li>
            </ul>

        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('.iq-colorselect li').on('click', function() {

                // const colorConfig = {
                //     class: $(this).attr('class')
                // }

                // // console.log($(this).attr('class'));
                // localStorage.setItem('className', JSON.stringify($(this).attr('class')));

                // var color = JSON.parse(localStorage.getItem('className'));
                // $(this).addClass('color');
            });
        $('.numbertoword').on('keyup', function () {
            var number = $('.numbertoword').val();
            var words = convertNumberToWords(number);
            $('.wordscontainer').text(words);
        });

        function convertNumberToWords(number) {
            var ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine'];
            var teens = ['Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
            var tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

            function convertChunk(num) {
                var words = '';
                if (num >= 100) {
                    words += ones[Math.floor(num / 100)] + ' Hundred ';
                    num %= 100;
                }
                if (num >= 10 && num <= 19) {
                    words += teens[num - 10];
                } else {
                    words += tens[Math.floor(num / 10)];
                    if (num % 10 > 0) {
                        words += '-' + ones[num % 10];
                    }
                }
                return words;
            }

            if (number === '0') {
                return 'Zero';
            }

            var words = '';
            if (number >= 10000000) {
                words += convertChunk(Math.floor(number / 10000000)) + ' Crore ';
                number %= 10000000;
            }
            if (number >= 100000) {
                words += convertChunk(Math.floor(number / 100000)) + ' Lakh ';
                number %= 100000;
            }
            if (number >= 1000) {
                words += convertChunk(Math.floor(number / 1000)) + ' Thousand ';
                number %= 1000;
            }
            if (number > 0) {
                words += convertChunk(number);
            }

            return words.trim();
        }
        });
    </script>
</body>

</html>