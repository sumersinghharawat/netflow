<table id="datatable-buttons" class="table table-bordered dt-responsive w-100">
    <thead>
        <tr>
            <th>
                {{ __('common.member_name') }}
            </th>
            <th>
                {{ __('common.category') }}
            </th>
            <th>
                {{ __('common.amount') }}
            </th>
            <th>
                {{ __('ewallet.transaction_date') }}
            </th>
        </tr>
    </thead>
    <tbody>

        @forelse($ewalletTransactions as $transaction)
            <tr>

                <td>
                    {{ $transaction->user->userDetail->name }}
                    &nbsp;{{ $transaction->user->userDetail->second_name }}<br>
                    {{ $transaction->user->username }}
                </td>
                <td>
                    {{ $transaction->amount_type }}
                </td>
                <td>
                    ₹ {{ formatCurrency($transaction->amount) }}
                </td>
                <td>
                    {{ Carbon\Carbon::parse($transaction->date_added)->toDateString() }}
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
