@extends('layouts.app')
@section('title', __('settings.mail_settings'))
@section('content')

    <div class="container-fluid settings_page ">
        <div class="card">
            <div class="card-header">
                @include('admin.settings.inc.links')
                <div class="">
                    <div class="alert alert-info alert-dismissible fade show mt-3" role="alert">
                        <h4><i class="far fa-envelope"></i>
                            {{ __('settings.mail_settings') }}</h4>
                        <p class="text-justify mt-lg-2">
                            {{ __('settings.mail_settings_description') }}
                        </p>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>

                </div>
            </div>
            <div class="card-body">
                <form method="post" action="{{ route('update.mailSettings') }}" id="smtp">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <label for="mailType">{{ __('settings.mail_type') }} <span class="text-danger">*</span> </label>

                            <select id="mailType" name="mailType" class="form-select">
                                {{-- <option value="normal" @if ($settingsData->reg_mail_type == 'normal') selected @endif>
                                    {{ __('settings.normal_mail') }}
                                </option> --}}
                                <option value="smtp" @if ($settingsData->reg_mail_type == 'smtp') selected @endif>SMTP</option>
                            </select>
                        </div>
                    </div>
                    <div class="row" id="smtpDetails" style="display:none;">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="mailType">{{ __('settings.smtp_auth') }} <span
                                            class="text-danger">*</span></label>
                                    <select id="mailType" name="smtpAuthtype" class="form-select">
                                        <option value="1" @if ($settingsData->smtp_authentication == '1') selected @endif>{{ __('common.enabled') }}
                                        </option>
                                        <option value="0" @if ($settingsData->smtp_authentication == '0') selected @endif>{{ __('common.disabled') }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label>
                                        {{ __('settings.smtp_protocol') }}
                                    </label>
                                    <select id="mailType" name="smtpProtocol" class="form-select">
                                        <option value="tls" @if ($settingsData->smtp_protocol == 'tls') selected @endif>TLS
                                        </option>
                                        <option value="ssl" @if ($settingsData->smtp_protocol == 'ssl') selected @endif>SSL
                                        </option>
                                        <option value="none" @if ($settingsData->smtp_protocol == 'none') selected @endif>NONE
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label>
                                        {{ __('settings.smtp_host') }}
                                    </label>
                                    <input class="form-control" type="text" name="smtpHost"
                                        value="{{ $settingsData->smtp_host }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label>
                                        SMTP {{ __('common.username') }}
                                    </label>
                                    <input class="form-control" type="text" name="smtpusername"
                                        value="{{ $settingsData->smtp_username }}">
                                </div>
                                <div class="col-md-4">
                                    <label>
                                        SMTP {{ __('common.password') }}
                                    </label>
                                    <input class="form-control" type="password" name="smtppw"
                                        value="{{ $settingsData->smtp_password }}">
                                </div>
                                <div class="col-md-4">
                                    <label>
                                        {{ __('settings.smtp_port') }}
                                    </label>
                                    <input class="form-control" type="text" name="smtpport"
                                        value="{{ $settingsData->smtp_port }}">
                                </div>
                                <div class="col-md-4">
                                    <label>
                                        {{ __('common.timeout') }}
                                    </label>
                                    <input class="form-control" type="text" name="smtptimeout"
                                        value="{{ $settingsData->smtp_timeout }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <button class="btn btn-primary" onclick="this.form.submit()">{{ __('common.submit') }}</button>
                        </div>
                    </div>
                </form>
                <div class="col-md-8 float-end">
                    <div class="row">
                        <div class="col-md-6">
                            Please enter the recipient's email address and click "Send Mail" to test that your configurations are okay.
                        </div>
                        <div class="col-md-6">
                            <form method="post" action="{{ route('test.mail') }}">
                            @csrf
                                <div class="input-group" >
                                    <input type="email" class="form-control" name="tomail" id="tomail" placeholder="To mail id" required>
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary">{{ __('common.send_test_mail') }}</button>
                                    </div>
                                    <div class="invalid-tooltip">
                                        Please enter the recipient's email address and click "Send Mail" to test that your configurations are okay.
                                    </div>
                                  </div>

                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection
@push('scripts')
    <script>
        $(document).on('change', '#mailType', function() {

            if ($('#mailType').val() == "smtp") {
                $("#smtpDetails").css("display", "block");
            } else {
                $("#smtpDetails").css("display", "none");
            }
        });
        $(document).ready(function() {

            if ($('#mailType').val() == "smtp") {
                $("#smtpDetails").css("display", "block");
            } else {
                $("#smtpDetails").css("display", "none");
            }
        });
    </script>
@endpush
