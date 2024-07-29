@extends('layouts.app')
@section('title', trans('reports.epin_transfer_report'))
@section('content')
    <div class="row">
        <div class="col-lg-12 d-print-none">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">{{ __('reports.epin_transfer_report') }}</h5>
                    <form class="row row-cols-lg-auto g-3 align-items-center" method="GET"
                        action="{{ route('reports.epinTransfer') }}">
                        <div class="col-lg-3">
                            <label for="inlineFormInputGroupUsername">{{ __('common.from_user') }}</label>
                            <div class="input-group">
                                <div class="input-group">
                                    <select class="userData form-select select2-search-user" id="fromUser" name="fromUser">
                                        @if (request()->fromUser && $fromUser)
                                            <option value="{{ $fromUser->id }}"selected>{{ $fromUser->username }}</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <label for="inlineFormInputGroupUsername">{{ __('common.to_user') }}</label>
                            <div class="input-group">
                                <select class="userData form-select select2-ajax select2-search-user" id="toUser" name="toUser">
                                    @if (request()->toUser && $toUser)
                                        <option value="{{ $toUser->id }}"selected>{{ $toUser->username }}</option>
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-2">
                            <label for="dateRange">{{ __('common.dateRange') }}</label>
                            <select class="form-select" id="dateRange" name="date">
                                <option value="today" {{ request('date') == 'today' ? 'selected' : '' }}>
                                    {{ __('common.today') }}</option>
                                <option value="overall" {{ request('date') == 'overall' ? 'selected' : '' }}>
                                    {{ __('common.overall') }}</option>
                                <option value="month" {{ old('date') == 'month' ? 'selected' : '' }}>
                                    {{ __('common.month') }}</option>
                                <option value="year" {{ old('date') == 'year' ? 'selected' : '' }}>
                                    {{ __('common.year') }}</option>
                                <option value="custom" {{ old('date') == 'custom' ? 'selected' : '' }}>
                                    {{ __('common.custom') }}</option>
                            </select>
                        </div>
                        <div class="col-lg-4 d-none" id="dates">
                            <div class="row">
                                <div class="col-lg-6">
                                    <label class=""
                                        for="fromDate">{{ __('common.fromDate') }}</label>
                                    <div class="input-group">
                                        <input type="date" class="form-control" id="fromDate"
                                            name="fromDate" value="{{ old('fromDate') }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <label class=""
                                        for="toDate">{{ __('common.toDate') }}</label>
                                    <div class="input-group">
                                        <input type="date" class="form-control" id="toDate"
                                            name="toDate" value="{{ old('toDate') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 d-flex">
                            <div id="s-button" class="mt-lg-4">
                                <button type="submit" class="btn btn-primary w-md">{{ __('common.submit') }}</button>
                                <a href="{{ route('reports.epinTransfer') }}"
                                    class="btn btn-danger w-md ms-2">{{ __('common.reset') }}</a>
                            </div>

                        </div>
                    </form>

                </div>
                <!-- end card body -->
            </div>
            <!-- end card -->
        </div>
        <!-- end col -->
    </div>
    <div class="row">
        <div class="col-lg-12">
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

                        <div class="card-body">

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

                            <div class="table-responsive">
                                <table class="table align-middle table-nowrap mb-0">
                                    <thead class="table-light">
                                        <tr>

                                            <th class="align-middle">#</th>
                                            <th class="align-middle">{{ __('common.from_user') }}</th>
                                            <th class="align-middle">{{ __('common.to_user') }}</th>
                                            <th class="align-middle">{{ __('reports.epin') }}</th>
                                            <th class="align-middle">{{ __('reports.transfer_date') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($epinTransferHistory as $item)
                                            <tr>
                                                <td>
                                                    {{ $epinTransferHistory->firstItem() + $loop->index }} </td>
                                                <td>{{ $item->fromUser->userDetail->name . ' ' . $item->fromUser->userDetail->second_name . ' ' . '(' . $item->fromUser->username . ')' }}
                                                </td>
                                                <td>{{ $item->toUser->userDetail->name . ' ' . $item->toUser->userDetail->second_name . ' ' . '(' . $item->toUser->username . ')' }}
                                                </td>
                                                <td>
                                                    {{ $item->epin->numbers }}
                                                </td>
                                                <td>
                                                    {{ Carbon\Carbon::parse($item->date)->format('Y-m-d h:i:s A') }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="100%">
                                                    <div class="nodata_view">
                                                        <img src="{{ asset('assets/images/nodata-icon.png') }}"
                                                            alt="">
                                                        <span class="text-secondary fs-5">{{ __('common.no_data') }}</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse

                                    </tbody>
                                </table>
                            </div>
                            <span class="pagination_new d-print-none">
                                {{ $epinTransferHistory->links() }}
                            </span>
                            <!-- end table-responsive -->
                        </div>
                    </div>
                </div>
            </div>
        @endsection

        @push('scripts')
            <script>
                $(() => {
                    getUsers();
                })
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

                const downloadExcel = () => {
                    try {
                        let data = {
                            username: $('#username').val(),
                            filter_type: $('#filter_type').val(),
                            fromDate: $('#fromDate').val(),
                            toDate: $('#toDate').val(),
                        }
                        console.log(data);
                        var url = "{{ route('export.epinTransferReport') }}?" + $.param(data)
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
                            filter_type: $('#filter_type').val(),
                            fromDate: $('#fromDate').val(),
                            toDate: $('#toDate').val(),
                        }

                        var url = "{{ route('export.epinTransferReportcsv') }}?" + $.param(data)
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
