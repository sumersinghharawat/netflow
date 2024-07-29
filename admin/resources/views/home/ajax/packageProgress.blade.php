@forelse ($packageOverView as $package)
    <div class="d-flex new_members_row">
        <div class="flex-grow-1">
            <h5>{{ $package['name'] }}</h5>
            <p class="mb-0">{{ __('dashboard.you_have') }}
                {{ $package['count'] . ' ' . $package['name'] }}
                {{ __('dashboard.package') }}
                {{ __('dashboard.team_purchase') }}
                <strong class="btn-sm btn btn-primary" style="float:right">{{ $package['count'] }}</strong>
            </p>
        </div>
    </div>
@empty
<div class="nodata_view"  >
    <img src="{{asset('assets/images/nodata-icon.png')}}" alt="">
    <span class="text-secondary fs-5">{{ __('common.no_data') }}</span>
</div>
@endforelse
