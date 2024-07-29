@extends('layouts.app')
@section('title', trans('reports.top_earners_report'))
@section('content')
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
        <h4 class="mb-sm-0 font-size-18">{{ __('reports.top_earners_report') }}</h4>
    </div>
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
                    <div class="float-end d-flex d-print-none">
                        {{-- <button class="btn btn-primary" id="excel">Create Excel</button>
                        <button class="btn btn-primary" id="csv">Create CSV</button> --}}

                        <form action="{{ route('export.topearnersexcel') }}" method="post">
                            @csrf
                            <button class="btn btn-primary me-1">
                                {{ __('common.create_excel') }}
                            </button>
                        </form>
                        <span data-href="{{ route('export.topearnerscsv') }}" id="export" class="btn btn-primary me-1"
                            onclick="exporttopearners(event.target);">{{ __('common.create_csv') }}</span>

                        <a href="javascript:window.print()" class="btn btn-primary waves-effect waves-light me-1"
                            id="printButton"><i class="fa fa-print"></i></a>
                    </div>
                </div>

            </div>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="table-responsive">
                    <table class="table  m-b-none">
                        <thead>
                            <tr class="th">
                                <th>{{ __('common.name') }}</th>
                                <th>{{ __('reports.total_earnings') }}</th>
                                <th>{{ __('common.ewallet_balance') }}</th>
                                <th class="d-print-none">{{ __('common.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $tab = 'userEarnings';
                            @endphp

                            @forelse ($topEarners as $earners)
                                <tr>
                                    <td>
                                        {{ $earners->userDetails->name . ' ' . $earners->userDetails->second_name . '(' . $earners->username . ')' }}
                                    </td>
                                    <td>
                                        {{ $currency . '' . formatCurrency($earners->legamtDetails->first()->total_amount) }}
                                    </td>
                                    <td>
                                        {{ $currency . '' . formatCurrency($earners->userBalance->balance_amount) }}
                                    </td>
                                    <td class="d-print-none">
                                        <a href="{{ url('admin/ewallet/?userId=' . $earners->id . '&tab=' . $tab) }}"
                                            class="btn btn-primary"><i class="fa fa-eye"></i></a>
                                    </td>
                                </tr>


                            @empty
                                <tr>
                                   <td colspan="4">
                                   <div class="nodata_view">
                                        <img src="{{ asset('assets/images/nodata-icon.png') }}" alt="">
                                        <span class="text-secondary">{{ __('common.no_data') }}</span>
                                    </div>
                                   </td>
                                   
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                </div>
                <div class="pagination_new d-print-none">{{ $topEarners->links() }}</div>

            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $('#checkAll').on('click', function() {
            $('input:checkbox').not(this).prop('checked', this.checked);
        })


        function exporttopearners(_this) {
            let _url = $(_this).data('href');
            window.location.href = _url;
        }
    </script>
@endpush
