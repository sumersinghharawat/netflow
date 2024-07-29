import React, { useState } from "react";
import OptionsButton from "../../components/Common/buttons/OptionsButton";
import EwalletTiles from "../../components/Ewallet/EwalletTiles";
import EwalletTable from "../../components/Ewallet/EwalletTable";
import FundTransfer from "../../components/Ewallet/FundTransfer";
import { NavLink } from "react-router-dom";
import { ApiHook } from "../../hooks/apiHook";
import { useTranslation } from "react-i18next";
import { useSelector } from "react-redux";

const EwalletLayout = () => {
  const [showTransfer, setShowTransfer] = useState(false);
  const [currentPage, setCurrentPage] = useState(1);
  const { t } = useTranslation();
  const style = {
    position: "absolute",
    inset: "0px auto auto 0px, margin: 0px",
    transform: "translate(190px, 42px)",
  };

  const handleEwalletTransfer = () => {
    setShowTransfer((prevShowTransfer) => !prevShowTransfer);
  };
  const ewallet = ApiHook.CallEwalletTiles();
  const userSelectedCurrency = useSelector(
    (state) => state.user?.selectedCurrency
  );
  const conversionFactor = useSelector(
    (state) => state?.user?.conversionFactor
  );
  const moduleStatus = useSelector(
    (state) => state.dashboard?.appLayout?.moduleStatus
  );
  
  return (
    <>
      <div className="page_head_top">{t("ewallet")}</div>
      <div className="ewallet_top_btn_sec">
        <div className="row justify-content-between">
          <div className="col-md-4">
            {!!moduleStatus?.pin_status && (
              <>
                <NavLink className="btn_ewallt_page" activeclassname="active">
                  {t("ewallet")}
                </NavLink>
                <NavLink to={"/e-pin"} className="btn_ewallt_page">
                  {t("epin")}
                </NavLink>
              </>
            )}
          </div>
          <div className="col-md-4 text-end">
            <OptionsButton
              title={"ewallet_fund_transfer"}
              handleOpen={handleEwalletTransfer}
              style={style}
              type={"ewallet"}
            />
          </div>
        </div>
      </div>
      <EwalletTiles
        tiles={ewallet?.data}
        currency={userSelectedCurrency}
        conversionFactor={conversionFactor}
      />
      <EwalletTable
        currentPage={currentPage}
        setCurrentPage={setCurrentPage}
        currency={userSelectedCurrency}
      />
      <FundTransfer
        balance={ewallet?.data?.balance}
        show={showTransfer}
        handleClose={handleEwalletTransfer}
        currency={userSelectedCurrency}
      />
    </>
  );
};

export default EwalletLayout;
