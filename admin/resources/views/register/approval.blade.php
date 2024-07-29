@extends('layouts.app')
@section('title', __('common.approval'))

@section('content')
    <main class="my-2">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __('common.approval') }}</h4>
            </div>
            <div class="card">
                <div class="card-body">

                    <form action="{{ route('approval.approve') }}" method="post" id="submit_form">
                        @csrf
                        <div class="table-responsive">
                            <table class="table table-hover" id="approval_table">
                                <thead>
                                    <tr>
                                        <th>
                                            <input type="checkbox" id="checkAll" name="checkAll" class="form-check-input">
                                        </th>
                                        <th>
                                            {{ __('common.name') }}
                                        </th>
                                        <th>
                                            {{ __('common.email') }}
                                        </th>
                                        <th>
                                            {{ __('common.sponsor') }}
                                        </th>
                                        @if ($modulestatus->product_status)
                                            <th>
                                                {{ __('common.package') }}
                                            </th>
                                        @endif
                                        <th>
                                            {{ __('common.totalAmount') }}
                                        </th>
                                        <th>
                                            {{ __('common.payment_method') }}
                                        </th>
                                        <th>
                                            {{ __('common.action') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($pendingUsers as $key=>$list)
                                        <tr class="{{ $list['status'] == 'failed' ? 'bg-light-hash' : '' }}">
                                            <td>
                                                <input type="checkbox" name="user[]"
                                                    @if ($list['status'] != 'pending') disabled @endif
                                                    class="form-check-input {{ $list['status'] != 'processing' ? 'checked-box' : '' }}"
                                                    value="{{ $list['id'] }}">
                                            </td>
                                            <td>
                                                @if (isset($list['first_name']))
                                                    {{ $list['first_name'] . ' ' . '(' . $list['username'] . ')' }}
                                                @else
                                                    {{ $list['username'] }}
                                                @endif
                                            </td>
                                            <td>
                                                {{ $list['email'] }}
                                            </td>
                                            <td>
                                                {{ $list['sponsorUsername'] }}
                                            </td>
                                            @if ($modulestatus->product_status)
                                                <td>
                                                    {{ $list['package'] }}
                                                </td>
                                            @endif
                                            <td>
                                                {{ $currency . ' ' . formatCurrency($list['totalAmount']) }}
                                            </td>
                                            <td>
                                                {{ $list['paymentMethod']->name }}
                                            </td>
                                            <td>
                                                @if ($list['status'] == 'pending')
                                                    <a class="pointer" data-bs-toggle="modal"
                                                        data-bs-target="#list{{ $list['id'] }}">
                                                        <i class="mdi mdi-eye"></i></a>
                                                    @if ($list['payment'] == 'bank-transfer')
                                                        <a class="pointer" data-bs-toggle="modal"
                                                            data-bs-target="#receipt{{ $list['id'] }}">
                                                            <i class="bx bx-receipt"></i></a>
                                                    @endif
                                                @elseif ($list['status'] == 'failed')
                                                    <p>
                                                        {{ $list['status'] }} : {{ $list['failed_reason'] }}
                                                    </p>
                                                @else
                                                    <div class="progress-bar progress-bar-striped bg-success p-2"
                                                        role="progressbar" style="width: 75%;height:10px" aria-valuenow="25"
                                                        aria-valuemin="0" aria-valuemax="100">
                                                        {{ __('common.progress') }}</div>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="100%">
                                                <div class="nodata_view">
                                                    <img src="{{ asset('assets/images/nodata-icon.png') }}" alt="">
                                                    <span>{{ __('common.no_data') }}</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse

                                </tbody>

                            </table>
                            <!-- <div class="col-md-12 d-flex popup-btn-area" style="gap:10px">
                                                                <button type="submit" class="btn btn-primary" name="approve"
                                                                    {{-- value="1">{{ __('common.approve') }}</button> --}}
                                                                <button type="submit" class="btn btn-danger" name="rejected"
                                                                    {{-- value="1">{{ __('common.reject') }}</button> --}}
                                                            </div> -->
                            <div class="row">
                                <div class="popup-btn-area col-8 d-none" id="reg_approval_action_popup">
                                    <div class="row">
                                        <div class="text-white col">
                                            <span id="active_items_selected_span"></span>
                                            <!-- <div id="active_items_selected_div_new"></div> -->
                                        </div>
                                        <div class="col">
                                            <button class="btn btn-primary" name="approve"
                                                value="1">{{ __('common.approve') }}</button>
                                            <button class="btn btn-danger" name="rejected"
                                                value="1">{{ __('common.reject') }}</button>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </main>
    @forelse ($pendingUsers as $list)
        @isset($list['receipt'])
            <div class="modal fade modal-receipt" id="receipt{{ $list['id'] }}" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header text-white">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <img src="{{ $list['receipt'] }}" alt="404 not found" class="img-fluid">
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        @endisset
        <div class="modal fade" id="list{{ $list['id'] }}" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" style="max-width: 800px">
                <div class="modal-content">
                    <div class="modal-header text-white">
                        <h4>{{ __('approval.user_information') }}</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="max-height: 80vh;overflow: auto;">
                        <div class="col">

                               <div class="user_infrom__top_dtl">
                                        <div class="user_infrom__top_usr_photo"></div>
                                        <div class="user_infrom__usr_dtl">
                                            <p>{{ __('common.username') }}:</p>
                                            <h5 class="" id="exampleModalLabel"><span class="text-uppercase text-primary">{{ $list['username'] }}</span></h5>
                                        </div>
                                </div>
                            <strong class="usr_inform_pop_head">{{ __('approval.sponsor_and_package_information') }}</strong>

                            <div class="usr_inform_box_sc">
                                <div class="usr_inform_box">
                                    <p>{{ __('common.sponsor') }} :</p>
                                    <strong>{{ $list['sponsor'] }}</strong>
                                </div>
                                <div class="usr_inform_box">
                                    <p>{{ __('common.package') }} :</p>
                                    <strong>{{ $list['package'] }}</strong>
                                </div>
                            <strong class="usr_inform_pop_head">{{ __('approval.contact_information') }}</strong>

                                @if (isset($list['first_name']))
                                <div class="usr_inform_box">
                                    <p>{{ __('common.first_name') }} :</p>
                                    <strong>{{ $list['first_name'] }}</strong>
                                </div>
                                @endif
                                @if (isset($list['last_name']))
                                <div class="usr_inform_box">
                                    <p>{{ __('common.last_name') }} :</p>
                                    <strong>{{ $list['last_name'] }}</strong>
                                </div>
                                @endif
                                @if (isset($list['date_of_birth']) && in_array('date_of_birth', $dynamicFields))
                                <div class="usr_inform_box">
                                    <p>{{ __('common.date_of_birth') }}:</p>
                                    <strong>{{ $list['date_of_birth'] }}</strong>
                                </div>
                                @endif

                                @if (isset($list['country']) && in_array('country', $dynamicFields))
                                <div class="usr_inform_box">
                                    <p>{{ __('common.country') }} :</p>
                                    <strong>{{ $list['country'] }}</strong>
                                </div>
                                @endif

                                @if (isset($list['state']) && in_array('state', $dynamicFields))
                                <div class="usr_inform_box">
                                    <p>{{ __('common.state') }} :</p>
                                    <strong> {{ $list['state'] }}</strong>
                                </div>
                                @endif

                                @if (isset($list['email']) && in_array('email', $dynamicFields))
                                <div class="usr_inform_box">
                                    <p>{{ __('common.email') }} :</p>
                                    <strong>{{ $list['email'] }}</strong>
                                </div>
                                @endif

                                @if (isset($list['mobile']) && in_array('email', $dynamicFields))
                                <div class="usr_inform_box">
                                    <p>{{ __('common.mobile_number') }} :</p>
                                    <strong>{{ $list['mobile'] }}</strong>
                                </div>
                                @endif



                            </div>

                        </div>


                    </div>
                </div>
            </div>
        </div>

    @empty
    @endforelse
@endsection

@push('scripts')
    <script>
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
            if ($(".checked-box:checked").length > 0) { // any one is checked
                let items_selected = $(".checked-box:checked").length;
                console.log(items_selected);
                $('#active_items_selected_span').text(`${items_selected} items selected`);
                // let remains = 10 - items_selected;
                // $('#active_items_selected_div_new').text(`${remains} more items left`);
                $('#reg_approval_action_popup').removeClass('d-none');
            } else { // none is checked
                $('.checked-box').prop('checked', false);
                $('#approval_table #checkAll').prop('checked', false);
                $('#reg_approval_action_popup').addClass('d-none');
            }
        }

        $(document).on('click', function() {
            showActiveActionPopup()
        });
    </script>
@endpush
