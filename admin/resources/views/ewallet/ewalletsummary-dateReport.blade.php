<!-- <div class="col-md-6">
    <div class="debit-credit">
        <div class="list-group">
            <div class="list-group-item list-group-item-header color-text credit">
                <h5>{{ __('ewallet.credit') }}</h5>
            </div>
            <div id="credited_items" class="summary-tile-grid">
                <div class="row mt-4">
                    @foreach ($ewalletCategories as $category)
                        @if ($details->has($category) && $details[$category]['type'] == "credit")
                            <div class="col-md-3">
                                <div class="card border-start border-success">
                                    <div class="card-body">
                                        <h6>{{ __("ewallet.$category") }}</h6>
                                        <strong class="text-success amount">$ {{ formatCurrency($details[$category]['amount']) }}</strong>
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

<div class="col-md-6">
    <div class="debit-credit">
        <div class="list-group">
            <div class="list-group-item list-group-item-header color-text debit">
                <h5>{{ __('ewallet.debit') }}</h5>
            </div>
            <div id="debited_items" class="summary-tile-grid">

                <div class="row mt-4">
                    @foreach ($ewalletCategories as $category)
                        @if ($details->has($category) && $details[$category]['type'] == "debit")
                            <div class="col-md-3">
                                <div class="card border-start border-danger">
                                    <div class="card-body">
                                        <h6>{{ __("ewallet.$category") }}</h6>
                                        <strong class="text-danger amount">$ {{ formatCurrency($details[$category]['amount']) }}</strong>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div> -->
<div class="col-xl-6 col-lg-6 col-md-12">
    <div class="debit-credit">
        <div class="list-group">
            <div class="list-group-item list-group-item-header color-text credit">
                <h5>{{ __('common.credit') }}</h5>
            </div>
            <div id="credited_items" class="summary-tile-grid">
                <div class="row mt-4">
                    @foreach ($ewalletCategories as $category)
                        @if ($details->has($category) && $details[$category]['type'] == "credit")
                            <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-4 col-sm-6 col-12">
                                <div class="card border-start border-success">
                                    <div class="card-body">
                                        <h6>{{ __("ewallet.$category") }}</h6>
                                        <strong
                                            class="text-success amount">{{ $currency }}
                                            {{ formatCurrency($details[$category]['amount']) }}</strong>
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
<div class="col-xl-6 col-lg-6 col-md-12">
    <div class="debit-credit">
        <div class="list-group">
            <div class="list-group-item list-group-item-header color-text debit mt-3">
                <h5>{{ __('common.debit') }}</h5>
            </div>
            <div id="debited_items" class="summary-tile-grid">

                <div class="row mt-4">
                    @foreach ($ewalletCategories as $category)
                        @if ($details->has($category) && $details[$category]['type'] == "debit")
                            <div class="col-md-6">
                                <div class="card border-start border-danger">
                                    <div class="card-body">
                                        <h6>{{ __("ewallet.$category") }}</h6>
                                        <strong
                                            class="text-danger amount">{{ $currency }}
                                            {{ formatCurrency($details[$category]['amount']) }}</strong>
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
