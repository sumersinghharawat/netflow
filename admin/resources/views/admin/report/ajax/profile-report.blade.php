<div class="panel panel-default">
    <div class="panel-body">
        <div class="card">
            <div class="card-header">
                <div class="mb-2">
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
                                        alt="" class=" img-fluid">
                                </span>
                            @endif
                        </div>


                    </div>
                </div>


            </div>
            <div class="row">
                <h5 style="text-align: center;"><strong>{{ __('common.memberName') }} :
                    </strong>{{ $user->userDetails->name }}({{ $user->username }})</h5>
            </div>

            <div class="d-flex gap-1 justify-content-end mb-3 d-print-none">
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
            <table class="table table-hover">
                <thead>
                    <tr class="th">

                        <th>{{ __('common.sponsor') }}</th>
                        <th>{{ __('common.email') }}</th>
                        <th>{{ __('common.phone') }}</th>
                        <th>{{ __('common.country') }}</th>
                        <th>{{ __('common.zip') }}</th>
                        <th>{{ __('reports.enrollment_date') }}</th>

                    </tr>
                </thead>
                <tbody>

                    <td>
                        {{ $user->sponsor->username ?? 'NA' }}
                    </td>
                    <td>
                        {{ $user->email }}
                    </td>
                    <td>
                        {{ $user->userDetails->mobile }}
                    </td>
                    <td>

                        {{ $user->userDetails->country->name ?? 'NA' }}
                    </td>
                    <td>
                        @isset($user->userDetails->pin)
                            {{ $user->userDetails->pin }}
                        @else
                            NA
                        @endisset

                    </td>
                    <td>
                        {{ Carbon\Carbon::parse($user->userDetails->join_date)->toDateString() }}
                    </td>

                </tbody>
            </table>
        </div>
</div>
    </div>
</div>
</div>

</div>
