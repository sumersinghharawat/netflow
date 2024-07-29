<div class="vertical-menu">

    <div data-simplebar class="h-100">
         <!-- LOGO -->
         <div class="navbar-brand-box position-relative top_head_logo_1">
            <a href="{{ route('dashboard') }}" class="logo logo-dark">
               @if (isFileExists($companyProfile->logo) && $companyProfile->logo != null)
                    <span class="logo-sm">
                        <img src="{{ $companyProfile->logo }}" alt="">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ $companyProfile->logo }}" alt="">
                    </span>
                @else
                    <span class="logo-sm">
                        <img src="{{ asset('assets/images/logo-dark.png') }}" alt="">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('assets/images/logo-dark.png') }}" alt="">
                    </span>
                @endif
            </a>

            <a href="{{ route('dashboard') }}" class="logo logo-light">
                @if (isFileExists($companyProfile->logo) && $companyProfile->logo != null)
                    <span class="logo-sm">
                        <img src="{{ $companyProfile->logo }}" alt="">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ $companyProfile->logo }}" alt="">
                    </span>
                @else
                    <span class="logo-sm">
                        <img src="{{ asset('assets/images/logo-light.png') }}" alt="">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('assets/images/logo-light.png') }}" alt="">
                    </span>
                @endif
            </a>

            <svg class="shape_leftmenubar" width="125" height="122" viewBox="0 0 125 122" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M0.5 121.5V0.999213L119.793 0.999421C121.52 0.962822 123.256 0.962586 125 0.99943L119.793 0.999421C54.7882 2.37684 1.96118 55.2601 0.5 121.5Z"
                    fill="#2A3042" />
            </svg>


        </div>
        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            @if (auth()->user()->user_type == 'admin')
                <ul class="metismenu list-unstyled" id="side-menu">
                    @forelse($menuitems as $menu)
                        <li>
                            @if ($menu->permission)
                                @if ($menu->is_heading == 1 && $menu->has_children == 0 && $menu->permission->admin_permission)
                                    <a href="{{ $menu->route_name == 'javascript: void(0);' ? 'javascript: void(0);' : route($menu->route_name) }}"
                                        class="waves-effect" @if ($menu->slug == 'register' || $menu->slug == 'store') target="_blank" @endif>
                                        <i class="{{ $menu->admin_icon }}"></i>
                                        <span key="t-layouts">{{ __("menu.{$menu->slug}") }}</span>
                                    </a>
                                @elseif($menu->is_heading == 1 && $menu->permission->admin_permission)
                                    <a href="javascript: void(0);"
                                        class="@if ($menu->has_children == 1) has-arrow @endif waves-effect">
                                        <i class="{{ $menu->admin_icon }}"></i>
                                        <span key="t-layouts">{{ __("menu.{$menu->slug}") }}</span>
                                    </a>
                                @endif
                                <ul class="sub-menu" aria-expanded="true">
                                    @foreach ($menu->children->sortBy('child_order') as $item)
                                        @if ($item->react_only == 0 && $item->permission->admin_permission)
                                            <li><a
                                                    href="{{ route($item->route_name) }}">{{ __("menu.{$item->slug}") }}</a>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @empty
                        <li>
                            <a href="{{ route('dashboard') }}" class="waves-effect">
                                <i class="bx bx-home-circle"></i>
                                <span>{{ __('menu.dashboard') }}</span>
                            </a>
                        </li>
                    @endforelse

                    <li>
                        <form action="{{ route('logout') }}" method="post">
                            @csrf
                            <div class="text-center">
                                <button type="submit"
                                    class="btn-danger w-75 btn p-2 mt-3">{{ __('menu.logout') }}</button>
                            </div>
                        </form>
                    </li>
                </ul>
            @else
                <ul class="metismenu list-unstyled" id="side-menu">
                    @forelse($employeeMenus as $menu)
                        <li>
                            @if ($menu->menuDetails->is_heading == 1 && $menu->menuDetails->has_children == 0)
                                <a href="{{ route($menu->menuDetails->route_name) }}" class="waves-effect">
                                    <i class="{{ $menu->menuDetails->admin_icon }}"></i>
                                    <span key="t-layouts">{{ __("menu.{$menu->menuDetails->slug}") }}</span>
                                </a>
                            @elseif($menu->menuDetails->is_heading == 1)
                                <a href="javascript: void(0);"
                                    class="@if ($menu->menuDetails->has_children == 1) has-arrow @endif waves-effect">
                                    <i class="{{ $menu->menuDetails->admin_icon }}"></i>
                                    <span key="t-layouts">{{ __("menu.{$menu->menuDetails->slug}") }}</span>
                                </a>
                            @endif
                            <ul class="sub-menu" aria-expanded="true">
                                @foreach ($menu->menuDetails->children->sort() as $item)
                                    @if ($employeeMenus->pluck('menu_id')->search($item->id))
                                        <li>
                                            <a href="{{ route($item->route_name) }}">{{ __("menu.{$item->slug}") }}</a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </li>

                    @empty
                        <li>
                            <a href="{{ route('dashboard') }}" class="waves-effect">
                                <i class="bx bx-home-circle"></i>
                                <span>{{ __('menu.dashboard') }}</span>
                            </a>
                        </li>
                    @endforelse
                    <li>
                        <form action="{{ route('employee.logout') }}" method="post">
                            @csrf
                            <div class="text-center">
                                <button type="submit"
                                    class="btn-danger w-75 btn p-2 mt-3">{{ __('menu.logout') }}</button>
                            </div>
                        </form>

                    </li>
                </ul>
            @endif
        </div>
    </div>
</div>
