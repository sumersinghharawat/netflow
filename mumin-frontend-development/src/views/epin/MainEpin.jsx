import React, { useState } from "react";
import OptionsButtton from "../../components/Common/buttons/OptionsButton";
import EpinTiles from "../../components/Epin/EpinTiles";
import EpinTable from "../../components/Epin/EpinTable";
import EpinPurchase from "../../components/Epin/EpinPurchase";
import { NavLink } from "react-router-dom";
import { ApiHook } from "../../hooks/apiHook";
import EpinRequest from "../../components/Epin/EpinRequest";
import EpinTransfer from "../../components/Epin/EpinTransfer";
import { useSelector } from "react-redux";
import { useTranslation } from "react-i18next";

const EpinLayout = () => {
  const userSelectedCurrency = useSelector(
    (state) => state.user?.selectedCurrency
  );
  const [showEpinPurchase, setShowEpinPurchase] = useState(false);
  const [showEpinRequest, setShowEpinRequest] = useState(false);
  const [showEpinTransfer, setShowEpinTransfer] = useState(false);
  const [selectedPending, setSelectedPending] = useState(false);
  const { t } = useTranslation();
  const conversionFactor = useSelector(
    (state) => state?.user?.conversionFactor
  );
  const style = {
    position: "absolute",
    inset: "0px auto auto 0px, margin: 0px",
    transform: "translate(140px, 42px)",
  };
  const epinTiles = ApiHook.CallEpinTiles();

  const handleEpinPurchase = () => {
    setShowEpinPurchase((prevShowEpinPurchase) => !prevShowEpinPurchase);
  };
  const handleEpinRequest = () => {
    setShowEpinRequest((prevShowEpinRequest) => !prevShowEpinRequest);
  };
  const handleEpinTransfer = () => {
    setShowEpinTransfer((prevShowEpinTransfer) => !prevShowEpinTransfer);
  };

  return (
    <>
      <div className="page_head_top">{t("epin")}</div>
      <div className="ewallet_top_btn_sec">
        <div className="row justify-content-between">
          <div className="col-md-4">
            <NavLink to={"/e-wallet"} className="btn_ewallt_page">
              {t("ewallet")}
            </NavLink>
            <NavLink className="btn_ewallt_page" activeclassname="active">
              {t("epin")}
            </NavLink>
          </div>
          <div className="col-md-4 text-end">
            <OptionsButtton
              title={"ePinPurchase"}
              handleOpen={handleEpinPurchase}
              handleRequest={handleEpinRequest}
              handleTransfer={handleEpinTransfer}
              style={style}
              type={"epin"}
            />
          </div>
        </div>
      </div>
      <EpinTiles
        data={epinTiles?.data?.epinTiles}
        conversionFactor={conversionFactor}
        currency={userSelectedCurrency}
      />
      <EpinTable
        type={"epin"}
        selectedPending={selectedPending}
        setSelectedPending={setSelectedPending}
      />
      <EpinPurchase
        show={showEpinPurchase}
        handleClose={handleEpinPurchase}
        amounts={epinTiles?.data?.epinAmounts}
        balance={epinTiles?.data?.ewalletBalance}
        currency={userSelectedCurrency}
        conversionFactor={conversionFactor}
      />
      <EpinRequest
        show={showEpinRequest}
        handleClose={handleEpinRequest}
        amounts={epinTiles?.data?.epinAmounts}
        conversionFactor={conversionFactor}
        selectedPending={selectedPending}
        setSelectedPending={setSelectedPending}
      />
      <EpinTransfer
        show={showEpinTransfer}
        handleClose={handleEpinTransfer}
      />
    </>
  );
};

export default EpinLayout;
