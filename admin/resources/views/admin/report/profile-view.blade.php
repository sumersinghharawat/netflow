@extends('layouts.app')
@section('title', __('reports.profile_reports'))
@section('content')
<style>
    @media print {
        .card {
            display: table;
        }
        }
</style>
    <div class="row d-print-none">
        <div class="col-md-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __('reports.profile_reports') }}</h4>
            </div>
            <div class="card">

                <div class="card-body">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <form method="post" id="userform" action="{{ route('users.report') }}"
                                enctype="multipart/form-data" onsubmit="getUserData(this)">
                                @csrf

                                <div class="row">
                                    <div class="col-md-4">

                                        <div class="ajax-select mt-3 mt-lg-0">
                                            <label class="form-label">{{ __('common.select_user') }}</label>
                                            <select name="username" id="username"
                                                class="form-control select2-ajax select2-multiple select2-search-user"></select>
                                        </div>

                                        <span id="error" style="color: red;">
                                        </span>
                                    </div>
                                    <div class="col-md-2" style="margin-top:3px">
                                        <button type="submit" class="btn btn-primary mob_mrg_0"
                                            style="margin-top: 26px;">{{ __('common.view') }}</button>
                                    </div>
                                </div>
                            </form>

                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
    <div class="row d-print-none">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="card">
                    <div class="card-body filter-report">
                        <div class="panel-body">
                            <form method="get" id="userDateform" action="{{ route('users.dateReport') }}"
                                enctype="multipart/form-data" onsubmit="getUsersDateData(this)">
                                @csrf
                                <div class="row">

                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <input class="form-control" type="date" id="fromDate" autocomplete="off"
                                                        name="fromDate" value="">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <input class="form-control" type="date" id="toDate" name="toDate" autocomplete="off"
                                                        value="">
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-primary"
                                            onclick="getUsersDateData()">{{ __('common.view') }}</button>
                                    </div>
                                </div>
                            </form>

                        </div>

                    </div>

                </div>

            </div>
        </div>
    </div>

    <div class="row">
        <div id="report" class="d-none">
        </div>
    </div>
    <div class="row">
        <div id="dateReport" class="d-none">
            @include('admin.report.ajax.profile-dateReport')
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(() => {
            getUsers();
        })


        const getUserData = async (form) => {
            $('#dateReport').addClass("d-none");
            $('#report').removeClass("d-none");
            event.preventDefault()
            var formElements = new FormData(form);
            for (var [key, value] of formElements) {
                form.elements[key].classList.remove('is-invalid', 'd-block')
            }
            $('.invalid-feedback').remove()

            let url = form.action
            let data = getForm(form)
            const res = await $.post(`${url}`, data)
                .catch((err) => {
                    if (err.status === 422) {
                        formvalidationError(form, err)
                    }
                }).then((result) => {
                    $('#report').html('')
                    $('#report').html(result.data)
                })
        }
        const getUsersDateData = async () => {
            try {
                $('#report').addClass("d-none");
                $(`#username`).val('').trigger('change')
                event.preventDefault()
                let url = "{{ route('users.dateReport') }}"
                let params = {
                    fromDate: $('#fromDate').val(),
                    toDate: $('#toDate').val(),
                }
                $('#dateReport').removeClass("d-none");

                var table = $('#profileDateReport').DataTable({
                    processing: true,
                    serverSide: true,
                    searching: false,
                    "bDestroy": true,
                    "sDom": 'Lfrtlip',
                    columnDefs: {
                        orderable: false
                    },
                    "language": {
                        "emptyTable": "<div class='nodata_view'><img src='{{ asset('assets/images/nodata-icon.png') }}'' alt=''><span class='text-secondary fs-5'>{{ __('common.no_data') }}</span></div>"
                    },
                    ajax: {
                        type: "GET",
                        url: url,
                        data: params,
                        error: function(xhr, error, thrown) {
                            if (xhr.status === 422) {
                                $('#dateReport').addClass("d-none");
                                notifyError(xhr.responseJSON.message)
                            }
                        }
                    },

                    columns: [{
                        data: 'member',
                        name: 'member',
                    }, {
                        data: 'sponsor_name',
                        name: 'sponsor_name',
                    }, {
                        data: 'email',
                        name: 'email',
                    }, {
                        data: 'phone',
                        name: 'phone',
                    }, {
                        data: 'country',
                        name: 'country',
                    }, {
                        data: 'pin',
                        name: 'pin',
                        orderable: false,
                    }, {
                        data: 'date_of_joining',
                        name: 'date_of_joining',
                        orderable: false,
                    }],

                })
            } catch (error) {
                console.log(error);
            }

        }

        const downloadExcelDate = () => {
            try {
                let data = {
                    fromDate: $('#fromDate').val(),
                    toDate: $('#toDate').val(),
                }
                var url = "{{ route('export.profilereportexcel') }}?" + $.param(data)
                var a = document.createElement("a");
                a.href = url;
                a.download = "active_deactive_report" + Date() + ".xlsx";
                document.body.appendChild(a);
                a.click();

            } catch (error) {
                console.log(error);
            }
        }

        const downloadCSVDate = () => {
            try {
                let data = {
                    fromDate: $('#fromDate').val(),
                    toDate: $('#toDate').val(),
                }

                var url = "{{ route('export.profilereportcsv') }}?" + $.param(data)
                var a = document.createElement("a");
                a.href = url;
                a.download = "active_deactive_report.csv";
                document.body.appendChild(a);
                a.click();
                window.location.href = _url;


            } catch (error) {
                console.log(error);
            }
        }

        const downloadExcel = () => {
            try {
                let data = {
                    username: $('#username').val(),
                }
                console.log(data);
                var url = "{{ route('export.profilereportexcel') }}?" + $.param(data)
                var a = document.createElement("a");
                a.href = url;
                a.download = "active_deactive_report" + Date() + ".xlsx";
                document.body.appendChild(a);
                a.click();

            } catch (error) {
                console.log(error);
            }
        }

        const downloadCSV = () => {
            try {
                let data = {
                    username: $('#username').val(),
                }

                var url = "{{ route('export.profilereportcsv') }}?" + $.param(data)
                var a = document.createElement("a");
                a.href = url;
                a.download = "active_deactive_report.csv";
                document.body.appendChild(a);
                console.log(a);
                a.click();
                window.location.href = _url;


            } catch (error) {
                console.log(error);
            }
        }
    </script>
@endpush
