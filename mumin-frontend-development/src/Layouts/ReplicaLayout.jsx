import React, { useEffect, useState } from "react";
import { useLocation, useNavigate, useParams } from "react-router";
import "../style.css";
import { Link } from "react-router-dom";
import TermsAndConditionReplicaModal from "../components/Common/modals/ReplicaTerms";
import PolicyReplica from "../components/Common/modals/ReplicaPolicy";
import { useSelector } from "react-redux";
import { useTranslation } from "react-i18next";

function ReplicaLayout({ children }) {
  const [fillPercentage, setFillPercentage] = useState(0);
  const [isSticky, setIsSticky] = useState(false);
  const [showTerms, setShowTerms] = useState(false);
  const [showPolicy, setShowPolicy] = useState(false);
  const [showMobileMenu, setShowMobileMenu] = useState(false);
  const navigate = useNavigate();
  const params = useParams();
  const location = useLocation();
  const { t } = useTranslation();
  const terms = useSelector((state) => state?.replica?.termsAndPolicy?.terms);
  const policy = useSelector((state) => state?.replica?.termsAndPolicy?.policy);
  const companyDetails = useSelector((state) => state.replica?.companyDetails);
  const registerLink = useSelector((state) => state?.replica?.registerLink)
  // ---------------------------------------- API ----------------------------------------------
  

  if (params.username && params.hashKey) {
    localStorage.setItem("hashKey", params.hashKey);
    localStorage.setItem("referralId", params.username);
  }
  
  // --------------------------------------- Function -----------------------------------------
  const handleScroll = () => {
    const scrollTop = document.documentElement.scrollTop;
    const scrollDown = window.scrollY;
    const windowHeight = window.innerHeight;
    const documentHeight = document.documentElement.scrollHeight;

    if (scrollDown < 135) {
      setIsSticky(false);
    } else {
      setIsSticky(true);
    }

    const scrollPercentage =
    (scrollTop / (documentHeight - windowHeight)) * 100;
    
    const newFillPercentage = Math.min(Math.max(scrollPercentage, 0), 100);
    
    setFillPercentage(newFillPercentage);
  };
  
  const handleScrollToTop = () => {
    window.scrollTo({ top: 0, behavior: "smooth" });
  };

  const handleNavLink = () => {
    setShowMobileMenu(false);
    const hash = localStorage.getItem("hashKey");
    const referraiId = localStorage.getItem("referralId");
    navigate(`/replica/${referraiId}/${hash}`);
  };
  
  useEffect(() => {
    window.addEventListener("scroll", handleScroll);
    return () => {
      window.removeEventListener("scroll", handleScroll);
    };
  }, []);

  return (
    <div className="pageReplica" id="pageReplica">
      <div className="top__bar-four">
        <div className="custom__container">
          <div className="row">
            <div className="col-lg-8">
              <div className="top__bar-four-left lg-t-center">
                <ul>
                  <li>
                    <a href="https://www.google.com/maps" target="">
                      <i className="fas fa-map-marker-alt"></i>
                      {`${t("location")} :
                      ${companyDetails?.address ?? ""}`}
                    </a>
                  </li>
                  <li>
                    <a href="mailto:info.me@gmail.com">
                      <i className="fas fa-envelope"></i>
                      {`${t("email")} :
                      ${companyDetails?.email ?? ""}`}
                    </a>
                  </li>
                </ul>
              </div>
            </div>
            <div className="col-lg-4">
              <div className="top__bar-four-right">
                <h6>{`${t("follow_us")} :`}</h6>
                <div className="top__bar-four-right-social">
                  <ul>
                    <li>
                      <a href="https://www.facebook.com/" target="">
                        <i className="fab fa-facebook-f"></i>
                        <span>Facebook</span>
                      </a>
                    </li>
                    <li>
                      <a href="https://www.instagram.com/" target="">
                        <i className="fab fa-instagram"></i>
                        <span>Instagram</span>
                      </a>
                    </li>
                    <li>
                      <a href="https://twitter.com/" target="">
                        <i className="fab fa-twitter"></i>
                        <span>Twitter</span>
                      </a>
                    </li>
                    <li>
                      <a href="https://dribbble.com/" target="">
                        <i className="fab fa-dribbble"></i>
                        <span>Dribbble</span>
                      </a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div
        className={`header__sticky ${
          isSticky ? "header__sticky-sticky-menu" : ""
        }`}
      >
        <div className="container custom__container">
          <div className="header__area-menubar p-relative">
            <div className="header__area-menubar-left">
              <div className="header__area-menubar-left-logo">
                <a href="index.html">
                  <img className="dark-n" src="/img/logo.png" alt="Logo" />
                  <img className="light-n" src="/img/logo.png" alt="Logo" />
                </a>
              </div>
            </div>
            <div className="header__area-menubar-center">
              <div className="header__area-menubar-center-menu four menu-responsive">
                <ul
                  id="mobilemenu"
                  className={`${showMobileMenu ? "menu_open" : ""}`}
                >
                  <li>
                    <a href="#home" onClick={handleNavLink}>
                      {t("home")}
                    </a>
                  </li>

                  <li>
                    <a href="#aboutus" onClick={handleNavLink}>
                      {t("about_us")}
                    </a>
                  </li>
                  <li>
                    <a href="#services" onClick={handleNavLink}>
                      {t("services")}
                    </a>
                  </li>
                  <li>
                    <a href="#contact" onClick={handleNavLink}>
                      {t("contact_us")}
                    </a>
                  </li>
                </ul>
              </div>
            </div>
            <div className="header__area-menubar-right">
              <div className="header__area-menubar-right-responsive-menu menu__bar two">
                <i
                  className="flaticon-dots-menu"
                  onClick={() => setShowMobileMenu(true)}
                ></i>
              </div>
              <div className="header__area-menubar-right-contact">
                <div className="header__area-menubar-right-contact-icon">
                  <i className="fal fa-envelope-open-text"></i>
                </div>
                <div className="header__area-menubar-right-contact-info">
                  <span>{t("message")}</span>
                  <h6>
                    <a href="#home">{companyDetails?.email}</a>
                  </h6>
                </div>
              </div>
              <div className="header__area-menubar-right-btn four">
                {location.pathname !== "/replica-register" && (
                  <Link className="btn-one" to={registerLink}>
                    {t('register')}{" "}
                  </Link>
                )}
              </div>
            </div>
          </div>
          <div
            className={`menu__bar-popup four ${showMobileMenu ? "show" : ""}`}
          >
            <div className="menu__bar-popup-close">
              <i
                className="fal fa-times"
                onClick={() => setShowMobileMenu(false)}
              ></i>
            </div>
            <div className="menu__bar-popup-left">
              <div className="menu__bar-popup-left-logo">
                <a href="index.html">
                  <img src="/img/logo.png" alt="logo" />
                </a>
                <div className="responsive-menu"></div>
              </div>
              <div className="menu__bar-popup-left-social">
                <h6>{t("follow_us")}</h6>
                <ul>
                  <li>
                    <a href="https://www.facebook.com/" target="">
                      <i className="fab fa-facebook-f"></i>
                    </a>
                  </li>
                  <li>
                    <a href="https://www.instagram.com/" target="">
                      <i className="fab fa-instagram"></i>
                    </a>
                  </li>
                  <li>
                    <a href="https://twitter.com/" target="">
                      <i className="fab fa-twitter"></i>
                    </a>
                  </li>
                  <li>
                    <a href="https://dribbble.com/" target="">
                      <i className="fab fa-dribbble"></i>
                    </a>
                  </li>
                  <li>
                    <a href="https://www.behance.net/" target="">
                      <i className="fab fa-behance"></i>
                    </a>
                  </li>
                  <li>
                    <a href="https://www.linkedin.com/" target="">
                      <i className="fab fa-linkedin-in"></i>
                    </a>
                  </li>
                  <li>
                    <a href="https://www.youtube.com/" target="">
                      <i className="fab fa-youtube"></i>
                    </a>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* ---------------------------------------- Set child components here -------------------------------------- */}

      {children}

      {/* --------------------------------------------------------------------------------------------------------- */}

      <div
        className="footer__two"
        style={{ backgroundImage: "url(/img/shape/footer-bg.png)" }}
      >
        <div className="container">
          <div className="row">
            <div className="col-xl-6 col-lg-6 col-sm-12">
              <div className="footer__two-widget">
                <div className="footer__two-widget-about">
                  <h6>{t("follow_us")}</h6>
                  <div className="footer__two-widget-about-social">
                    <ul>
                      <li>
                        <a href="https://www.facebook.com/" target="">
                          <i className="fab fa-facebook-f"></i>
                        </a>
                      </li>
                      <li>
                        <a href="https://twitter.com/" target="">
                          <i className="fab fa-twitter"></i>
                        </a>
                      </li>
                      <li>
                        <a href="https://www.behance.net/" target="">
                          <i className="fab fa-behance"></i>
                        </a>
                      </li>
                      <li>
                        <a href="https://dribbble.com/" target="">
                          <i className="fab fa-dribbble"></i>
                        </a>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
            <div className="col-xl-3 col-lg-3 col-sm-12">
              <div className="footer__two-widget">
                <h4>{t("main_pages")}</h4>
                <div className="footer__area-widget-menu four">
                  <ul>
                    <li>
                      <a href="#home" onClick={handleNavLink}>
                        {t("home")}
                      </a>
                    </li>

                    <li>
                      <a href="#aboutus" onClick={handleNavLink}>
                        {t("about_us")}
                      </a>
                    </li>
                    <li>
                      <a href="#services" onClick={handleNavLink}>
                        {t("services")}
                      </a>
                    </li>
                    <li>
                      <a href="#contact" onClick={handleNavLink}>
                        {t("contact_us")}
                      </a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
            <div className="col-xl-3 col-lg-3 col-sm-12">
              <div className="footer__two-widget">
                <h4>{t("quick_links")}</h4>
                <div className="footer__area-widget-menu four">
                  <ul>
                    <li>
                      <Link onClick={() => setShowTerms(true)}>
                        {t("terms_and_condition")}
                      </Link>
                    </li>
                    <li>
                      <Link onClick={() => setShowPolicy(true)}>
                        {t("privacy_policy")}
                      </Link>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div className="copyright__one">
          <div className="container">
            <div className="row">
              <div className="col-xl-12">
                <p>
                  Copyright 2023{" "}
                  <a href="#home" onClick={() => handleNavLink}>
                    {companyDetails?.name}
                  </a>{" "}
                  - All Rights Reserved
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div
        className={`scroll-up scroll-four ${
          fillPercentage > 0 ? "active-scroll" : ""
        }`}
        onClick={handleScrollToTop}
      >
        <svg
          className="scroll-circle svg-content"
          width="100%"
          height="100%"
          viewBox="-1 -1 102 102"
        >
          <path
            d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98"
            strokeDasharray="307.919"
            strokeDashoffset={(307.919 * (100 - fillPercentage)) / 100}
          />
        </svg>
      </div>
      <TermsAndConditionReplicaModal
        show={showTerms}
        setShow={setShowTerms}
        data={terms}
      />
      <PolicyReplica show={showPolicy} setShow={setShowPolicy} data={policy} />
    </div>
  );
}

export default ReplicaLayout;
