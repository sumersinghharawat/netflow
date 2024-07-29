<div class="table-responsive">
    <table class="table mb-0" id="transactionTable">

        <thead class="table-light">
            <tr>
                <th>{{ __('coomon.member_name') }}</th>
                <th>{{ __('common.category') }}</th>
                <th>{{ __('common.ammount') }}</th>
                <th>{{ __('business.transaction_date') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions[0] as $transaction)
                <tr>
                    <td>{{ $transaction->full_name }}</td>
                    <td>{{ $transaction->type }}</td>
                    <td>{{ $transaction->amount }}</td>
                    <td>{{ $transaction->date }}</td>
                </tr>
            @empty
            @endforelse

        </tbody>
    </table>
</div>
