import React from "react";
import { NavLink, useLocation } from "react-router-dom";

const MobileFooter = ({ menus }) => {
    const location = useLocation();
    const islinkActive = (link) => {
        if (link === "networks") {
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
  return (
    <div className="mobile_footer_menu">
    <div className="mobile_footer_menu_sec">
      {menus?.map(
        (menuItem, index) =>
          menuItem.isMain &&
          !menuItem.ecomLink && (
            <NavLink
              key={index}
              to={menuItem.slug}
              className={`mobile_footer_menu_list ${islinkActive(menuItem.slug)}` }
            >
              <img src={`/images/${menuItem.userIcon}`} alt="" />
            </NavLink>
          )
      )}
    </div>
  </div>
  );
};

export default MobileFooter;
