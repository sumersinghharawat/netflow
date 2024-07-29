@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="card">
            <div class="card-body">
                <h4>{{ __('tools.add_new_material') }}</h4>
                <form action="{{ route('material.addnew') }}" method="post" class="needs-validation" novalidate
                    enctype="multipart/form-data">

                    @csrf
                    <div class="form-group">
                        <label>{{ __('tools.file_category') }} <span class="text-danger">*</span></label>
                        <select name="category" id="" class="form-select" required>
                            @foreach ($category as $value)
                                <option value="{{ $value->id }}">{{ $value->type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>{{ __('tools.file_title') }} <span class="text-danger">*</span></label>
                        <input type="text" name="title" value="{{ old('title') }}" class="form-control" required>
                        @error('title')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <div class="valid-feedback">
                            Looks good!
                        </div>
                        <div class="invalid-feedback">
                            {{ __('tools.this_field_is_required') }}
                        </div>
                    </div>
                    <div class="form-group">
                        <label>{{ __('tools.file') }} <span class="text-danger">*</span></label>
                        <input type="file" name="file" class="form-control" required>
                        @error('file')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <div class="valid-feedback">
                            Looks good!
                        </div>
                        <div class="invalid-feedback">
                            {{ __('tools.this_field_is_required') }}
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group">
                            <label>{{ __('common.description') }} <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="description" row="4" cols="90" required>{{ old('description') }}</textarea>
                            @error('description')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            <div class="valid-feedback">
                                Looks good!
                            </div>
                            <div class="invalid-feedback">
                                {{ __('tools.this_field_is_required') }}
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">{{ __('common.upload') }}</button>
                        <a href="{{ route('material') }}" class="btn btn-danger">{{ __('common.back') }}</a>

                    </div>
                </form>
            </div>

        </div>
    </div>
@endsection
