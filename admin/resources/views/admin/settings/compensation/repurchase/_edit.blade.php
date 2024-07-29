@extends('layouts.app')
@section('content')
    <div class="container mt-5">
        <div class="panel panel-default">
            <div class="panel-body">
                <form action="{{ route('rePurchaseUpdate', $package->id) }}" role="form" method="post"
                    accept-charset="utf-8">
                    @csrf

                    <div class="form-group">
                        <label class="">{{__('common.product_id')}}</label>
                        <input type="text" class="form-control" name="title" id="currency_title"
                            value="{{ $package->product_id }}" readonly>
                    </div>
                    <div class="form-group">
                        <label class="">{{__('common.product_name')}}</label>
                        <input type="text" class="form-control" name="product_name" value="{{ $package->name }}">
                    </div>
                    <div class="form-group">
                        <label class="required">{{__('common.product_value')}}</label>
                        <input type="text" class="form-control" name="product_value"
                            value="{{ round($package->price, 0) }}">

                    </div>
                    <div class="form-group">
                        <label class="">{{__('settings.pv')}}</label>
                        <input type="text" class="form-control" name="pair_value" value="{{ $package->pair_value }}">

                    </div>

                    <div class="form-group">
                        <button class="btn btn-sm btn-primary" type="submit">{{__('common.update')}}</button>
                    </div>
                </form>
            </div>
        </div>


    </div>

@endsection
