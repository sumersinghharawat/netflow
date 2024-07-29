@extends('layouts.app')
@section('content')
@if(!empty($success))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="mdi mdi-check-all me-2"></i>
    {{ $success }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
    <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 font-size-18">{{__('cart.invoice')}}</h4>
                </div>
            </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="invoice-title">
                        <h4 class="float-end font-size-16">Order # {{$order->invoice_no}}</h4>
                        <div class="mb-4">
                            <img src="assets/images/logo-dark.png" alt="logo" height="20"/>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-6">
                            <address>
                                <strong>{{ __('common.company') }}:</strong><br>
                                {{ __('common.your_logo') }}<br>
                                {{ __('common.company_name') }}<br>
                                {{ __('common.company_profile') }}<br>
                                {{ __('common.phone') }}: 9999999999<br>
                                {{ __('common.email') }}: companyname@emil.com
                            </address>
                        </div>
                        <div class="col-sm-6 text-sm-end">
                            <address class="mt-2 mt-sm-0">
                                <strong>{{__('cart.shipped_to')}}:</strong><br>
                                {{$address->name}}<br>
                                {{$address->address}}<br>
                                {{$address->zip}}<br>
                                {{$address->city}}<br>
                                {{$address->mobile}}
                            </address>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 mt-3">
                            <address>
                                <strong>{{__('cart.payment_method')}}</strong><br>
                                @if($paymentMethod->name == 'Free Joining'){{ __('cart.cash_on_delivery') }}@else{{ $paymentMethod->name }}@endif<br>
                            </address>
                        </div>
                        <div class="col-sm-6 mt-3 text-sm-end">
                            <address>
                                <strong>{{ __('cart.order_date') }}:</strong><br>
                                {{date('d M Y', strtotime($order->order_date))}}<br><br>
                            </address>
                        </div>
                    </div>
                    <div class="py-2 mt-3">
                        <h3 class="font-size-15 fw-bold">{{__('cart.order_summary')}}</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-nowrap">
                            <thead>
                                <tr>
                                    <th style="width: 70px;">{{__('cart.no')}}</th>
                                    <th>{{__('cart.item')}}</th>
                                    <th>{{__('cart.quantity')}}</th>
                                    <th class="text-end">{{__('cart.price')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $i = 1; @endphp
                                @forelse ($cart_items as $item)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>{{$item->packageDetails->name}}</td>
                                    <td>{{$item->quantity}}</td>
                                    <td class="text-end">{{$currency}} {{formatCurrency($item->quantity*$item->packageDetails->price)}}</td>
                                </tr>
                                @empty
                                <tr><td>{{__('cart.no_products')}}</td></tr>
                                @endforelse
                                <tr>
                                    <td colspan="2" class="border-0 text-end">
                                        <strong>{{__('cart.total')}}</strong></td>
                                    <td class="border-0 text-end"><h4 class="m-0">{{$currency}} {{formatCurrency($total)}}</h4></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="d-print-none">
                        <div class="float-end">
                            <a href="javascript:window.print()" class="btn btn-success waves-effect waves-light me-1"><i class="fa fa-print"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end row -->

</div> <!-- container-fluid -->
</div>

<!-- end row -->
@endsection
@push('scripts')

@endpush
