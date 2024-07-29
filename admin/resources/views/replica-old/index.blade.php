@extends('layouts.replica')
@section('content')

<section class="banner_section" style="background-image: url('{{ asset($banner->image) }}');" id="home">
	<div class="container">
		<div class="banner_contant_sec">
			@isset($data['home_title1'])
			<h3>{{ $data['home_title1'] }}</h3>
			@endisset
			@isset($data['home_title2'])
			<h1>{{ $data['home_title2'] }}</h1>
			@endisset
			<a target="_blank" class="banner_button" href="{{ route('replica.registerForm', $user->username) }}">{{ __('replica.join_us') }}</a>
		</div>
	</div>
</section>
<section class="plan_cnt_sec" id="plan">
	<div class="container">
		@isset($data['plan'])
		<table>
			<tr>
				<td>
					{!! $data['plan'] !!}
				</td>
			</tr>
		</table>
		@endisset
	</div>
</section>
<section class="about_section" id="about">
	<div class="container">
		<div class="row">
			@isset($data['about'])
			<table>
				<tr>
					<td>
						{!! $data['about'] !!}
					</td>
				</tr>
			</table>
			@endisset
			<div class="col-md-6">
				<div class="about_section_img"><img src="{{ asset($banner->image) }}" alt=""></div>
			</div>
		</div>
	</div>
</section>
<section class="contact_section" id="contact">
	<div class="container">
		<div class="row">
            @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ __('replica.will_contact') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if(session('errors '))
            <div class="alert alert-false alert-dismissible fade show" role="alert">
                {{ __('replica.please_checkout_the_field') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ __('replica.please_checkout_the_field') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
			<div class="col-md-6">
				<div class="contact_section_head">
					<h2>{{ __('replica.contact_us') }}</h2>
					<span>{{ __('replica.get_in_touch') }}</span>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="contact_bx_phone">
							<div class="contact_bx_phone_ico"><i class="fa fa-solid fa-phone"></i></div>
							<span>{{ __('replica.call_us') }}</span>
							@isset($data['contact_phone'])
							<strong>{{ $data['contact_phone'] }}</strong>
							@endisset
						</div>
					</div>
					<div class="col-md-6">
						<div class="contact_bx_phone">
							<div class="contact_bx_phone_ico"><i class="fa fa-solid fa-envelope"></i></div>
							<span>{{ __('replica.mail_now') }}</span>
							@isset($data['contact_mail'])
							<strong>{{ $data['contact_mail'] }}</strong>
							@endisset
						</div>
					</div>
					<div class="col-md-12">
						<div class="contact_bx_phone">
							<div class="contact_bx_phone_ico"><i class="fa fa-solid fa-map"></i></div>
							<span>{{ __('replica.address') }}</span>
							@isset($data['contact_address'])
							<strong>{{ $data['contact_address'] }}</strong>
							@endisset
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="form_sec_box">
					<h4>{{ __('replica.fill_out') }}</h4>
					<form action="{{ route('replica.contact') }}" method="POST" enctype="multipart/form-data">

						@csrf
						<input type="hidden" name="user" value="{{ $user->username }}" id="user">
                        <input type="hidden" name="url" value="#contact" id="url">
						<div class="form_sec_box_group">
							<div class="row">
								<div class="col-md-6">
									<div class="group">
										<label for="">{{ __('replica.name') }}</label>
										<input class="form-control @error('name')is-invalid @enderror" type="text" placeholder="Name" name="name" value="{{ old('name') }}">
										@error('name')
										{{ $message }}
										@enderror
									</div>
								</div>
								<div class="col-md-6">
									<div class="group">
										<label for="">{{ __('replica.email') }}</label>
										<input class="form-control @error('Email')is-invalid @enderror" type="text" placeholder="Email" name="Email" value="{{ old('Email') }}">
										@error('Email')
										{{ $message }}
										@enderror
									</div>
								</div>
								<div class="col-md-6">
									<div class="group">
										<label for="">{{ __('replica.phone') }}</label>
										<input class="form-control @error('Phone')is-invalid @enderror" type="text" placeholder="Phone" name="Phone" value="{{ old('Phone') }}">
										@error('Phone')
										{{ $message }}
										@enderror
									</div>
								</div>
								<div class="col-md-6">
									<div class="group">
										<label for="">{{ __('replica.address') }}</label>
										<input class="form-control @error('Address')is-invalid @enderror" type="text" placeholder="Address" name="Address" value="{{ old('Address') }}">
										@error('Address')
										{{ $message }}
										@enderror
									</div>
								</div>
								<div class="col-md-12">
									<div class="group">
										<label for="">{{ __('replica.message') }}</label>
										<textarea name="Message" class="form-control @error('Message')is-invalid @enderror"  id="" cols="30" rows="5">
										{{ old('Message') }}
										</textarea>
										@error('Message')
										{{ $message }}
										@enderror
									</div>
								</div>
								<div class="col-md-12">
									<button type="submit" class="banner_button">{{ __('replica.sent') }}</button>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection
