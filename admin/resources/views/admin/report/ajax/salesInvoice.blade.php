<div class="row">
    <div class="col-md-6">
        <div class="page-title-right">
        @php
                $invoiceId = $moduleStatus->ecom_status ? $data->order_id : $data->salesOrder?->invoice_no;
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

        #salesInvoice,
        #salesInvoice * {
            visibility: visible;
        }

        #salesInvoice {
            /* Additional styles for the modal container (if needed) */
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
                        <img src="{{ $companyProfile->logo }}" alt=""
                            class=" img-fluid">
                    </span>
                @endif
            </div>


        </div>

    </div>

    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>{{ __('common.memberName') }}</th>
                    <th>{{ __('common.package') }}</th>
                    <th>{{ __('common.package_amount') }}</th>
                    <th>{{ __('common.payment_method') }}</th>
                    @if (!$moduleStatus->ecom_status)
                    <th>{{ __('common.registration_fee') }}</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                    @if ($moduleStatus->ecom_status)
                    {{ $data->firstname . ' ' . $data->lastname . '(' . $data->user->username . ')' }}
                        @forelse ($data->orderDetails as $orders)
                            <td>{{ $orders->model  }}</td>
                            <td>{{ $currency . ' ' . formatCurrency( $orders->price + $orders->tax) }}</td>
                            <td>{{ $data->payment_method ?? 'NA'  }}</td>
                            @empty
                            <tr>
                                <td>{{ __('common.no_data') }}</td>
                            </tr>
                        @endforelse
                    @else
                    
                    {{ $data->userDetail?->name . ' ' . $data->userDetail?->second_name . '(' . $data->username . ')' }}
                    <td>{{ $data->package->name }}</td>
                    <td>{{ $currency . ' ' . formatCurrency($data->salesOrder->amount ?? 0) }}</td>
                    <td>{{ $data->salesOrder->paymentMethod->name ?? 'NA' }}</td>
                    <td>{{ $currency . ' ' . formatCurrency($data->salesOrder?->reg_amount) }}</td>

                    @endif
            </tbody>
        </table>
    </div>
</div>
