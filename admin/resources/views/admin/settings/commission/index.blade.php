@extends('layouts.app')
@section('title', trans('settings.commission'))
@section('content')
    <div class="container-fluid settings_page">
        <div class="card">
            <div class="card-header">

                @include('admin.settings.inc.links')

                <div class="">
                    <div class="alert alert-info alert-dismissible fade show mt-3" role="alert">
                        <h4><i
                                class="dripicons-device-desktop me-2"></i>{{ __('settings.commission_settings') }}</h4>
                        <p class="text-justify mt-lg-2">
                            {{ __('settings.commission_paragraph') }}
                        </p>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>

                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('commission.update') }}" method="post" class="">
                    <div class="row">
                    @csrf
                    @if ($moduleStatus->purchase_wallet)
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>{{ __('settings.purchase_wallet_commission') }}</label>
                            <input type="number" name="purchase_wallet_commission" class="form-control"
                            autocomplete="off" value="{{ $configuration['purchase_income_perc'] }}" min="0">
                            @error('purchase_wallet_commission')
                                <span class="text-danger form-text">{{ $message }}</span>
                            @enderror
                        </div>
                        </div>
                    @endif
                    <div class="col-md-2">
                    <div class="form-group">
                        <label>{{ __('settings.service_charge') }}</label>
                        <input type="number" name="service_charge" class="form-control"
                        autocomplete="off" value="{{ $configuration['service_charge'] }}" min="0">
                        @error('service_charge')
                            <span class="text-danger form-text">{{ $message }}</span>
                        @enderror
                    </div>
                    </div>

                    <div class="col-md-2">
                    <div class="form-group">
                        <label>{{ __('settings.tax_percentage') }}</label>
                        <input type="number" name="tax" class="form-control" value="{{ $configuration['tds'] }}"
                        autocomplete="off"  min="0">
                        @error('`')
                            <span class="text-danger form-text">{{ $message }}</span>
                        @enderror
                    </div>
                    </div>

                    <div class="col-md-2">
                    <div class="form-group">
                        <label>{{ __('settings.transaction_fee') }}</label>
                        <input type="number" name="transaction_fee" class="form-control"
                        autocomplete="off" value="{{ formatCurrency($configuration['trans_fee']) }}" min="0">
                        @error('transaction_fee')
                            <span class="text-danger form-text">{{ $message }}</span>
                        @enderror
                    </div>
                    </div>
                    </div>

                    <div class="row">
                    <div class="col-md-4">
                    <div class="form-check ms-3 form-group">
                        <label class="form-check-label" for="u">
                            <input class="form-check-input check_bx_new" type="checkbox" value="1"
                                name="skip_blocked_users_commission"
                                {{ $configuration['skip_blocked_users_commission'] ? 'checked' : '' }} id="u">

                            {{ __('settings.skip_bonus_for_blocked_users') }}
                        </label>
                    </div>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">{{ __('common.update') }}</button>
                    </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
