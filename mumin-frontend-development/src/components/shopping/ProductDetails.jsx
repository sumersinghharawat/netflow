import React from "react";
import ProductForm from "./ProductForm";
import { useTranslation } from "react-i18next";
import { ApiHook } from "../../hooks/apiHook";
import { useParams } from "react-router";

const ProductDetails = () => {
  const { t } = useTranslation();
  const params = useParams();

  // --------------------------------------- API -----------------------------------------------
  const productDetails = ApiHook.CallProductDetails(params.id);

  return (
    <div className="productBodySec">
      <div className="row">
        <div className="col-md-4">
          <div className="repurchaseBg">
            <h5>{productDetails.data?.name}</h5>
            <div className="imgSpaceProduct">
              <img
                src={productDetails.data?.image ?? "/images/product2.jpg"}
                alt=""
              />
            </div>
          </div>
        </div>
        <div className="col-md-8">
          <div className="purchseCartBg">
            <div className="productIdPvSec">
              <div className="catagorySec">
                <p>
                  <span>{`${t("category")} :`}</span>{" "}
                  {productDetails.data?.name}
                </p>
              </div>
              <div className="catagorySec">
                <p>
                  <span>{`${t("product_id")} :`}</span>{" "}
                  {productDetails.data?.productId}
                </p>
              </div>
            </div>
            <ProductForm product={productDetails.data} />
          </div>
        </div>
      </div>
    </div>
  );
};

export default ProductDetails;
