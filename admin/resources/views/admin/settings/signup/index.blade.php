@extends('layouts.app')
@section('title', __('settings.signup_settings'))
@section('content')
    <div class="container-fluid settings_page">
        <div class="card">
            <div class="card-header">
                @include('admin.settings.inc.links')
                <div class="">
                    <div class="alert alert-info alert-dismissible fade show mt-3" role="alert">
                        <h4><i
                                class="dripicons-user-id me-2"></i>{{ __('settings.signup_settings') }}</h4>
                        <p class="text-justify mt-lg-2">
                            {{ __('settings.signup_settings_description') }}
                        </p>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>

                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('signup.settings.update') }}" method="post">
                    @csrf
                    <div class="form-group ">
                        <label>{{ __('settings.registration_amount') }}</label>
                        <div class="col-md-4">
                            <div class="input-group">
                                <div class="input-group-text">{{ $data['currency'] }}</div>
                                <input type="text" class="form-control" name="reg_amount"
                                    value="{{ formatCurrency($data['configuration']->reg_amount) }}">
                            </div>
                        </div>
                        {{-- <input type="text" name="reg_amount" class="form-control"
                            value="{{ $data['configuration']->reg_amount }}" min="0.00"> --}}
                        @error('reg_amount')
                            <span class="text-danger form-text">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group ">
                        <input class="form-check-input" type="checkbox" value="1" name="block"
                            {{ !$data['signupSetting']->registration_allowed ? 'checked' : '' }} id="registration">
                        <label class="form-check-label" for="registration">
                            {{ __('settings.block_user_registration') }}
                        </label>
                    </div>
                    <div class="form-group ">
                        <input class="form-check-input" type="checkbox" value="1" name="mail_notification"
                            {{ $data['signupSetting']->mail_notification ? 'checked' : '' }} id="mail_notification">
                        <label class="form-check-label" for="mail_notification">
                            {{ __('settings.enable_mail_notification') }}
                        </label>
                    </div>
                    <div class="form-group ">
                        <input class="form-check-input" type="checkbox" value="1" name="free_join"
                            {{ $data['pendingSignUp']->reg_pending_status ? 'checked' : '' }} id="free-join">
                        <label class="form-check-label" for="free-join">
                            {{ __('settings.enable_admin_verification') }}
                        </label>
                    </div>
                    {{-- <div class="form-group ">
                        <label class="form-check-label" for="flexCheckChecked">
                            {{ __('settings.enable_binary_position(leg)_locking') }}

                        </label>
                        <select class="form-select" name="binary_leg" id="binary_lock">
                            <option {{ $data['signupSetting']->binary_leg == 'left' ? 'selected' : '' }} value="left">
                                {{ __('common.left_leg') }}
                            </option>
                            <option {{ $data['signupSetting']->binary_leg == 'right' ? 'selected' : '' }} value="right">
                                {{ __('common.right_leg') }}
                            </option>
                            <option {{ $data['signupSetting']->binary_leg == 'any' ? 'selected' : '' }} value="any"> {{ __('common.Any') }}
                            </option>
                        </select>
                    </div> --}}

                    {{-- next phase works uncomment here TODO --}}
                    {{--<div class="form-group ">
                        <input class="form-check-input" type="checkbox" value="1" name="email_verification"
                            {{ $data['signupSetting']->email_verification == 1 ? 'checked' : '' }} id="email-verification">
                        <label class="form-check-label" for="email-verification">
                            {{ __('settings.enable_email_verification') }}
                        </label>
                    </div>--}}
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">{{ __('common.update') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
