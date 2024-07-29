<table id="datatable1" class="table  table-bordered" cellspacing="0" width="100%">


    <thead class="table-light">
        <tr>
            <th>
                {{ __('reports.description') }}
            </th>
            <th>
                {{ __('reports.amount') }}
            </th>
            <th>
                {{ __('reports.balance') }}
            </th>
            <th>
                {{ __('reports.transaction_date') }}
            </th>

        </tr>
    </thead>
    <tbody>
        @forelse($purchaseWalletTransaction as $pwallet)
            <tr>

                <td>
                    {{ $pwallet['description'] }}
                </td>

                <td>
                    ₹ {{ $pwallet['amount'] }}
                </td>
                <td>
                    ₹ {{ $pwallet['balance'] }}
                </td>
                <td>
                    {{ $pwallet['transaction_date'] }}
                </td>

            </tr>
        @empty
            <tr>
                <td>{{ __('common.no_data') }}</td>
            </tr>
        @endforelse
    </tbody>
    <tfoot>

    </tfoot>
</table>
