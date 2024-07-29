@forelse ($requests as $epin)
    <tr id="requestedEpin{{ $epin->id }}">
        <td>
            <div class="form-check font-size-16">
                <input class="form-check-input" type="checkbox" id="orderidcheck01">
                <label class="form-check-label" for="orderidcheck01"></label>
            </div>
        </td>
        <td>
            <div class="d-flex">
                <img src="{{ asset('assets/images/users/avatar-1.jpg') }}" class="me-3 rounded-circle w-25"
                    alt="user-pic">
                <div>
                    <h5>{{ $epin->requestedUser->userDetail->name }}
                    </h5>
                    <span>{{ $epin->requestedUser->username }}</span>
                </div>
            </div>
        </td>
        <td id="requestedPin{{ $epin->id }}">
            {{ $epin->requested_pin_count }}
        </td>
        <td>
            <input type="numer" class="w-50" min="0" id="allocate{{ $epin->id }}"
                max="{{ $epin->requested_pin_count }}" value="{{ $epin->requested_pin_count }}">

        </td>
        <td>
            {{ $currency . '' . number_format($epin->pin_amount, 2) }}
        </td>
        <td>
            {{ date('F.j.y', strtotime($epin->requested_date)) }}
        </td>
        <td>
            {{ date('F.j.y', strtotime($epin->expiry_date)) }}
        </td>
        <td>
            <div class="d-flex gap-3">
                <a href="javascript:void()" class="text-success mt-lg-1" onclick="allocateEpin({{ $epin->id }})"><i
                        class="bx bx-check-circle  font-size-18"></i></a>
                <a href="javascript:void()" class="text-danger" onclick="deleteEpinRequest({{ $epin->id }})"><i
                        class="mdi mdi-delete font-size-18"></i></a>
            </div>
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
