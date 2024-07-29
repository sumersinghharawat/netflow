@extends('layouts.app')
@section('content')
@section('title', __('reports.payout_released_report'))


<div class="row">
    <div class="col-md-12">
        <div class="card d-print-none">
            <div class="card-body filter-report">
                <div class="row">
                    <div class="col-md-12">
                        <h4>{{ __('reports.payout_released_report') }}</h4>
                    </div>
                    <div class="col-md-8">

                    </div>
                </div>
                <form>
                    <div class="row">

                        <div class="col-md-4">

                            <label>{{ __('common.status') }}</label>
                            <div class="form-group">
                                <select class="form-control" name="status" class="">
                                    <option value="released" @if (app('request')->input('status') == 'released') selected @endif>
                                        {{ __('common.released') }}
                                    </option>
                                    <option value="pending" @if (app('request')->input('status') == 'pending') selected @endif>
                                        {{ __('common.pending') }}
                                    </option>
                                </select>
                            </div>

                        </div>

                        <div class="col-lg-3">
                            <label>{{ __('common.dateRange') }}</label>
                            <div class="form-group">
                                <select class="form-select" name="filter_type" id="dateRange">

                                    <option value="overall" @selected(app('request')->input('filter_type') == 'overall')>
                                        {{ __('common.overall') }}
                                    </option>
                                    <option value="today" @selected(app('request')->input('filter_type') == 'today')>
                                        {{ __('common.today') }}
                                    </option>
                                    <option value="month" @selected(app('request')->input('filter_type') == 'month')>
                                        {{ __('common.month') }}
                                    </option>
                                    <option value="year" @selected(app('request')->input('filter_type') == 'year')>
                                        {{ __('common.year') }}
                                    </option>
                                    <option value="custom" @selected(app('request')->input('filter_type') == 'custom')>
                                        {{ __('common.custom') }}
                                    </option>

                                </select>
                            </div>
                        </div>

                        <div class="col-lg-3 {{ app('request')->input('filter_type') == 'custom' ? 'd-block' : 'd-none' }}"
                            id="dates">
                            <div class="row">
                                <div class="col-lg-6">
                                    <label class="" for="fromDate">{{ __('common.fromDate') }}</label>
                                    <div class="form-group">
                                        <input type="date" class="form-control" name="fromDate" id="fromDate"
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

                        <div class="col-md-3">
                            <button class="btn btn-primary" type="submit" style="margin-top: 27px;">
                                {{ __('common.view') }}
                            </button>
                            <a href="{{ route('reports.payouts') }}" class="btn btn-danger" style="margin-top: 27px;">
                                {{ __('common.reset') }}
                            </a>
                        </div>

                    </div>
                </form>
            </div>
        </div>


        <div class="card">

            <div class="card-header">

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

                    <div class="col-md-12">
                        <div style="float: right;">
                            <button class="btn btn-primary" id="excel"
                                onclick="downloadExcel()">{{ __('common.create_excel') }}</button>
                            <button class="btn btn-primary" id="csv"
                                onclick="downloadCSV()">{{ __('common.create_csv') }}</button>
                            <a href="javascript:window.print()" class="btn btn-success waves-effect waves-light me-1"
                                id="printButton"><i class="fa fa-print"></i></a>
                        </div>
                    </div>
                </div>


            </div>

            <div class="card-body">
                <div class="panel panel-default">
                    <div class="panel-body">

                        <div class="row">
                            <div class="col-md-8">


                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr class="th">
                                            <th>{{ __('common.invoice_no') }}</th>
                                            <th>{{ __('common.memberName') }}</th>
                                            <th>{{ __('common.totalAmount') }}</th>
                                            <th>{{ __('common.date') }}</th>
                                            <th>{{ __('common.status') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $tAmount = 0;
                                        @endphp
                                        @forelse($amountPaids as $data)
                                            <tr>
                                                <td>
                                                    <a href='#' data-bs-toggle="modal" data-bs-target="#invoice"
                                                        onclick="getInvoice({{ $data->id }})" class="invoice_no">
                                                        {{ getPayoutInvoiceNo($data->id) }}</a>
                                                </td>
                                                <td>
                                                    {{ $data->user->userDetails->name }}&nbsp;
                                                    {{ $data->user->userDetails->second_name }}&nbsp;
                                                    ({{ $data->user->username }})
                                                </td>

                                                <td>
                                                    {{ $currency . '' . formatCurrency($data->amount) }}
                                                    @php
                                                        $tAmount += $data->amount;
                                                    @endphp
                                                </td>


                                                <td>

                                                    {{ Carbon\Carbon::parse($data->date)->format('d-M-Y  g:i:s A') }}
                                                </td>
                                                <td>
                                                    @if ($data->type == 'released')
                                                        <span class="badge-credit">
                                                            {{ __('common.paid') }}</span>
                                                    @elseif($data->type == 'pending')
                                                        <span class="badge-credit">
                                                            {{ __('common.pending') }}</span>
                                                    @elseif($data->type == 'approved')
                                                        <span class="badge-credit">
                                                            {{ __('common.approved') }}</span>
                                                    @endif


                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="100%">
                                                    <div class="nodata_view">
                                                        <img src="{{ asset('assets/images/nodata-icon.png') }}"
                                                            alt="">
                                                        <span
                                                            class="text-secondary fs-5">{{ __('common.no_data') }}</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse

                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td>
                                            </td>
                                            <td>
                                                <h5>
                                                    {{ __('common.total') }}
                                                </h5>
                                            </td>
                                            <td>
                                                <h5>
                                                    {{-- {{ $currency . '' . formatCurrency($amountPaids->sum('amount')) }} --}}
                                                    {{ $currency . '' . formatCurrency($tAmount) ?? 0 }}
                                                </h5>
                                            </td>
                                            <td>

                                            </td>

                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="pagination_new">
                                {{ $amountPaids->onEachSide(2)->links() }}
                            </div>



                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div id="invoice" class="modal fade model-" tabindex="-1" aria-labelledby="myModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5>{{ __('common.invoice') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="getInvoice">
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary waves-effect"
                            data-bs-dismiss="modal">Close</button>

                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>
    </div>


@endsection
@push('scripts')
    <script>
        const getInvoice = async (id) => {

            let url = "{{ route('getinvoice.details', ':id') }}";
            url = url.replace(":id", id)

            const res = await $.get(`${url}`)
                .catch((err) => {
                    if (err.status === 422) {
                        formvalidationError(form, err)
                    }
                });

            $('#getInvoice').html(' ')
            $('#getInvoice').html(res.data)
        }

        $(document).on('change', '#dateRange', function() {
            let value = $(this).val()
            if (value == 'custom') {
                $('#dates').removeClass('d-none')
                $('#dates').addClass('d-block')
            } else {
                $('#dates').removeClass('d-block')
                $('#dates').addClass('d-none')
            }
        })

        const downloadExcel = () => {
            try {
                let data = {
                    filter_type: $('#dateRange').val(),
                    fromDate: $('#fromDate').val(),
                    toDate: $('#toDate').val(),
                }

                var url = "{{ route('export.excel.payoutRelease') }}?" + $.param(data)
                var a = document.createElement("a");
                a.href = url;
                a.download = "payout_pending_report" + Date() + ".xlsx";
                document.body.appendChild(a);
                a.click();

            } catch (error) {
                console.log(error);
            }
        }

        const downloadCSV = () => {
            try {
                let data = {
                    filter_type: $('#dateRange').val(),
                    fromDate: $('#fromDate').val(),
                    toDate: $('#toDate').val(),
                }

                var url = "{{ route('export.payoutRelease.csv') }}?" + $.param(data)
                var a = document.createElement("a");
                a.href = url;
                a.download = "payout_pending_report.csv";
                document.body.appendChild(a);
                a.click();

            } catch (error) {
                console.log(error);
            }
        }
    </script>
@endpush
