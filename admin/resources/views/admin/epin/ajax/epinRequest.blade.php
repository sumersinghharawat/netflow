<div class="table-responsive">
    <table class="table align-middle table-nowrap table-check" id="epinlist">
        <thead class="table-light">
            <tr>
                <th style="width: 20px;" class="align-middle">
                    <div class="form-check font-size-16">
                        <input class="form-check-input" type="checkbox" id="checkAll">
                        <label class="form-check-label" for="checkAll"></label>
                    </div>
                </th>
                <th class="align-middle">{{ __('epin.allocatedMember') }}</th>
                <th class="align-middle">{{ __('epin.epin') }}</th>
                <th class="align-middle">{{ __('epin.amount') }}</th>
                <th class="align-middle">{{ __('epin.balanceAmount') }}</th>
                <th class="align-middle">{{ __('common.status') }}</th>
                <th class="align-middle">{{ __('common.expiryDate') }}</th>
                <th class="align-middle">{{ __('common.action') }}</th>
            </tr>
        </thead>
        <tbody>

            @forelse ($epins as $epin)
                <tr id="epinNumber{{ $epin->id }}">
                    <td>
                        <div class="form-check font-size-16">
                            <input class="form-check-input" type="checkbox" id="orderidcheck01">
                            <label class="form-check-label" for="orderidcheck01"></label>
                        </div>
                    </td>
                    <td id="EpinUser{{ $epin->id }}">
                        <div class="d-flex">
                            <img src="{{ asset('assets/images/users/avatar-1.jpg') }}" class="me-3 rounded-circle"
                                alt="user-pic" width="50px">
                            <div>
                                <h5>{{ $epin->allocatedUser->userDetail->name }}
                                </h5>
                                <span>{{ $epin->allocatedUser->username }}</span>
                            </div>
                        </div>
                    </td>
                    <td>{{ $epin->numbers }}</td>
                    <td>
                        <span
                            class="badge badge-pill badge-soft-primary font-size-12">{{ $currency . '' . number_format($epin->amount, 2) }}</span>
                    </td>
                    <td>
                        <span
                            class="badge badge-pill badge-soft-success font-size-12">{{ $currency . '' . number_format($epin->balance_amount, 2) }}</span>
                    </td>
                    <td>
                        <span class="text-success">{{ $epin->status }}</span>
                    </td>
                    <td>
                        {{ date('F j, Y', strtotime($epin->expiry_date)) }}
                    </td>

                    <td>
                        @if($epin->status != 'deleted')
                        <div class="d-flex gap-3">

                            <a href="javascript:void()" class="text-danger"
                                onclick="deleteEpin({{ $epin->id }})"><i
                                    class="mdi mdi-delete font-size-18"></i></a>
                            <a href="javascript:void()" class="text-danger mt-lg-1"
                                onclick="epinStatusChange({{ $epin->id }})" title="block"><i
                                    class="bx bx-block font-size-18"></i></a>
                        </div>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td>
                        <div class="nodata_view"  >
                            <img src="{{asset('assets/images/nodata-icon.png')}}" alt="">
                            <span>{{ __('common.no_data') }}</span>
                        </div>
                    </td>
                </tr>
            @endforelse

        </tbody>
    </table>
</div>
<ul class="pagination pagination-rounded justify-content-end mb-2">
    {{ $epins->links() }}
</ul>
