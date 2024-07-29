<div class="row">
    <div class="col-md-6">
        <div class="page-title-right fw-bolder">
            @php
                $invoiceId = $moduleStatus->ecom_status ? $order->order_id : $order->id;
            @endphp

            {{ __('common.invoice') }} # : INV-{{ $invoiceId ?? '' }}

        </div>
    </div>
    <div class="col-md-6">
        <div style="float: right;">
            <a href="javascript:window.print()" class="btn btn-success waves-effect waves-light me-1" id="printButton"><i
                    class="fa fa-print"></i></a>
        </div>

    </div>
</div>
<style>
     @media print {
        body * {
            visibility: hidden;
        }

        #purchseInvoice,
        #purchseInvoice * {
            visibility: visible;
        }
    }
</style>
<div class="card-header" id="invoicePrint">

    <div class="mb-2">
        <div class="row report_address_row">
            <div class="col-md-6">
                <div class="report_address_box">
                    <h4 class="card-title ">{{ $companyProfile->name }}</h4>
                    <p class="text-muted fw-bolder">{{ $companyProfile->address }}</p>
                    <p class="text-muted fw-bolder">{{ __('common.phone') }} : {{ $companyProfile->phone }}</p>
                    <p class="text-muted fw-bolder">{{ __('common.email') }} : {{ $companyProfile->email }}</p>
                </div>
            </div>
            <div class="col-md-6 report_logo">
                @if ($companyProfile->logo == null)
                    <span class="logo-sm">
                        <img src="{{ asset('assets/images/logo-dark.png') }}" alt="" class="img-fluid">
                    </span>
                @else
                    <span class="logo-sm">
                        <img src="{{ $companyProfile->logo }}" alt="" class=" img-fluid">
                    </span>
                @endif
            </div>


        </div>

    </div>
</div>
<div class="row mt-3">
    <div class="col-sm-6 ms-4">
        <address>
            <strong>{{ __('reports.purchased_address') }} :</strong><br>
            @if ($moduleStatus->ecom_status)
                {{ $order->firstname ?? 'NA' . ',' }}<br>
                {{ $order->shipping_address_1 ?? 'NA' }}<br>
                {{ $order->shipping_city . ' ' . $order->shipping_postcode . ',' }}<br>
                {{ __('common.mobile') }}{{ ': ' . $order->telephone }}
            @else
                {{ $order->address->name ?? 'NA' . ',' }}<br>
                {{ $order->address->address ?? 'NA' }}<br>
                {{ $order->address->city . ' ' . $order->address->zip . ',' }}<br>
                {{ __('common.mobile') }}{{ ': ' . $order->address->mobile }}
            @endif
        </address>
    </div>

</div>

<div class="card-body">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>{{ __('common.item') }}</th>
                <th>{{ __('Party.quantity') }}</th>
                <th>{{ __('reports.unit_cost') }}</th>
                <th>{{ __('common.total') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($order->orderDetails as $orders)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $moduleStatus->ecom_status ? $orders->name : $orders->package->name }}</td>
                    <td>{{ $orders->quantity ?? 0 }}</td>
                    <td>{{ $currency . ' ' . formatCurrency($moduleStatus->ecom_status ? $orders->price + $orders->tax : $orders->amount) }}
                    </td>
                    <td>{{ $currency . ' ' . formatCurrency($moduleStatus->ecom_status ? ($orders->price + $orders->tax) * $orders->quantity : $orders->quantity * $orders->amount) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td>{{ __('common.no_data') }}</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td></td>
                <td colspan="3" class="fw-bolder">{{ __('common.total') }}</td>
                @php
                    if ($moduleStatus->ecom_status) {
                        $total = $order->total;
                    } else {
                        $total = $order->orderDetails->sum('amount');
                    }
                @endphp
                <td class="fw-bolder">{{ $currency . ' ' . formatCurrency($total ?? 0) }}
                </td>
            </tr>
        </tfoot>
    </table>
</div>
