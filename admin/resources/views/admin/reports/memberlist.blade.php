@extends('layouts.app')
@section('title', __('profile.member_list'))

@section('content')
    <div class="row">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">{{ __('profile.member_list') }}</h4>
        </div>

        <div class="page_top_cnt_boxs_view1">

            <div class="col-sm-12">
                <div class="card">
                    <div class="row no-gutters align-items-center page_top_cnt_boxs_view_box_2">
                        {{-- <div class="card_img_bx bg-success rounded card-img">
                            <span class="">
                                <i class="bx bxs-user-plus font-size-30"></i>
                            </span>
                        </div> --}}
                        <div class="card-body">
                            <span class="card-text" id="pendingBalance">{{ ($status == 'active') ? __('profile.total_joinings'):__('profile.total_blocked') }}</span>
                            <h5 class="card-title">{{ $count }}</h5>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="card">
                    <div class="row no-gutters align-items-center page_top_cnt_boxs_view_box_2">
                        {{-- <div class="card_img_bx bg-primary rounded card-img">
                            <span class="">
                                <i class="bx bxs-user-detail font-size-30"></i>
                            </span>
                        </div> --}}
                        <div class="card-body">
                            <label class="card-text" id="pendingBalance">{{ __('profile.today_joinings') }}</label>
                            <h5 class="card-title">{{ $today_count }}</h5>
                        </div>
                    </div>
                </div>
            </div>

        </div>



    </div>
    <div class="card business-card">
        <div class="card-body">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link @if (!$tab) active @endif" data-bs-toggle="tab"
                        href="#home" role="tab">
                        <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                        <span class="d-none d-sm-block">{{ __('profile.member_list') }}</span>
                    </button>
                </li>
                {{-- @if (!$moduleStatus->ecom_status && $moduleStatus->package_upgrade)
                    <li class="nav-item">
                        <button class="nav-link @if ($tab == 'pending-upgrade') active @endif" data-bs-toggle="tab"
                            href="#profile" role="tab" onclick="viewPendingPackage()">
                            <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
                            <span class="d-none d-sm-block">{{ __('profile.pending_upgrades') }}</span>
                        </button>
                    </li>
                @endif
                @if (!$moduleStatus->ecom_status && $moduleStatus->subscription_status)
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" href="#renewal" role="tab"
                            onclick="viewPendingRenewal()">
                            <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
                            <span class="d-none d-sm-block">{{ __('profile.pending_renewal') }}</span>
                        </button>
                    </li>
                @endif --}}


            </ul>

            <!-- Tab panes -->
            <div class="tab-content text-muted">
                <div class="tab-pane @if (!$tab) active @endif" id="home" role="tabpanel">

                    <div class="filter_box_new">

                        <form>
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="">{{ __('common.username') }}</label>
                                    <select name="username" class="usernames select2-search-user form-select">
                                        @isset($username)
                                            <option value="{{ $username->id }}" selected>{{ $username->username }}
                                            </option>
                                        @endisset
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="">{{ __('common.status') }}</label>
                                    <select class="form-select" name="status" id="">
                                        <option value="active"
                                            {{ request()->input('status') == 'active' ? 'selected' : '' }}>
                                            {{ __('common.active') }}</option>
                                        <option value="blocked"
                                            {{ request()->input('status') == 'blocked' ? 'selected' : '' }}>
                                            {{ __('common.blocked') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-3 d-flex" style="margin-top:24px">
                                    <button class="btn btn-primary w-md" type="submit">{{ __('common.submit') }}</button>
                                    <a href="{{ route('memberlist.view') }}" class="btn btn-danger w-md ms-2 md-2"
                                        type="button">{{ __('common.reset') }}</a>
                                </div>
                            </div>

                        </form>

                    </div>



                    <div class="row">

                        <form action="{{ route('memberlist.userupdate') }}" method='post'>
                            @csrf
                            <div class="table-responsive">
                                <table class="table m-b-none" id="memberlist_table">
                                    <thead>
                                        <tr class="th">
                                            <th>
                                                <input type="checkbox" id="checkAll" name="checkAll"
                                                    class="form-check-input">
                                            </th>
                                            <th>{{ __('common.memberName') }}</th>
                                            <th>{{ __('common.sponsor') }}</th>
                                            <th>{{ __('common.email') }}</th>
                                            <th>{{ __('common.phone') }}</th>
                                            <th>{{ __('common.joining_date') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($memberList as $user)
                                            <tr>
                                                <td><input type="checkbox" name="user[]"
                                                        class="form-check-input checked-box" value="{{ $user->id }}">
                                                </td>
                                                </td>
                                                <td>{{ $user->userDetails->name . ' ' . $user->userDetails->second_name ?? 'NA' }}
                                                    <a
                                                        href="{{ route('profile.view', 'username=' . $user->id) }}">{{ '(' . $user->username . ')' }}</a>

                                                </td>
                                                <td>{{ $user->sponsor->username ?? 'NA' }}</td>
                                                <td>{{ $user->email ?? 'NA' }}</td>
                                                <td>{{ $user->userDetails->mobile ?? 'NA' }}</td>
                                                <td>{{ $user->date_of_joining }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="100%">
                                                    <div class="nodata_view">
                                                        <img src="{{ asset('assets/images/nodata-icon.png') }}"
                                                            alt="">
                                                        <span class="text-secondary">{{ __('common.no_data') }}</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                <span
                                    class="pagination_new d-print-none">{{ $memberList->appends(request()->query())->links() }}</span>
                                @if (count($memberList))
                                    {{-- <!-- <div class="">
                                        <button type="submit"
                                            class="btn {{ request()->input('status') == 'blocked' ? 'btn-primary' : 'btn-danger' }}">
                                            {{ request()->input('status') == 'blocked' ? __('common.activate') : __('common.block') }}

                                        </button>
                                    </div> --> --}}
                                    <div class="row">
                                        <div class="popup-btn-area col-8 d-none" id="memberlist_action_popup">
                                            <div class="row">
                                                <div class="text-white col">
                                                    <span id="active_items_selected_span"></span>
                                                    <!-- <div id="active_items_selected_div_new"></div> -->
                                                </div>
                                                <div class="col">
                                                    <button type="submit"
                                                        class="btn float-end {{ request()->input('status') == 'blocked' ? 'btn-primary' : 'btn-danger' }}">
                                                        {{ request()->input('status') == 'blocked' ? __('common.activate') : __('common.block') }}
                                                    </button>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>


                <div class="tab-pane @if ($tab == 'pending-upgrade') active @endif" id="profile" role="tabpanel">
                    <div class="filter_box_new">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="">{{ __('common.username') }}</label>
                                <select name="username" class="usernames select2-search-user form-select"
                                    id="pending-username">
                                </select>
                            </div>

                            <div class="col-md-3 d-flex" style="margin-top:24px">
                                <button class="btn btn-primary w-md" type="button"
                                    onclick="viewPendingPackage()">{{ __('common.submit') }}</button>
                                <button class="btn btn-danger w-md ms-2 md-2" type="button"
                                    onclick="resetPendingTable()">{{ __('common.reset') }}</button>
                            </div>
                        </div>
                    </div>

                    <div class="card1">
                        <div class="card-body-1">
                            <table id="datatable-pending-upgrades"
                                class="table table-bordered dt-responsive nowrap w-100">
                                <thead>
                                    <th>
                                        #
                                    </th>
                                    <th>{{ __('common.name') }}</th>
                                    <th>{{ __('common.sponsor') }}</th>
                                    <th>{{ __('common.package') }}</th>
                                    <th>{{ __('common.totalAmount') }}</th>
                                    <th>{{ __('common.payment_method') }}</th>
                                    <th>{{ __('common.action') }}</th>
                                </thead>
                                <tbody>

                                </tbody>
                                <div class="row">
                                    <div class="popup-btn-area col-8 d-none" id="upgradepending_action_popup">
                                        <div class="row">
                                            <div class="text-white col">
                                                <span id="upgrade_items_selected_span"></span>
                                                <!-- <div id="active_items_selected_div_new"></div> -->
                                            </div>
                                            <div class="col">
                                                <button type="submit"
                                                    class="btn float-end {{ request()->input('status') == 'blocked' ? 'btn-primary' : 'btn-danger' }}">
                                                    {{ request()->input('status') == 'blocked' ? __('common.activate') : __('common.block') }}
                                                </button>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="renewal" role="tabpanel">
                    <div class="filter_box_new"></div>
                    <div class="card1">
                        <div class="card-body-1">
                            <table id="datatable-pending-renewal" class="table table-bordered dt-responsive nowrap w-100">
                                <thead>
                                    <th>#</th>
                                    <th>{{ __('profile.invoice_id') }}</th>
                                    <th>{{ __('common.username') }}</th>
                                    <th>{{ __('common.package') }}</th>
                                    <th>{{ __('common.amount') }}</th>
                                    <th>{{ __('common.action') }}</th>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

            <div class="offcanvas offcanvas-end" tabindex="-1" id="receipt" aria-labelledby="offcanvasRightLabel">
                <div class="offcanvas-header">
                    <h5 id="offcanvasRightLabel">{{ __('profile.receipt') }}</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <div>
                        <img src="#" width="250px" alt="404 not found" class="img-fluid" id="pending-reciept">
                    </div>

                </div>
            </div>

            @if (isset($packagevalidityextendhistory) && $packagevalidityextendhistory != '')
                @foreach ($packagevalidityextendhistory as $item)
                    <div class="offcanvas offcanvas-end" tabindex="-1" id="renewalReceipt{{ $item->id }}"
                        aria-labelledby="offcanvasRightLabel">
                        <div class="offcanvas-header">
                            <h5 id="offcanvasRightLabel">{{ __('profile.receipt') }}</h5>
                            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                                aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body">
                            <div>
                                <img src="{{ asset($item->bankReciept?->receipt) }}" alt="404 not found"
                                    class="img-fluid">
                            </div>

                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
    </div>
@endsection

@push('scripts')
    <script>
        var tab = `{{ $tab }}`;
        $('#checkAll').on('click', function(e) {
            $('input:checkbox').not(this).prop('checked', this.checked);
            showActiveActionPopup();
            e.stopImmediatePropagation();
        })
        $('#checkAll1').on('click', function(e) {
            $('input:checkbox.checked-box1').not(this).prop('checked', this.checked);
            showUpgradeActionPopup();
            e.stopImmediatePropagation();
        })
        $(() => {
            // viewPendingPackage();
            // viewPendingRenewal();
            if (tab && tab == 'pending-upgrade') {
                viewPendingPackage();
            }
            getUsers();
            $('input:checkbox').prop('checked', false);
        });

        const viewPendingRenewal = async () => {
            try {
                var table = $('#datatable-pending-renewal').DataTable({
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
                        url: "{{ route('package.renewal.pending') }}",
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            searchable: false,
                            orderable: false,
                        },
                        {
                            data: 'invoice_id',
                            name: 'invoice_id',
                            searchable: true
                        },
                        {
                            data: 'username',
                            name: 'username',
                            searchable: true
                        },
                        {
                            data: 'package',
                            name: 'package',
                            searchable: true
                        },
                        {
                            data: 'total_amount',
                            name: 'total_amount',
                            searchable: true
                        },
                        {
                            data: 'approve',
                            name: 'approve',
                            searchable: true
                        },


                    ]
                });
            } catch (error) {
                console.log(error);
            }

        }

        const viewPendingPackage = async () => {
            try {
                $('input:checkbox').prop('checked', false);
                showUpgradeActionPopup()
                var table = $('#datatable-pending-upgrades').DataTable({
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
                        url: "{{ route('package.upgrade.pending') }}",
                        data: {
                            username: $('#pending-username').val(),
                        }

                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            searchable: false,
                            orderable: false,
                        },
                        {
                            data: 'name',
                            name: 'name',
                        },
                        {
                            data: 'sponsor',
                            name: 'sponsor',
                        },
                        {
                            data: 'package',
                            name: 'package',
                        },
                        {
                            data: 'payment_amount',
                            name: 'payment_amount',
                        },
                        {
                            data: 'payment_method',
                            name: 'payment_method',
                        },
                        {
                            data: 'approve',
                            name: 'approve',
                        },

                    ]

                });
            } catch (error) {
                console.log(error);
            }

        }
        const approvePackage = async (id) => {
            event.preventDefault()
            let url = "{{ route('package.upgrade.approve', 'id:') }}"
            url = url.replace('id:', id)
            let userId = $('#userId').val()
            const res = await $.post(`${url}`)
                .catch((err) => {
                    if (err.status === 422) notifyError(err.responseJSON.message)
                }).then((result) => {
                    notifySuccess(result.message)
                    $('#datatable-pending-upgrades').DataTable().draw();
                })
        }

        const approveRenewal = async (id) => {
            event.preventDefault()
            let url = "{{ route('package.renewal.approve', 'id:') }}"
            url = url.replace('id:', id)
            let userId = $('#userId').val()
            const res = await $.post(`${url}`)
                .catch((err) => {
                    if (err.status === 422) notifyError(err.responseJSON.message)
                }).then((result) => {
                    notifySuccess(result.message)
                    $('#datatable-pending-renewal').DataTable().draw();
                })
        }

        function showActiveActionPopup() {
            if ($(".checked-box:checked").length > 0) { // any one is checked
                let items_selected = $(".checked-box:checked").length;
                $('#active_items_selected_span').text(`${items_selected} items selected`);
                $('#memberlist_action_popup').removeClass('d-none');
            } else { // none is checked
                $('.checked-box').prop('checked', false);
                $('#memberlist_table #checkAll').prop('checked', false);
                $('#memberlist_action_popup').addClass('d-none');
            }
        }

        function showUpgradeActionPopup() {
            if ($(".checked-box1:checked").length > 0) { // any one is checked
                let items_selected = $(".checked-box1:checked").length;
                $('#upgrade_items_selected_span').text(`${items_selected} items selected`);
                $('#upgradepending_action_popup').removeClass('d-none');
            } else { // none is checked
                $('.checked-box1').prop('checked', false);
                $('#datatable-pending-upgrades #checkAll1').prop('checked', false);
                $('#upgradepending_action_popup').addClass('d-none');
            }
        }

        $('.checked-box1').on('click', function() {
            showUpgradeActionPopup()
        })

        $('.checked-box').on('click', function() {
            showActiveActionPopup()
        })

        const resetPendingTable = () => {
            try {
                $('#pending-username').empty();
                viewPendingPackage();
            } catch (error) {
                console.log(error);
            }
        }

        const viewPendingBankReciept = (image) => {
            try {
                $('#receipt').offcanvas('show')
                $('#pending-reciept').attr('src', '');
                $('#pending-reciept').attr('src', image);
                console.log(image);
            } catch (error) {
                console.log(error);
            }
        }
    </script>
@endpush
