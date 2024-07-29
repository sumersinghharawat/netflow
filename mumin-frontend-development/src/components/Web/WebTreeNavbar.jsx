import React, { useState } from "react";
import { NavLink } from "react-router-dom";
import SubmitButton from "../Common/buttons/SubmitButton";
import { useSelector } from "react-redux";
import { useLocation } from "react-router-dom";
import { useTranslation } from "react-i18next";
import { Form } from "react-bootstrap";
import { ApiHook } from "../../hooks/apiHook";

const WebTreeNavbar = ({ menu, searchUsername, setSearchUsername }) => {
  const { t } = useTranslation();
  const location = useLocation();
  const pathname = location.pathname;
  const [username, setUsername] = useState("");
  const [selectedMenu, setSelectedMenu] = useState(true);
  if (location.pathname === "/sponsor-tree-web") {
    ApiHook.CallSponsorTreeList("", "", searchUsername);
  } else if (location.pathname === "/genealogy-tree-web") {
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

  return (
    <>
      <div
        className={`tree_view_top_filter_bar mt-2 hide_show_mobile ${
          selectedMenu ? "show_mn" : ""
        } ${location.pathname === "/tree-view-web" ? "tree_web_view" : ""}`}
      >
        <div className="row justify-content-between">
          {(trimmedPathname === "genealogy-tree-web" ||
            trimmedPathname === "sponsor-tree-web") && (
            <div className="col-md-4 mob_filter_right ">
              {location.pathname !== "/tree-view-web" &&  
              
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
              }
            </div>
          )}
        </div>
      </div>
    </>
  );
};

export default WebTreeNavbar;
