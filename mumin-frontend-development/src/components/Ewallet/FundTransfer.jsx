import React, { useState } from "react";
import { Offcanvas, Form } from "react-bootstrap";
import { useSelector } from "react-redux";
import { ApiHook } from "../../hooks/apiHook";
import { useQueryClient } from "@tanstack/react-query";
import SubmitButton from "../Common/buttons/SubmitButton";
import { toast } from "react-toastify";
import { useTranslation } from "react-i18next";
import CurrencyConverter from "../../Currency/CurrencyConverter";
import { reverseNumberDisplay } from "../../utils/currencyNumberDisplay";
import CurrencyInput from "react-currency-input-field";

const EwalletTransferOffcanvas = ({ balance, show, handleClose, currency }) => {
  const { t } = useTranslation();
  const [transferData, setTransferData] = useState({
    username: "",
    amount: "",
    transactionPassword: "",
  });
  const transFee = useSelector(
    (state) => state.dashboard?.appLayout?.configuration?.transFee
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
  const queryClient = useQueryClient();
  const [errorMessage, setErrorMessage] = useState({
    username: null,
    amount: null,
    transactionPassword: null,
  });

  const handleChange = (e) => {
    const { id, value } = e.target;
    setTransferData((prevData) => ({
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
  const transferMutation = ApiHook.CallFundTransfer();
  const isFormValid = () => {
    return (
      transferData?.username.trim() !== "" &&
      transferData?.amount > 0 &&
      transferData?.transactionPassword.trim() !== ""
    );
  };
  const handleSubmit = async (e) => {
    e.preventDefault();
    if (!isFormValid()) {
      return;
    }
    try {
      let convertAmount;
      if (currency.id === defaultCurrency.id) {
        convertAmount = reverseNumberDisplay(
          CurrencyConverter(transferData?.amount, conversionFactor, 0)
        );
      } else {
        convertAmount = reverseNumberDisplay(
          CurrencyConverter(transferData?.amount, conversionFactor, 1)
        );
      }
      const postData = {
        username: transferData?.username,
        amount: convertAmount,
        transactionPassword: transferData?.transactionPassword,
      };
      await transferMutation.mutateAsync(postData, {
        onSuccess: (res) => {
          if (res.status === 200) {
            setErrorMessage({
              username: null,
              transactionPassword: null,
            });
            setTransferData({
              username: "",
              amount: "",
              transactionPassword: "",
            });
            queryClient.invalidateQueries({ queryKey: ["statement"] });
            queryClient.invalidateQueries({ queryKey: ["ewallet-tiles"] });
            handleClose(false);
          } else {
            if (res?.data?.code === 1011) {
              setErrorMessage((prevErrors) => ({
                ...prevErrors,
                username: t(res?.data?.description),
              }));
            } else if (res?.data?.code === 1015) {
              setErrorMessage((prevErrors) => ({
                ...prevErrors,
                transactionPassword: t(res?.data?.description),
              }));
            } else if (res?.data?.code === 1014) {
              setErrorMessage((prevErrors) => ({
                ...prevErrors,
                amount: t(res?.data?.description),
              }));
            } else if (res?.data?.code === 1088) {
              setErrorMessage((prevErrors) => ({
                ...prevErrors,
                username: t(res?.data?.description),
              }));
            } else if (res?.data?.code) {
              toast.error(res?.data?.description);
            } else {
              toast.error(res?.message);
            }
          }
        },
      });
    } catch (error) {
      // Handle general error or network issue
      console.error("Error submitting transfer:", error);
    }
  };

  return (
    <Offcanvas
      show={show}
      onHide={handleClose}
      placement="end"
      id="ewalletTrnsfer"
    >
      <Offcanvas.Header closeButton>
        <Offcanvas.Title>{t("ewalletFundTransfer")}</Offcanvas.Title>
      </Offcanvas.Header>
      <Offcanvas.Body>
        <Form>
          <Form.Group className="mb-3">
            <Form.Label>{t("transferToUsername")} *</Form.Label>
            <Form.Control
              id="username"
              type="text"
              placeholder="Transfer To (Username)"
              onChange={(e) => handleChange(e)}
              value={transferData.username}
              required
              isInvalid={errorMessage?.username !== null}
            />
            <Form.Control.Feedback type="invalid">
              {errorMessage.username}
            </Form.Control.Feedback>
          </Form.Group>
          <Form.Group className="mb-3">
            <Form.Label>
              {t("amount")}
              <span>*</span>
            </Form.Label>
            <div className="amount-field input-group">
              <Form.Control as="select" disabled className="max-40">
                <option value="">{currency?.symbolLeft}</option>
              </Form.Control>
              <CurrencyInput
                name="amount"
                id="amount"
                placeholder={t("amount")}
                value={transferData.amount}
                onValueChange={(value, id) => handleChange({ target: { value, id } })}
                required
                allowNegativeValue={false}
                // decimalsLimit={2}
              />
              <div className="number-field-invalid-feedback">{errorMessage.amount}</div>
            </div>
          </Form.Group>
          <Form.Group className="mb-3">
            <Form.Label>{t("availableAmount")}</Form.Label>
            <div className="input-group">
              <Form.Control as="select" disabled className="max-40">
                <option value="">{currency?.symbolLeft}</option>
              </Form.Control>
              <Form.Control
                type="text"
                disabled
                value={CurrencyConverter(balance, conversionFactor)}
              />
            </div>
          </Form.Group>
          <Form.Group className="mb-3">
            <Form.Label>{t("transactionFee")}</Form.Label>
            <div className="input-group">
              <Form.Control as="select" disabled className="max-40">
                <option value="">{currency?.symbolLeft}</option>
              </Form.Control>
              <Form.Control type="text" disabled value={transFee} />
            </div>
          </Form.Group>
          <Form.Group className="mb-3">
            <Form.Label>{t("transactionPassword")} *</Form.Label>
            <Form.Control
              id="transactionPassword"
              type="password"
              placeholder="Transaction Password"
              onChange={(e) => handleChange(e)}
              value={transferData.transactionPassword}
              required
              isInvalid={errorMessage?.transactionPassword !== null}
            />
            <Form.Control.Feedback type="invalid">
              {errorMessage.transactionPassword}
            </Form.Control.Feedback>
          </Form.Group>
          <SubmitButton
            isSubmitting={!isFormValid() || transferMutation.isLoading}
            click={handleSubmit}
            text="submit"
            className={"btn btn-primary submit_btn"}
            isLoading={transferMutation.isLoading}
          />
        </Form>
      </Offcanvas.Body>
    </Offcanvas>
  );
};

export default EwalletTransferOffcanvas;
