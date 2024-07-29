@extends('layouts.app')
@section('title', 'AddNews')
@section('content')
    <div class="container">
        <div class="card">
            <div class="card-body">
                <h4>{{ __('tools.create_news') }}</h4>
                <form action="{{ route('news.addnews') }}" method="post" class="mt-3 needs-validation"
                    enctype="multipart/form-data" novalidate>
                    @csrf
                    <div class="form-group">
                        <label>{{ __('tools.title') }} <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                            value="{{ old('title') }}" min="0" required>
                        @error('title')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>{{ __('common.image') }} <span class="text-danger"></span></label>
                        <input type="file" name="image" class="form-control @error('image') is-invalid @enderror"
                            required>
                        @error('image')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="form-group ">
                            <label>{{ __('common.description') }} <span class="text-danger">*</span></label><br>
                            <textarea class="form-control @error('description') is-invalid @enderror" name="description" row="4"
                                cols="90" required>{{ old('description') }}</textarea>
                            @error('description')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror

                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">{{ __('common.submit') }}</button>
                        <a href="{{ route('news') }}" class="btn btn-danger">{{ __('common.back') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
