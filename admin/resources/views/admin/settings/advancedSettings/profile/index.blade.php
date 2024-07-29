@extends('layouts.app')
@section('title', 'Profile-Settings')

@section('content')
    <div class="container-fluid settings_page profile_page">
        <div class="card">
            <div class="card-header">
                @include('admin.settings.advancedSettings.inc.links')
                <div class="">
                    <div class="alert alert-info alert-dismissible fade show mt-3" role="alert">
                        <h4><i class="far fa-user"></i> {{ __('settings.profile') }}</h4>
                        <p class="text-justify mt-lg-2">
                            Here you can set the auto-logout feature. You can enable or disable Two Factor
                            Authentication(2FA) using
                            google authenticator.
                            You can enable age restriction. You can also set auto-generated usernames. You can also enable
                            or
                            disable strong password settings.
                        </p>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('profile_update') }}" role="form" class="" method="post" name="signup_form"
                    id="profile_form" accept-charset="utf-8">
                    @csrf
                    <input type="hidden" name="common_id" value="{{ $data['commonSettings']->id }}">
                    <input type="hidden" name="moduleStatus_id" value="{{ $data['moduleStatus']->id }}">

                    <div class="form-group">
                        <div class="checkbox">
                            <label class="i-checks">
                                <input class="form-check-input"
                                    {{ $data['commonSettings']->active == 1 ? 'checked' : '' }} type="checkbox"
                                    value="yes" name="enableAutoLogout" id="enableAutoLogout"><i></i>
                                {{ __('settings.enableTimeout') }}
                            </label>
                        </div>
                    </div>

                    <div class="form-group col-lg-3 col-md-4 col-sm-6 col-sx-12 @if ($data['commonSettings']->active == 1) d-block @else d-none @endif"
                        id="logoutTime">
                        <label class="required">{{ __('settings.logout_out_time') }} <span class="text-danger">*</span>
                        </label>
                        <select name='logoutTime' class="form-select" id="logoutTime">
                            <option {{ $data['commonSettings']->logout_time == '300000' ? 'selected' : '' }} value="300000"> 5 {{ __('settings.minutes_of_inactivity') }} </option>
                            <option {{ $data['commonSettings']->logout_time == '600000' ? 'selected' : '' }} value="600000"> 10 {{ __('settings.minutes_of_inactivity') }} </option>
                            <option {{ $data['commonSettings']->logout_time == '900000' ? 'selected' : '' }} value="600000"> 15 {{ __('settings.minutes_of_inactivity') }} </option>
                            <option {{ $data['commonSettings']->logout_time == '1800000' ? 'selected' : '' }} value="1800000"> 30 {{ __('settings.minutes_of_inactivity') }} </option>
                            <option {{ $data['commonSettings']->logout_time == '3600000' ? 'selected' : '' }} value="3600000"> 1 {{ __('settings.hr_of_inactivity') }} </option>
                            <option {{ $data['commonSettings']->logout_time == '7200000' ? 'selected' : '' }} value="7200000"> 2 {{ __('settings.hr_of_inactivity') }} </option>

                        </select>
                        @error('logoutTime')
                            <span class="text-danger form-text">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <div class="checkbox">
                            <label class="i-checks">
                                <input class="form-check-input"
                                    {{ $data['signupSettings']->age_limit > 0 ? 'checked' : '' }} type="checkbox"
                                    value="yes" name="ageRestriction" id="ageLimitStatus"><i></i>
                                {{ __('settings.enable_age_restriction') }}
                            </label>
                        </div>
                    </div>

                    <div class="form-group col-lg-3 col-md-4 col-sm-6 col-sx-12 @if ($data['signupSettings']->age_limit > 0) d-block @else d-none @endif"
                        id="ageLimit">
                        <label class="required">{{ __('settings.minimum_age_required') }} <span class="text-danger">*</span>
                        </label>
                        <input type="text" value="{{ $data['signupSettings']->age_limit }}" name="age_limit"
                            class="form-control">
                        @error('age_limit')
                            <span class="text-danger form-text">{{ $message }}</span>
                        @enderror
                    </div>


                    <div class="form-group">
                        <div class="checkbox">
                            <label class="i-checks">
                                <input class="form-check-input" type="checkbox" value="1" name="login_unapproved"
                                    {{ $data['signupSettings']->login_unapproved == '1' ? 'checked' : '' }}><i></i> Enable
                                {{ __('settings.unapproved_user_login') }}
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="checkbox">
                            <label class="i-checks">
                                <input class="form-check-input" type="checkbox" value="1" name="two_factor"
                                    {{ $data['moduleStatus']->google_auth_status == '1' ? 'checked' : '' }}><i></i> Enable
                                {{ __('settings.two_factor_authentication') }}
                            </label>
                        </div>
                    </div>

                    <hr>
                    <h3>{{ __('common.username') }}</h3>
                    <div class="row">
                        <div class="form-group col-lg-3 col-md-4 col-sm-6 col-sx-12">
                            <input type="hidden" name="userConfig_id" value="{{ $data['usernameConfig']->id }}">
                            <label class="required">{{ __('common.username_type') }}</label>
                            <select name='user_name_type' class="form-select" id="userNameType">
                                <option {{ $data['usernameConfig']->user_name_type == 'static' ? 'selected' : '' }}
                                    value="static">
                                    {{ __('common.static') }}</option>
                                <option {{ $data['usernameConfig']->user_name_type == 'dynamic' ? 'selected' : '' }}
                                    value="dynamic">{{ __('common.dynamic') }}</option>

                            </select>

                        </div>
                    </div>
                    <div class="form-group">
                        <label>{{ __('settings.username_length') }}</label><br><br>
                        <div class="col-md-4">
                            <input type="text" id="username-length-slider" name="username_length">
                        </div>
                    </div>

                    <div class="form-group" id="prefix_status_div">
                        <div class="form-group">
                            <div class="checkbox">
                                <label class="i-checks">
                                    <input class="form-check-input" type="checkbox" value="yes" name="prefix_status"
                                        @checked($data['usernameConfig']->prefix_status) id="prefixStatus">
                                    {{ __('settings.enable_username_prefix') }}
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group {{ $data['usernameConfig']->prefix_status ? 'd-block' : 'd-none' }}"
                        id="userNamePrefix">
                        <label>{{ __('settings.username_prefix') }}<span class="text-danger form-text">*</span></label>
                        <input type="text" name="prefix" class="form-control w-25"
                            value="{{ $data['usernameConfig']->prefix }}" id="prefixName">
                        @error('prefix')
                            <span class="text-danger form-text">{{ $message }}</span>
                        @enderror

                    </div>
                    <hr>
                    <h3>{{ __('common.password') }}</h3>
                    <div class="row">
                        <div class="form-group col-lg-3 col-md-4 col-sm-6 col-sx-12">
                            <label class="required">{{ __('settings.minimum_password_length') }}</label>
                            <input type="text" class="form-control" name="min_password_length" id="min_password_length"
                                value="{{ $data['passwordPolicy']->min_length }}">
                            @error('min_password_length')
                                <span class="text-danger form-text">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="checkbox">
                            <input type="hidden" name="password_policy_id" value="{{ $data['passwordPolicy']->id }}">
                            <label class="i-checks">
                                <input class="form-check-input" type="checkbox" value="1" name="enable_policy"
                                    {{ $data['passwordPolicy']->enable_policy == '1' ? 'checked' : '' }}
                                    id="enablePolicy">
                                {{ __('settings.enable_password_policy') }}
                            </label>
                        </div>
                    </div>
                    <div id="passwordPolicyDiv" class="d-none">
                        <div class="form-group">
                            <div class="checkbox">
                                <label class="i-checks">
                                    <input type="checkbox" class="form-check-input" name="password[mixed_case]"
                                        id="mixed_case" value="1"
                                        {{ $data['passwordPolicy']->mixed_case == 1 ? 'checked' : '' }}>
                                    {{ __('settings.contain_mixed_case_letters') }}
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="checkbox">
                                <label class="i-checks">
                                    <input type="checkbox" class="form-check-input" name="password[number]"
                                        id="contain_number" value="1"
                                        {{ $data['passwordPolicy']->number == 1 ? 'checked' : '' }}><i></i>
                                    {{ __('settings.should_contain_number') }}
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="checkbox">
                                <label class="i-checks">
                                    <input type="checkbox" class="form-check-input" name="password[sp_char]"
                                        id="contain_sp_char" {{ $data['passwordPolicy']->sp_char == 1 ? 'checked' : '' }}
                                        value="1">
                                    {{ __('settings.should_contain_special_character') }}
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary" type="submit">{{ __('common.update') }}</button>
                    </div>
                </form>

            </div>

        </div>

    </div>
@endsection
@push('scripts')
    <script src="{{ asset('assets/libs/ion-rangeslider/js/ion.rangeSlider.min.js') }}"></script>

    <script>
        $(() => {
            $("#username-length-slider").ionRangeSlider({
                skin: "square",
                grid: !0,
                type: "double",
                min: 6,
                max: 20,
                from: "{{ Str::of($data['usernameConfig']->length)->explode(';')[0] }}",
                to: "{{ Str::of($data['usernameConfig']->length)->explode(';')[1] }}",
                step: 1
            })
            if ($('#enablePolicy').prop('checked')) {
                $('#passwordPolicyDiv').removeClass('d-none');
            }
            let type = $('#userNameType').val();
            if (type == 'static') {
                $('#prefix_status_div').removeClass('d-block');
                $('#prefix_status_div').addClass('d-none');
                $('#userNamePrefix').removeClass('d-block');
                $('#userNamePrefix').addClass('d-none');
                // $('#prefixStatus').prop('checked', false);
            } else {
                $('#prefix_status_div').removeClass('d-none');
                $('#prefix_status_div').addClass('d-block');
            }

        });

        $(document).on('change', '#ageLimitStatus', function() {
            if (this.checked) {
                $('#ageLimit').removeClass('d-none');
                $('#ageLimit').addClass('d-block');
            } else {
                $('#ageLimit').removeClass('d-block');
                $('#ageLimit').addClass('d-none');

            }
        });

        $(document).on('change', '#enableAutoLogout', function() {
            if (this.checked) {
                $('#logoutTime').removeClass('d-none');
                $('#logoutTime').addClass('d-block');
            } else {
                $('#logoutTime').removeClass('d-block');
                $('#logoutTime').addClass('d-none');

            }
        });

        $(document).on('change', '#userNameType', function() {
            let type = $('#userNameType').val();
            if (type == 'static') {
                $('#prefix_status_div').removeClass('d-block');
                $('#prefix_status_div').addClass('d-none');
                $('#userNamePrefix').removeClass('d-block');
                $('#userNamePrefix').addClass('d-none');
                $('#prefixStatus').prop('checked', false);
            } else {
                $('#prefix_status_div').removeClass('d-none');
                $('#prefix_status_div').addClass('d-block');
                $('#userNamePrefix').removeClass('d-none');
                $('#userNamePrefix').addClass('d-block');
            }
        });

        $(document).on('change', '#prefixStatus', function() {
            if (this.checked) {
                $('#userNamePrefix').removeClass('d-none');
                $('#userNamePrefix').addClass('d-block');
            } else {
                $('#userNamePrefix').removeClass('d-block');
                $('#userNamePrefix').addClass('d-none');
            }
        });

        $(document).on('change', '#enablePolicy', function() {
            if (this.checked) {
                $('#passwordPolicyDiv').removeClass('d-none');
                $('#passwordPolicyDiv').addClass('d-block');
            } else {
                $('#passwordPolicyDiv').removeClass('d-block');
                $('#passwordPolicyDiv').addClass('d-none');

            }
        });
    </script>
@endpush
