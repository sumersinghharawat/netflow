import React, { useState } from "react";
import { useSelector } from "react-redux";
import { NavLink } from "react-router-dom";
import CurrencyConverter from "../../Currency/CurrencyConverter";
import { ApiHook } from "../../hooks/apiHook";
import { useTranslation } from "react-i18next";
import { toast } from "react-toastify";
import { useQueryClient } from "@tanstack/react-query";
import Loader from "react-js-loader";

const ProductLists = ({ products }) => {
  const { t } = useTranslation();
  const queryClient = useQueryClient();
  const [cartId,setCartId] = useState('')
  const userSelectedCurrency = useSelector(
    (state) => state.user?.selectedCurrency
  );
  const conversionFactor = useSelector(
    (state) => state?.user?.conversionFactor
  );

  const addCartMutation = ApiHook.CallAddToCart();
  const handleAddToCart = (id) => {
    const data = {
      packageId: id,
    };
    setCartId(id)
    addCartMutation.mutate(data, {
      onSuccess: (res) => {
        if (res?.status) {
          queryClient.invalidateQueries({ queryKey: ["cart-items"] });
        } else {
          if (res?.data?.code) {
            toast.error(res?.data?.description);
          } else {
            toast.error(res?.message);
          }
        }
      },
    });
  };
  return (
    <div className="shoping_page_section">
      <div className="row">
        {products?.map((product, index) => (
          <div className="col-xl-3 col-lg-4 col-md-4" key={index}>
            <div className="product_box">
              <NavLink to={`/product-details/${product.id}`}>
                <div className="product_box_image">
                  <img
                    src={product.image ?? "/images/product3.jpg"}
                    alt="product"
                  />
                </div>
              </NavLink>
              <div className="product_box_content">
                <div className="product_box_head">{product.name}</div>
                <div className="product_box_category">{product.category}</div>
                <div className="product_box_amnt">{`${
                  userSelectedCurrency.symbolLeft
                } ${CurrencyConverter(product.price, conversionFactor)}`}</div>
              </div>
              <div className="product_box_btn_sec">
                <button
                  id={`btn-${index}`}
                  className="product_box_btn1"
                  onClick={() => handleAddToCart(product.id)}
                  disabled={addCartMutation.isLoading}
                >
                  {addCartMutation.isLoading && (cartId === product.id) && (
                    <div style={{ padding: "10px" }}>
                      <Loader type="bubble-top" bgColor={"white "} size={30} />
                    </div>
                  )}
                  <i className="fa fa-cart-shopping"></i> {t("add_to_cart")}
                </button>
                <NavLink
                  to={`/product-details/${product.id}`}
                  className="product_box_btn2"
                >
                  <i className="fa fa-eye"></i> {t("more_details")}
                </NavLink>
              </div>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
};

export default ProductLists;
