<div class="tab-pane active" id="income" role="tabpanel">
    <table class="dashboard_income_commission_table">
        @forelse($incomeAndCommission['income'] as $item)
            @php
                $str = $item['type'];
                $words = explode(' ', $str);
            @endphp
            <tr>
                <td>{{ __('dashboard.' . $item['type']) }}</td>
                <td><span class="text-success">{{ $currency }}
                        {{ formatNumberShort(formatCurrency($item['amount'])) }}
                    </span></td>
                <td><span
                        class="btn-primary btn-sm float-end">{{ Str::upper($words[0][0]) }}
                        @if (isset($words[1][0]))
                            {{ Str::upper($words[1][0]) }}
                        @endif
                    </span>
                </td>
            </tr>
        @empty
        <div class="nodata_view"  >
            <img src="{{asset('assets/images/nodata-icon.png')}}" alt="">
            <span class="text-secondary">{{ __('common.no_data') }}</span>
        </div>
        @endforelse
    </table>
</div>

<div class="tab-pane" id="commission" role="tabpanel">
    <table class="dashboard_income_commission_table">
        @foreach ($incomeAndCommission['commission'] as $item)
            @php
                $str = $item['type'];
                $words = explode(' ', $str);
            @endphp
            <tr>
                <td>{{ __('dashboard.' . $item['type']) }}</td>
                <td><span class="text-success">{{ $currency }}
                        {{ formatNumberShort(formatCurrency($item['amount'])) }}
                    </span></td>
                <td><span
                        class="btn-primary btn-sm float-end">{{ Str::upper($words[0][0]) }}
                        @if (isset($words[1][0]))
                            {{ $words[1][0] }}
                        @endif
                    </span></td>
            </tr>
        @endforeach
    </table>

</div>
