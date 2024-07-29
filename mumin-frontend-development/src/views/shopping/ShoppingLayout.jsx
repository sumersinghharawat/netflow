import React from "react";
import ProductLists from "../../components/shopping/ProductLists";
import { ApiHook } from "../../hooks/apiHook";
import { Link } from "react-router-dom";
import { useTranslation } from "react-i18next";

const ShoppingLayout = () => {
  const { t } = useTranslation();
  const items = ApiHook.CallRepurchaseItems();
  return (
    <>
      <div className="page_head_top">{t("shopping")}</div>
      <div className="ewallet_top_btn_sec">
        <div className="row justify-content-between">
          <div className="col-md-4 text-end">
            <div className="dropdown btn-group top_right_pop_btn_position">
              <Link to={"/repurchase-report"} className="top_righ_pop_btn">
                {t("repurchase_report")}
              </Link>
            </div>
          </div>
        </div>
      </div>
      <ProductLists products={items?.data} />
    </>
  );
};

export default ShoppingLayout;
