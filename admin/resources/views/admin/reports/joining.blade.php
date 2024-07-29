@extends('layouts.app')
@section('title', __('reports.user_joining_report'))
@section('content')
    <div class="row d-print-none">
        <div class="col-md-4">
            <h4>{{ __('reports.user_joining_report') }}</h4>
        </div>
        <div class="col-md-8">

        </div>
    </div><br>
    <div class="card d-print-none">
        <div class="card-body filter-report">
            <form method="get" action="{{ route('report.join') }}">
                <div class="hstack gap-3 col-md-5 form-group">
                    <select name="daterange" id="daterange" class="form-select">
                        <option value="all" {{ $flag == 0 ? 'selected' : '' }}>{{ __('common.overall') }}
                        </option>
                        <option value="today" {{ $flag == 1 ? 'selected' : '' }}>{{ __('common.today') }}
                        </option>
                        <option value="month" {{ $flag == 2 ? 'selected' : '' }}>{{ __('common.month') }}
                        </option>
                        <option value="year" {{ $flag == 3 ? 'selected' : '' }}>{{ __('common.year') }}
                        </option>
                    </select>
                    <button type="submit" class="btn btn-primary">{{ __('common.submit') }}</button>

                </div>


            </form>
        </div>
    </div>

    <div class="card">
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
            <div class="form-group d-print-none">
                <div class="row d-flex justify-content-end">
                    <div class="col-md-5 d-flex align-items-center justify-content-end" style="gap:1%">
                        <form style="width:auto" action="{{ route('export.joiningreportexcel') }}" method="post">
                            @csrf
                            @if (request()->has('daterange'))
                                <input type="hidden" name="daterange" value="{{ request()->daterange }}">
                            @endif
                            <button class="btn btn-primary">
                                {{ __('common.create_excel') }}
                            </button>

                        </form>
                        {{--  <span data-href="{{ route('export.joiningreportcsv') }}" id="export" class="btn btn-primary"
                            onclick="exportjoiningreport(event.target);">{{ __('common.create_csv') }}</span> --}}
                        <span id="export" class="btn btn-primary"
                            onclick="downloadCSV();">{{ __('common.create_csv') }}</span>
                        <a href="javascript:window.print()" class="btn btn-success waves-effect waves-light me-1"
                            id="printButton"><i class="fa fa-print"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table  m-b-none mt-3" id="datatable">
                        <thead>
                            <tr class="th">
                                <th>#</th>
                                <th>{{ __('common.memberName') }}</th>
                                <th>{{ __('common.sponsor') }}</th>
                                @if ($moduleStatus->product_status || $moduleStatus->ecom_status)
                                    <th>{{ __('common.package') }}</th>
                                @endif
                                @if ($regFeeStatus)
                                    <th>{{ __('common.registration_fee') }}</th>
                                @endif
                                <th>{{ __('common.payment_method') }}</th>
                                <th>{{ __('reports.enrollment_date') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($joinDetails as $value)
                                <tr>
                                    <td>{{ $joinDetails->firstItem() + $loop->index }}</td>
                                    <td>{{ $value->userDetails->name . ' ' . $value->userDetails->second_name }}</td>
                                    <td>{{ $value->user->sponsor->username ?? 'NA' }}</td>
                                    @if ($moduleStatus->product_status || $moduleStatus->ecom_status)
                                        <td>{{ $value->package->name ?? $value->package?->model . ' (' . $currency . ' ' . formatCurrency($value->package?->price) . ')' ?? 'NA' }}
                                    @endif
                                    </td>
                                    @if ($regFeeStatus)
                                        <td>{{ $currency . ' ' . formatCurrency($value->reg_amount) ?? 'NA' }}
                                        </td>
                                    @endif
                                    <td>{{ $value->paymentGateway->name ?? 'NA' }}</td>
                                    <td>{{ Carbon\Carbon::parse($value->user->date_of_joining)->format('d M Y h:iA') }}</td>
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
                    <div class="pagination_new"> {{ $joinDetails->links() }}</div>
                </div>

            @endsection

            @push('scripts')
                <script>
                    function exportjoiningreport(_this) {
                        let _url = $(_this).data('href');
                        window.location.href = _url;
                    }
                    const downloadCSV = () => {
                        try {
                            let data = {
                                daterange: $('#daterange').val(),
                            }

                            var url = "{{ route('export.joiningreportcsv') }}?" + $.param(data)
                            var a = document.createElement("a");
                            a.href = url;
                            a.download = "package_upgrade_report.csv";
                            document.body.appendChild(a);
                            a.click();


                        } catch (error) {
                            console.log(error);
                        }
                    }
                </script>
            @endpush
