import React from "react";
import UserDropdown from "./DashboardUserDropdown";
import DoughnutChart from "./payoutOverviewChart";
import LinkSection from "./UserLinks";
import { useSelector } from "react-redux";

const RightContentSection = ({ props }) => {
    const currency = useSelector(state=> state.user?.selectedCurrency);
    const conversionFactor= useSelector((state) => state.user?.conversionFactor);
  return (
    <>
      <UserDropdown props={props?.userProfile} />
      <DoughnutChart
        pending={props?.payoutDoughnut?.pending}
        approved={props?.payoutDoughnut?.approved}
        payoutPaid={props?.payoutOverview?.payoutPaid}
        currency={currency}
        conversionFactor={conversionFactor}
      />
      <LinkSection
        payoutTab={props?.payoutOverview}
        replicaLink={props?.replicaLink}
        leadCaptureLink={props?.leadCaptureLink}
        currency={currency}
        conversionFactor={conversionFactor}
      />
    </>
  );
};

export default RightContentSection;
