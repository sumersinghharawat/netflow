@extends('layouts.replica')
@section('content')
    <section class="register text-center">
        <div class="container">
            <div class="register_box">
                <div class="row">
                    <div class="col-md-4">
                        <div class="signup_img_lft"><img src="assets/images/signup.png" alt=""></div>
                    </div>
                    <div class="col-md-8">
                        <div class="register_box_head">
                            <h2>{{ __('replica.register_now') }}</h2>
                        </div>
                        <div class="register_box_frm">
                            <form action="{{ route('replica.register') }}" method="post" role="form" id="registerForm"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="form-wizard-wrapper">
                                    <ul class="form-wizard-steps">
                                        <li><a class="form-wizard-link active" href="javascript:;"
                                                data-attr="info"><span>{{ __('replica.sponsor_package_inf') }}</span></a>
                                        </li>
                                        <li><a class="form-wizard-link" href="javascript:;"
                                                data-attr="ads"><span>{{ __('replica.Contact_information') }}</span></a>
                                        </li>
                                        <li><a class="form-wizard-link" href="javascript:;"
                                                data-attr="placement"><span>{{ __('replica.Login_information') }}</span></a>
                                        </li>
                                        <li><a class="form-wizard-link" href="javascript:;"
                                                data-attr="schedule"><span>{{ __('replica.Payment_type') }}</span></a></li>
                                        <li class="form-wizardmove-button"></li>
                                    </ul>
                                    <div class="form-wizard-content-wrapper">
                                        <div class="form-wizard-content show" data-tab-content="info" id="tab-1">
                                            <input type="hidden" name="user" value="{{ $user->username }}"
                                                id="user">
                                            <h6>{{ __('replica.sponsor_package_inf') }}</h6>
                                            <div class="form-row">
                                                <div class="form-column">
                                                    <label for="">{{ __('replica.sponsor_username') }}<span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="sponsor-username"
                                                        value="{{ $user->username }}">
                                                </div>
                                                <div class="form-column">
                                                    <label for="">{{ __('replica.sponsor_full_name') }}<span
                                                            class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" class="form-control"
                                                        value="{{ $user->userDetails->name . ' ' . $user->userDetails->second_name }}"
                                                        readonly>
                                                </div>
                                                @isset($datas['products'])
                                                    <div class="form-column">
                                                        <label>{{ __('replica.product') }} <span
                                                                class="text-danger">*</span></label>
                                                        <select name="product_id"
                                                            id="Package"class="form-select wizard-required" required>
                                                            <option value="">{{ __('replica.select_product') }}</option>
                                                            @foreach ($datas['products'] as $package)
                                                                <option value="{{ $package->id }}"
                                                                    data-item="{{ $package->price }}">
                                                                    {{ $package->name . '(' . round($package->price) . ')' }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <div class="wizard-form-error"></div>
                                                        <div class="error text-danger form-text"></div>
                                                    </div>
                                                @endisset
                                                @if ($datas['modulestatus']->mlm_plan == 'Binary')
                                                    <div class="form-column">
                                                        <label class="">{{ __('replica.position') }}<span
                                                                class="text-danger">*</span></label>
                                                        <select name="position" id="LegPosition"
                                                            class="form-control wizard-required" required>
                                                            <option value="">{{ __('replica.select_leg') }}</option>
                                                            <option value="L" selected>{{ __('replica.left_leg') }}
                                                            </option>
                                                            <option value="R">{{ __('replica.right_leg') }}</option>
                                                        </select>
                                                        <div class="wizard-form-error"></div>
                                                        <div class="error text-danger form-text"></div>
                                                    </div>
                                                @else
                                                    <input type='hidden' value='coming from oc table value' name='position'
                                                        id='position' class="form-control">
                                                @endif
                                                <div class="full-wdth clearfix" id="username-next-btn">
                                                    <a id="replica-tab-1" data-tab="1"
                                                        class="form-wizard-next-btn float-right">{{ __('replica.next') }}</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-wizard-content" data-tab-content="ads">
                                            <h6>{{ __('replica.Contact_information') }}</h6>
                                            <div class="form-row">
                                                @forelse ($datas['customFields'] as $fields)
                                                    <div class="form-column">
                                                        <label
                                                            class="text-capitalize">{{ str_replace('_', ' ', $fields->name) }}
                                                            <span
                                                                class="{{ $fields->required ? 'text-danger' : 'd-none' }}">*</span>
                                                        </label>
                                                        @if ($fields->name == 'country')
                                                            <input type="hidden" name="user"
                                                                value="{{ $user->username }}" id="user">
                                                            <select class="form-control" name="country" id="country">
                                                                @foreach ($datas['countries'] as $country)
                                                                    <option value="{{ $country->id }}"
                                                                        {{ $country->id == auth()->user()->userDetail->country_id ? 'selected' : '' }}>
                                                                        {{ $country->name }}</option>
                                                                @endforeach
                                                                <div class="wizard-form-error"></div>
                                                                <div class="error text-danger form-text"></div>
                                                            </select>
                                                        @elseif ($fields->name == 'state')
                                                            <div id="state">
                                                            </div>
                                                        @elseif ($fields->name == 'gender')
                                                            <select name="gender" id=""
                                                                class="form-control {{ $fields->required ? 'wizard-required' : '' }}">
                                                                <option value="">{{ __('replica.select_gender') }}
                                                                </option>
                                                                <option value="M" selected>{{ __('replica.male') }}
                                                                </option>
                                                                <option value="F">{{ __('replica.female') }}</option>
                                                                <option value="O">{{ __('replica.other') }}</option>
                                                            </select>
                                                            <div class="wizard-form-error"></div>
                                                            <div class="error text-danger form-text"></div>
                                                        @elseif ($fields->name == 'address_line1' || $fields->name == 'address_line2')
                                                            <textarea name="{{ $fields->name }}" id="" cols="30" rows="2" class="form-control">{{ old($fields->name) }}</textarea>
                                                            @error($fields->name)
                                                                <span class="text-danger form-text">{{ $message }}</span>
                                                            @enderror
                                                            <div class="wizard-form-error"></div>
                                                            <div class="error text-danger form-text"></div>
                                                        @elseif($fields->name == 'date_of_birth')
                                                            <div class="input-group" id="datepicker2">
                                                                <input type="date" name="date_of_birth"
                                                                    id="dateOfBirthReplica"
                                                                    class="form-control wizard-required "
                                                                    placeholder="dd M, yyyy" data-date-format="dd mm yyyy"
                                                                    data-date-container='#datepicker2'
                                                                    data-provide="datepicker" data-date-autoclose="true"
                                                                    value="{{ now()->subYear($datas['signupSettings']->age_limit)->format('Y/m/d') }}">

                                                                {{-- <span class="input-group-text"><i class="mdi mdi-calendar"></i></span> --}}
                                                            </div>
                                                        @else
                                                            <input type="{{ $fields->type }}" name="{{ $fields->name }}"
                                                                id="{{ $fields->name }}"
                                                                value="{{ old($fields->name,Str::of($fields->name)->replace('_', ' ')->ucfirst()) }}"
                                                                class="form-control text-field @if ($fields->required) wizard-required @endif">
                                                            @error($fields->name)
                                                                <span class="text-danger form-text"
                                                                    id="error_{{ $fields->name }}">{{ $message }}</span>
                                                            @enderror
                                                            <div class="text-danger wizard-form-error"
                                                                id="error_{{ $fields->name }}"></div>

                                                            {{-- <span class="text-danger form-text" id="error_mobile"></span>
                                                <span class="text-danger form-text" id="error_email"></span> --}}
                                                            <div class="error text-danger form-text"></div>
                                                        @endif
                                                    </div>
                                                @empty
                                                @endforelse
                                                <div class="full-wdth clearfix">
                                                    <a href="javascript:;"
                                                        class="form-wizard-previous-btn float-left">{{ __('replica.previous') }}</a>
                                                    <a href="javascript:;" id="replica-tab-2"
                                                        class="form-wizard-next-btn float-right">{{ __('replica.next') }}</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-wizard-content" data-tab-content="placement">
                                            <h6>{{ __('replica.Login_information') }}</h6>
                                            <div class="form-row">
                                                @if ($datas['usernameConfig']->user_name_type != 'dynamic')
                                                    <div class="form-column">
                                                        <label class="">{{ __('replica.username') }}<span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" name="username" id="username"
                                                            class="form-control wizard-required"
                                                            value="{{ old('username') }}" required>
                                                        <div class="wizard-form-error"></div>
                                                        <div class="error text-danger form-text"></div>
                                                        @error('username')
                                                            <span class="text-danger form-text">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                @endif
                                                <div class="form-column">
                                                    <label class="">{{ __('replica.password') }}<span
                                                            class="text-danger">*</span></label>
                                                    <input type="password" name="password" id="password"
                                                        class="form-control wizard-required" required>
                                                    <input type="file" id="receipt_file" style="display:none"
                                                        name="receipt_file" value="">
                                                    <div class="wizard-form-error"></div>

                                                </div>
                                                <div class="form-column">
                                                    <label class="">{{ __('replica.password') }}<span
                                                            class="text-danger">*</span></label>
                                                    <input type="password" name="password_confirmation" id="confirm"
                                                        class="form-control wizard-required" required>
                                                    <div class="wizard-form-error"></div>
                                                    <div class="error text-danger form-text"></div>
                                                    <span class="text-danger form-text" id="error_password_confirmation">
                                                        @if (isset($message))
                                                            {{ $message }}
                                                        @endif
                                                    </span>

                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input ms-3 mt-lg-2 wizard-required"
                                                        type="checkbox" value="yes" id="flexCheckChecked"
                                                        name="terms">
                                                    <label class="form-check-label" for="flexCheckChecked">
                                                        <a href="#" class="nav-link" data-bs-toggle="modal"
                                                            data-bs-target="#terms">
                                                            {{ __('replica.accept_terms_and_conditions') }}<span
                                                                class="text-danger">*</span></a>
                                                    </label>
                                                    <div class="wizard-form-error"></div>
                                                    <div class="error text-danger form-text" id="error_terms">
                                                        @if (isset($message))
                                                            {{ $message }}
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="full-wdth clearfix">
                                                    <a href="javascript:;"
                                                        class="form-wizard-previous-btn float-left">{{ __('replica.previous') }}</a>
                                                    <a href="javascript:;" id="replica-tab-3"
                                                        class="form-wizard-next-btn float-right">{{ __('replica.next') }}</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-wizard-content" data-tab-content="schedule">
                                            <h5 class="text-center">{{ __('replica.Payment_type') }}</h5>
                                            <h5 class="text-center p-2">{{ __('replica.total_amount') }}: <span
                                                    id="totalAmount"></span>
                                            </h5>
                                            <div class="form-row">
                                                <div class="content-wrapper">
                                                    <input type="hidden" name="totalAmount" id="TotalAmount"
                                                        value="{{ old('totalAmount') }}">
                                                    <div class="col-md-12">
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <div class="nav flex-column nav-pills" id="v-pills-tab"
                                                                    role="tablist" aria-orientation="vertical">
                                                                    @forelse ($datas['paymentGateWay'] as $item)
                                                                        <label for="payment-id-{{ $item->id }}"
                                                                            class="nav-link mb-2 payment-tab"
                                                                            id="v-pills-home-{{ $item->id }}"
                                                                            data-bs-toggle="pill"
                                                                            href="#v-pills-{{ $item->id }}"
                                                                            role="tab"
                                                                            data-method="{{ $item->slug }}"
                                                                            aria-controls="v-pills-{{ $item->id }}"
                                                                            aria-selected="true"
                                                                            onclick="setPaymentMethod({{ $item }}, this)">
                                                                            <input type="radio"
                                                                                id="payment-id-{{ $item->id }}"
                                                                                {{ $loop->index == 0 ? 'checked' : '' }}
                                                                                name="payment_method"
                                                                                value="{{ $item->id }}">{{ $item->name }}
                                                                        </label>
                                                                    @empty
                                                                    @endforelse
                                                                </div>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <div class="tab-content text-muted mt-4 mt-md-0"
                                                                    id="v-pills-tabContent">
                                                                    @forelse ($datas['paymentGateWay'] as $item)
                                                                        <div class="tab-pane fade payment-content"
                                                                            id="v-pills-{{ $item->id }}"
                                                                            role="tabpanel"
                                                                            aria-labelledby="v-pills-{{ $item->id }}">
                                                                            @if ($item->slug == 'bank-transfer')
                                                                                <p>{{ __('replica.bank_details') }}</p>
                                                                                <div class="form-group">
                                                                                    <label
                                                                                        for="reciept">{{ __('replica.select_reciept') }}
                                                                                        <span
                                                                                            class="text-danger">*</span></label>
                                                                                    <input type="file" name="reciept"
                                                                                        id="userfile"
                                                                                        onchange="recieptChange(event)"
                                                                                        class="form-control wizard-required @error('reciept') is-invalid @enderror">
                                                                                    <span
                                                                                        class="text-danger form-text">({{ __('replica.allowed_types_jpg') }})</span>
                                                                                    @error('reciept')
                                                                                        <div class="text-danger">
                                                                                            {{ $message }}
                                                                                        </div>
                                                                                    @enderror
                                                                                </div>
                                                                                <div class="form-group">
                                                                                    <img src="" alt=""
                                                                                        id="recipetImage"
                                                                                        class="img-fluid w-50">
                                                                                </div>
                                                                                <div class="form-group">

                                                                                    <input type="hidden" name="user"
                                                                                        value="{{ $user->username }}"
                                                                                        id="user">

                                                                                    <button type="button"
                                                                                        class="btn btn-primaryupdate_profile_image"
                                                                                        onclick="addReciept();"
                                                                                        id="update_profile_image">{{ __('replica.add_payment_receipt') }}</button>
                                                                                </div>
                                                                            @elseif($item->slug == 'free-joining')
                                                                                {{ __('replica.click_submit_finish_button_to_continue') }}
                                                                            @elseif($item->slug == 'e-pin')
                                                                                <div id="epinDetails">
                                                                                    <div class="alert alert-dark"
                                                                                        role="alert">
                                                                                        {{ __('replica.no_epin_applied') }}
                                                                                    </div>
                                                                                    <div class="row">
                                                                                        <div class="form-group col-md-8">
                                                                                            <input type="text"
                                                                                                name="epin[]"
                                                                                                id="epin"
                                                                                                class="form-control epins"
                                                                                                placeholder="Enter E-pin">
                                                                                        </div>
                                                                                        <div class="form-group col-md-4">
                                                                                            <button type="button"
                                                                                                class="btn btn-primary mt-1"
                                                                                                onclick="checkEpinAvailability()">{{ __('replica.apply') }}
                                                                                            </button>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            @elseif($item->slug == 'e-wallet')
                                                                                <div class="alert alert-warning alert-dismissible fade show d-none"
                                                                                    role="alert" id="EwalletAlert">
                                                                                </div>
                                                                                <div class="row">
                                                                                    <label>
                                                                                        {{ __('replica.username') }}<span>*</span>
                                                                                    </label>
                                                                                    <input class="form-control"
                                                                                        id="tranusername" type="text"
                                                                                        name="tranusername">
                                                                                </div>
                                                                                <br>
                                                                                <div class="row">
                                                                                    <label>
                                                                                        {{ __('replica.transaction_password') }}<span>*</span>
                                                                                    </label>
                                                                                    <input id="tranPassword"
                                                                                        class="form-control"
                                                                                        type="password"
                                                                                        name="tranPassword">
                                                                                </div>
                                                                                <div class="row">
                                                                                    <div class="col-md-6">
                                                                                        <button type="button"
                                                                                            id="check"
                                                                                            class="btn btn-primary"
                                                                                            onclick="checkEwalletavailability()">{{ __('replica.check_availability') }}
                                                                                        </button>
                                                                                    </div>
                                                                                </div>
                                                                                <span id="error">
                                                                                </span>
                                                                            @elseif($item->slug == 'stripe')
                                                                                <div class="p-4 border">
                                                                                    <div class="form-group mt-4 mb-0">
                                                                                        <div id="paymentResponse"
                                                                                            class="text-danger font-italic">
                                                                                        </div>
                                                                                        <label
                                                                                            for="cardnumberInput">{{ __('replica.card_number') }}</label>
                                                                                        <div id="paymentResponse"
                                                                                            class="text-danger font-italic">
                                                                                        </div>
                                                                                        <div id="card_number"
                                                                                            class="field form-control">
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="row">
                                                                                        <div class="col-lg-6">
                                                                                            <div
                                                                                                class="form-group mt-4 mb-0 required">
                                                                                                <label
                                                                                                    for="expirydateInput">{{ __('replica.expiry_date') }}</label>
                                                                                                <div id="card_expiry"
                                                                                                    class="field form-control">
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-lg-6">
                                                                                            <div
                                                                                                class="form-group mt-4 mb-0 required">
                                                                                                <label
                                                                                                    for="cvvcodeInput">{{ __('replica.CVV_code') }}</label>
                                                                                                <div id="card_cvc"
                                                                                                    class="field form-control">
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="row">
                                                                                        <div class="col-md-6">
                                                                                            <button type="submit"
                                                                                                id="stripe"
                                                                                                class="btn btn-primary">{{ __('replica.pay') }}
                                                                                            </button>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            @else
                                                                                <p>
                                                                                    {{ $item->name }}
                                                                                </p>
                                                                            @endif
                                                                        </div>
                                                                    @empty
                                                                    @endforelse
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="full-wdth clearfix">
                                                    <a href="javascript:;"
                                                        class="form-wizard-previous-btn float-left">{{ __('replica.previous') }}</a>
                                                    <button type="submit" id="form-submit-button"
                                                        class="form-wizard-submit float-right d-none">{{ __('replica.finish') }}</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="terms" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{ __('replica.terms_and_conditions') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-justify">
                        {{ $datas['terms']->terms_and_conditions }}
                    </div>
                </div>
            </div>
        </div>
    </section>
    @push('wizardStyle')
        <link rel="stylesheet" href="{{ asset('assets/libs/wizardNew/wizard.css') }}">
        {{-- <link rel="stylesheet" href="{{ asset('assets/libs/chenfengyuan/datepicker/datepicker.min.css') }}"> --}}
    @endpush
    @push('scripts')
        <script src="https://js.stripe.com/v3/"></script>
        {{-- <script src="{{ asset('assets/libs/wizardNew/wizard-script.js') }}"></script> --}}
        {{-- <script src="{{ asset('assets/libs/chenfengyuan/datepicker/datepicker.min.js') }}"></script> --}}
        <script src="{{ asset('assets/js/common.js') }}"></script>
        <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('assets/libs/metismenu/metisMenu.min.js') }}"></script>
        <script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>
        <script src="{{ asset('assets/libs/toastr/build/toastr.min.js') }}"></script>
        <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
        <script src="{{ asset('assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js') }}"></script>


        <script>
            const stripe = Stripe(`{{ stripePublicKey() }}`);

            let elements = stripe.elements();
            let style = {
                base: {
                    color: '#32325D',
                    fontWeight: 500,
                    fontFamily: 'Source Code Pro, Consolas, Menlo, monospace',
                    fontSize: '16px',
                    fontSmoothing: 'antialiased',

                    '::placeholder': {
                        color: '#CFD7DF',
                    },
                    ':-webkit-autofill': {
                        color: '#e39f48',
                    },
                },
                invalid: {
                    color: '#E25950',

                    '::placeholder': {
                        color: '#FFCCA5',
                    },
                }
            };
            let elementClasses = {
                focus: 'focused',
                empty: 'empty',
                invalid: 'invalid',
            };


            let product;
            $(() => {

                var adminusername = $('#user').val();
                let url = "{{ route('replica.state', ':user') }}";
                url = url.replace(':user', adminusername);
                $.ajax({
                    url: url,
                    type: 'get',
                    success: function(response) {
                        $("#state").html(" ");
                        $("#state").html(response.state);
                    }
                });
                $(document).on('change', '#country', function() {
                    let country = $('#country').val();
                    var adminusername = $('#user').val();
                    let url = "{{ route('replica.country.state') }}" + '/' + country + '/' + adminusername;
                    //url = url.replace(':user', adminusername);
                    // url = url.replace(':country', country);
                    $.ajax({
                        url: url,
                        type: 'get',
                        success: function(response) {
                            console.log(response)
                            $('#state').html(' ');
                            $('#state').html(response.state);
                        }
                    });
                })



                $(document).on('change', '#Package', function() {
                    let package = $('#Package').val();
                    let packAmount = $(this).find(':selected').data('item')
                    let totalSum = parseInt(packAmount) + {{ $registerAmount }}
                    $('#totalAmount').html(' ');
                    $('#totalAmount').html(totalSum);
                    $('#TotalAmount').val(' ');
                    $('#TotalAmount').val(totalSum);
                });

                // $(document).on('change', '#LegPosition', function() {
                //     let position = $('#LegPosition').val();
                //     let username = $('#username').val();
                //     let sponsorName = $('#sponsorName').val();
                //     if (sponsorName != '') {
                //         let url = "{{ route('check.legAvailability') }}" + '/' + position + '/' + username;
                //         $.ajax({
                //             url: url,
                //             type: 'get',
                //             success: function(response) {
                //                 console.log(response);
                //             }
                //         });


                //     }
                // });

                $('.payment-tab').first().addClass('active');
                $('.payment-tab').first().attr('aria-selected', 'true');
                $('.payment-content').first().addClass('show active');

                if ($('.payment-tab').first().val() == "stripe") {
                    stripeInitialize();
                }
            });


            $(document).on("focusout", "#sponsor-username", function() {
                let sponsor = $(this).val();
                let url = "{{ route('ajax.replica-sponsorName', ':sponsor') }}"
                url = url.replace(':sponsor', sponsor);
                $.ajax({
                    url: url,
                    type: 'get',
                    success: function(response) {
                        if (response.status == false) {
                            notifyError('Invalid Sponsor Username');
                            nextWizardStep = false;
                            $('#username-next-btn').addClass('d-none');
                        } else {
                            $("#sponsor-full-name").val(response.data.user_detail.name + " " +
                                response.data.user_detail.second_name);
                            $("#sponsorId").val(response.data.id);
                            nextWizardStep = true;
                            $('#username-next-btn').removeClass('d-none');
                        }
                    }
                });
            });
            const recieptChange = (event) => {
                let output = document.getElementById('recipetImage');
                output.src = URL.createObjectURL(event.target.files[0]);
                output.onload = function() {
                    URL.revokeObjectURL(output.src) // free memory
                }
            }


            const setPaymentMethod = (item, el) => {
                $(`#payment-id-${item.id}`).attr('checked', true);
                if (el.dataset.method == "bank-transfer" ||
                    el.dataset.method == 'e-pin' ||
                    el.dataset.method == 'e-wallet' ||
                    el.dataset.method == "stripe") {
                    $('#form-submit-button').addClass('d-none')
                } else {
                    $('#form-submit-button').removeClass('d-none')
                }
                $(`#v-pills-home-${item.id}`).text();
                $('#payment').val(' ')
                $('#payment').val(item.id);
                if (item.slug == 'bank-transfer') {
                    $('#reciept').attr('required', 'true');
                } else if (item.slug == 'stripe') {
                    stripeInitialize();
                }

            }


            const checkEwalletavailability = async () => {
                var adminusername = $('#user').val();
                try {
                    $('#EwalletAlert').addClass('d-none')
                    let url = "{{ route('replica.check.ewallet', ':user') }}";
                    url = url.replace(':user', adminusername);
                    let data = {
                        username: $("#tranusername").val(),
                        password: $('#tranPassword').val(),
                        totalAmount: $('#TotalAmount').val(),
                    }
                    const res = await $.post(`${url}`, data);
                    notifySuccess(res.message);
                    $('#form-submit-button').removeClass('d-none');

                } catch (error) {
                    console.log(error);
                    $('#form-submit-button').addClass('d-none');
                    if (error.status == 422) {
                        notifyError(error.responseJSON.message)
                    }
                }


            }


            const addReciept = () => {
                event.preventDefault();
                var file_data = $('#userfile').prop('files')[0];
                var uname = $('#username').val();
                var adminusername = $('#user').val();

                if (typeof(file_data) === 'undefined') {
                    file_data = ' ';
                    console.log(1);
                }
                var form_data = new FormData();
                form_data.append('reciept', file_data);
                form_data.append('user_name', uname);
                form_data.append('user', adminusername);

                $.ajax({
                    type: 'POST',
                    url: "{{ route('replica.add-payment-receipt') }}",
                    data: form_data,
                    cache: true,
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        notifySuccess(data.success)
                        $('#form-submit-button').removeClass('d-none');

                    },
                    error: function(err) {
                        notifyError(err.responseJSON.message)

                    },
                });
            }

            const checkEpinAvailability = async (remove = 0) => {
                event.preventDefault();
                event.target.disabled = true;
                let epin = $("input[name='epin']").val();
                let usedEpin = [];
                $(".old-epins").map((k, el) => usedEpin[k] = {
                    ['id']: el.dataset.epinid,
                    ['value']: el.value,
                    ['usedAmount']: el.dataset.usedamount
                });
                let url = "{{ route('register.check.epin') }}";
                let params = {
                    sponsor: $('#sponsor-username').val(),
                    sponsorId: $('#sponsorId').val(),
                    epin: epin ? epin : '',
                    epinOld: usedEpin,
                    totalAmount: $('#TotalAmount').val(),
                    packageId: $('#Package').val(),
                    remove: remove
                }
                const res = await $.post(`${url}`, params)
                    .catch((error) => {
                        $('#apply-epin').attr('disabled', false);
                        if (error.status == 422) {
                            notifyError(error.responseJSON.message)
                        }
                    });
                $('#apply-epin').attr('disabled', false);
                if (typeof(res) != "undefined") {
                    $('#epinDetails').html('');
                    $('#epinDetails').html(res.view);
                    if (res.finishStatus) {
                        $('#form-submit-button').removeClass('d-none');
                    } else {
                        $('#form-submit-button').addClass('d-none');
                    }
                }
            }

            const clearEpin = async (id) => {
                $('#epin_' + id).remove();
                checkEpinAvailability(1);
            }

            const stripeInitialize = () => {
                let card = elements.create('cardNumber', {
                    style: style,
                    classe: elementClasses
                });
                card.mount('#card_number');
                let expiry = elements.create('cardExpiry', {
                    style: style,
                    classe: elementClasses
                }).mount('#card_expiry');
                let cvc = elements.create('cardCvc', {
                    style: style,
                    classe: elementClasses
                }).mount('#card_cvc');

                var resultContainer = document.getElementById('paymentResponse');
                card.addEventListener('change', function(event) {
                    if (event.error) {
                        resultContainer.innerHTML = '<p>' + event.error.message + '</p>';
                    } else {
                        resultContainer.innerHTML = '';
                    }
                });

                $('#registerForm').on('submit', function() {
                    event.preventDefault();
                    createToken(card, resultContainer, this);
                })
            }

            const stripeHandleResponse = (status, response) => {
                if (response.error) {
                    $('.error')
                        .removeClass('hide')
                        .find('.alert')
                        .text(response.error.message);
                } else {
                    var token = response['id'];
                    $form.find('input[type=text]').empty();
                    $form.append("<input type='hidden' name='stripeToken' value='" + token + "'/>");
                    $form.get(0).submit();
                }
            }
            const createToken = (cardElement, resultContainer, form) => {
                stripe.createToken(cardElement).then(function(result) {
                    if (result.error) {
                        resultContainer.innerHTML = '<p>' + result.error.message + '</p>';
                    } else {
                        stripeTokenHandler(result.token, form);
                    }
                });
            }
            const stripeTokenHandler = (token, form) => {
                var hiddenInput = document.createElement('input');
                hiddenInput.setAttribute('type', 'hidden');
                hiddenInput.setAttribute('name', 'stripeToken');
                hiddenInput.setAttribute('value', token.id);
                form.appendChild(hiddenInput);
                form.submit();
            }
        </script>
    @endpush
@endsection
