import React from "react";
import { useTranslation } from "react-i18next";
import UpgradeComponents from "../../components/upgrade/UpgradeComponents";
import { ApiHook } from "../../hooks/apiHook";
import UpgradePaymentModal from "../../components/upgrade/UpgradePaymentModal";
import { useState } from "react";
import { useSelector } from "react-redux";

const Upgrade = () => {
  const { t } = useTranslation();
  const [show, setShow] = useState(false);
  const [selectedUpgradePlan, setSelectedUpgradePlan] = useState();
  const conversionFactor = useSelector(
    (state) => state?.user?.conversionFactor
  );
  const userSelectedCurrency = useSelector(
    (state) => state.user?.selectedCurrency
  );
  const handleUpgradeClick = (plan) => {
    setShow(true);
    setSelectedUpgradePlan(plan);
  };
  const productsList = ApiHook.CallGetUpgradeProducts();
  const currentProduct = productsList?.find((item) => item.upgradable === 1);

  return (
    <>
      <div className="page_head_top">{t("upgrade")}</div>
      <UpgradeComponents
        data={productsList}
        handleUpgradeClick={handleUpgradeClick}
        currency={userSelectedCurrency}
        conversionFactor={conversionFactor}
      />
      <UpgradePaymentModal
        show={show}
        setShow={setShow}
        currentProduct={currentProduct}
        data={selectedUpgradePlan}
        currency={userSelectedCurrency}
        conversionFactor={conversionFactor}
      />
    </>
  );
};

export default Upgrade;
