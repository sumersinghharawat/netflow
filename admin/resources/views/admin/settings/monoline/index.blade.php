@extends('layouts.app')
@section('title', __('settings.commission_settings'))
@section('content')
    <div class="container-fluid settings_page ">
        <div class="card">
            <div class="card-header ">
                @include('admin.settings.inc.links')
            </div>
            <div class="card-body settings_cnt_dv mt-lg-5">
                <form action="{{ route('monoline.update', $config->id) }}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="member_count">{{ __('settings.downline_count') }} <span
                                        class="text-danger">*</span></label>
                                <input type="number" name="downline_count" id="member_count" class="form-control"
                                    min="0" value="{{ old('downline_count', $config->downline_count) }}">

                                @error('downline_count')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="form-group">
                                <label for="bonus">{{ __('settings.bonus') }} <span
                                        class="text-danger">*</span></label>

                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i>{{ $currency }}</i>
                                    </span>
                                    <input type="number" name="bonus" id="bonus" class="form-control"
                                        min="0" value="{{ old('bonus', $config->bonus) }}">

                                    @error('bonus')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- <div class="col-6">
                            <div class="form-group">
                                <label for="limit">{{ __('settings.limit') }} <span
                                        class="text-danger">*</span></label>
                                <input type="number" name="limit" id="limit" class="form-control"
                                    min="0" value="{{ old('limit', $config->limit) }}">
                                @error('limit')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div> --}}

                        {{-- <div class="col-6">
                            <div class="form-group">
                                <label for="user_count">{{ __('settings.referral_count') }} <span
                                        class="text-danger">*</span></label>
                                <input type="number" name="referral_count" id="user_count" class="form-control"
                                    min="0" value="{{ old('referral_count', $config->referral_count) }}">

                                @error('referral_count')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div> --}}
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">{{ __('common.submit') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
