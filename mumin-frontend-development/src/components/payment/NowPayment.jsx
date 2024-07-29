import {
  useStripe,
  useElements,
  PaymentElement,
} from "@stripe/react-stripe-js";
import { useState } from "react";
import CurrencyConverter from "../../Currency/CurrencyConverter";
import { useSelector } from "react-redux";
import Alert from "@mui/material/Alert";
import { useQueryClient } from "@tanstack/react-query";
import { useTranslation } from "react-i18next";

const NowPayment = ({
  totalAmount,
  action,
  handleSubmitFinish,
}) => {
  const { t } = useTranslation();
  const Currency = useSelector((state) => state.user?.selectedCurrency);
  const stripe = useStripe();
  const elements = useElements();
  const paymentElementOptions = {
    layout: "tabs",
  };
  const queryClient = useQueryClient();
  const [error, seterror] = useState("");
  const [loader, setLoader] = useState(false);
  const [btnDisabled, setbtnDisabled] = useState(true);
  const handleSubmit = async (event) => {
    setLoader(true);
    event.preventDefault();
    if (!stripe || !elements) {
      setLoader(false);
      return;
    }

    const result = await stripe.confirmPayment({
      elements,
      confirmParams: {
        return_url: "http://localhost:3000/payment-success",
      },
      redirect: "if_required",
    });

    if (result.error) {
      setLoader(false);
      seterror(result.error.message);
      console.log(result.error.message);
    } else {
      console.log("result.paymentIntent ====== ", result.paymentIntent);
      if (result.paymentIntent && result.paymentIntent.status === "succeeded") {
        if (action === "register") {
          const response = await handleSubmitFinish(result.paymentIntent.id);
          console.log("response inside stripe ===>>>>", response);
        }
      } else {
        setLoader(false);
      }
    }
  };
  const elementRendered = () => {
    setbtnDisabled(false);
  };
  return (
    <form onSubmit={handleSubmit}>
      {error && (
        <Alert severity="error" className="mb-1">
          {error}
        </Alert>
      )}

      <h5>
        Total Amount :{" "}
        <span>
          {" "}
          {`${Currency.symbolLeft} ${CurrencyConverter(totalAmount)}`}
        </span>
      </h5>

      <PaymentElement
        options={paymentElementOptions}
        onReady={elementRendered}
      />
      <button
        disabled={btnDisabled || loader}
        className="btn btn-primary submit_btn mt-3"
      >
        {loader ? (
          <div>
            <i className="fa-solid fa-spinner fa-spin" /> {t("processing")}
          </div>
        ) : action === "register" ? (
          t("finish")
        ) : (
          t("submit")
        )}
      </button>
    </form>
  );
};

export default NowPayment;
