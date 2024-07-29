import React from "react";
import { PayPalScriptProvider, PayPalButtons } from "@paypal/react-paypal-js";
import { PAYPAL_CLIENT_ID } from "../../config/config";
import { toast } from "react-toastify";
import { useNavigate } from "react-router";

const MyPayPalOrderButton = ({ currency, price, handleSubmit, paymentId }) => {
  const navigate = useNavigate();

  const paypalOptions = {
    "client-id": PAYPAL_CLIENT_ID,
    currency: currency,
  };

  const handleOrderApproval = (data, actions) => {
    console.log("payment Details:", data, "actions----", actions);
    if (data.orderID) {
      handleSubmit(paymentId);
    }
  };

  const handleError = (err) => {
    console.error("PayPal error:", err);
    toast.error(err);
    navigate("/dashboard");
  };

  return (
    <div>
      <PayPalScriptProvider options={paypalOptions}>
        <PayPalButtons
          style={{ layout: "vertical" }}
          createOrder={(data, actions) => {
            return actions.order.create({
              purchase_units: [
                {
                  amount: {
                    value: price,
                  },
                },
              ],
              intent: "CAPTURE",
            });
          }}
          onApprove={handleOrderApproval}
          onError={handleError} // Add error handling callback
        />
      </PayPalScriptProvider>
    </div>
  );
};

export default MyPayPalOrderButton;
