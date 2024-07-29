import React, { useState } from "react";
import { useTranslation } from "react-i18next";
import { Link } from "react-router-dom";
import CurrencyConverter from "../../Currency/CurrencyConverter";
import { ApiHook } from "../../hooks/apiHook";
import Skeleton from "react-loading-skeleton";
import { useSelector } from "react-redux";

const EarningsExpenses = ({ earnings, currency, conversionFactor }) => {
  const { t } = useTranslation();
  const [activeTab, setActiveTab] = useState("earnings");
  const [expenseCheck, setExpenseCheck] = useState(false);
  const moduleStatus = useSelector(
    (state) => state.dashboard?.appLayout?.moduleStatus
  );

  const expenses = ApiHook.CallDahboardExpenses(expenseCheck, setExpenseCheck);

  const handleTabClick = (tabId) => {
    if (tabId === "expenses") {
      setExpenseCheck(true);
    }
    setActiveTab(tabId);
  };

  return (
    <div className={moduleStatus?.rank_status ? "col-md-4" : "col-md-5"}>
      <div className="joinings_viewBox teamperfomance">
        <div className="joinings_viewBox_head">
          <h5>{t("earningsAndExpenses")}</h5>
        </div>
        {earnings && (
          <ul
            className="teamPerfomance_tab nav nav-tabs mb-3"
            id="ex1"
            role="tablist"
          >
            {earnings && (
              <li className="nav-item" role="presentation">
                <Link
                  className={`nav-link${
                    activeTab === "earnings" ? " active" : ""
                  }`}
                  id={`ex1-tab-${"earnings"}`}
                  data-bs-toggle="tab"
                  role="tab"
                  aria-controls={"earnings"}
                  aria-selected={activeTab === "earnings"}
                  onClick={() => handleTabClick("earnings")}
                >
                  {t("earnings")}
                </Link>
              </li>
            )}
            {expenses && (
              <li className="nav-item" role="presentation">
                <Link
                  className={`nav-link${
                    activeTab === "expenses" ? " active" : ""
                  }`}
                  id={`ex1-tab-${"expenses"}`}
                  data-bs-toggle="tab"
                  role="tab"
                  aria-controls={"expenses"}
                  aria-selected={activeTab === "expenses"}
                  onClick={() => handleTabClick("expenses")}
                >
                  {t("expenses")}
                </Link>
              </li>
            )}
          </ul>
        )}

        <div className="tab-content" id="ex2-content">
          {activeTab === "earnings" && (
            <div
              className={`tab-pane${
                activeTab === "earnings" ? " show active" : " fade"
              }`}
              id={"earnings"}
              role="tabpanel"
              aria-labelledby={"earnings"}
            >
              <div className="teammbrs_cnt">
                {!earnings ? (
                  <div className="teammbrs_cnt_name_dtl">
                    <div className="teammbrs_cnt_name">
                      <Skeleton count={9} />
                    </div>
                  </div>
                ) : earnings?.length === 0 ? (
                  <div className="no-data-div">
                    <div className="no-data-div-image">
                      <img src="/images/nodata-image.png" alt="" />
                    </div>
                    <p>{t("noDataFound")}</p>
                  </div>
                ) : (
                  earnings?.map((row, rowIndex) => (
                    <div className="earning_expence_row" key={rowIndex}>
                      <span>{t(row.amountType)}</span>
                      <strong style={{ color: "#03AD47" }}>{`${
                        currency?.symbolLeft
                      } ${CurrencyConverter(
                        row.amount,
                        conversionFactor
                      )}`}</strong>
                    </div>
                  ))
                )}
              </div>
            </div>
          )}
          {expenses && (
            <div
              className={`tab-pane${
                activeTab === "expenses" ? " show active" : " fade"
              }`}
              id={"expenses"}
              role="tabpanel"
              aria-labelledby={"expenses"}
            >
              <div className="teammbrs_cnt">
                {!expenses?.data ? (
                  <div className="earning_expence_row">
                    <span>
                      <Skeleton count={9} />
                    </span>
                  </div>
                ) : expenses?.data?.length === 0 ? (
                  <div className="no-data-div">
                    <div className="no-data-div-image">
                      <img src="/images/nodata-image.png" alt="" />
                    </div>
                    <p>{t("noDataFound")}</p>
                  </div>
                ) : (
                  expenses?.data?.map((row, rowIndex) => (
                    <div className="earning_expence_row" key={rowIndex}>
                      <span>{t(row.amountType)}</span>
                      <strong style={{ color: "red" }}>{`${
                        currency?.symbolLeft
                      } ${CurrencyConverter(
                        row.amount,
                        conversionFactor
                      )}`}</strong>
                    </div>
                  ))
                )}
              </div>
            </div>
          )}
        </div>
      </div>
    </div>
  );
};

export default EarningsExpenses;
