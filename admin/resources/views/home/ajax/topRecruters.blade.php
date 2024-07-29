@forelse ($topRecruters as $user)
    <div class="d-flex new_members_row">
        <div class="flex-shrink-0 me-3">
            @if($user->image)
                <img class="rounded avatar-sm" style="border-radius: 9px !important" src="{{ $user->image }}"
                alt="Generic placeholder image">
            @else
                <img class="rounded avatar-sm" src="{{ asset('assets/images/users/avatar-1.jpg') }}"
                alt="Generic placeholder image">
            @endif
        </div>
        <div class="flex-grow-1">
            <h5>{{ $user->username }}</h5>
            <p class="mb-0">
                {{ $user->name . ' ' . $user->secondName }}
                <strong style="float:right" class="btn-sm btn btn-primary">{{ $user->count }}</strong>
            </p>
        </div>
    </div>
@empty
    <div class="nodata_view">
        <img src="{{ asset('assets/images/nodata-icon.png') }}" alt="">
        <span class="text-secondary fs-5">{{ __('common.no_data') }}</span>
    </div>
@endforelse
