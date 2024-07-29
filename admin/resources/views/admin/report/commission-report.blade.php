@extends('layouts.app')
@section('title', trans('reports.commission_report'))
@section('content')
    <style>
        .select2-container {
            width: 100% !important;
        }
    </style>
    <div class="row d-print-none ">
        <div class="col-md-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __('reports.commission_report') }}</h4>
            </div>
            <div class="card">
                <div class="card-body filter-report">

                    <form class="row row-cols-lg-auto g-3 align-items-center" method="GET">
                        <div class="col-lg-2">
                            <label for="inlineFormInputGroupUsername">{{ __('common.username') }}</label>
                            <div class="form-group">
                                <div class="input-group">
                                    <select class="form-control select2-ajax select2-search-user" id="transaction-user"
                                        name="username">
                                        @if ($username)
                                            <option value="{{ $username->id }}">{{ $username->username }}</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-2">
                            <label>{{ __('common.dateRange') }}</label>
                            <div class="form-group">
                                <select class="form-select" name="filter_type" id="dateRange">

                                    <option value="overall" @if (app('request')->input('filter_type') == 'overall') selected @endif>
                                        {{ __('common.overall') }}
                                    </option>
                                    <option value="today" @if (app('request')->input('filter_type') == 'today') selected @endif>
                                        {{ __('common.today') }}
                                    </option>
                                    <option value="month" @if (app('request')->input('filter_type') == 'month') selected @endif>
                                        {{ __('common.month') }}
                                    </option>
                                    <option value="year" @if (app('request')->input('filter_type') == 'year') selected @endif>
                                        {{ __('common.year') }}
                                    </option>
                                    <option value="custom" @if (app('request')->input('filter_type') == 'custom') selected @endif>
                                        {{ __('common.custom') }}
                                    </option>

                                </select>
                            </div>
                        </div>

                        <div class="col-lg-2">
                            <label>{{ __('reports.commission_type') }}</label>
                            <div class="form-group">

                                <select class="js-example-basic-multiple" id="commissionType" name="commissionType[]"
                                    multiple="multiple">
                                    @foreach ($commission_types as $key => $type)
                                        @if ($type)
                                            <option value="{{ $type }}">
                                                {{ __('ewallet.' . $type) }}
                                            </option>
                                            @if (request()->input('commissionType') != null)
                                                @foreach (request()->input('commissionType') as $old)
                                                    @if ($type == $old)
                                                        <option value="{{ $old }}" selected>
                                                            {{ __('ewallet.' . $old) }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            @endif
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4 ms-lg-4 {{ app('request')->input('filter_type') == 'custom' ? 'd-block' : 'd-none' }}"
                            id="dates">
                            <div class="row ms-lg-4">
                                <div class="col-lg-6">
                                    <label class="" for="fromDate">{{ __('common.fromDate') }}</label>
                                    <div class="form-group">
                                        <input type="date" class="form-control" id="fromDate" name="fromDate"
                                            value="{{ app('request')->input('fromDate') }}">
                                    </div>
                                </div>
                                <div class="col-lg-6 ">
                                    <label class="" for="toDate">{{ __('common.toDate') }}</label>
                                    <div class="form-group">
                                        <input type="date" class="form-control" id="toDate" name="toDate"
                                            value="{{ app('request')->input('toDate') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 d-flex form-group">
                            <div id="s-button"
                                class="{{ app('request')->input('filter_type') == 'custom' ? '' : 'mt-lg-4 ms-lg-5' }}">
                                <button type="submit" class="btn btn-primary ">{{ __('common.submit') }}</button>
                                <a href="{{ route('reports.commission') }}"
                                    class="btn btn-danger ms-2">{{ __('common.reset') }}</a>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <div class="card">

        <div class="card-header">
            <div class="row report_address_row">
                <div class="col-md-6">
                    <div class="report_address_box">
                        <h4 class="card-title ">{{ $companyProfile->name }}</h4>
                        <p class="text-muted fw-bolder">{{ $companyProfile->address }}</p>
                        <p class="text-muted fw-bolder">{{ __('common.phone') }} :
                            {{ $companyProfile->phone }}</p>
                        <p class="text-muted fw-bolder">{{ __('common.email') }} :
                            {{ $companyProfile->email }}</p>
                    </div>
                </div>
                <div class="col-md-6 report_logo">
                    @if ($companyProfile->logo == null)
                        <span class="logo-sm">
                            <img style="max-width:200px" src="{{ asset('assets/images/logo-dark.png') }}" alt=""
                                class="img-fluid">
                        </span>
                    @else
                        <span class="logo-sm">
                            <img src="{{ $companyProfile->logo }}" alt=""
                                class="img-fluid">
                        </span>
                    @endif


                </div>
            </div>
        </div>


        <div class="card-body">
            <div class="form-group">
                <div class="row d-flex justify-content-end">
                    <div class="d-flex flex-wrap gap-1 justify-content-end d-print-none">
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
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr class="th">
                                        <th>#</th>
                                        <th>{{ __('common.memberName') }}</th>
                                        <th>{{ __('common.type') }}</th>
                                        <th>{{ __('common.amount') }}</th>
                                        @if ($showTDS == 'yes')
                                            <th>{{ __('common.tax') }}</th>
                                        @endif
                                        @if ($showServiceCharge == 'yes')
                                            <th>{{ __('reports.service_charge') }}</th>
                                        @endif
                                        @if ($showAmountPayable == 'yes')
                                            <th>{{ __('reports.amount_payable') }}</th>
                                        @endif
                                        <th> {{ __('common.date') }}</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $tAmount = 0;
                                        $tTax = 0;
                                        $tServiceCharge = 0;
                                        $tAmountPayable = 0;
                                    @endphp

                                    @forelse($commission as $report)
                                        <tr>
                                            <td>{{ $loop->index + $commission->firstItem() }}</td>
                                            <td>
                                                @if ($report->user->delete_status)
                                                    {{ $report->user->userDetails->name . '' . $report->user->userDetails->secondName . '(' . $report->user->username . ')' }}
                                                @else
                                                    {{ $report->user->username }}
                                                @endif
                                            </td>
                                            <td>
                                                @if ($module_status->table_status && $module_status->mlm_plan == 'Board')
                                                    {{ __('reports.table_commission') }}
                                                @elseif($report->amount_type == 'daily_investment')
                                                    {{ __('reports.daily_investment') }}
                                                @elseif($report->amount_type == 'purchase_donation')
                                                    {{ __('reports.purchase_donation') }}
                                                @else
                                                    {{ __('ewallet.' . $report->amount_type) }}
                                                @endif
                                            </td>
                                            <td>
                                                {{ $currency . ' ' . formatCurrency($report->total_amount) }}
                                                @php
                                                    $tAmount += $report->total_amount;
                                                @endphp
                                            </td>
                                            @if ($showTDS == 'yes')
                                                <td>
                                                    {{ $currency . '' . formatCurrency($report->tds) }}
                                                    @php
                                                        $tTax += $report->tds;
                                                    @endphp
                                                </td>
                                            @endif
                                            @if ($showServiceCharge == 'yes')
                                                <td>
                                                    {{ $currency . ' ' . formatCurrency($report->service_charge) }}
                                                    @php
                                                        $tServiceCharge += $report->service_charge;
                                                    @endphp
                                                </td>
                                            @endif
                                            @if ($showAmountPayable == 'yes')
                                                <td>
                                                    {{ $currency . ' ' . formatCurrency($report->amount_payable) }}
                                                    @php
                                                        $tAmountPayable += $report->amount_payable;
                                                    @endphp
                                                </td>
                                            @endif
                                            <td>
                                                {{ $report->created_at->format('Y M d h:i:s A') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="100%">
                                                <div class="nodata_view">
                                                    <img src="{{ asset('assets/images/nodata-icon.png') }}"
                                                        alt="">
                                                    <span class="text-secondary">{{ __('common.no_data') }}</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td></td>
                                        <td colspan="2">
                                            <strong> {{ __('common.total') }}</strong>
                                        </td>
                                        <td>
                                            <strong>{{-- {{ $currency . '' . formatCurrency($commission->sum('total_amount')) }} --}}
                                                {{ $currency . '' . formatCurrency($tAmount) }}
                                            </strong>
                                        </td>
                                        @if ($showTDS == 'yes')
                                            <td>
                                                <strong>
                                                    {{--   {{ $currency . '' . formatCurrency($commission->sum('tds')) }} --}}
                                                    {{ $currency . '' . formatCurrency($tTax) }}
                                                </strong>
                                            </td>
                                        @endif
                                        @if ($showServiceCharge == 'yes')
                                            <td>
                                                <strong>
                                                    {{-- {{ $currency . '' . formatCurrency($commission->sum('service_charge')) }} --}}
                                                    {{ $currency . '' . formatCurrency($tServiceCharge) }}
                                                </strong>
                                            </td>
                                        @endif
                                        @if ($showAmountPayable == 'yes')
                                            <td>
                                                <strong>
                                                    {{-- {{ $currency . '' . formatCurrency($commission->sum('amount_payable')) }} --}}
                                                    {{ $currency . '' . formatCurrency($tAmountPayable) }}
                                                </strong>
                                            </td>
                                        @endif
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <span class="pagination_new d-print-none">{{ $commission->appends(request()->query())->links() }}</span>
                    </div>
                </div>

            </div>
        </div>
    </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $('.js-example-basic-multiple').select2();
            getUsers();
            let perPageDropdown = document.getElementById('per-page');
            perPageDropdown.addEventListener('change', function () {
                let url = new URL(window.location.href);
                let searchParams = new URLSearchParams(url.search);
                searchParams.set('per_page', this.value);
                window.location.href = url.origin + url.pathname + '?' + searchParams.toString();
            });
        });

        $(document).on('change', '#filter_type', function() {
            if ($('#filter_type').val() == "custom") {
                $("#customRange").css("display", "block");
                $("#customRange").css("margin-top", "27px");
            } else {
                $("#customRange").css("display", "none");
            }
        });


        function validUser(username) {
            var dataString = "username=" + username;

            $.ajax({
                type: "GET",
                url: "{{ route('validate.user') }}",
                data: dataString,
                success: function(result) {

                    if (result['status'] == "not_exist") {
                        $(error).text(result['message']);
                    }

                },
                error: function(passParams) {

                }
            });

        }

        $('#userData').select2({
            placeholder: 'Username',
            ajax: {
                url: "{{ route('load.users') }}",
                dataType: 'json',
                delay: 250,

                processResults: function(result) {

                    return {
                        results: $.map(result.data, function(item) {
                            return {
                                text: item.username,
                                id: item.id,

                            }
                        })
                    };

                },
                cache: true
            }

        });


        $(document).on('change', '#dateRange', function() {
            let value = $(this).val()
            if (value == 'custom') {
                $('#dates').removeClass('d-none')
                $('#dates').addClass('d-block')
                $('#s-button').removeClass('mt-lg-4')
                $('#s-button').removeClass('ms-lg-5')
            } else {
                $('#dates').removeClass('d-block')
                $('#dates').addClass('d-none')
                $('#s-button').addClass('mt-lg-4')
                $('#s-button').addClass('ms-lg-5')

            }
        })

        function exportcommissionreport(_this) {
            let _url = $(_this).data('href');
            window.location.href = _url;
        }

        const downloadExcel = () => {
            try {
                let data = {
                    username: $('#transaction-user').val(),
                    filter_type: $('#dateRange').val(),
                    fromDate: $('#fromDate').val(),
                    toDate: $('#toDate').val(),
                    commissionType: $('#commissionType').val(),
                }
                var url = "{{ route('export.commissionreportexcel') }}?" + $.param(data)
                var a = document.createElement("a");
                a.href = url;
                // a.download = "active_deactive_report" + Date() + ".xlsx";
                document.body.appendChild(a);
                a.click();
            } catch (error) {
                console.log(error);
            }
        }

        const downloadCSV = () => {
            try {
                let data = {
                    username: $('#transaction-user').val(),
                    filter_type: $('#dateRange').val(),
                    fromDate: $('#fromDate').val(),
                    toDate: $('#toDate').val(),
                    commissionType: $('#commissionType').val(),
                }

                var url = "{{ route('export.commissionreportcsv') }}?" + $.param(data)
                var a = document.createElement("a");
                a.href = url;
                a.download = "active_deactive_report.csv";
                document.body.appendChild(a);
                a.click();
                // window.location.href = _url;


            } catch (error) {
                console.log(error);
            }
        }
    </script>
@endpush
