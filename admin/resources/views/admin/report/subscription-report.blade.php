@extends('layouts.app')
@section('title', __('reports.subscription_report'))
@section('content')
    <div class="row">
        <div class="col-md-4">
            <h4>{{ __('reports.subscription_report') }}</h4>
        </div>
        <div class="col-md-8">

        </div>
    </div><br>
    <div class="card d-print-none">
        <div class="card-body filter-report">
            <div class="col-lg-6">
                <div class="hstack">
                    <select class="userData form-select select2-ajax select2-search-user" name="username" id="username">
                    </select>
                    <div class="d-flex gap-2 ms-1">
                        <button type="button" class="btn btn-primary"
                            onclick="getReport()">{{ __('common.search') }}</button>
                        <button type="button" class="btn btn-danger"
                            onclick="resetData()">{{ __('common.reset') }}</button>
                    </div>
                </div>

            </div>
        </div>
    </div>


    <div class="card ">
        <div class="card-body">
            <div class="row report_address_row">
                <div class="col-md-6">
                    <div class="report_address_box">
                        <h4 class="card-title ">{{ $companyProfile->name }}</h4>
                        <p class="text-muted fw-bolder">{{ $companyProfile->address }}</p>
                        <p class="text-muted fw-bolder">{{ __('common.phone') }} : {{ $companyProfile->phone }}
                        </p>
                        <p class="text-muted fw-bolder">{{ __('common.email') }} : {{ $companyProfile->email }}
                        </p>
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

                <div class="col-md-12 d-print-none">
                    <div class="float-end d-flex">
                        <div class="d-flex flex-wrap gap-1">
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

        <div class="row">
            <div class="col-12">

                <div class="card1" id="reportTable">
                    <div class="table-responsive">
                        <table class="table align-middle table-nowrap table-check" id="epinlist">
                            <thead class="table-light">
                                <tr>
                                    <th class="align-middle">#</th>
                                    <th class="align-middle">{{ __('common.memberName') }}</th>
                                    @if ($moduleStatus->product_status || $moduleStatus->ecom_status)
                                        <th class="align-middle">{{ __('common.package') }}</th>
                                    @endif
                                    <th class="align-middle">{{ __('reports.subscription_amount') }}</th>
                                    <th class="align-middle">{{ __('common.payment_method') }}</th>
                                    <th class="align-middle">{{ __('reports.subscription_date') }}</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                            <tfoot>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="fw-bolder">{{ __('common.total') }}:<span id="total">0</span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                </div>
            </div>
        </div>

    </div>




@endsection

@push('scripts')
    <script>
        $(() => {
            getUsers();
            getReport();
        });

        const getReport = async () => {
            try {
                let data = {
                    username: $('#username').val()
                }

                let url = "{{ route('reports.getSubscription') }}";

                var table = $('#epinlist').DataTable({
                    processing: true,
                    serverSide: true,
                    searching: false,
                    "bDestroy": true,
                    "sDom": 'Lfrtlip',
                    orderable: false,
                    "language": {
                        "emptyTable": "<div class='nodata_view'><img src='{{asset('assets/images/nodata-icon.png')}}'' alt=''><span class='text-secondary fs-5'>{{ __('common.no_data') }}</span></div>"
                    },
                    ajax: {
                        type: "GET",
                        url: url,
                        data: data
                    },
                    columns: [{
                        data: 'DT_RowIndex',
                        searchable: false,
                        orderable: false,
                    }, {
                        data: 'member',
                        name: 'member',
                        orderble: false,
                    }, {
                        data: 'package',
                        name: 'package',
                        orderble: false,
                    }, {
                        data: 'total_amount',
                        name: 'total_amount',
                        orderable: false,
                    }, {
                        data: 'payment_method',
                        name: 'payment_method',
                        orderble: false,

                    }, {
                        data: 'created_at',
                        name: 'created_at',
                        orderble: false,

                    }],
                    drawCallback: function(data) {
                        $('#total').html();
                        $('#total').html(data.json.sum);
                    }

                })
            } catch (error) {
                console.log(error);
            }
        }

        const resetData = () => {
            try {
                $('#username').select2("val", " ");
                getReport();

            } catch (error) {
                console.log(error);
            }
        }

        const downloadExcel = () => {
            try {
                let data = {
                    username: $('#username').val()
                }

                var url = "{{ route('excel.subscriptionReport') }}?" + $.param(data)
                var a = document.createElement("a");
                a.href = url;
                a.download = "subscription_report_" + Date() + ".xlsx";
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

                var url = "{{ route('export.subscriptionReport.csv') }}?" + $.param(data)
                var a = document.createElement("a");
                a.href = url;
                a.download = "subscription_report.csv";
                document.body.appendChild(a);
                a.click();

            } catch (error) {
                console.log(error);
            }
        }
    </script>
@endpush
