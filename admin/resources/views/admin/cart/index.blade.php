@extends('layouts.app')
@section('content')
    <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 font-size-18">{{__('cart.shopping_cart')}}</h4>
                </div>
            </div>
    </div>
    @if (config('mlm.demo_status') == 'yes')
    <p class="bx bx-error-circle">{{ __('ticket.note_add_on_module') }}</p>
    @endif
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0 table-nowrap">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{__('cart.product')}}</th>
                                        <th>{{__('cart.price')}}</th>
                                        <th>{{__('cart.quantity')}}</th>
                                        <th colspan="2">{{__('cart.sub_total')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($products as $item)
                                    <tr>
                                        <td>
                                            <img src="{{asset('assets/images/product/repurchase.png')}}" alt="product-img"
                                                title="product-img" class="avatar-md" />
                                        </td>
                                        <input type="hidden" name="package_id" id="package_id" value="{{$item->package_id}}">
                                        <td>
                                            {{$currency}} {{formatCurrency(number_format((float)$item->packageDetails->price, 2, '.', ''))}}
                                        </td>
                                        <td>
                                            <div class="me-3" style="width: 120px;">
                                                <input id="quantity{{$item->package_id}}" type="text" class="qty-update" value="0{{$item->quantity}}" name="demo_vertical">
                                            </div>
                                        </td>
                                        <td id="total_price{{$item->package_id}}">
                                            {{$currency}} {{formatCurrency(number_format((float)($item->quantity*$item->packageDetails->price), 2, '.', ''))}}
                                        </td>
                                        <td>
                                            <button type="button" id="cart-delete{{$item->package_id}}" class="btn btn-danger waves-effect mt-2 me-1 waves-light cart-delete">
                                                {{__('common.delete')}}
                                            </button>
                                        </td>
                                    </tr>
                                    {{-- <tr>
                                        <td>{{ __('common.grand_total') }} : </td>
                                        <td>$ </td>
                                    </tr> --}}
                                    @empty
                                    <tr>
                                        <td colspan="100%">
                                            <div class="nodata_view">
                                                <img src="{{asset('assets/images/nodata-icon.png')}}" alt="">
                                                <span>{{ __('common.no_data') }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse

                                </tbody>
                            </table>
                        </div>
                        <div class="row mt-4">
                            <div class="col-sm-6">
                                <a href="{{route('products.view')}}" class="btn btn-secondary">
                                    <i class="mdi mdi-arrow-left me-1"></i> {{__('cart.continue_shopping')}} </a>
                            </div> <!-- end col -->
                            <div class="col-sm-6">
                                <div class="text-sm-end mt-2 mt-sm-0">
                                    <a href="{{route('cart.checkout')}}" class="btn btn-success" data-token="{{ csrf_token() }}">
                                        <i class="mdi mdi-cart-arrow-right me-1"></i> {{__('cart.checkout')}} </a>
                                </div>
                            </div> <!-- end col -->
                        </div> <!-- end row-->
                    </div>
                </div>
            </div>
        </div>
        <!-- end row -->

    </div> <!-- container-fluid -->
</div>
<!-- End Page-content -->
<!-- end row -->
@endsection
@push('scripts')
<script>
    $(".qty-update").change(function(e){
        e.preventDefault();
        var package_id = $(this).closest("tr").find("#package_id").val();
        var quantity = $("#quantity"+package_id).val();

        $.ajax({
        type:'POST',
        url:"{{ route('cart.update') }}",
        data:{quantity:quantity, package_id:package_id},
        success:function(data){
            if($.isEmptyObject(data.error)){
                //notifySuccess(data.success);
                $("#total_price"+package_id).html(data.total_price);
            }else{
                notifyError(data.error);
            }
        }
        });

    });
    $(".cart-delete").click(function(e){
        e.preventDefault();
        var package_id = $(this).closest("tr").find("#package_id").val();
        var token = $(this).data("token");
        $.ajax(
        {
            url: "cart/delete/"+package_id,
            type: 'GET',
            dataType: "JSON",
            data: {
                "package_id": package_id,
                "_method": 'DELETE',
                "_token": token,
            },
            success: function (data)
            {
                notifySuccess(data.success);
                $("#cart-delete"+package_id).closest("tr").hide();
            }
        });

        notifyError(data.error);
    });
</script>
@endpush
