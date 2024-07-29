import React from "react";
import { ApiHook } from "../../hooks/apiHook";
import { useSelector } from "react-redux";
import { useTranslation } from "react-i18next";
import CurrencyConverter from "../../Currency/CurrencyConverter";
import { useQueryClient } from "@tanstack/react-query";
import { useNavigate } from "react-router";

const CheckoutPackage = ({ items, totalAmount, handleNext }) => {
  const { t } = useTranslation();
  const queryClient = useQueryClient();
  const navigate = useNavigate();

  const addCartMutation = ApiHook.CallAddToCart();
  const decrementCartMutation = ApiHook.CallDecrementCartItem();
  const removeCartMutation = ApiHook.CallRemoveCartItem();

  const userSelectedCurrency = useSelector(
    (state) => state.user?.selectedCurrency
  );
  const conversionFactor = useSelector(
    (state) => state?.user?.conversionFactor
  );

  if (items?.length === 0) {
    navigate("/shopping");
  }

  const handleQuantityChange = (event, packageId) => {
    if (event.target.id === "plus") {
      addCartMutation.mutate(
        { packageId: packageId },
        {
          onSuccess: (res) => {
            if (res.status) {
              queryClient.invalidateQueries({ queryKey: ["cart-items"] });
            }
          },
        }
      );
    } else if (event.target.id === "minus") {
      decrementCartMutation.mutate(
        { packageId: packageId },
        {
          onSuccess: (res) => {
            if (res.status) {
              queryClient.invalidateQueries({ queryKey: ["cart-items"] });
            }else{
              queryClient.invalidateQueries({ queryKey: ["cart-items"] });
            }
          },
        }
      );
    }
  };

  const handleRemoveCartItem = (packageId) => {
    removeCartMutation.mutate(
      { packageId: packageId },
      {
        onSuccess: (res) => {
          if (res.status) {
            queryClient.invalidateQueries({ queryKey: ["cart-items"] });
            if (items?.data?.length === 0) {
              navigate("/shopping");
            }
          }
        },
      }
    );
  };

  return (
    <>
      <div className="checkout_list_scrl">
        {items?.map((product, index) => (
          <div className="checkout_contant_cart_row" key={index}>
            <div className="checkout_cnt_image">
              <img src={product.image ?? "/images/product2.jpg"} alt="" />
            </div>
            <div className="checkout_cnt_product">
              <span>{t("product_name")}</span>
              <strong>{product.name}</strong>
            </div>
            <div className="checkout_cnt_price">
              <span>{t("price")}</span>
              <strong>{`${userSelectedCurrency.symbolLeft} ${CurrencyConverter(
                product.price,
                conversionFactor
              )}`}</strong>
            </div>
            <div className="checkout_cnt_qty">
              <span>{t("quantity")}</span>
              <div className="checkout_cnt_qty_btn_sec">
                <button
                  id="minus"
                  className="checkout_cnt_qty_btn"
                  onClick={(e) => handleQuantityChange(e, product?.packageId)}
                >
                  <i id="minus" className="fa fa-minus"></i>
                </button>
                <input
                  className="checkout_cnt_qty_input"
                  type="text"
                  value={product.quantity}
                  onChange={(e) => handleQuantityChange(e, product?.packageId)}
                />
                <button
                  id="plus"
                  className="checkout_cnt_qty_btn"
                  onClick={(e) => handleQuantityChange(e, product?.packageId)}
                >
                  <i id="plus" className="fa fa-plus"></i>
                </button>
              </div>
            </div>
            <div className="checkout_cnt_ttl_amnt">
              <span>{t("totalAmount")}</span>
              <strong>{`${userSelectedCurrency.symbolLeft} ${CurrencyConverter(
                product.price * product.quantity,
                conversionFactor
              )}`}</strong>
            </div>
            <div className="checkout_cnt_action_btn_sec">
              <a
                href="#"
                className="btn_chekcout_row"
                onClick={() => handleRemoveCartItem(product?.packageId)}
              >
                <i className="fa fa-trash"></i>
              </a>
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

export default CheckoutPackage;
