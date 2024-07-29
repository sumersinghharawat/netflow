import React, { useEffect, useRef, useState } from "react";
import { createChart } from "../../utils/joiningChart";
import Loader from "react-js-loader";
import { Link } from "react-router-dom";
import { ApiHook } from "../../hooks/apiHook";
import { useTranslation } from "react-i18next";

const JoiningChart = ({ charts }) => {
  const { t } = useTranslation();
  const chartRef = useRef(null);
  const [selectedFilter, setSelectedFilter] = useState("month");

  const handleFilterClick = (filter) => {
    setSelectedFilter(filter);
  };

  ApiHook.CallGraphFilter(selectedFilter);

  useEffect(() => {
    if (charts !== null) {
      const cleanup = createChart(chartRef, charts);
      return () => {
        cleanup();
      };
    }
  }, [charts]);
  return (
    <div className="col-md-7">
      <div className="joinings_viewBox">
        <div className="joinings_viewBox_head">
          <h5>{t("joinings")}</h5>
          <div className="box_filter">
            <Link
              className={`${selectedFilter === "year" ? "active" : ""}`}
              onClick={() => handleFilterClick("year")}
            >
              {t("year")}
            </Link>
            <Link
              className={`${selectedFilter === "month" ? "active" : ""}`}
              onClick={() => handleFilterClick("month")}
            >
              {t("month")}
            </Link>
            <Link
              className={`${selectedFilter === "day" ? "active" : ""}`}
              onClick={() => handleFilterClick("day")}
            >
              {t("day")}
            </Link>
          </div>
        </div>
        {charts === null ? (
          <div
            className="joinings_viewBox_graph"
            style={{
              display: "inline-block",
              textAlign: "center",
              marginTop: "60px",
            }}
          >
            <div>
              <Loader
                type="rectangular-ping"
                bgColor={"#ada7a7"}
                color={"#ada7a7"}
              />
            </div>
          </div>
        ) : (
          <div className="joinings_viewBox_graph">
            <canvas ref={chartRef} id="joiningChart" />
          </div>
        )}
      </div>
    </div>
  );
};

export default JoiningChart;
