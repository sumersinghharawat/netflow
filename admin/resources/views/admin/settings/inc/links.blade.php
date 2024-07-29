<div class="row">
    <div class="col-xl-12">
        <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteName() == 'commission' ? 'active' : '' }}"
                    href="{{ route('commission') }}" id="commission">
                    <span class=""><i class="fas fa-percent"></i></span>
                    <span class="">{{ __('settings.commission') }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteName() == 'compensation' ? 'active' : '' }}"
                    href="{{ route('compensation') }}" role="tab" id="compensation">
                    <span class=""><i class="fab fa-buffer"></i></span>
                    <span class="">{{ __('settings.compensation') }}</span>
                </a>
            </li>
            @if ($moduleStatus['mlm_plan'] != 'Binary' &&
                $moduleStatus['mlm_plan'] != 'Unilevel' &&
                $moduleStatus['mlm_plan'] != 'Party' &&
                $moduleStatus['mlm_plan'] != 'Xup' &&
                $moduleStatus->mlm_plan != 'Monoline')
                <li class="nav-item">
                    <a class="nav-link {{ Route::currentRouteName() == 'settings.plan' ? 'active' : '' }}"
                        href="{{ route('settings.plan') }}" role="tab" id="compensation">
                        <span class=""><i class="far fa-user"></i></span>
                        <span class="">{{ str_replace('_', ' ', $moduleStatus['mlm_plan']) }}</span>
                    </a>
                </li>
            @endif

            @if ($moduleStatus['rank_status'])
                <li class="nav-item">
                    <a class="nav-link {{ Route::currentRouteName() == 'rank' ? 'active' : '' }}"
                        href="{{ route('rank') }}" role="tab" id="rank">
                        <span class=""><i class="far fa-star"></i></span>
                        <span class="">{{ __('settings.rank') }}</span>
                    </a>
                </li>
            @endif
            <li class="nav-item ">
                <a class="nav-link {{ Route::currentRouteName() == 'payout' ? 'active' : '' }}"
                    href="{{ route('payout') }}" role="tab" id="payout">
                    <span class=""><i class="fas fa-chart-line"></i></span>
                    <span class="">{{ __('settings.payout') }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteName() == 'payment.view' ? 'active' : '' }}"
                    href="{{ route('payment.view') }}" role="tab" id="payment">
                    <span class=""><i class="far fa-money-bill-alt"></i></span>
                    <span class="">{{ __('settings.payment') }}</span>
                </a>
            </li>
            <li class="nav-item ">
                <a class="nav-link {{ Route::currentRouteName() == 'signup' ? 'active' : '' }}"
                    href="{{ route('signup') }}" role="tab" id="signup">
                    <span class=""><i class="far fa-user-circle"></i></span>
                    <span class="">{{ __('settings.signup') }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteName() == 'mail' ? 'active' : '' }} "
                    href="{{ route('mail') }}" role="tab" id="mail">
                    <span class=""><i class="far fa-envelope"></i></span>
                    <span class="">{{ __('settings.mail') }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link  {{ Route::currentRouteName() == 'apiKey' ? 'active' : '' }}"
                    href="{{ route('apiKey') }}" role="tab" id="api">
                    <span class=""><i class="fas fa-key"></i></span>
                    <span class="">API Key</span>
                </a>
            </li>
            @if ($moduleStatus['subscription_status'] || $moduleStatus['subscription_status_demo'])
                <li class="nav-item">
                    <a class="nav-link {{ Route::currentRouteName() == 'subscription' ? 'active' : '' }}"
                        href="{{ route('subscription') }}" role="tab" id="subscription">
                        <span class=""><i class="fas fa-cog"></i></span>
                        <span class="">{{ __('settings.subscription') }}</span>
                    </a>
                </li>
            @endif
        </ul>
    </div>
</div>
