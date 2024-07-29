import React from "react";
import { Elements } from "@stripe/react-stripe-js";
import { loadStripe } from "@stripe/stripe-js";
import StripecheckoutForm from "../../components/payment/StripecheckoutForm";

function Stripe({
  clientSecret,
  totalAmount,
  action,
  handleClose,
  setclientSecret,
  handleSubmitFinish,
  publicKey,
}) {
  const stripePromise = loadStripe(publicKey);
  const options = {
    clientSecret: clientSecret,
  };
  return (
    <Elements stripe={stripePromise} options={options}>
      <StripecheckoutForm
        totalAmount={totalAmount}
        action={action}
        handleClose={handleClose}
        setclientSecret={setclientSecret}
        handleSubmitFinish={handleSubmitFinish}
      />
    </Elements>
  );
}

export default Stripe;
