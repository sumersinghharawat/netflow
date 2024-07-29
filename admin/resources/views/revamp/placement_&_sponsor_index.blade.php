<html lang="en">
<head>

    <meta charset="utf-8" />
    <title>Login | Revamp</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <!-- Bootstrap Css -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{ asset('assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />

</head>

<body>
    <div class="account-pages my-5 pt-sm-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card overflow-hidden">
                        <div class="bg-primary bg-soft">
                            <div class="row">
                                <div class="col-7">
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="p-2">
                                @if (request()->routeIs('sponsor-index'))
                                    <form class="form-horizontal" action="{{ route('revamp.insert-sponsor-index') }}" method="get">
                                        <!-- @csrf -->
                                        <div class="mb-3">
                                            <label for="prefix" class="form-label">Prefix</label>
                                            <input type="text" class="form-control @error('prefix') is-invalid @enderror" id="prefix"
                                                placeholder="Enter prefix" name="prefix">
                                                @error('prefix')
                                                    <div class="text-danger">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                        </div>

                                        <div class="mt-3 d-grid">
                                            <button class="btn btn-primary waves-effect waves-light" type="submit">Submit
                                                </button>
                                        </div>
                                    </form>
                                @elseif (request()->routeIs('user-placement'))
                                    <form class="form-horizontal" action="{{ route('revamp.placement') }}" method="get">
                                        <!-- @csrf -->
                                        <div class="mb-3">
                                            <label for="prefix" class="form-label">Prefix</label>
                                            <input type="text" class="form-control @error('prefix') is-invalid @enderror" id="prefix"
                                                placeholder="Enter prefix" name="prefix">
                                                @error('prefix')
                                                    <div class="text-danger">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                        </div>

                                        <div class="mt-3 d-grid">
                                            <button class="btn btn-primary waves-effect waves-light" type="submit">Submit
                                                </button>
                                        </div>
                                    </form>
                                @endif
                                
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

    <!-- App js -->
    <script src="{{ asset('assets/js/app.js') }}"></script>
</body>

</html>
