<!doctype html>
<html lang="en">

<head>

    <meta charset="utf-8" />
    <title>Company Name | Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Level up your network marketing business with Infinite MLM Software." name="description" />
    <meta content="Infinitemlmsoftware.com" name="author" />
    <!-- App favicon -->
    {{-- @if($favicon)
        <link rel="shortcut icon" href="{{ $favicon }}">
        <!-- <img src="{{ $favicon }}" class="w-50" id="shrinkLogo"> -->
    @else --}}
        <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">
    {{-- @endif --}}

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
        box-shadow: 0 0.75rem 2.5rem rgb(18 38 63 / 12%);
        border-radius: 20px;
    }

    .bg-primary.bg-soft {
        background-color: #dbf4ff !important;
    }

    .form-control {
        border: 1px solid #e9e9e9;
    }
    .alreadyRegsitrdBox{text-align: center}
    .alreadyRgtrdCntbtn{
        background-color: #218beb;
        padding: 8px 20px;
        display: inline-block;
        border-radius: 30px;
        text-align: center;
        color: #fff;
        font-size: 15px;
        border: 0;
box-shadow: 0px 10px 20px #218beb47;
    }
    .startNewAccbtn{
        width: auto;
        padding: 10px 20px;
        background-color: #fff;
        text-align: center;
        display: inline-block;
        border-radius: 30px;
        color: #242424;
        font-size: 15px;
        border: 0;
        box-shadow: 0px 10px 20px #d7d7d7;

    }
</style>
</head>

<body>
    <div class="account-pages my-5 pt-sm-5">
        @php
            $status = session()->has('auth_attempt_status') ? true : false;
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
                            <img style="max-width: 200px;" src="{{ asset('assets/images/logo-dark.png') }}"
                                alt="">
                        </div>
                        <div class="bg-primary bg-soft" style="display: none">
                            <div class="row">
                                <div class="col-7">
                                    <div class="text-primary p-4">
                                        <h5 class="text-primary">Already Registered !</h5>
                                        <p>Continue with existing Account?</p>
                                        {{-- <a class="btn btn-primary waves-effect waves-light" href="/auth/google"><span class="mdi mdi-google"></span>  SignUp With Google </a> --}}
                                    </div>
                                </div>
                                <div class="col-5 align-self-end">
                                    <img src="{{ asset('assets/images/profile-img.png') }}" alt="" class="img-fluid">
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="alreadyRegsitrdBox">
                            <h3>Already Registered !</h3>
                            <p>Continue with existing Account?</p>

                            <div class="p-2">
                                <form class="form-horizontal" action="{{ route('continue.old') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="email" value="<?php echo $email ?>">
                                    <button type="submit" class="alreadyRgtrdCntbtn waves-effect waves-light" >Continue</button>

                               </form>
                            </div>
                            <div class="p-2">
                               <form class="form-horizontal" action="{{ route('start.new') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="email" value="<?php echo $email ?>">
                                    <button type="submit" class="startNewAccbtn waves-effect waves-light" >Start New Account</button>
                               </form>
                                {{-- <form class="form-horizontal" action="{{ route('login') }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Username/Email</label>
                                        <input type="text"
                                            class="form-control @error('username') is-invalid @enderror" id="username"
                                            placeholder="Enter username or Email" name="username"
                                            value="{{ $username ? $username : '' }}">
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
                                                value="{{ $username ? $username . '123' : '' }}">
                                            <button class="btn btn-light " type="button" id="password-addon"><i
                                                    class="mdi mdi-eye-outline"></i></button>
                                        </div>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="remember-check">
                                        <label class="form-check-label" for="remember-check">
                                            Remember me
                                        </label>
                                        <a href="http://user.infinitemlmsoftware.com" class="float-end">User Login</a>
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
                                </form> --}}
                            </div>

                        </div>

                        </div>
                    </div>
                    @php
                        $demoStatus = config('mlm.demo_status');
                    @endphp

                    @if ($demoStatus == 'yes')
                        <div class="mt-5 text-center">

                            <div>
                                <p>Don't have an account ? <a href="https://infinitemlmsoftware.com/"
                                        class="fw-medium text-primary">
                                        Signup now </a> </p>
                                <p>©
                                    <script>
                                        document.write(new Date().getFullYear())
                                    </script>Crafted with <i class="mdi mdi-heart text-danger"></i>
                                    by R&D
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
