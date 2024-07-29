@extends('layouts.app')
@section('content')
    <div class="container mt-5">
        @include('admin.settings.links')
        <form action="{{route('kyc_category_add')}}" method="post" class="mt-3">
            @csrf
            <div class="form-group">
                <label>{{ __('settings.add_your_category') }}</label>
                <input type="text" name="category" class="form-control"
                    value="" min="0">
            </div>
           <div class="form-group">
                <button type="submit" class="btn btn-primary">{{ __('common.add') }}</button>
            </div>
        </form>
    </div>
@endsection
