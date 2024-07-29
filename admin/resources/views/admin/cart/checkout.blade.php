@extends('layouts.app')
@section('content')
    <style>
        .contact_addrs_slct {
            width: 100%;
            height: 100%;
            position: absolute;
            z-index: 1;
            opacity: 0;
            top: 0;
            left: 0;
        }

        .address-card.active {
            background-color: rgba(52, 195, 143, .25) !important;
        }
    </style>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0 font-size-18">{{ __('cart.checkout') }}</h4>
            </div>
        </div>
    </div>
    <p class="bx bx-error-circle"> {{ __('ticket.note_add_on_module') }} </p>
    <div class="checkout-tabs">
        <div class="row">
            <div class="col-xl-2 col-sm-3">
                <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <a class="nav-link active" id="v-pills-shipping-tab" data-bs-toggle="pill" href="#v-pills-shipping"
                        role="tab" aria-controls="v-pills-shipping" aria-selected="true">
                        <i class="bx bxs-truck d-block check-nav-icon mt-4 mb-2"></i>
                        <p class="fw-bold mb-4">{{ __('cart.contact_info') }}</p>
                    </a>

                    <a class="nav-link" id="v-pills-confir-tab" data-bs-toggle="pill" href="#v-pills-confir" role="tab"
                        aria-controls="v-pills-confir" aria-selected="false">
                        <i class="bx bx-badge-check d-block check-nav-icon mt-4 mb-2"></i>
                        <p class="fw-bold mb-4">{{ __('cart.order_info') }}</p>
                    </a>
                    <a class="nav-link" id="v-pills-payment-tab" data-bs-toggle="pill" href="#v-pills-payment"
                        role="tab" aria-controls="v-pills-payment" aria-selected="false">
                        <i class="bx bx-money d-block check-nav-icon mt-4 mb-2"></i>
                        <p class="fw-bold mb-4">{{ __('cart.payment_info') }}</p>
                    </a>
                </div>
            </div>
            <div class="col-xl-10 col-sm-9">
                <div class="card">
                    <div class="card-body">
                        <div class="tab-content" id="v-pills-tabContent">
                            <div class="tab-pane fade show active" id="v-pills-shipping" role="tabpanel"
                                aria-labelledby="v-pills-shipping-tab">
                                <h4 class="card-title">{{ __('cart.contact_information') }}</h4>
                                <p class="card-title-desc">{{ __('cart.fill_all') }}</p>
                                <div class="user-address">
                                    <div class="row">
                                        @forelse($user_address->chunk(3) as $chunk)
                                            @foreach ($chunk as $item)
                                                <div class="col-md-4" id="address{{ $item->id }}">
                                                    <div class="modal-dialog " role="document">
                                                        <div class="modal-content address-card @if ($item->is_default) active @endif"
                                                            id="card-{{ $item->id }}">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">{{ $item->name }}</h5>
                                                                <form
                                                                    action="{{ route('cart.address-delete', $item->id) }}"
                                                                    method="post">
                                                                    <noscript>
                                                                        @csrf
                                                                        @method('delete')
                                                                    </noscript>
                                                                    <button type="submit" class="btn-close" id="sa-warning"
                                                                        onclick="deleteAddress({{ $item->id }})"
                                                                        aria-label="Close">
                                                                    </button>
                                                                </form>
                                                            </div>
                                                            <div class="modal-body">
                                                                <input type="radio" name="addressdata"
                                                                    class="contact_addrs_slct" onclick="setAddress()"
                                                                    value="{{ $item->id }}">
                                                                <p>{{ $item->address }}</p>
                                                                <p>{{ $item->zip }}</p>
                                                                <p>{{ $item->city }}</p>
                                                                <p>{{ $item->mobile }}</p>
                                                            </div>
                                                            @if (!$item->is_default)
                                                                <div class="modal-footer">
                                                                    <button type="button"
                                                                        id="default-btn{{ $item->id }}"
                                                                        class="btn btn-secondary default-btn waves-effect waves-light"
                                                                        onclick="makeDefault({{ $item->id }})">{{ __('cart.set_default') }}</button>
                                                                </div>
                                                            @endif

                                                        </div><!-- /.modal-content -->
                                                    </div>

                                                    <!-- end col -->
                                                </div>
                                            @endforeach
                                        @empty
                                            <p>{{ __('cart.no_address') }}</p>
                                        @endforelse
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="card">
                                        <div class="card-body">
                                            <div>
                                                <button type="button" class="btn btn-primary waves-effect waves-light"
                                                    data-bs-toggle="offcanvas" data-bs-target="#addressModal"
                                                    aria-controls="offcanvasRight">{{ __('cart.add_new_address') }}</button>
                                            </div> <!-- end preview-->
                                        </div>
                                        <!-- end card body -->
                                    </div>
                                    <!-- end card -->
                                </div>
                            </div>

                            <div class="tab-pane fade" id="v-pills-payment" role="tabpanel"
                                aria-labelledby="v-pills-payment-tab">
                                {{-- <form action="{{ route('checkout.submit') }}" method="post" enctype='multipart/form-data'>
                                     @csrf --}}
                                <form action="{{ route('checkout.submit') }}" id="renewForm" method="post"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group">
                                        <div class="content-wrapper">
                                            <input type="hidden" name="user_id" id="user_id" value=>
                                            <input type="hidden" name="type_cart" id="typeCart" value="cart">
                                            <div class="col-12">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="nav flex-column nav-pills" id="v-pills-tab"
                                                            role="tablist" aria-orientation="vertical">
                                                            @forelse ($paymentGateways as $item)
                                                                <span for="payment-id-{{ $item->id }}"
                                                                    style="cursor: pointer"
                                                                    class="nav-link mb-2 payment-tab"
                                                                    id="v-pills-home-{{ $item->id }}"
                                                                    data-bs-toggle="pill"
                                                                    href="#v-pills-{{ $item->id }}" role="tab"
                                                                    data-method="{{ $item->slug }}"
                                                                    aria-controls="v-pills-{{ $item->id }}"
                                                                    aria-selected="true"
                                                                    onclick="setPaymentMethod({{ $item }}, this)">
                                                                    <input type="radio"
                                                                        id="payment-id-{{ $item->id }}"
                                                                        {{ $loop->index == 0 ? 'checked' : '' }}
                                                                        name="payment_method"
                                                                        value="{{ $item->id }}">@if($item->slug == 'free-joining'){{ __('cart.cash_on_delivery') }}@else{{ $item->name }}@endif
                                                                </span>
                                                            @empty
                                                            @endforelse
                                                        </div>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <div class="tab-content text-muted mt-4 mt-md-0"
                                                            id="v-pills-tabContent">
                                                            @forelse ($paymentGateways as $item)
                                                                <div class="tab-pane fade payment-content"
                                                                    id="v-pills-{{ $item->id }}" role="tabpanel"
                                                                    aria-labelledby="v-pills-{{ $item->id }}">
                                                                    @if ($item->slug == 'bank-transfer')
                                                                        <p>{{ __('common.bank_details') }}</p>
                                                                        <div class="form-group">
                                                                            <label for="reciept">{{ __('common.select_reciept') }}
                                                                                <span class="text-danger">*</span></label>
                                                                            <input type="file" name="reciept"
                                                                                id="userfile"
                                                                                onchange="recieptChange(event)"
                                                                                class="form-control wizard-required @error('reciept') is-invalid @enderror">
                                                                            <span class="text-danger form-text">( {{ __('common.allowed_types') }} jpg|jpeg|png)</span>
                                                                            @error('reciept')
                                                                                <div class="text-danger">
                                                                                    {{ $message }}
                                                                                </div>
                                                                            @enderror
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <img src="" alt=""
                                                                                id="recipetImage" class="img-fluid w-50">
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <button type="button"
                                                                                class="btn btn-primary update_profile_image"
                                                                                onclick="addReciept();"
                                                                                id="update_profile_image">{{ __('cart.add_payment_receipt') }}</button>
                                                                        </div>
                                                                    @elseif($item->slug == 'free-joining')
                                                                        {{ __('cart.click_finish') }}
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
                                                                                        class="btn btn-primary"
                                                                                        id="apply-epin"
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
                                                                                    id="transaction_username"
                                                                                    type="text" name="tranusername">
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <label>
                                                                                    {{ __('common.transaction_password') }}<span>*</span>
                                                                                </label>
                                                                                <input id="tranPassword"
                                                                                    class="form-control" type="password"
                                                                                    name="tranPassword">
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
                                                                    @elseif($item->slug == 'purchase_wallet')
                                                                        <div class="alert alert-warning alert-dismissible fade show d-none"
                                                                            role="alert" id="purWalletAlert">
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-md-12">
                                                                                <label>
                                                                                    {{ __('common.username') }}<span>*</span>
                                                                                </label>
                                                                                <input class="form-control"
                                                                                    id="pwalletUsername"
                                                                                    type="text" name="pwalletUsername">
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <label>
                                                                                    {{ __('common.transaction_password') }}<span>*</span>
                                                                                </label>
                                                                                <input id="pwalletPassword"
                                                                                    class="form-control" type="password"
                                                                                    name="pwalletPassword">
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-md-6">
                                                                                <button type="button" id="check"
                                                                                    class="btn btn-primary mt-3"
                                                                                    onclick="checkPurchaseWalletAvailability()">{{ __('register.check_availavblity') }}
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
                                                                                <label for="cardnumberInput">{{ __('common.card_number') }}</label>
                                                                                <div id="paymentResponse"
                                                                                    class="text-danger font-italic"></div>
                                                                                <div id="card_number"
                                                                                    class="field form-control">
                                                                                </div>
                                                                            </div>
                                                                            <div class="row">
                                                                                <div class="col-lg-6">
                                                                                    <div
                                                                                        class="form-group mt-4 mb-0 required">
                                                                                        <label for="expirydateInput">{{ __('common.expiry_date') }}</label>
                                                                                        <div id="card_expiry"
                                                                                            class="field form-control">
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-lg-6">
                                                                                    <div
                                                                                        class="form-group mt-4 mb-0 required">
                                                                                        <label for="cvvcodeInput">{{ __('common.cvv_code') }}</label>
                                                                                        <div id="card_cvc"
                                                                                            class="field form-control">
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="row">
                                                                                <div class="col-md-6">
                                                                                    <button type="submit" id="stripe"
                                                                                        class="btn btn-primary">{{ __('common.pay') }}
                                                                                    </button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    @elseif($item->slug == 'paypal')
                                                                        <!-- Set up a container element for the button -->
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
                                        <div class="row mt-4">
                                            <div class="col-sm-6 form-group">
                                                <a href="{{ route('products.view') }}"
                                                    class="btn text-muted d-none d-sm-inline-block btn-link">
                                                    <i class="mdi mdi-arrow-left me-1"></i>
                                                    {{ __('cart.back_to_shopping_cart') }} </a>
                                            </div> <!-- end col -->
                                            <div class="col-sm-6">
                                                <button type="submit" id="form-submit-button"
                                                    class="btn btn-primary d-none">{{ __('common.finish') }}</button>
                                            </div> <!-- end col -->
                                        </div> <!-- end row -->

                                    </div>
                            </div>
                            <div class="tab-pane fade" id="v-pills-confir" role="tabpanel"
                                aria-labelledby="v-pills-confir-tab">
                                <div class="card shadow-none border mb-0">
                                    <div class="card-body">
                                        <h4 class="card-title mb-4">{{ __('cart.order_summary') }}</h4>

                                        <div class="table-responsive">
                                            <table class="table align-middle mb-0 table-nowrap">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th scope="col">{{ __('cart.product') }}</th>
                                                        <th scope="col">{{ __('cart.price') }}</th>
                                                        <th scope="col">{{ __('cart.quantity') }}</th>
                                                        <th scope="col">{{ __('cart.total') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($cart_items as $item)
                                                        <input type='hidden' name="product_id" id="Package"
                                                            value="{{ $item->packageDetails->id }}">
                                                        <tr>
                                                            <td>
                                                                {{ $item->packageDetails->name }}
                                                            </td>
                                                            <td>
                                                                {{ $currency }}
                                                                {{ formatCurrency(number_format((float) $item->packageDetails->price, 2, '.', '')) }}
                                                            </td>
                                                            <td>
                                                                {{ $item->quantity }}
                                                            </td>
                                                            <td id="total_price{{ $item->package_id }}">
                                                                {{ $currency }}
                                                                {{ formatCurrency(number_format((float) ($item->quantity * $item->packageDetails->price), 2, '.', '')) }}
                                                            </td>

                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td>{{ __('cart.no_products') }}</td>
                                                        </tr>
                                                    @endforelse

                                                    <tr>
                                                        <td colspan="2">
                                                            <h6 class="m-0 text-end">{{ __('cart.total') }}:</h6>
                                                        </td>
                                                        <td>
                                                            {{ $currency }}
                                                            {{ formatCurrency(number_format((float) $total, 2, '.', '')) }}
                                                        </td>
                                                        <input type="hidden" name="totalAmount" id="TotalAmount"
                                                            value="{{ $total }}">
                                                        <input type="hidden" name="total_pv"
                                                            value="{{ $total_pv }}">
                                                        <input type="hidden" name="default_address"
                                                            @if (isset($default_address)) value="{{ $default_address }}" @endif
                                                            id="is_default_address">
                                                    </tr>


                                                </tbody>
                                            </table>

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
    </div>


    <!-- end row -->

    </div> <!-- container-fluid -->
    </div>
    @include('admin.cart.inc.modal')

@endsection
@push('scripts')

    <script src="https://js.stripe.com/v3/"></script>
    <script src="{{ asset('assets/libs/chenfengyuan/datepicker/datepicker.min.js') }}"></script>
    <script src="https://www.paypal.com/sdk/js?client-id={{ config('paypal.sandbox.client_id') }}&currency=USD"></script>

    <script>

        $(document).ready(function() {
            $('#form-submit-button').addClass('d-none');
            $(window).keydown(function(event){
                if(event.keyCode == 13) {
                event.preventDefault();
                return false;
                }
            });
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
        $(() => {

            $('.date-picker-dob').datepicker({
                format: 'mm/dd/yyyy',
            });
            $('.payment-tab').first().addClass('active');
            $('.payment-tab').first().attr('aria-selected', 'true');
            $('.payment-content').first().addClass('show active');

            if ($('.payment-tab').first().val() == "stripe") {
                stripeInitialize();
            } else if($('.payment-tab').first().val() == "paypal") {
                $('#paypal-button-container').empty();
                payPalPay();
            }

        });


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
                el.dataset.method == 'purchase_wallet' ||
                el.dataset.method == "stripe" ||
                el.dataset.method == "paypal")
            {
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
            } else if(item.slug == 'paypal'){
                $('#paypal-button-container').empty();
                payPalPay();
            }

        }
        const activateApplyEpin = () => {
            if (event.target.value) {
                $('#apply-epin').attr('disabled', false);
            } else {
                $('#apply-epin').attr('disabled', true);
            }
        }

        // const checkEwalletavailability = async () => {
        //     try {
        //         $('#EwalletAlert').addClass('d-none')
        //         let url = "{{ route('register.check.ewallet') }}";
        //         let data = {
        //             username: $("#tranusername").val(),
        //             password: $('#tranPassword').val(),
        //             totalAmount: $('#TotalAmount').val(),
        //         }
        //         const res = await $.post(`${url}`, data);
        //         notifySuccess(res.message);
        //         $('#form-submit-button').removeClass('d-none');

        //     } catch (error) {
        //         console.log(error);
        //         $('#form-submit-button').addClass('d-none');
        //         if (error.status == 422) {
        //             notifyError(error.responseJSON.message)
        //         }
        //     }


        // }
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

        const addReciept = async () => {
            event.preventDefault()
            var file_data = $('#userfile').prop('files')[0];
            console.log(typeof(file_data));
            if (typeof(file_data) === 'undefined') {
                file_data = ' ';
                console.log(1);
            }
            var form_data = new FormData();
            form_data.append('reciept', file_data);

            $.ajax({
                type: 'POST',
                url: "{{ route('cart.add-payment-receipt') }}",
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

        const addNewAddress = async (form) => {
            event.preventDefault()
            var formElements = new FormData(form);
            for (var [key, value] of formElements) {
                form.elements[key].classList.remove('is-invalid', 'd-block')
            }
            $('.invalid-feedback').remove()

            let url = form.action
            let data = getForm(form)

            const res = await $.post(`${url}`, data)
                .catch((err) => {
                    if (err.status === 422) {
                        formvalidationError(form, err)
                    }
                })
            if (typeof(res) != "undefined") {
                $('#addressModal').offcanvas('hide')
                $('.user-address').html(res.data)
                form.reset();
                notifySuccess(res.message)
            }
        }

        const deleteAddress = async (id) => {
            event.preventDefault()
            let confirm = await confirmSwal()
            if (confirm.isConfirmed == true) {
                let url = "{{ route('cart.address-delete', ':id') }}";
                url = url.replace(":id", id)
                const res = await $.post(url, {
                    '_method': "delete",
                })

                notifySuccess(res.message)
                await $(`#address${id}`).remove()
            }
        };

        const makeDefault = async (id) => {
            event.preventDefault()
            let confirm = await confirmDefault()
            if (confirm.isConfirmed == true) {
                let url = "{{ route('cart.default-address', ':id') }}";
                url = url.replace(":id", id)
                const res = await $.post(url, {
                    '_method': "get",
                })

                notifySuccess(res.message)
                $('.address-card').removeClass('active');
                $("#card-" + id).addClass("active")
                $("#is_default_address").val("1")
                $(".default-btn").addClass('d-none');
                // $(".finish").removeAttr("disabled")
                // await $(`#address${id}`).remove()
            }
        };
        $('.payment-type').click(function() {
            $("#payment_type").val($(this).text());
        });
        $('#v-pills-payment-tab').click(function() {
            $(".finish").removeAttr("disabled")
        });

        const setAddress = () => {
            let el = event.target;
            $('.address-card').removeClass('active');
            $(`#card-${el.value}`).addClass('active');
            $('#is_default_address').val(el.value);
        }

        const payPalPay = () => {
            paypal.Buttons({
                // Call your server to set up the transaction
                createOrder: function(data, actions) {
                    return $.post(`{{ route('paypal.create') }}`,
                        JSON.stringify({
                            'type_cart' : true,
                            'user_id' : "{{auth()->user()->id}}",
                            'amount' : $("#TotalAmount").val(),
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
                    console.log('onApprove '+ data1);
                    return fetch(`{{ route('paypal.capture') }}`, {
                        method: 'POST',
                        body: JSON.stringify({
                            orderId: data2.orderID,
                            payment_gateway_id: $("#payapalId").val(),
                            user_id: "{{ auth()->user()->id }}",
                        })
                    }).then(function(res) {
                        return res.json();
                    }).then(function(orderData) {
                        var hiddenInput = document.createElement('input');
                        hiddenInput.setAttribute('type', 'hidden');
                        hiddenInput.setAttribute('name', 'paypalOrderId');
                        hiddenInput.setAttribute('value', orderData.id);
                        hiddenInput.setAttribute('id', 'paypalOrderId');
                        $('#paypalToken').append(hiddenInput);
                        $('#renewForm').submit();
                    });
                }

            }).render('#paypal-button-container');
        }

        const checkPurchaseWalletAvailability = async () => {
            $("#pwalletUsername").removeClass('is-invalid');
            $('#sponsor-username').removeClass('is-invalid');
            $('#pwalletPassword').removeClass('is-invalid');
            $('#TotalAmount').removeClass('is-invalid');
            let url = "{{ route('cart.check.pwallet') }}";
            let data = {
                transaction_username: $("#pwalletUsername").val(),
                sponsor: $('#sponsor-username').val(),
                tranPassword: $('#pwalletPassword').val(),
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
                    $('#purWalletAlert').removeClass('d-block').addClass('d-none').html('');
                    $("#pwalletUsername").removeClass('is-invalid');
                    $('#sponsor-username').removeClass('is-invalid');
                    $('#pwalletPassword').removeClass('is-invalid');
                    $('#TotalAmount').removeClass('is-invalid');
                    $('#form-submit-button').removeClass('d-none');
                    $('.invalid-feedback').remove();

                } else {
                    $('#purWalletAlert').removeClass('d-none').addClass('d-block').html(res.message);
                    $('.invalid-feedback').remove();

                }
            }
        }

    </script>
@endpush
