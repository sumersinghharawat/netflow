import React from "react";
import TimelineLinks from "./crmTimelineLinks";
import { useTranslation } from "react-i18next";
import { useParams } from "react-router";
import { Link } from "react-router-dom";

const CrmTimeline = () => {
  const { t } = useTranslation();
  const params = useParams();

  return (
    <>
      <h4>
        <Link class="back_btn" to="/crm-dashboard">
          <i class="fa-solid fa-arrow-left"></i>
        </Link>
      </h4>
      <div className="page_head_top">
        {t("timeline")}
        <div className="right_btn_mob_toggle">
          <i className="fa fa-bars"></i>
        </div>
      </div>
      <TimelineLinks id={params.id} />
    </>
  );
};

export default CrmTimeline;
