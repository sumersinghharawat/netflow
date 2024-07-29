@include('layouts.inc.head')

<body data-sidebar="dark">
    <!-- Begin page -->
    <div id="layout-wrapper">

        @include('layouts.inc.header')

        <!-- ========== Left Sidebar Start ========== -->
        @include('layouts.inc.support-navigation');
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    @include('layouts.inc.footer')
