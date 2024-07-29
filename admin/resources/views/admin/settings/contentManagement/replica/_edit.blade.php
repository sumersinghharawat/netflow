@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <a href="javascript:history.back()" class="btn m-b-xs btn-sm btn-info btn-addon" style="margin-bottom:10px"><i class="fa fa-backward" style="margin-right:10px"></i>{{ __('common.back') }}</a>
	<div class="card">
		<div class="card-body">
	        <h4>{{ __('replica.replica_stite') }}</h4>
            @if (!$replic_default->isEmpty())
			<form method="post" class="mt-3" action="{{ route('replication.site.update.default') }}" enctype="multipart/form-data">
            @else
			<form method="post" class="mt-3" action="{{ route('replication.site.add.default', ['id' => $id]) }}" enctype="multipart/form-data">
            @endif

				@csrf
				<div class="row">
					<div class="form-group">
						<input type="text" class="form-control" name="language" value="Replica Content (Default)"
							readonly>
					</div>
				</div>
				@forelse ($replic_default as $key => $value)
                    <div class="row">
                        @if($value['key'] != 'plan' && $value['key'] != 'policy' && $value['key'] != 'terms' && $value['key'] != 'about' && $value['key'] != 'why_choose_us' && $value['key'] != 'features')
                        <div class="form-group">
                            {{ __('replica.'.$value['key']) }}
                            <input type="text" class="form-control" name="{{ $value['key'] }}" value="{{ $value['value'] }}">
                            <input type="hidden" class="form-control" name="{{ $value['key'] . '1' }}" value="{{ $value['id'] }}">
                        </div>
                        @else
                        <div class="form-group">
                            {{ $value['key'] }}
                            <textarea class="summernote form-control" name="{{ $value['key'] }}" rows="6">@isset($value['value']){!! $value['value'] !!}@endisset</textarea>
                            <input type="hidden" class="form-control" name="{{ $value['key'] . '1' }}" value="{{ $value['id'] }}">
                        </div>
                        @endif
                    </div>
                @empty
                    <div class="row">
                        <div class="form-group">
                            {{ __('replica.home_title1') }}
                            <input type="text" class="form-control @error('home_title1') is-invalid @enderror" name="home_title1" value="{{ old('home_title1') }}">
                            @error('home_title1')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            {{ __('replica.home_title2') }}
                            <input type="text" class="form-control @error('home_title2') is-invalid @enderror" name="home_title2" value="{{ old('home_title2') }}">
                            @error('home_title2')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            {{ __('replica.plan') }}
                            <textarea class="summernote form-control @error('plan') is-invalid @enderror" name="plan" rows="6">{{ old('plan') }}</textarea>
                            @error('plan')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            {{ __('replica.contact_phone') }}
                            <input type="text" class="form-control @error('contact_phone') is-invalid @enderror" name="contact_phone" value="{{ old('contact_phone') }}">
                            @error('contact_phone')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            {{ __('replica.contact_mail') }}
                            <input type="email" class="form-control @error('contact_mail') is-invalid @enderror" name="contact_mail" value="{{ old('contact_mail') }}">
                            @error('contact_mail')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            {{ __('replica.contact_address') }}
                            <input type="text" class="form-control @error('contact_address') is-invalid @enderror" name="contact_address" value="{{ old('contact_address') }}">
                            @error('contact_address')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            {{ __('replica.policy') }}
                            <textarea class="summernote form-control @error('policy') is-invalid @enderror" name="policy" rows="6">{{ old('policy') }}</textarea>
                            @error('policy')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            {{ __('replica.terms') }}
                            <textarea class="summernote form-control @error('terms') is-invalid @enderror" name="terms" rows="6">{{ old('terms') }}</textarea>
                            @error('terms')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            {{ __('replica.about') }}
                            <textarea class="summernote form-control @error('about') is-invalid @enderror" name="about" rows="6">{{ old('about') }}</textarea>
                            @error('about')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            {{ __('replica.why_choose_us') }}
                            <textarea class="summernote form-control @error('why_choose_us') is-invalid @enderror" name="why_choose_us" rows="6">{{ old('why_choose_us') }}</textarea>
                            @error('why_choose_us')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
				@endforelse
				<div class="form-group">
					<button class="btn btn-primary" type="submit">{{ __('common.update') }}</button>
				</div>
			</form>
            <br>

			
		</div>
	</div>
</div>
@endsection
@push('scripts')
<script src="summernote-bs5.js"></script>
<script>
	$(document).ready(function() {
	    $('.summernote').summernote({
	    	styleWithSpan: false,
	    	toolbar: [
	    	    ['style', ['style','bold', 'italic', 'underline', 'clear']],
	    	    ['font', ['strikethrough', 'superscript', 'subscript']],
	    	    ['fontsize', ['fontsize']],
	    	    ['fontname', ['fontname']],
	    	    ['color', ['color']],
	    	    ['para', ['ul', 'ol', 'paragraph']],
	    	    ['height', ['height']],
	    	    ['table', ['table']],
	    	    ['insert', ['link', 'image','elfinder','hr']],
	    	    ['view', ['fullscreen', 'codeview']],
	    	    ['help', ['help']]
	    	  ],
	    });
	    let buttons = $('.note-editor button[data-toggle="dropdown"]');

	        buttons.each((key, value)=>{
	          $(value).on('click', function(e){
	            $(this).attr('data-bs-toggle', 'dropdown')
	            console.log()
	            ata('id', 'dropdownMenu');
	          })
	        })

	});
</script>
@endpush
@push('page-style')
<link href="summernote-bs5.css" rel="stylesheet">
@endpush
