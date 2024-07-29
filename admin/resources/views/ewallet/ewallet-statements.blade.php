<div id="EwalletStateDateReport">
    <table id="datatable-buttons" class="table table-bordered dt-responsive w-100">
        <thead>
            <tr>
                <th style="width: 50%">
                    {{ __('ewallet.description') }}
                </th>
                <th>
                    {{ __('common.amount') }}
                </th>
                <th>
                    {{ __('ewallet.transaction_fee') }}
                </th>
                <th>
                    {{ __('common.balance') }}
                </th>
                <th>
                    {{ __('ewallet_transaction_date') }}
                </th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $statement)
                <tr>
                    <td>
                        {{ $statement['description'] }}
                    </td>
                    <td>
                        ₹ {{ formatCurrency($statement['amount']) }}
                    </td>
                    <td>
                        ₹ {{ formatCurrency($statement['transaction_fee']) }}
                    </td>
                    <td>
                        ₹ {{ formatCurrency($statement['balance']) }}
                    </td>
                    <td>
                        {{ Carbon\Carbon::parse($statement['transaction_date'])->toDateString() }}
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
</div>
