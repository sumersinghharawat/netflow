import React, { useState } from "react";
import { useTranslation } from "react-i18next";
import { Link } from "react-router-dom";
import { ApiHook } from "../../hooks/apiHook";
import Skeleton from "react-loading-skeleton";

const RankingComponent = ({ ranks, currentRank }) => {
  const { t } = useTranslation();
  const [activeTab, setActiveTab] = useState("rank");
  const [rankIndex, setRankIndex] = useState(currentRank ?? 1); // Start with index 1 (current)
  const [rankCheck, setRankCheck] = useState(false);

  //--------------------------------- API -------------------------------------
  const rankOverview = ApiHook.CallRankOverview(rankCheck, setRankCheck);

  const handleTabChange = (tabId) => {
    if (tabId === "rank-overview") {
      setRankCheck(true);
    }
    setActiveTab(tabId);
  };

  const showPreviousRankingClick = () => {
    if (rankIndex <= ranks?.length && rankIndex > 1) {
      setRankIndex(rankIndex - 1);
    }
  };

  const showNextRankingClick = () => {
    if (ranks) {
      const currentIndex = rankIndex !== null ? rankIndex : 1;
      if (currentIndex < ranks.length) {
        setRankIndex(currentIndex + 1);
      }
    }
  };

  const rankTabs = ranks?.map((rank, index) => (
    <div
      key={rank.id}
      className={`ranking-icon rankingbox${index}`}
      style={{ display: rank.id === rankIndex ? "block" : "none" }}
    >
     <img src={rank.image ?? "/images/ranking-icon.png"} alt="" />
      <p>
        {rank.id < currentRank
          ? `${t("previous")}`
          : rank.id === currentRank
          ? `${t("current")}`
          : `${t("next")}`}{" "}
        {t("ranking")}
      </p>
      <span>{rank.name}</span>
    </div>
  ));

  return (
    <div className="col-md-4">
      <div className="ranking-box">
        <div className="joinings_viewBox_head">
          <h5>{t("ranking")}</h5>
        </div>
        <ul
          className="teamPerfomance_tab nav nav-tabs mb-3"
          id="ex1"
          role="tablist"
        >
          <li className="nav-item" role="presentation">
            <Link
              className={`nav-link ${activeTab === "rank" ? "active" : ""}`}
              id="ex1-tab-3"
              onClick={() => handleTabChange("rank")}
            >
              {t("rank")}
            </Link>
          </li>
          <li className="nav-item" role="presentation">
            <Link
              className={`nav-link ${
                activeTab === "rank-overview" ? "active" : ""
              }`}
              id="ex1-tab-3"
              onClick={() => handleTabChange("rank-overview")}
            >
              {t("rank_overview")}
            </Link>
          </li>
        </ul>
        <div className="tab-content" id="ex1-content">
          <div
            className={`tab-pane ${activeTab === "rank" ? "active" : ""}`}
            id="rank"
            role="tabpanel"
            aria-labelledby="rank"
          >
            {ranks ? (
              rankTabs
            ) : (
              <div className={"ranking-icon"}>
                <Skeleton
                  circle
                  width="100px"
                  height="100px"
                  containerClassName="avatar-skeleton"
                  count={1}
                  style={{ marginBottom: "7px", marginTop: "30px" }}
                />
                <Skeleton count={1} width={"220px"} />
                <Skeleton count={1} width={"150px"} />
              </div>
            )}
            {rankIndex > 1 && (
              <div className="previcon">
                <a href="#/" onClick={showPreviousRankingClick}>
                  <img src="/images/prev-icon.svg" alt="" />
                </a>
              </div>
            )}
            {rankIndex < ranks?.length && (
              <div className="nxt-icon">
                <a href="#/" onClick={showNextRankingClick}>
                  <img src="/images/nxt-icon.svg" alt="" />
                </a>
              </div>
            )}
          </div>
          <div
            className={`tab-pane ${
              activeTab === "rank-overview" ? "active" : ""
            }`}
            id="rank-overview"
            role="tabpanel"
            aria-labelledby="rank-overview"
          >
            <div className="top_recuirters_section">
              {!rankOverview.data ? (
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
              ) : (
                rankOverview.data?.map((member, index) => (
                  <div key={index} className="teammbrs_cnt_row">
                    <div className="teammbrs_cnt_img">
                      <img src={member.image ?? "/images/team3.png"} alt="" />
                    </div>
                    <div className="teammbrs_cnt_name_dtl">
                      <div className="teammbrs_cnt_name">{member.name}</div>
                      <div className="top_recuirters_num">
                        <span>{member.count}</span>
                      </div>
                    </div>
                  </div>
                ))
              )}
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default RankingComponent;
