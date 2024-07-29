import React from "react";
import { useTranslation } from "react-i18next";
import { useSelector } from "react-redux";
import CurrencyConverter from "../../Currency/CurrencyConverter";

const CheckoutOrderSummary = ({ handleNext, totalAmount, items }) => {
  const { t } = useTranslation();
  const userSelectedCurrency = useSelector(
    (state) => state.user?.selectedCurrency
  );
  const conversionFactor = useSelector(
    (state) => state?.user?.conversionFactor
  );
  return (
    <>
      <div className="checkout_list_scrl">
        {items?.map((product, index) => (
          <div
            className="checkout_contant_cart_row checkout_odr_summry"
            key={index}
          >
            <div className="checkout_cnt_image">
              <img src={product.image ?? "/images/product2.jpg"} alt="" />
            </div>
            <div className="checkout_cnt_product">
              <span>{t("product_name")}</span>
              <strong>{product.name}</strong>
            </div>
            <div className="checkout_cnt_qty">
              <span>{t("quantity")}</span>
              <div className="checkout_cnt_qty_btn">{product.quantity}</div>
            </div>
            <div className="checkout_cnt_ttl_amnt">
              <span>{`${t("totalAmount")}: `}</span>
              <strong>{`${userSelectedCurrency.symbolLeft} ${CurrencyConverter(
                product.price * product.quantity,
                conversionFactor
              )}`}</strong>
            </div>
          </div>
        ))}
      </div>
      <div className="checkout_cnt_ttl_amnt">
        <span>{`${t("totalAmount")}: `}</span>
        <strong>{`${userSelectedCurrency.symbolLeft} ${CurrencyConverter(
          totalAmount,
          conversionFactor
        )}`}</strong>
      </div>

      <div className="checkout_continuew_btn">
        <button
          className="btn btn-primary checkout_cnt_btn"
          onClick={handleNext}
        >
          {t("continue")}
        </button>
      </div>
    </>
  );
};

export default CheckoutOrderSummary;
