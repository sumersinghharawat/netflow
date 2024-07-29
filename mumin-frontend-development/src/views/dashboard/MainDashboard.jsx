import React from "react";
import QuickBalance from "../../components/Dashboard/DashboardTiles";
import JoiningChart from "../../components/Dashboard/JoiningChart";
import TeamMembers from "../../components/Dashboard/TeamMembers";
import TeamMembersEarningSection from "../../components/Dashboard/TeamPerformance";
import EarningsExpenses from "../../components/Dashboard/Earnings";
import { ApiHook } from "../../hooks/apiHook";
import { useSelector } from "react-redux";
import { useTranslation } from "react-i18next";
import RankComponent from "../../components/Dashboard/RankComponent";

const Dashboard = () => {
  const { t } = useTranslation();
  const user = JSON.parse(localStorage.getItem("user"));
  const joiningChartData = useSelector(
    (state) => state.dashboard?.dashboardOne
  );
  const userSelectedCurrency = useSelector(
    (state) => state.user?.selectedCurrency
  );
  const conversionFactor = useSelector(
    (state) => state?.user?.conversionFactor
  );
  const moduleStatus = useSelector(
    (state) => state.dashboard?.appLayout?.moduleStatus
  );

  // --------------------------------------------- API -------------------------------------------------
  const dashboard = ApiHook.CallDashboardTiles();
  const dashboardDetails = ApiHook.CallDashboardDetails();

 
  return (
    <>
      <div className="page_head_top">{t("dashboard")}</div>
      <div className="center_content_head">
        <span>
          {t("welcome")} {user?.fullName}{" "}
        </span>
      </div>
      <QuickBalance
        tiles={dashboard?.data}
        currency={userSelectedCurrency}
        conversionFactor={conversionFactor}
      />
      <div className="joining_Teammbr_section">
        <div className="row">
          <JoiningChart charts={joiningChartData} />
          <TeamMembers members={dashboardDetails?.data?.newMembers} />
        </div>
      </div>
      <div className="team_members_earning_section">
        <div className="row">
          <TeamMembersEarningSection
            topEarners={dashboardDetails?.data?.topEarners}
            currency={userSelectedCurrency}
            conversionFactor={conversionFactor}
          />
          {!!moduleStatus?.rank_status && (
            <RankComponent
              ranks={dashboardDetails?.data?.ranks}
              currentRank={dashboardDetails?.data?.currentRank}
            />
          )}
          <EarningsExpenses
            earnings={dashboardDetails?.data?.earnings}
            currency={userSelectedCurrency}
            conversionFactor={conversionFactor}
          />
        </div>
      </div>
    </>
  );
};

export default Dashboard;
