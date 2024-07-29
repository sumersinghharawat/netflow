import React from "react";
import { useTranslation } from "react-i18next";
import { NavLink } from "react-router-dom";

const CrmLinks = () => {
  const { t } = useTranslation();
  return (
    <div className="mb-3 hide_show_mobile">
      <div className="row justify-content-between">
        <div className="col-md-8 hide_show_mobile">
          <NavLink to={'/crm-dashboard'} className="btn_ewallt_page">
            {t("CRM")}
          </NavLink>
          <NavLink to={'/view-lead'} className="btn_ewallt_page">
            {t("view_lead")}
          </NavLink>
          <NavLink to={'/add-lead'} className="btn_ewallt_page">
            {t("add_lead")}
          </NavLink>
          <NavLink to={'/crm-graph'} className="btn_ewallt_page">
            {t("graph")}
          </NavLink>
        </div>
      </div>
    </div>
  );
};

export default CrmLinks;
