<header id="page-topbar">
    <div class="navbar-header">
        <div class="d-flex">
            <!-- LOGO -->
            <div class="navbar-brand-box position-relative top_head_logo_2">
                <a href="{{ route('dashboard') }}" class="logo logo-dark">
                    <span class="logo-sm">
                        @if (isFileExists($companyProfile->logo_shrink) && $companyProfile->logo_shrink != null)
                            <img src="{{ $companyProfile->logo_shrink }}" alt="">
                        @else
                            <img src="{{ asset('assets/images/shrink-logo.png') }}" alt="">
                        @endif
                    </span>
                    <span class="logo-lg">
                        @if (isFileExists($companyProfile->logo) && $companyProfile->logo != null)
                            <img src="{{ $companyProfile->logo }}" alt="">
                        @else
                            <img src="{{ asset('assets/images/logo-dark.png') }}" alt="">
                        @endif
                    </span>
                </a>

                <a href="{{ route('dashboard') }}" class="logo logo-light">
                    <span class="logo-sm">
                        @if (isFileExists($companyProfile->logo_shrink) && $companyProfile->logo_shrink != null)
                            <img src="{{ $companyProfile->logo_shrink }}" alt="">
                        @else
                            <img src="{{ asset('assets/images/shrink-logo.png') }}" alt="">
                        @endif
                    </span>
                    <span class="logo-lg">
                        @if ($companyProfile->theme == 'dark')
                            @if (isFileExists($companyProfile->logo_dark) && $companyProfile->logo_dark != null)
                                <img src="{{ $companyProfile->logo_dark }}" alt="">
                            @else
                                <img src="{{ asset('assets/images/logo-light.png') }}" alt="">
                            @endif
                        @else
                            @if (isFileExists($companyProfile->logo) && $companyProfile->logo != null)
                                <img src="{{ $companyProfile->logo }}" alt="">
                            @else
                                <img src="{{ asset('assets/images/logo-light.png') }}" alt="">
                            @endif
                        @endif
                    </span>
                </a>

                <svg class="shape_leftmenubar" width="125" height="122" viewBox="0 0 125 122" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M0.5 121.5V0.999213L119.793 0.999421C121.52 0.962822 123.256 0.962586 125 0.99943L119.793 0.999421C54.7882 2.37684 1.96118 55.2601 0.5 121.5Z"
                        fill="#2A3042" />
                </svg>
            </div>
            <button type="button" class="btn btn-sm font-size-20 header-item waves-effect toggle_menu_button"
                id="vertical-menu-btn">
                <i class="fa fa-fw fa-bars"></i>
            </button>
        </div>

        <div class="d-flex right_icons_sc">

        @if (!session()->get('is_preset') && config('mlm.demo_status') == 'yes')
            <a href="https://user.infinitemlmsoftware.com/login/{{ auth()->user()->username }}" target="_blank" class="user_dashboard_floating_btn" target="_new">
                <i class="user_dashboard_Icon bx bx-user"></i>
                <span >User Dashboard</span>
            </a>
        @endif
            @if ($moduleStatus->multi_currency_status)
                @php
                    $currn = $currencies->where('default', 1)->first() ? $currencies->where('default', 1)->first() : $currencies->first();
                    $prefix = config('database.connections.mysql.prefix');
                @endphp
                <div class="dropdown d-inline-block currency_blc">
                    <button type="button" class="btn header-item waves-effect" data-bs-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        @if (cache()->has($prefix . 'userCurrency'))
                            <span>{{ Cache::get($prefix . 'userCurrency')->symbol_left }}</span>
                        @else
                            <span>{{ $currn->symbol_left }}</span>
                        @endif
                        <i class="mdi mdi-chevron-down"></i>

                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        @forelse($currencies as $currency)
                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item notify-item language"
                                onclick="changeCurrency('{{ $currency->id }}')">
                                <span>{{ $currency->symbol_left }}</span> <span>{{ $currency->title }}</span>
                            </a>
                        @empty
                        @endforelse
                    </div>
                </div>
            @endif
            @if ($moduleStatus->multilang_status)
                @php
                    $lang = $languages->where('default', 1)->first();

                    $userLocale = Auth::user()->default_lang;
                    if ($userLocale) {
                        $lang = $languages->where('id', $userLocale)->first();
                    }

                @endphp
                <div class="dropdown d-inline-block">
                    <button type="button" class="btn header-item waves-effect" data-bs-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <img id="header-lang-img" src="{{ asset('assets/images/flags/' . $lang->flag_image) }}"
                            alt="Header Language" height="16">
                        <i class="mdi mdi-chevron-down"></i>

                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        @forelse($languages as $language)
                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item notify-item language"
                                onclick="confirmLanguage('{{ $language->id }}')" data-lang="{{ $language->code }}">
                                <img src="{{ asset('assets/images/flags/' . $language->flag_image) }}" alt="user-image"
                                    class="me-1" height="12"> <span
                                    class="align-middle">{{ $language->name_in_english }}</span>
                            </a>
                        @empty
                        @endforelse
                    </div>
                </div>
            @endif
            <div class="dropdown d-none d-lg-inline-block ms-1">
                <button type="button" class="btn header-item noti-icon waves-effect" data-bs-toggle="fullscreen">
                    <i class="bx bx-fullscreen"></i>
                </button>
            </div>
            <div class="dropdown d-none d-lg-inline-block ms-1">
                <button type="button" class="btn header-item noti-icon waves-effect"
                    id="page-header-notifications-dropdown" data-bs-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false">
                    <i class="bx bx-bell @if ($items['notificationCount']) bx-tada @endif"></i>
                    <span class="badge bg-danger rounded-pill">
                        @if ($items['notificationCount'] > 99)
                            99+
                        @else
                            {{ $items['notificationCount'] }}
                        @endif
                    </span>
                </button>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                    aria-labelledby="page-header-notifications-dropdown">
                    <div class="p-3">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="m-0" key="t-notifications"> {{ __('notification.notifications') }} </h6>
                            </div>
                            {{-- <div class="col-auto">
                                <a href="#!" class="small" key="t-view-all"> View All</a>
                            </div> --}}
                        </div>
                    </div>
                    <div id="notification-container">
                        @forelse($items['notifications'] as $notification)
                            @php
                                $notify = $notification->data;
                            @endphp
                            <a href="javascript: void(0);" onclick="readNotification('{{ $notification->id }}')"
                                class="text-reset notification-item">
                                <div class="d-flex">
                                    <div class="avatar-xs me-3">
                                        <span class="avatar-title bg-primary rounded-circle font-size-16">
                                            {!! $notify['icon'] !!}
                                        </span>
                                    </div>

                                    <div class="flex-grow-1">
                                        <h6 class="mb-1" key="t-your-order">
                                            {{ __('notification.' . $notify['title']) }}</h6>
                                        <div class="font-size-12 text-muted">
                                            <p class="mb-1" key="t-grammer">{{ $notify['username'] }}
                                                {{ __('notification.send') }}
                                                {{ __('notification.' . $notify['type']) }}</p>
                                            <p class="mb-0"><i class="mdi mdi-clock-outline"></i> <span
                                                    key="t-min-ago">{{ $notification->created_at->diffForHumans() }}
                                                </span></p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <!-- <p>{{ __('common.no_data') }}</p> -->
                            <div class="nodata_view">
                                <img src="{{ asset('assets/images/nodata-icon.png') }}" alt=""
                                    width="25%">
                                <span class="text-secondary fs-6"
                                    style="padding-top:20%;">{{ __('common.no_data') }}</span>
                            </div>
                        @endforelse

                    </div>
                    <div class="p-2 border-top d-grid">
                        <a class="btn btn-sm btn-link font-size-14 text-center" href="javascript:void(0)"
                            onclick="markAsRead()">
                            <i class="mdi mdi-check-circle me-1"></i> <span
                                key="t-view-more">{{ __('notification.mark_all_read') }}</span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="dropdown d-inline-block">

                <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    @if (auth()->user()->user_type == 'employee')
                        @if (auth()->user()->employeeDetail->image == null || !isFileExists(auth()->user()->employeeDetail->image))
                            <img class="rounded-circle header-profile-user"
                                src="{{ asset('assets/images/users/avatar-1.jpg') }}" alt="Header Avatar">
                        @else
                            <img class="rounded-circle header-profile-user"
                                src="{{ auth()->user()->employeeDetail->image }}" alt="Header Avatar">
                        @endif
                    @else
                        @if (auth()->user()->userDetail->image == null || !isFileExists(auth()->user()->userDetail->image))
                            <img class="rounded-circle header-profile-user"
                                src="{{ asset('assets/images/users/avatar-1.jpg') }}" alt="Header Avatar">
                        @else
                            <img class="rounded-circle header-profile-user"
                                src="{{ auth()->user()->userDetail->image }}" alt="Header Avatar">
                        @endif
                    @endif
                    <span class="d-none d-xl-inline-block ms-1"
                        key="t-henry">{{ Str::ucfirst(auth()->user()->username) }}</span>
                    <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <!-- item-->
                    <a class="dropdown-item" href="{{ route('profile.view') }}"><i class="bx bx-user font-size-16 align-middle me-1"></i>
                        <span key="t-profile">Profile</span></a>
                    @if(config('mlm.demo_status') == 'yes')
                        <a class="dropdown-item" target="_blank" href="https://user.infinitemlmsoftware.com"><i
                            class="bx bx-user font-size-16 align-middle me-1"></i>
                            <span key="t-profile">User Login</span></a>
                    @endif
                    {{-- <a class="dropdown-item" href="#"><i class="bx bx-user font-size-16 align-middle me-1"></i>
                        <span key="t-profile">Signup</span></a> --}}
                    <form
                        @if (Auth::user()->user_type == 'employee') action="{{ route('employee.logout') }}" @else action="{{ route('logout') }}" @endif
                        method="post">
                        <div class="dropdown-divider"></div>
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="bx bx-power-off font-size-16 align-middle me-1 text-danger"></i>
                            <span>Logout</span></button>
                    </form>

                </div>
            </div>

            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item noti-icon right-bar-toggle waves-effect">
                    <i class="bx @if ($companyProfile->theme == 'dark') bx-sun @else bx-moon @endif"></i>
                </button>
            </div>

        </div>
    </div>
</header>
