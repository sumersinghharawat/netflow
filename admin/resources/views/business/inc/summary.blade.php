<div class="row mt-4">
    <div class="col-md-6">
        <div class="debit-credit">
            <div class="list-group">
                <div class="list-group-item list-group-item-header color-text credit">
                    <h5>{{ __('business.paid') }}</h5>
                </div>
                <div id="credited_items" class="summary-tile-grid">
                    <div class="row mt-4">
                        <div class="col-md-4">
                            <div class="card border-start border-danger">
                                <div class="card-body">
                                    <h6>{{ __('business.paid') }}</h6>
                                    <strong class="text-success amount">{{ $currency }}
                                        {{ formatCurrency($totalPaid) }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="debit-credit">
            <div class="list-group">
                <div class="list-group-item list-group-item-header color-text debit">
                    <h5>{{ __('business.pending') }}</h5>
                </div>
                <div id="debited_items" class="summary-tile-grid">
                    <div class="row mt-4">
                        <div class="col-md-4">
                            <div class="card border-start border-warning">
                                <div class="card-body">
                                    <h6>{{ __('business.pending') }}</h6>
                                    <strong class="text-success amount">{{ $currency }}
                                        {{ formatCurrency($totalPending) }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="debit-credit">
            <div class="list-group">
                <div class="list-group-item list-group-item-header color-text credit ">
                    <h5>{{ __('business.income') }}</h5>
                </div>
                <div id="credited_items" class="summary-tile-grid">
                    <div class="row mt-4">
                        @forelse ($totlaIncome as $key => $income)
                            <div class="col-md-3">
                                <div class="card border-start border-success">
                                    <div class="card-body">
                                        <h6>
                                            {{ __('business.' . $key) }}
                                        </h6>
                                        <strong class="text-success amount">{{ $currency }}
                                            {{ formatCurrency($income) }}</strong>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-md-4">
                                <div class="card border-start border-success">
                                    <div class="card-body">
                                        <h6>
                                            {{ __('business.income') }}
                                        </h6>
                                        <strong class="text-success amount">$ 0.00</strong>
                                    </div>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

<div id="bonusReport"   class="col-md-12">
        <div class="debit-credit">
            <div class="list-group">
                <div class="list-group-item list-group-item-header color-text credit mt-3">
                    <h5>{{ __('business.bonus') }}</h5>
                </div>
                <div id="credited_items" class="summary-tile-grid">
                    <div class="row mt-4">
                        @forelse ($totalBonus as $key => $bonus)
                            <div class="col-md-3">
                                <div class="card border-start border-primary">
                                    <div class="card-body">
                                        <h6>
                                            {{ __('business.' . $key) }}
                                        </h6>
                                        <strong
                                            class="text-success amount">{{ $currency }}{{ formatCurrency($bonus) }}</strong>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-md-4">
                                <div class="card border-start border-primary">
                                    <div class="card-body">
                                        <h6>
                                            {{ __('business.bonus') }}
                                        </h6>
                                        <strong class="text-success amount">{{ $currency }}
                                            {{ $bonus['amount'] }}</strong>
                                    </div>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
