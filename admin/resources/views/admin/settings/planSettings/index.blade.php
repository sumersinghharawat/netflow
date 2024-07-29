@extends('layouts.app')
@section('title', 'Plan Settings')
@section('content')
    <div class="container ">
        <div class="card">
            <div class="card-header py-4">
                @include('admin.settings.inc.links')
                <div class="py-3">
                    @if ($moduleStatus['mlm_plan'] == 'Matrix')
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <span class="fw-bolder text-primary">
                                <h4><i class="bx bx-wrench me-2"></i>{{ __('planSettings.matrix_settings') }}</h4>
                            </span>
                            <p class="text-justify mt-lg-2">
                                {{ __('planSettings.matrix_description') }}
                            </p>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>

                        <form action="{{ route('matrix.config.update') }}" method="post">
                            @csrf
                            <div class="form-group">
                                <label for="widthCeiling">{{ __('planSettings.Width_ceiling') }} <span
                                        class="text-danger">*</span></label>
                                <input type="number" name="width_ceiling" id="widthCeiling"
                                    class="form-control @error('width_ceiling') is-invalid @enderror"
                                    value="{{ $configuration->width_ceiling }}">
                                @error('width_ceiling')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">{{ __('planSettings.update') }}</button>
                            </div>
                        </form>
                    @elseif($moduleStatus['mlm_plan'] == 'Donation')
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <span class="fw-bolder text-primary">
                                <h4><i class="bx bx-gift me-2"></i>{{ __('planSettings.donation_settings') }}</h4>
                            </span>
                            <p class="text-justify mt-lg-2">
                                {{ __('planSettings.donation_description') }}
                            </p>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <h5>{{ __('planSettings.donation_settings') }}</h5>
                                    <hr class="bg-primary">
                                    <div class="table-responsive">
                                        <table class="table  mb-0">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>{{ __('planSettings.level') }}</th>
                                                    <th> {{ __('planSettings.rate') }} </th>
                                                    <th> {{ __('planSettings.referral_count') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @isset($data['donations'])
                                                    <form action="{{ route('donation.config.update') }}" method="post">
                                                        @csrf
                                                        @forelse ($data['donations'] as $donation)
                                                            <tr>
                                                                <td>{{ $loop->iteration }}</td>
                                                                <td><input type="text" name="name[{{ $donation->id }}]"
                                                                        value="{{ $donation->name }}"
                                                                        class="form-control @error('name.' . $donation->id . '*') is-invalid @enderror">
                                                                </td>
                                                                <td><input type="number" name="rate[{{ $donation->id }}]"
                                                                        value="{{ $donation->pm_rate }}" class="form-control"
                                                                        min="0">
                                                                </td>
                                                                <td>
                                                                    <input type="number"
                                                                        name="referral_count[{{ $donation->id }}]"
                                                                        value="{{ $donation->referral_count }}"
                                                                        class="form-control" min="0">
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            {{ __('planSettings.no_data') }}
                                                        @endforelse
                                                    @endisset
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="form-group py-3">
                                        <label for="">{{ __('planSettings.donation_type') }} <span
                                                class="text-danger">*</span></label>
                                        <select name="donation_type" id="" class="form-select">
                                            <option value="manual"
                                                {{ $data['configurtaion']->donation_type == 'manual' ? 'selected' : '' }}>
                                                {{ __('planSettings.manual') }}</option>
                                            <option value="automatic"
                                                {{ $data['configurtaion']->donation_type == 'automatic' ? 'selected' : '' }}>
                                                {{ __('planSettings.automatic') }}</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <button type="submit"
                                            class="btn btn-primary">{{ __('planSettings.submit') }}</button>
                                    </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @elseif($moduleStatus['mlm_plan'] == 'Stair_Step')
                        @include('stairStep.index')
                    @endif

                </div>
            </div>
        </div>
    </div>



    </div>
@endsection
