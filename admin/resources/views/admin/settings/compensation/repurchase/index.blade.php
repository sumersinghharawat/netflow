@extends('layouts.app')
@section('content')
    <div class="container-fluid">

    <div class="card">
        <div class="card-body">
        @include('admin.settings.inc.links')

        <table id="repurchase_package_list_table" class="display table  m-b-none">
            <thead>
              <tr>

                  <th>{{__('settings.id')}}</th>
                  <th>{{__('common.product_image')}}</th>
                  <th>{{__('common.package_name')}}</th>
                  <th>{{__('common.category')}}</th>
                  <th>{{__('common.amount')}}</th>
                  @if($data['pv_visible']=='yes')
                  <th>{{__('settings.pv')}}</th>
                  @endif
                  @if($data['bv_visible']=='yes')
                  <th>{{__('common.bv')}}</th>
                  @endif
                <th>{{__('common.action')}}</th>
              </tr>
            </thead>
            <tbody>
                @foreach($data['repurchase'] as $value)
                <tr>

                    <td>{{$value->product_id}}</td>
                    <td>image.jpg</td>
                    <td>{{$value->name}}</td>
                    <td>{{$value->catogery_id}}</td>
                    <td>{{$value->price}}</td>
                    @if($data['pv_visible']=='yes')
                    <th>{{$value->pair_value}}</th>
                    @endif
                    @if($data['bv_visible']=='yes')
                    <th>{{$value->bv_value}}</th>
                    @endif
                    <td>
                        <form action="{{ route('rePurchaseDis', $value->id) }}" method="post">
                            @csrf
                            <a href="{{ route('rePurchaseEdit', $value->id) }}"> {{__('common.edit')}}</a>
                            <button type="submit" onclick="confirm('Are you sure?')"
                                class="btn ms-3 {{ $value->active == 'yes' ? 'btn-danger' : 'btn-success' }}">
                                {{ $value->active == 'yes' ? 'disable' : 'enable' }}</button>
                        </form>
                    </td>

                </tr>
                @endforeach
            </tbody>
         </table>
         <a href="{{ route('rePurchasecategory') }}">{{__('settings.add_categories')}}</a><br>
         <a href="{{ route('rePurchaseAdd') }}">{{__('settings.add_purchase_package')}}</a>

    </div>
    </div>
    </div>
@endsection
