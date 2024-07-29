import React, { useState } from "react";
import PayoutTiles from "../../components/Payout/PayoutTiles";
import PayoutTable from "../../components/Payout/PayoutTable";
import SubmitButton from "../../components/Common/buttons/SubmitButton";
import PayoutRequest from "../../components/Payout/PayoutRequest";
import { ApiHook } from "../../hooks/apiHook";
import { useTranslation } from "react-i18next";

const PayoutLayout = () => {
  const { t } = useTranslation();
  const [showPayout, setShowPayout] = useState(false);
  const [currentPage, setCurrentPage] = useState(1);
  const [activeTab, setActiveTab] = useState([
    { value: "paid", label: `${t("paid")}` },
    { value: "requested", label: `${t("requested")}` },
    { value: "approved", label: `${t("approved")}` },
    { value: "rejected", label: `${t("rejected")}` },
  ]);
  const activeTabValues = activeTab.map(item => item.value);
  const [itemsPerPage, setItemsPerPage] = useState(10);

  const handlePayout = () => {
    setShowPayout((prevShowPayout) => !prevShowPayout);
  };

  //--------------------------------- API --------------------------------
  const payoutDetails = ApiHook.CallPayoutDetails(
    currentPage,
    itemsPerPage,
    activeTabValues.map(item => `"${item}"`)
  );
  const requestDetails = ApiHook.CallPayoutRequestDetails();
  const tiles = ApiHook.CallPayoutTiles();

  return (
    <>
      <div className="page_head_top">{t("payout")}</div>
      <div className="ewallet_top_btn_sec">
        <div className="row justify-content-between">
          <div className="col-md-4"></div>
          <div className="col-md-4 text-end ">
            <div className="dropdown btn-group top_right_pop_btn_position">
              <SubmitButton
                isSubmitting=""
                click={handlePayout}
                text={"payoutRequest"}
                className="top_righ_pop_btn"
              />
            </div>
          </div>
        </div>
      </div>
      <PayoutTiles
        percentage={tiles?.data?.tilePercentages}
        tiles={tiles?.data?.payoutOverviewTotal}
      />
      <PayoutTable
        data={payoutDetails?.data?.payoutDetails}
        type={"payout"}
        setActiveTab={setActiveTab}
        setCurrentPage={setCurrentPage}
        currentPage={currentPage}
        itemsPerPage={itemsPerPage}
        setItemsPerPage={setItemsPerPage}
        activeTab={activeTab}
      />
      <PayoutRequest
        show={showPayout}
        handleClose={handlePayout}
        data={requestDetails?.data}
      />
    </>
  );
};

export default PayoutLayout;
