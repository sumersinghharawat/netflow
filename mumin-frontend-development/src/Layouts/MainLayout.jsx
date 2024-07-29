import { React, useEffect, useRef, useState } from "react";
import FooterSection from "../components/Common/Footer";
import LeftSection from "../components/Common/LeftSection";
import HeaderSection from "../components/Common/HeaderSection";
import { Outlet, useLocation } from "react-router";
import RightContentSection from "../components/Dashboard/RightContent";
import layoutAnimation from "../utils/layoutAnimation";
import { NavLink } from "react-router-dom";
import ShoppingCart from "../components/shopping/ShoppingCart";
import { ApiHook } from "../hooks/apiHook";
import VisitersForm from "../components/Common/modals/VisitersForm";
import '../custom.css'
import { useSelector } from "react-redux";
import MobileFooter from "../components/Common/MobileFooter";
import { useTranslation } from "react-i18next";
import i18n from "../i18n";

const MainLayout = () => {
  const { t } = useTranslation();
  const [isLeftMenuVisible, setIsLeftMenuVisible] = useState(false);
  
  const dropdownRef = useRef(null);
  const toggleMenuRef = useRef(null);
  const toggleMobileRef = useRef(null);
  const containerRef = useRef(null);
  const [isQuickMenuOpen, setIsQuickMenuOpen] = useState(false);
  const [demoFormVisible, setDemoFormVisible] = useState(false);
  const [countries, setCountries] = useState([]);
  const [dashboardCheck, setDashboardCheck] = useState(false);
  const location = useLocation();

  const appLayout = ApiHook.CallAppLayout();
  const rightSection = ApiHook.CallDashboardRight(
    dashboardCheck,
    setDashboardCheck
  );
  const userData = useSelector((state)=> state.user?.loginResponse?.user)
  const userLang = useSelector((state)=> state.user?.selectedLanguage)
  const user = JSON.parse(userData || "{}");

  const handleLeftMenuToggle = () => {
    setIsLeftMenuVisible(!isLeftMenuVisible);
  };
  const handleQuickMenuToggle = () => {
    setIsQuickMenuOpen(!isQuickMenuOpen);
  };
  useEffect(() => {
    if (location.pathname) {
      setIsQuickMenuOpen(false);
      setIsLeftMenuVisible(false);
    }
    if (location.pathname === "/dashboard") {
      setDashboardCheck(true);
    }
    layoutAnimation(location, containerRef);
    const handleOutsideClick = (event) => {
      if (!dropdownRef.current.contains(event.target)) {
        setIsQuickMenuOpen(false);
      }
      if (
        !(
          toggleMenuRef.current.contains(event.target) ||
          toggleMobileRef.current.contains(event.target)
        )
      ) {
        setIsLeftMenuVisible(false);
      }
    };

    document.addEventListener("click", handleOutsideClick);

    return () => {
      document.removeEventListener("click", handleOutsideClick);
    };
  }, [location.pathname]);
  
 
  return (
    <>
      <div className={`${isLeftMenuVisible ? "left_menu_show" : ""}`}>
        <main
          className={
            location.pathname === "/dashboard" ? "dashboard_main_dv" : "main_dv"
          }
        >
          <section className="left_content_section">
            <HeaderSection
              count={appLayout?.data?.notificationCount}
              handleLeftMenuToggle={handleLeftMenuToggle}
              userName={user?.username}
              appLayout={appLayout?.data}
              toggleMobileRef={toggleMobileRef}
            />
            <LeftSection
              isLeftMenuVisible={isLeftMenuVisible}
              handleLeftMenuToggle={handleLeftMenuToggle}
              menus={appLayout?.data?.menu?.sideMenus}
              spclMenu={appLayout?.data?.menu?.spclMenu}
              toggleMenuRef={toggleMenuRef}
              logo={appLayout?.data?.companyProfile?.logo}
            />
            <div
              ref={containerRef}
              style={{ position: "relative", width: "100%", height: "100%" }}
            >
              <div className="center_Content_section">
                <Outlet />
              </div>
            </div>
            {location.pathname === "/dashboard" && <FooterSection />}
          </section>
          {location.pathname === "/dashboard" && (
            <section className="right_content_section">
              <RightContentSection props={rightSection?.data} />
            </section>
          )}
        </main>
        {location.pathname !== "/dashboard" && <FooterSection />}
        <div className="float_menu_btm" ref={dropdownRef}>
          <button
            className={`dropdown-toggle ${isQuickMenuOpen ? "show" : ""}`}
            onClick={handleQuickMenuToggle}
            aria-expanded={isQuickMenuOpen}
          >
            <i className="fa-solid fa-bars"></i>
          </button>
          {isQuickMenuOpen && (
            <div
              className="dropdown-menu usr_prfl right-0 show"
              style={{
                position: "fixed",
                inset: "auto 0px 0px auto",
                margin: "0px",
                transform: "translate(-50px, -102px)",
              }}
              data-popper-placement="top-end"
            >
              <ul>
                {appLayout?.data?.menu?.quickMenus.map((menuItem, index) => (
                  <li key={index}>
                    <NavLink
                      to={`/${menuItem.slug}`}
                      className={`dropdown-item ${({ isActive }) =>
                        isActive ? "active" : ""}`}
                    >
                      <i className={`${menuItem.quickIcon}`}></i>{" "}
                      {t(menuItem.slug)}
                    </NavLink>
                  </li>
                ))}
              </ul>
            </div>
          )}
        </div>
        {(location.pathname === "/shopping" ||
          location.pathname === "/product-details") && <ShoppingCart />}
      </div>
      <MobileFooter
        menus={appLayout?.data?.menu?.sideMenus}
      />
      <VisitersForm
        isVisible={demoFormVisible}
        setIsVisible={setDemoFormVisible}
        countries={countries}
      />
    </>
  );
};

export default MainLayout;
