<div>
    <h4 class="card-title">{{ __('common.payment_information') }}</h4>
    <p class="card-title-desc">{{ __('common.fill_all_information_below') }}</p>
    <div class="card">
        <div class="card-body">

            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">{{ __('profile.reactivation_optn') }}</h4>
                    <div class="row">
                    <form action="{{route('renew.submit')}}" id="renewForm" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <div class="content-wrapper">
                                <input type="hidden" name="totalAmount" id="TotalAmount" value="">
                                <input type="hidden" name="product_id" id="Package" value="">
                                <input type="hidden" name="user_id" id="user_id" value=>
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
                                                        aria-controls="v-pills-{{ $item->id }}" aria-selected="true"
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
                                                            <p>{{ __('common.bank_details') }}</p>
                                                            <div class="form-group">
                                                                <label for="reciept">{{ __('common.select_reciept') }}
                                                                    <span class="text-danger">*</span></label>
                                                                <input type="file" name="reciept" id="userfile"
                                                                    onchange="recieptChange(event)"
                                                                    class="form-control wizard-required @error('reciept') is-invalid @enderror">
                                                                <span class="text-danger form-text">({{ __('common.allowed_types') }} jpg|jpeg|png)</span>
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
                                                                    onclick="addReciept();" id="update_profile_image">Add
                                                                    {{ __('common.payment_reciept') }}</button>
                                                            </div>
                                                        @elseif($item->slug == 'free-joining')
                                                            {{ __('common.click_submit_finish_button_to_continue') }}
                                                        @elseif($item->slug == 'e-pin')
                                                            <div id="epinDetails">
                                                                <div class="alert alert-dark" role="alert">
                                                                    No E-pin Applied
                                                                </div>
                                                                <div class="row">
                                                                    <div class="form-group col-md-8">
                                                                        <input type="text" name="epin[]" id="epin"
                                                                            class="form-control epins"
                                                                            placeholder="Enter E-pin">

                                                                    </div>
                                                                    <div class="form-group col-md-4">
                                                                        <button type="button" class="btn btn-primary mt-1"
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
                                                                <label>
                                                                    {{ __('common.user_name') }}<span>*</span>
                                                                </label>
                                                                <input class="form-control" id="tranusername"
                                                                    type="text" name="tranusername">
                                                            </div><br>
                                                            <div class="row">
                                                                <label>
                                                                    {{ __('common.transaction_password') }}<span>*</span>
                                                                </label>
                                                                <input id="tranPassword" class="form-control"
                                                                    type="password" name="tranPassword">
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <button type="button" id="check"
                                                                        class="btn btn-primary"
                                                                        onclick="checkEwalletavailability({{$user->id}})">{{ __('common.check_availability') }}
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
                                                                    <div id="card_number" class="field form-control">
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-lg-6">
                                                                        <div class="form-group mt-4 mb-0 required">
                                                                            <label for="expirydateInput">{{ __('common.expiry_date') }}</label>
                                                                            <div id="card_expiry"
                                                                                class="field form-control"></div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-6">
                                                                        <div class="form-group mt-4 mb-0 required">
                                                                            <label for="cvvcodeInput">{{ __('common.cvv_code') }}</label>
                                                                            <div id="card_cvc"
                                                                                class="field form-control"></div>
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
                                    class="btn btn-primary">Finish</button>
                            </div>
                        </div>

                    </form>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <a class="nav-link mb-2 payment-type" id="v-pills-authorize-tab" data-bs-toggle="pill" href="#v-pills-authorize" role="tab" aria-controls="v-pills-authorize" aria-selected="false"><label class="form-check-label font-size-13" for="paymentoptionsRadio1"><i
                        class="fab fa-cc-mastercard me-1 font-size-20 align-top"></i> Authorize.Net</label></a>
                    <a class="nav-link mb-2 payment-type" id="v-pills-paypal-tab" data-bs-toggle="pill" href="#v-pills-paypal" role="tab" aria-controls="v-pills-paypal" aria-selected="false"><label class="form-check-label font-size-13" for="paymentoptionsRadio2"><i
                        class="fab fa-cc-paypal me-1 font-size-20 align-top"></i> Paypal</label></a>
                    <a class="nav-link mb-2 payment-type" id="v-pills-bitgo-tab" data-bs-toggle="pill" href="#v-pills-bitgo" role="tab" aria-controls="v-pills-bitgo" aria-selected="false"><label class="form-check-label font-size-13" for="paymentoptionsRadio2"><i
                        class="fab fa-btc me-1 font-size-20 align-top"></i> BitGo</label></a>
                    <a class="nav-link mb-2 payment-type" id="v-pills-payeer-tab" data-bs-toggle="pill" href="#v-pills-payeer" role="tab" aria-controls="v-pills-payeer" aria-selected="true"><label class="form-check-label font-size-13" for="paymentoptionsRadio2"><i
                        class="bx bxs-parking me-1 font-size-20 align-top"></i> Payeer</label></a>
                    <a class="nav-link mb-2 payment-type" id="v-pills-sofort-tab" data-bs-toggle="pill" href="#v-pills-sofort" role="tab" aria-controls="v-pills-sofort" aria-selected="true"><label class="form-check-label font-size-13" for="paymentoptionsRadio2"><i
                        class="fas fa-euro-sign  font-size-20 align-top"></i> Sofort</label></a>
                    <a class="nav-link mb-2 payment-type" id="v-pills-squareup-tab" data-bs-toggle="pill" href="#v-pills-squareup" role="tab" aria-controls="v-pills-squareup" aria-selected="true"><label class="form-check-label font-size-13" for="paymentoptionsRadio2"><i
                        class="fa fa-square me-1 font-size-20 align-top"></i> SquareUp</label></a>
                    <a class="nav-link mb-2 payment-type" id="v-pills-blockchain-tab" data-bs-toggle="pill" href="#v-pills-blockchain" role="tab" aria-controls="v-pills-blockchain" aria-selected="true"><label class="form-check-label font-size-13" for="paymentoptionsRadio2"><i
                        class="fa fa-asterisk me-1 font-size-20 align-top"></i> Blockchain</label></a>
                    <a class="nav-link mb-2 payment-type" id="v-pills-epin-tab" data-bs-toggle="pill" href="#v-pills-epin" role="tab" aria-controls="v-pills-epin" aria-selected="true"><label class="form-check-label font-size-13" for="paymentoptionsRadio2"><i
                        class="fas fa-map-pin  me-1 font-size-20 align-top"></i> Epin</label></a>
                    <a class="nav-link mb-2 payment-type" id="v-pills-ewallet-tab" data-bs-toggle="pill" href="#v-pills-ewallet" role="tab" aria-controls="v-pills-ewallet" aria-selected="true"><label class="form-check-label font-size-13" for="paymentoptionsRadio2"><i
                        class="fas fa-wallet  me-1 font-size-20 align-top"></i> Ewallet</label></a>
                    <a class="nav-link mb-2 payment-type" id="v-pills-bank-tab" data-bs-toggle="pill" href="#v-pills-bank" role="tab" aria-controls="v-pills-bank" aria-selected="true"><label class="form-check-label font-size-13" for="paymentoptionsRadio2"><i
                        class="bx bxs-bank  me-1 font-size-20 align-top"></i> Bank Tranfer</label></a>
                    <a class="nav-link mb-2 payment-type" id="v-pills-free-purchase-tab" data-bs-toggle="pill" href="#v-pills-free-purchase" role="tab" aria-controls="v-pills-free-purchase" aria-selected="true"><label class="form-check-label font-size-13" for="paymentoptionsRadio2"><i
                        class="fa fa-cog me-1 font-size-20 align-top"></i> Free Purchase</label></a>
                    <a class="nav-link mb-2 payment-type" id="v-pills-purchase-wallet-tab" data-bs-toggle="pill" href="#v-pills-purchase-wallet" role="tab" aria-controls="v-pills-purchase-wallet" aria-selected="true"><label class="form-check-label font-size-13" for="paymentoptionsRadio2"><i
                        class="bx bx-basket  me-1 font-size-20 align-top"></i> Purchase Wallet</label></a>
                    </div>
                </div><input type="hidden" name="payment_type" id="payment_type">
                <div class="col-md-8">
                    <div class="tab-content text-muted mt-4 mt-md-0" id="v-pills-tabContent">
                        <div class="tab-pane fade" id="v-pills-authorize" role="tabpanel" aria-labelledby="v-pills-authorize-tab">
                            <p>{{ __('common.click_finish_to_continue') }}
                            </p>
                        </div>
                        <div class="tab-pane fade" id="v-pills-paypal" role="tabpanel" aria-labelledby="v-pills-paypal-tab">
                            <p>{{ __('common.click_finish_to_continue') }}
                            </p>
                        </div>
                        <div class="tab-pane fade" id="v-pills-bitgo" role="tabpanel" aria-labelledby="v-pills-bitgo-tab">
                            <p>{{ __('common.click_finish_to_continue') }}
                            </p>
                        </div>
                        <div class="tab-pane fade " id="v-pills-payeer" role="tabpanel" aria-labelledby="v-pills-payeer-tab">
                            <p>{{ __('common.payment_available_in_live_mode_only', [Payeer]) }}</p>
                        </div>
                        <div class="tab-pane fade " id="v-pills-sofort" role="tabpanel" aria-labelledby="v-pills-sofort-tab">
                            <p>{{ __('common.payment_available_in_live_mode_only', [Sofort]) }}</p>
                        </div>
                        <div class="tab-pane fade " id="v-pills-squareup" role="tabpanel" aria-labelledby="v-pills-squareup-tab">
                            <p>{{ __('common.payment_available_in_live_mode_only', [Square Up]) }}</p>
                        </div>
                        <div class="tab-pane fade " id="v-pills-blockchain" role="tabpanel" aria-labelledby="v-pills-blockchain-tab">
                            <p>{{ __('common.payment_available_in_live_mode_only', [Block Chain]) }}</p>
                        </div>
                        <div class="tab-pane fade " id="v-pills-epin" role="tabpanel" aria-labelledby="v-pills-epin-tab">
                            <p>{{ __('common.payment_available_in_live_mode_only', [Epin]) }}</p>
                        </div>
                        <div class="tab-pane fade " id="v-pills-ewallet" role="tabpanel" aria-labelledby="v-pills-ewallet-tab">
                            <p>{{ __('common.payment_available_in_live_mode_only', [E Wallet]) }}</p>
                        </div>
                        <div class="tab-pane fade " id="v-pills-bank" role="tabpanel" aria-labelledby="v-pills-bank-tab">
                            <p><label>{{ __('common.account_details') }}</label><textarea class="form-control" name="account_details">{{ old('account_details') }}</textarea></p>
                            <p><input type="file" name="image" ></p> ({{ __('common.allowed_types') }} jpg|jpeg|png)
                        </div>
                        <div class="tab-pane fade " id="v-pills-free-purchase" role="tabpanel" aria-labelledby="v-pills-free-purchase">
                            <p>{{ __('common.click_finish_to_continue') }}</p>
                        </div>
                        <div class="tab-pane fade " id="v-pills-purchase-wallet" role="tabpanel" aria-labelledby="v-pills-purchase-wallet">
                            <p>{{ __('common.check_availability') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
