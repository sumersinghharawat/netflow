<div class="row">
    <div class="col-xl-12">
        <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteName() == 'profile' ? 'active' : '' }}"
                    href="{{ route('profile') }}" id="profile">
                    <span class=""><i class="far fa-user"></i></span>
                    <span class="">{{ __('settings.profile') }}</span>
                </a>
            </li>
            @if ($moduleStatus->multi_currency_status)
                <li class="nav-item">
                    <a class="nav-link {{ Route::currentRouteName() == 'currency' ? 'active' : '' }}"
                        href="{{ route('currency') }}" role="tab" id="currency">
                        <span class=""><i class="far fa-money-bill-alt"></i></span>
                        <span class="">{{ __('settings.currency') }}</span>
                    </a>
                </li>
            @endif
            @if ($moduleStatus->multilang_status)
                <li class="nav-item">
                    <a class="nav-link {{ Route::currentRouteName() == 'language' ? 'active' : '' }}"
                        href="{{ route('language') }}" role="tab" id="language">
                        <span class=""><i class="fas fa-globe"></i></span>
                        <span class="">{{ __('settings.language') }}</span>
                    </a>
                </li>
            @endif
            @if ($moduleStatus->pin_status)
                <li class="nav-item ">
                    <a class="nav-link {{ Route::currentRouteName() == 'pinconfig.index' ? 'active' : '' }}"
                        href="{{ route('pinconfig.index') }}" role="tab" id="pinconfig">
                        <span class=""><i class="fas fa-project-diagram"></i></span>
                        <span class="">{{ __('settings.epin') }}</span>
                    </a>
                </li>
            @endif
            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteName() == 'signupField' ? 'active' : '' }}"
                    href="{{ route('signupField') }}" role="tab" id="signupField">
                    <span class=""><i class="fas fa-cog"></i></span>
                    <span class="">{{ __('settings.custom_field') }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteName() == 'tree.view' ? 'active' : '' }}"
                    href="{{ route('tree.view') }}" role="tab" id="tree">
                    <span class=""><i class="fas fa-network-wired"></i></span>
                    <span class="">{{ __('settings.tree') }}</span>
                </a>
            </li>
        </ul>
    </div>
</div>
