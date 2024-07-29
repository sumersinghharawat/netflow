<footer class="footer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <script>
                    document.write(new Date().getFullYear())
                </script>
            </div>
        </div>
    </div>
</footer>
</div>
<!-- end main content-->

</div>
<!-- END layout-wrapper -->

<!-- Right Sidebar -->
<div class="right-bar">
    <div data-simplebar class="h-100">
        <div class="rightbar-title d-flex align-items-center px-3 py-4">

            <h5 class="m-0 me-2">{{ __('common.settings') }}</h5>

            <a href="javascript:void(0);" class="right-bar-toggle ms-auto">
                <i class="mdi mdi-close noti-icon"></i>
            </a>
        </div>

        <!-- Settings -->
        <hr class="mt-0" />
        <h6 class="text-center mb-0">{{ __('common.choose_layout') }}</h6>

        <div class="p-4">
            <div class="mb-2">
                <img src="{{ asset('assets/images/layouts/layout-1.jpg') }}" class="img-thumbnail" alt="layout images">
            </div>

            <div class="form-check form-switch mb-3">
                <input class="form-check-input theme-choice" type="checkbox" id="light-mode-switch" checked>
                <label class="form-check-label" for="light-mode-switch">Light Mode</label>
            </div>

            <div class="mb-2">
                <img src="{{ asset('assets/images/layouts/layout-2.jpg') }}" class="img-thumbnail" alt="layout images">
            </div>
            <div class="form-check form-switch mb-3">
                <input class="form-check-input theme-choice" type="checkbox" id="dark-mode-switch">
                <label class="form-check-label" for="dark-mode-switch">Dark Mode</label>
            </div>


        </div>

    </div> <!-- end slimscroll-menu-->
</div>
<!-- /Right-bar -->

<!-- Right bar overlay-->
<div class="rightbar-overlay"></div>

@if (auth()->user()->user_type != 'employee')
<div class="sticky-menu-container d-print-none">
    <div class="inner-menu closed">
        <ul class="menu-list">
            <li class="menu-item">
                <span class="item-icon">
                    <i class='bx bxs-offer'></i>
                </span>
                <a href="{{ route('commission') }}"><span class="item-text">{{ __('quickMenu.commission') }}</span></a>
            </li>
            <li class="menu-item">
                <span class="item-icon">
                    <i class='bx bx-calculator'></i>
                </span>
                <a href="{{ route('compensation') }}"><span class="item-text">{{ __('quickMenu.compensation') }}</span></a>
            </li>
            {{--<li class="menu-item">
                <span class="item-icon">
                    <i class="bx bx-task"></i>
                </span>
                <a href="{{ route('manage.commission') }}"><span class="item-text">{{ __('quickMenu.commission-status') }}</span></a>
            </li> --}}
            @if ($moduleStatus['rank_status'])
            <li class="menu-item">
                <span class="item-icon">
                    <i class='bx bx-trophy'></i>
                </span>
                <a href="{{ route('rank') }}"><span class="item-text">{{ __('quickMenu.rank') }}</span>
            </li></a>
            @endif
            @if ($moduleStatus['mlm_plan'] != 'Binary' && $moduleStatus->mlm_plan != 'Monoline')
            <li class="menu-item">
                <span class="item-icon">
                    <i class='bx bxs-wrench '></i>
                </span>
                <a href="{{ route('settings.plan') }}"><span class="item-text">{{ str_replace('_', ' ', $moduleStatus['mlm_plan']) }}</span>
            </li></a>
            @endif

            <li class="menu-item">
                <span class="item-icon">
                    <i class='bx bx-id-card'></i>
                </span>
                <a href="{{ route('payout') }}"><span class="item-text">{{ __('quickMenu.payout') }}</span></a>
            </li>
            <li class="menu-item">
                <span class="item-icon">
                    <i class='bx bx-credit-card'></i>
                </span>
                <a href="{{ route('payment.view') }}"><span class="item-text">{{ __('quickMenu.payment') }}</span></a>
            </li>
            <li class="menu-item">
                <span class="item-icon">
                    <i class='bx bx-user-plus'></i>
                </span>
                <a href="{{ route('signup') }}"><span class="item-text">{{ __('quickMenu.signup') }}</span></a>
            </li>
            @if ($moduleStatus->subscription_status)
            <li class="menu-item">
                <span class="item-icon">
                    <i class='bx bx-sync'></i>
                </span>
                <a href="{{ route('subscription') }}"><span class="item-text">{{ __('quickMenu.subscription') }}</span></a>
            </li>
            @endif

            <li class="menu-item">
                <span class="item-icon">
                    <i class='bx bx-envelope'></i>
                </span>
                <a href="{{ route('mail') }}"><span class="item-text">{{ __('quickMenu.mail') }}</span></a>
            </li>
            <li class="menu-item">
                <span class="item-icon">
                    <i class='bx bx-key'></i>
                </span>
                <a href="{{ route('apiKey') }}"><span class="item-text">{{ __('quickMenu.api-Key') }}</span></a>
            </li>
        </ul>
    </div>
    <div class="outer-button">
        <div class="icon-container">
            <i class="bx bx-cog"></i>
        </div>
    </div>
</div>
@endif

{{-- server alerts --}}
<div id="server-alert-parent-div" style="display:none;">

</div>
{{-- server alerts end --}}

<!-- JAVASCRIPT -->
<script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/libs/metismenu/metisMenu.min.js') }}"></script>
<script src="{{ asset('assets/js/app.js') }}"></script>
<script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
<script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/common.js') }}"></script>

<!-- Plugins js -->
<script src="{{ asset('assets/libs/dropzone/min/dropzone.min.js') }}"></script>


<script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>


<script src="{{ asset('assets/libs/toastr/build/toastr.min.js') }}"></script>
<script src="{{ asset('assets/libs/table-edits/build/table-edits.min.js') }}"></script>

<!-- Auto logout -->
<script src="{{ asset('assets/js/pages/bootstrap-timeout.init.js') }}"></script>
<script src="{{ asset('assets/js/pages/session-timeout.init.js') }}"></script>

<!-- Sweet Alerts js -->
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>

<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script src="{{ asset('assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/ecommerce-cart.init.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script src="https://unpkg.com/@panzoom/panzoom@4.5.1/dist/panzoom.min.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
<script src="https://unpkg.com/filepond/dist/filepond.min.js"></script>
<script src="https://unpkg.com/jquery-filepond/filepond.jquery.js"></script>
<script src="https://unpkg.com/filepond-plugin-file-encode/dist/filepond-plugin-file-encode.min.js"></script>
<script src="https://unpkg.com/filepond-plugin-file-poster/dist/filepond-plugin-file-poster.js"></script>

@stack('scripts')

@stack('wizardScripts')

</body>

</html>
