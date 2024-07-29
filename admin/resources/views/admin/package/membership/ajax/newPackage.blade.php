
<table class="table  table-hover rankTable">
    <thead>
        <tr class="th">
            <th>#</th>
            <th>{{ __('package.name') }}</th>
            <th>{{ __('common.amount') }}</th>
            @if ($pvVisible == 'yes')
                <th>{{ __('package.pv') }}</th>
            @endif
            @if ($bvVisible == 'yes')
                <th>{{ __('package.bv_value') }}</th>
            @endif
            @if ($moduleStatus->subscription_status)
                <th>{{ __('package.validity') }}</th>
            @endif
            <th>{{ __('common.action') }}</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($packages as $package)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $package->name }}</td>
                <td>{{ $currency . ' ' . formatCurrency($package->price) }}</td>
                @if ($pvVisible == 'yes')
                    <td>{{ $package->pair_value }}</td>
                @endif
                @if ($bvVisible == 'yes')
                    <td>{{ $package->bv_value }}</td>
                @endif
                @if ($moduleStatus->subscription_status)
                    <td>
                        {{ $package->validity }}
                    </td>
                @endif
                <td>
                    <form action="{{ route('packageDis', $package->id) }}" method="post">
                        @csrf
                        <a href="{{ route('member.package.edit', $package->id) }}" class="btn btn-primary"
                            onclick="editPackage(this)"><i class="bx bx-edit"></i></a>
                        <button type="submit" class="btn ms-3 {{ $package->active ? 'btn-danger' : 'btn-success' }}">
                            <i class="{{ $package->active ? 'bx bx-block ' : 'bx bx-check-circle ' }}"></i></button>
                    </form>

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
<ul class="pagination pagination-rounded justify-content-end m-2">
    {{ $packages->links() }}
</ul>
