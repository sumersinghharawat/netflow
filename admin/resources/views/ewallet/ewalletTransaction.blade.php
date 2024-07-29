    <table id="datatable1" class="table table-bordered dt-responsive w-100">
        <thead>
            <th>{{ __('common.member_name') }}</th>
            <th>{{ __('common.category') }}</th>
            <th>{{ __('common.amount') }}</th>
            <th>{{ __('ewallet.transaction_date') }}</th>
        </thead>
        <tbody>
            @forelse($ewalletTransactions as $transaction)
            <tr>
                <td>
                    @if(isset($transaction->user->userDetail))
                    {{ $transaction->user->userDetail->name }} &nbsp;{{ $transaction->user->userDetail->second_name }}<br>
                    @endif
                    {{ $transaction->user->username }}
                </td>
                <td>
                    {{ __("ewallet.$transaction->amount_type") }}
            </td>
            <td>
                <span class="text-{{ ($transaction->type == "credit") ? 'success' : 'danger'}} transaction-amount">
                    $ {{ formatCurrency($transaction->amount) }}
                </span>
            </td>
            <td>
                {{Carbon\Carbon::parse($transaction->date_added)->format('M d, Y, i:sa') }}
            </td>
            </tr>
            @empty
            <tr>
                <td colspan="100%">
                    <div class="nodata_view"  >
                        <img src="{{asset('assets/images/nodata-icon.png')}}" alt="">
                        <span>{{ __('common.no_data') }}</span>
                    </div>
                </td>
            </tr>
            @endforelse

        </tbody>
        <tfoot>

        </tfoot>

    </table>
