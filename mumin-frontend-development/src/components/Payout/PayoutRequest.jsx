import React, { useState } from "react";
import { Offcanvas, Table, Form } from "react-bootstrap";
import { useTranslation } from "react-i18next";
import { ApiHook } from "../../hooks/apiHook";
import SubmitButton from "../Common/buttons/SubmitButton";
import { useQueryClient } from "@tanstack/react-query";
import { useSelector } from "react-redux";
import CurrencyConverter from "../../Currency/CurrencyConverter";
import { reverseNumberDisplay } from "../../utils/currencyNumberDisplay";
import { toast } from "react-toastify";
import CurrencyInput from "react-currency-input-field";

const PayoutRequest = ({ show, handleClose, data }) => {
  const { t } = useTranslation();
  const queryClient = useQueryClient();
  const [payoutData, setPayoutData] = useState({
    payoutAmount: "",
    transactionPassword: "",
  });
  const [errorMessage, setErrorMessage] = useState({
    payoutAmount: null,
    transactionPassword: null,
  });
  const userSelectedCurrency = useSelector(
    (state) => state?.user?.selectedCurrency
  );
  const conversionFactor = useSelector(
    (state) => state?.user?.conversionFactor
  );
  const defaultCurrencyJson = useSelector(
    (state) => state?.user?.loginResponse?.defaultCurrency
  );

  const defaultCurrency = defaultCurrencyJson
    ? JSON.parse(defaultCurrencyJson)
    : null;
  const payoutMutation = ApiHook.CallPayoutRequest();

  const handleChange = (e) => {
    const { id, value } = e.target;
    setPayoutData((prevData) => ({
      ...prevData,
      [id]: value,
    }));
    setErrorMessage((prevData) => ({
      ...prevData,
      [id]: null,
    }));
    if (value === null || value === "") {
      setErrorMessage((prev) => ({
        ...prev,
        [id]: "*Required",
      }));
    }
  };
  const handleAmountChange = (amountCount) => {
    if (amountCount?.length === 0) {
      setErrorMessage((prev) => ({
        ...prev,
        payoutAmount: "*Required",
      }));
    } else {
      setErrorMessage((prevData) => ({
        ...prevData,
        payoutAmount: null,
      }));
    }

    setPayoutData((prevData) => ({
      ...prevData,
      amountCount,
    }));
  };
  const isFormValid = () => {
    return (
      payoutData?.payoutAmount > 0 &&
      payoutData?.transactionPassword.trim() !== ""
    );
  };
  const handleSubmit = async (e) => {
    e.preventDefault();
    if (!isFormValid()) {
      return;
    }
    let convertAmount;
    if (userSelectedCurrency.id === defaultCurrency.id) {
      convertAmount = reverseNumberDisplay(
        CurrencyConverter(payoutData?.payoutAmount, conversionFactor, 0)
      );
    } else {
      convertAmount = reverseNumberDisplay(
        CurrencyConverter(payoutData?.payoutAmount, conversionFactor, 1)
      );
    }
    const postData = {
      payoutAmount: convertAmount,
      transactionPassword: payoutData?.transactionPassword,
    };
    await payoutMutation.mutateAsync(postData, {
      onSuccess: (res) => {
        if (res.status === 200) {
          setErrorMessage({
            username: null,
            transactionPassword: null,
          });
          setPayoutData({
            payoutAmount: "",
            transactionPassword: "",
          });
          queryClient.invalidateQueries({
            queryKey: ["payout-request-details"],
          });
          queryClient.invalidateQueries({
            queryKey: ["payout-details"],
          });
          queryClient.invalidateQueries({
            queryKey: ["payout-tiles"],
          });
          handleClose(false);
        } else {
          if (res?.data?.code === 1015) {
            setErrorMessage((prevErrors) => ({
              ...prevErrors,
              transactionPassword: t(res?.data?.description),
            }));
          } else if (res?.data?.code === 1027) {
            setErrorMessage((prevErrors) => ({
              ...prevErrors,
              payoutAmount: t(res?.data?.description),
            }));
          } else if (res?.data?.code === 1073) {
            setErrorMessage((prevErrors) => ({
              ...prevErrors,
              transactionPassword: t(res?.data?.description),
            }));
          } else if (res?.data?.code === 1028) {
            setErrorMessage((prevErrors) => ({
              ...prevErrors,
              payoutAmount: t(res?.data?.description),
            }));
          } else if (res?.data?.code === 1025) {
            setErrorMessage((prevErrors) => ({
              ...prevErrors,
              payoutAmount: t(res?.data?.description),
            }));
          } else if (res?.data?.code) {
            toast.error(res?.data?.description);
          } else {
            toast.error(res?.data?.message);
          }
        }
      },
    });
  };
  return (
    <Offcanvas
      show={show}
      placement="end"
      onHide={handleClose}
      scroll={true}
      backdrop={true}
    >
      <Offcanvas.Header closeButton>
        <Offcanvas.Title>{t("payoutRequest")}</Offcanvas.Title>
      </Offcanvas.Header>
      <Offcanvas.Body>
        <Form>
          <Form.Group className="mb-3 amount-field">
            <Form.Label>{t("withdrawalAmount")}</Form.Label>
            <div className="input-group">
              <Form.Control as="select" disabled className="max-40">
                <option value="">{userSelectedCurrency?.symbolLeft}</option>
              </Form.Control>
              <CurrencyInput
                name="payoutAmount"
                id="payoutAmount"
                placeholder={t("amount")}
                value={payoutData?.payoutAmount}
                onValueChange={(value, id) =>
                  handleChange({ target: { value, id } })
                }
                required
                allowNegativeValue={false}
              />
              <div className="number-field-invalid-feedback">
                {errorMessage.payoutAmount}
              </div>
            </div>
          </Form.Group>
          <Form.Group className="mb-3">
            <Form.Label>{t("transactionPassword")} *</Form.Label>
            <Form.Control
              id="transactionPassword"
              type="password"
              placeholder={t("transactionPassword")}
              onChange={(e) => handleChange(e)}
              value={payoutData?.transactionPassword}
              required
              isInvalid={errorMessage?.transactionPassword !== null}
            />
            <Form.Control.Feedback type="invalid">
              {errorMessage?.transactionPassword}
            </Form.Control.Feedback>
          </Form.Group>
          <SubmitButton
            isSubmitting={!isFormValid() || payoutMutation.isLoading}
            click={handleSubmit}
            text={payoutMutation.isLoading ? "Submitting..." : "Submit"}
            className={"btn btn-primary submit_btn"}
          />
        </Form>
        {data?.payoutFee !== 0 && (
          <p>
            {t("an_additional_amount_of")}
            <span className="popAmntVal">
              {data?.payoutFeeMode === "percentage"
                ? `${data?.payoutFee} %`
                : `${userSelectedCurrency?.symbolLeft} ${CurrencyConverter(
                    Number(data?.payoutFee),
                    conversionFactor
                  )}`}
            </span>
            {t("will_be_debited_as_payout_fee")}
          </p>
        )}
        <Table className="table payout_pop_table">
          <thead>
            <tr>
              <th colSpan="2">{t("particulars")}</th>
            </tr>
          </thead>
          <tbody>
            {data &&
              Object.entries(data).map(([key, value]) => (
                <tr key={key}>
                  <td>{t(`${key}`)}</td>
                  {key === "defaultCurrency" && (
                    <td>{`(${userSelectedCurrency?.symbolLeft})`}</td>
                  )}
                  {key === "payoutMethod" && <td>{value}</td>}
                  {key === "requestValidity" && (
                    <td>{`${value} ${t("days")}`}</td>
                  )}
                  {key === "ewalletBalance" && (
                    <td>{`${
                      userSelectedCurrency?.symbolLeft
                    } ${CurrencyConverter(
                      Number(value),
                      conversionFactor
                    )}`}</td>
                  )}
                  {key === "maxPayoutAmount" && (
                    <td>{`${
                      userSelectedCurrency?.symbolLeft
                    } ${CurrencyConverter(
                      Number(value),
                      conversionFactor
                    )}`}</td>
                  )}
                  {key === "minPayoutAmount" && (
                    <td>{`${
                      userSelectedCurrency?.symbolLeft
                    } ${CurrencyConverter(
                      Number(value),
                      conversionFactor
                    )}`}</td>
                  )}
                  {key === "payoutFee" && (
                    <td>
                      {data.payoutFeeMode === "percentage" ? (
                        <span className="popAmntVal">{`${data.payoutFee} %`}</span>
                      ) : (
                        `${
                          userSelectedCurrency?.symbolLeft
                        } ${CurrencyConverter(
                          Number(data.payoutFee),
                          conversionFactor
                        )}`
                      )}
                    </td>
                  )}
                  {key === "requestInProgress" && (
                    <td>{`${
                      userSelectedCurrency?.symbolLeft
                    } ${CurrencyConverter(
                      Number(value),
                      conversionFactor
                    )}`}</td>
                  )}
                  {key === "totalPaid" && (
                    <td>{`${
                      userSelectedCurrency?.symbolLeft
                    } ${CurrencyConverter(
                      Number(value),
                      conversionFactor
                    )}`}</td>
                  )}
                  {key === "availablePayoutAmount" && (
                    <td>{`${
                      userSelectedCurrency?.symbolLeft
                    } ${CurrencyConverter(
                      Number(value),
                      conversionFactor
                    )}`}</td>
                  )}
                  {key === "payoutFeeMode" && <td>{t(value)}</td>}
                </tr>
              ))}
          </tbody>
        </Table>
      </Offcanvas.Body>
    </Offcanvas>
  );
};

export default PayoutRequest;
