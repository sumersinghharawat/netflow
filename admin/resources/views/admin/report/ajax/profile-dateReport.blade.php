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
                                    <p class="text-muted fw-bolder">{{ __('common.phone') }} :
                                        {{ $companyProfile->phone }}</p>
                                    <p class="text-muted fw-bolder">{{ __('common.email') }} :
                                        {{ $companyProfile->email }}</p>
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
                <br>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                        </div>

                        <div class="form-group">
                            {{--  <div class="row d-flex justify-content-end">
                        <div class="col-md-5 d-flex align-items-center justify-content-end" style="gap:1%">
                            <form style="width:auto" action="{{ route('export.profiledatereportexcel') }}" method="post">
                                @csrf
                                @if (request()->has('daterange'))
                                    <input type="hidden" name="fromDate" value="{{ request()->fromDate }}">
                                    <input type="hidden" name="toDate" value="{{ request()->toDate }}">
                                @endif
                                <button class="btn btn-primary">
                                    {{ __('common.create_excel') }}
                                </button>

                            </form>
                            <!-- onclick="exportprofiledatereport(event.target);" -->
                            <span data-href="{{ route('export.profiledatereportcsv') }}" id="export" class="btn btn-primary"
                                >{{ __('common.create_csv') }}</span>
                            <a href="javascript:window.print()" class="btn btn-success waves-effect waves-light me-1"
                                id="printButton"><i class="fa fa-print"></i></a>
                        </div>
                    </div> --}}
                            <div class="float-end d-flex gap-1 d-print-none">
                                <button class="btn btn-primary" onclick="downloadExcelDate()">
                                    {{ __('common.create_excel') }}
                                </button>

                                <button type="button" class="btn btn-primary waves-effect waves-light" id="export"
                                    onclick="downloadCSVDate()">
                                    <i class="font-size-16 align-middle me-2"></i>{{ __('common.create_csv') }}
                                </button>
                                <a href="javascript:window.print()"
                                    class="btn btn-success waves-effect waves-light me-1" id="printButton"><i
                                        class="fa fa-print"></i></a>

                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover" id="profileDateReport">
                                <thead>
                                    <tr class="th">
                                        <th>{{ __('common.memberName') }}</th>
                                        <th>{{ __('common.sponsor') }}</th>
                                        <th>{{ __('common.email') }}</th>
                                        <th>{{ __('common.phone') }}</th>
                                        <th>{{ __('common.country') }}</th>
                                        <th>{{ __('common.zip') }}</th>
                                        <th>{{ __('reports.enrollment_date') }}</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- @foreach ($users as $user)
                                        <tr>
                                            <td>{{ $user->userDetails->name . ' ' . $user->userDetails->second_name ?? 'NA' }}
                                            </td>
                                            <td>
                                                @if (isset($user->sponsor))
                                                    {{ $user->sponsor->username }}
                                                @else
                                                    NA
                                                @endif
                                            </td>
                                            <td>
                                                @if (isset($user->userDetails))
                                                    {{ $user->email }}
                                                @endif
                                            </td>
                                            <td>
                                                @if (isset($user->userDetails))
                                                    {{ $user->userDetails->mobile }}
                                                @endif
                                            </td>
                                            <td>
                                                @if (isset($user->userDetails))
                                                    {{ $user->userDetails->country->name }}
                                                @endif
                                            </td>
                                            <td>
                                                @isset($user->userDetails->pin)
                                                    {{ $user->userDetails->pin }}
                                                @else
                                                    NA
                                                @endisset

                                            </td>
                                            <td>
                                                @if (isset($user->userDetails))
                                                    {{ Carbon\Carbon::parse($user->date_of_joining)->toDateString() }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach --}}


                                </tbody>
                            </table>
                        </div>


                    </div>
                </div>
            </div>


        </div>
