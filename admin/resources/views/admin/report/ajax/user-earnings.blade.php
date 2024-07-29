
    <table id="datatable-buttons" class="table table-bordered dt-responsive w-100">

        <thead>
            <tr>
            <th>
                {{ __('reports.category') }}
            </th>
            <th>
               {{ __('reports.total_amount') }}
            </th>
            <th>
                {{ __('reports.tax') }}
            </th>
            <th>
                {{ __('reports.service_charge') }}
            </th>
            <th>
                {{ __('reports.amount_payable') }}
            </th>


            <th>
                {{ __('reports.transaction_date') }}
            </th>
            </tr>
        </thead>
        <tbody>



            @forelse($data as $item)
            <tr>

                <td>
                   {{ $item['category'] }}
                </td>
                <td>
                    ₹    {{ $item['amount'] }}
                </td>
                <td>
                    ₹    {{ $item['tax'] }}
                </td>
                <td>

                    ₹  {{ $item['service_charge'] }}
                </td>
                <td>
                    ₹  {{ $item['amount_payable'] }}
                </td>
                <td>
                    {{Carbon\Carbon::parse($item['transaction_date'])->toDateString() }}
                </td>
            </tr>
            @empty

            @endforelse

        </tbody>
    </table>


