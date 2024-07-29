@dd(1)
@extends('layouts.app')
@section('content')
    <div class="container mt-5">
        @include('admin.settings.links_advanced')
        <form action="{{ route('commission.update', $commission['id']) }}" method="post" class="mt-3">
            @csrf
            <div class="form-group">
                <label>{{ __('settings.purchase_wallet_commission') }}(%)</label>
                <input type="number" name="purchase_income_perc" class="form-control"
                    value="{{ $commission['purchase_income_perc'] }}" min="0">
            </div>
            <div class="form-group">
                <label>{{ __('settings.service_charge') }} (%)</label>
                <input type="number" name="service_charge" class="form-control"
                    value="{{ $commission['service_charge'] }}" min="0">
            </div>
            <div class="form-group">
                <label>{{ __('common.tax') }} (%)</label>
                <input type="number" name="tds" class="form-control" value="{{ $commission['tds'] }}" min="0">
            </div>
            <div class="form-check ms-5">
                <input class="form-check-input" type="checkbox" value="yes" name="skip_blocked_users_commission"
                    {{ $commission['skip_blocked_users_commission'] == 'yes' ? 'checked' : '' }}>
                <label class="form-check-label">
                    {{ __('settings.skip_bonus_for_blocked_users') }}
                </label>
            </div>
            <div class="form-group">
                <label>{{ __('common.transaction_fee') }}</label>
                <input type="number" name="trans_fee" class="form-control" value="{{ $commission['trans_fee'] }}"
                    min="0">
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">{{ __('common.update') }}</button>
            </div>
        </form>
    </div>
@endsection
