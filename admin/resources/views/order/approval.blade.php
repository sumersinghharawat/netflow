@extends('layouts.app')
@section('title', 'Approval')

@section('content')
    <div class="row">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">{{ __('common.approval') }}</h4>
        </div>
    </div>
    @if (config('mlm.demo_status') == 'yes')
        <h5 class="mb-2 text-black"><i class="mdi mdi-alert-circle-outline me-3"></i>This is an addon module</h5>
    @endif
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="">{{ __('common.username') }}</label>
                            <select class="form-control select2-ajax select2-search-user select2-multiple" id="users"
                                name="userId"></select>
                            <span id="error" style="color: red;">
                            </span>
                        </div>
                        <div class="col-md-2" style="margin-top: 23px;">
                            <button class="btn btn-primary" type="button"
                                onclick="loadpendingOrder()">{{ __('common.view') }}</button>
                        </div>
                    </div>
                    <div class="row">
                        <form action="{{ route('order.approve') }}" method="post">
                            @csrf
                            <div id="pendingOrder" class="row mt-4">
                                <table id="datatable-view-pendingorders" class="table table-hover">
                                    <thead>
                                        <th>
                                            <input type="checkbox" id="checkAll" name="checkAll" class="form-check-input">
                                        </th>
                                        <th>{{ __('common.invoice_no') }}</th>
                                        <th>{{ __('common.member') }}</th>
                                        <th>{{ __('common.amount') }}</th>
                                        <th>{{ __('common.payment_method') }}</th>
                                        <th>{{ __('common.order_date') }}</th>
                                        <th>{{ __('common.action') }}</th>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                                {{-- <div class="mt-3">
                                    <button type="submit" class="btn btn-primary">{{ __('common.approve') }}</button>
                                </div> --}}
                                <div class="row">
                                    <div class="popup-btn-area col-8 d-none" id="approval_action_popup">
                                        <div class="row">
                                            <div class="text-white col">
                                                <span id="active_items_selected_span"></span>
                                                <!-- <div id="active_items_selected_div_new"></div> -->
                                            </div>
                                            <div class="col">
                                                <!-- <button type="submit"
                                                    class="btn {{ request()->input('status') == 'blocked' ? 'btn-primary' : 'btn-danger' }}">
                                                    {{ request()->input('status') == 'blocked' ? __('common.activate') : __('common.block') }}

                                                </button> -->
                                                <button type="submit" class="btn btn-primary">{{ __('common.approve') }}</button>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
    </div>
    <div class="modal fade" id="viewReceipt" tabindex="-1" role="dialog" aria-labelledby="composemodalTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="composemodalTitle">{{ __('approval.receipt') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div id="receipt">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('common.close') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $('#checkAll').on('click', function(e) {
            $('input:checkbox').not(this).prop('checked', this.checked);
            showOrderActiveActionPopup();
            e.stopImmediatePropagation();
        })

        $(() => {
            getUsers();
            loadpendingOrder();
            $('input:checkbox').prop('checked', false);
        });

        const loadpendingOrder = async () => {

            let params = {

                userId: $('#users').val()
            }
            console.log(params);
            var table = $('#datatable-view-pendingorders').DataTable({
                processing: true,
                serverSide: true,
                "sDom": 'Lfrtlip',
                searching: false,
                "language": {
                    "emptyTable": "<div class='nodata_view'><img src='{{ asset('assets/images/nodata-icon.png') }}'' alt=''><span class='text-secondary fs-5'>{{ __('common.no_data') }}</span></div>"
                },
                "bDestroy": true,
                ajax: {
                    type: "GET",
                    url: "{{ route('getpendingOrders') }}",
                    data: params
                },
                columns: [
                    {
                        data: 'checkall',
                        name: 'checkall',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        data: 'invoice_no',
                        name: 'invoice_no',
                        searchable: true
                    },
                    {
                        data: 'member',
                        name: 'name',
                        searchable: true
                    },
                    {
                        data: 'total_amount',
                        name: 'amount',
                        searchable: true
                    },
                    {
                        data: 'payment_method',
                        name: 'payment_method',
                        searchable: true,
                    },
                    {
                        data: 'order_date',
                        name: 'order_date',
                        searchable: true,
                    },
                    {
                        data: 'view_receipt',
                        name: 'view_receipt',
                        searchable: true,
                    }


                ]
            });
        }

        const getReceipt = async (id) => {
            let url = "{{ route('getReceipt', ':id') }}";
            url = url.replace(":id", id)
            const res = await $.get(`${url}`)
            $('#receipt').html(' ');
            $('#receipt').html(res.data);
        };

         function showOrderActiveActionPopup() {
             if ($(".checked-box:checked").length > 0) { // any one is checked
                 let items_selected = $(".checked-box:checked").length;
                 $('#active_items_selected_span').text(`${items_selected} items selected`);
                 $('#approval_action_popup').removeClass('d-none');
             } else { // none is checked
                 $('.checked-box').prop('checked', false);
                 $('#datatable-view-pendingorders #checkAll').prop('checked', false);
                 $('#approval_action_popup').addClass('d-none');
             }
         }


    </script>
@endpush
