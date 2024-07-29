@extends('layouts.app')
@section('title', trans('reports.total_bonus_report'))
@push('page-style')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <style>
        .grid-5-box {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 20px;
        }

        div.dataTables_wrapper div.dataTables_paginate {
            float: left;
        }

        div.dataTables_paginate a {
            padding: 4px 12px;
            margin: 2px;
            border: solid 1px #e3e3e3;
            cursor: pointer;
            color: #242424
        }

        .dataTables_paginate a.disabled {
            color: #ccc;
            opacity: 0.8;
            pointer-events: none
        }

        .dataTables_paginate a.current {
            background-color: #556ee6;
            color: #fff
        }

        .search-label {
            padding-right: 10px;
        }
        @media print {
        .card {
            display: table;
        }
        }

    /* .search-tag{width: 30px!important;} */
    </style>
@endpush
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __('reports.total_bonus_report') }}</h4>
            </div>
        </div>
    </div>
    <div class="col-lg-12 d-print-none">
        <div class="card">
            <div class="card-body">

                <form class="row row-cols-lg-auto g-3 align-items-center" method="GET">
                    <div class="col-lg-3">
                        <label for="inlineFormInputGroupUsername">{{ __('common.username') }}</label>
                        <div class="input-group">
                            <div class="input-group">
                                <select class="userData form-select select2-search-user" name="username"
                                    id="username"></select>
                            </div>
                        </div>
                    </div>


                    <div class="col-lg-2">
                        <label for="dateRange">{{ __('common.dateRange') }}</label>
                        <select class="form-select" id="dateRange" name="date">
                            <option value="today">{{ __('common.today') }}</option>
                            <option value="overall" selected>{{ __('common.overall') }}</option>
                            <option value="month">{{ __('common.month') }}</option>
                            <option value="year">{{ __('common.year') }}</option>
                            <option value="custom">{{ __('common.custom') }}</option>
                        </select>
                    </div>
                    <div class="col-lg-4 d-none" id="dates">
                        <div class="row">
                            <div class="col-lg-6">
                                <label class="" for="inlineFormInputGroupUsername">{{ __('common.fromDate') }}</label>
                                <div class="input-group">
                                    <input type="date" class="form-control" id="fromDate" name="fromDate">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <label class="" for="inlineFormInputGroupUsername">{{ __('common.toDate') }}</label>
                                <div class="input-group">
                                    <input type="date" class="form-control" id="toDate" name="toDate">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 d-flex">
                        <div id="s-button" class="mt-4">
                            <button type="button" class="btn btn-primary "
                                onclick="loadTotalBonus()">{{ __('common.submit') }}</button>
                            <a href="{{ route('reports.totalbonus') }}"
                                class="btn btn-danger ms-2">{{ __('common.reset') }}</a>
                        </div>

                    </div>
                </form>
            </div>

        </div>


    </div>
    <!-- end card body -->
    <div class="card">
        <div class="card-header">
            <div class="row report_address_row">
                <div class="col-md-6">
                    <div class="report_address_box">
                        <h4 class="card-title ">{{ $companyProfile->name }}</h4>
                        <p class="text-muted fw-bolder">{{ $companyProfile->address }}</p>
                        <p class="text-muted fw-bolder">{{ __('common.phone') }} : {{ $companyProfile->phone }}</p>
                        <p class="text-muted fw-bolder">{{ __('common.email') }} : {{ $companyProfile->email }}</p>
                    </div>
                </div>
                <div class="col-md-6 report_logo">
                    @if ($companyProfile->logo == null)
                        <span class="logo-sm">
                            <img src="{{ asset('assets/images/logo-dark.png') }}" alt="" class="img-fluid">
                        </span>
                    @else
                        <span class="logo-sm">
                            <img src="{{ $companyProfile->logo }}" alt=""
                                class="img-fluid">
                        </span>
                    @endif


                </div>

                <div class="col-md-12">
                    <div class="float-end d-flex justify-content-end">
                        <div class="d-flex flex-wrap gap-1 d-print-none">

                            <button class="btn btn-primary" onclick="downloadExcel()">
                                {{ __('common.create_excel') }}
                            </button>

                            <button type="button" class="btn btn-primary waves-effect waves-light" id="export"
                                onclick="downloadCSV()">
                                <i class="font-size-16 align-middle me-2"></i>{{ __('common.create_csv') }}
                            </button>


                            <a href="javascript:window.print()" class="btn btn-success waves-effect waves-light me-1"
                                id="printButton"><i class="fa fa-print"></i></a>
                        </div>
                    </div>
                </div>

            </div>
        </div>


        <div class="card-body">
            <table id="datatable-view-totalBonus" class="table table-bordered dt-responsive nowrap w-100">
                <thead>
                    <th>#</th>
                    <th>{{ __('common.name') }}</th>
                    <th>{{ __('common.totalAmount') }}</th>
                    @if ($showTDS)
                        <th>{{ __('common.tds') }}</th>
                    @endif
                    @if ($showServiceCharge)
                        <th>{{ __('common.service_charge') }} </th>
                    @endif
                    @if ($showAmountPayable)
                        <th>{{ __('common.amount_payable') }}</th>
                    @endif
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>


    </div>
@endsection

@push('scripts')
    <script>
        $(() => {
            loadTotalBonus();
            $('.js-example-basic-multiple').select2();
            getUsers();
        });


        const loadTotalBonus = async () => {
            let params = {
                username: $('#username').val(),
                dateRange: $('#dateRange').val(),
                from: $('#fromDate').val(),
                to: $('#toDate').val(),
            }
            let dateRange = $('#dateRange').val();
            if (dateRange == 'custom') {
                var from = $('#fromDate').val();
                var to = $('#toDate').val();

                if (from.trim().length === 0) {
                    $('#fromDate').addClass('is-invalid');
                    notifyError('from date is required');
                    return false;
                } else {
                    $('#fromDate').removeClass('is-invalid');

                }

                if (to.trim().length === 0) {
                    $('#toDate').addClass('is-invalid');
                    notifyError('to date is required');

                    return false;
                } else {
                    $('#toDate').removeClass('is-invalid');

                }
            }
            var table = $('#datatable-view-totalBonus').DataTable({
                processing: true,
                serverSide: true,
                "sDom": 'Lfrtlip',
                searching: false,
                "bDestroy": true,
                "language": {
                    "emptyTable": "<div class='nodata_view'><img src='{{ asset('assets/images/nodata-icon.png') }}'' alt=''><span class='text-secondary fs-5'>{{ __('common.no_data') }}</span></div>"
                },
                ajax: {
                    type: "GET",
                    url: "{{ route('getTotalBonus') }}",
                    data: params,
                },
                columns: [{
                        data: 'index',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        data: 'member',
                        name: 'name',
                        searchable: true,
                        orderable: false,
                    },
                    {
                        data: 'total_amount',
                        name: 'total_amount',
                        searchable: true
                    },
                    {
                        data: 'tds',
                        name: 'tds',
                        searchable: true,
                    },
                    {
                        data: 'service_charge',
                        name: 'service_charge',
                        searchable: true,
                    },
                    {
                        data: 'amount_payable',
                        name: 'amount_payable',
                        searchable: true,
                    },

                ]
            });
        }

        $(document).on('change', '#dateRange', function() {
            let value = $(this).val()
            if (value == 'custom') {
                $('#dates').removeClass('d-none')
                $('#dates').addClass('d-block')
                $('#s-button').removeClass('mt-lg-4')
            } else {
                $('#dates').removeClass('d-block')
                $('#dates').addClass('d-none')
                $('#s-button').addClass('mt-lg-4')
            }
        })

        function exportbonusreport(_this) {
            let _url = $(_this).data('href');
            window.location.href = _url;
        }

        const downloadExcel = () => {
            try {
                let data = {
                    username: $('#username').val(),
                    filter_type: $('#dateRange').val(),
                    fromDate: $('#fromDate').val(),
                    toDate: $('#toDate').val(),
                }
                console.log(data);
                var url = "{{ route('export.bonusreportexcel') }}?" + $.param(data)
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
                    filter_type: $('#dateRange').val(),
                    fromDate: $('#fromDate').val(),
                    toDate: $('#toDate').val(),
                }

                var url = "{{ route('export.bonusreportcsv') }}?" + $.param(data)
                var a = document.createElement("a");
                a.href = url;
                a.download = "active_deactive_report.csv";
                document.body.appendChild(a);
                a.click();

            } catch (error) {
                console.log(error);
            }
        }

    </script>
@endpush
