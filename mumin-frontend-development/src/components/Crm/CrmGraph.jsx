import React from "react";
import YearlyChart from "./YearChart";
import MonthlyChart from "./MonthChart";
import { ApiHook } from "../../hooks/apiHook";
import { useTranslation } from "react-i18next";

const CrmGraphSegment = () => {
  // --------------------------------------------- API -------------------------------------------------
  const { t } = useTranslation();
  const graph = ApiHook.CallCrmGraph();

  return (
    <>
      <div className="page_head_top">{t("graph")}</div>
      <div className="row">
        <YearlyChart data={graph.data?.leadCountByMonth} />
        <MonthlyChart data={graph.data?.leadCountByDay} />
      </div>
    </>
  );
};

export default CrmGraphSegment;
