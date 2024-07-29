@extends('layouts.app')
@section('title', trans('epin.epin'))

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __('epin.epin') }}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="text-white">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-primary" data-bs-toggle="offcanvas"
                                    data-bs-target="#addEpin" aria-controls="offcanvasRight"><i
                                        class="bx bx-plus  font-size-16 align-middle me-2"></i>{{ __('epin.addEpin') }}</button>
                                <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false" id="btnGroupVerticalDrop1">

                                    <i class="mdi mdi-chevron-down"></i>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="btnGroupVerticalDrop1">

                                    <a class="dropdown-item" href="#transferEpin" data-bs-toggle="offcanvas" role="button"
                                        aria-controls="transferEpin">{{ __('epin.epinTransfer') }}</a>
                                    <a class="dropdown-item" data-bs-toggle="offcanvas" href="#addPurchaseEpin"
                                        role="button" aria-controls="addPurchaseEpin">{{ __('epin.epinPurchase') }}</a>
                                </div>
                            </div>

                        </li>
                    </ol>
                </div>

            </div>
        </div>
    </div>

    <div class="page_top_cnt_boxs_view1">
        <div class="col-md-12">

            <div class="card">
                <div class="row no-gutters align-items-center page_top_cnt_boxs_view_box_2">
                    <!-- <div class="card_img_bx bg-primary rounded card-img">
                        <span class="">
                            <i class="bx bx-paperclip font-size-30"></i>
                        </span>
                    </div> -->
                    <div class="card-body">
                    <p class="card-text text-muted fw-medium mb-0">{{ __('epin.activeEpin') }}</p>
                        <h5 class="mb-0 card-title" id="activePins">{{ $activePins }}</h5>
                      
                    </div>
                </div>
            </div>


        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="row no-gutters align-items-center page_top_cnt_boxs_view_box_2">
                    <!-- <div class="card_img_bx bg-success rounded card-img">
                        <span class="">
                            <i class="bx bx-money font-size-30"></i>
                        </span>
                    </div> -->
                    <div class="card-body">
                    <p class="card-text text-muted fw-medium mb-0">{{ __('epin.epinBalance') }}</p>
                        <h5 class="mb-0 card-title" id="Epinbalance">{{ $currency . ' ' . formatCurrency($balanceAmount) }}
                        </h5>
                        

                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="card">
                <div class="row no-gutters align-items-center page_top_cnt_boxs_view_box_2">
                    <!-- <div class="card_img_bx bg-warning rounded card-img">
                        <span class="">
                            <i class="bx bx-time-five font-size-30"></i>
                        </span>
                    </div> -->
                    <div class="card-body">
                       
                        <p class="card-text text-muted fw-medium mb-0">{{ __('epin.pendingRequest') }}</p>
                        <h5 class="mb-0 card-title" id="pendingEpin">{{ $pendingPins }}</h5>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card business-card">
                <div class="card-body">

                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" href="#home" role="tab">
                                <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                                <span class="d-none d-sm-block">{{ __('epin.epinList') }}</span>
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link nav-color" data-bs-toggle="tab" href="#pending" role="tab"
                                onclick="getPendingRequestEpins()">
                                <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
                                <span class="d-none d-sm-block">{{ __('epin.pendingRequest') }}</span>
                            </button>
                        </li>

                    </ul>


                    <!-- Tab panes -->
                    <div class="tab-content text-muted">
                        <div class="tab-pane active" id="home" role="tabpanel">
                            <div class="col-lg-12">
                                <div class="filter_box_new">
                                    <form class="row  g-3 align-items-center">
                                        @csrf
                                        <div class="col-md-3">
                                            <div class="input-group">
                                                <label for="">{{ __('common.username') }}</label>
                                                <select name="username[]" class="usernames select2-search-user d-none"
                                                    multiple="multiple" id="activeUserName"></select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="input-group">
                                                <label for="">{{ __('epin.epin') }}</label>
                                                <select name="epin[]" class="epins select2-search-epin d-none"
                                                    multiple="multiple" id="activeEpinNumbers"></select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="input-group">
                                                <label for="">{{ __('common.amount') }}</label>
                                                <select name="amount[]" class="amounts select2-search-amount d-none"
                                                    multiple="multiple" id="activeAmounts"></select>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <label for="">{{ __('common.status') }}</label>
                                            <select class="form-select epinStatus" id="activeEpinStatus" name="status">
                                                <option value="active" selected>{{ __('common.active') }}</option>
                                                <option value="blocked">{{ __('common.blocked') }}</option>
                                                <option value="expired">{{ __('epin.expired') }}</option>
                                                <option value="used">{{ __('epin.used') }}</option>
                                                <option value="deleted">{{ __('common.deleted') }}</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3 d-flex" style="margin-top:42px">
                                            <button type="button" class="btn btn-primary w-md"
                                                onclick="getActiveEpins()">{{ __('common.search') }}</button>
                                            <a href="{{ route('epin.index') }}"
                                                class="btn btn-danger ms-2">{{ __('common.reset') }}</a>
                                        </div>
                                    </form>



                                </div>
                                <div class="row">
                                    <div class="col-12">

                                        <div class="card1" id="epinTable">
                                            <div class="table-responsive">
                                                <table class="table align-middle table-nowrap table-check" id="epinlist">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>
                                                                <input type="checkbox" name=""
                                                                    id="activeEpinsParent" class="form-check-input">
                                                            </th>
                                                            <th class="align-middle">{{ __('epin.allocatedMember') }}</th>
                                                            <th class="align-middle">{{ __('epin.epin') }}</th>
                                                            <th class="align-middle">{{ __('common.amount') }}</th>
                                                            <th class="align-middle">{{ __('epin.balanceAmount') }}</th>
                                                            <th class="align-middle">{{ __('common.status') }}</th>
                                                            <th class="align-middle">{{ __('common.expiryDate') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>

                                                    </tbody>
                                                </table>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="pending" role="tabpanel">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="filter_box_new">
                                        <form class="row  g-3 align-items-center">
                                            <div class="col-md-2">
                                                <div class="input-group">
                                                    <div class="input-group">
                                                        <label for="">{{ __('common.username') }}</label>
                                                        <select name="username[]"
                                                            class="usernames select2-search-user d-none"
                                                            multiple="multiple" id="pendingUsername"></select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 d-flex" style="margin-top:42px">
                                                <button type="button" class="btn btn-primary w-md"
                                                    onclick="getPendingRequestEpins()">{{ __('common.search') }}</button>
                                                <button onclick="resetPendingRequests()" type="button"
                                                    class="btn btn-danger w-md ms-2">{{ __('common.reset') }}</button>
                                            </div>
                                        </form>

                                    </div>
                                    <!-- end card body -->

                                </div>
                                <div class="col-lg-12">

                                    <div class="card1">
                                        <div id="pendingTable">


                                            <div class="table-responsive">

                                                <table class="table align-middle table-nowrap table-check "
                                                    id="pendingList">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>
                                                                <input type="checkbox" id="parent_checkbox"
                                                                    class="form-check-input">
                                                            </th>
                                                            <th class="align-middle">{{ __('common.name') }}</th>
                                                            <th class="align-middle">{{ __('epin.requestedPinCount') }}
                                                            </th>
                                                            <th class="align-middle">{{ __('common.count') }}</th>
                                                            <th class="align-middle">{{ __('common.amount') }}</th>
                                                            <th class="align-middle">{{ __('epin.requestedDate') }}</th>
                                                            <th class="align-middle">{{ __('common.expiryDate') }}
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>


                                                    </tbody>
                                                </table>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="popup-btn-area col-8 d-none" id="epin_requested_list_action_popup">
            <div class="row">
                <div class="text-white col">
                    <span id="requested_items_selected_epin_span"></span> items selected
                </div>
                <div class="col">
                    <button class="btn btn-primary" onclick="allocateEpin()">Approve</button>
                    <button class="btn btn-danger" onclick="deleteEpinRequest()">Reject</button>
                </div>

            </div>
        </div>
    </div>

    <div class="row">
        <div class="popup-btn-area col-md-4 d-none" id="epin_active_list_action_popup">
            <div class="col">
                <button class="btn btn-primary" onclick="epinStatusChange()" id="active_btn_status">Block</button>
                <button class="btn btn-danger" onclick="deleteEpin()">Delete</button>
                <div class="text-white col">
                    <span id="active_items_selected_epin_span"></span>
                </div>
            </div>
        </div>
    </div>

    @include('admin.epin._inc._modal')
@endsection

@push('scripts')
    <script>
        $(() => {
            getUsers();
            getEpin();
            getAmounts();
            getUsersInsideCanvas('addEpin', 'userName-add-epin');
            getUsersInsideCanvas('transferEpin', 'fromUSER');
            getUsersInsideCanvas('transferEpin', 'transfer-to_user');
            getUsersInsideCanvas('addPurchaseEpin', 'userName-epinPurchase');
            getActiveEpins();

        });
        const addNewEpin = async (form) => {
            event.preventDefault()

            var formElements = new FormData(form);
            console.log(formElements);

            for (var [key, value] of formElements) {
                form.elements[key].classList.remove('is-invalid', 'd-block')
            }
            $('.invalid-feedback').remove()

            let url = form.action
            let data = getForm(form)

            const res = await $.post(`${url}`, data)
                .catch((err) => {
                    if (err.status === 422) {
                        console.log(err);
                        let msg = "Please check the values you've submitted"
                        notifyError(msg)
                        formvalidationError(form, err)
                    } else if (err.status === 403) {
                        notifyError(err.responseJSON.message)
                    } else if (err.status === 403) {
                        notifyError(err.responseJSON.message)
                    }
                })
            if (typeof(res) != 'undefined') {

                $('#addEpin').offcanvas('hide')
                let currency = "{{ $currency }}";
                notifySuccess(res.message)
                $('#epinlist').DataTable().draw();
                $('#activePins').html(' ')
                $('#activePins').html(res.activePins)
                $('#Epinbalance').html(' ')
                $('#Epinbalance').html(currency + res.balanceAmount)
                $('#userName-add-epin').empty();
                $('#userName-add-epin').empty();
                form.reset()
            }

        }
        const addEpinPurchase = async (form) => {
            event.preventDefault()

            var formElements = new FormData(form);
            for (var [key, value] of formElements) {
                form.elements[key].classList.remove('is-invalid', 'd-block')
            }
            $('.invalid-feedback').remove()

            let url = form.action
            let data = getForm(form)

            const res = await $.post(`${url}`, data)
                .catch((err) => {
                    if (err.status === 422) {
                        formvalidationError(form, err)
                    }
                })
            if (typeof(res) != 'undefined') {
                $('#addPurchaseEpin').offcanvas('hide')
                let currency = "{{ $currency }}";
                $('#epinlist').DataTable().draw();
                $('#activePins').html(' ')
                $('#activePins').html(res.activePins)
                $('#Epinbalance').html(' ')
                $('#Epinbalance').html(currency + res.balanceAmount)
                notifySuccess(res.message)
                $('#userName-epinPurchase').empty();
                $('#userName-epinPurchase').empty();
                form.reset()
            }
        }
        const transferEpin = async (form) => {

            event.preventDefault()

            var formElements = new FormData(form);
            for (var [key, value] of formElements) {
                form.elements[key].classList.remove('is-invalid', 'd-block')
            }
            $('.invalid-feedback').remove()

            let url = form.action
            let data = getForm(form)
            var fromUser = form.from_user.value;
            var toUser = form.to_user.value;
            if (fromUser === toUser) {
                let msg = "Cannot transfer between the same users"
                notifyError(msg)
                formvalidationError(form, err)
            }
            const res = await $.post(`${url}`, data)
                .catch((err) => {
                    if (err.status === 422) {
                        let msg = "Please check the values you've submitted"
                        notifyError(msg)
                        formvalidationError(form, err)
                    }
                })
            if (typeof(res) != 'undefined') {
                form.reset()
                form.reset()
                $('#transferEpin').offcanvas('hide')
                let epinId = res.id
                let tableId = '#EpinUser' + epinId;
                $('#epinlist').DataTable().draw();

                $('#Epin').html('');
                $('#fromUSER').empty();
                $('#transfer-to_user').empty();
                $('#Epin').html('');
                $('#fromUSER').empty();
                $('#transfer-to_user').empty();
                notifySuccess(res.message)
            }
        }

        $('#fromUSER').on('change', function() {
            event.preventDefault()
            let url = "{{ route('epin.userlist') }}"
            let data = {
                'from_user': $(this).val()
            };
            let id = $(this).attr('id')
            $('.invalid-feedback').remove()
            $(`input[id="${id}"]`).removeClass("is-invalid")
            const res = $.post(`${url}`, data)
                .then((result) => {
                    console.log(result);
                    $('#Epin').html(' ')
                    $('#Epin').html(result.epinList)
                })
                .catch((err) => {
                    console.log(err);
                    if (err.status === 422) {
                        inputvalidationError(id, err)
                    }
                })
        })

        const deleteEpin = async () => {
            try {
                let confirm = await confirmSwal()
                let currency = "{{ $currency }}";
                if (confirm.isConfirmed) {
                    let selected_epins = [];
                    $('.epin-active-single:checked').each(function() {
                        selected_epins.push({
                            'pin_id': $(this).val(),
                        });
                    });
                    let data = {
                        epins: selected_epins
                    };
                    let url = "{{ route('epin.delete') }}"
                    const res = await $.post(`${url}`, data)
                    $('#epinlist').DataTable().draw();
                    notifySuccess(res.message)
                    $('#activePins').html(' ')
                    $('#activePins').html(res.count)
                    $('#Epinbalance').html(' ')
                    $('#Epinbalance').html(currency + res.balance)
                    $('#epin_active_list_action_popup').addClass('d-none');
                    $("#activeEpinsParent").prop('checked', false);

                }
            } catch (error) {
                console.log(error);
            }

        }

        const deleteEpinRequest = async () => {
            try {
                let confirm = await confirmSwal()
                if (confirm.isConfirmed == true) {
                    let selected_epins = [];
                    $('.epin-check-single:checked').each(function() {
                        selected_epins.push({
                            'pin_id': $(this).val(),
                            'count': $(this).closest('tr').find('select[name="count[]"]').val(),
                        });
                    });
                    let data = {
                        'epins': selected_epins
                    };
                    let url = "{{ route('requestedEpin.delete') }}"
                    const res = await $.post(`${url}`, data);
                    $('#pendingList').DataTable().draw();
                    $('#pendingEpin').html(' ')
                    $('#pendingEpin').html(res.pendingCount)
                    notifySuccess(res.message)
                    $('#epin_requested_list_action_popup').addClass('d-none');
                    $("#parent_checkbox").prop('checked', false);
                }
            } catch (error) {
                console.log(error);
            }

        }
        const filterUsername = async (form) => {
            event.preventDefault()

            var formElements = new FormData(form);
            console.log(formElements);

            for (var [key, value] of formElements) {
                form.elements[key].classList.remove('is-invalid', 'd-block')
            }
            $('.invalid-feedback').remove()

            let url = form.action
            let data = getForm(form)

            const res = await $.post(`${url}`, data)
                .catch((err) => {
                    if (err.status === 422) {
                        let msg = "Please check the values you've submitted"
                        notifyError(msg)
                        formvalidationError(form, err)
                    }
                }).then((result) => {
                    $('#pendingList > tbody').html(' ')
                    $('#pendingList > tbody').html(result.data)
                })
        }

        const filterEpin = async (form) => {
            event.preventDefault()
            let usernames = $('.usernames').val();
            let epins = $('.epins').val();
            let amounts = $('.amounts').val();
            let epinStatus = $('.epinStatus').val();
            let url = form.action
            let data = {
                'username': usernames,
                'epin': epins,
                'amount': amounts,
                'epinStatus': epinStatus,
            }
            const res = await $.get(`${url}`, data)
                .catch((err) => {
                    if (err.status === 422) {
                        let msg = "Please check the values you've submitted"
                        notifyError(msg)
                        formvalidationError(form, err)
                    }
                })
            console.log(res);
            $('#epinTable').html(' ')
            $('#epinTable').html(res.data)

        }

        const allocateEpin = async () => {
            try {
                event.preventDefault()
                let url = "{{ route('allocate.epin') }}"
                let selected_epins = [];
                $('.epin-check-single:checked').each(function() {
                    selected_epins.push({
                        'pin_id': $(this).val(),
                        'count': $(this).closest('tr').find('select[name="count[]"]').val(),
                    });
                });
                let data = {
                    epins: selected_epins
                };
                const result = await $.post(`${url}`, data)
                $('#activePins').html(' ')
                $('#activePins').html(result.activePins)
                $('#Epinbalance').html(' ')
                $('#Epinbalance').html(result.balanceAmount)
                $('#pendingEpin').html('')
                $('#pendingEpin').html(result.pendingCount)
                $('#epinlist').DataTable().draw();
                $('#pendingList').DataTable().draw();
                $('#epin_requested_list_action_popup').addClass('d-none');
                notifySuccess(result.message);
                $("#parent_checkbox").prop('checked', false);
            } catch (error) {
                if (error.status === 422) notifyError(err.responseJSON.message)
            }
        }

        const epinStatusChange = async () => {
            try {
                event.preventDefault()
                let url = "{{ route('status.epin') }}"
                let currency = "{{ $currency }}";
                let selected_epins = [];
                $('.epin-active-single:checked').each(function() {
                    selected_epins.push({
                        'pin_id': $(this).val(),
                    });
                });
                let data = {
                    epins: selected_epins
                }
                const res = await $.post(`${url}`, data)
                let pinStatus = res.pinStatus
                // if (pinStatus == 'blocked') {
                //     $('#epinStatus' + id).text('blocked')
                // }
                $('#epinlist').DataTable().draw();
                notifySuccess(res.message)
                $('#activePins').html(' ')
                $('#activePins').html(res.activePins)
                $('#Epinbalance').html(' ')
                $('#Epinbalance').html(currency + res.balanceAmount)
                $('#epinlist #activeEpinsParent').prop('checked', false);
                $('#epin_active_list_action_popup').addClass('d-none');
            } catch (error) {
                console.log(error);
            }

        }

        $('#epin-multiple').select2({
            placeholder: 'E-pin',
            width: '100%',
            ajax: {
                url: "{{ route('get.epins') }}",
                dataType: 'json',
                delay: 250,
                processResults: function(result) {
                    return {
                        results: $.map(result.data, function(item) {
                            return {
                                text: item.numbers,
                                id: item.numbers0
                            }
                        })
                    };
                },
                cache: true
            }
        })
        $('#amount-multiple').select2({
            placeholder: 'Amount',
            ajax: {
                url: "{{ route('get.pinAmounts') }}",
                dataType: 'json',
                delay: 250,
                processResults: function(result) {
                    return {
                        results: $.map(result.data, function(item) {
                            return {
                                text: item.amount,
                                id: item.amount
                            }
                        })
                    };
                },
                cache: true
            }
        })

        const getActiveEpins = () => {
            try {
                let url = "{{ route('get.active.epins') }}";
                let params = {
                    'username': $('#activeUserName').val(),
                    'status': $('#activeEpinStatus').val(),
                    'epin': $('#activeEpinNumbers').val(),
                    'amount': $('#activeAmounts').val(),
                }
                var table = $('#epinlist').DataTable({
                    processing: true,
                    serverSide: true,
                    searching: false,
                    "bDestroy": true,
                    "sDom": 'Lfrtlip',
                    orderable: false,
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
                            orderable: false
                        },
                        {
                            data: 'username',
                            name: 'username',
                        }, {
                            data: 'numbers',
                            name: 'numbers',
                        }, {
                            data: 'amount',
                            name: 'amount',
                        }, {
                            data: 'balance_amount',
                            name: 'balance_amount',
                        }, {
                            data: 'status',
                            name: 'status',
                        }, {
                            data: 'expiry',
                            name: 'expiry',
                            orderable: false,
                        }
                    ],
                    "drawCallback": function(settings) {
                        let status = settings.ajax.data.status;
                        let pinStatus = $('select[name="status"]').val();
                        if(pinStatus === 'deleted') {
                            $('#activeEpinsParent').prop('disabled', true);
                        } else {
                            $('#activeEpinsParent').prop('disabled', false);
                        }
                        console.log(pinStatus)
                        $('#epinlist #activeEpinsParent').prop('checked', false);
                        $('#epin_active_list_action_popup').addClass('d-none');
                        if (status == 'active') {
                            $('#active_btn_status').html('')
                            $('#active_btn_status').html('Block')
                        } else if (status == 'blocked') {
                            $('#active_btn_status').html('')
                            $('#active_btn_status').html('Unblock')
                        }
                    }

                })
            } catch (error) {
                console.log(error);
            }
        }

        const getPendingRequestEpins = () => {
            try {
                let url = "{{ route('get.requested.epins') }}";
                let params = {
                    'username': $('#pendingUsername').val(),
                }
                var table = $('#pendingList').DataTable({
                    processing: true,
                    serverSide: true,
                    searching: false,
                    "bDestroy": true,
                    orderable: false,
                    "sDom": 'Lfrtlip',
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
                            orderble: false,
                        },
                        {
                            data: 'username',
                            name: 'username',
                        }, {
                            data: 'requested_pin_count',
                            name: 'requested_pin_count',
                        }, {
                            data: 'allotted_pin',
                            name: 'allotted_pin',
                        }, {
                            data: 'pin_amount',
                            name: 'pin_amount',
                        }, {
                            data: 'requested_date',
                            name: 'requested_date',
                        }, {
                            data: 'expiry_date',
                            name: 'expiry_date',
                        }
                    ]

                })
            } catch (error) {
                console.log(error);
            }
        }

        $('#parent_checkbox').on('click', function(e) {
            $('#pendingList tbody :checkbox').prop('checked', $(this).is(':checked'));
            showEpinRequestsActionPopup();
            e.stopImmediatePropagation();
        })


        function showEpinRequestsActionPopup() {
            if ($(".epin-check-single:checked").length > 0) { // any one is checked
                let items_selected = $(".epin-check-single:checked").length;
                $('#requested_items_selected_epin_span').text(items_selected);
                $('#epin_requested_list_action_popup').removeClass('d-none');
            } else { // none is checked
                $('.epin-check-single').prop('checked', false);
                $('#pendingList #parent_checkbox').prop('checked', false);
                $('#epin_requested_list_action_popup').addClass('d-none');
            }
        }

        $('#activeEpinsParent').on('click', function(e) {
            $('#epinlist tbody :checkbox').prop('checked', $(this).is(':checked'));
            showEpinActiveActionPopup();
            e.stopImmediatePropagation();
        })

        function showEpinActiveActionPopup() {
            if ($(".epin-active-single:checked").length > 0) { // any one is checked
                let items_selected = $(".epin-active-single:checked").length;
                $('#active_items_selected_epin_span').text(`${items_selected} items selected`);
                $('#epin_active_list_action_popup').removeClass('d-none');
            } else { // none is checked
                $('.epin-active-single').prop('checked', false);
                $('#epinlist #activeEpinsParent').prop('checked', false);
                $('#epin_active_list_action_popup').addClass('d-none');
            }
        }

        const resetPendingRequests = async () => {
            try {
                $("#pendingUsername").empty(" ");
                getPendingRequestEpins();
            } catch (error) {
                console.log(error);
            }
        }
    </script>
@endpush
