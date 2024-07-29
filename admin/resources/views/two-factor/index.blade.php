<!doctype html>
<html lang="en">

<head>

    <meta charset="utf-8" />
    <title>{{ $companyDetails->name }} | Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Level up your network marketing business with Infinite MLM Software." name="description" />
    <meta content="Infinitemlmsoftware.com" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <!-- Bootstrap Css -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{ asset('assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
</head>

<style>
    .login_logo {
        width: 100%;
        height: auto;
        float: left;
        padding: 30px 0;
        text-align: center;
    }

    .card {
        box-shadow: 0 0.75rem 2.5rem rgb(18 38 63 / 12%);
        border-radius: 20px;
    }

    .bg-primary.bg-soft {
        background-color: #dbf4ff !important;
    }

    .form-control {
        border: 1px solid #e9e9e9;
    }
    .two-fa-link {
        flex-direction: row-reverse;
        display:flex;
        margin-top: 10px;
    }
</style>
</head>

<body>
    <div class="account-pages my-5 pt-sm-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    @if(session()->has('success'))
                        <div class="alert alert-success">
                            {{ session()->get('success') }}
                        </div>
                    @endif
                    @if(session()->has('error'))
                        <div class="alert alert-error">
                            {{ session()->get('error') }}
                        </div>
                    @endif

                    <div class="card overflow-hidden">
                        <div class="login_logo">
                            <img style="max-width: 200px;" src="{{ asset('assets/images/logo-dark.png') }}"
                                alt="">
                        </div>
                        <div class="bg-primary bg-soft">
                            <div class="row">
                                <div class="col-12">
                                    <div class="text-primary p-4">
                                        <center>
                                            <h3 class="text-primary">{{ __('common.two_factor_auth') }}</h3>
                                            <small>{{ __('common.two_factor_desription') }}</small>
                                        </center>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-4">
                            <div class="p-2">
                                <form class="form-horizontal" action="{{ route('twofactor.verify') }}" method="POST">
                                    @csrf
                                    @if ($hasSecret)
                                    <input type="hidden" name="secret_code" value="{{ $secretCode }}">
                                    @endif
                                    <div class="mb-3">
                                        <div class="input-group auth-pass-inputgroup">
                                            <input type="password"
                                                class="form-control @error('code') is-invalid @enderror"
                                                placeholder="{{ __('common.enter_code') }}" aria-label="Password"
                                                aria-describedby="password-addon" name="code" value="">
                                        </div>
                                        @error('code')
                                            <span class="text-danger">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                        @error('secret_code')
                                            <span class="text-danger">
                                                {{ __('common.code_not_match') }}
                                            </span>
                                        @enderror
                                    </div>
                                    @if ($qrCode)
                                        <div class="mt-1 text-center">
                                            {!! $qrCode !!}
                                        </div>
                                    @endif
                                    <div class="mt-3 d-grid">
                                        <button class="btn btn-primary waves-effect waves-light"
                                            type="submit">{{ __('common.verify') }}</button>
                                    </div>
                                    @if (!$hasSecret)
                                        <div class="two-fa-link">
                                            <a href="{{ route('twoFA.reset') }}">{{ __('common.resetTwoFA') }}</a>
                                        </div>
                                    @endif
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end account-pages -->

    <!-- JAVASCRIPT -->
    <script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/metismenu/metisMenu.min.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer></script>

    <!-- App js -->
    <script src="{{ asset('assets/js/app.js') }}"></script>
</body>

</html>
