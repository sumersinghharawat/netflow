

(function ($) {
	"use strict";
	///============= * Background Image  =============\\\
	$("[data-background]").each(function() {
		$(this).css("background-image", "url(" + $(this).attr("data-background") + ")")
	});

	///============= * Responsive Menu Icon  =============\\\
	$(document).on("click", ".menu__bar i", function () {
		$(this).toggleClass('clicked');
		$('.menu__bar-popup').toggleClass('show');
	});
	$(document).on("click", ".menu__bar-popup-close", function () {
		$('.menu__bar i').removeClass('clicked');
		$('.menu__bar-popup').removeClass('show');
	});

	///============= * Responsive Menu  =============\\\
	$('.menu-responsive').meanmenu({
		meanMenuContainer: '.responsive-menu',
		meanScreenWidth: '1050',
		meanMenuOpen: '<span></span><span></span><span></span>',
		meanMenuClose: '<i class="fal fa-times"></i>'
	});	

    ///============= * Header Sticky  =============\\\
    $(window).on("scroll", function () {
        var scrollDown = $(window).scrollTop();
        if (scrollDown < 135) {
            $(".header__sticky").removeClass("header__sticky-sticky-menu");
        } else {
            $(".header__sticky").addClass("header__sticky-sticky-menu");
        }
    });

    ///============= * Search Icon Popup  =============\\\
	$(document).on("click", ".header__area-menubar-center-search-icon.open, .header__area-menubar-right-search-icon.open", function () {
		$(".header__area-menubar-center-search-box, .header__area-menubar-right-search-box")
		.fadeIn()
		.addClass("active");
	});
	$(document).on("click", ".header__area-menubar-center-search-box-icon, .header__area-menubar-right-search-box-icon", function () {
		$(this).fadeIn().removeClass("active");
	});
	$(document).on("click", ".header__area-menubar-center-search-box-icon i, .header__area-menubar-right-search-box-icon i", function () {
		$(".header__area-menubar-center-search-box, .header__area-menubar-right-search-box")
		.fadeOut()
		.removeClass("active");
	});

	///============= * Sidebar Popup  =============\\\
	$(document).on("click", ".header__area-menubar-right-sidebar-popup-icon", function () {
		$('.header__area-menubar-right-sidebar-popup').addClass('active');
		$('.sidebar-overlay').addClass('show');
	});
	$(document).on("click", ".header__area-menubar-right-sidebar-popup .sidebar-close-btn", function () {
		$('.header__area-menubar-right-sidebar-popup').removeClass('active');
		$('.sidebar-overlay').removeClass('show');
	});

	///============= * Banner Slider  =============\\\
	let sliderActive1 = '.banner-slider';
	let sliderInit1 = new Swiper(sliderActive1, {
		loop: true,
		slidesPerView: 1,
		effect: 'fade',
		autoplay: {
			delay: 5500,
			reverseDirection: false,
			disableOnInteraction: false,
		},
		navigation: {
			nextEl: '.swiper-button-next',
			prevEl: '.swiper-button-prev',
		},		
		pagination: {
			el: ".banner-pagination",
			type: "fraction",
			clickable: true,
		},
	});
	function animated_swiper(selector, init) {
		let animated = function animated() {
			$(selector + ' [data-animation]').each(function() {
				let anim = $(this).data('animation');
				let delay = $(this).data('delay');
				let duration = $(this).data('duration');
				$(this).removeClass('anim' + anim).addClass(anim + ' animated').css({
					webkitAnimationDelay: delay,
					animationDelay: delay,
					webkitAnimationDuration: duration,
					animationDuration: duration
				}).one('animationend', function() {
					$(this).removeClass(anim + ' animated');
				});
			});
		};
		animated();
		init.on('slideChange', function() {
			$(sliderActive1 + ' [data-animation]').removeClass('animated');
		});
		init.on('slideChange', animated);
	}
	animated_swiper(sliderActive1, sliderInit1);

	///============= * Features Active Hover  =============\\\
	$(document).on("mouseenter", ".features-area-item", function () {
		$(".features-area-item").removeClass("features-area-item-hover");
		$(this).addClass("features-area-item-hover");
	});

	///============= * CounterUp  =============\\\
	var counter = $('.counter');
	counter.counterUp({
		time: 2500,
		delay: 100
	});

	///============= * Testimonial Slider  =============\\\
    var mySwiper = new Swiper(".testimonial__slider", {
		direction: 'vertical',
        spaceBetween: 30,
		slidesPerView: 2,
		speed: 2000,
		loop: true,
		pagination: {
			el: ".testimonial-pagination",
			clickable: true,
		},
		autoplay: {
			delay: 4500,
			reverseDirection: false,
			disableOnInteraction: false,
		},
	});	
	///============= * Testimonial Two  =============\\\
	var galleryTop = new Swiper('.gallery-top', {
		spaceBetween: 10,
		navigation: {
		  nextEl: '.swiper-button-next',
		  prevEl: '.swiper-button-prev',
		},
		loop: true,
	    loopedSlides: 4
	});
	var galleryThumbs = new Swiper('.gallery-thumbs', {
		spaceBetween: 10,
		centeredSlides: true,
		slidesPerView: 'auto',
		slideToClickedSlide: true,
		loop: true,
		loopedSlides: 4,
		autoplay: {
			delay: 4500,
			reverseDirection: false,
			disableOnInteraction: false,
		},
	});
	galleryTop.controller.control = galleryThumbs;
	galleryThumbs.controller.control = galleryTop;

	///============= * Video Popup  =============\\\
	$('.video-popup').magnificPopup({
		type: 'iframe'
	});
	
	///============= * Image Popup  =============\\\
	$('.img-popup').magnificPopup({
		type: 'image',
		gallery: {
			enabled: true
		}
	});

	///============= * Portfolio One  =============\\\
	var swiper = new Swiper(".portfolio__one-slider", {
		loop: true,
		speed: 2000,
		spaceBetween: 30,
		slidesPerView: 4,
		autoplay: {
			delay: 4500,
			reverseDirection: false,
			disableOnInteraction: false,
		},
		breakpoints: {
			1: {
				slidesPerView: 1
			},
			750: {
				spaceBetween: 30,
				slidesPerView: 2
			},
			1115: {
				spaceBetween: 25,
				slidesPerView: 3
			},
			1500: {
				slidesPerView: 4
			},
		}
	});		
	///============= * Request Quote  =============\\\
	var swiper = new Swiper(".request__quote-slider", {
		loop: true,
		speed: 1500,
		spaceBetween: 40,
		slidesPerView: 1,
		autoplay: {
			delay: 3500,
			reverseDirection: false,
			disableOnInteraction: false,
		},
		pagination: {
			el: ".swiper-pagination",
			clickable: true,
		},
	});		
	///============= * Portfolio Three Active Hover  =============\\\
	$(document).on("click", ".portfolio__three-item", function () {
		removeActiveClasses();
		$(this).addClass("active");
	});	
	function removeActiveClasses() {
		$(".portfolio__three-item").removeClass("active");
	}	
    ///============= * Theme Loader  =============\\\
    $(window).on("load", function () {
        $(".theme-loader").fadeOut(0.0009);
    });
	///============= * Dark & Light Switch  =============\\\
	$('.switch__tab-open').on('click', function () {
		$(this).hide();
		$('.switch__tab-close').show();
		$('.switch__tab-icon').css('left', '260px');
		$('.switch__tab-area').css({
			'left': '0',
		});
	});
	$('.switch__tab-close').on('click', function () {
		$(this).hide();
		$('.switch__tab-open').show();
		$('.switch__tab-icon').css('left', '0');
		$('.switch__tab-area').css({
			'left': '-260px',
		});
	});
	$('.type-dark-mode button').on('click', function (e) {
		$(this).addClass('active').siblings().removeClass('active');
		var themeDark = $('.type-dark-mode button.active').attr('data-mode');
		if (themeDark == 'dark-mode') {
		  	$('body').addClass('dark-mode');
		} 
		else {
		  	$('body').removeClass('dark-mode');
		}		
	});  
	///============= * RTL & LTR Switch  =============\\\
	$('.ltr-rtl-mode button').on('click', function (e) {
		$(this).addClass('active').siblings().removeClass('active');
		var themeRtl = $('.ltr-rtl-mode button.active').attr('data-mode');
		if (themeRtl == 'rtl-mode') {
		  	$('body').addClass('rtl-mode');
		} else {
		  	$('body').removeClass('rtl-mode');
		}
	});

	///============= * Team Skill Bar  =============\\\
	if($('.skill__area-item-bar').length) {
		$('.skill__area-item-bar').appear(function() {
			var el = $(this);
			var percent = el.data('width');
			$(el).css('width', percent + '%');
		}, {
			accY: 0
		});
	};

	///============= * Isotope Filter  =============\\\
	$(window).on('load', function(){
		var $grid = $('.noxiy__filter-active').isotope();
		$('.noxiy__filter-button').on('click', 'button', function () {
			var filterValue = $(this).attr('data-filter');
			$grid.isotope({
				filter: filterValue
			});
		});
		$('.noxiy__filter-button').on('click', 'button', function () {
			$(this).siblings('.active').removeClass('active');
			$(this).addClass('active');
		});
   });

   	///============= * Portfolio Active Hover  =============\\\
	const projectsItems = document.querySelectorAll('.portfolio__four-item');
	projectsItems.forEach(item => {
		const heading = item.querySelector('.portfolio__four-item-inner-content-btn');
		heading.addEventListener('mouseenter', () => {
			projectsItems.forEach(item => {
				item.classList.remove('active');
			});
			item.classList.add('active');
		});
	});
	
    ///============= * croll To Top =============\\\
	var scrollPath = document.querySelector(".scroll-up path");
	var pathLength = scrollPath.getTotalLength();
	scrollPath.style.transition = scrollPath.style.WebkitTransition = "none";
	scrollPath.style.strokeDasharray = pathLength + " " + pathLength;
	scrollPath.style.strokeDashoffset = pathLength;
	scrollPath.getBoundingClientRect();
	scrollPath.style.transition = scrollPath.style.WebkitTransition = "stroke-dashoffset 10ms linear";
	var updatescroll = function () {
		var scroll = $(window).scrollTop();
		var height = $(document).height() - $(window).height();
		var scroll = pathLength - (scroll * pathLength) / height;
		scrollPath.style.strokeDashoffset = scroll;
	};
	updatescroll();
	$(window).scroll(updatescroll);
	var offset = 50;
	var duration = 950;
	jQuery(window).on("scroll", function () {
		if (jQuery(this).scrollTop() > offset) {
			jQuery(".scroll-up").addClass("active-scroll");
		}
		else {
			jQuery(".scroll-up").removeClass("active-scroll");
		}
	});	
	jQuery(".scroll-up").on("click", function (event) {
	  	event.preventDefault();
	  	jQuery("html, body").animate(
			{ scrollTop: 0, } , duration
		);
	  	return false;
	});	
})(jQuery);

$(document).on("click", "#mobilemenu>li>a", function () {
	$('.menu__bar i').removeClass('clicked');
	$('.menu__bar-popup').removeClass('show');
});

