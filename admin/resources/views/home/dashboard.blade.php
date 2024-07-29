@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
    <div class="row">
        <div class="col-12">
            <!-- {{ __('dashboard.admin_panel') }}</h4> -->
            <div class="dashboard_main_contant_area">
                <div class="top_page_head">
                    {{ Str::upper(__('dashboard.dashboard')) }}
                </div>

                <div class="row">
                    <div class="col-xxl-12 col-lg-12 col-md-12">

                        <div class="dashboard_top_quick_boxes dashboard_new_bx">


                            @if ((auth()->user()->user_type == 'employee' && in_array('ewallet-balance', $dashboardPermission)) ||
                                auth()->user()->user_type == 'admin')
                                    <!-- <h4 class="card-title mb-4">{{ Str::upper(__('dashboard.quick_balance')) }}</h4> -->
                                <div class="card mini-stats-wid">


                                    <div class="card-body">
                                        <div class="">
                                            <div class="flex-shrink-0 align-self-center">
                                                <!-- <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                                    <span class="avatar-title">
                                                        <img src="{{ asset('assets/images/icons/epin-icon.png') }}" alt="">
                                                    </span>
                                                </div> -->
                                            </div>
                                            <div class="flex-grow-1">
                                                <span class="text-muted ">{{ __('dashboard.e_wallet_balance') }}
                                                    <div class="tooltip card_tooltip">

                                                            <i class='bx bx-info-circle tooltip--button dashboard_top_box_tooltip_ico'></i>
                                                        <div class="tooltip--content" id="description-one" role="tooltip">
                                                            <p>{{ __('dashboard.ewalletbalance_of_all_users') }}</p>
                                                        </div>
                                                    </div>
                                                </span>
                                                <h4 class="mb-0">{{ $currency }}
                                                    {{ formatNumberShort(formatCurrency($ewalletBalance), 2) }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if ((auth()->user()->user_type == 'employee' && in_array('total-income', $dashboardPermission)) ||
                                auth()->user()->user_type == 'admin')
                                <div class="card mini-stats-wid">

                                    <div class="card-body">
                                        <div class="">
                                            <div class="flex-shrink-0 align-self-center ">
                                                <!-- <div class="avatar-sm rounded-circle bg-primary mini-stat-icon">
                                                    <span class="avatar-title rounded-circle bg-primary">
                                                        <img src="{{ asset('assets/images/icons/income-icon.png') }}"
                                                            alt="">
                                                    </span>
                                                </div> -->
                                            </div>
                                            <div class="flex-grow-1">
                                                <span class="text-muted ">{{ __('dashboard.income') }}
                                                    <div class="tooltip card_tooltip">

                                                            <i class='bx bx-info-circle tooltip--button dashboard_top_box_tooltip_ico'></i>
                                                        <div class="tooltip--content" id="description-one" role="tooltip">
                                                            <p>{{ __('dashboard.all_product_amounts_including_service_charge_tax') }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </span>
                                                <h4 class="mb-0">{{ $currency }}
                                                    {{ formatNumberShort(formatCurrency($TotalIncome), 2) }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if ((auth()->user()->user_type == 'employee' && in_array('bonus', $dashboardPermission)) ||
                                auth()->user()->user_type == 'admin')
                                <div class="card mini-stats-wid">

                                    <div class="card-body">
                                        <div class="">
                                            <div class="flex-shrink-0 align-self-center">
                                                <!-- <div class="avatar-sm rounded-circle bg-primary mini-stat-icon">
                                                    <span class="avatar-title rounded-circle bg-primary">
                                                        <img src="{{ asset('assets/images/icons/bonus-icon.png') }}"
                                                            alt="">
                                                    </span>
                                                </div> -->
                                            </div>
                                            <div class="flex-grow-1">
                                                <span class="text-muted ">{{ __('dashboard.bonus') }}
                                                    <div class="tooltip card_tooltip">

                                                            <i class='bx bx-info-circle tooltip--button dashboard_top_box_tooltip_ico'></i>
                                                        <div class="tooltip--content" id="description-one" role="tooltip">
                                                            <p>{{ __('dashboard.commissions_earned_by_all_users') }}</p>
                                                        </div>
                                                    </div>
                                                </span>
                                                <h4 class="mb-0">{{ $currency }}
                                                    {{ formatNumberShort(formatCurrency($bussinessBonus), 2) }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if ((auth()->user()->user_type == 'employee' && in_array('payout-paid', $dashboardPermission)) ||
                                auth()->user()->user_type == 'admin')
                                <div class="card mini-stats-wid">

                                    <div class="card-body">
                                        <div class="">
                                            <div class="flex-shrink-0 align-self-center">
                                                <!-- <div class="avatar-sm rounded-circle bg-primary mini-stat-icon">
                                                    <span class="avatar-title rounded-circle bg-primary">
                                                        <img src="{{ asset('assets/images/icons/paid-icon.png') }}" alt="">
                                                    </span>
                                                </div> -->
                                            </div>
                                            <div class="flex-grow-1">
                                                <span class="text-muted ">{{ __('dashboard.paid') }}
                                                    <div class="tooltip card_tooltip">

                                                            <i class='bx bx-info-circle tooltip--button dashboard_top_box_tooltip_ico'></i>
                                                        <div class="tooltip--content" id="description-one" role="tooltip">
                                                            <p>{{ __('dashboard.payout_paid') }}</p>
                                                        </div>
                                                    </div>
                                                </span>
                                                <h4 class="mb-0">{{ $currency }}
                                                    {{ formatNumberShort(formatCurrency($bussinessPaid), 2) }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if ((auth()->user()->user_type == 'employee' && in_array('payout-pending', $dashboardPermission)) ||
                                auth()->user()->user_type == 'admin')
                                <div class="card mini-stats-wid">

                                    <div class="card-body">
                                        <div class="">
                                            <div class="flex-shrink-0 align-self-center">
                                                <!-- <div class="avatar-sm rounded-circle bg-primary mini-stat-icon">
                                                    <span class="avatar-title rounded-circle bg-primary">
                                                        <img src="{{ asset('assets/images/icons/pending-icon.png') }}"
                                                            alt="">
                                                    </span>
                                                </div> -->
                                            </div>
                                            <div class="flex-grow-1">
                                                <span class="text-muted ">{{ __('dashboard.pending_amount') }}
                                                    <div class="tooltip card_tooltip">

                                                            <i class='bx bx-info-circle tooltip--button dashboard_top_box_tooltip_ico'></i>
                                                        <div class="tooltip--content" id="description-one" role="tooltip">
                                                            <p>{{ __('dashboard.pending_payouts') }}</p>
                                                        </div>
                                                    </div>
                                                </span>
                                                <h4 class="mb-0">{{ $currency }}
                                                    {{ formatNumberShort(formatCurrency($bussinessPending), 2) }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                      <!-- add joining section -->
                    </div>
                    @if ((auth()->user()->user_type == 'employee' && in_array('joinings', $dashboardPermission)) ||
                        auth()->user()->user_type == 'admin')
                        <div class="@if(!$moduleStatus['replicated_site_status'] && !$moduleStatus['lead_capture_status']) col-xl-6 @else col-xl-6 @endif ">
                            <div class="card crd_mn_hgt">
                                <div class="card-body">
                                    <h4 class="card-title mb-4">{{ __('dashboard.joinings') }}</h4>
                                    <div class="dropdown filter_btn_home">
                                        <button class="btn  dropdown-toggle" type="button" id="dropdownMenuButton1"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class='bx bx-dots-vertical-rounded'></i>
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                            <li><a class="dropdown-item" href="javascript:void(0)"
                                                    onclick="loadJoiningChart('month')">{{ __('common.month') }}</a></li>
                                            <li><a class="dropdown-item" href="javascript:void(0)"
                                                    onclick="loadJoiningChart('day')">{{ __('common.day') }}</a></li>
                                            <li><a class="dropdown-item" onclick="loadJoiningChart('year')"
                                                    href="javascript:void(0)">{{ __('common.year') }}</a></li>
                                        </ul>
                                    </div>
                                    <div  class="chart-container">
                                        <canvas class="joinings" id="joinings_linechart" height="280"></canvas>
                                    </div>
                                </div>
                            </div>
                            <!--end card-->
                        </div>
                    @endif

                     @if (auth()->user()->user_type != 'employee')
                      
                     
                    @if ((auth()->user()->user_type == 'employee' && in_array('chart-income-commission', $dashboardPermission)) ||
                        auth()->user()->user_type == 'admin')
                        <div class="col-xl-6">
                            <div class="card crd_mn_hgt">
                                <div class="card-body">
                                    <h4 class="card-title mb-4">{{ __('dashboard.income_vs_commission') }}</h4>
                                    <div class="dropdown filter_btn_home">
                                        <button class="btn  dropdown-toggle" type="button" id="dropdownMenuButton1"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class='bx bx-dots-vertical-rounded'></i>
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                            <li><a class="dropdown-item" href="javascript:void(0)"
                                                    onclick="incomeCart('month')">{{ __('common.month') }}</a></li>
                                            <li><a class="dropdown-item" onclick="incomeCart('year')"
                                                    href="javascript:void(0)">{{ __('common.year') }}</a></li>
                                        </ul>
                                    </div>
                                    <div class="chart-container">
                                    <canvas height="280" id="incomebarchart"></canvas>
                                </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if ((auth()->user()->user_type == 'employee' && in_array('income-and-commission', $dashboardPermission)) ||
                        auth()->user()->user_type == 'admin')
                        <div class="col-xxl-5 col-xl-4">      
                            <div class="card crd_mn_hgt">
                                <div class="card-body">

                                    <h4 class="card-title">{{ __('dashboard.income_commission') }}</h4>

                                    <!-- Nav tabs -->
                                    <ul class="nav nav-tabs nav-tabs-custom nav-justified nav-cus-tab" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active " data-bs-toggle="tab" href="#income"
                                                role="tab">
                                                <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                                                <span class="d-none d-sm-block">{{ __('dashboard.income') }}</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#commission" role="tab">
                                                <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
                                                <span class="d-none d-sm-block">{{ __('dashboard.commission') }}</span>
                                            </a>
                                        </li>

                                    </ul>

                                    <!-- Tab panes -->
                                    <div class="tab-content p-3 text-muted" id="income-commission">

                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    <!-- Transactions section start -->
                <div class="col-xxl-4 col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12">
                    <div class="card crd_mn_hgt transaction-sec">
                    <div class="card-body">
                <h4 class="card-title mb-4">TRANSACTIONS</h4>
                <div class="trans-item-sec">
                    <div class="row trans-item">
                        <div class="col-xl-12">
                            <div class="nodata_view {{ count($transactions) > 0 ? 'd-none' : 'd-block' }} ">
                                <img src="{{ asset('assets/images/nodata-icon.png') }}" alt="">
                                <span class="text-secondary fs-5">{{ __('common.no_data') }}</span>
                            </div>
                            @foreach($transactions as $transaction)
                            <div class="row trans-item">
                                    <div class="col-xl-2 col-lg-2 col-md-2 col-sm-2 col-2">
                                    <div class="trans-img">
                                        @switch(($transaction->amount_type)?$transaction->amount_type:$transaction->ewallet_type)
                                            @case('admin_debit')
                                            @case('admin_credit')
                                            @case('registration')
                                            @case('package_validity')
                                            @case('repurchase')
                                            @case('upgrade')
                                                <img src="assets\images\ewallet-transaction2.png" alt="">
                                                @break

                                            @case('pin_purchase')
                                            @case('pin_purchase_delete')
                                            @case('pin_purchase_refund')
                                                <img src="assets\images\epin-transaction1.png" alt="">
                                                @break

                                            @case('payout')
                                            @case('payout_delete')
                                            @case('payout_request')
                                            @case('payout_release')
                                            @case('payout_release_manual')
                                                <img src="assets\images\payout-transaction3.png" alt="">
                                                @break

                                            @case('admin_user_debit')
                                            @case('admin_user_credit')
                                                <img src="assets\images\efundtrfr-transaction4.png" alt="">
                                                @break

                                            @default
                                                <img src="assets\images\ewallet-transaction2.png" alt="">
                                        @endswitch
                                    </div>

                                    </div>
                                    <div class="col-xl-10 col-lg-10 col-md-10 col-sm-10 col-10">
                                        <div class="row">
                                            <div class="col-xl-8 col-lg-8 col-md-8 col-sm-8 col-8">
                                            <div class="trans-id">
                                                <p>{{__("dashboard.$transaction->ewallet_type")?__("dashboard.$transaction->amount_type"):null }}</p>
                                                <span>{{ str_replace('_', ' ', $transaction->amount_type) }}</span>
                                            </div>
                                            </div>
                                            <div class="col-xl-4 col-lg-4 col-lg-4 col-md-4 col-sm-4 col-4 count">
                                            <div class="trans-count">
                                                <span>{{ $currency }}
                                                    {{ formatNumberShort(formatCurrency($transaction->amount), 2) }}</span>
                                            </div>
                                            </div>
                                        </div>
                                    </div>        
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                </div>
                    </div>
                </div>
                <!-- --- -->
                    <div class="col-xxl-3 col-xl-4 col-lg-6 col-md-6 col-sm-12">
                        <!-- end col -->
                        @if ((auth()->user()->user_type == 'employee' && in_array('chart-payout', $dashboardPermission)) ||
                        auth()->user()->user_type == 'admin')
                            <div class="card crd_mn_hgt">
                                <div class="card-body">
                                <h4 class="card-title mb-4">{{ __('dashboard.payout_overview') }}</h4> 
                                    <div class="row text-center">
                                        <!-- <div class="nodata_view"> -->
                                        <div class="nodata_view {{ $payoutTotalRequest > 0 ? 'd-none' : 'd-block' }} ">
                                            <img src="{{ asset('assets/images/nodata-icon.png') }}" alt="">
                                            <span class="text-secondary fs-5">{{ __('common.no_data') }}</span>
                                        </div>
                                        <div
                                            class="col-xxl-12 col-lg-12 {{ $payoutTotalRequest > 0 ? 'd-block' : 'd-none' }}">
                                            <div class="doughnut_inner_val">
                                                <strong>
                                                    {{ $payoutPerc }} %
                                                </strong>{{ __('dashboard.paid') }}
                                            </div>
                                            <canvas class="doghnut_chart_1" id="doughnut" height="150"></canvas>
                                        </div>
                                        <div
                                            class="col-xxl-12 col-lg-12 d-flex align-items-center {{ $payoutTotalRequest ? 'd-block' : 'd-none' }}">
                                            <div class="payout_ovrview_lgnd">
                                            <div class="row ">

                                                <div class="col-xxl-4 col-md-4 col-4 text-right">
                                                <p class="text-muted text-truncate"><span
                                                            style="background-color:#33338E" class="pyotu_clr_bx1"></span>
                                                        {{ __('dashboard.paid') }}</p>
                                                    <h5 class="mb-0">
                                                        {{ $currency . ' ' . formatNumberShort(formatCurrency($doughnutDataViewArray[0]), 2) }}
                                                    </h5>
                                                 
                                                </div>
                                                <div class="col-xxl-4 col-md-4 col-4 text-center">
                                                <p class="text-muted text-truncate"><span
                                                            style="background-color:#8D79F6" class="pyotu_clr_bx1"></span>
                                                        {{ __('dashboard.approved') }}
                                                    </p>
                                                    <h5 class="mb-0">
                                                        {{ $currency . ' ' . formatNumberShort(formatCurrency($doughnutDataViewArray[1]), 2) }}
                                                    </h5>
                                                   
                                                </div>
                                                <div class="col-xxl-4 col-md-4 col-4 text-left">
                                                <p class="text-muted text-truncate"><span
                                                            style="background-color:#D9D9D9" class="pyotu_clr_bx1"></span>
                                                        {{ __('dashboard.pending') }}</p>
                                                    <h5 class="mb-0">
                                                        {{ $currency . ' ' . formatNumberShort(formatCurrency($doughnutDataViewArray[2]), 2) }}
                                                    </h5>
                                                   
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    </div>

                                </div>
                            </div>
                        @endif
                        <!-- end col -->
                    </div>

                </div>
                <!--top row end-->
                <div class="row">
                    @if ((auth()->user()->user_type == 'employee' && in_array('team-performance', $dashboardPermission)) ||
                    auth()->user()->user_type == 'admin')
                    <div class="col-xl-8">
                        <div class="card crd_mn_hgt new_members_view_bx
                        ">
                            <div class="card-body">
                                <h4 class="card-title">{{ __('dashboard.team_performance') }}</h4>
                                <!-- Nav tabs -->
                                <div class="row">
                                    <div class="col-xxl-3 col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                    <ul class="nav nav-tabs nav-tabs-custom nav-justified nav-perfomance" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-bs-toggle="tab" href="#earners"
                                            role="tab">
                                            <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                                            <span class="d-none d-sm-block">{{ __('dashboard.top_earners') }}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#recruiters" role="tab"
                                            onclick="getTopRecruiters()">
                                            <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
                                            <span
                                                class="d-none d-sm-block">{{ __('dashboard.top_recruiters') }}</span>
                                        </a>
                                    </li>
                                    @if ($moduleStatus['product_status'] || $moduleStatus->ecom_status)
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#package" role="tab"
                                                onclick="getPackages()">
                                                <span class="d-block d-sm-none"><i class="far fa-envelope"></i></span>
                                                <span
                                                    class="d-none d-sm-block">{{ __('dashboard.package_overview') }}</span>
                                            </a>
                                        </li>
                                    @endif
                                    @if ($moduleStatus['rank_status'])
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#rank" role="tab"
                                                onclick="getRanks()">
                                                <span class="d-block d-sm-none"><i class="fas fa-cog"></i></span>
                                                <span
                                                    class="d-none d-sm-block">{{ __('dashboard.rank_overview') }}</span>
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                                    </div>
                                    <div class="col-xxl-9 col-xl-8 col-lg-8 col-md-8 col-sm-12">
                                    <div class="tab-content p-3 text-muted">
                                    <div class="tab-pane active" id="earners" role="tabpanel" id="top-earners">
                                        <div class="loader_tab_inn"><img
                                                src="{{ asset('assets/images/icons/loader.gif') }}" alt="">
                                        </div>

                                    </div>
                                    <div class="tab-pane" id="recruiters" role="tabpanel">
                                        <div class="loader_tab_inn"><img
                                                src="{{ asset('assets/images/icons/loader.gif') }}" alt="">
                                        </div>
                                    </div>
                                    @if ($moduleStatus['product_status'] || $moduleStatus->ecom_status)
                                        <div class="tab-pane" id="package" role="tabpanel">
                                        </div>
                                    @endif
                                    @if ($moduleStatus['rank_status'])
                                        <div class="tab-pane" id="rank" role="tabpanel">
                                        </div>
                                    @endif
                                </div>
                                    </div>
                                </div>
                              
                                <!-- Tab panes -->
                                
                            </div>
                        </div>
                    </div>
                @endif
                @if ((auth()->user()->user_type == 'employee' && in_array('new-members-panel', $dashboardPermission)) ||
                        auth()->user()->user_type == 'admin')
                        <div class="col-xl-4">
                            <div class="card crd_mn_hgt">
                                <div class="card-body">
                                    <h4 class="card-title">{{ __('dashboard.new_members') }}</h4>
                                    <div class="col-lg-12 padding-15 dashboard_btm_scrl member-list">
                                        @forelse($newUsers as $newUser)
                                            <div class="row team_perfomance_row">
                                                <div class="col-xl-2 col-lg-1 col-xs-2 col-md-1 col-sm-1 col-2 padding-zero ">
                                                    <span class="thumb-sm avatar ">
                                                        @if ($newUser->userDetail?->image == null || !isFileExists($newUser->userDetail->image))
                                                            <img src="{{ asset('/assets/images/users/avatar-1.jpg') }}">
                                                        @else
                                                            <img class="rounded avatar-sm" style="border-radius: 9px !important" src="{{ $newUser->userDetail->image }}">
                                                        @endif

                                                        <i class="on b-white bottom"></i>
                                                    </span>
                                                </div>
                                                <div class="col-xl-10 col-lg-11 col-md-11 col-xs-10 col-sm-11 col-10 padding-zero new-member-list">
                                                    <div class="row">
                                                        <div class="col-xxl-6 col-xl-12 col-lg-6 col-md-6 col-sm-12 col-12 pull-left">
                                                            <div class="member-full-name">{{ $newUser->userDetail?->name }}
                                                            </div>
                                                            <!-- <span class="member-user-name">{{ $newUser->username }}</span> -->
                                                        </div>
                                                        <div class="col-xxl-6 col-xl-12 col-lg-6 col-md-6 col-sm-12 col-12 text-center padding-zero">
                                                            <div class="member-package">
                                                                 <span class="member-user-name">{{ $newUser->username }}</span>
                                                                <!-- {{ $currency }}
                                                                {{ formatCurrency($newUser->package->price ?? 0) }}
                                                                <small class="text-msuted clear text-ellipsis"
                                                                    style="font-weight: 300;">{{ Carbon\Carbon::parse($newUser->date_of_joining)->format('M d, Y, g:ia') }}</small> -->
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <!-- <p>{{ __('common.no_data') }}</p> -->
                                            <div class="nodata_view">
                                                <img src="{{ asset('assets/images/nodata-icon.png') }}" alt="">
                                                <span class="text-secondary fs-5">{{ __('common.no_data') }}</span>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
            </div>
       

        @endif

    <div class="row">
        @if ($moduleStatus['replicated_site_status'] || $moduleStatus['lead_capture_status'])
        <div class="col-xl-12">
            <div class="card replica_box">

                <div class="row">
                    <!-- replica link row start-->
                    @if ($moduleStatus['replicated_site_status'])
                    @if (config('mlm.demo_status') == 'yes')
                        <p class="bx bx-error-circle"> {{ __("ticket.note_add_on_module") }} </p>
                    @endif
                        <div class="col-xl-6">
                            <div class="card-body">
                                <h4 class="card-title mb-4">{{ __('dashboard.promotion_tools') }}</h4>
                                <div class="rep-head">
                                    <h5>{{ __('dashboard.replica_link') }}</h5>
                                    <button type="button" class="rpl-social">
                                        <i class='bx bxl-facebook'></i>
                                        </i>
                                    </button>
                                    <button type="button" class="rpl-social">
                                        <i class='bx bxl-twitter'></i>
                                    </button>
                                    <a type="button" class="rpl-social">
                                        <i class='bx bxl-linkedin'></i>
                                    </a>
                                </div>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="replica_url"
                                        value="{{ $replicaurl }}">
                                    <button class="btn btn-primary" type="button"
                                        onclick="copyClipBoard('replica_url')"
                                        title="{{ __('common.click_copy_to_clipboard') }}">{{ __('dashboard.copy') }}</button>
                                </div>

                            </div>
                        </div>
                        <!--end card-->
                    @endif
                    <!--end col-->
                    @if ($moduleStatus['lead_capture_status'])
                        <div class="col-xl-6">
                            <div class="card-body">
                                <h4 class="card-title mb-4">&nbsp;</h4>
                                <div class="rep-head">
                                    <h5>{{ __('dashboard.lead_capture') }}</h5>
                                    <button type="button" class="rpl-social">
                                        <i class='bx bxl-facebook'></i>
                                        </i>
                                    </button>
                                    <button type="button" class="rpl-social">
                                        <i class='bx bxl-twitter'></i>
                                    </button>
                                    <a type="button" class="rpl-social">
                                        <i class='bx bxl-linkedin'></i>
                                    </a>
                                </div>
                                <div class="input-group">
                                    <input type="text" class="form-control" value='{{ $url }}'
                                        id="lead_capture">
                                    <button class="btn btn-primary" type="button"
                                        onclick="copyClipBoard('lead_capture')"
                                        title="{{ __('common.click_copy_to_clipboard') }}">{{ __('dashboard.copy') }}</button>
                                </div>
                            </div>
                        </div>
                        <!--end card-->
                        @endif
                </div>
            </div>
        </div>
@endif 
        <!-- replica link row end-->
       
       
        {{-- <div class="col-xl-12">
    <div class="card crd_mn_hgt transaction-sec">
    <div class="card-body">
    <h4 class="card-title">Promotion Tools</h4>
    <div class="row link-row">
        <div class="col-xl-6">
<div class="link-sec">
    <p>Referral Link</p>
    <div class="rep-icon">
        <div class="img"><a href=""><img src="assets\images\fb-logo.png" alt=""></a></div>
        <div class="img"><a href=""><img src="assets\images\twit-logo.png" alt=""></a></div>
        <div class="img"> <a href=""><img src="assets\images\link-logo.png" alt=""></a></div>
    </div>
</div>
<div class="link-btn">
    <p>https://www.figma.com/file/BKwJdPq5wf3bcOaMaYsy1o/Untitled?type</p>
    <button>Copy</button>
</div>
        </div>
        <div class="col-xl-6">
<div class="link-sec">
    <p>Lead Capture</p>
    <div class="rep-icon">
       <div class="img"><a href=""><img src="assets\images\fb-logo.png" alt=""></a></div>
        <div class="img"><a href=""><img src="assets\images\twit-logo.png" alt=""></a></div>
        <div class="img"><a href=""> <img src="assets\images\link-logo.png" alt=""></a></div>
    </div>
</div>
<div class="link-btn">
    <p>https://www.figma.com/file/BKwJdPq5wf3bcOaMaYsy1o/Untitled?type</p>
    <button>Copy</button>
</div>
        </div>
    </div>
</div>
</div>
    </div> --}}
                <!--teamperfomnce row end-->
                <!--income payout row end-->
                <!-- income commsion row end-->
   
    <!-- premotion-sec -->



    </div>
</div>
   
@endsection

@push('scripts')
    <script src="{{ asset('assets/libs/chart.js/Chart.bundle.min.js') }}"></script>

    <script>
        let incomeCommission;
        let joiningsChart;
        let plan = `{{ $moduleStatus->mlm_plan }}`;

        $(() => {
            getTopEarners();
            getIncomeCommission();
            const data = {
                labels: [],
                datasets: [{
                        label: "Income",
                        barPercentage: 0.5,
                        barThickness: 15,
                        maxBarThickness: 15,
                        minBarLength: 2,
                        data: [],
                        backgroundColor: "#33338E",
                    },
                    {
                        label: "Commission",
                        barPercentage: 0.5,
                        barThickness: 15,
                        maxBarThickness: 15,
                        minBarLength: 2,
                        backgroundColor: "#8D79F6",
                        data: [],
                    },

                ],

            };

            let config = {
                type: 'bar',
                data: data,
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    scales: {
                        xAxes: [{
                            barThickness: 7,
                            maxBarThickness: 7,
                           // stacked: false,
                            gridLines: {
                                display: false,
                            }
                        }],
                        yAxes: [
                            {   
                                gridLines: {
                                    display: false,
                                }
                            }
                        ],
                    }
                }
                // options: {
                //     scales: {
                //         xAxes: [{
                //             gridLines: {
                //                 display: false
                //             }
                //         }],
                //         yAxes: [{
                //             gridLines: {
                //                 display: false
                //             }
                //         }]
                //     }
                // }
            };



          
            incomeCommission = new Chart(
                $('#incomebarchart'),
                config
            );
           
            if (plan == "Binary") {
                datasets = [{
                        label: "Left Joinings",
                        fill: !0,
                        lineTension: 0.5,
                        // backgroundColor: "#B09FFF",
                        backgroundColor: createGradient(),
                        borderColor: "#B09FFF",
                        borderCapStyle: "butt",
                        borderDash: [],
                        borderDashOffset: 0,
                        borderJoinStyle: "miter",
                        pointBorderColor: "#B09FFF",
                        pointBackgroundColor: "#fff",
                        pointBorderWidth: 1,
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: "#B09FFF",
                        pointHoverBorderColor: "#B09FFF",
                        pointHoverBorderWidth: 1,
                        pointRadius: 1,
                        pointHitRadius: 10,
                        data: [],
                       
                    },
                    {
                        label: "Right Joinings",
                        fill: !0,
                        lineTension: 0.5,
                        // backgroundColor: "#2C008A",
                        backgroundColor: createGradient2(),
                        borderColor: "#6342FF",
                        borderCapStyle: "butt",
                        borderDash: [],
                        borderDashOffset: 0,
                        borderJoinStyle: "miter",
                        pointBorderColor: "#6342FF",
                        pointBackgroundColor: "#6342FF",
                        pointBorderWidth: 1,
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: "#6342FF",
                        pointHoverBorderColor: "#6342FF",
                        pointHoverBorderWidth: 2,
                        pointRadius: 1,
                        pointHitRadius: 10,
                        data: [],
                    }
                ];
            } else {
                datasets = [{
                    label: "Joinings",
                    fill: !0,
                    lineTension: 0.5,
                    backgroundColor: "rgba(85, 110, 230, 0.2)",
                    borderColor: "#027ae9",
                    borderCapStyle: "butt",
                    borderDash: [],
                    borderDashOffset: 0,
                    borderJoinStyle: "miter",
                    pointBorderColor: "#027ae9",
                    pointBackgroundColor: "#fff",
                    pointBorderWidth: 1,
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: "#027ae9",
                    pointHoverBorderColor: "#fff",
                    pointHoverBorderWidth: 2,
                    pointRadius: 1,
                    pointHitRadius: 10,
                    data: [],
                }];
            }

            const joinData = {
                labels: [],
                datasets,
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    scales: {
                        xAxes: [{
                            
                            gridLines: {
                                display: false,
                            }
                        }],
                        yAxes: [
                            {
                                gridLines: {
                                    display: false,
                                }
                            }
                        ],
                    }

                }
            };

            let joinConfig = {
                type: 'line',
                data: joinData,
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    scales: {
                        xAxes: [{
                            
                            gridLines: {
                                display: false,
                            }
                        }],
                        yAxes: [
                            {
                                gridLines: {
                                    display: false,
                                }
                            }
                        ],
                    }

                }
            };
            joiningsChart = new Chart(
                $('#joinings_linechart'),
                joinConfig
            );
            incomeCart();
            loadDoughnutChart();
            loadJoiningChart();
            function createGradient() {
                    var ctx = document.getElementById('joinings_linechart').getContext('2d');
                    var gradient = ctx.createLinearGradient(0, 0, 0, 500);
                    gradient.addColorStop(0, 'rgba(232, 227, 255, 1)');
                    gradient.addColorStop(1, 'rgba(252, 252, 255, 0)');
                    return gradient;
                }
                function createGradient2() {
                    var ctx = document.getElementById('joinings_linechart').getContext('2d');
                    var gradient = ctx.createLinearGradient(0, 0, 0, 500);
                    gradient.addColorStop(0, 'rgba(99, 66, 255, 1)');
                    gradient.addColorStop(1, 'rgba(252, 252, 255, 0)');
                    return gradient;
                }
        });


        const incomeCart = async (type = "month") => {
            const res = await $.get(`{{ route('income.bonus.graph') }}`, {
                type
            });
            let labels = res.data.graphLabel;
            incomeCommission.data.labels = labels;
            incomeCommission.data.datasets[0].data = res.data.resultIncome;
            incomeCommission.data.datasets[1].data = res.data.resultBonus;
            // incomeCommission.options.scales.yAxes[0].ticks
            incomeCommission.update();
        }
        const getTopRecruiters = async () => {
            try {
                let url = "{{ route('dashboard.getTopRecruiters') }}";
                const res = await $.get(url);
                $('#recruiters').html('');
                $('#recruiters').html(res.data);
            } catch (error) {
                console.log(error);
            }
        }

        const getPackages = async () => {
            try {
                console.log(1);
                let url = "{{ route('dashboard.PackageProgress') }}";
                const res = await $.get(url);
                $('#package').html('');
                $('#package').html(res.data);
            } catch (error) {
                console.log(error);
            }
        }

        const getRanks = async () => {
            try {
                let url = "{{ route('dashboard.rankData') }}";
                const res = await $.get(url);
                $('#rank').html('');
                $('#rank').html(res.data);
            } catch (error) {
                console.log(error);
            }
        }

        const loadDoughnutChart = () => {
            const data = {
                labels: ['Paid', 'Approved', 'Pending'],
                datasets: [{
                    data: [`{{ $doughnutDataViewArray[0] }}`, `{{ $doughnutDataViewArray[1] }}`,
                        `{{ $doughnutDataViewArray[2] }}`
                    ],
                    backgroundColor: [
                        '#2C008A',
                        '#8D79F6',
                        '#D9D9D9'
                    ],
                    hoverOffset: 4
                }]
            };
            const config = {
                type: 'doughnut',
                data: data,
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    cutoutPercentage: 80,
                    legend: {
                        display: false
                    },
                }
            };

            const myChart = new Chart(
                $('#doughnut'),
                config
            );
        }
        const loadJoiningChart = async (type = "month") => {

            if (plan == "Binary") {
                const leftCount = await $.get(`{{ route('joinings.graph.binary.left') }}`, {
                    type
                });
                const rightCount = await $.get(`{{ route('joinings.graph.binary.right') }}`, {
                    type
                });
                let labels = leftCount.data.graphLabel;
                joiningsChart.data.labels = labels;
                joiningsChart.data.datasets[0].data = leftCount.data.leftJoinCount;
                joiningsChart.data.datasets[1].data = rightCount.data.rightJoinCount;

            } else {
                const res = await $.get(`{{ route('joinings.graph') }}`, {
                    type
                });
                let labels = res.data.graphLabel;
                joiningsChart.data.labels = labels;
                joiningsChart.data.datasets[0].data = res.data.joinArrayCount;
            }
            joiningsChart.update();
        }

        const getTopEarners = async () => {
            let url = `{{ route('top-earners') }}`;
            const res = await $.get(`${url}`);
            if (typeof(res) != 'undefined') {
                $('#earners').html(res.data)
            }
        }

        const getIncomeCommission = async () => {
            let url = `{{ route('income.commission') }}`;
            const res = await $.get(`${url}`);
            if (typeof(res) != 'undefined') {
                $('#income-commission').html(res.data)
            }
        }
    </script>
@endpush
