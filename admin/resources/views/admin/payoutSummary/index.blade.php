@extends('layouts.app')
@section('title', 'Payout')
@section('content')
     <!-- Loader -->
    <div id="preloader" class="d-none">
        <div id="status" class="loader_new_py">
            <div class="spinner-chase">
                <div class="chase-dot"></div>
                <div class="chase-dot"></div>
                <div class="chase-dot"></div>
                <div class="chase-dot"></div>
                <div class="chase-dot"></div>
                <div class="chase-dot"></div>
            </div>
            <div class="loader_new_cnt">{{ __('payout.approve_payment_notification') }}</div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">
                    {{ __('payout.payout') }}
                </h4>
            </div>
        </div>
    </div>

    <div class="page_top_cnt_boxs_view1">

        <div class="col-sm-12">
            <div class="card">
                <div class="row no-gutters align-items-center page_top_cnt_boxs_view_box_2">
                    <!-- <div class="card_img_bx">
                        <img class="card-img bg-primary rounded img-fluid"
                            src="{{ asset('assets/images/ewallet/E-Wallet-w.png') }}" alt="Card image">
                    </div> -->
                    <div class="card-body">
                       
                        <p class="card-text">{{ __('payout.pending') }}</p>
                        <h5 class="card-title" id="pendingBalance">{{ $data['pending'] }}</h5>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-sm-12">
            <div class="card">
                <div class="row no-gutters align-items-center page_top_cnt_boxs_view_box_2">
                    <!-- <div class="card_img_bx">
                        <img class="card-img bg-success rounded img-fluid"
                            src="{{ asset('assets/images/ewallet/income-w.png') }}" alt="Card image">
                    </div> -->
                    <div class="card-body">
                       
                        <p class="card-text">{{ __('payout.approved') }}</p>
                         <h5 class="card-title" id="approvedBalance">{{ $data['approved'] }}</h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-12">
            <div class="card">
                <div class="row no-gutters align-items-center page_top_cnt_boxs_view_box_2">
                    <!-- <div class="card_img_bx">
                        <img class="card-img bg-warning rounded img-fluid"
                            src="{{ asset('assets/images/ewallet/Bonus-w.png') }}" alt="Card image">
                    </div> -->
                    <div class="card-body">
                       
                        <p class="card-text">{{ __('payout.paid') }}</p>
                        <h5 class="card-title" id="paidBalance">{{ $data['paid'] }}</h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-12">
            <div class="card">
                <div class="row no-gutters align-items-center page_top_cnt_boxs_view_box_2">
                    <!-- <div class="card_img_bx bg-danger rounded card-img">
                        <span class="">
                            <i class="bx bx-x font-size-26"></i>
                        </span>
                    </div> -->
                    <div class="card-body">
                       
                        <p class="card-text">{{ __('payout.rejected') }}</p>
                        <h5 class="card-title" id="rejectedBalance">{{ $data['rejected'] }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card business-card">
                <div class="card-body">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#payoutSummary" role="tab"
                                aria-selected="true" onclick="filterSummaryReports()">
                                <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                                <span class="d-none d-sm-block">{{ __('payout.payout_summary') }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#payoutRelease" role="tab"
                                aria-selected="false" onclick="getPayoutRelease()">
                                <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
                                <span class="d-none d-sm-block">{{ __('payout.payout_release') }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#processPayment" role="tab"
                                aria-selected="false" onclick="getProcessPayment()">
                                <span class="d-block d-sm-none"><i class="far fa-envelope"></i></span>
                                <span class="d-none d-sm-block">{{ __('payout.process_payment') }}</span>
                            </a>
                        </li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content text-muted">
                        <div class="tab-pane active" id="payoutSummary" role="tabpanel">
                            <div class="filter_box_new">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="">{{ __('common.user_name') }}</label>
                                        <select
                                            class="form-control select2-ajax select2-search-user select2-multiple d-none"
                                            multiple="multiple" id="payoutSummeryUser" name="username"></select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="">{{ __('common.status') }}</label>
                                        <select name="status" class="form-select" id="payoutSummaryStatus">
                                            <option value="pending">{{ __('common.pending') }}</option>
                                            <option value="approved">{{ __('common.approved') }}</option>
                                            <option value="paid" selected>{{ __('common.paid') }}</option>
                                            <option value="rejected">{{ __('common.rejected') }}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4" style="margin-top:23px">
                                        <a href="#" class="btn btn-primary"
                                            onclick="filterSummaryReports()">{{ __('common.view') }}</a>
                                        <a href="javascript:void(0);" onclick="reset('summary')"
                                            class="btn btn-danger">{{ __('common.reset') }}</a>
                                    </div>
                                </div>
                            </div>
                            <table id="payoutSummaryTable" class="table  table-hover">
                                <thead class="table">
                                    <th>{{ __('common.member_name') }}</th>
                                    <th>{{ __('payout.invoice_number') }}</th>
                                    <th>{{ __('common.amount') }}</th>
                                    <th>{{ __('payout.payout_method') }}</th>
                                    <th>{{ __('common.ewallet_balance') }}</th>
                                    <th>{{ __('payout.paid_date') }}</th>
                                    <th>{{ __('payout.rejected_date') }}</th>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="100%">
                                            <div class="nodata_view">
                                                <img src="{{ asset('assets/images/nodata-icon.png') }}" alt="">
                                                <span>{{ __('common.no_data') }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane" id="payoutRelease" role="tabpanel">
                            <div class="filter_box_new">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="">{{ __('common.user_name') }}</label>
                                        <select
                                            class="form-control select2-ajax select2-search-user select2-multiple d-none"
                                            multiple="multiple" id="payoutReleaseUsers" name="username"></select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="">{{ __('common.payment_gateway') }}</label>
                                        <select name="paymentGateway" class="form-select"
                                            id="payoutRelesePaymentGateway">
                                            @forelse ($paymentGateWay as $item)
                                                <option value="{{ $item->id }}"
                                                    {{ $item->name == 'Bank Transfer' ? 'selected' : '' }}>
                                                    {{ $item->name == 'Bitcoin' ? 'Blocktrail' : $item->name }}
                                                </option>
                                            @empty
                                                <option value="Bank Transfer" selected>Bank Transfer</option>
                                            @endforelse
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="">{{ __('payout.payout_type') }}</label>
                                        <select name="payoutReleseType" class="form-select" id="payoutReleaseType">
                                            @if ($payoutRelease == 'both')
                                                <option value="admin">{{ __('payout.manual') }}</option>
                                                <option value="user">{{ __('payout.user_request') }}</option>
                                            @elseif($payoutRelease == 'from_ewallet')
                                                <option value="admin">{{ __('payout.manual') }}</option>
                                            @elseif($payoutRelease == 'ewallet_request')
                                                <option value="user">{{ __('payout.user_request') }}</option>
                                            @endif
                                        </select>
                                    </div>
                                    @if ($moduleStatus['kyc_status'])
                                        <div class="col-md-2">
                                            <label for="">KYC {{ __('common.status') }}</label>
                                            <select name="kyc_status" class="form-select" id="payoutReleseKycStatus">
                                                <option value="active">{{ __('payout.kyc_verified') }}</option>
                                                <option value="nonactive">{{ __('payout.kyc_unverified') }}</option>
                                            </select>
                                        </div>
                                    @endif
                                    <div class="col-md-2" style="margin-top:23px">
                                        <a href="#" class="btn btn-primary"
                                            onclick="getPayoutRelease()">{{ __('common.view') }}</a>
                                        <a href="javascript:void(0);" onclick="reset('payout-release')"
                                            class="btn btn-danger">{{ __('common.reset') }}</a>
                                    </div>
                                </div>
                            </div>
                            <div id="data-table-payoutRelease" class="row">
                                <table id="payoutReleaseTable" class="table  table-hover">
                                    <thead class="table">
                                        <th><input type="checkbox" name="" id="checkAll"
                                                class="form-check-input">
                                        </th>
                                        <th>{{ __('common.member_name') }}</th>
                                        <th>{{ __('payout.payout_amount') }}</th>
                                        <th>{{ __('payout.payout_method') }}</th>
                                        <th>{{ __('payout.payout_type') }}</th>
                                        <th>{{ __('common.ewallet_balance') }}</th>
                                        <th></th>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane" id="processPayment" role="tabpanel">
                            <div class="filter_box_new">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="">{{ __('common.user_name') }}</label>
                                        <select
                                            class="form-control select2-ajax select2-search-user select2-multiple d-none"
                                            multiple="multiple" name="username" id="processPaymentUsers"></select>
                                    </div>
                                    <div class="col-md-3" style="margin-top:23px">
                                        <a href="#" class="btn btn-primary"
                                            onclick="getProcessPayment()">{{ __('common.view') }}</a>
                                        <a href="#" type="button" class="btn btn-danger"
                                            onclick="resetProcessPayment(this)">{{ __('common.reset') }}</a>
                                    </div>
                                </div>
                            </div>
                            <div id="data-table-processPayment" class="row">
                                <table id="payoutProcessTable" class="table  table-hover">
                                    <thead class="table">
                                        <th>{{ __('common.member_name') }}</th>
                                        <th>{{ __('common.amount') }}</th>
                                        <th>{{ __('payout.approved_date') }}</th>
                                        <th>{{ __('common.action') }}</th>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div>
                </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(() => {
            getSummaryReports();
            getUsers();
        });
        const getPayoutRelease = async () => {
            event.preventDefault();
            getUsers();
            let url = "{{ route('payout.reports.release') }}";
            let params = {
                users: $('#payoutReleaseUsers').val(),
                payment_method: $('#payoutRelesePaymentGateway').val(),
                payoutReleaseType: $('#payoutReleaseType').val(),
                kycStatus: $('#payoutReleseKycStatus').val(),
            }
            var table = $('#payoutReleaseTable').DataTable({
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
                    url: url,
                    data: params
                },
                columns: [{
                        data: 'checkbox',
                        orderable: false,
                    },
                    {
                        data: 'member_name',
                        name: 'member_name'
                    },
                    {
                        data: 'amount',
                        name: 'amount_type',
                        orderable: false
                    },
                    {
                        data: 'payment_method',
                        name: 'payment_method',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'payout_type',
                        name: 'payout_type',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'balance',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false,
                    }
                ]
            });
        }

        const getSummaryReports = () => {
            let url = "{{ route('payout.reports.summary') }}";
            let params = {
                users: $('#payoutSummeryUser').val(),
                status: $('#payoutSummaryStatus').val(),
            }

            let columnNames = [{
                    data: 'member_name',
                    name: 'member_name',
                    title: `{{ __('common.member_name') }}`,
                    visible: true,
                },
                {
                    data: 'invoice_number',
                    name: 'id',
                    orderable: true,
                    title: `{{ __('payout.invoice_number') }}`,
                    visible: true,
                },
                {
                    data: 'amount',
                    name: 'amount',
                    orderable: true,
                    title: `{{ __('common.amount') }}`,
                    visible: true,
                },
                {
                    data: 'payment_method',
                    name: 'payment_method',
                    orderable: false,
                    title: `{{ __('payout.payout_method') }}`,
                    visible: true,
                },
                {
                    data: 'ewallet_balance',
                    name: 'ewallet_balance',
                    orderable: false,
                    title: `{{ __('ewallet.ewallet_balance') }}`,
                    visible: false,
                },
                {
                    data: 'payout_date',
                    name: 'created_at',
                    title: `{{ __('payout.paid_date') }}`,
                    orderable: true,
                    visible: true,
                },
                {
                    data: 'rejected_date',
                    name: 'updated_at',
                    visible: false,
                    orderable: true,
                },
            ];
            let newFilterColumnName = columnNames.map((value, index, array) => {
                var alter = {
                    title: value.title,
                    visible: value.visible,
                };

                if ($('#payoutSummaryStatus').val() == 'pending') {
                    alter = {
                        ...alter,
                        visible: (value.name == 'id' || value.name == 'payment_method' || value.visible ==
                            'false' || value.name == 'updated_at' ? false : true),
                        title: (value.name == 'created_at' ? 'Requested Date' : value.title)
                    };
                }

                if ($('#payoutSummaryStatus').val() == 'approved') {
                    alter = {
                        ...alter,
                        visible: (value.name == 'id' || value.name == 'ewallet_balance' || value.name ==
                            'updated_at' ? false : true),
                        title: (value.name == 'created_at' ? 'Approved Date' : value.title)
                    }
                }

                if ($('#payoutSummaryStatus').val() == 'rejected') {
                    alter = {
                        ...alter,
                        visible: (value.name == 'id' || value.name == 'payment_method' || value.name ==
                            'ewallet_balance' ? false : true),
                        title: (value.name == 'created_at' ? 'Approved Date' : value.title)
                    }
                }

                return ({
                    ...value,
                    ...alter
                });
            });
            let table = $('#payoutSummaryTable').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                "sDom": 'Lfrtlip',
                "language": {
                    "emptyTable": "<div class='nodata_view'><img src='{{ asset('assets/images/nodata-icon.png') }}'' alt=''><span class='text-secondary fs-5'>{{ __('common.no_data') }}</span></div>"
                },
                ajax: {
                    type: "GET",
                    url: url,
                    data: params,
                },
                columns: newFilterColumnName,

            });
        }

        const filterSummaryReports = () => {
            event.preventDefault();
            $('#payoutSummaryTable').DataTable().clear().destroy();
            getSummaryReports();
        }
        let isProcessing = false;
        const approvePayoutRelease = async () => {

           if (isProcessing) {
           return;
           }
           isProcessing = true;
           try{
               event.preventDefault();
           let checkedItems = [];
           let items_selected = $(".checked-box:checked");
           for await(item of items_selected) {
               checkedItems.push(item.value);
           }
           console.log("checkedItems "+checkedItems,typeof checkedItems);
           let url = "{{ route('payout.release.request', ['checkedItems' => ':checkedItems']) }}";
           url = url.replace(':checkedItems', checkedItems);
           let amounts = [];
           for(let i=0;i<checkedItems.length;i++)
           {
               console.log('id =',checkedItems[i]);
               let str = '#adminPayout_' + checkedItems[i];
               console.log($(str).val());
               amounts.push($(str).val());
           }
           console.log(amounts);
           let data = {
               amount: amounts
           }
           let amount = await getSummaryAmounts();
           const res = await $.post(`${url}`, data)
               .catch((err) => {
                   if (err.status === 422) {
                       let msg = err.responseJSON.message;
                       notifyError(msg);
                       str = str.replace('#', '');
                       inputvalidationError(str, err);
                   }
               }).then((result) => {
                   let msg = result.message;
                   notifySuccess(msg);
                   $('#payoutReleaseTable').DataTable().draw();
                   $('#pendingBalance').html('');
                   $('#pendingBalance').html(amount.pending);
                   $('#approvedBalance').html('');
                   $('#approvedBalance').html(amount.approved);
                   $('#paidBalance').html('');
                   $('#paidBalance').html(amount.paid);
                   $('#rejectedBalance').html('');
                   $('#rejectedBalance').html(amount.rejected);
               })
               isProcessing = false;
           }  catch (error) {
               isProcessing = false;
           }
       }

        const getProcessPayment = async () => {
            event.preventDefault();
            getUsers();
            let url = "{{ route('payout.process.payment') }}";
            let params = {
                users: $('#processPaymentUsers').val(),
            }
            var table = $('#payoutProcessTable').DataTable({
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
                    url: url,
                    data: params
                },
                columns: [{
                        data: 'member_name',
                        name: 'member_name',
                    }, {
                        data: 'amount',
                        name: 'amount',
                        orderable: true,
                    },
                    {
                        data: 'payout_date',
                        name: 'created_at',
                        orderable: true,
                    },
                    {
                        data: 'action'
                    },
                ]
            })
        }

        const approveProcessPayment = async (id) => {
            event.preventDefault();
            let url = "{{ route('payout.approve.payment') }}/" + id;
            let amount = await getSummaryAmounts();
            const res = await $.post(`${url}`)
                .catch((err) => {
                    console.log(err);
                }).then((result) => {
                    let msg = result.message;
                    notifySuccess(msg);
                    $('#payoutProcessTable').DataTable().draw();
                    $('#payoutSummaryTable').DataTable().draw();
                    $('#pendingBalance').html('');
                    $('#pendingBalance').html(amount.pending);
                    $('#approvedBalance').html('');
                    $('#approvedBalance').html(amount.approved);
                    $('#paidBalance').html('');
                    $('#paidBalance').html(amount.paid);
                    $('#rejectedBalance').html('');
                    $('#rejectedBalance').html(amount.rejected);
                })
        }
        const resetProcessPayment = async (data) => {
            event.preventDefault();
            $('#processPaymentUsers').empty();
            getProcessPayment();
        }

        const approvePayoutReleaseUserRequest = async () => {
            event.preventDefault();
            let checkedItems = [];
            let items_selected = $(".checked-box:checked");
            for await(item of items_selected) {
                checkedItems.push(item.value);
            }
            console.log(checkedItems);
            $('#preloader').removeClass('d-none');
            let url = "{{ route('payout.approve.user.request', ['checkedItems' => ':checkedItems']) }}";
            url = url.replace(':checkedItems', checkedItems);
            let amount = await getSummaryAmounts();
            const res = await $.post(url)
                .catch((err) => {
                    $('#preloader').addClass('d-none');
                    if (err.status === 422) {
                        let msg = err.responseJSON.message;
                        notifyError(msg);
                    }
                }).then((result) => {
                    $('#preloader').addClass('d-none');
                    let msg = result.message;
                    notifySuccess(msg);
                    $('#payoutReleaseTable').DataTable().draw();
                    $('#pendingBalance').html('');
                    $('#pendingBalance').html(amount.pending);
                    $('#approvedBalance').html('');
                    $('#approvedBalance').html(amount.approved);
                    $('#paidBalance').html('');
                    $('#paidBalance').html(amount.paid);
                    $('#rejectedBalance').html('');
                    $('#rejectedBalance').html(amount.rejected);

                })
            $('#preloader').addClass('d-none');

        }
        const rejectPayoutReleaseUserRequest = async () => {
            event.preventDefault();
            let checkedItems = [];
            let items_selected = $(".checked-box:checked");
            for await(item of items_selected) {
                checkedItems.push(item.value);
            }
            let url = "{{ route('payout.reject.user.request', ['checkedItems' => ':checkedItems']) }}";
            url = url.replace(':checkedItems', checkedItems);
            let amount = await getSummaryAmounts();
            console.log(amount);
            const res = await $.post(`${url}`)
                .catch((err) => {
                    if (err.status === 422) {
                        let msg = err.responseJSON.message;
                        notifyError(msg);
                    }
                }).then((result) => {
                    let msg = result.message;
                    notifySuccess(msg);
                    $('#payoutReleaseTable').DataTable().draw();
                    $('#pendingBalance').html('');
                    $('#pendingBalance').html(amount.pending);
                    $('#approvedBalance').html('');
                    $('#approvedBalance').html(amount.approved);
                    $('#paidBalance').html('');
                    $('#paidBalance').html(amount.paid);
                    $('#rejectedBalance').html('');
                    $('#rejectedBalance').html(amount.rejected);

                })
        }

        const getSummaryAmounts = async () => {
            try {
                let url = "{{ route('payout.summary.amounts') }}";
                const res = await $.get(`${url}`);
                return res;
            } catch (error) {
                console.log(error)
            }

        }

        const reset = (tab) => {
            event.preventDefault();
            switch (tab) {
                case 'summary':
                    $('#payoutSummeryUser').empty();
                    $('#payoutSummaryTable').DataTable().destroy();
                    getSummaryReports();
                    break;
                case'payout-release':
                    $('#payoutReleaseUsers').empty();
                    $('#payoutRelesePaymentGateway')[0].selectedIndex = 0;
                    $('#payoutReleaseType')[0].selectedIndex = 0;
                    $('#payoutReleseKycStatus')[0].selectedIndex = 0;
                    $('#payoutReleaseTable').DataTable().destroy();
                    getPayoutRelease();
                default:
                    break;
            }
        }
        $(() => {
            $('input:checkbox').prop('checked', false);
        })
        $('#checkAll').on('click', function(e) {
            if ($('.checked-box').not(':disabled').length) {
                $('.checked-box').not(this).not(':disabled').prop('checked', this.checked);
                showActiveActionPopup();
                e.stopImmediatePropagation();
            }
        })
        $('#submit_form').on('submit', function() {
            $('#submit_form button').addClass('disabled');
            $('#submit_form button').text("{{ __('common.loading') }}");
        });
        $('#approval_table').DataTable({
            "lengthChange": false,
            "searching": false,
        });

        function showActiveActionPopup() {
            let checkedItems = [];
            let items_selected = $(".checked-box:checked");
            for(item of items_selected) {
                checkedItems.push(item.value);
            }
            console.log(checkedItems);
            if ($(".checked-box:checked").length > 0) {
                // console.log(checkedItems);
                $('#active_items_selected_span').text(`${checkedItems.length} items selected`);
                // let remains = 10 - items_selected;
                // $('#active_items_selected_div_new').text(`${remains} more items left`);
                $('#reg_approval_action_popup').removeClass('d-none');
            } else { // none is checked
                $('.checked-box').prop('checked', false);
                $('#approval_table #checkAll').prop('checked', false);
                $('#reg_approval_action_popup').addClass('d-none');
            }
        }

        $(document).on('click', '.checked-box', function(e){
            showActiveActionPopup();
        });

    </script>
@endpush
