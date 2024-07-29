<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Replica</title>
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/replica/images/favicon.ico') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
    <link rel="stylesheet" href="{{ asset('assets/replica/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/replica/css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/replica/css/responsive.css') }}">
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/default.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/libs/toastr/build/toastr.min.css') }}">
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
</head>

<body>
    <header class="main_header_sec">
        @csrf
        <div class="top_bar_section">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="top_cnt_sec">
                            <div class="top_cnt_sec_box">
                                <i class="fa fa-regular fa-envelope"></i>{{ $user->email }}
                            </div>
                            <div class="top_cnt_sec_box">
                                <i class="fa fa-solid fa-phone"></i> {{ $user->userDetail->mobile }}
                            </div>
                            <div class="top_cnt_sec_box">
                                <i class="fa fa-regular fa-user"></i>
                                {{ $user->userDetail->name . ' ' . $user->userDetail->second_name }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <a href="">
                        <div class="main_logo"><img src="{{ asset('assets/replica/images/logo.png') }}" alt="">
                        </div>
                    </a>
                </div>
                <div class="col-md-8 mob_full_sec">
                    <nav class="navbar navbar-expand-lg navbar-light">
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                            data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
                            aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse justify-content-end menubar_sec" id="navbarNav">
                            <ul class="navbar-nav">
                                <li class="nav-item">
                                    <a class="nav-link active" aria-current="page"
                                        href="{{ $replicaurl }}#home">{{ __('replica.home') }}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ $replicaurl }}#plan">{{ __('replica.plan') }}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ $replicaurl }}#about">{{ __('replica.about') }}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ $replicaurl }}#contact">{{ __('replica.contact') }}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link login_btn"
                                        href="{{ route('replica.registerForm', $user->username) }}">{{ __('replica.register') }}</a>
                                </li>
                            </ul>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </header>
    @yield('content')
    <footer class="footer_sec">
        <div class="container">
            <div class="row">
                <div class="col-md-6 footer_lft">
                    {{ date('Y') }}{{ $company->name }}
                </div>
                <div class="col-md-6 fooer_link">
                    <a href="" data-bs-toggle="modal" data-bs-target="#myModal"
                        style="align-content: right;">{{ __('replica.privacy_policy') }}</a>
                    <div id="myModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="myModalLabel">{{ __('replica.privacy_policy') }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body" style="text-align:left;">
                                    @isset($data['policy'])
                                        {!! $data['policy'] !!}
                                    @endisset
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary waves-effect"
                                        data-bs-dismiss="modal">{{ __('replica.close') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#myModal1"
                        style="align-content: right;">{{ __('replica.terms_&_conditions') }}</a>
                    <div id="myModal1" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="myModalLabel">{{ __('replica.terms_&_conditions') }}
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body" style="text-align:left;">
                                    @isset($data['terms'])
                                        {!! $data['terms'] !!}
                                    @endisset
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary waves-effect"
                                        data-bs-dismiss="modal">{{ __('replica.close') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <script src="{{ asset('assets/replica/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('assets/replica/js/bootstrap.bundle.js') }}"></script>
    <script>
        var nextWizardStep = false;
        jQuery(document).ready(function() {
            jQuery('.form-wizard-wrapper').find('.form-wizard-link').click(function() {
                // jQuery('.form-wizard-link').removeClass('active');
                // var innerWidth = jQuery(this).innerWidth();
                // jQuery(this).addClass('active');
                // var position = jQuery(this).position();
                // jQuery('.form-wizardmove-button').css({"left": position.left, "width": innerWidth});
                // var attr = jQuery(this).attr('data-attr');
                // jQuery('.form-wizard-content').each(function(){
                // alert(jQuery(this).find("a").attr('id'));
                // if (jQuery(this).attr('data-tab-content') == attr) {
                // jQuery(this).addClass('show');
                // }else{
                // jQuery(this).removeClass('show');
                // }
                // });
                return false;
            });
            jQuery('.form-wizard-next-btn').click(async function() {
                var next = jQuery(this);
                //alert(jQuery(this).find("a").attr('id'));
                let tabId = $(this).attr('id');
                if (tabId == 'replica-tab-1') {
                    let packageId = $("#Package").val();

                    $(".invalid-feedback").remove();
                    let url = `{{ route('replica.check.package') }}?product_id=${packageId}`;
                    const res = await $.get(`${url}`)
                        .catch((err) => {
                            if (err.status == 422) {
                                nextWizardStep = false;
                                console.log('from error ----');
                                console.log(nextWizardStep)
                                elementvalidationError('Package', err, 'Package');
                            }
                        })
                    if (typeof(res) != 'undefined') {
                        $(this).remove('is-invalid');
                        $(this).add('is-valid');
                        nextWizardStep = true;
                        console.log('from success ----');
                        console.log(nextWizardStep)
                    }

                } else if (tabId == 'replica-tab-2') {
                    let email = $("#email").val();
                    let mobile = $('#mobile').val();
                    let dateOfBirthReplica = $("#dateOfBirthReplica").val();
                    // if (dateOfBirthReplica != null) {
                    $(".invalid-feedback").remove();
                    let dob = dateOfBirthReplica;
                    let url = `{{ route('replica.check.dob') }}?dob=${dob}`;
                    const res = await $.get(`${url}`)
                        .catch((err) => {
                            if (err.status == 422) {
                                nextWizardStep = false;
                                console.log('from error ----');
                                console.log(nextWizardStep)

                                elementvalidationError('dateOfBirthReplica', err, 'datepicker2');
                            }
                        })


                    let data = {
                        mobile: mobile,
                        email: email,
                    }
                    let u = `{{ route('replica.check.mobile') }}`;
                    const valid = await $.post(`${u}`, data)
                        .catch((err) => {
                            if (err.status == 422) {
                                nextWizardStep = false;
                                $('#mobile').addClass('is-invalid');
                                $('#error_mobile').html('');
                                $('#error_mobile').html(err.responseJSON.errors.mobile);
                                $('#email').addClass('is-invalid');
                                $('#error_email').html('');
                                $('#error_email').html(err.responseJSON.errors.email);
                            }
                        })
                    if (typeof(res) != 'undefined' && typeof(valid) != 'undefined') {
                        $(this).remove('is-invalid');
                        $(this).add('is-valid');
                        nextWizardStep = true;
                        console.log('from success ----');
                        console.log(nextWizardStep)
                    }
                    // }
                } else if (tabId == 'replica-tab-3') {
                    $('#username').removeClass('is-valid');
                    $('#username').removeClass('is-invalid');
                    $('#password').removeClass('is-valid');
                    $('#password').removeClass('is-invalid');
                    $('#confirm').removeClass('is-invalid');
                    $('#terms').removeClass('is-invalid');
                    let username = $('#username').val();
                    let password = $('#password').val();
                    let confirmPassword = $('#confirm').val();
                    let terms = $('#flexCheckChecked').is(':checked') ? 'yes' : 'no';
                    let data = {
                        username: username,
                        password: password,
                        confirmPassword: confirmPassword,
                        terms: terms,
                    }
                    let u = `{{ route('replica.check.username') }}`;
                    const res = await $.post(`${u}`, data)
                        //let url      = `/replica/check-username`;
                        //url     = url+`?username=${username}&password=${password}&confirm=${confPass}&terms=${terms}`;

                        .catch((err) => {
                            if (err.status == 422) {
                                nextWizardStep = false;
                                validationErrorsById(err);
                                $('#password').addClass('is-invalid');
                                $('#error_password').html('');
                                $('#error_password').html(err.responseJSON.errors.password);
                                $('#error_password_confirmation').html('');
                                $('#error_password_confirmation').html(err.responseJSON.errors
                                    .confirmPassword);
                                $('#error_terms').html('');
                                $('#error_terms').html(err.responseJSON.errors
                                    .terms);
                                $('#username').addClass('is-valid');
                                $('#password').addClass('is-valid');
                            }
                        });
                    if (typeof(res) != 'undefined') {
                        $('#username').addClass('is-valid');
                        $('#password').addClass('is-valid');
                        nextWizardStep = true;
                    }
                }
                console.log('from outside ----');
                console.log(nextWizardStep)
                jQuery(document).find('.form-wizard-content').each(function() {
                    console.log(nextWizardStep)
                    if (nextWizardStep) {

                        next.parents('.form-wizard-content').removeClass('show');
                        next.parents('.form-wizard-content').next('.form-wizard-content')
                            .addClass('show');
                        if (jQuery(this).hasClass('show')) {
                            var formAtrr = jQuery(this).attr('data-tab-content');
                            jQuery(document).find('.form-wizard-wrapper li a').each(function() {
                                if (jQuery(this).attr('data-attr') == formAtrr) {
                                    jQuery(this).addClass('active');
                                    var innerWidth = jQuery(this).innerWidth();
                                    var position = jQuery(this).position();
                                    jQuery(document).find('.form-wizardmove-button')
                                        .css({
                                            "left": position.left,
                                            "width": innerWidth
                                        });
                                } else {
                                    jQuery(this).removeClass('active');
                                }
                            });
                        }
                    }
                });
            });
            jQuery('.form-wizard-previous-btn').click(function() {
                var prev = jQuery(this);
                prev.parents('.form-wizard-content').removeClass('show');
                prev.parents('.form-wizard-content').prev('.form-wizard-content').addClass('show');
                jQuery(document).find('.form-wizard-content').each(function() {
                    if (jQuery(this).hasClass('show')) {
                        var formAtrr = jQuery(this).attr('data-tab-content');
                        jQuery(document).find('.form-wizard-wrapper li a').each(function() {
                            if (jQuery(this).attr('data-attr') == formAtrr) {
                                jQuery(this).addClass('active');
                                var innerWidth = jQuery(this).innerWidth();
                                var position = jQuery(this).position();
                                jQuery(document).find('.form-wizardmove-button').css({
                                    "left": position.left,
                                    "width": innerWidth
                                });
                            } else {
                                jQuery(this).removeClass('active');
                            }
                        });
                    }
                });
            });
        });
    </script>
    @stack('scripts')
</body>

</html>
