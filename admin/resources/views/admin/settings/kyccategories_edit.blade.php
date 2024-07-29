@extends('layouts.app')
@section('content')
    <div class="container mt-5">
        @include('admin.settings.links')
        <form action="{{route('kyc_category_update',$KycCategory[0]['id'])}}" method="post" class="mt-3">
            @csrf
            <div class="form-group">
                <label>Edit Your Category</label>
                <input type="text" name="category" value="{{$KycCategory[0]['category']}}" class="form-control"
                  min="0">

            </div>
           <div class="form-group">
                <button type="submit" class="btn btn-primary">{{ __('common.update') }}</button>
            </div>
        </form>
    </div>
@endsection
