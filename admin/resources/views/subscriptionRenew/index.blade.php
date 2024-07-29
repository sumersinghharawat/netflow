@extends('layouts.app')
@section('title', 'Package Upgrade')
@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __('profile.subscription_renewal') }}</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    @if (config('mlm.demo_status') == 'yes')
        <p class="bx bx-error-circle"> {{ __('ticket.note_add_on_module') }} </p>
    @endif
    <div class="row">
        <div class="col-md-12">
            <!-- Simple card -->
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">{{ __('profile.package_details') }}</h4>
                    <div class="row d-flex justify-content-center">
                        <div class="col-md-3">
                            <div class="profile-photo-view">
                                @if ($user->userDetail->image)
                                    <img src="{{ asset($user->userDetail->image) }}">
                                @else
                                    <img src="{{ asset('/assets/images/users/avatar-1.jpg') }}">
                                @endif
                            </div>
                        </div>
                        <div class="col-md-9">
                            <input type="hidden" name="username" id="username" value="{{ $user->username }}">
                            <p class="card-text">{{ __('package-upgrade.user_name') }} : {{ $user->username }}</p>
                            <p class="card-text">{{ __('package-upgrade.sponsor_name') }} : {{ $user->sponsor->username }}</p>
                            <p class="card-text">{{ __('package-upgrade.subscription_end') }}
                                : <kbd>{{ Carbon\Carbon::parse($user->product_validity)->format('d M Y  g:i:s A') }}</kbd></p>
                            <p class="card-text">{{ __('package-upgrade.renewal_charge') }} : {{ $currency }}
                                {{ formatCurrency($product_amount) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div><!-- end col -->
    </div>
    <!-- end row -->

    <div class="row">
        <div class="col-md-12">

            <!-- Simple card -->
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">{{ __('profile.reactivation_optn') }}</h4>
                    <div class="row">
                        <h5 class="text-center text-black p-2">{{ __('common.totalAmount') }} : {{ $currency }}
                            {{ formatCurrency($product_amount) }}<span id="totalAmount"></span>
                        </h5>
                        <form action="{{ route('subscriptions.renew.submit') }}" id="renewForm" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <div class="content-wrapper">
                                    <input type="hidden" name="totalAmount" id="TotalAmount"
                                        value="{{ $product_amount }}">
                                    <input type="hidden" name="product_id" id="Package"
                                        value="{{ $user->package->id ?? $user->package->product_id }}">
                                    <input type="hidden" name="user_id" id="user_id" value={{ $user->id }}>
                                    <input type="hidden" name="type_renew" id="typeRenew" value="renew">
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist"
                                                    aria-orientation="vertical">
                                                    @forelse ($paymentGateways as $item)
                                                        <span for="payment-id-{{ $item->id }}" style="cursor: pointer"
                                                            class="nav-link mb-2 payment-tab"
                                                            id="v-pills-home-{{ $item->id }}" data-bs-toggle="pill"
                                                            href="#v-pills-{{ $item->id }}" role="tab"
                                                            data-method="{{ $item->slug }}"
                                                            aria-controls="v-pills-{{ $item->id }}"
                                                            aria-selected="true"
                                                            onclick="setPaymentMethod({{ $item }}, this)">
                                                            <input type="radio" id="payment-id-{{ $item->id }}"
                                                                {{ $loop->index == 0 ? 'checked' : '' }}
                                                                name="payment_method"
                                                                value="{{ $item->id }}">{{ $item->name }}
                                                        </span>
                                                    @empty
                                                    @endforelse
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="tab-content text-muted mt-4 mt-md-0" id="v-pills-tabContent">
                                                    @forelse ($paymentGateways as $item)
                                                        <div class="tab-pane fade payment-content"
                                                            id="v-pills-{{ $item->id }}" role="tabpanel"
                                                            aria-labelledby="v-pills-{{ $item->id }}">
                                                            @if ($item->slug == 'bank-transfer')
                                                                <p>Bank Details</p>
                                                                <div class="form-group">
                                                                    <label for="reciept">{{ __('common.select_receipt') }}
                                                                        <span class="text-danger">*</span></label>
                                                                    <input type="file" name="reciept" id="userfile"
                                                                        onchange="recieptChange(event)"
                                                                        class="form-control wizard-required @error('reciept') is-invalid @enderror">
                                                                    <span class="text-danger form-text">
                                                                        ({{ __('common.allowed_types') }} jpg|jpeg|png)
                                                                    </span>
                                                                    @error('reciept')
                                                                        <div class="text-danger">
                                                                            {{ $message }}
                                                                        </div>
                                                                    @enderror
                                                                </div>
                                                                <div class="form-group">
                                                                    <img src="" alt="" id="recipetImage"
                                                                        class="img-fluid w-50">
                                                                </div>
                                                                <div class="form-group">
                                                                    <button type="button"
                                                                        class="btn btn-primary update_profile_image"
                                                                        onclick="addReciept();"
                                                                        id="update_profile_image">{{ __('package-upgrade.add_payment_receipt') }}</button>
                                                                </div>
                                                            @elseif($item->slug == 'free-joining')
                                                                click submit finish button to continue
                                                            @elseif($item->slug == 'e-pin')
                                                                <div id="epinDetails">
                                                                    <div class="alert alert-dark" role="alert">
                                                                        {{ __('register.no_epin_applied') }}
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="form-group col-md-8">
                                                                            <input type="text" name="epin"
                                                                                id="epin"
                                                                                onkeyup="activateApplyEpin()"
                                                                                class="form-control epins"
                                                                                placeholder="Enter E-pin">
                                                                        </div>
                                                                        <div class="form-group col-md-4">
                                                                            <button type="button" disabled
                                                                                class="btn btn-primary" id="apply-epin"
                                                                                onclick="checkEpinAvailability()">{{ __('common.apply') }}
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @elseif($item->slug == 'e-wallet')
                                                                <div class="alert alert-warning alert-dismissible fade show d-none"
                                                                    role="alert" id="EwalletAlert">
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <label>
                                                                            {{ __('common.username') }}<span>*</span>
                                                                        </label>
                                                                        <input class="form-control"
                                                                            id="transaction_username" type="text"
                                                                            name="tranusername">
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <label>
                                                                            {{ __('common.transaction_password') }}<span>*</span>
                                                                        </label>
                                                                        <input id="tranPassword" class="form-control"
                                                                            type="password" name="tranPassword">
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <button type="button" id="check"
                                                                            class="btn btn-primary mt-3"
                                                                            onclick="checkEwalletavailability()">{{ __('register.check_availavblity') }}
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                                <span id="error">
                                                                </span>
                                                            @elseif($item->slug == 'stripe')
                                                                <div class="p-4 border">
                                                                    <div class="form-group mt-4 mb-0">
                                                                        <div id="paymentResponse"
                                                                            class="text-danger font-italic"></div>
                                                                        <label for="cardnumberInput">Card Number</label>
                                                                        <div id="paymentResponse"
                                                                            class="text-danger font-italic"></div>
                                                                        <div id="card_number" class="field form-control">
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-lg-6">
                                                                            <div class="form-group mt-4 mb-0 required">
                                                                                <label for="expirydateInput">Expiry
                                                                                    date</label>
                                                                                <div id="card_expiry"
                                                                                    class="field form-control"></div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-6">
                                                                            <div class="form-group mt-4 mb-0 required">
                                                                                <label for="cvvcodeInput">CVV Code</label>
                                                                                <div id="card_cvc"
                                                                                    class="field form-control"></div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <button type="submit" id="stripe"
                                                                                class="btn btn-primary">Pay
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @elseif($item->slug == 'paypal')
                                                                <!-- Set up a container element for the button -->
                                                                <input type="hidden" name="paypalOrderId" value="" id="paypalOrderId">
                                                                <div id="paypal-button-container"></div>
                                                                <div id="paypalToken"></div>
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
                                <div class="form-group text-center">
                                    <button type="submit" id="form-submit-button"
                                        class="btn btn-primary d-none">{{ __('common.finish') }}</button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>

        </div><!-- end col -->
    </div>


@endsection
@push('scripts')
    <script src="https://js.stripe.com/v3/"></script>
    <script src="{{ asset('assets/libs/chenfengyuan/datepicker/datepicker.min.js') }}"></script>

    @php
        $isPaypal = $paymentGateways->where('slug', 'paypal')->first();
        $paypalConfig = getPaypalConfigs();
    @endphp
    @if ($isPaypal && $isPaypal->status && $isPaypal->registration)
        <script src="https://www.paypal.com/sdk/js?client-id={{ $paypalConfig['client_id'] }}&currency=USD"></script>
    @endif

    <script>
         $(() => {
            $(window).keydown(function(event) {
                if (event.keyCode == 13) {
                    event.preventDefault();
                    return false;
                }
            });

            $('.date-picker-dob').datepicker({
                format: 'mm/dd/yyyy',
            });
            $('.payment-tab').first().addClass('active');
            $('.payment-tab').first().attr('aria-selected', 'true');
            $('.payment-content').first().addClass('show active');

            if ($('.payment-tab').first().val() == "stripe") {
                stripeInitialize();
            } else if ($('.payment-tab').first().val() == "paypal") {
                $('#paypal-button-container').empty();
                payPalPay();
            }

        });

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

        recieptChange = (event) => {
            let output = document.getElementById('recipetImage');
            output.src = URL.createObjectURL(event.target.files[0]);
            output.onload = function() {
                URL.revokeObjectURL(output.src) // free memory
            }
        }

        setPaymentMethod = (item, el) => {
            $(`#payment-id-${item.id}`).attr('checked', true);
            if (el.dataset.method == "bank-transfer" ||
                el.dataset.method == 'e-pin' ||
                el.dataset.method == 'e-wallet' ||
                el.dataset.method == "stripe" ||
                el.dataset.method == "paypal") {

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
            } else if (item.slug == 'paypal') {
                $('#paypal-button-container').empty();
                payPalPay();
            }

        }

        const checkEwalletavailability = async () => {
            $("#transaction_username").removeClass('is-invalid');
            $('#sponsor-username').removeClass('is-invalid');
            $('#tranPassword').removeClass('is-invalid');
            $('#TotalAmount').removeClass('is-invalid');
            let url = "{{ route('cart.check.ewallet') }}";
            let data = {
                transaction_username: $("#transaction_username").val(),
                sponsor: $('#sponsor-username').val(),
                tranPassword: $('#tranPassword').val(),
                totalAmount: $('#TotalAmount').val(),
            }
            const res = await $.post(`${url}`, data).catch(error => {
                console.log(error);
                $('#form-submit-button').addClass('d-none');
                if (error.status == 422) {
                    validationErrorsById(error);
                }
            });
            if (typeof(res) != 'undefined') {
                if (res.status) {
                    notifySuccess(res.message);
                    $('#EwalletAlert').removeClass('d-block').addClass('d-none').html('');
                    $("#transaction_username").removeClass('is-invalid');
                    $('#sponsor-username').removeClass('is-invalid');
                    $('#tranPassword').removeClass('is-invalid');
                    $('#TotalAmount').removeClass('is-invalid');
                    $('#form-submit-button').removeClass('d-none');
                    $('.invalid-feedback').remove();

                } else {
                    $('#EwalletAlert').removeClass('d-none').addClass('d-block').html(res.message);
                    $('.invalid-feedback').remove();

                }
            }
        }

        const activateApplyEpin = () => {
            if (event.target.value) {
                $('#apply-epin').attr('disabled', false);
            } else {
                $('#apply-epin').attr('disabled', true);
            }
        }

        const addReciept = async () => {
            event.preventDefault()
            var file_data = $('#userfile').prop('files')[0];
            var uname = $('#username').val();
            console.log(typeof(file_data));
            if (typeof(file_data) === 'undefined') {
                file_data = ' ';
                console.log(1);
            }
            var form_data = new FormData();
            form_data.append('reciept', file_data);
            form_data.append('user_name', uname);

            $.ajax({
                type: 'POST',
                url: "{{ route('renew.add-payment-receipt') }}",
                data: form_data,
                cache: true,
                contentType: false,
                processData: false,
                success: function(data) {
                    notifySuccess(data.success)
                    $('#form-submit-button').removeClass('d-none')

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
            let url = "{{ route('cart.check.epin') }}";
            let params = {
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
            $('#epin_' + id).val('');
            checkEpinAvailability(1);
        }

        stripeInitialize = () => {
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

            $('#renewForm').on('submit', function() {
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

        const payPalPay = () => {
            paypal.Buttons({
                // Call your server to set up the transaction
                createOrder: function(data, actions) {
                    return $.post(`{{ route('paypal.create') }}`,
                            JSON.stringify({
                                'type_renew': true,
                                'user_id': "{{ auth()->user()->id }}",
                                'amount': $("#TotalAmount").val(),
                                'prefix' : "{{ config('database.connections.mysql.prefix') }}",
                                'package_id' : "{{ $user->package->id }}"
                            })
                        )
                        .then(function(res) {
                            return JSON.stringify(res);
                        })
                        .then(function(orderData) {
                            let data = JSON.parse(orderData);
                            return data.id;
                        });
                },

                // Call your server to finalize the transaction
                onApprove: function(data, actions) {
                    let data1 = JSON.stringify(data);
                    let data2 = JSON.parse(data1);
                    return fetch(`{{ route('paypal.capture') }}`, {
                        method: 'POST',
                        body: JSON.stringify({
                            orderId: data2.orderID,
                            payment_gateway_id: $("#payapalId").val(),
                            user_id: "{{ auth()->user()->id }}",
                            prefix : "{{ config('database.connections.mysql.prefix') }}",
                        })
                    }).then(function(res) {
                        return res.json();
                    }).then(function(orderData) {
                        $('#paypalOrderId').val(orderData.id);
                        $('#renewForm').submit();
                    }).catch( (err) => {
                        console.log(err);
                    });
                },

            }).render('#paypal-button-container');
        }
    </script>
@endpush
