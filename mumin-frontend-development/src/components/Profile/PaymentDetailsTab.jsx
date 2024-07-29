import React, { useState } from "react";
import { useTranslation } from "react-i18next";
import SubmitButton from "../Common/buttons/SubmitButton";
import { ApiHook } from "../../hooks/apiHook";
import { toast } from "react-toastify";

const PaymentDetailsTab = ({ payment }) => {
  const { t } = useTranslation();
  const [paymentDetails, setPaymentDetails] = useState({});
  const [isEditModeEnabled, setIsEditModeEnabled] = useState(false);

  const paymentMutation = ApiHook.CallPaymentDetails();

  const handleChange = (event) => {
    const { name, value } = event.target;
    setPaymentDetails((prevCredentials) => ({
      ...prevCredentials,
      [name]: value,
    }));
  };
  const toggleEditMode = () => {
    setIsEditModeEnabled(!isEditModeEnabled);
  };

  const onSubmit = () => {
    paymentMutation.mutate(paymentDetails, {
      onSuccess: (res) => {
        toast.success(res?.data);
        setIsEditModeEnabled(false);
      },
    });
  };

  return (
    <div id="fourthTab" className="tabcontent">
      <div className="editSec">
        <div className={`editSec ${isEditModeEnabled ? "disabled" : ""}`}>
          <span
            style={{ textDecoration: "none", cursor: "pointer" }}
            onClick={toggleEditMode}
          >
            <i
              className="fa-solid fa-pen-to-square"
              style={{ color: "#32009c" }}
            ></i>
          </span>
        </div>
      </div>
      <h3>{t("paymentDetails")}</h3>
      <div className="tabcontent_form_section">
        {paymentDetails.paymentMethod === "5" && (
          <div className="mb-3 row tabBlockClass">
            <label className="col-sm-3 col-form-label labelWidthClass">
              {t("stripeAccount")}:
            </label>
            <div className="col-md-9 col-sm-12 col-12">
              <input
                name="stripeAccount"
                type="text"
                className="form-control"
                disabled={!isEditModeEnabled}
                onChange={handleChange}
              />
            </div>
          </div>
        )}
        {paymentDetails.paymentMethod === "6" && (
          <div className="mb-3 row tabBlockClass">
            <label className="col-sm-3 col-form-label labelWidthClass">
              {t("paypalAccount")}:
            </label>
            <div className="col-md-9 col-sm-12 col-12">
              <input
                name="paypalAccount"
                type="text"
                className="form-control"
                disabled={!isEditModeEnabled}
                onChange={handleChange}
              />
            </div>
          </div>
        )}
        <div className="mb-3 row tabBlockClass">
          <label className="col-sm-3 col-form-label labelWidthClass">
            {t("paymentMethod")}:
          </label>
          <div className="col-md-9 col-sm-12 col-12">
            <select
              name="paymentMethod" // Add a name to your select element
              className="form-select"
              disabled={!isEditModeEnabled}
              value={paymentDetails.paymentMethod || ""} // Set the value of the select based on state
              onChange={handleChange}
            >
              {payment?.options.map((option, key) => (
                <option key={key} value={option.id}>
                  {option.name}
                </option>
              ))}
            </select>
          </div>
        </div>
        <div
          className={`paymenytLinkBtn ${isEditModeEnabled ? "disabled" : ""}`}
        >
          {/* <button type="button" className="btn" disabled={!isEditModeEnabled}>{t('linkStripe')}</button> */}
          <SubmitButton
            id={"1"}
            isSubmitting={!isEditModeEnabled}
            click={onSubmit}
            text="update"
            className="btn"
          />
        </div>
      </div>
    </div>
  );
};

export default PaymentDetailsTab;
