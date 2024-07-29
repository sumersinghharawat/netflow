<!DOCTYPE html>
<html lang="en">
<head>
	<!-- Start Meta -->
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Replica Website</title>
	<!-- Favicons -->
	<link rel="icon" type="image/png') }}" href="{{ asset('assets/replica/img/favicon.png') }}') }}">
	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="{{ asset('assets/replica/css/bootstrap.min.css') }}">
	<!-- Font Awesome CSS -->
	<link rel="stylesheet" href="{{ asset('assets/replica/css/all.css') }}">
	<!-- Animate CSS -->
	<link rel="stylesheet" href="{{ asset('assets/replica/css/animate.css') }}">
	<!-- Flaticon -->
	<link rel="stylesheet" href="{{ asset('assets/replica/font/flaticon.css') }}">	
	<!-- Swiper Bundle CSS -->
	<link rel="stylesheet" href="{{ asset('assets/replica/css/swiper-bundle.min.css') }}">
	<!-- Magnific Popup CSS -->
	<link rel="stylesheet" href="{{ asset('assets/replica/css/magnific-popup.css') }}">
	<!-- Mean Menu CSS -->
	<link rel="stylesheet" href="{{ asset('assets/replica/css/meanmenu.min.css') }}">
	<!-- Custom CSS -->
	<link rel="stylesheet" href="{{ asset('assets/replica/sass/style.css') }}"> 
</head>

<body>
	<!-- Preloader Start -->
	<div class="theme-loader">
		<div class="spinner">
			<div class="spinner-bounce one"></div>
			<div class="spinner-bounce two"></div>
			<div class="spinner-bounce three"></div>
		</div>
	</div>
	<!-- Preloader End -->
	<!-- Top Bar Area Start -->
	<div class="top__bar-four">
		<div class="custom__container">
			<div class="row">
				<div class="col-lg-8">
					<div class="top__bar-four-left lg-t-center">
						<ul>
							<li><a href="https://www.google.com/maps" target="_blank"><i class="fas fa-map-marker-alt"></i>Location : Location, Area</a></li>
							<li><a href="#"><i class="fas fa-envelope"></i>{{ $user->email }}</a></li>
						</ul>
					</div>
				</div>
				<div class="col-lg-4">
					<div class="top__bar-four-right">
						<h6>Follow Us :</h6>
						<div class="top__bar-four-right-social">
							<ul>
								<li><a href="https://www.facebook.com/" target="_blank"><i class="fab fa-facebook-f"></i><span>Facebook</span></a></li>
								<li><a href="https://www.instagram.com/" target="_blank"><i class="fab fa-instagram"></i><span>Instagram</span></a></li>
								<li><a href="https://twitter.com/" target="_blank"><i class="fab fa-twitter"></i><span>Twitter</span></a></li>
								<li><a href="https://dribbble.com/" target="_blank"><i class="fab fa-dribbble"></i><span>Dribbble</span></a></li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Top Bar Area End -->
	<!-- Header Area Start -->
	<div class="header__area one header__sticky">
        <div class="container custom__container">
            <div class="header__area-menubar p-relative">
                <div class="header__area-menubar-left">
                    <div class="header__area-menubar-left-logo">
						
						<!-- Logo section -->

                        <!-- <a href="#home"><img class="dark-n" src="{{ asset('assets/replica/img/logo.png') }}" alt="Logo-image"><img class="light-n" src="{{ asset('assets/replica/img/logo-1.png') }}" alt="Logo-image"></a> --> 
                    </div>
                </div>
				<div class="header__area-menubar-center">
                    <div class="header__area-menubar-center-menu four menu-responsive">						
                        <ul id="mobilemenu">
							<li><a href="#home">{{ __('replica.home') }}</a></li>
							<li><a href="#aboutus">{{ __('replica.about') }}</a></li>
							<li><a href="#services">{{ __('replica.plan') }}</a></li>
							<li><a href="#contact">{{ __('replica.contact') }}</a></li>
                        </ul>
                    </div>
				</div>
                <div class="header__area-menubar-right">
					<div class="header__area-menubar-right-responsive-menu menu__bar two">
						<i class="flaticon-dots-menu"></i>
					</div>
					
					<div class="header__area-menubar-right-contact">
						<div class="header__area-menubar-right-contact-icon">
							<i class="fal fa-envelope-open-text"></i>
						</div>
						<div class="header__area-menubar-right-contact-info">
							<span>Message</span>
							<h6><a href="#">{{ $data['contact_mail'] ?? 'support@companyname.com' }}</a></h6>
						</div>
					</div>
					<div class="header__area-menubar-right-btn four">						
						<a class="btn-one" href="{{ route('replica.registerForm', $user->username) }}">{{ __('replica.join_us') }} </a>
					</div>
                </div>
            </div>
			<div class="menu__bar-popup four">
				<div class="menu__bar-popup-close"><i class="fal fa-times"></i></div>
				<div class="menu__bar-popup-left">
					<div class="menu__bar-popup-left-logo">
						<a href="index.html"><img src="{{ asset('assets/replica/img/logo.png') }}" alt="logo-image"></a>
						<div class="responsive-menu"></div>
					</div>
					<div class="menu__bar-popup-left-social">
						<h6>Follow Us</h6>
						<ul>
							<li><a href="https://www.facebook.com/" target="_blank"><i class="fab fa-facebook-f"></i></a></li>
							<li><a href="https://www.instagram.com/" target="_blank"><i class="fab fa-instagram"></i></a></li>
							<li><a href="https://twitter.com/" target="_blank"><i class="fab fa-twitter"></i></a></li>
							<li><a href="https://dribbble.com/" target="_blank"><i class="fab fa-dribbble"></i></a></li>							
							<li><a href="https://www.behance.net/" target="_blank"><i class="fab fa-behance"></i></a></li>
							<li><a href="https://www.linkedin.com/" target="_blank"><i class="fab fa-linkedin-in"></i></a></li>
							<li><a href="https://www.youtube.com/" target="_blank"><i class="fab fa-youtube"></i></a></li>
						</ul>						
					</div>
				</div>
					
			</div>
        </div>
    </div>    
	<!-- Header Area End -->
	<!-- Banner Area Start -->
	<div class="banner__one swiper banner-slider" id="home">
		<div class="swiper-wrapper">
			<div class="swiper-slide">
				<div class="banner__one-image dark-n" data-background="{{ $banner[0]->image }}"></div>
				<div class="banner__one-image light-n" data-background="{{ $banner[0]->image }}"></div>
				<div class="container">
					<div class="row">
						<div class="col-xl-12">
							<div class="banner__one-content">
								<span data-animation="fadeInUp" data-delay=".3s">{{ $data['home_title1'] }}</span>
								<h1 data-animation="fadeInUp" data-delay=".7s">{{ $data['home_title2'] }}</h1>
								<div class="banner__one-content-button" data-animation="fadeInUp" data-delay="1s">
									<a class="btn-one" href="#aboutus">{{ __('replica.discover_more')}}</a>
                                   
								</div>
								<img class="banner__one-shape-four" src="{{ asset('assets/replica/img/shape/banner-6.png') }}" alt="banner-shape">
							</div>
							<img class="banner__one-shape-two" src="{{ asset('assets/replica/img/shape/banner-5.png') }}"  data-animation="fadeInUpBig" data-delay="2s" alt="banner-shape">
							<img class="banner__one-shape-three" src="{{ asset('assets/replica/img/shape/banner-1.png') }}" data-animation="fadeInRightBig" data-delay="1.5s" alt="banner-shape">
						</div>
					</div>
				</div>
			</div>
			@isset($banner[1]->image)
			<div class="swiper-slide">
				<div class="banner__one-image dark-n" data-background="{{ $banner[1]->image }}"></div>
				<div class="banner__one-image light-n" data-background="{{ $banner[1]->image }}"></div>
				<div class="container">
					<div class="row">
						<div class="col-xl-12">
							<div class="banner__one-content">
								<span data-animation="fadeInUp" data-delay=".3s">{{ $data['home_title1'] }}</span>
								<h1 data-animation="fadeInUp" data-delay=".7s">{{ $data['home_title2'] }}</h1>
								<div class="banner__one-content-button" data-animation="fadeInUp" data-delay="1s">
									<a class="btn-one" href="#aboutus">{{ __('replica.discover_more')}}</a>
                                   
								</div>
								<img class="banner__one-shape-four" src="{{ asset('assets/replica/img/shape/banner-6.png') }}" alt="banner-shape">
							</div>
							<img class="banner__one-shape-two" src="{{ asset('assets/replica/img/shape/banner-5.png') }}"  data-animation="fadeInUpBig" data-delay="2s" alt="banner-shape">
							<img class="banner__one-shape-three" src="{{ asset('assets/replica/img/shape/banner-1.png') }}" data-animation="fadeInRightBig" data-delay="1.5s" alt="banner-shape">
						</div>
					</div>
				</div>
			</div>
			@endisset
		</div>
		<div class="banner__one-arrow">
			<div class="banner__one-arrow-prev swiper-button-prev"><i class="fal fa-long-arrow-left"></i></div>
			<div class="banner__one-arrow-next swiper-button-next"><i class="fal fa-long-arrow-right"></i></div>
		</div>
		<img class="banner__one-shape-one" src="{{ asset('assets/replica/img/shape/banner-7.png') }}" alt="banner-shape">		
	</div>
	<!-- Banner Area End -->
	<!-- Features Area Start -->
    <div class="features">
        <div class="container">
            @isset($data['features'])
				{!! $data['features'] !!}
			@endisset
        </div>
    </div>
    <!-- Features Area End -->
	<!-- About Area Start -->
	<div class="about__two section-padding" id="aboutus">
		<img class="about__two-shape-one" src="{{ asset('assets/replica/img/shape/about-1.png') }}" alt="about-shape">
		<img class="about__two-shape-two dark-n" src="{{ asset('assets/replica/img/shape/about-2.png') }}" alt="about-shape">
		
				@isset($data['about'])
			
					{!! $data['about'] !!}
					
				@endisset
		
	</div>
	<!-- About Area End -->
	<!--Product Section-->
		<!-- <section class="product-list-section">
			<div class="container">
				<h2>Our Products</h2>
				<div class="product-list">
					<div class="row">
						<div class="col-md-4">
							<div class="product-list-box">
								<div class="product-list-box-img"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section> -->
	<!--Product Section End-->
	<!-- Services Area Start -->
	<div class="services__one section-padding" id="services">
		<div class="shape-slide">
			<div class="sliders scroll">
				<img src="{{ asset('assets/replica/img/shape/services-1.png') }}" alt="service-shape">
			</div>
			<div class="sliders scroll">
			  <img src="{{ asset('assets/replica/img/shape/services-1.png') }}" alt="service-shape">
			</div>
		</div>
		<div class="container">
			<div class="row mb-30 align-items-end">
				<div class="col-xl-9 col-lg-9 lg-mb-30">
					<div class="services__one-title lg-t-center">
						<h2>{{ __('replica.plan') }}</h2>
					</div>
				</div>
			
			</div>
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
	</div>
	<!-- Services Area End -->
	<!-- Counter Area Start -->
	<div class="custom__container">
		<div class="row">
			<div class="col-xl-12">
				<div class="counter__area">					
					<div class="shape-slide">
						<div class="sliders scrolls">
							<img src="{{ asset('assets/replica/img/shape/counter-bg.png') }}" alt="counter-shape">
						</div>
						<div class="sliders scrolls">
						<img src="{{ asset('assets/replica/img/shape/counter-bg.png') }}" alt="counter-shape">
						</div>
					</div>
					<div class="counter__area-item">
						<div class="counter__area-item-icon">
							<i class="flaticon-review"></i>
						</div>
						<div class="counter__area-item-info">
							<h2><span class="counter">150</span>K</h2>
							<h6>Happy Customer</h6>
						</div>
					</div>
					<div class="counter__area-item">
						<div class="counter__area-item-icon">
							<i class="flaticon-meeting"></i>
						</div>
						<div class="counter__area-item-info">
							<h2><span class="counter">259</span>+</h2>
							<h6>Professional Agent</h6>
						</div>
					</div>
					<div class="counter__area-item">
						<div class="counter__area-item-icon">
							<i class="flaticon-success"></i>
						</div>
						<div class="counter__area-item-info">
							<h2><span class="counter">180</span>+</h2>
							<h6>National Award</h6>
						</div>
					</div>
					<div class="counter__area-item">
						<div class="counter__area-item-icon">
							<i class="flaticon-globe"></i>
						</div>
						<div class="counter__area-item-info">
							<h2><span class="counter">193</span>+</h2>
							<h6>Country Connected</h6>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Counter Area End -->
	<!-- Benefits Area Start -->
	<div class="benefits__area section-padding">
		<div class="container">
			
				@isset($data['why_choose_us'])
			
					{!! $data['why_choose_us'] !!}
			
				@endisset
				
			</div>
	</div>
	<!-- Benefits Area End -->

	<!-- Request Quote Area Start -->
	<div class="request__quote" data-background="{{ asset('assets/replica/img/pages/request-quote.jpg') }}">
		
		<div class="container">
			<div class="row align-items-center">
				<div class="col-xl-9 col-lg-9 col-md-8 md-mb-30">
					<div class="request__quote-title">
						<h2>Enquire Now for more details</h2>
						<div class="request__quote-title-btn">
							<a class="btn-one" href="#contact">Contact Us!</a>
							<img class="left-right-animate" src="{{ asset('assets/replica/img/icon/arrow.png') }}" alt="quote-icon">
						</div>
					</div>					
				</div>
				
			</div>
		</div>
	</div>
	<!-- Request Quote Area End -->	

	<div class="contact__three page section-padding" id="contact">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-xl-6">
					<!-- <img src="{{ asset('assets/replica/img/icon/contact-us-vct.webp') }}" alt=""> -->
					<div class="contact__two-info">
                        <h2 class="mb-60 lg-mb-30"><span>{{ __('replica.get_in_touch') }}</span></h2>
                        <div class="contact__two-info-item">
							@isset($data['contact_address'])
							<strong>{{ $data['contact_address'] }}</strong>
							@endisset
                        </div>
                        <div class="contact__two-info-item">
                            <h6>{{ __('replica.email') }} <span>:</span></h6>
                            <span>
								@isset($data['contact_mail'])
								<strong>{{ $data['contact_mail'] }}</strong>
								@endisset
							</span>
                        </div>
                        <div class="contact__two-info-item">
                            <h6>{{ __('replica.call_us') }}<span>:</span></h6>
                            <span>
								@isset($data['contact_phone'])
								<strong>{{ $data['contact_phone'] }}</strong>
								@endisset
							</span>
                        </div>
                    </div>
				</div>
				<div class="col-xl-6">
					<div class="contact__three-form t-center">
						<div class="contact__three-form-title">	
							<h2>{{ __('replica.contact_us') }}</h2>
						</div>
						<form action="{{ route('replica.contact') }}" method="POST" 		enctype="multipart/form-data">

							@csrf
							<div class="row">
								<div class="col-md-6 mb-30">
									<div class="contact__two-right-form-item contact-item">
										<span class="fal fa-user"></span>
										<input class="form-control @error('name')is-invalid @enderror" type="text" name="name" placeholder="Full Name" required="required">
										@error('name')
										{{ $message }}
										@enderror
									</div>
								</div>
								<div class="col-md-6 md-mb-30">
									<div class="contact__two-right-form-item contact-item">
										<span class="far fa-envelope-open"></span>
										<input class="form-control @error('Email')is-invalid @enderror" type="email" name="email" placeholder="Email Address" required="required">
										@error('Email')
										{{ $message }}
										@enderror											
									</div>
								</div>
								<div class="col-md-6 mb-30">
									<div class="contact__two-right-form-item contact-item">
										<span class="fal fa-user"></span>
										<input class="form-control @error('Phone')is-invalid @enderror" type="text" name="name" placeholder="Phone" required="required">
										@error('Phone')
										{{ $message }}
										@enderror
									</div>
								</div>
								<div class="col-md-6 md-mb-30">
									<div class="contact__two-right-form-item contact-item">
										<span class="far fa-envelope-open"></span>
										<input class="form-control @error('Address')is-invalid @enderror" type="text" name="Address" placeholder="Address" required="required">	
										@error('Address')
										{{ $message }}
										@enderror										
									</div>
								</div>
								<div class="col-md-12 mb-30">
									<div class="contact__two-right-form-item contact-item">
										<span class="far fa-comments"></span>
										<textarea name="message" class="form-control @error('Message')is-invalid @enderror" placeholder="Message"></textarea>
										@error('Message')
										{{ $message }}
										@enderror
									</div>
								</div>
								<div class="col-md-12">
									<div class="contact__two-right-form-item">
										<button class="btn-one" type="submit">{{ __('replica.sent') }} </button>
									</div>	
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Footer Area Start -->
	<div class="footer__two" data-background="{{ asset('assets/replica/img/shape/footer-bg.png') }}">
		<div class="container">
			<div class="row">
				<div class="col-xl-6 col-lg-6 col-sm-12">
					<div class="footer__two-widget">
						<div class="footer__two-widget-about">
							<h6>Follow Us</h6>
							<div class="footer__two-widget-about-social">
								<ul>
									<li><a href="https://www.facebook.com/" target="_blank"><i class="fab fa-facebook-f"></i></a></li>
									<li><a href="https://twitter.com/" target="_blank"><i class="fab fa-twitter"></i></a></li>
									<li><a href="https://www.behance.net/" target="_blank"><i class="fab fa-behance"></i></a></li>
									<li><a href="https://dribbble.com/" target="_blank"><i class="fab fa-dribbble"></i></a></li>
								</ul>
							</div>							
						</div>
					</div>
				</div>
				<div class="col-xl-3 col-lg-3 col-sm-12">
					<div class="footer__two-widget">
						<h4>{{ __('replica.main_pages')}}</h4>
						<div class="footer__area-widget-menu four">
							<ul>
								<li><a href="#home">{{ __('replica.home') }}</a></li>
								<li><a href="#aboutus">{{ __('replica.about') }}</a></li>
							
							</ul>
						</div>
					</div>
				</div>
				<div class="col-xl-3 col-lg-3 col-sm-12">
					<div class="footer__two-widget">
						<h4>{{ __('replica.quick_links')}}</h4>
						<div class="footer__area-widget-menu four">
							<ul>
								<li><a href="#services">{{ __('replica.plan') }}</a></li>
								<li><a href="#contact">{{ __('replica.contact') }}</a></li>
							</ul>
						</div>
					</div>
				</div>
						
			</div>
		</div>
		<div class="copyright__one">
			<div class="container">
				<div class="row">
					<div class="col-xl-12">
						<p>Copyright <a href="#home">{{ date('Y') }}{{ $company->name }}</a> - All Rights Reserved</p>
					</div>
				</div>
			</div>
		</div>		
	</div>
	<!-- Footer Area End -->	
	<!-- Scroll Btn Start -->
	<div class="scroll-up scroll-four">
		<svg class="scroll-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102"><path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" /> </svg>
	</div>
	<!-- Scroll Btn End -->	
	<!-- Main JS -->
	<script src="{{ asset('assets/replica/js/jquery-3.7.0.min.js') }}"></script>
	<!-- Bootstrap JS -->
	<script src="{{ asset('assets/replica/js/bootstrap.min.js') }}"></script>
	<!-- Counter Up JS -->
	<script src="{{ asset('assets/replica/js/jquery.counterup.min.js') }}"></script>
	<!-- Popper JS -->
	<script src="{{ asset('assets/replica/js/popper.min.js') }}"></script>
	<!-- Progressbar JS -->
	<script src="{{ asset('assets/replica/js/progressbar.min.js') }}"></script>
	<!-- Magnific Popup JS -->
	<script src="{{ asset('assets/replica/js/jquery.magnific-popup.min.js') }}"></script>
	<!-- Swiper Bundle JS -->
	<script src="{{ asset('assets/replica/js/swiper-bundle.min.js') }}"></script>
    <!-- Isotope JS -->
	<script src="{{ asset('assets/replica/js/isotope.pkgd.min.js') }}"></script>
	<!-- Waypoints JS -->
	<script src="{{ asset('assets/replica/js/jquery.waypoints.min.js') }}"></script>
	<!-- Mean Menu JS -->
	<script src="{{ asset('assets/replica/js/jquery.meanmenu.min.js') }}"></script>
	<!-- Custom JS -->
	<script src="{{ asset('assets/replica/js/custom.js') }}"></script>
</body>

</html>