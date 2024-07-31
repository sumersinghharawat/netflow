import React, { useState } from "react";
import { Link, NavLink, useLocation, useNavigate } from "react-router-dom";
import { Popover, OverlayTrigger } from "react-bootstrap";
import { ApiHook } from "../../hooks/apiHook";
import { useTranslation } from "react-i18next";
import menuPlaceHolder from "../../examples/dashboardMenu.json";
const LeftSection = ({
  isLeftMenuVisible,
  handleLeftMenuToggle,
  menus,
  spclMenu,
  toggleMenuRef,
  logo,
}) => {
  const { t } = useTranslation();
  const navigate = useNavigate();
  const [activeSubMenuIndex, setActiveSubMenuIndex] = useState(-1);
  const [registerLinkCheck, setRegisterLinkCheck] = useState(false);
  const [storeLinkCheck, setStoreLinkCheck] = useState(false);
  const location = useLocation();
  const menuItems = menus ? menus : menuPlaceHolder;

  const handleDropMenuClick = (index) => {
    if (activeSubMenuIndex === index) {
      setActiveSubMenuIndex(-1); // Close the submenu if it's already open
    } else {
      setActiveSubMenuIndex(index); // Open the clicked submenu
    }
  };
  const renderPopover = (
    content // popover the tilte in Menu
  ) => (
    <Popover>
      <Popover.Body>{content}</Popover.Body>
    </Popover>
  );
  const islinkActive = (link) => {
    if (link === "/networks") {
      return location.pathname === "/sponsor-tree" ||
        location.pathname === "/genealogy-tree" ||
        location.pathname === "/tree-view" ||
        location.pathname === "/downline-members" ||
        location.pathname === "/referral-members"
        ? "active"
        : "";
    } else if (location.pathname === link) {
      return "active";
    }
  };
  // ----------------------------- Api Call for Ecom Link ----------------------
  const registerLink = ApiHook.CallRegisterLink(
    registerLinkCheck,
    setRegisterLinkCheck
  );
  if (registerLink.isFetched) {
    window.location.href = registerLink.data?.link;
  }
  const storeLink = ApiHook.CallStoreLink(storeLinkCheck, setStoreLinkCheck);
  if (storeLink.isFetched) {
    window.location.href = storeLink.data?.link;
  }
  return (
    <aside className="left_sidebar">
      <div className="left_mn_toogle_btn"></div>
      <div
        ref={toggleMenuRef}
        className={`left_navigation_full_hover ${
          isLeftMenuVisible ? "show_mn" : ""
        }`}
      >
        <div
          className="left_mn_toogle_btn"
          onClick={handleLeftMenuToggle}
        ></div>
        <div className="quick_balance_Box_left_logo">
          <img src={/*logo ?? */"/images/logo_user.png"} alt="" />
        </div>
        <div className="left_navigation_left_navigation">
          <ul>
            {menuItems?.map((item, index) => (
              <li
                key={index}
                className={`${
                  item.subMenu?.length > 0 ? "drop-menu" : ""
                } ${islinkActive(`/${item.slug}`)}`}
                onClick={() => handleDropMenuClick(index)}
              >
                {item?.subMenu?.length > 0 ? (
                  <>
                    <span className="navigation_ico">
                      <img src={`/images/${item.userIcon}`} alt="" />
                    </span>
                    {t(`${item.slug}`)}
                    {item.subMenu?.length > 0 && (
                      <i className="fa fa-angle-down"></i>
                    )}
                  </>
                ) : !item.ecomLink ? (
                  <NavLink to={`/${item.slug}`}>
                    <span className="navigation_ico">
                      <img src={`/images/${item.userIcon}`} alt="" />
                    </span>
                    {t(`${item.slug}`)}
                    {item.subMenu?.length > 0 && (
                      <i className="fa fa-angle-down"></i>
                    )}
                  </NavLink>
                ) : item.slug === "shopping" ? (
                  <Link onClick={() => setStoreLinkCheck(true)}>
                    <span className="navigation_ico">
                      <img src={`/images/${item.userIcon}`} alt="" />
                    </span>
                    {t(`${item.slug}`)}
                  </Link>
                ) : (
                  <Link onClick={() => setRegisterLinkCheck(true)}>
                    <span className="navigation_ico">
                      <img src={`/images/${item.userIcon}`} alt="" />
                    </span>
                    {t(`${item.slug}`)}
                  </Link>
                )}
                {item?.subMenu?.length > 0 && (
                  <ul
                    className={`sub-menu ${
                      activeSubMenuIndex === index ? "show_mn" : ""
                    }`}
                  >
                    {item?.subMenu.map((subItem, subIndex) => (
                      <li key={subIndex}>
                        <NavLink to={`/${subItem.slug}`}>
                          {t(`${subItem.slug}`)}
                        </NavLink>
                      </li>
                    ))}
                  </ul>
                )}
              </li>
            ))}
          </ul>
        </div>
      </div>
      <nav className="left_navigation_section">
        <ul className="left_navigation">
          {menuItems?.map((menuItem, index) =>
            menuItem.isMain && !menuItem.ecomLink ? (
              <li key={index}>
                <OverlayTrigger
                  key={menuItem.slug}
                  trigger={["hover", "focus"]}
                  placement="right"
                  overlay={renderPopover(t(`${menuItem.slug}`))}
                >
                  <NavLink
                    className={islinkActive(`/${menuItem.slug}`)}
                    to={menuItem.slug}
                  >
                    <i>
                      <img src={`/images/${menuItem.userIcon}`} alt="" />
                    </i>
                    <span>{menuItem.title}</span>
                  </NavLink>
                </OverlayTrigger>
              </li>
            ) : (
              menuItem.ecomLink &&
              menuItem.isMain && (
                <li key={index}>
                  <OverlayTrigger
                    key={menuItem.slug}
                    trigger={["hover", "focus"]}
                    placement="right"
                    overlay={renderPopover(t(`${menuItem.slug}`))}
                  >
                    <Link onClick={() => setRegisterLinkCheck(true)}>
                      <i>
                        <img src={`/images/${menuItem.userIcon}`} alt="" />
                      </i>
                      <span>{menuItem.title}</span>
                    </Link>
                  </OverlayTrigger>
                </li>
              )
            )
          )}
        </ul>
        {/* {spclMenu &&
          (spclMenu?.ecomLink ? (
            <div
              className="support_menu_btn"
              onClick={() => setStoreLinkCheck(true)}
            >
              <img
                src={
                  spclMenu.slug === "shopping"
                    ? "/images/shopping-cart-white_old.svg"
                    : `/images/${spclMenu?.userIcon}`
                }
                alt=""
              />
            </div>
          ) : (
            <div
              className="support_menu_btn"
              onClick={() => navigate("/shopping")}
            >
              <img
                src={
                  spclMenu.slug === "shopping"
                    ? "/images/shopping-cart-white_old.svg"
                    : `/images/${spclMenu?.userIcon}`
                }
                alt=""
              />
            </div>
          ))} */}
      </nav>
    </aside>
  );
};

export default LeftSection;
