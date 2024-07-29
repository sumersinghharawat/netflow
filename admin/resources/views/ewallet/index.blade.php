@extends('layouts.app')
@section('title', __('ewallet.e-wallet'))
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __('ewallet.e-wallet') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="text-white">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-primary" data-bs-toggle="offcanvas"
                                    data-bs-target="#ewallet-fund-transfer"
                                    aria-controls="offcanvasRight">{{ __('ewallet.fund_transfer') }}</button>
                                <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false" id="btnGroupVerticalDrop1">
                                    <i class="mdi mdi-chevron-down"></i>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="btnGroupVerticalDrop1">
                                    <a class="dropdown-item" href="#ewallet-fund-credit" data-bs-toggle="offcanvas"
                                        role="button" aria-controls="transferEpin">{{ __('ewallet.fund_credit') }}</a>
                                    <a class="dropdown-item" data-bs-toggle="offcanvas" href="#ewallet-fund-debit"
                                        role="button" aria-controls="addPurchaseEpin">{{ __('ewallet.fund_debit') }}</a>
                                </div>
                            </div>

                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="page_top_cnt_boxs_view1">

        <div class="col-sm-12">
            <div class="card">

                <div class="row no-gutters align-items-center page_top_cnt_boxs_view_box_2">
                    <!-- <div class="card_img_bx">
                        <img class="card-img bg-success rounded img-fluid"
                            src="{{ asset('assets/images/ewallet/income-w.png') }}" alt="Card image">
                    </div> -->
                    <div class="card-body">
                    <p class="card-text text-1">{{ __('common.credited') }}</p>
                        <h5 class="card-title">{{ $currency }} {{ formatNumberShort(formatCurrency($total['credit'])) }}
                            <div class="tooltip-index card_tooltip">
                                <i class='bx bx-info-circle tooltip--button dashboard_top_box_tooltip_ico'></i>
                                <div class="tooltip--content" id="description-one" role="tooltip">
                                    <p>{{ __('ewallet.all_credits') }}</p>
                                </div>
                            </div>
                        </h5>
                       
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-12">
            <div class="card">

                <div class="row no-gutters align-items-center page_top_cnt_boxs_view_box_2">
                    <!-- <div class="card_img_bx">
                        <img class="card-img bg-success rounded bg-danger rounded"
                            src="{{ asset('assets/images/ewallet/Bonus-w.png') }}" alt="Card image">
                    </div> -->
                    <div class="card-body">
                    <p class="card-text ">{{ __('common.debited') }}</p>
                        <h5 class="card-title">{{ $currency }}
                            {{ formatNumberShort(formatCurrency($total['debit'])) }}
                            <div class="tooltip-index card_tooltip">
                                <i class='bx bx-info-circle tooltip--button dashboard_top_box_tooltip_ico'></i>
                                <div class="tooltip--content" id="description-one" role="tooltip">
                                    <p>{{ __('ewallet.all_debits') }}</p>
                                </div>
                            </div>
                        </h5>
                      
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-12">
            <div class="card">

                <div class="row no-gutters align-items-center page_top_cnt_boxs_view_box_2">
                    <!-- <div class="card_img_bx">
                        <img class="card-img bg-info rounded img-fluid"
                            src="{{ asset('assets/images/ewallet/E-Wallet-w.png') }}" alt="Card image">
                    </div> -->
                    <div class="card-body">
                    <p class="card-text ">{{ __('ewallet.ewallet_balance') }}</p>
                        <h5 class="card-title">{{ $currency }}
                            {{ formatNumberShort(formatCurrency($total['credit'] - $total['debit'])) }}
                            <div class="tooltip-index card_tooltip">
                                <i class='bx bx-info-circle tooltip--button dashboard_top_box_tooltip_ico'></i>
                                <div class="tooltip--content" id="description-one" role="tooltip">
                                    <p>{{ __('ewallet.credit_debit') }}</p>
                                </div>
                            </div>
                        </h5>
                     
                    </div>
                </div>
            </div>
        </div>

        @if ($moduleStatus->purchase_wallet)
            <div class="col-sm-12">
                <div class="card">

                    <div class="row no-gutters align-items-center page_top_cnt_boxs_view_box_2">

                        <!-- <div class="card_img_bx purchase_wallt rounded card-img">
                            <span class="">
                                <i class="bx bxs-wallet font-size-26"></i>
                            </span>
                        </div> -->
                        <div class="card-body">
                        <p class="card-text">{{ __('ewallet.purchase_wallet') }}</p>
                            <h5 class="card-title">{{ $currency }}
                                {{ formatNumberShort(formatCurrency(round($purchaseWalletBalance, 2))) }}
                                <div class="tooltip-index card_tooltip">
                                    <i class='bx bx-info-circle tooltip--button dashboard_top_box_tooltip_ico'></i>
                                    <div class="tooltip--content" id="description-one" role="tooltip">
                                        <p>{{ __('ewallet.all_user_purchase_wallet_total') }}</p>
                                    </div>
                                </div>
                            </h5>
                           
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="col-sm-12">
            <div class="card">

                <div class="row no-gutters align-items-center page_top_cnt_boxs_view_box_2">
                    <!-- <div class="card_img_bx commission_ernd rounded card-img">
                        <span class="">
                            <i class="bx bxs-bar-chart-alt-2 font-size-26"></i>
                        </span>
                    </div> -->
                    <div class="card-body">
                    <p class="card-text">{{ __('ewallet.commission_earned') }}</p>
                        <h5 class="card-title">{{ $currency }}
                            {{ formatNumberShort(formatCurrency($commissionEarned)) }}
                            <div class="tooltip-index card_tooltip">
                                <i class='bx bx-info-circle tooltip--button dashboard_top_box_tooltip_ico'></i>
                                <div class="tooltip--content" id="description-one" role="tooltip">
                                    <p>{{ __('ewallet.all_earned_commissions') }}</p>
                                </div>
                            </div>
                        </h5>
                      
                    </div>
                </div>
            </div>
        </div>

    </div>


    <div class="row">
        <div class="col-md-12">
            <div class="card business-card">
                <div class="card-body">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#ewallet-summary-tab" role="tab"
                                id="ewallet-summary">
                                <span class="d-none d-sm-block">
                                    {{ __('ewallet.ewallet_summary') }}
                                </span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#ewallet-transaction-tab"
                                onclick="getEWalletTransaction(this)" role="tab" id="ewallet-transaction">
                                <span class="d-none d-sm-block">{{ __('ewallet.ewallet_transaction') }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#ewallet-balance-tab" role="tab"
                                onclick="getEwalletBalance()">
                                <span class="d-none d-sm-block">{{ __('ewallet.ewallet_balance') }}</span>
                            </a>
                        </li>
                        @if ($moduleStatus->purchase_wallet)
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#ewallet-purchase-tab" role="tab"
                                    data-url="{{ route('ewallet.purchase') }}" onclick="getPurchaseWallet(this)">
                                    <span class="d-none d-sm-block">{{ __('ewallet.purchase_wallet') }}
                                    </span>
                                </a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#ewallet-statement-tab" role="tab"
                                data-url="{{ route('ewallet.statement') }}" onclick="getEwalletStatement(this)">
                                <span class="d-none d-sm-block">{{ __('ewallet.ewallet_statement') }}
                                </span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#ewallet-userearnings-tab"
                                data-url="{{ route('user.earnings') }}" onclick="getUserEarnings(this)" role="tab">
                                <span class="d-block d-sm-none"><i class="fas fa-cog"></i></span>
                                <span class="d-none d-sm-block"> {{ __('ewallet.user_earnings') }}
                                </span>
                            </a>
                        </li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content text-muted">

                        <div class="tab-pane" id="ewallet-summary-tab" role="tabpanel">
                            <div class="filter_box_new">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="">{{ __('common.date') }}</label>
                                        <input type="text" class="date-range-picker form-control"
                                            id="wallet-summary" />
                                    </div>
                                </div>
                            </div>

                            <div id="dateReport" class="row mt-4">
                                <div class="col-xl-6 col-lg-6 col-md-12">
                                    <div class="debit-credit">
                                        <div class="list-group">
                                            <div class="list-group-item list-group-item-header color-text credit">
                                                <h5>{{ __('common.credit') }}</h5>
                                            </div>
                                            <div id="credited_items" class="summary-tile-grid">
                                                <div class="row mt-4">
                                                    @foreach ($ewalletCategories as $category)
                                                        @if ($details->has($category) && $details[$category]['type'] == 'credit')
                                                            <div class="col-md-4">
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
                                                        @if ($details->has($category) && $details[$category]['type'] == 'debit')
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
                            </div>
                        </div>

                        <div class="tab-pane" role="tabpanel" id="ewallet-transaction-tab">
                            <div class="filter_box_new">
                                <div class="row ">
                                    <div class="col-md-2">
                                        <label for="">{{ __('common.username') }}</label>
                                        <select class="form-control select2-ajax select2-search-user select2-multiple d-none"
                                            multiple="multiple" id="transaction-user" name="username" data-user="transactions"></select>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="">{{ __('common.status') }}</label>
                                        <select class="form-select select2 select2-multiple d-none" multiple="multiple"
                                            name="type" id="type" data-status="transactions">
                                            <option value="credit">{{ __('common.credited') }}</option>
                                            <option value="debit">{{ __('common.debited') }}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="">{{ __('common.category') }}</label>
                                        <select class="select2 select2-multiple form-select d-none" multiple="multiple"
                                            name="category" id="category" data-category="transactions">
                                            @foreach ($ewalletCategories as $category)
                                                @if ($category != null)
                                                    <option value="{{ $category }}">{{ __("ewallet.$category") }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="">{{ __('ewallet.ewallet_transaction') }}</label>
                                        <input type="text" class="date-range-picker form-control"
                                            id="wallet-transaction" data-date="transactions"/>
                                    </div>
                                    <div class="col-md-3" style="margin-top:23px">
                                        <a href="{{ route('ewallet.transaction') }}"
                                            onclick="getEWalletTransaction(this)"
                                            class="btn btn-primary" data-route="transactions">{{ __('common.view') }}</a>
                                        <a href="{{ route('ewallet') }}" onclick="resetSearch('transactions')"
                                            class="btn btn-primary">{{ __('common.reset') }}</a>
                                    </div>
                                </div>
                            </div>
                            <div id="TransDateReport" class="row mt-2">
                                <table id="data-table-transaction" class="table  table-hover">
                                    <thead>
                                        <th>{{ __('common.member_name') }}</th>
                                        <th>{{ __('common.category') }}</th>
                                        <th>{{ __('common.amount') }}</th>
                                        {{-- <th>{{ __('ewallet.fund_transfer_fee') }}</th> --}}
                                        <th>{{ __('common.transaction_date') }}</th>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane" id="ewallet-balance-tab" role="tabpanel">
                            <div class="filter_box_new">
                                <div class="row">
                                    <div class="col-md-2">
                                        <label for="">{{ __('common.username') }}</label>
                                        <select class="form-control select2-ajax select2-multiple select2-search-user d-none"
                                            multiple="multiple" name="username" data-user="balance" id="balance-user"></select>
                                    </div>
                                    <div class="col-md-3" style="margin-top:23px">
                                        <button class="btn btn-primary"
                                            onclick="getEwalletBalance(this)">{{ __('common.search') }}</button>
                                        <a href="{{ route('ewallet') }}" data-route="balance" onclick="resetSearch('balance')"
                                            class="btn btn-danger">{{ __('common.reset') }}</a>
                                    </div>
                                </div>
                            </div>
                            <div id="ewalletBalance" class="row mt-2">
                                <table id="data-table-balance" class="table  table-hover">
                                    <thead class="table">
                                        <th>{{ __('common.member_name') }}</th>
                                        <th>{{ __('ewallet.ewallet_balance') }}</th>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @if ($moduleStatus->purchase_wallet)
                            <div class="tab-pane" id="ewallet-purchase-tab" role="tabpanel">
                                <div class="row">
                                    <div class="card text-white-50 addon-box">
                                        <h5 class="mb-2 text-black"><i
                                                class="mdi mdi-alert-circle-outline me-3"></i>{{ __('common.addon_module') }}
                                        </h5>
                                    </div>
                                </div>
                                <div class="filter_box_new">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label for="">{{ __('common.username') }}</label>
                                            <select class="form-control select2-ajax select2-multiple select2-search-user d-none"
                                                multiple="multiple" name="username" id="purchase-wallet-user" data-user="purchaseWallet">
                                                <option value="{{ auth()->user()->id }}" selected>
                                                    {{ auth()->user()->username }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-3" style="margin-top:23px">
                                            <button class="btn btn-primary" onclick="getPurchaseWallet(this)" data-route="purchaseWallet"
                                                data-url="{{ route('ewallet.purchase') }}">{{ __('common.search') }}</button>
                                            <a href="{{ route('ewallet') }}" onclick="resetSearch('purchaseWallet')"
                                                class="btn btn-danger">{{ __('common.reset') }}</a>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <table id="data-table-purchase-wallet" class="table  table-hover">
                                        <thead class="table">
                                            <th>{{ __('common.description') }}</th>
                                            <th>{{ __('common.amount') }}</th>
                                            <th>{{ __('common.balance') }}</th>
                                            <th>{{ __('common.transaction_date') }}</th>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>


                            </div>
                        @endif

                        <div class="tab-pane" id="ewallet-statement-tab" role="tabpanel">
                            <div class="filter_box_new">
                                <div class="row">
                                    <div class="col-md-2">
                                        <label for="">{{ __('common.username') }}</label>
                                        <select class="form-control select2-ajax  select2-search-user d-none" selected data-user="statement"
                                            id="wallet-statement-user" name="username">
                                            <option value="{{ auth()->user()->id }}" >
                                                {{ auth()->user()->username }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-md-3" style="margin-top:23px">
                                        <button type="button" class="btn btn-primary"
                                            data-url="{{ route('ewallet.statement') }}" data-route="statement"
                                            onclick="getEwalletStatement(this)">{{ __('common.view') }}</button>
                                        <a href="{{ route('ewallet') }}" type="submit" onclick="resetSearch('statement')"
                                            class="btn btn-danger">{{ __('common.reset') }}</a>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-2">
                                <table id="data-table-wallet-statement" class="table  table-hover">
                                    <thead class="table">
                                        <th>{{ __('common.description') }}</th>
                                        <th>{{ __('common.amount') }}</th>
                                        <th>{{ __('common.balance') }}</th>
                                        <th>{{ __('common.transaction_date') }}</th>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>

                        </div>

                        <div class="tab-pane" id="ewallet-userearnings-tab" role="tabpanel">
                            <div class="filter_box_new">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <label for="">{{ __('common.username') }}</label>
                                                <select data-user="earnings"
                                                    class="form-control select2-ajax select2-multiple select2-search-user d-none"
                                                    name="username" multiple="multiple" id="user-earnings-user">
                                                    <option value="{{ auth()->user()->id }}" selected>
                                                        {{ auth()->user()->username }}
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label for="">{{ __('common.category') }}</label>
                                                <select class="form-control select2 select2-multiple d-none" multiple="multiple" data-category="earnings"
                                                    name="category" id="user-earnings-category">
                                                    @foreach ($earningsCategories as $category)
                                                        <option value="{{ $category }}">
                                                            {{ __("ewallet.{$category}") }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label for="">{{ __('common.date') }}</label>
                                                <input type="text" class="date-range-picker form-control"
                                                    id="user-earnings-date-picker" />
                                            </div>
                                            <div class="col-md-3" style="margin-top:23px">
                                                <button type="button" onclick="getUserEarnings(this)"
                                                    data-url="{{ route('user.earnings') }}" data-route="earnings"
                                                    class="btn btn-primary">{{ __('common.view') }}</button>
                                                <a href="{{ route('ewallet') }}" type="submit" onclick="resetSearch('earnings')"
                                                    class="btn btn-danger">{{ __('common.reset') }}</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-2">
                                <table id="data-table-user-earnings" class="table  table-hover">
                                    <thead class="table">
                                        <th>{{ __('common.category') }}</th>
                                        <th>{{ __('common.total_amount') }}</th>
                                        <th>{{ __('common.tax') }}</th>
                                        <th>{{ __('common.service_charge') }}</th>
                                        <th>{{ __('common.amount_payable') }}</th>
                                        <th>{{ __('common.transaction_date') }}</th>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('ewallet.inc._modal', ['transferFee' => $transferFee])
@endsection

@push('scripts')
    <script>
        var startDate = moment().subtract(1, 'year').add(1, 'day');
        var endDate   = moment().add(1, 'day');
        $(() => {
            getUsers();
            let activeId = "{{ $active }}"
            $(`#ewallet-${activeId}-tab`).addClass('active')
            $(`#ewallet-${activeId}`).addClass('active')
            $('#wallet-summary').daterangepicker({
                startDate,
                endDate,
                ranges: {
                    'All Time': [startDate, endDate],
                    'Last 30 days': [moment().subtract(29, 'days'), moment()],
                    'Last 90 days': [moment().subtract(89, 'days'), moment()],
                    'Last Year': [moment().subtract(1, 'year').add(1, 'day'), moment()],
                },
            }, callback);
            getUsersInsideCanvas('ewallet-fund-transfer', 'select2-canvas-transfer-from');
            getUsersInsideCanvas('ewallet-fund-transfer', 'select2-canvas-transfer-to');

            getUsersInsideCanvas('ewallet-fund-credit', 'select2-canvas-credit');
            getUsersInsideCanvas('ewallet-fund-debit', 'select2-canvas-debit');

        })

        const callback = async (start, end, label) => {
            data = {
                fromDate: start.format('YYYY-MM-DD'),
                toDate: end.format('YYYY-MM-DD')
            }

            const res = await $.get("{{ route('ewallet.dateReport') }}", data)
            $('#dateReport').html(res.data)
        }
        const getEWalletTransaction = async () => {
            event.preventDefault()
            $('.tab-pane').removeClass('active')
            $('#ewallet-transaction-tab').addClass('active')
            getUsers();

            let url = "{{ route('ewallet.transaction') }}";
            let params = {
                fromDate: startDate.format('YYYY-MM-DD'),
                toDate: endDate.format('YYYY-MM-DD'),
                users: $('#transaction-user').val(),
                category: $('#category').val(),
                type: $('#type').val()
            }
            console.log(params);
            var table = $('#data-table-transaction').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                "sDom": 'Lfrtlip',
                "bDestroy": true,
                "language": {
                    "emptyTable": "<div class='nodata_view'><img src='{{ asset('assets/images/nodata-icon.png') }}'' alt=''><span class='text-secondary fs-5'>{{ __('common.no_data') }}</span></div>"
                },
                ajax: {
                    type: "GET",
                    url: url,
                    data: params
                },
                columns: [{
                        data: 'username',
                        name: 'username',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'amount_type',
                        name: 'category',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'amount',
                        name: 'amount',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'date',
                        name: 'date',
                        orderable: false,
                        searchable: false
                    },
                ]
            });
        }
        const getEwalletBalance = async () => {
            event.preventDefault();
            let url = "{{ route('ewallet.balance') }}";
            getUsers();
            let param = {
                user: $('#balance-user').val()
            };

            console.log(param)
            var table = $('#data-table-balance').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                "bDestroy": true,
                "sDom": 'Lfrtlip',
                "language": {
                    "emptyTable": "<div class='nodata_view'><img src='{{ asset('assets/images/nodata-icon.png') }}'' alt=''><span class='text-secondary fs-5'>{{ __('common.no_data') }}</span></div>"
                },
                ajax: {
                    type: "GET",
                    url: url,
                    data: param
                },
                columns: [{
                        data: 'member',
                        name: 'member',

                    },
                    {
                        data: 'balance',
                        name: 'userBalance.balance_amount',
                        orderable: true
                    },
                ]
            });
        }
        const getPurchaseWallet = async (href) => {
            event.preventDefault();
            let url = href.dataset.url;
            console.log(url);
            getUsers();
            let param = {
                users: $('#purchase-wallet-user').val()
            }
            var table = $('#data-table-purchase-wallet').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                "bDestroy": true,
                "sDom": 'Lfrtlip',
                "bSort": false,
                "language": {
                    "emptyTable": "<div class='nodata_view'><img src='{{ asset('assets/images/nodata-icon.png') }}'' alt=''><span class='text-secondary fs-5'>{{ __('common.no_data') }}</span></div>"
                },
                ajax: {
                    type: "GET",
                    url: url,
                    data: param
                },
                columns: [{
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'amount',
                        name: 'purchase_wallet',
                        orderable: false
                    },
                    {
                        data: 'balance',
                        name: 'balance',
                        orderable: false
                    },
                    {
                        data: 'date',
                        name: 'date',
                        orderable: false
                    },

                ]
            });

        }
        const getEwalletStatement = async (href) => {
            event.preventDefault();
            let url = href.dataset.url;
            getUsers();
            let param = {
                user: $('#wallet-statement-user').val()
            }
            var table = $('#data-table-wallet-statement').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                orderable: false,
                "bDestroy": true,
                "sDom": 'Lfrtlip',
                "bSort": false,
                "language": {
                    "emptyTable": "<div class='nodata_view'><img src='{{ asset('assets/images/nodata-icon.png') }}'' alt=''><span class='text-secondary fs-5'>{{ __('common.no_data') }}</span></div>"
                },
                ajax: {
                    type: "GET",
                    url: url,
                    data: param
                },
                columns: [{
                        data: 'description',
                        name: 'description',
                        orderable: false,
                    },
                    {
                        data: 'amount',
                        name: 'amount',
                        orderable: false
                    },
                    {
                        data: 'balance',
                        name: 'balance',
                        orderable: false
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        orderable: false,
                    },

                ]
            });
        }
        const getUserEarnings = async (href) => {
            event.preventDefault();
            let url = href.dataset.url;
            getUsers();
            let param = {
                users: $('#user-earnings-user').val(),
                category: $('#user-earnings-category').val(),
                fromDate: startDate.format('YYYY-MM-DD'),
                toDate: endDate.format('YYYY-MM-DD')
            };
            var table = $('#data-table-user-earnings').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                "bDestroy": true,
                "sDom": 'Lfrtlip',
                "bSort": false,
                orderable: false,
                "language": {
                    "emptyTable": "<div class='nodata_view'><img src='{{ asset('assets/images/nodata-icon.png') }}'' alt=''><span class='text-secondary fs-5'>{{ __('common.no_data') }}</span></div>"
                },
                ajax: {
                    type: "GET",
                    url: url,
                    data: param
                },
                columns: [{
                        data: 'category',
                        name: 'category'
                    },
                    {
                        data: 'total_amount',
                        name: 'total_amount',
                        orderable: false
                    },
                    {
                        data: 'tax',
                        name: 'tax',
                        orderable: false
                    },
                    {
                        data: 'service_charge',
                        name: 'service_charge',
                        orderable: false
                    },
                    {
                        data: 'amount_payable',
                        name: 'amount_payable',
                        orderable: false
                    },
                    {
                        data: 'date_of_submission',
                        name: 'date_of_submission',
                        orderable: false
                    },
                ]
            });

        }
        const fundTransfer = async (form) => {
            event.preventDefault();
            let url = form.action;
            let data = getForm(form);

            const res = await $.post(`${url}`, data)
                .catch((err) => {
                    if (err.status === 422) {
                        $('.invalid-feedback').remove();
                        formvalidationError(form, err)
                    } else if (err.status === 403) {
                        notifyError(err.responseJSON.message);
                    }
                })
            if (typeof(res) != "undefined") {
                notifySuccess(res.message)
                form.reset()
                $('#select2-canvas-transfer-from').empty();
                $('#select2-canvas-transfer-to').empty();
                $('#ewallet-fund-transfer').offcanvas('hide')
            }
        }
        const fundCredit = async (form) => {
            event.preventDefault();
            let url = form.action;
            let data = getForm(form);
            const res = await $.post(`${url}`, data)
                .catch((err) => {
                    if (err.status === 422) {
                        $('.invalid-feedback').remove();
                        formvalidationError(form, err)
                    } else if (err.status === 403) {
                        notifyError(err.message);
                    }
                })
            if (typeof(res) != "undefined") {
                notifySuccess(res.message)
                form.reset()
                $('#select2-canvas-credit').empty();
                $('#ewallet-fund-credit').offcanvas('hide')
            }
        }
        const fundDebit = async (form) => {
            event.preventDefault();
            let url = form.action;
            let data = getForm(form);
            const res = await $.post(`${url}`, data)
                .catch((err) => {
                    if (err.status === 422) {
                        $('.invalid-feedback').remove();
                        formvalidationError(form, err)
                    } else if (err.status === 403) {
                        console.log(err);
                        notifyError(err.responseJSON.message);
                    }
                })
            if (typeof(res) != "undefined") {
                notifySuccess(res.message)
                form.reset()
                $('#select2-canvas-debit').empty();
                $('#ewallet-fund-debit').offcanvas('hide')
            }
        }

        $('#wallet-transaction').daterangepicker({
            startDate,
            endDate,
            ranges: {
                'All Time': [startDate, endDate],
                'Last 30 days': [moment().subtract(29, 'days'), moment()],
                'Last 90 days': [moment().subtract(89, 'days'), moment()],
                'Last Year': [moment().subtract(1, 'year').add(1, 'day'), moment()],
            },
            locale: {
                format: 'YYYY-MM-DD'
            }
        }, (start, end, label) => {
            startDate = start;
            endDate = end
        });
        $('#user-earnings-date-picker').daterangepicker({
            startDate,
            endDate,
            ranges: {
                'All Time': [startDate, endDate],
                'Last 30 days': [moment().subtract(29, 'days'), moment()],
                'Last 90 days': [moment().subtract(89, 'days'), moment()],
                'Last Year': [moment().subtract(1, 'year').add(1, 'day'), moment()],
            },
        }, (start, end, label) => {
            startDate = start;
            endDate = end
        });

        const showBalance = async () => {
            var userId = $('#select2-canvas-transfer-from').val();
            event.preventDefault()
            let url = "{{ route('show.ewallet-balance', 'id:') }}"
            url = url.replace('id:', userId)
            const res = await $.get(`${url}`)
                .catch((err) => {
                    console.table(err)
                }).then((result) => {
                    $('#ewallet_balance').val(result.data);
                })

        }

        const resetSearch = (tab) => {
            event.preventDefault();
            $("[data-user='" + tab + "']").val(null).trigger('change');
            if (tab == 'transactions') {
                $("[data-status='" + tab + "']").val(null).trigger('change');
                $("[data-category='" + tab + "']").val(null).trigger('change');
                let url = $("*[data-route='" + tab + "']");
                //change the selected date range of that picker
                startDate = moment().subtract(1, 'year').add(1, 'day');
                endDate   = moment().add(1, 'day');
                $('#wallet-transaction').data('daterangepicker').setStartDate(`${startDate.format('YYYY-MM-DD')}`);
                $('#wallet-transaction').data('daterangepicker').setEndDate(`${endDate.format('YYYY-MM-DD')}`);
                getEWalletTransaction(url[0]);
            } else if(tab == 'balance') {
                let url = $("*[data-route='" + tab + "']");
                getEwalletBalance(url[0]);
            } else if(tab == 'purchaseWallet') {
                let url = $("*[data-route='" + tab + "']");
                getPurchaseWallet(url[0]);
            } else if(tab == 'statement') {
                let url = $("*[data-route='" + tab + "']");
                getEwalletStatement(url[0]);
            } else if(tab == 'earnings') {
                $("[data-category='" + tab + "']").val(null).trigger('change');
                let url = $("*[data-route='" + tab + "']");
                //change the selected date range of that picker
                startDate = moment().subtract(1, 'year').add(1, 'day');
                endDate   = moment().add(1, 'day');
                $('#user-earnings-date-picker').data('daterangepicker').setStartDate(`${startDate.format('MM/DD/YYYY')}`);
                $('#user-earnings-date-picker').data('daterangepicker').setEndDate(`${endDate.format('MM/DD/YYYY')}`);
                getUserEarnings(url[0]);
            }
        }
    </script>
@endpush
