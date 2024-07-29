@extends('layouts.app')
@section('content')
    <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 font-size-18">{{__('cart.product_details')}}</h4>
                </div>
            </div>
    </div>
    <p class="bx bx-error-circle"> {{ __('ticket.note_add_on_module') }}  </p>

<div class="row">

<div class="col-lg-12">

    <div class="row mb-3">
        <div class="col-xl-4 col-sm-6">
            <div class="card" style="height:95%">
              <div class="card-body">
                <img src="{{asset('assets/images/product/repurchase.png')}}" alt="" class="img-fluid mx-auto d-block">
              </div>
            </div>
        </div>
        <div class="col-xl-8">
        <div class="card" style="height:95%">
              <div class="card-body">
            <div class="mt-4 mt-xl-3">
                <h2 style="color:#000" class="head_prddtl_pg mt-1 mb-3">{{$package->name}}</h2>
                <h4 class="mt-1 mb-3">{{$package->product_id}}</h4>
                <h5 class="mb-4">Price : <span > {{$currency}} {{formatCurrency(number_format((float)$package->price, 2, '.', ''))}}</span> </h5>
                <h5 class="mb-4">PV : <span >{{$package->bv_value}}</span> </h5>
            </div>


        <div class="mt-2">
        <h5 class="mb-3">{{__('cart.specifications')}} :</h5>

        <div class="table-responsive">
            <table class="table_cart mb-0 table-bordered">
                <tbody>
                    <tr>
                        <th scope="row" style="width: 200px;">{{__('cart.category')}}</th>
                        <td>{{$package->category->name}}</td>
                    </tr>
                    <tr><input type="hidden" name="package_id" id="package_id" value="{{$package->id}}">
                        <th scope="row" style="width: 200px;">{{__('cart.quantity')}}</th>
                        <td><div class="me-3" style="width: 120px;"  ><input id="quantity{{$package->id}}" type="text" @if($package->quantity==0) value="1" @else value="{{$package->quantity}}" @endif class="qty-update"  name="demo_vertical"></div></td>
                    </tr>
                    <tr>
                        <th scope="row" style="width: 200px;">{{__('cart.sub_total')}}</th>
                        <td id="total_price{{$package->id}}">
                            @if(isset($product->packageDetails->price))
                            {{$currency}}  {{formatCurrency(number_format((float)($product->quantity*$product->packageDetails->price), 2, '.', ''))}}
                            @else
                            0.00
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th scope="row" style="width: 200px;">{{__('cart.description')}}</th>
                        <td>@if(isset($package->description))<textarea disabled name="description">{!!$package->description!!}</textarea>@endif</td>
                    </tr>
                </tbody>
            </table>
            <div class="text-left">
                <button type="button" class="btn btn-info waves-effect qty-update  mt-2 waves-light me-1 ">
                    {{__('common.update')}}
                </button>
                <a href="{{route('products.view')}}"><button type="button" class="btn btn-success waves-effect  mt-2 waves-light">
                    <i class="bx bx-shopping-bag me-2"></i>{{__('common.cancel')}}
                </button></a>
            </div>
        </div>
    </div>
    <!-- end Specifications -->

    </div>
    </div>
    </div>

    </div>
    <!-- end row -->



</div>
<!-- end card -->
</div>
<!-- end row -->
@endsection
@push('scripts')
<script>
$(".qty-update").click(function(e){
    e.preventDefault();
    var package_id = $("#package_id").val();
    var quantity = $("#quantity"+package_id).val();

    $.ajax({
    type:'POST',
    url:"{{ route('cart.update') }}",
    data:{quantity:quantity, package_id:package_id},
    success:function(data){
        if($.isEmptyObject(data.error)){
            notifySuccess(data.success);
            $("#total_price"+package_id).html(data.total_price);
        }else{
            notifyError(data.error);
        }
    }
    });

});
</script>
@endpush
