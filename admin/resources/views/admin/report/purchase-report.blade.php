@extends('layouts.app')
@section('title', __('reports.repurchase_report'))
@section('content')
    <div class="row d-print-none">
        <div class="col-md-4">
            <h4>{{ __('reports.repurchase_report') }}</h4>
        </div>
        <div class="col-md-8">

        </div>
    </div><br>
    <div class="card d-print-none">
        <div class="card-body filter-report">
            <div class="card-body filter-report">

                <form class="row row-cols-lg-auto g-3 align-items-center" method="GET" id="header-form">
                    <div class="col-lg-3">
                        <label for="inlineFormInputGroupUsername">{{ __('common.username') }}</label>
                        <div class="form-group">
                            <div class="input-group">
                                <select class="form-control select2-ajax select2-search-user" id="username"
                                    name="username">

                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <label>{{ __('common.dateRange') }}</label>
                        <div class="form-group">
                            <select class="form-select" name="filter_type" id="dateRange">

                                <option value="overall" selected>
                                    {{ __('common.overall') }}
                                </option>
                                <option value="today">
                                    {{ __('common.today') }}
                                </option>
                                <option value="month">
                                    {{ __('common.month') }}
                                </option>
                                <option value="year">
                                    {{ __('common.year') }}
                                </option>
                                <option value="custom">
                                    {{ __('common.custom') }}
                                </option>

                            </select>
                        </div>
                    </div>


                    <div class="col-lg-3 d-none" id="dates">

                    </div>
                    <div class="col-lg-3 d-flex form-group">
                        <div id="s-button" class="mt-lg-4">
                            <button type="button" class="btn btn-primary"
                                onclick="getReport()">{{ __('common.submit') }}</button>
                            <button class="btn btn-danger ms-2" onclick="resetData()"
                                type="button">{{ __('common.reset') }}</button>
                        </div>

                    </div>
                </form>
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
                                    <th class="align-middle">{{ __('common.invoice_no') }}</th>
                                    <th class="align-middle">{{ __('common.memberName') }}</th>
                                    <th class="align-middle">{{ __('common.totalAmount') }}</th>
                                    <th class="align-middle">{{ __('common.payment_method') }}</th>
                                    <th class="align-middle">{{ __('reports.purchase_date') }}</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                            <tfoot>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td class="fw-bolder float-end">{{ __('common.total') }}:</td>
                                    <td class="fw-bolder"><span id="total">0</span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                </div>
            </div>
        </div>

    </div>

    <div class="modal fade bs-example-modal-xl" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel"
        aria-hidden="true" id="purchseInvoice">
        <div class="modal-dialog modal-lg">

            <div class="modal-content">
                <div class="modal-body">
                    <div id="invoice"></div>

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
                    username: $('#username').val(),
                    filter_type: $('#dateRange').val(),
                    fromDate: $('#fromDate').val(),
                    toDate: $('#toDate').val(),
                }

                let url = "{{ route('report.getPurchaseReport') }}";

                var table = $('#epinlist').DataTable({
                    processing: true,
                    serverSide: true,
                    searching: false,
                    "bDestroy": true,
                    "sDom": 'Lfrtlip',
                    orderable: false,
                    "language": {
                        "emptyTable": "<div class='nodata_view'><img src='{{ asset('assets/images/nodata-icon.png') }}'' alt=''><span class='text-secondary fs-5'>{{ __('common.no_data') }}</span></div>"
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
                        data: 'invoice_no',
                        name: 'invoice_no',
                        orderble: false,
                    }, {
                        data: 'member',
                        name: 'member',
                        orderble: false,
                    }, {
                        data: 'total_amount',
                        name: 'total_amount',
                        orderble: false,
                    }, {
                        data: 'payment_method',
                        name: 'payment_method',
                        orderble: false,

                    }, {
                        data: 'order_date',
                        name: 'order_date',
                        orderble: false,

                    }],

                    // drawCallback: function(data) {
                    //     $('#total').html();
                    //     $('#total').html(data.json.sum);
                    // },
                    drawCallback: function(row, data, start, end, display) {
                        var api = this.api();
                        var intVal = (amountString) => {
                            if (typeof amountString === 'string') {
                                let withCurrency = amountString.replace(/(<([^>]+)>)/ig, '');
                                let amountOnly = withCurrency.replace(/[\$,]/ig, '');
                                return amountOnly * 1;
                            } else {
                                return amountString * 1;
                            }
                        }


                        // Total over this page
                        pageTotal = api
                            .column(3, {
                                page: 'current'
                            })
                            .data()
                            .reduce(function(initial, values) {
                                return intVal(initial) + intVal(values);
                            }, 0);
                        pageTotal = pageTotal.toFixed(2);
                        $(api.column(3).footer()).html(withCurrency(pageTotal));
                    }

                })


            } catch (error) {
                console.log(error);
            }
        }

        $(document).on('change', '#dateRange', function() {
            let value = $(this).val()
            if (value == 'custom') {
                $('#dates').removeClass('d-none');
                $('#dates').addClass('d-block')
                let newElement = `<div class="row">
                            <div class="col-lg-6">
                                <label class="" for="fromDate">{{ __('common.fromDate') }}</label>
                                <div class="form-group">
                                    <input type="date" class="form-control" name="fromDate" id="fromDate">
                                </div>
                            </div>
                            <div class="col-lg-6 ">
                                <label class="" for="toDate">{{ __('common.toDate') }}</label>
                                <div class="form-group">
                                    <input type="date" class="form-control" id="toDate" name="toDate">
                                </div>
                            </div>
                        </div>`;
                $('#dates').append(newElement);
            } else {
                $('#dates').removeClass('d-block');
                $('#dates').addClass('d-none');
                $('#dates').html('');
            }
        })

        const resetData = () => {
            try {
                $('#username').select2("val", " ");
                $('#dateRange option:eq(0)').prop('selected', 'true')
                $('#dates').removeClass('d-block');
                $('#dates').addClass('d-none');
                $('#dates').html('');
                getReport();

            } catch (error) {
                console.log(error);
            }
        }

        const downloadExcel = () => {
            try {
                let data = {
                    username: $('#username').val(),
                    filter_type: $('#dateRange').val(),
                    fromDate: $('#fromDate').val(),
                    toDate: $('#toDate').val(),
                }

                var url = "{{ route('export.purchaseReport') }}?" + $.param(data)
                var a = document.createElement("a");
                a.href = url;
                a.download = "purchase_report_" + Date() + ".xlsx";
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

                var url = "{{ route('export.purchaseReport.csv') }}?" + $.param(data)
                var a = document.createElement("a");
                a.href = url;
                a.download = "purchase_report.csv";
                document.body.appendChild(a);
                a.click();

            } catch (error) {
                console.log(error);
            }
        }

        const getInvoice = async (href) => {
            try {
                event.preventDefault();
                let url = href.href;
                const res = await $.get(`${url}`);
                $('#invoice').html('');
                $('#invoice').html(res.data);
                $('#purchseInvoice').modal('show');
            } catch (error) {
                console.log(error);
            }

        }
    </script>
@endpush
