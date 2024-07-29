import React from "react";
import { useTranslation } from "react-i18next";
import RenewComponent from "../../components/Renew/RenewComponent";
import { ApiHook } from "../../hooks/apiHook";
import { useSelector } from "react-redux";

const Renew = () => {
  const { t } = useTranslation();
  const userData = useSelector((state) => state.user?.loginResponse?.user);
  const userSelectedCurrency = useSelector(
    (state) => state.user?.selectedCurrency
  );

  //------------------------------------------ API ----------------------------------------
  
  const data = ApiHook.CallGetSubscriptionDetails();

  return (
    <>
      <div className="page_head_top">{t("renew")}</div>
      <RenewComponent
        data = {data}
        userData = {userData ? JSON.parse(userData) : null}
        currency = {userSelectedCurrency}
      />
    </>
  );
};

export default Renew;
