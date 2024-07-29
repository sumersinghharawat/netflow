@extends('layouts.app')
@section('title', trans('reports.rank_achivers_report'))
@section('content')

    <div class="row">
        <div class="col-md-12 ">
            <div class="card d-print-none">
                <div class="card-body filter-report">
                    <div class="row">
                        <div class="col-md-4">
                            <h4>{{ __('reports.rank_achivers_report') }}</h4>
                        </div>
                        <div class="col-md-8">

                        </div>
                    </div>
                    <form>
                        <div class="row">
                            <div class="col-lg-4">
                                <label>{{ __('common.rank') }}</label>
                                <div class="form-group">
                                    <select class="js-example-basic-multiple form-select" name="rank[]" id="rank" multiple="multiple"
                                        style="width:100%;">

                                        @forelse ($ranks as $rank)
                                            <option value="{{ $rank->id }}"
                                                @if (request()->input('rank') && in_array($rank->id, request()->input('rank'))) selected @endif>{{ $rank->name }}
                                            </option>
                                        @empty
                                            <div class="nodata_view"  >
                                                <img src="{{asset('assets/images/nodata-icon.png')}}" alt="">
                                                <span>{{ __('common.no_data') }}</span>
                                            </div>
                                        @endforelse
                                    </select>
                                </div>

                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="dateRange">{{ __('common.dateRange') }}</label>
                                    <select class="form-select" id="dateRange" name="date">
                                        <option value="overall" @selected(request()->input('date') == 'overall')>{{ __('common.overall') }}
                                        </option>
                                        <option value="today" @selected(request()->input('date') == 'today')>{{ __('common.today') }}
                                        </option>
                                        <option value="month" @selected(request()->input('date') == 'month')>{{ __('common.month') }}
                                        </option>
                                        <option value="year" @selected(request()->input('date') == 'year')>{{ __('common.year') }}</option>
                                        <option value="custom" @selected(request()->input('date') == 'custom')>{{ __('common.custom') }}
                                        </option>
                                    </select>
                                </div>

                            </div>
                            <div class="col-lg-4 {{ request()->input('date') == 'custom' ? 'd-block' : 'd-none' }}"
                                id="dates">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <label class=""
                                            for="inlineFormInputGroupUsername">{{ __('common.fromDate') }}</label>
                                        <div class="input-group">
                                            <input type="date" class="form-control" id="inlineFormInputGroupUsername"
                                                name="fromDate" id="fromDate" value="{{ request()->input('fromDate') }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <label class=""
                                            for="inlineFormInputGroupUsername">{{ __('common.toDate') }}</label>
                                        <div class="input-group">
                                            <input type="date" class="form-control" id="inlineFormInputGroupUsername"
                                                name="toDate" id="toDate" value="{{ request()->input('toDate') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 d-flex">
                                <div id="s-button" style="margin-top:26px">
                                    <button type="submit" class="btn btn-primary ">{{ __('common.submit') }}</button>
                                    <a href="{{ route('reports.rank-achievers') }}"
                                        class="btn btn-danger ms-2">{{ __('common.reset') }}</a>
                                </div>

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
                                        <img src="{{ asset('assets/images/logo-dark.png') }}" alt=""
                                            class="img-fluid">
                                    </span>
                                @else
                                    <span class="logo-sm">
                                        <img src="{{ $companyProfile->logo }}"
                                            alt="" class="img-fluid">
                                    </span>
                                @endif


                            </div>

                        <div class="col-md-12">
                            <div class="float-end d-flex gap-1 d-print-none">
                                <button class="btn btn-primary" onclick="downloadExcel()">
                                    {{ __('common.create_excel') }}
                                </button>

                                <button type="button" class="btn btn-primary waves-effect waves-light" id="export" onclick="downloadCSV()">
                                    <i class="font-size-16 align-middle me-2"></i>{{ __('common.create_csv') }}
                                </button>
                                <a href="javascript:window.print()" class="btn btn-success waves-effect waves-light me-1"
                                    id="printButton"><i class="fa fa-print"></i></a>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="card">
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr class="th">

                                            <th>{{ __('common.memberName') }}</th>
                                            <th>{{ __('reports.new_rank') }}</th>
                                            <th>{{ __('reports.rank_achieved_date') }}</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($rankAchievers as $data)
                                            <tr>
                                                <td>
                                                    {{ $data->user->userDetails->name }}&nbsp;
                                                    {{ $data->user->userDetails->second_name }}&nbsp;
                                                    ({{ $data->user->username }})
                                                </td>

                                                <td>
                                                    {{ $data->rank->name }}
                                                </td>
                                                <td>
                                                    {{ Carbon\Carbon::parse($data->created_at)->format('d-M-Y  h:i:s A') }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="100%">
                                                    <div class="nodata_view"  >
                                                        <img src="{{asset('assets/images/nodata-icon.png')}}" alt="">
                                                        <span class="text-secondary">{{ __('common.no_data') }}</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="pagination_new d-print-none">{{ $rankAchievers->onEachSide(1)->links() }}</div>
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
            });

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
            });

            // function exportrankachievers(_this) {
            //     let _url = $(_this).data('href');
            //     window.location.href = _url;
            // }

            const downloadExcel = () => {
                try {
                    let data = {
                        rank: $('#rank').val(),
                        filter_type: $('#dateRange').val(),
                        fromDate: $('#fromDate').val(),
                        toDate: $('#toDate').val(),
                    }
                    console.log(data);
                    var url = "{{ route('export.rankachieversexcel') }}?" + $.param(data)
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
                        rank: $('#rank').val(),
                        filter_type: $('#dateRange').val(),
                        fromDate: $('#fromDate').val(),
                        toDate: $('#toDate').val(),
                    }

                    var url = "{{ route('export.rankachieverscsv') }}?" + $.param(data)
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
