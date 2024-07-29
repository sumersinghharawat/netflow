@extends('layouts.app')
@section('content')
    <div class="container">
        <h4>{{ __('tools.update_news') }}</h4>
        <form action="{{ route('news.update', $news->id) }}" method="post" class="mt-3" enctype="multipart/form-data"
            class="needs">
            @csrf
            <div class="form-group">
                <label>{{ __('common.title') }}</label>
                <input type="text" name="title" value="{{ $news->title }}" class="form-control" required>
                @error('title')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label>{{ __('common.image') }}</label>
                <input type="file" name="image" class="form-control">
                <div class="p-2">
                    <img src="{{ $news->image }}" class="img-fluid w-25" alt="404 not found">
                </div>
            </div>
            <div class="form-group">
                <label>{{ __('common.description') }}</label><br>
                <textarea name="description" id="" cols="30" rows="10" class="form-control" required>{{ $news->description }}</textarea>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">{{ __('common.update') }}</button>
                <a href="{{ route('news') }}" class="btn btn-danger">{{ __('common.back') }}</a>
            </div>
        </form>
    </div>
@endsection
