@extends('layouts.app')
@section('title', __('profile.profile_view'))
@section('content')
    <form method="get" id="userform" action="{{ route('profile.view') }}" enctype="multipart/form-data"
        onsubmit="getUserData(this)">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">{{ __('profile.profile_view') }}</h4>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="card-group justify-content-end">
                    <div class="col-md-4 float-end">
                        <div class="profile_top_srch_sc">
                            <div class="ajax-select">
                                <select name="username" class="form-control treeview_frm_input select2-search-user">
                                    @isset($data['user'])
                                        <option selected value="{{ $data['user']->id }}">{{ $data['user']->username }}</option>
                                    @endisset
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">{{ __('common.view') }}</button>
                        </div>
                        <span id="error" style="color: red;"></span>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="row d-flex">
        <div class="col-md-4 col-lg-4 col-xl-3">
            <div id="profView">
                <div class="card full_height profile_view_pg" id="profile_view_pg">
                    <div class="card-body">
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle float-end user_cng_psrd" type="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <span>.</span><span>.</span><span>.</span>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <div class="text-center w-100 form-group">
                                        <button type="submit" class="btn btn-outline-secondary"
                                            data-bs-target="#resetPassword" data-bs-toggle="modal"><i class="bx bx-lock"
                                                style="font-size: 16px"></i> {{ __('profile.change_password') }}</button>
                                    </div>
                                    <div class="text-center w-100 form-group">
                                        <button type="submit" class="btn btn-outline-secondary "
                                            data-bs-target="#resetTransactionPassword" data-bs-toggle="modal"><i
                                                class="bx bx-lock" style="font-size: 16px"></i>
                                            {{ __('profile.change_transaction_password') }}</button>
                                    </div>

                                </li>

                            </ul>
                        </div>

                        {{-- <form action="{{ route('profile.update') }}" method="post" enctype="multipart/form-data"> --}}
                        @csrf
                        <div class="form-group">
                            <div class="row d-flex justify-content-center">
                                <div class="profile-photo-view">
                                    @if ($data['user']->userDetail->image == null || !isFileExists($data['user']->userDetail->image))
                                        <img src="{{ asset('/assets/images/users/avatar-1.jpg') }}" id="user-img">
                                    @else
                                        <img src="{{ $data['user']->userDetail->image }}" id="user-img">
                                    @endif
                                    <div class="profile-photo-update-btn">{{ __('common.update') }}
                                        <input id="name" type="file" class="form-control" name="image"
                                            value="" required="" autofocus="" aria-invalid="false">
                                    </div>
                                </div>
                            </div>
                            @if ($data['user']->user_type != 'admin')
                                <div class="form-check form-switch form-switch-lg mb-3 " dir="ltr">
                                    <label
                                        class="form-check-label {{ !$data['user']->active ? 'text-danger' : 'text-success' }}"
                                        for="SwitchCheckSizelg"
                                        id="userActive-status">{{ $data['user']->active ? __('profile.active') : __('profile.blocked') }}</label>
                                    <input
                                        class="form-check-input {{ !$data['user']->active ? 'bg-danger' : 'bg-success' }}"
                                        onchange="changeUserStatus({{ $data['user']->id }})" type="checkbox"
                                        id="SwitchCheckSizelg" @if ($data['user']->active) checked @endif>
                                </div>
                            @endif

                            <input type="hidden" name="userId" value="{{ $data['user']->id }}">
                        </div>
                        {{-- <div class="form-group text-center">
                            <button type="button" id="profPicButton" class="btn btn-primary text-white">{{ __('common.update') }}</button>
                        </div> --}}
                        {{-- </form> --}}
                        <div>
                            <label for="name"
                                class="col-md-12 control-label profile_user_sub_name profile_name_txt">{{ $data['user']->userDetail->name }}
                                {{ $data['user']->userDetail->second_name }}</label>
                        </div>
                        <div class="form-group">
                            <label for=""
                                class="col-md-8 control-label profile_user_sub_name">{{ $data['user']->email }}</label>
                        </div>

                        @if ($data['user']->user_type == 'user')
                            @if ($data['moduleStatus']->kyc_status)
                                <div class="text-center w-100 form-group">
                                    <div>
                                        KYC:
                                        @if ($data['user']->userDetail->kyc_status)
                                            @php
                                                $status = 'Approved';
                                            @endphp
                                            <div class="kyc_verified_round"><i class="fa fa-check"></i></div>
                                        @else
                                            @php
                                                $status = 'any';
                                            @endphp
                                            <div class="kyc_notverified_round"><i class="fa fa-times"></i></div>
                                        @endif
                                        <a class="more_info btn btn-secondary btn-sm"
                                            href="{{ route('profile.kycDetails') }}">{{ __('profile.more_info') }}</a>
                                    </div>
                                </div>
                            @endif
                        @endif

                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8 col-lg-8 col-xl-9">
            <div class="card full_height profile_view_pg">
                <div class="card-body">
                    <div class="row">
                        @if ($data['moduleStatus']->product_status || $data['moduleStatus']->ecom_status)
                            <div class="col-md-4">
                                <div class="profile_view_membership_sc">
                                    {{ __('profile.membership_package') }}:
                                    <span>{{ Str::ucfirst($data['user']->package->name ?? ($data['user']->package->model ?? 'NA')) }}</span>
                                    @if( !$data['moduleStatus']->ecom_status)
                                    @if ($data['moduleStatus']->package_upgrade && $data['user']->user_type == 'user' && $data['upgradablePack'])
                                        <div class="text-center w-100 form-group" style="text-align: left !important;">
                                            <a href="{{ route('package.upgrade', ['id' => $data['user']->id]) }}"><button
                                                    type="btn" class="btn btn-primary text-white"
                                                    style="margin-top: 5px;">{{ __('profile.upgrade') }}</button></a>
                                        </div>
                                    @endif
                                    @endif
                                </div>
                            </div>
                            @if ($data['moduleStatus']->subscription_status)
                                <div class="col-md-4">
                                    <div class="profile_view_membership_sc">
                                        {{ __('profile.membership_expire') }}:
                                        @if ($data['user']->product_validity)
                                            <span>{{ Carbon\Carbon::parse($data['user']->product_validity)->format('d M Y  g:i:s A') }}</span>
                                        @else
                                            <span>NA</span>
                                        @endif
                                    </div>

                                    @if (
                                        $data['moduleStatus']->subscription_status &&
                                            $currentDate > $data['user']->product_validity &&
                                            $data['user']->user_type != 'admin')
                                            @if( !$data['moduleStatus']->ecom_status)
                                        <div class="profile_view_membership_sc mb-6" style="margin-bottom: 10px;">
                                            <a href="{{ route('subscriptions.renewal.index', $data['user']->id) }}"><button
                                                    class="btn btn-primary">{{ __('profile.renew_membership') }}</button></a>
                                        </div>
                                        @endif
                                    @endif
                                </div>
                            @endif
                        @endif
                        @if ($data['moduleStatus']->rank_status)
                            <div class="col-md-4">
                                <div class="profile_view_rank_sec">
                                    <div class="profile_view_rank_top">
                                        {{ __('profile.rank') }} :
                                        @if ($data['user']->rankDetail)
                                            <strong
                                                style="{{ $data['user']->rankDetail->color ? 'color:' . $data['user']->rankDetail->color : 'color:#000' }}">{{ $data['user']->rankDetail->name }}</strong>
                                            @if ($data['user']->rankDetail->image)
                                                <div class="rank-avatar-bg">
                                                    <img src="{{ asset($data['user']->rankDetail->image) }}"
                                                        alt="">
                                                </div>
                                            @endif
                                        @else
                                            <span style="color: ">NA</span>
                                        @endif
                                    </div>

                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="profile_pv_view_sec">
                        @if ($data['moduleStatus']->product_status || $data['moduleStatus']->ecom_status)
                            <div class="profile_pv_box">
                                <div class="profile_pv_box_head">
                                    <div class="profile_pv_box_head_ico"><i class="fa fa-user"></i></div>
                                    {{ __('profile.personal') }} PV
                                    <div class="profile_pv_box_cnt">
                                        {{ $data['user']->personal_pv }}
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#updatePv"
                                            class="updatede_pv_btn">{{ __('profile.update_pv') }}</a>
                                    </div>
                                </div>
                            </div>

                            <div class="profile_pv_box">
                                <div class="profile_pv_box_head">
                                    <div class="profile_pv_box_head_ico"><i class="fa fa-users"></i></div>
                                    {{ __('profile.group') }} PV
                                    <div class="profile_pv_box_cnt">
                                        {{ $data['user']->group_pv }}
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if ($data['moduleStatus']->mlm_plan == 'Binary')
                            <div class="profile_pv_box">
                                <div class="profile_pv_box_head">
                                    <div class="profile_pv_box_head_ico"><i class="fa fa-arrow-left"></i></div>
                                    {{ __('profile.left_carry') }}
                                    <div class="profile_pv_box_cnt">
                                        {{ $data['user']->total_left_carry }}
                                    </div>
                                </div>
                            </div>

                            <div class="profile_pv_box">
                                <div class="profile_pv_box_head">
                                    <div class="profile_pv_box_head_ico"><i class="fa fa-arrow-right"></i></div>
                                    {{ __('profile.right_carry') }}
                                    <div class="profile_pv_box_cnt">
                                        {{ $data['user']->total_right_carry }}
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                </div>
            </div>

        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card text-center mb-3">
                <div class="card-body profile_view_btm_dtl">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist"
                                aria-orientation="vertical">
                                <a class="nav-link mb-2 active" id="v-pills-home-tab" data-bs-toggle="pill"
                                    href="#v-pills-home" role="tab" aria-controls="v-pills-home"
                                    aria-selected="true">{{ __('profile.personal_details') }}</a>
                                <a class="nav-link mb-2" id="v-pills-profile-tab" data-bs-toggle="pill"
                                    href="#v-pills-profile" role="tab" aria-controls="v-pills-profile"
                                    aria-selected="false">{{ __('profile.bank_details') }}</a>
                                <a class="nav-link mb-2" id="v-pills-messages-tab" data-bs-toggle="pill"
                                    href="#v-pills-messages" role="tab" aria-controls="v-pills-messages"
                                    aria-selected="false">{{ __('profile.contact_details') }}</a>
                                @if (!$customFields->isEmpty())
                                    <a class="nav-link mb-2" id="v-pills-additional-details-tab" data-bs-toggle="pill"
                                        href="#v-pills-additional-details" role="tab"
                                        aria-controls="v-pills-additional-details"
                                        aria-selected="false">{{ __('profile.additional_details') }}</a>
                                @endif
                                @if ($data['user']->id != auth()->user()->id)
                                    <a class="nav-link" id="v-pills-payments-tab" data-bs-toggle="pill"
                                        href="#v-pills-payments" role="tab" aria-controls="v-pills-payments"
                                        aria-selected="false">{{ __('profile.payment_details') }}</a>
                                @endif
                                @if (
                                    $data['moduleStatus']->multi_currency_status ||
                                        $data['moduleStatus']->multilang_status ||
                                        ($data['moduleStatus']->mlm_plan == 'Binary' && $data['user']->user_type == 'user') ||
                                        $data['moduleStatus']->google_auth_status)
                                    <a class="nav-link mt-lg-2" id="v-pills-settings-tab" data-bs-toggle="pill"
                                        href="#v-pills-settings" role="tab" aria-controls="v-pills-settings"
                                        aria-selected="false">{{ __('profile.settings') }}</a>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="tab-content text-muted mt-4 mt-md-0" id="v-pills-tabContent">
                                <div class="tab-pane fade show active" id="v-pills-home" role="tabpanel"
                                    aria-labelledby="v-pills-home-tab">
                                    <form action="{{ route('profileDetail.update') }}" method="post"
                                        enctype="multipart/form-data" onsubmit="addPersonalDetails(this)">
                                        @csrf
                                        <div class="form-group row">
                                            <label for="name"
                                                class="col-md-4 control-label">{{ __('profile.first_name') }}
                                                <span class="text-danger">*</span></label>
                                            <div class="col-md-6">
                                                <input id="name" type="text" class="form-control valid"
                                                    name="firstname" value="{{ $data['user']->userDetail->name }}"
                                                    autofocus="" autocomplete="off" aria-invalid="false">
                                                @error('firstname')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                                <input type="hidden" name="userId" value="{{ $data['user']->id }}">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for=""
                                                class="col-md-4 control-label">{{ __('profile.last_name') }} </label>
                                            <div class="col-md-6">
                                                <input id="" type="text" class="form-control valid"
                                                    name="lastname" value="{{ $data['user']->userDetail->second_name }}"
                                                    autofocus="" autocomplete="off" aria-invalid="false">
                                                @error('lastname')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for=""
                                                class="col-md-4 control-label">{{ __('profile.gender') }} <span
                                                    class="text-danger">*</span> </label>
                                            <div class="col-md-6">
                                                <select name="gender" class="form-select" id="" autocomplete="off">
                                                    <option value="M"
                                                        {{ $data['user']->userDetails->gender == 'M' ? 'selected' : '' }}>
                                                        {{ __('common.male') }}
                                                    </option>
                                                    <option value="F"
                                                        {{ $data['user']->userDetails->gender == 'F' ? 'selected' : '' }}>
                                                        {{ __('common.female') }}
                                                    </option>
                                                    <option value="O"
                                                        {{ $data['user']->userDetails->gender == 'O' ? 'selected' : '' }}>
                                                        {{ __('common.other') }}
                                                    </option>
                                                </select>
                                                @error('gender')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group row mb-5">
                                            <label for=""
                                                class="col-md-4 control-label">{{ __('profile.date_of_birth') }} <span
                                                    class="text-danger">*</span></label>
                                            <div class="col-md-6">
                                                <input id="" type="date" class="form-control valid"
                                                    name="dob" value="{{ $data['user']->userDetails->dob }}"
                                                    autofocus="" autocomplete="off" aria-invalid="false" max="{{ date('Y-m-d') }}">
                                                @error('dob')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group text-start">
                                            <button type="submit"
                                                class="btn btn-primary text-white">{{ __('common.update') }}</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="tab-pane fade" id="v-pills-profile" role="tabpanel"
                                    aria-labelledby="v-pills-profile-tab">
                                    <form onsubmit="addBankDetails(this)" action="{{ route('bankDetail.update') }}"
                                        method="post" enctype="multipart/form-data">
                                        @csrf
                                        <div class="form-group row">
                                            <label for="name"
                                                class="col-md-4 control-label">{{ __('profile.bank_name') }} <span
                                                    class="text-danger">*</span></label>
                                            <div class="col-md-6">
                                                <input id="name" type="text" class="form-control valid"
                                                    name="bank_name" value="{{ $data['user']->userDetail->bank }}"
                                                    autofocus="" autocomplete="off" aria-invalid="false">
                                                @error('bank_name')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                                <input type="hidden" name="userId" value="{{ $data['user']->id }}">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for=""
                                                class="col-md-4 control-label">{{ __('profile.branch_name') }} <span
                                                    class="text-danger">*</span></label>
                                            <div class="col-md-6">
                                                <input id="" type="text" class="form-control valid"
                                                    name="branch_name" value="{{ $data['user']->userDetail->branch }}"
                                                    autofocus="" autocomplete="off" aria-invalid="false">
                                                @error('branch_name')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for=""
                                                class="col-md-4 control-label">{{ __('profile.account_holder_name') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <div class="col-md-6">
                                                <input id="" type="text" class="form-control valid"
                                                    name="acc_holder"
                                                    value="{{ $data['user']->userDetail->nacct_holder }}" autofocus="" autocomplete="off"
                                                    aria-invalid="false">
                                                @error('acc_holder')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for=""
                                                class="col-md-4 control-label">{{ __('profile.account_number') }}
                                                <span class="text-danger">*</span></label>
                                            <div class="col-md-6">
                                                <input id="" type="number" class="form-control valid"
                                                    name="acc_number"
                                                    value="{{ $data['user']->userDetail->account_number }}"
                                                    autofocus="" autocomplete="off" aria-invalid="false" min="0">
                                                @error('acc_number')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="" class="col-md-4 control-label">IFSC <span
                                                    class="text-danger">*</span></label>
                                            <div class="col-md-6">
                                                <input id="" type="text" class="form-control valid"
                                                    name="ifsc" value="{{ $data['user']->userDetail->ifsc }}"
                                                    autofocus="" autocomplete="off" aria-invalid="false">
                                                @error('ifsc')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="" class="col-md-4 control-label">Pan No <span
                                                    class="text-danger">*</span></label>
                                            <div class="col-md-6">
                                                <input id="" type="text" class="form-control valid"
                                                    name="pan" value="{{ $data['user']->userDetail->pan }}"
                                                    autofocus="" autocomplete="off" aria-invalid="false">
                                                @error('pan')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group text-start">
                                            <button type="submit"
                                                class="btn btn-primary text-white">{{ __('common.update') }}</button>
                                        </div>
                                    </form>
                                </div>

                                <div class="tab-pane fade" id="v-pills-messages" role="tabpanel"
                                    aria-labelledby="v-pills-messages-tab">
                                    <form action="{{ route('contactDetail.update') }}" onsubmit="addContactDetails(this)"
                                        method="post" enctype="multipart/form-data">
                                        @csrf
                                        <div class="form-group row">
                                            <label for="name"
                                                class="col-md-4 control-label">{{ __('profile.address') }} @if (!$signupField->where('name', 'address_line1')->where('required', 1)->isEmpty())
                                                    <span class="text-danger">*</span>
                                                @endif
                                            </label>
                                            <div class="col-md-6">
                                                <input id="name" type="text" class="form-control valid"
                                                    name="address" value="{{ $data['user']->userDetail->address }}"
                                                    autofocus="" autocomplete="off" aria-invalid="false">
                                                @error('address')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for=""
                                                class="col-md-4 control-label">{{ __('profile.address') }} 2 @if (!$signupField->where('name', 'address_line2')->where('required', 1)->isEmpty())
                                                    <span class="text-danger">*</span>
                                                @endif
                                            </label>
                                            <div class="col-md-6">
                                                <input id="" type="text" class="form-control valid"
                                                    name="address2" value="{{ $data['user']->userDetail->address2 }}"
                                                    autofocus="" autocomplete="off" aria-invalid="false">
                                                @error('address2')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                                <input type="hidden" name="userId" value="{{ $data['user']->id }}">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for=""
                                                class="col-md-4 control-label">{{ __('profile.country') }} @if (!$signupField->where('name', 'country')->where('required', 1)->isEmpty())
                                                    <span class="text-danger">*</span>
                                                @endif
                                            </label>
                                            <div class="col-md-6">
                                                <select name="country" class="form-select" id="country" autocomplete="off">
                                                    @foreach ($data['countries'] as $country)
                                                        <option value="{{ $country->id }}"
                                                            {{ $country->id == $data['user']->userDetail->country_id ? 'selected' : '' }}>
                                                            {{ $country->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('country')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for=""
                                                class="col-md-4 control-label">{{ __('profile.state') }}@if (!$signupField->where('name', 'state')->where('required', 1)->isEmpty())
                                                    <span class="text-danger">*</span>
                                                @endif
                                            </label>
                                            <div class="col-md-6">
                                                <div id="States">
                                                    <select name="state" id="state" class="form-control" autocomplete="off">
                                                        <option>{{ __('common.select_state') }}</option>
                                                    </select>

                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for=""
                                                class="col-md-4 control-label">{{ __('profile.city') }} @if (!$signupField->where('name', 'city')->where('required', 1)->isEmpty())
                                                    <span class="text-danger">*</span>
                                                @endif
                                            </label>
                                            <div class="col-md-6">
                                                <input id="" type="text" class="form-control valid"
                                                    name="city" value="{{ $data['user']->userDetail->city }}"
                                                    autofocus="" autocomplete="off" aria-invalid="false">
                                                @error('city')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for=""
                                                class="col-md-4 control-label">{{ __('profile.zip_code') }} @if (!$signupField->where('name', 'pin')->where('required', 1)->isEmpty())
                                                    <span class="text-danger">*</span>
                                                @endif
                                            </label>
                                            <div class="col-md-6">
                                                <input id="" type="number" class="form-control valid"
                                                    name="pin" value="{{ $data['user']->userDetail->pin }}"
                                                    autofocus="" autocomplete="off" aria-invalid="false" min="0">
                                                @error('pin')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for=""
                                                class="col-md-4 control-label">{{ __('profile.email') }} </label>
                                            <div class="col-md-6">
                                                <input id="" type="email" class="form-control valid"
                                                    name="email" value="{{ $data['user']->email }}"
                                                    autofocus="" autocomplete="off" aria-invalid="false">
                                                @error('email')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for=""
                                                class="col-md-4 control-label">{{ __('profile.mobile_no') }} <span
                                                    class="text-danger">*</span></label>
                                            <div class="col-md-6">
                                                <input id="" type="number" class="form-control valid"
                                                    name="mob" value="{{ $data['user']->userDetail->mobile }}"
                                                    autofocus="" autocomplete="off" aria-invalid="false" min="0">
                                                @error('mob')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for=""
                                                class="col-md-4 control-label">{{ __('profile.phone_no') }} @if (!$signupField->where('name', 'phone')->where('required', 1)->isEmpty())
                                                    <span class="text-danger">*</span>
                                                @endif
                                            </label>
                                            <div class="col-md-6">
                                                <input id="" type="number" class="form-control valid"
                                                    name="phone" value="{{ $data['user']->userDetail->land_phone }}"
                                                    autofocus="" autocomplete="off" aria-invalid="false" min="0">
                                                @error('phone')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group text-start">
                                            <button type="submit"
                                                class="btn btn-primary text-white">{{ __('common.update') }}</button>
                                        </div>
                                    </form>
                                </div>
                                @if (!$customFields->isEmpty())
                                    <div class="tab-pane fade" id="v-pills-additional-details" role="tabpanel"
                                        aria-labelledby="v-pills-additional-details-tab">
                                        <form action="{{ route('additionalDetail.update') }}"
                                            onsubmit="addAdditionalDetails(this)" method="post"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" name="userID" value="{{ $data['user']->id }}">
                                            @forelse ($customFields as $item)
                                                <div class="form-group row">
                                                    <label for="name"
                                                        class="col-md-4 control-label">{{ $item->customFieldLang->where('language_id', auth()->user()->default_lang)->first()->value ?? 'NA' }}
                                                        @if ($item->required)
                                                            <span class="text-danger">*</span>
                                                        @endif
                                                    </label>
                                                    @php
                                                        $value = $data['user']->additionalDetails->where('customfield_id', $item->id)->first()->value ?? '';
                                                        $name = $item->required ? 'required' : 'non_required';
                                                    @endphp
                                                    <div class="col-md-6">
                                                        <input type="text" name="{{ $name . '[' . $item->id . ']' }}"
                                                            id="" class="form-control"
                                                            value="{{ $value }}">
                                                    </div>
                                                </div>
                                            @empty
                                            @endforelse
                                            <div class="form-group text-start">
                                                <button type="submit"
                                                    class="btn btn-primary text-white">{{ __('common.update') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                @endif
                                @if ($data['user']->id != auth()->user()->id)
                                    <div class="tab-pane fade" id="v-pills-payments" role="tabpanel"
                                        aria-labelledby="v-pills-payments-tab">
                                        <form action="{{ route('update.user.payment-details', $data['user']->id) }}"
                                            onsubmit="addPaymentDetails(this)"method="post"
                                            enctype="multipart/form-data" id="payment-details">
                                            <div class="form-group row">
                                                <label for=""
                                                    class="col-md-4 control-label">{{ __('common.payment_method') }}
                                                </label>
                                                <div class="col-md-6">
                                                    <select name="payout_type" id="payout-payment-method"
                                                        class="form-select" onchange="changePayoutMethod(this)">
                                                        @forelse ($data['paymentGateway']  as $item)
                                                            <option value="{{ $item->id }}"
                                                                data-slug="{{ $item->slug }}"
                                                                @selected($data['user']->userDetail->payout_type == $item->id)>
                                                                {{ $item->name }}
                                                            </option>
                                                        @empty
                                                            <option value="">{{ __('common.no_data') }}</option>
                                                        @endforelse
                                                    </select>
                                                </div>
                                            </div>
                                            @forelse ($data['paymentGateway'] as $item)
                                                <div class="form-group row @if ($data['user']->user_type == 'admin') d-none @endif payout-field-{{ $item->id }} payout-methods"
                                                    id="gateway-{{ $item->id }}">
                                                    @if ($item->slug != 'bank-transfer')
                                                        <label for=""
                                                            class="col-md-4 control-label">{{ $item->slug }}
                                                        </label>
                                                        <div class="col-md-6">
                                                            <input id="" type="text"
                                                                class="form-control valid" name="{{ $item->slug }}"
                                                                value="{{ base64_decode($data['user']->userDetails[$item->slug]) }}"
                                                                autofocus="" aria-invalid="false" min="0">
                                                        </div>
                                                    @endif
                                                </div>
                                            @empty
                                                {{ __('common.no_data') }}
                                            @endforelse

                                            <div class="form-group text-start">
                                                <button type="submit"
                                                    class="btn btn-primary text-white">{{ __('common.update') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                @endif
                                @if (
                                    $data['moduleStatus']->multi_currency_status ||
                                        $data['moduleStatus']->multilang_status ||
                                        ($data['moduleStatus']->mlm_plan == 'Binary' && $data['user']->user_type == 'user') ||
                                        $data['moduleStatus']->google_auth_status)
                                    <div class="tab-pane fade" id="v-pills-settings" role="tabpanel"
                                        aria-labelledby="v-pills-settings-tab">
                                        <form action="{{ route('update.default.settings', $data['user']->id) }}"
                                            method="post" enctype="multipart/form-data">
                                            @csrf
                                            @if ($data['moduleStatus']->multilang_status)
                                                <div class="form-group row">
                                                    <label for=""
                                                        class="col-md-4 control-label">{{ __('profile.language') }}
                                                    </label>
                                                    <div class="col-md-6">
                                                        <select name="default_lang" id="deafult-lang"
                                                            class="form-select">
                                                            @forelse ($languages as $item)
                                                                <option value="{{ $item->id }}"
                                                                    @selected($data['user']->default_lang == $item->id)>{{ $item->name }}
                                                                </option>
                                                            @empty
                                                                <option value="">{{ __('common.no_data') }}
                                                                </option>
                                                            @endforelse
                                                        </select>
                                                    </div>
                                                </div>

                                            @endif
                                            @if ($data['moduleStatus']->multi_currency_status)
                                                <div class="form-group row">

                                                    <label for=""
                                                        class="col-md-4 control-label">{{ __('profile.currency') }}
                                                    </label>
                                                    <div class="col-md-6">
                                                        <select name="default_currency" id="default_currency"
                                                            class="form-select">
                                                            @forelse ($currencies  as $item)
                                                                <option value="{{ $item->id }}"
                                                                    @selected($data['user']->default_currency == $item->id)>
                                                                    <span>{{ $item->symbol_left }}</span>
                                                                    <span>{{ $item->title }}</span>
                                                                </option>
                                                            @empty
                                                                <option value="">{{ __('common.no_data') }}
                                                                </option>
                                                            @endforelse
                                                        </select>
                                                    </div>
                                                </div>
                                            @endif

                                            @if ($data['user']->user_type == 'user' && $data['moduleStatus']->mlm_plan == 'Binary')
                                                <div class="form-group row">

                                                    <label for=""
                                                        class="col-md-4 control-label">{{ __('profile.binary_position_lock') }}
                                                    </label>
                                                    <div class="col-md-6">
                                                        <select name="binary_position" id="binary_position"
                                                            class="form-select">
                                                            <option value="any" @selected($data['user']->binary_leg == 'any')>
                                                                {{ __('profile.none') }}</option>
                                                            <option value="left" @selected($data['user']->binary_leg == 'left')>
                                                                {{ __('profile.left_leg') }}</option>
                                                            <option value="right" @selected($data['user']->binary_leg == 'right')>
                                                                {{ __('profile.right_leg') }}</option>
                                                            <option value="weak_leg" @selected($data['user']->binary_leg == 'weak')>
                                                                {{ __('profile.weak_leg') }}
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                            @endif

                                            @if ($data['moduleStatus']->google_auth_status)
                                                <div class="form-group row">

                                                    <label for=""
                                                        class="col-md-4 control-label">{{ __('profile.two_step_verification') }}
                                                    </label>
                                                    <div class="col-md-6">
                                                        <select name="google_auth_status" id="google_auth"
                                                            class="form-select">
                                                            <option value="1" @selected($data['user']->google_auth_status)>
                                                                {{ __('profile.enabled') }}</option>
                                                            <option value="0" @selected(!$data['user']->google_auth_status)>
                                                                {{ __('profile.disabled') }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="form-group text-start">
                                                <button type="submit"
                                                    class="btn btn-primary text-white">{{ __('common.update') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    @include('admin.profile.inc.modal')


@endsection
@push('scripts')
    <script>
        $(() => {
            $.ajax({
                url: "{{ route('ajax.state') }}",
                type: 'get',
                success: function(response) {
                    $("#state").html(" ");
                    $("#state").html(response.state);
                }
            });
            let country = $('#country').val();
            let userId = $('#userId').val();
            var dataString = "userId=" + userId;

            let url = "{{ route('country.state', ':country') }}";
            url = url.replace(':country', country);
            $.ajax({
                data: dataString,
                url: url,
                type: 'get',
                success: function(response) {

                    $('#States').html(' ');
                    $('#States').html(response.state);
                }
            });
            getUsers();
            let urlString = window.location.href;
            let paramString = urlString.split('?')[1];
            let queryString = new URLSearchParams(paramString);
        });
        $('#userData').select2({
            placeholder: 'Username',
            ajax: {
                url: "{{ route('load.users') }}",
                dataType: 'json',
                delay: 250,

                processResults: function(result) {

                    return {
                        results: $.map(result.data, function(item) {
                            return {
                                text: item.username,
                                id: item.id,

                            }
                        })
                    };

                },
                cache: true
            }
        });

        const addPersonalDetails = async (form) => {
            event.preventDefault()

            var formElements = new FormData(form);
            console.log(formElements);
            var dobInput = form.querySelector('input[name="dob"]');
            var dobValue = new Date(dobInput.value);
            var currentDate = new Date();
            var age = currentDate.getFullYear() - dobValue.getFullYear();
            if (age < 18) {
                let msg = "You need to be 18 years old...!"
                notifyError(msg)
                formvalidationError(form, err)
            }
            for (var [key, value] of formElements) {
                form.elements[key].classList.remove('is-invalid', 'd-block')
            }
            $('.invalid-feedback').remove()

            let url = form.action
            let data = getForm(form)

            const res = await $.post(`${url}`, data)
                .catch((err) => {
                    if (err.status === 422) {
                        let msg = "Please check the values you've submitted"
                        notifyError(msg)
                        formvalidationError(form, err)
                    } else if (err.status === 401) {
                        let errors = err.responseJSON.errors;
                        notifyError(errors)
                    }
                })
            if (typeof(res) != 'undefined')
                notifySuccess(res.message)

        }
        const addBankDetails = async (form) => {
            event.preventDefault()

            var formElements = new FormData(form);

            for (var [key, value] of formElements) {
                form.elements[key].classList.remove('is-invalid', 'd-block')
            }
            $('.invalid-feedback').remove()

            let url = form.action
            let data = getForm(form)
            console.log(data);
            const res = await $.post(`${url}`, data)
                .catch((err) => {
                    if (err.status === 422) {
                        let msg = "Please check the values you've submitted"
                        notifyError(msg)
                        formvalidationError(form, err)
                    } else if (err.status === 401) {
                        let errors = err.responseJSON.errors;
                        notifyError(errors)
                    }
                });
                console.log(res);
            if (typeof(res) != 'undefined')
                notifySuccess(res.message)

        }
        const addContactDetails = async (form) => {
            event.preventDefault()

            var formElements = new FormData(form);
            console.log(formElements);

            for (var [key, value] of formElements) {
                form.elements[key].classList.remove('is-invalid', 'd-block')
            }
            $('.invalid-feedback').remove()

            let url = form.action
            let data = getForm(form)

            const res = await $.post(`${url}`, data)
                .catch((err) => {
                    if (err.status === 422) {
                        let msg = "Please check the values you've submitted"
                        notifyError(msg)
                        formvalidationError(form, err)
                    } else if (err.status === 401) {
                        let errors = err.responseJSON.errors;
                        notifyError(errors)
                    }
                })
            if (typeof(res) != 'undefined')
                notifySuccess(res.message)

        }

        const getUserData = async (form) => {
            let url = "{{ route('profile.view') }}";

            const res = $.get(`${url}`)

                .catch((err) => {

                    if (err.status === 422) {
                        formvalidationError(form, err)
                    }
                }).then((result) => {


                })
        };

        $(document).on('change', '#country', function() {
            let country = $('#country').val();
            let userId = $('#userId').val();
            var dataString = "userId=" + userId;

            let url = "{{ route('country.state', ':country') }}";
            url = url.replace(':country', country);
            $.ajax({
                data: dataString,
                url: url,
                type: 'get',
                success: function(response) {

                    $('#States').html(' ');
                    $('#States').html(response.state);
                }
            });
        });

        const changeUserStatus = async (user) => {
            let route = `{{ route('change.user.active.status') }}`;
            const res = await $.post(`${route}`, {
                    user
                })
                .catch((err) => {
                    if (err.status == 422) {
                        alert(422)
                    } else if (err.status === 401) {
                        let errors = err.responseJSON.errors;
                        notifyError(errors)
                    }
                });
            if (typeof(res) != 'undefined') {
                $('#userActive-status').html(`${res.data}`)
                let status = res.data;
                console.log(status);
                if (status == 'Blocked') {
                    $('#SwitchCheckSizelg').addClass('bg-danger');
                    $('#userActive-status').removeClass('text-success');
                    $('#userActive-status').addClass('text-danger');
                } else {
                    $('#SwitchCheckSizelg').removeClass('bg-danger');
                    $('#userActive-status').removeClass('text-danger');
                    $('#userActive-status').addClass('text-success');
                }
            }

        }

        const changePassword = async (form) => {
            try {
                event.preventDefault();

                var formElements = new FormData(form);
                for (var [key, value] of formElements) {
                    form.elements[key].classList.remove('is-invalid', 'd-block')
                }
                $('.invalid-feedback').remove()

                let url = form.action
                let data = getForm(form)

                const res = await $.post(`${url}`, data)
                form.reset();
                $('#resetPassword').modal('hide');
                notifySuccess(res.message);
            } catch (error) {
                if (error.status === 422) {
                    let msg = "Please check the values you've submitted"
                    notifyError(msg)
                    formvalidationError(form, error)
                } else if (error.status === 401) {
                    let errors = error.responseJSON.errors;
                    notifyError(errors)
                }
            }
        }

        const changeTransPassword = async (form) => {
            try {
                event.preventDefault();

                var formElements = new FormData(form);
                for (var [key, value] of formElements) {
                    form.elements[key].classList.remove('is-invalid', 'd-block')
                }
                $('.invalid-feedback').remove()

                let url = form.action
                let data = getForm(form)

                const res = await $.post(`${url}`, data)
                form.reset();
                $('#resetTransactionPassword').modal('hide');
                console.log(res.message);
                notifySuccess(res.message);


            } catch (error) {
                if (error.status === 422) {
                    let msg = "Please check the values you've submitted";
                    notifyError(msg);
                    formvalidationError(form, error);
                } else if (error.status === 401) {
                    let errors = error.responseJSON.errors;
                    notifyError(errors)
                }
            }
        }

        const addPaymentDetails = async (form) => {
            try {
                event.preventDefault()
                var formElements = new FormData(form);

                for (var [key, value] of formElements) {
                    console.log(key);
                    form.elements[key].classList.remove('is-invalid', 'd-block')
                }
                $('.invalid-feedback').remove()

                let url = form.action
                let data = getForm(form)

                const res = await $.post(`${url}`, data)
                notifySuccess(res.message)
            } catch (error) {
                if (error.status === 422) {
                    let msg = "Please check the values you've submitted"
                    notifyError(msg)
                    formvalidationError(form, error)
                } else if (error.status === 401) {
                    let errors = error.responseJSON.errors;
                    notifyError(errors)
                }
            }

        }

        const addAdditionalDetails = async (form) => {
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
                        let msg = "Please check the values you've submitted"
                        notifyError(msg)
                        formvalidationError(form, err)
                    } else if (err.status === 401) {
                        let errors = err.responseJSON.errors;
                        notifyError(errors)
                    }
                })
            if (typeof(res) != 'undefined')
                notifySuccess(res.message)

        }

        $(document).on('change', '#name', function() {
            let userId = $('#userId').val();
            var dataString = "userId=" + userId;

            var image = $(this)[0].files[0];
            var formData = new FormData();
            formData.append('userId', userId);
            formData.append('image', image);
            var data = formData;
            let url = "{{ route('profile.update') }}";

            $.ajax({
                    type: 'POST',
                    enctype: 'multipart/form-data',
                    url,
                    data,
                    processData: false,
                    contentType: false,
                    cache: false,
                })
                .catch((err) => {
                    console.log(err);
                    if (err.status === 422) {
                        notifyError(err.responseJSON.message);
                    } else if (err.status === 401) {
                        let errors = err.responseJSON.errors;
                        notifyError(errors)
                    }
                }).then((res) => {
                    console.log(res);
                    if (typeof(res) != "undefined") {
                        // $('#profView').load(location.href + ' #profile_view_pg');
                        $("#user-img").attr("src", "")
                        $("#user-img").attr("src", res.image)
                        notifySuccess(res.message);
                    }
                })
        });

        const updatePv = async (action) => {
            try {
                event.preventDefault()
                $('#updatePv button').addClass('disabled')
                $('#cancel_button').text("{{ __('common.loading') }}")
                $('#deduct_button').text("{{ __('common.loading') }}")
                $('#add_button').text("{{ __('common.loading') }}")
                $('.invalid-feedback').remove()
                $('#pv').removeClass('is-invalid');
                let userid = "{{ $data['user']->id }}";
                let url = "{{ route('update.user.pv') }}/" + userid;
                let data = {
                    pv: $('#pv').val(),
                    action: action,
                    user_id: userid
                }

                const res = await $.post(`${url}`, data)
                location.reload();
            } catch (error) {
                $('#updatePv button').removeClass('disabled')
                $('#cancel_button').text("{{ __('common.cancel') }}")
                $('#deduct_button').text("{{ __('profile.deduct_pv') }}")
                $('#add_button').text("{{ __('profile.update_pv') }}")
                if (error.status === 422) {
                    let msg = "Please check the values you've submitted";
                    inputvalidationError('pv', error)
                    notifyError(msg)
                } else if (error.status === 401) {
                    let e = error.responseJSON.errors;
                    notifyError(e)
                }

            }
        }
    </script>
@endpush
