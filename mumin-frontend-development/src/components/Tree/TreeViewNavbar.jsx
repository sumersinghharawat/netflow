import React, { useState } from "react";
import { NavLink } from "react-router-dom";
import SubmitButton from "../Common/buttons/SubmitButton";
import { useSelector } from "react-redux";
import { useLocation } from "react-router-dom";
import { useTranslation } from "react-i18next";
import { Form } from "react-bootstrap";
import { ApiHook } from "../../hooks/apiHook";

const TreeViewNavbar = ({ menu, searchUsername, setSearchUsername }) => {
  const { t } = useTranslation();
  const location = useLocation();
  const pathname = location.pathname;
  const [username, setUsername] = useState("");
  const [selectedMenu, setSelectedMenu] = useState(false);
  if (location.pathname === "/sponsor-tree") {
    ApiHook.CallSponsorTreeList("", "", searchUsername);
  } else if (location.pathname === "/genealogy-tree") {
    ApiHook.CallGenealogyTreeList("", "", searchUsername);
  } else {
    //  Do nothing
  }
  const trimmedPathname = pathname.replace(/^\//, "");
  const plan = useSelector(
    (state) => state.dashboard?.appLayout?.moduleStatus?.mlm_plan
  );

  const handleChange = (e) => {
    const { value } = e.target;
    setUsername(value);
  };
  const handleSearch = () => {
    setSearchUsername(username);
  };

  const handleKeyPress = (e) => {
    if (e.key === "Enter") {
      // If Enter key is pressed, trigger the search
      handleSearch();
    }
  };
  const handleSideMenuToggle = () => {
    setSelectedMenu(!selectedMenu);
  };

  return (
    <>
      <div className="page_head_top">
        {t(menu)}
        <div className="right_btn_mob_toggle" onClick={handleSideMenuToggle}>
          <i className="fa fa-bars"></i>
        </div>
      </div>
      <div
        className={`tree_view_top_filter_bar mt-2 hide_show_mobile ${
          selectedMenu ? "show_mn" : ""
        }`}
      >
        <div className="row justify-content-between">
          <div
            className={`col-md-8 hide_show_mobile ${
              selectedMenu ? "show_mn" : ""
            }`}
          >
            <NavLink
              to="/genealogy-tree"
              className={({ isActive }) =>
                isActive ? "btn_ewallt_page active" : "btn_ewallt_page"
              }
            >
              {t("genealogyTree")}
            </NavLink>
            {plan !== "Unilevel" && (
              <NavLink
                to="/sponsor-tree"
                className={({ isActive }) =>
                  isActive ? "btn_ewallt_page active" : "btn_ewallt_page"
                }
              >
                {t("sponsorTree")}
              </NavLink>
            )}
            <NavLink
              to={"/tree-view"}
              className={({ isActive }) =>
                isActive ? "btn_ewallt_page active" : "btn_ewallt_page"
              }
            >
              {t("treeView")}
            </NavLink>
            <NavLink
              to={"/downline-members"}
              className={({ isActive }) =>
                isActive ? "btn_ewallt_page active" : "btn_ewallt_page"
              }
            >
              {t("downlineMembers")}
            </NavLink>
            {plan !== "Unilevel" && (
              <NavLink
                to={"/referral-members"}
                className={({ isActive }) =>
                  isActive ? "btn_ewallt_page active" : "btn_ewallt_page"
                }
              >
                {t("referralMembers")}
              </NavLink>
            )}
          </div>
          {(trimmedPathname === "genealogy-tree" ||
            trimmedPathname === "sponsor-tree") && (
            <div className="col-md-4 mob_filter_right ">
              <div className="right_search_div d-flex gap-1 nav-bar-flex">
                <Form.Group>
                  <Form.Control
                    id="Search"
                    type="text"
                    placeholder={t("search")}
                    onChange={(e) => handleChange(e)}
                    onKeyPress={(e) => handleKeyPress(e)}
                    value={username}
                    required
                  />
                </Form.Group>
                <SubmitButton
                  className="btn btn-primary"
                  text={t("search")}
                  click={handleSearch}
                />
              </div>
            </div>
          )}
        </div>
      </div>
    </>
  );
};

export default TreeViewNavbar;
