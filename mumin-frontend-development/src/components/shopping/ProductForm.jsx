import React from "react";
import { useTranslation } from "react-i18next";
import { useNavigate } from "react-router";
import CurrencyConverter from "../../Currency/CurrencyConverter";
import { useSelector } from "react-redux";

const ProductForm = ({ product }) => {
  const { t } = useTranslation();
  const navigate = useNavigate();

  const userSelectedCurrency = useSelector(
    (state) => state.user?.selectedCurrency
  );
  const conversionFactor = useSelector(
    (state) => state?.user?.conversionFactor
  );

  const handleBack = () => {
    navigate("/shopping");
  };

  return (
    <div className="productFormSec">
      <div className="mb-3">
        <label htmlFor="quantity" className="form-label">
          {t("quantity")}
        </label>
        <input
          type="text"
          className="form-control"
          id="quantity"
          placeholder="1"
          disabled
        />
      </div>
      <div className="mb-3">
        <label htmlFor="price" className="form-label">
          {t("price")}
        </label>
        <input
          type="text"
          className="form-control"
          id="price"
          placeholder={`${userSelectedCurrency?.symbolLeft} ${CurrencyConverter(
            product?.price,
            conversionFactor
          )}`}
          disabled
        />
      </div>
      <div className="mb-3">
        <label htmlFor="totalPV" className="form-label">
          {t("total_pv")}
        </label>
        <input
          type="text"
          className="form-control"
          id="totalPV"
          placeholder={product?.pairValue}
          disabled
        />
      </div>
      <div className="mb-3">
        <label htmlFor="description" className="form-label">
          {t("description")}
        </label>
        <textarea
          type="text"
          className="form-control"
          id="description"
          placeholder={product?.description}
          disabled
        />
      </div>
      <button
        type="button"
        className="btn text-white float-start back mt-4 rounded-3"
        style={{ backgroundColor: "#2c008a" }}
        onClick={handleBack}
      >
        {t("back")}
      </button>
    </div>
  );
};

export default ProductForm;
