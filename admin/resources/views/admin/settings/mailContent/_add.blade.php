@extends('layouts.app')
@section('title', 'Mail Content Edit')
@section('content')
    <div class="card">
        <div class="card-body">
            {{-- <h4>{{ $id }}</h4> --}}
            <div class="panel-body  mt-3">
                <form method="post" action="{{ route('mailcontent-addnew') }}">
                    @csrf
                    <div class="form-group">
                        <label>{{ __('mail.subject') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('subject') is-invalid @enderror" name="subject">
                        @error('subject')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <input type="hidden" name="lang_id" value="{{ $language_id }}"/>
                    <input type="hidden" name="mail_type" value="{{ $id }}"/>
                    <div class="form-group">
                        <label>{{ __('common.content') }} <span class="text-danger">*</span></label>
                        <textarea class="summernote form-control" name="content" rows="6">
                            
                        </textarea>
                        @error('content')
                            <div class="invalid-feedback d-block">
                            </div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary" type="submit">{{ __('common.add') }}</button>
                        <a href="{{ route('mailcontent') }}" class="btn btn-danger">{{ __('common.back') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="tab-pane" role="tabpanel">
                <table class="table">
                    <tr>
                        <th>PlaceHolders</th>
                        <th>Name</th>
                    </tr>
                <tbody>
                    @foreach ($placeholders as $placeholder)
                    <tr>
                        <td>&#123;&#123;{{$placeholder->placeholder}}&#125;&#125;</td>
                        <td>{{$placeholder->name}}</td>
                    </tr>
                    @endforeach
                </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection
