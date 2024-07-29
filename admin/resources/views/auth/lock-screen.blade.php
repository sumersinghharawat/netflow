<!doctype html>
<html lang="en">
    <head>

        <meta charset="utf-8" />
        <title>Company Name | Login</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Level up your network marketing business with Infinite MLM Software." name="description" />
        <meta content="Infinitemlmsoftware.com" name="author" />
        <!-- App favicon -->

        @if(isset($favicon) && $favicon)
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
                                <!-- <img style="max-width: 200px;" src="{{ asset('assets/images/logo-dark.png') }}"
                                    alt=""> -->
                                    @if (isFileExists($companyProfile->logo) && $companyProfile->logo != null)
                                        <img style="max-width: 200px;" src="{{ $companyProfile->login_logo }}" alt="">
                                    @else
                                        <img style="max-width: 200px;" src="{{ asset('assets/images/logo-dark.png') }}" alt="">
                                    @endif
                            </div>
                            <div class="bg-primary bg-soft">
                                <div class="row">
                                    <div class="col-7">
                                        <div class="text-primary p-4">
                                            <h5 class="text-primary">Lock screen</h5>
                                            <p>Enter your password to unlock the screen!</p>
                                        </div>
                                    </div>
                                    <div class="col-5 align-self-end">
                                        @if($user['image'])
                                            <img style="max-width: 200px;" src="{{ asset('assets/images/profile-img.png') }}"
                                                alt="">
                                        @else
                                            <img src="{{ asset('assets/images/profile-img.png') }}" alt="" class="img-fluid">
                                        @endif

                                    </div>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <div style="display:none">
                                    <a href="index.html">
                                        <div class="avatar-md profile-user-wid mb-4">
                                            <span class="avatar-title rounded-circle bg-light">
                                                <img src="assets/images/logo.svg" alt="" class="rounded-circle" height="34">
                                            </span>
                                        </div>
                                    </a>
                                </div>
                                <div class="p-2">
                                    <form class="form-horizontal" action="{{ route('login') }}" method="POST">
                                        @csrf
                                        <div class="user-thumb text-center mb-4">
                                            <img src="assets/images/users/avatar-1.jpg" class="rounded-circle img-thumbnail avatar-md" alt="thumbnail">
                                            <h5 class="font-size-15 mt-3">{{ $user['fullname'] }}</h5>
                                        </div>

                                        <input type="hidden"
                                            class="form-control @error('username') is-invalid @enderror" id="username"
                                            placeholder="Enter username" name="username"
                                            value="{{ $user['username'] ? $user['username'] : '' }}">

                                        <div class="input-group auth-pass-inputgroup">
                                            <input type="password" class="form-control" placeholder="Enter password"
                                                aria-label="Password" aria-describedby="password-addon" name="password">
                                            <button class="btn btn-light " type="button" id="password-addon"><i
                                                    class="mdi mdi-eye-outline"></i></button>

                                        </div>
                                        @error('password')
                                            <span class="text-danger">
                                                {{ $message }}
                                            </span>
                                        @enderror

                                        <div class="mt-3 d-grid">
                                            <button class="btn btn-primary waves-effect waves-light" type="submit">Unlock</button>
                                        </div>

                                    </form>
                                </div>

                            </div>
                        </div>
                        <div class="mt-5 text-center">
                            <p>Not you ? return <a href="{{ route('login') }}" class="fw-medium text-primary"> Sign In </a> </p>
                            <p>©
                                <script>
                                    document.write(new Date().getFullYear())
                                </script>Crafted with <i class="mdi mdi-heart text-danger"></i>
                                by R&D
                            </p>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- JAVASCRIPT -->
        <script src="assets/libs/jquery/jquery.min.js"></script>
        <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="assets/libs/metismenu/metisMenu.min.js"></script>
        <script src="assets/libs/simplebar/simplebar.min.js"></script>
        <script src="assets/libs/node-waves/waves.min.js"></script>

        <!-- App js -->
        <script src="assets/js/app.js"></script>

    </body>

<!-- Mirrored from themesbrand.com/skote/layouts/auth-lock-screen.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 18 Mar 2022 06:41:22 GMT -->
</html>
