import React, { useState } from "react";
import { useTranslation } from "react-i18next";
import { NavLink } from "react-router-dom";
import CurrencyConverter from "../../Currency/CurrencyConverter";
import { useSelector } from "react-redux";
import { ApiHook } from "../../hooks/apiHook";
import { useQueryClient } from "@tanstack/react-query";

function ShoppingCart() {
  const { t } = useTranslation();
  const queryClient = useQueryClient();
  const [showCartItems, setShowCartItems] = useState(true);
  const userSelectedCurrency = useSelector(
    (state) => state.user?.selectedCurrency
  );
  const conversionFactor = useSelector(
    (state) => state?.user?.conversionFactor
  );

  //------------------------------------- API ---------------------------------------
  const items = ApiHook.CallCartItems(setShowCartItems);
  const addCartMutation = ApiHook.CallAddToCart();
  const decrementCartMutation = ApiHook.CallDecrementCartItem();
  const removeCartMutation = ApiHook.CallRemoveCartItem();

  const handleCartArrowClick = () => {
    setShowCartItems(!showCartItems);
  };

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
          }
        },
      }
    );
  };

  const totalAmount = items?.data?.reduce(
    (total, product) => total + product.price * product.quantity,
    0
  );

  const renderProducts = () => {
    return items?.data?.map((product, index) => (
      <tr key={index}>
        <td>
          <div className="profile_table">
            <img src={product?.image ?? "/images/product2.jpg"} alt="" />
          </div>
          {product?.name}
        </td>
        <td>{`${userSelectedCurrency.symbolLeft} ${CurrencyConverter(
          product.price,
          conversionFactor
        )}`}</td>
        <td>
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
              value={product?.quantity}
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
        </td>
        <td>{`${userSelectedCurrency.symbolLeft} ${CurrencyConverter(
          product.price * product.quantity,
          conversionFactor
        )}`}</td>
        <td>
          <button
            className="btn_chekcout_row"
            onClick={() => handleRemoveCartItem(product?.packageId)}
          >
            <i className="fa fa-trash"></i>
          </button>
        </td>
      </tr>
    ));
  };

  return (
    <>
      {items?.data?.length > 0 && (
        <div className="shopping_footer_cart">
          <div className="shopping_footer_cart_cnt">
            <div className="shopping_cart_arrow" onClick={handleCartArrowClick}>
              <i
                className={
                  showCartItems ? "fa fa-angle-down" : "fa fa-angle-up"
                }
              ></i>
            </div>
            <div className="shopping_cart_prd_dtl">
              <h5>
                {items?.data?.length} {t("products")}
              </h5>
              <p>{items?.data?.map((product) => product.name).join(", ")}</p>
            </div>
            <div className="shopping_cart_prd_amount">
              <p>{t("totalAmount")}</p>
              <h5>{`${userSelectedCurrency.symbolLeft} ${CurrencyConverter(
                totalAmount,
                conversionFactor
              )}`}</h5>
            </div>
            <div className="shopping_cart_prd_btn">
              <NavLink
                to={"/checkout"}
                className="btn btn-primary checkout_btn_cart"
              >
                {t("checkout")}
              </NavLink>
            </div>
          </div>
          <div
            className={`shopping_cart_item_showing ${
              showCartItems ? "show_mn" : ""
            }`}
          >
            <table>
              <thead>
                <tr>
                  <th>{t("product_name")}</th>
                  <th>{t("price")}</th>
                  <th>{t("quantity")}</th>
                  <th>{t("totalAmount")}</th>
                  <th>{t("action")}</th>
                </tr>
              </thead>
              <tbody>{renderProducts()}</tbody>
            </table>
          </div>
        </div>
      )}
    </>
  );
}

export default ShoppingCart;
