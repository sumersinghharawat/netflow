import React, { useEffect, useRef } from "react";
import Swiper from "swiper";
import $ from "jquery";

const ReplicaBanners = ({ data }) => {
  const sliderRef = useRef(null);

  useEffect(() => {
    const sliderActive1 = ".banner-slider";

    // Create the Swiper instance and store it in the ref
    sliderRef.current = new Swiper(sliderActive1, {
      loop: true,
      slidesPerView: 1,
      effect: "fade",
      navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
      },
      pagination: {
        el: ".banner-pagination",
        type: "fraction",
        clickable: true,
      },
    });

    function animated_swiper(selector, init) {
      const animated = () => {
        $(selector + " [data-animation]").each(function () {
          let anim = $(this).data("animation");
          let delay = $(this).data("delay");
          let duration = $(this).data("duration");
          $(this)
            .removeClass("anim" + anim)
            .addClass(anim + " animated")
            .css({
              animationDelay: delay,
              animationDuration: duration,
            })
            .one("animationend", function () {
              $(this).removeClass(anim + " animated");
            });
        });
      };
      animated();
      init.on("slideChange", function () {
        $(selector + " [data-animation]").removeClass("animated");
      });
      init.on("slideChange", animated);
    }

    animated_swiper(sliderActive1, sliderRef.current);

    // Manually change the slide every 7 seconds
    const changeSlideInterval = setInterval(() => {
      if (sliderRef.current) {
        sliderRef.current.slideNext();
      }
    }, 5500);

    // Cleanup Swiper and interval when the component unmounts
    return () => {
      if (sliderRef.current) {
        sliderRef.current.destroy();
      }
      clearInterval(changeSlideInterval);
    };
  }, []);

  // Function to handle custom button click and change slide
  const handleButtonClick = (direction) => {
    if (sliderRef.current) {
      if (direction === "prev") {
        sliderRef.current.slidePrev();
      } else if (direction === "next") {
        sliderRef.current.slideNext();
      }
    }
  };

  return (
    <div className="banner__one swiper banner-slider" id="home">
      <div className="swiper-wrapper">
        {data?.banner?.map((banner, index) => (
          <div className="swiper-slide" key={index}>
            <div
              className="banner__one-image dark-n"
              style={{ backgroundImage: `url(${banner})` }}
            ></div>
            <div
              className="banner__one-image light-n"
              style={{
                backgroundImage: `url(${banner})`,
              }}
            ></div>
            <div className="container">
              <div className="row">
                <div className="col-xl-12">
                  <div className="banner__one-content">
                    <span data-animation="fadeInUp" data-delay=".3s">
                      {data?.title1}
                    </span>
                    <h1 data-animation="fadeInUp" data-delay=".7s">
                      {data?.title2}
                    </h1>
                    <div
                      className="banner__one-content-button"
                      data-animation="fadeInUp"
                      data-delay="1s"
                    >
                      <a classNswiperame="btn-one" href="#aboutus">
                        Discover More
                      </a>
                    </div>
                    <img
                      className="banner__one-shape-four"
                      src="/img/shape/banner-6.png"
                      alt="banner-shape"
                    />
                  </div>
                  <img
                    className="banner__one-shape-two"
                    src="/img/shape/banner-5.png"
                    data-animation="fadeInUpBig"
                    data-delay="2s"
                    alt="banner-shape"
                  />
                  <img
                    className="banner__one-shape-three"
                    src="/img/shape/banner-1.png"
                    data-animation="fadeInRightBig"
                    data-delay="1.5s"
                    alt="banner-shape"
                  />
                </div>
              </div>
            </div>
          </div>
        ))}
      </div>
      <div className="banner__one-arrow">
        <div
          className="banner__one-arrow-prev swiper-button-prev"
          onClick={() => handleButtonClick("prev")}
        >
          <i className="fal fa-long-arrow-left"></i>
        </div>
        <div
          className="banner__one-arrow-next swiper-button-next"
          onClick={() => handleButtonClick("next")}
        >
          <i className="fal fa-long-arrow-right"></i>
        </div>
      </div>
      <img
        className="banner__one-shape-one"
        src="/img/shape/banner-7.png"
        alt="banner-shape"
      />
    </div>
  );
};

export default ReplicaBanners;
