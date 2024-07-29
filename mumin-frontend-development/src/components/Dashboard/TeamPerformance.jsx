import React, { useState } from "react";
import { useTranslation } from "react-i18next";
import { Link } from "react-router-dom";
import CurrencyConverter from "../../Currency/CurrencyConverter";
import { ApiHook } from "../../hooks/apiHook";
import { useSelector } from "react-redux";
import Skeleton from "react-loading-skeleton";

const TeamMembersEarningSection = ({
  topEarners,
  currency,
  conversionFactor,
}) => {
  const { t } = useTranslation();
  const [activeTab, setActiveTab] = useState("top-earners");
  const [recruitersCheck, setRecruitersCheck] = useState(false);
  const [packageCheck, setPackageCheck] = useState(false);
  const moduleStatus = useSelector(
    (state) => state.dashboard?.appLayout?.moduleStatus
  );

  //-------------------------------- API ----------------------------------------
  const topRecruiters = ApiHook.CallTopRecruiters(
    recruitersCheck,
    setRecruitersCheck
  );
  const packageOverview = ApiHook.CallPackageOverview(
    packageCheck,
    setPackageCheck
  );

  const handleTabChange = (tab) => {
    if (tab === "top-recruiters") {
      setRecruitersCheck(true);
    } else if (tab === "package-overview") {
      setPackageCheck(true);
    }
    setActiveTab(tab);
  };
  return (
    <div className={moduleStatus?.rank_status ? "col-md-4" : "col-md-7"}>
      <div className="joinings_viewBox teamperfomance">
        <div className="joinings_viewBox_head">
          <h5>{t("teamPerformance")}</h5>
        </div>
        {topEarners && (
          <ul
            className="teamPerfomance_tab nav nav-tabs mb-3"
            id="ex1"
            role="tablist"
          >
            {topEarners && (
              <li className="nav-item" role="presentation">
                <Link
                  className={`nav-link ${
                    activeTab === "top-earners" ? "active" : ""
                  }`}
                  id={`ex1-tab-${"top-earners"}`}
                  data-bs-toggle="tab"
                  role="tab"
                  aria-controls={"top-earners"}
                  aria-selected={activeTab === "top-earners" ? "true" : "false"}
                  onClick={() => handleTabChange("top-earners")}
                >
                  {t("topEarners")}
                </Link>
              </li>
            )}
            {topRecruiters && (
              <li className="nav-item" role="presentation">
                <Link
                  className={`nav-link ${
                    activeTab === "top-recruiters" ? "active" : ""
                  }`}
                  id={`ex1-tab-${"top-recruiters"}`}
                  data-bs-toggle="tab"
                  role="tab"
                  aria-controls={"top-recruiters"}
                  aria-selected={
                    activeTab === "top-recruiters" ? "true" : "false"
                  }
                  onClick={() => handleTabChange("top-recruiters")}
                >
                  {t("topRecruiters")}
                </Link>
              </li>
            )}
            {!!moduleStatus?.product_status && (
              <li className="nav-item" role="presentation">
                <Link
                  className={`nav-link ${
                    activeTab === "package-overview" ? "active" : ""
                  }`}
                  id={`ex1-tab-${"package-overview"}`}
                  data-bs-toggle="tab"
                  role="tab"
                  aria-controls={"package-overview"}
                  aria-selected={
                    activeTab === "package-overview" ? "true" : "false"
                  }
                  onClick={() => handleTabChange("package-overview")}
                >
                  {t("packageOverview")}
                </Link>
              </li>
            )}
          </ul>
        )}
        <div className="tab-content" id="ex1-content">
          {activeTab === "top-earners" && (
            <div
              className={`tab-pane fade ${
                activeTab === "top-earners" ? "show active" : ""
              }`}
              id={"top-earners"}
              role="tabpanel"
              aria-labelledby={"top-earners"}
            >
              <div className="top_earners_Section">
                {!topEarners ? (
                  <div className="teammbrs_cnt_row">
                    <div className="teammbrs_cnt_img">
                      <Skeleton
                        circle
                        width="45px"
                        height="45px"
                        containerClassName="avatar-skeleton"
                        count={4}
                      />
                    </div>
                    <div className="teammbrs_cnt_name_dtl">
                      <div className="teammbrs_cnt_name">
                        <Skeleton count={9} />
                      </div>
                    </div>
                  </div>
                ) : topEarners.length === 0 ? (
                  <div className="no-data-div">
                    <div className="no-data-div-image">
                      <img src="/images/nodata-image.png" alt="" />
                    </div>
                    <p>{t("noDataFound")}</p>
                  </div>
                ) : (
                  topEarners.map((item, index) => (
                    <div key={index}>
                      <div className="teammbrs_cnt_row">
                        <div className="teammbrs_cnt_img">
                          <img
                            src={
                              item.image
                                ? item.image
                                : "/images/user-profile.png"
                            }
                            alt=""
                          />
                        </div>
                        <div className="teammbrs_cnt_name_dtl">
                          <div className="teammbrs_cnt_name">
                            {item.name}
                            <span>{item.username}</span>
                          </div>
                        </div>
                        <div className="teamperfoamance_amount">
                          <strong style={{ color: "#03AD47" }}>{`${
                            currency?.symbolLeft
                          } ${CurrencyConverter(
                            item.balanceAmount,
                            conversionFactor
                          )}`}</strong>
                        </div>
                      </div>
                    </div>
                  ))
                )}
              </div>
            </div>
          )}
          {topRecruiters && (
            <div
              className={`tab-pane fade ${
                activeTab === "top-recruiters" ? "show active" : ""
              }`}
              id={"top-recruiters"}
              role="tabpanel"
              aria-labelledby={"top-recruiters"}
            >
              <div className="top_earners_Section">
                {!topRecruiters?.data ? (
                  <div className="teammbrs_cnt_row">
                    <div className="teammbrs_cnt_img">
                      <Skeleton
                        circle
                        width="45px"
                        height="45px"
                        containerClassName="avatar-skeleton"
                        count={4}
                      />
                    </div>
                    <div className="teammbrs_cnt_name_dtl">
                      <div className="teammbrs_cnt_name">
                        <Skeleton count={9} />
                      </div>
                    </div>
                    <div className="teammbrs_cnt_img">
                      <Skeleton
                        circle
                        width="45px"
                        height="45px"
                        containerClassName="avatar-skeleton"
                        count={4}
                      />
                    </div>
                  </div>
                ) : topRecruiters?.data?.length === 0 ? (
                  <div className="no-data-div">
                    <div className="no-data-div-image">
                      <img src="/images/nodata-image.png" alt="" />
                    </div>
                    <p>{t("noDataFound")}</p>
                  </div>
                ) : (
                  topRecruiters?.data?.map((item, index) => (
                    <div key={index}>
                      <div className="teammbrs_cnt_row">
                        <div className="teammbrs_cnt_img">
                          <img
                            src={
                              item.image
                                ? item.image
                                : "/images/user-profile.png"
                            }
                            alt=""
                          />
                        </div>
                        <div className="teammbrs_cnt_name_dtl">
                          <div className="teammbrs_cnt_name">
                            {item.name}
                            <span>{item.username}</span>
                          </div>
                        </div>
                        <div className="top_recuirters_num">
                          <span>{item.count}</span>
                        </div>
                      </div>
                    </div>
                  ))
                )}
              </div>
            </div>
          )}
          {packageOverview && (
            <div
              className={`tab-pane fade ${
                activeTab === "package-overview" ? "show active" : ""
              }`}
              id={"package-overview"}
              role="tabpanel"
              aria-labelledby={"package-overview"}
            >
              <div className="top_earners_Section">
                {!packageOverview?.data ? (
                  <div className="teammbrs_cnt_row">
                    <div className="teammbrs_cnt_img">
                      <Skeleton
                        circle
                        width="45px"
                        height="45px"
                        containerClassName="avatar-skeleton"
                        count={4}
                      />
                    </div>
                    <div className="teammbrs_cnt_name_dtl">
                      <div className="teammbrs_cnt_name">
                        <Skeleton count={9} />
                      </div>
                    </div>
                    <div className="teammbrs_cnt_img">
                      <Skeleton
                        circle
                        width="45px"
                        height="45px"
                        containerClassName="avatar-skeleton"
                        count={4}
                      />
                    </div>
                  </div>
                ) : packageOverview?.data?.length === 0 ? (
                  <div className="no-data-div">
                    <div className="no-data-div-image">
                      <img src="/images/nodata-image.png" alt="" />
                    </div>
                    <p>{t("noDataFound")}</p>
                  </div>
                ) : (
                  packageOverview?.data?.map((item, index) => (
                    <div key={index}>
                      <div className="teammbrs_cnt_row">
                        <div className="teammbrs_cnt_img">
                          <img
                            src={item.image ?? "/images/package.png"}
                            alt=""
                          />
                        </div>
                        <div className="teammbrs_cnt_name_dtl">
                          <div className="teammbrs_cnt_name">
                            {item.name}
                            <span>{item.username}</span>
                          </div>
                        </div>
                        <div className="top_recuirters_num">
                          <span>{item.count}</span>
                        </div>
                      </div>
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

export default TeamMembersEarningSection;
