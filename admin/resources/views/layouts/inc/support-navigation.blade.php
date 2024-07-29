<div class="vertical-menu">

    <div data-simplebar class="h-100">

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                {{-- <li class="menu-title" key="t-menu">Menu</li> --}}
                <li>
                    <a href="{{ route('dashboard') }}" class="waves-effect">
                        <span>{{ __('menu.dashboard') }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('support.index') }}" class="waves-effect">
                        <span>{{ __('menu.ticket_dashboard') }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('support.show') }}" class="waves-effect">
                        <span>{{ __('menu.view_tickets') }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('support.category') }}" class="waves-effect">
                        <span>{{ __('menu.category') }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('support.configure') }}" class="waves-effect">
                        <span>{{ __('menu.configuration') }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('support.open') }}" class="waves-effect">
                        <span>{{ __('menu.open_tickets') }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('support.resolved') }}" class="waves-effect">
                        <span>{{ __('menu.resolved_tickets') }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('support.faq') }}" class="waves-effect">
                        <span>FAQ</span>
                    </a>
                </li>
                <li>
                    <form action="{{ route('logout') }}" method="post">
                        @csrf
                        <div class="text-center">
                            <button type="submit" class="btn-danger w-75 btn p-2 mt-3">{{ __('menu.logout') }}</button>
                        </div>
                    </form>

                </li>
            </ul>
        </div>
    </div>
</div>
