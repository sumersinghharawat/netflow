import React from "react";
import { useTranslation } from "react-i18next";
import { NavLink } from "react-router-dom";

const TimelineLinks = ({ id }) => {
  const { t } = useTranslation();

  return (
    <div className="mb-3 hide_show_mobile">
      <div className="row justify-content-between">
        <div className="col-md-8 hide_show_mobile">
          <NavLink to={`/crm-timeline/${id}`} className="btn_ewallt_page">
            {t("lead_details")}
          </NavLink>
          <NavLink to={`/crm-lead-history/${id}`} className="btn_ewallt_page ">
            {t("lead_history")}
          </NavLink>
        </div>
      </div>
    </div>
  );
};

export default TimelineLinks;
