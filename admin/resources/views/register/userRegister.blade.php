@extends('layouts.app')
@section('title', __('register.register_new_member'))
@section('content')
    <div class="container">
        <div class="text-center user_register_box">
            <h4>{{ __('register.register_new_member') }}</h4>
        </div>
    </div>
    <main class="my-5">
        <div class="container">
            <section class="wizard-section">
                <div class="row no-gutters">
                    <div class="col-lg-12 col-md-12">
                        <div class="form-wizard card">
                            <form action="{{ route('user.register') }}" method="post" role="form" id="registerForm"
                                enctype="multipart/form-data" onkeydown="return event.key != 'Enter';">
                                @csrf
                                <input type="hidden" name="regFromTree" value="{{ $data['regFromTree'] }}">
                                <div class="form-wizard-header">
                                    <ul class="list-unstyled form-wizard-steps clearfix">
                                        <li class="active"><span>1</span></li>
                                        <li><span>2</span></li>
                                        <li><span>3</span></li>
                                        <li><span>4</span></li>
                                    </ul>
                                </div>
                                <fieldset class="wizard-fieldset show">
                                    <h5 class="text-center text-black">{{ __('register.sponsor_and_package_information') }}
                                    </h5>
                                    <div class="form-group">
                                        <label class="text-dark">{{ __('common.sponsor_username') }} <span
                                                class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="sponsorName" id="sponsor-username"
                                            class="form-control wizard-required" onfocusout="checkSponsor(this)"
                                            value="{{ old('sponsorName', $data['sponsor']->username) }}" required>
                                        @error('sponsorName')
                                            <span class="text-danger form-text">{{ $message }}</span>
                                        @enderror
                                        <div class="wizard-form-error"></div>
                                        <div class="error text-danger form-text"></div>
                                    </div>
                                    <div class="form-group">
                                        <label class="text-dark">{{ __('common.sponsor_full_name') }}</label>
                                        <input type="text" name="sponsorFullname" id="sponsor-full-name"
                                            class="form-control wizard-required"
                                            value="{{ old('sponsorFullname', $data['sponsor']->userDetail->name . ' ' . $data['sponsor']->userDetail->second_name) }}"
                                            readonly>
                                        <input type="hidden" name="sponsor_id"
                                            value="{{ old('sponsor_id', $data['sponsor']->id) }}" id="sponsorId">
                                        <div class="wizard-form-error"></div>
                                        <div class="error text-danger form-text"></div>
                                    </div>

                                    @if ($data['regFromTree'] && in_array($data['modulestatus']->mlm_plan, ['Binary', 'Matrix']))
                                        <div class="form-group">
                                            <label class="text-dark">{{ __('common.placement_username') }} <span
                                                    class="text-danger">*</span>
                                            </label>
                                            <input type="text" name="placement_username" id="placement-username"
                                                class="form-control wizard-required" readonly
                                                value="{{ old('placement_username', $data['placementDetails']->username) }}"
                                                required>
                                            @error('placement_username')
                                                <span class="text-danger form-text">{{ $message }}</span>
                                            @enderror
                                            <div class="wizard-form-error"></div>
                                            <div class="error text-danger form-text"></div>
                                        </div>
                                        <div class="form-group">
                                            <label class="text-dark">{{ __('common.placement_fullname') }}</label>
                                            <input type="text" name="placement_fullname" id="placement-full-name"
                                                class="form-control wizard-required"
                                                value="{{ old('placement_fullname', $data['placementDetails']->userDetail->name . ' ' . $data['placementDetails']->userDetail->second_name) }}"
                                                readonly>
                                            <input type="hidden" name="placement_id"
                                                value="{{ old('placement_id', $data['placementDetails']->id) }}"
                                                id="placementId">
                                            <div class="wizard-form-error"></div>
                                            <div class="error text-danger form-text"></div>
                                        </div>
                                    @else
                                        <input type="hidden" name="placement_username" id=""
                                            value="{{ old('placement_username', $data['placementDetails']->username) }}">
                                        <input type="hidden" name="placement_fullname" id=""
                                            value="{{ old('placement_fullname', $data['placementDetails']->userDetail->name . ' ' . $data['placementDetails']->userDetail->second_name) }}">
                                    @endif

                                    @if ($data['modulestatus']->mlm_plan == 'Binary')
                                        <div class="form-group">
                                            <label class="text-black">{{ __('register.position') }} <span
                                                    class="text-danger">*</span></label>
                                            @if ($data['regFromTree'])
                                                <select name="position" id="LegPosition"
                                                    onchange="checkLegAvailability(this)"
                                                    class="form-select wizard-required" required>
                                                    <option value="{{ $position }}" selected>
                                                        @if ($position == 'L')
                                                            {{ __('register.left_leg') }}
                                                        @elseif($position == 'R')
                                                            {{ __('register.right_leg') }}
                                                        @endif
                                                    </option>
                                                </select>
                                                {{-- TODO add open cart status check --}}
                                            @else
                                                <select name="position" id="LegPosition"
                                                    onchange="checkLegAvailability(this)"
                                                    class="form-select wizard-required" required>
                                                    <option value="L"
                                                        @if (isset($position) && $position == 'L') selected @endif>
                                                        {{ __('register.left_leg') }}</option>
                                                    <option value="R"
                                                        @if (isset($position) && $position == 'R') selected @endif>
                                                        {{ __('register.right_leg') }}</option>
                                                </select>
                                            @endif
                                            <div class="wizard-form-error"></div>
                                            <div class="error text-danger form-text"></div>
                                        </div>
                                    @else
                                        <input type='hidden' name='position' id='position' class="form-control"
                                            value="{{ $position }}">
                                    @endif
                                    @isset($data['products'])
                                        <div class="form-group">
                                            <label class="text-black">{{ __('common.product') }} <span
                                                    class="text-danger">*</span></label>
                                            <select name="product_id" id="Package" class="form-select wizard-required"
                                                required>
                                                <option value="">{{ __('register.select_product') }}</option>
                                                @foreach ($data['products'] as $package)
                                                    <option value="{{ $package->id }}"
                                                        data-item="{{ formatCurrency($package->price) }}">
                                                        {{ $package->name . ' (' . $currency . ' ' . round(formatCurrency($package->price)) . ')' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="wizard-form-error"></div>
                                            <div class="error text-danger form-text"></div>
                                        </div>
                                    @endisset
                                    <div class="form-group clearfix" id="username-next-btn">
                                        <a class="form-wizard-next-btn float-end text-white"
                                            id="tab-1">{{ __('common.next') }}</a>
                                    </div>
                                </fieldset>

                                <fieldset class="wizard-fieldset">
                                    <h5 class="text-center text-black">{{ __('register.contact_information') }}</h5>

                                    @forelse ($data['customFields'] as $fields)
                                        <div class="form-group">
                                            @if ($fields->is_custom)
                                                <label
                                                    class="text-black text-capitalize">{{ $fields->customFieldLang->where('language_id', $data['default_lang'])->first()->value ?? 'NA' }}
                                                    <span
                                                        class="{{ $fields->required ? 'text-danger' : 'd-none' }}">*</span>
                                                </label>
                                            @else
                                                <label
                                                    class="text-black text-capitalize">{{ __('register.' . $fields->name) }}
                                                    <span
                                                        class="{{ $fields->required ? 'text-danger' : 'd-none' }}">*</span>
                                                </label>
                                            @endif
                                            @if ($fields->name == 'country')
                                                @php
                                                    if (auth()->user()->user_type == 'admin') {
                                                        $country_id = auth()->user()->userDetail->country_id;
                                                    } else {
                                                        $country_id = auth()->user()->employeeDetail->country_id;
                                                    }
                                                @endphp
                                                <select class="form-control" name="country"
                                                    {{ $fields->required ? 'wizard-required' : '' }} id="country">
                                                    @foreach ($data['countries'] as $country)
                                                        <option value="{{ $country->id }}"
                                                            {{ $country->id == $country_id ? 'selected' : '' }}>
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
                                                    class="form-control  {{ $fields->required ? 'wizard-required' : '' }}">
                                                    <option value="">{{ __('register.select_gender') }}</option>
                                                    <option value="M" selected>{{ __('register.male') }}</option>
                                                    <option value="F">{{ __('register.female') }}</option>
                                                    <option value="O">{{ __('register.other') }}</option>
                                                </select>
                                                <div class="wizard-form-error"></div>
                                                <div class="error text-danger form-text"></div>
                                            @elseif ($fields->name == 'address_line1' || $fields->name == 'address_line2')
                                                <textarea name="{{ $fields->name }}" id="" cols="30" rows="2" class="form-control">{{ old($fields->name, $fields->default_value) }}</textarea>
                                                @error($fields->name)
                                                    <span class="text-danger form-text">{{ $message }}</span>
                                                @enderror
                                                <div class="wizard-form-error"></div>
                                                <div class="error text-danger form-text"></div>
                                            @elseif($fields->name == 'date_of_birth')
                                                <div class="input-group" id="datepicker2">
                                                    <input type="date" name="date_of_birth"
                                                        onchange="checkAgeLimit(this)" id="dateOfBirth"
                                                        class="form-control wizard-required " placeholder="dd M, yyyy"
                                                        data-date-format="dd mm yyyy" data-date-container='#datepicker2'
                                                        data-provide="datepicker" data-date-autoclose="true"
                                                        value="{{ now()->subYear($data['signupSettings']->age_limit)->format('Y/m/d') }}">
                                                </div>
                                            @elseif($fields->type == 'textarea')
                                                <textarea id="{{ $fields->name }}" class="form-control @if ($fields->required) wizard-required @endif"
                                                    name="{{ $fields->name }}">{{ old($fields->name, $fields->default_value) }}</textarea>
                                            @elseif($fields->type == 'number')
                                                <input type="{{ $fields->type }}" name="{{ $fields->name }}"
                                                    id="{{ $fields->name }}" pattern="[1-9]" min="1"
                                                    max="10"
                                                    value="{{ old($fields->name, $fields->default_value) }}"
                                                    class="form-control @if ($fields->required) wizard-required @endif">
                                                @error($fields->name)
                                                    <span class="text-danger form-text">{{ __($message) }}</span>
                                                @enderror
                                                <div class="wizard-form-error"></div>
                                                <div class="error text-danger form-text" id="error_{{ $fields->name }}">
                                                </div>
                                            @else
                                                @if ($fields->is_custom)
                                                    <input type="{{ $fields->type }}" name="custom[{{ $fields->id }}]"
                                                        id="{{ $fields->name }}"
                                                        value="{{ old($fields->name, $fields->default_value) }}"
                                                        class="form-control @if ($fields->required) wizard-required @endif">
                                                    @error($fields->name)
                                                        <span class="text-danger form-text">{{ __($message) }}</span>
                                                    @enderror
                                                    <div class="wizard-form-error"></div>
                                                    <div class="error text-danger form-text"
                                                        id="error_{{ $fields->name }}">
                                                    </div>
                                                @else
                                                    <input type="{{ $fields->type }}" name="{{ $fields->name }}"
                                                        id="{{ $fields->name }}"
                                                        value="{{ old($fields->name, $fields->default_value) }}"
                                                        class="form-control @if ($fields->required) wizard-required @endif">
                                                    @error($fields->name)
                                                        <span class="text-danger form-text">{{ __($message) }}</span>
                                                    @enderror
                                                    <div class="wizard-form-error"></div>
                                                    <div class="error text-danger form-text"
                                                        id="error_{{ $fields->name }}">
                                                    </div>
                                                @endif
                                            @endif

                                        </div>

                                    @empty
                                    @endforelse
                                    <div class="form-group clearfix">
                                        <a href="javascript:;"
                                            class="form-wizard-previous-btn float-start">{{ __('common.previous') }}</a>
                                        <a href="javascript:;" id="tab-2"
                                            class="form-wizard-next-btn float-end">{{ __('common.next') }}</a>
                                    </div>
                                </fieldset>

                                <fieldset class="wizard-fieldset">
                                    <h5 class="text-center text-black">{{ __('register.login_information') }}</h5>
                                    <input type="hidden" id="usernameType"
                                        value="{{ $data['usernameConfig']->user_name_type }}">
                                    @if ($data['usernameConfig']->user_name_type != 'dynamic')
                                        <div class="form-group">
                                            <label class="text-black">{{ __('common.username') }} <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="username" id="username"
                                                class="form-control wizard-validate-field" value="{{ old('username') }}"
                                                required>
                                            <div class="wizard-form-error"></div>
                                            <div class="error text-danger form-text"></div>
                                            @error('username')
                                                <span class="text-danger form-text">{{ __($message) }}</span>
                                            @enderror
                                        </div>
                                    @elseif($data['usernameConfig']->user_name_type == 'dynamic')
                                        <input type="hidden" name="username" id="username"
                                            class="form-control wizard-validate-field" value="" required>
                                    @endif
                                    <div class="form-group">
                                        <label class="text-black">{{ __('common.password') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="password" name="password" id="password" class="form-control"
                                            required value="">
                                        <div class="wizard-form-error"></div>
                                        <div class="error text-danger form-text"></div>
                                        @error('password')
                                            <span class="text-danger form-text">{{ __($message) }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label class="text-black">{{ __('common.confirm_password') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="password" name="password_confirmation" id="confirm"
                                            class="form-control" required value="">
                                        <div class="wizard-form-error"></div>
                                        <div class="error text-danger form-text"></div>
                                        @error('c_password')
                                            <span class="text-danger form-text">{{ __($message) }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input ms-3 mt-lg-2" type="checkbox" value="yes"
                                            id="terms" name="terms">
                                        <label class="form-check-label" for="terms_condition" id="terms_condition">
                                            <a href="#" class="nav-link" data-bs-toggle="modal"
                                                data-bs-target="#terms-modal">{{ __('register.i_accept_terms_and_conditions') }}<span
                                                    class="text-danger">*</span></a>
                                        </label>
                                        <div class="wizard-form-error"></div>
                                        <div class="error text-danger form-text"></div>
                                    </div>
                                    <div class="form-group clearfix">
                                        <a href="javascript:;"
                                            class="form-wizard-previous-btn float-start">{{ __('common.previous') }}</a>
                                        <a href="javascript:;" id="tab-3"
                                            class="form-wizard-next-btn float-end">{{ __('common.next') }}</a>
                                    </div>
                                </fieldset>

                                <fieldset class="wizard-fieldset">
                                    <h5 class="text-center text-black reg_paymnt_type">{{ __('register.payment_type') }}
                                    </h5>
                                    <h5 class="text-center text-black p-2 total_amount_reg">
                                        {{ __('register.total_amount') }} : <span id="totalAmount">
                                            @if (!$data['modulestatus']->product_status)
                                                {{ $currency . ' ' . formatCurrency($registerAmount) }}
                                            @endif
                                        </span>
                                    </h5>
                                    <div class="form-group">
                                        <div class="content-wrapper">
                                            <input type="hidden" name="totalAmount" id="TotalAmount"
                                                value="{{ old('totalAmount') }}">
                                            <div class="col-12">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="nav flex-column nav-pills" id="v-pills-tab"
                                                            role="tablist" aria-orientation="vertical">
                                                            @forelse ($data['paymentGateWay'] as $item)
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
                                                                        value="{{ $item->id }}">{{ $item->name }}
                                                                </span>
                                                            @empty
                                                            @endforelse
                                                        </div>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <div class="paymnt_selection_bx">
                                                            <div class="tab-content text-muted mt-4 mt-md-0"
                                                                id="v-pills-tabContent">
                                                                @forelse ($data['paymentGateWay'] as $item)
                                                                    <div class="tab-pane fade payment-content"
                                                                        id="v-pills-{{ $item->id }}" role="tabpanel"
                                                                        aria-labelledby="v-pills-{{ $item->id }}">
                                                                        @if ($item->slug == 'bank-transfer')
                                                                        <p>{!! nl2br($bankdetails->account_info) !!}</p>
                                                                            <p>{{ __('register.bank_details') }}</p>
                                                                            <div class="form-group">
                                                                                <label
                                                                                    for="reciept">{{ __('register.select_reciept') }}
                                                                                    <span
                                                                                        class="text-danger">*</span></label>
                                                                                <input type="file" name="reciept"
                                                                                    id="userfile"
                                                                                    onchange="recieptChange(event)"
                                                                                    class="form-control wizard-required @error('reciept') is-invalid @enderror">
                                                                                <span
                                                                                    class="text-danger form-text">({{ __('register.allowed_types') }}
                                                                                    jpg|jpeg|png)</span>
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
                                                                            <div class="form-group d-flex">
                                                                                <button type="button"
                                                                                    class="btn btn-primary update_profile_image"
                                                                                    onclick="addReciept();"
                                                                                    id="update_profile_image">{{ __('register.add_payment_receipt') }}</button>

                                                                                <button type="button"
                                                                                    class="btn btn-danger ms-2 remove_profile_image d-none"
                                                                                    onclick="removeReciept();"
                                                                                    id="remove_reciept_image">{{ __('register.remove_payment_receipt') }}</button>
                                                                            </div>
                                                                        @elseif($item->slug == 'free-joining')
                                                                            {{ __('register.click_submit_button_to_continue') }}
                                                                        @elseif($item->slug == 'e-pin')
                                                                            <div id="epinDetails">
                                                                                <div class="alert alert-dark"
                                                                                    role="alert">
                                                                                    {{ __('register.no_epin_applied') }}
                                                                                </div>
                                                                                <div class="row">
                                                                                    <div class="form-group col-md-8">
                                                                                        <input type="text"
                                                                                            name="epin" id="epin"
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
                                                                                        type="text"
                                                                                        name="tranusername">
                                                                                </div>
                                                                                <div class="col-md-12">
                                                                                    <label>
                                                                                        {{ __('common.transaction_password') }}<span>*</span>
                                                                                    </label>
                                                                                    <input id="tranPassword"
                                                                                        class="form-control"
                                                                                        type="password"
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
                                                                        @elseif($item->slug == 'stripe')
                                                                            <div class="p-4 border">
                                                                                <div class="form-group mt-4 mb-0">
                                                                                    <div id="paymentResponse"
                                                                                        class="text-danger font-italic">
                                                                                    </div>
                                                                                    <label for="cardnumberInput">
                                                                                        {{ __('register.card_number') }}</label>
                                                                                    <div id="paymentResponse"
                                                                                        class="text-danger font-italic">
                                                                                    </div>
                                                                                    <div id="card_number"
                                                                                        class="field form-control"></div>
                                                                                </div>
                                                                                <div class="row">
                                                                                    <div class="col-lg-6">
                                                                                        <div
                                                                                            class="form-group mt-4 mb-0 required">
                                                                                            <label
                                                                                                for="expirydateInput">{{ __('common.expiryDate') }}</label>
                                                                                            <div id="card_expiry"
                                                                                                class="field form-control">
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-lg-6">
                                                                                        <div
                                                                                            class="form-group mt-4 mb-0 required">
                                                                                            <label
                                                                                                for="cvvcodeInput">{{ __('common.cvv') }}</label>
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
                                        </div>
                                        <div class="form-group clearfix">
                                            <a href="javascript:;"
                                                class="form-wizard-previous-btn float-start">{{ __('common.previous') }}</a>
                                            <button type="button" id="form-submit-button" onclick="submitForm(this)"
                                                class="form-wizard-submit float-end d-none btn">{{ __('common.finish') }}</button>
                                        </div>
                                    </div>
                                </fieldset>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>
    <div class="modal fade" id="terms-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ __('register.terms_and_conditions') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-justify">
                    {{ $data['terms']->terms_and_conditions }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('wizardStyle')
    <link rel="stylesheet" href="{{ asset('assets/libs/wizardNew/wizard.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/chenfengyuan/datepicker/datepicker.min.css') }}">
@endpush


@push('scripts')
    @php
        $isPaypal = $data['paymentGateWay']->where('slug', 'paypal')->first();
        $paypalConfig = getPaypalConfigs();
    @endphp
    @if ($isPaypal && $isPaypal->status && $isPaypal->registration)
        <script src="https://www.paypal.com/sdk/js?client-id={{ $paypalConfig['client_id'] }}&currency=USD"></script>
        {{-- <script src="https://www.paypal.com/sdk/js?client-id={{ config('paypal.sandbox.client_id') }}&currency=USD"></script> --}}
    @endif

    <script src="{{ asset('assets/libs/wizardNew/wizard-script.js') }}"></script>

    <script src="{{ asset('assets/libs/chenfengyuan/datepicker/datepicker.min.js') }}"></script>

    @php
        $isStripe = $data['paymentGateWay']->where('slug', 'stripe')->first();
    @endphp
    @if ($isStripe && $isStripe->status && $isStripe->registration)
        <script src="https://js.stripe.com/v3/"></script>`;
        {{-- <script src="{{ asset('assets/js/stripe.js') }}" async></script>`; --}}
    @endif
    <script>
        var nextWizardStep = true;

        let product;
        $(() => {
            var checkedValue = $('input[name="payment_method"]:checked').val();
            console.log('from ready  :- ',checkedValue);
            let epiField = $('#epin').val();
            if (epiField != '') {
                $('#apply-epin').attr('disabled', false);
            }
            $.ajax({
                url: "{{ route('ajax.state') }}",
                type: 'get',
                success: function(response) {
                    $("#state").html(" ");
                    $("#state").html(response.state);
                }
            });
            $(document).on('change', '#country', function() {
                let country = $('#country').val();
                let url = "{{ route('country.state', ':country') }}";
                url = url.replace(':country', country);
                $.ajax({
                    url: url,
                    type: 'get',
                    success: function(response) {
                        $('#state').html(' ');
                        $('#state').html(response.state);
                    }
                });
            });
            $(document).on('change', '#Package', function() {
                let package = $('#Package').val();
                let packAmount = $(this).find(':selected').data('item');
                let totalSum = parseInt(packAmount) + {{ $registerAmount }};
                product = package;
                let currency = "{{ $currency }} ";
                $('#totalAmount').html(' ');
                $('#totalAmount').html(currency + totalSum.toFixed(2));
                $('#TotalAmount').val(' ');
                $('#TotalAmount').val(totalSum.toFixed(2));
            });

            $('.date-picker-dob').datepicker({
                format: 'mm/dd/yyyy',
            });
            $('.payment-tab').first().addClass('active');
            $('.payment-tab').first().attr('aria-selected', 'true');
            $('.payment-content').first().addClass('show active');

            if ($('.payment-tab').first().val() == "stripe") {
                stripeInitialize();
            }
            generateUsername();
        });

        checkLegAvailability = async (el) => {
            let position = el.value;
            let sponsor = $('#sponsor-username').val();
            let url = `{{ route('check.legAvailability') }}/${position}/${sponsor}`;
            const res = $.get(`${url}`)
                .catch((err) => {
                    if (err.status == 422) {
                        el.value = "";
                        console.log(el.id);
                        selectvalidationError(el.id, err);
                    }
                });
        }

        recieptChange = (event) => {
            let output = document.getElementById('recipetImage');
            output.src = URL.createObjectURL(event.target.files[0]);
            output.onload = function() {
                URL.revokeObjectURL(output.src) // free memory
            }
        }

        setPaymentMethod = (item, el) => {
            $('input[name="payment_method"]').prop('checked', false);
            $(`#payment-id-${item.id}`).prop('checked', true);
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
            var checkedValue = $('input[name="payment_method"]:checked').val();
            console.log('from function  :- ',checkedValue);
        }

        const checkEwalletavailability = async () => {
            $("#transaction_username").removeClass('is-invalid');
            $('#sponsor-username').removeClass('is-invalid');
            $('#tranPassword').removeClass('is-invalid');
            $('#TotalAmount').removeClass('is-invalid');
            let url = "{{ route('register.check.ewallet') }}";
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
                url: "{{ route('user.add-payment-receipt') }}",
                data: form_data,
                cache: true,
                contentType: false,
                processData: false,
                success: function(data) {
                    notifySuccess(data.success)
                    $('#form-submit-button').removeClass('d-none')
                    $('#update_profile_image').attr('disabled', true)
                    $('#remove_reciept_image').removeClass('d-none');
                },
                error: function(err) {
                    notifyError(err.responseJSON.message)

                },
            });
        }

        const removeReciept = async () => {
            try {
                let data = {
                    'username': $('#username').val(),
                    '_method': 'Delete',
                }
                let url = "{{ route('remove.bank.reciept') }}";

                const res = await $.post(`${url}`, data);
                $('#recipetImage').attr('src', null);
                $('#update_profile_image').removeAttr('disabled')
                $('#remove_reciept_image').addClass('d-none');
                $('#form-submit-button').addClass('d-none');
                $('#userfile').val('')
                notifySuccess(res.message)
            } catch (error) {
                console.log(error);
            }
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

        stripeInitialize = () => {
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
                createToken(stripe, card, resultContainer, this);
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
        const createToken = (stripe, cardElement, resultContainer, form) => {
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
        const checkAgeLimit = async (element) => {
            $(".invalid-feedback").remove();
            let dob = element.value;
            let url = `{{ route('check.dob') }}?dob=${dob}`;
            const res = $.get(`${url}`)
                .catch((err) => {
                    if (err.status == 422) {
                        elementvalidationError(element.id, err, 'datepicker2');
                    }
                })
            element.classList.remove('is-invalid');

            element.classList.add('is-valid');
        }
        const activateApplyEpin = () => {
            if (event.target.value) {
                $('#apply-epin').attr('disabled', false);
            } else {
                $('#apply-epin').attr('disabled', true);
            }
        }

        const checkSponsor = async (el) => {
            el.classList.remove('is-invalid');
            el.classList.remove('is-valid');
            $('.invalid-feedback').removeClass('d-block').addClass('d-none');
            $(".invalid-feedback").remove();
            let username = el.value;
            let placementUsername = $("input[name='placement_username']").val();
            let url = "{{ route('ajax.sponsorName') }}"
            url = url + `?sponsor=${username}&placement=${placementUsername}`;
            console.log(url)

            const res = await $.get(`${url}`)
                .catch((err) => {
                    if (err.status === 422) {
                        nextWizardStep = false;
                        elementvalidationError(el.id, err, el.id)
                    }
                })
            if (typeof(res) != 'undefined') {
                $("#sponsor-full-name").val(res.data.user_detail.name + " " +
                    res.data.user_detail.second_name);
                $("#sponsorId").val(res.data.id);
                el.classList.add('is-valid');
                nextWizardStep = true;
            }
        }

        const generateUsername = async () => {
            try {
                let usernameType = $('#usernameType').val();
                if (usernameType == 'dynamic') {
                    let url = "{{ route('generate.dynamic.username') }}";
                    const res = await $.get(`${url}`);
                    $('#username').val('');
                    $('#username').val(res.username);
                }

            } catch (error) {
                console.log(error);
            }
        }

        const payPalPay = () => {
            paypal.Buttons({
                // Call your server to set up the transaction
                createOrder: function(data, actions) {
                    return $.post(`{{ route('paypal.create') }}`,
                            JSON.stringify({
                                'user_id': "{{ auth()->user()->id }}",
                                'amount': $("#TotalAmount").val(),
                                'prefix': "{{ config('database.connections.mysql.prefix') }}",
                                'package_id' : $('#Package').val()
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
                            prefix: "{{ config('database.connections.mysql.prefix') }}",
                        })
                    }).then(function(res) {
                        // console.log(res.json());
                        return res.json();
                        // return JSON.stringify(res);
                    }).then(function(orderData) {
                        console.log(orderData);
                        var hiddenInput = document.createElement('input');
                        hiddenInput.setAttribute('type', 'hidden');
                        hiddenInput.setAttribute('name', 'paypalOrderId');
                        hiddenInput.setAttribute('value', orderData.id);
                        hiddenInput.setAttribute('id', 'paypalOrderId');
                        $('#paypalToken').append(hiddenInput);
                        // alert('data');
                        $('#registerForm').submit();
                    });
                }

            }).render('#paypal-button-container');
        }

        const submitForm = (btn) => {
            try {
                let id = '#' + btn.id;
                $(id).prop('disabled', true);
                $(id).html('')
                $(id).html("{{ trans('common.loading') }}");
                $('#registerForm').submit();
            } catch (err) {
                console.log(err)
            }
        }
    </script>
@endpush
