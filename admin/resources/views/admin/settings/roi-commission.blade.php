    @extends('layouts.app')
@section('content')
    <div class="container">
    </div>
    <form method="post" action="{{ route('roiCommission.update') }}" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-4">
                            <h4>{{ __('compensation.roi_commissions') }}</h4>
                        </div>
                        <div class="col-md-8">

                        </div>
                    </div>
                    <br>

                    <div class="row">

                        <div class="col-md-6">
                            <label>
                                {{ __('settings.roi_criteria') }} <strong style="color:crimson;">*</strong>
                            </label>

                            <select class="form-select" name="roi_criteria" id="roi_criteria">
                                <option value="roi_based_on_membership_package">{{ __('settings.roi_based_on_membership') }}
                                </option>

                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>
                                {{ __('settings.calculation_period') }} <strong style="color:crimson;">*</strong>
                            </label>

                            <select class="form-select" name="period" id="period" onchange="manageSkipDays()">
                                <option value="daily" @if ($configuration->roi_period == 'daily') selected @endif>
                                    {{ __('compensation.daily') }}</option>
                                <option value="weekly" @if ($configuration->roi_period == 'weekly') selected @endif>
                                    {{ __('compensation.weekly') }}</option>
                                <option value="monthly" @if ($configuration->roi_period == 'monthly') selected @endif>
                                    {{ __('compensation.monthly') }}</option>
                                <option value="yearly" @if ($configuration->roi_period == 'yearly') selected @endif>
                                    {{ __('compensation.yearly') }}</option>

                            </select>
                        </div>
                    </div>
                    <br>
                    <div class="row @if ($configuration->roi_period != 'daily') d-none @endif" id="skip_days">
                        <label>
                            {{ __('compensation.days_to_skip') }}<strong style="color:crimson;">*</strong>
                        </label>
                        @foreach ($days->chunk(4) as $item)
                            <div class="row">
                                @foreach ($item as $key =>  $day)
                                    <div class="col-md-3">
                                    <?php
                                        $skipDays = explode(',', $configuration->roi_days_skip);
                                        $isChecked = in_array(strtolower($day), $skipDays);
                                        ?>
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="{{ $day }}"
                                                name="day[]" value=value="{{ $key }}" {{ $isChecked ? 'checked' : '' }}>
                                            <label class="form-check-label" for="{{ $day }}">
                                                {{ $day }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table  mb-0">
                                    <thead>
                                        <tr>
                                            <th> {{ __('compensation.package') }} </th>
                                            <th> {{ __('settings.hyip_roi') }} (%) </th>
                                            <th> {{ __('compensation.days') }} </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($packages as $package)
                                            @php
                                                $id = $package->id ?? $package->product_id;
                                                $roiField = 'roi' . $id;
                                                $daysField = 'days' . $id;
                                            @endphp
                                            <tr>
                                                <td>
                                                    {{ $package->name ?? $package->model }}
                                                </td>
                                                <td>
                                                    <input type="number"
                                                        class="form-control @error($roiField)
                                                    is-invalid
                                                    @enderror"
                                                        name="roi{{ $package->id ?? $package->product_id }}"
                                                        value="{{ $package->roi ?? 0 }}" min="0">
                                                    @error($roiField)
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <input type="number"
                                                        class="form-control @error($daysField)
                                                        is-invalid
                                                    @enderror"
                                                        name="days{{ $package->id ?? $package->product_id }}"
                                                        value="{{ $package->days }}" min="0">
                                                    @error($daysField)
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <div class="nodata_view">
                                                    <img src="{{ asset('assets/images/nodata-icon.png') }}" alt="">
                                                    <span>{{ __('common.no_data') }}</span>
                                                </div>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">

                    <div class="col-md-2 p-lg-3">
                        <button class="btn btn-primary waves-effect waves-light" name="update" type="submit"
                            style="margin-top: 27px;">
                            {{ __('common.update') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
@push('scripts')
    <script>
        const manageSkipDays = () => {
            let period = event.target.value;
            if (period == "daily") {
                $('#skip_days').removeClass('d-none').addClass('d-block');
            } else {
                $('#skip_days').removeClass('d-block').addClass('d-none');
            }
            console.log(period);
        }
    </script>
@endpush
