@extends('layouts.app')
@section('title', 'Business')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __('business.business') }}</h4>
            </div>
        </div>
    </div>

    <div class="page_top_cnt_boxs_view1">

        <div class="col-sm-12">
            <div class="card">

                <div class="row no-gutters align-items-center page_top_cnt_boxs_view_box_2">
                    {{-- <div class="card_img_bx">
                        <img class="card-img bg-success rounded img-fluid"
                            src="{{ asset('assets/images/ewallet/income-w.png') }}" alt="Card image">
                    </div> --}}
                    <div class="card-body">
                        <label class="card-text">{{ __('common.income') }}</label>
                        <h5 class="card-title">{{ $currency }}
                            {{ formatNumberShort(formatCurrency($totalOverview['income'])) }}
                            <div class="tooltip-index card_tooltip">
                                <i class='bx bx-info-circle tooltip--button dashboard_top_box_tooltip_ico'></i>
                                <div class="tooltip--content" id="description-one" role="tooltip">
                                    <p>{{ __('business.all_productamount_including_reg_fee_payoutfee_trans_fee_service_charge') }}
                                    </p>
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
                    {{-- <div class="card_img_bx">
                        <img class="card-img bg-success rounded bg-danger rounded"
                            src="{{ asset('assets/images/ewallet/Bonus-w.png') }}" alt="Card image">
                    </div> --}}
                    <div class="card-body">
                        <label class="card-text">{{ __('business.bonus') }}</label>
                        <h5 class="card-title">{{ $currency }}
                            {{ formatNumberShort(formatCurrency($totalOverview['bonus'])) }}
                            <div class="tooltip-index card_tooltip">
                                <i class='bx bx-info-circle tooltip--button dashboard_top_box_tooltip_ico'></i>
                                <div class="tooltip--content" id="description-one" role="tooltip">
                                    <p>{{ __('business.all_earned_commissions') }}</p>
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
                    {{-- <div class="card_img_bx">
                        <img class="card-img bg-info rounded img-fluid"
                            src="{{ asset('assets/images/ewallet/E-Wallet-w.png') }}" alt="Card image">
                    </div> --}}
                    <div class="card-body">
                        <label class="card-text">{{ __('business.paid') }}</label>
                        <h5 class="card-title">{{ $currency }}
                            {{ formatNumberShort(formatCurrency($totalOverview['paid'])) }}
                            <div class="tooltip-index card_tooltip">
                                <i class='bx bx-info-circle tooltip--button dashboard_top_box_tooltip_ico'></i>
                                <div class="tooltip--content" id="description-one" role="tooltip">
                                    <p>{{ __('business.paid_payouts') }}</p>
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
                    {{-- <div class="card_img_bx bg-success rounded card-img">
                        <span class="">
                            <i class="bx bxs-time font-size-26"></i>
                        </span>
                    </div> --}}
                    <div class="card-body">
                        <label class="card-text">{{ __('business.pending') }}</label>
                        <h5 class="card-title">{{ $currency }}
                            {{ formatNumberShort(formatCurrency($totalOverview['pending'])) }}
                            <div class="tooltip-index card_tooltip">
                                <i class='bx bx-info-circle tooltip--button dashboard_top_box_tooltip_ico'></i>
                                <div class="tooltip--content" id="description-one" role="tooltip">
                                    <p>{{ __('business.pending_payout') }}</p>
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

                    {{-- <div class="card_img_bx businss_profit rounded card-img">
                        <span class="">
                            <i class="bx bx-money font-size-26"></i>
                        </span>
                    </div> --}}
                    <div class="card-body">
                        <label class="card-text">{{ __('business.profit') }}</label>
                        <h5 class="card-title">{{ $currency }}
                            {{ formatNumberShort(formatCurrency($totalOverview['income'] - $totalOverview['paid'])) }}
                            <div class="tooltip-index card_tooltip">
                                <i class='bx bx-info-circle tooltip--button dashboard_top_box_tooltip_ico'></i>
                                <div class="tooltip--content" id="description-one" role="tooltip">
                                    <p>{{ __('business.total_income_profit') }}</p>
                                </div>
                            </div>
                        </h5>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-sm-12">
            <div class="card business-card">
                <div class="card-body">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
                        <li class="nav-item ">
                            <button class="nav-link active" data-bs-toggle="tab" href="#business-summary-tab" role="tab"
                                id="ewallet-summary">
                                <span class="d-none d-sm-block">
                                    {{ __('business.business_summary') }}
                                </span>
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" href="{{ route('business.transaction') }}"
                                onclick="getBusinessTransaction(this)" role="tab" id="businesstransaction-tab">
                                <span class="d-none d-sm-block">{{ __('business.business_transaction') }}</span>
                            </button>
                        </li>

                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content text-muted">

                        <div class="tab-pane active" id="business-summary-tab" role="tabpanel">
                            <div class="filter_box_new">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="">{{ __('common.date') }}</label>
                                        <input type="text" class="date-range-picker form-control"
                                            id="business-summary" />

                                    </div>
                                </div>
                            </div>
                            <div id="businessDateReport">

                            </div>
                        </div>

                        <div class="tab-pane" role="tabpanel" id="business-transaction-tab">
                            <div class="filter_box_new">
                                <div class="row ">
                                    <div class="col-md-2">
                                        <label>{{ __('common.username') }}</label>
                                        <select class="form-control select2-ajax select2-search-user select2-multiple"
                                            multiple="multiple" id="transaction-user" name="username[]"></select>
                                    </div>
                                    <div class="col-md-2">
                                        <label>{{ __('common.type') }}</label>
                                        <select class="form-select select2 select2-multiple" multiple="multiple"
                                            name="type" id="type">
                                            <option value="income">{{ __('business.income') }}</option>
                                            <option value="bonus">{{ __('business.bonus') }}</option>
                                            <option value="paid">{{ __('business.paid') }}</option>
                                            <option value="pending">{{ __('business.pending') }}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label>{{ __('common.category') }}</label>
                                        <select class="select2 select2-multiple form-select" multiple="multiple"
                                            name="category" id="category">
                                            @foreach ($businessCategories as $category)
                                                @if ($category != null)
                                                    <option value="{{ $category }}">{{ __('business.' . $category) }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label>{{ __('common.dateRange') }}</label>
                                        <input type="text" class="date-range-picker form-control"
                                            id="business-transaction" />
                                    </div>
                                    <div class="col-md-2" style="margin-top: 23px;">
                                        <a href="{{ route('business.transaction') }}"
                                            onclick="getBusinessTransaction(this)"
                                            class="btn btn-primary">{{ __('common.view') }}</a>
                                        <a href="{{ route('reports.business') }}" type="submit" onclick="resetSearch(this)"
                                            class="btn btn-primary">{{ __('common.reset') }}</a>
                                    </div>
                                </div>
                            </div>
                            <div class="TransDateReport" id="TransDateReport">
                                <div class="table-responsive referal_mem_table">
                                    <table class="table mb-0 " id="transactionTable">

                                        <thead class="table-light">
                                            <tr>
                                                <th>{{ __('common.memberName') }}</th>
                                                <th>{{ __('common.category') }}</th>
                                                <th>{{ __('common.amount') }}</th>
                                                <th>{{ __('common.transactionDate') }}</th>
                                            </tr>
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
    @endsection


    @push('scripts')
        <script>
            let startDate = moment().subtract(1, 'year').add(1, 'day');
            let endDate = moment().add(1, 'day');
            let label = 'All Time';

            $(() => {
                $('#business-summary').daterangepicker({
                    startDate,
                    endDate,
                    ranges: {
                        'All Time': [startDate, endDate],
                        'Last 30 days': [moment().subtract(29, 'days'), moment()],
                        'Last 90 days': [moment().subtract(89, 'days'), moment()],
                        'Last Year': [moment().subtract(1, 'year').add(1, 'day'), moment()],
                    },
                }, callback);

                getBusinessSummary();
            });

            const callback = async (start, end, label) => {
                data = {
                    fromDate: start.format('YYYY-MM-DD'),
                    toDate: end.format('YYYY-MM-DD'),
                    label: label
                }
                const res = await $.get("{{ route('business.getSumary') }}", data)

                $('#businessDateReport').html(res.data)
            }
            $('#business-transaction').daterangepicker({
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

            const getBusinessSummary = async () => {
                let params = {
                    fromDate: startDate.format('YYYY-MM-DD'),
                    toDate: endDate.format('YYYY-MM-DD'),
                    label: label
                }

                const res = await $.get(`{{ route('business.getSumary') }}`, params);
                if (typeof(res) != 'undefined') {
                    $('#businessDateReport').html(res.data);
                }
            }

            const getBusinessTransaction = async (href) => {
                event.preventDefault()
                $('.tab-pane').removeClass('active')
                $('#business-transaction-tab').addClass('active')
                getUsers();
                let url = "{{ route('business.transaction') }}";
                // let url = href.href;
                let params = {
                    fromDate: startDate.format('YYYY-MM-DD'),
                    toDate: endDate.format('YYYY-MM-DD'),
                    users: $('#transaction-user').val(),
                    category: $('#category').val(),
                    type: $('#type').val()
                }
                $('#transactionTable').DataTable({
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
                        url: url,
                        type: 'GET',
                        data: params,
                    },
                    columns: [{
                            data: 'full_name',
                        },
                        {
                            data: 'amount_type',
                        },
                        {
                            data: 'amount',
                        },
                        {
                            data: 'date',
                        },
                    ]
                });
            }
            const resetSearch = (el) => {
                event.preventDefault();
                let url = `{{ route('business.transaction') }}`;
                //change the selected date range of that picker
                startDate = moment().subtract(1, 'year').add(1, 'day');
                endDate   = moment().add(1, 'day');
                $('#transaction-user').val(null).trigger('change');
                $('#category').val(null).trigger('change');
                $('#type').val(null).trigger('change');
                getBusinessTransaction(url);

            }

        </script>
    @endpush
