@extends('layouts.app')
@section('title', __('reports.activate_deactivate'))
@section('content')
    <div class="row d-print-none">
        <h4>{{ __('reports.activate_deactivate') }}</h4>
    </div>
    <div class="card card-business">
        <div class="card-header">

            <div class="mb-2">
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
                                    class=" img-fluid">
                            </span>
                        @endif
                    </div>


                </div>

            </div>
        </div>
        <div class="card-body">
            <div class="row ">


                <div class="col-md-7 d-print-none">

                    <form action="" method='get'>
                        <div class="row">
                            <div class="col-md-4" style="margin-bottom:10px">
                                <label for="">{{ __('common.dateRange') }}</label>

                                <select class="form-select" name="filter_type" id="filter_type">
                                    <option value="overall" @if (app('request')->input('filter_type') == 'overall') selected @endif>
                                        {{ __('common.overall') }}
                                    </option>
                                    <option value="today" @if (app('request')->input('filter_type') == 'today') selected @endif>
                                        {{ __('common.today') }}</option>
                                    <option value="month" @if (app('request')->input('filter_type') == 'month') selected @endif>
                                        {{ __('common.month') }}
                                    </option>
                                    <option value="year" @if (app('request')->input('filter_type') == 'year') selected @endif>
                                        {{ __('common.year') }}
                                    </option>
                                    <option value="custom" @if (app('request')->input('filter_type') == 'custom') selected @endif>
                                        {{ __('common.custom') }}</option>

                                </select>
                            </div>
                            <div class="col-md-4 {{ app('request')->input('filter_type') == 'custom' ? 'd-block' : 'd-none' }}"
                                id="customRange">

                                <div class="row">
                                    <div class="col-lg-6">
                                        <label class="" for="fromDate">{{ __('common.fromDate') }}</label>
                                        <div class="input-group">
                                            <input type="date" class="form-control" id="fromDate" name="fromDate"
                                                value="{{ app('request')->input('fromDate') }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <label class="" for="toDate">{{ __('common.toDate') }}</label>
                                        <div class="input-group">
                                            <input type="date" class="form-control" id="toDate" name="toDate"
                                                value="{{ app('request')->input('toDate') }}">
                                        </div>
                                    </div>
                                </div>



                            </div>
                            <div class="col-md-4" style="margin-top:26px">
                                <button type="submit" class="btn btn-primary">{{ __('common.view') }}</button>


                                <a href="{{ route('reports.activateDeactivate') }}" class="btn btn-primary">
                                    {{ __('common.reset') }}
                                </a>

                            </div>
                        </div>
                    </form>

                </div>

                <div class="col-md-5 d-flex align-items-center justify-content-end" style="display: none;">
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

                <div class="table-responsive">
                    <table class="table  m-b-none">
                        <thead>
                            <tr class="th">

                                <th>#</th>
                                <th>{{ __('common.invoice_no') }}</th>
                                <th>{{ __('common.name') }}</th>
                                <th>{{ __('common.email') }}</th>
                                <th>{{ __('common.status') }}</th>
                                <th>{{ __('common.date') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <td>{{ $loop->index + $users->firstItem() }}</td>
                                    @if ($moduleStatus->ecom_status)
                                    <td> <a href="{{ route('report.getSalesInvoice', $user->ocOrder->order_id ?? 'NA') }}"
                                            onclick="getInvoice(this)"> {{ $user->ocOrder->invoice_prefix ?? 'NA' }}&nbsp;{{ $user->ocOrder->order_id ?? 'NA' }}</a>
                                    </td>
                                    @else
                                    <td> <a href="{{ route('report.getSalesInvoice', $user->salesOrder->invoice_no ?? 'NA') }}"
                                        onclick="getInvoice(this)"> {{ $user->salesOrder->invoice_no ?? 'NA' }}</a>
                                    </td>
                                    @endif
                                    <td>{{ $user->userDetails->name }}&nbsp;&nbsp;{{ $user->userDetails->second_name }}
                                        ({{ $user->username }})
                                    </td>
                                    <td>{{ $user->email }}
                                    </td>
                                    <td>
                                        @if ($user->active == '1')
                                            <span class="badge rounded-pill badge-soft-success font-size-11">
                                                {{ __('common.active') }}</span>
                                        @elseif($user->active == '0')
                                            <span
                                                class="badge rounded-pill badge-soft-warning font-size-11">{{ __('common.inactive') }}</span>
                                        @endif
                                    </td>

                                    <td>

                                        {{ Carbon\Carbon::parse($user->date_of_joining)->format('d-M-Y  g:i:s A') }}
                                    </td>



                                </tr>


                            @empty
                                <tr>
                                    <td colspan="100%">
                                        <div class="nodata_view">
                                            <img src="{{ asset('assets/images/nodata-icon.png') }}" alt="">
                                            <span class="text-secondary fs-5">{{ __('common.no_data') }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <span class="pagination_new d-print-none">{{ $users->links() }}</span>
                </div>

            </div>
        </div>
        <div class="modal fade bs-example-modal-xl" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel"
            aria-hidden="true" id="salesInvoice">
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
            $(document).on('change', '#filter_type', function() {
                if ($('#filter_type').val() == "custom") {
                    $("#customRange").removeClass('d-none');
                    $("#customRange").addClass('d-block');
                } else {
                    $('#customRange').addClass('d-none');
                    $("#customRange").removeClass('d-block');

                }
            });


            function exportactivedeactivecsv(_this) {
                let _url = $(_this).data('href');
                window.location.href = _url;
            }

            const downloadExcel = () => {
                try {
                    let data = {
                        // username: $('#username').val(),
                        filter_type: $('#filter_type').val(),
                        fromDate: $('#fromDate').val(),
                        toDate: $('#toDate').val(),
                    }
                    var url = "{{ route('export.activedeactiveexcel') }}?" + $.param(data)
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
                        // username: $('#username').val(),
                        filter_type: $('#filter_type').val(),
                        fromDate: $('#fromDate').val(),
                        toDate: $('#toDate').val(),
                    }

                    var url = "{{ route('export.activedeactivecsv') }}?" + $.param(data)
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

            const getInvoice = async (href) => {
                try {
                    event.preventDefault();
                    let url = href.href;
                    const res = await $.get(`${url}`);
                    $('#invoice').html('');
                    $('#invoice').html(res.data);
                    $('#salesInvoice').modal('show');
                } catch (error) {

                }
            }
        </script>
    @endpush
