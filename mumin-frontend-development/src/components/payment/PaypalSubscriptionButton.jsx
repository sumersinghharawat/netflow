import React from "react";
import { PayPalScriptProvider, PayPalButtons } from "@paypal/react-paypal-js";
import { PAYPAL_CLIENT_ID } from "../../config/config";
import { toast } from "react-toastify";
import { useTranslation } from "react-i18next";
import { useNavigate } from "react-router";
import { ApiHook } from "../../hooks/apiHook";

const MyPayPalSubscriptionButton = ({ currency, data }) => {
  const { t } = useTranslation();
  const navigate = useNavigate();
  const subscriptionMutation = ApiHook.CallAutoSubscription();
  const subscriptionPlan = {
    plan_id: data?.paypalPlanId,
    start_time: new Date(Date.now() + 24 * 60 * 60 * 1000).toISOString(),
    quantity: 1,
    return_url: "http://192.168.6.13:3000/profile",
  };
  const paypalSubscriptionOptions = {
    "client-id": PAYPAL_CLIENT_ID,
    currency: currency,
    vault: true,
    intent: "subscription",
  };

  const handleSubscriptionApproval = (data, actions) => {
    console.log("Subscription Details:", data);
    if (data.subscriptionID && data.orderID) {
      const subscriptionData = {
        planId: subscriptionPlan.plan_id,
        data: data,
      };
      subscriptionMutation.mutate(subscriptionData, {
        onSuccess: (res) => {
          if (res.status) {
            toast.success(t("subscription_created"));
            navigate("/profile");
          } else {
            toast.error(res?.message);
          }
        },
      });
    }
  };

  const handleError = (err) => {
    console.error("PayPal error:", err);
    toast.error("An error occurred during subscription processing.");
    navigate("/dashboard");
  };

  return (
    <div>
      <PayPalScriptProvider options={paypalSubscriptionOptions}>
        <PayPalButtons
          style={{ layout: "vertical", label: "subscribe" }} // Change the layout to "vertical"
          createSubscription={(data, actions) => {
            return actions.subscription.create({
              plan_id: subscriptionPlan.plan_id,
              start_time: subscriptionPlan.start_time,
              quantity: subscriptionPlan.quantity,
            });
          }}
          onApprove={handleSubscriptionApproval}
          onError={handleError} // Add error handling callback
        />
      </PayPalScriptProvider>
    </div>
  );
};

export default MyPayPalSubscriptionButton;
