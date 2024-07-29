@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __('cart.products') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="text-white">
                            <a href="{{ route('cart.view') }}"><button type="button" class="btn btn-primary waves-effect waves-light">{{ __('cart.view_cart') }}</button></a>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    @if (config('mlm.demo_status') == 'yes')
    <p class="bx bx-error-circle"> {{ __('ticket.note_add_on_module') }} </p>
    @endif

    <div class="row">

        <div class="row">
            @forelse ($packages as $item)
                <div class="col-xl-4 col-sm-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="product-img position-relative">
                                @if ($item->image)
                                @else
                                    <img src="{{ asset('assets/images/product/repurchase.png') }}" alt="no image"
                                        class="w-25 img-fluid mx-auto d-block">
                                @endif
                            </div>
                            <div class="mt-4 text-center">
                                <h5 class="mb-3 text-truncate"><a href="javascript: void(0);"
                                        class="text-dark">{{ $item->name }} </a></h5>
                                <h5>{{ $item->category->name }}</h5>

                                <h5 class="my-0"> <b>{{ $currency }}
                                        {{ formatCurrency(number_format((float) $item->price, 2, '.', '')) }}</b></h5>
                                <h5 class="my-0"> <b>{{ __('cart.pv_value') }} :{{ $item->bv_value }}</b></h5>
                                <div class="text-center">
                                    <a href="{{ route('add-to-cart', $item->id) }}"><button type="button"
                                            class="btn btn-primary waves-effect waves-light mt-2 me-1">
                                            <i class="bx bx-cart me-2"></i> {{ __('cart.add_to_cart') }}
                                        </button></a>
                                    <a href="{{ route('product-details', $item->id) }}"><button type="button"
                                            class="btn btn-success waves-effect  mt-2 waves-light me-1">
                                            <i class="bx bx-bullseye me-2"></i>{{ __('cart.more_details') }}
                                        </button></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center">{{ __('cart.no_products') }}</div>
            @endforelse
        </div>
        {{ $packages->links() }}
        <!-- end row -->

    </div>
    <!-- end row -->
@endsection
