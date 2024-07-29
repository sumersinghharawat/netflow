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
                                    <h6>{{ $wallet_details['paid']['type'] }}</h6>
                                    <strong class="text-success amount">$
                                        {{ $wallet_details['paid']['amount'] }}</strong>
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
                                    <h6>{{ $wallet_details['pending']['type'] }}</h6>
                                    <strong class="text-success amount">$
                                        {{ $wallet_details['pending']['amount'] }}</strong>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="Report" class="row mt-4">
    <div class="col-md-12">
        <div class="debit-credit">
            <div class="list-group">
                <div class="list-group-item list-group-item-header color-text credit">
                    <h5>{{ __('business.income') }}</h5>
                </div>
                <div id="credited_items" class="summary-tile-grid">
                    <div class="row mt-4">
                        @foreach ($wallet_details as $key => $detail)
                            @if ($detail['type'] == 'income')
                                <div class="col-md-4">
                                    <div class="card border-start border-success">
                                        <div class="card-body">
                                            <h6>
                                                {{ $key }}

                                            </h6>
                                            <strong class="text-success amount">$ {{ $detail['amount'] }}</strong>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="bonusReport" class="row mt-4">
    <div class="col-md-12">
        <div class="debit-credit">
            <div class="list-group">
                <div class="list-group-item list-group-item-header color-text credit">
                    <h5>{{ __('business.bonus') }}</h5>
                </div>
                <div id="credited_items" class="summary-tile-grid">
                    <div class="row mt-4">
                        @foreach ($wallet_details as $key => $detail)
                            @if ($detail['type'] == 'bonus')
                                <div class="col-md-4">
                                    <div class="card border-start border-primary">
                                        <div class="card-body">
                                            <h6>
                                                {{ $key }}
                                            </h6>
                                            <strong class="text-success amount">$ {{ $detail['amount'] }}</strong>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>
