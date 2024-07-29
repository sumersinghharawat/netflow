<!doctype html>
<html lang="en">

<head>

    <meta charset="utf-8" />
    <title>{{ $companyProfile->name }} | Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- App favicon -->
     @if($favicon)
        <link rel="shortcut icon" href="{{ $favicon }}">
        <!-- <img src="{{ $favicon }}" class="w-50" id="shrinkLogo"> -->
    @else
        <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">
    @endif

    <!-- Bootstrap Css -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{ asset('assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
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
            box-shadow: none;
            border-radius: 5px;
            border: solid 5px #fff;
            background-color: #f8f5ff;
        }
        .btn-light {
            color: #000;
            background-color: #ffffff;
            border-color: #e3d6ff;
        }

    .bg-primary.bg-soft {
        background-color: #E3D6FF !important;
    }
    .text-primary{
        color: #33338e;
    }
    .form-control {
        border: 1px solid #e9e9e9;
    }
    .btn-primary {
        color: #fff;
        background-color: #33338E;
        border-color: #33338E;
    }
</style>
</head>

<body>
    <div class="account-pages my-5 pt-sm-5">
        @php
            $status = session()->has('auth_attempt_status') ? true : false;
            $demoStatus = config('mlm.demo_status');
        @endphp
        @if ($errors->has('username'))
            @php
                $count = (session()->has('auth_attempts') ? session()->get('auth_attempts') : 0) + 1;
                session()->forget('auth_attempts');
                session()->put('auth_attempts', $count);
                if ($count >= 3) {
                    session()->put('auth_attempt_status', 1);
                }
            @endphp
        @endif


        <div class="container">
            <div class="row justify-content-center">

                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card overflow-hidden">
                        <div class="login_logo">
                            <!-- <img style="max-width: 200px;" src="{{ asset('assets/images/logo-dark.png') }}"
                                alt=""> -->
                                @if (isFileExists($companyProfile->logo) && $companyProfile->logo != null)
                                    <img style="max-width: 200px;" src="{{ $companyProfile->logo }}" alt="">
                                @else
                                    <img style="max-width: 200px;" src="{{ asset('assets/images/logo-dark.png') }}" alt="">
                                @endif
                        </div>
                        <div class="bg-primary bg-soft">
                            <div class="row">
                                <div class="col-7">
                                    <div class="text-primary p-4">
                                        <h5 class="text-primary">Welcome Back !</h5>
                                        <p>Sign in to continue</p>
                                    </div>
                                </div>
                                <div class="col-5 align-self-end">
                                    <img src="assets/images/profile-img.png" alt="" class="img-fluid">
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-0">



                            <div class="p-2">
                                <form class="form-horizontal" action="{{ route('login') }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text"
                                            class="form-control @error('username') is-invalid @enderror" id="username"
                                            placeholder="Enter username" name="username" value="">
                                        @error('username')
                                            <span class="text-danger">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Password</label>
                                        <div class="input-group auth-pass-inputgroup">
                                            <input type="password" class="form-control" placeholder="Enter password"
                                                aria-label="Password" aria-describedby="password-addon" name="password"
                                                value="">
                                            <button class="btn btn-light " type="button" id="password-addon"><i
                                                    class="mdi mdi-eye-outline"></i></button>
                                        </div>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="remember-check">
                                        <label class="form-check-label" for="remember-check">
                                            Remember me
                                        </label>
                                    </div>
                                    @if ($status)
                                        <div class="g-recaptcha mt-4"
                                            data-sitekey="{{ config('services.recaptcha.key') }}">
                                        </div>
                                        @error('g-recaptcha-response')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    @endif
                                    <div class="mt-3 d-grid">
                                        <button class="btn btn-primary waves-effect waves-light" type="submit">Log
                                            In</button>
                                    </div>

                                    {{-- <div class="mt-4 text-center">
                                        <a href="#" class="text-muted"><i class="mdi mdi-lock me-1"></i> Forgot your
                                            password?</a>
                                    </div> --}}
                                </form>
                            </div>

                        </div>
                    </div>
                    @if ($demoStatus == 'yes')
                        <div class="mt-5 text-center">

                            <div>
                                <p>Don't have an account ? <a href="#" class="fw-medium text-primary">
                                        Signup now </a> </p>
                                <p>©
                                    <script>
                                        document.write(new Date().getFullYear())
                                    </script>Crafted with <i class="mdi mdi-heart text-danger"></i>
                                    by {{ $companyProfile->name }}
                                </p>
                            </div>
                        </div>
                    @endif

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
