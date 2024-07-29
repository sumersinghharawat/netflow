import React, { useState } from "react";
import { Offcanvas, Form } from "react-bootstrap";
import { MultiSelect } from "react-multi-select-component";
import { ApiHook } from "../../hooks/apiHook";
import { useQueryClient } from "@tanstack/react-query";
import SubmitButton from "../Common/buttons/SubmitButton";
import CurrencyConverter from "../../Currency/CurrencyConverter";
import { epinCurrencyConverter } from "../../utils/epinCurrencyConversion";
import { useTranslation } from "react-i18next";
import { toast } from "react-toastify";
import CurrencyInput from "react-currency-input-field";
import DatePickerComponent from "../Common/DatePickerComponent";
import dayjs from "dayjs";

const EpinPurchase = ({
  show,
  handleClose,
  amounts,
  balance,
  currency,
  conversionFactor,
}) => {
  const { t } = useTranslation();
  const errorClassName = "border-color: var(--bs-form-invalid-border-color);";
  const [date, setExpireDate] = useState(dayjs());
  const [formState, setFormState] = useState({
    amountCount: [],
    epinCount: "",
    expiryDate: dayjs(),
    transactionPassword: "",
  });

  const [errorMessage, setErrorMessage] = useState({
    amountCount: null,
    epinCount: null,
    expiryDate: null,
    transactionPassword: null,
  });
  const [isCalenderOpen, setIsCalenderOpen] = useState(false);
  const queryClient = useQueryClient();

  // Api call for Epin Purchase
  const purchaseMutation = ApiHook.CallEpinPurchase();

  const handleChange = (e) => {
    const { id, value } = e.target;
    setFormState((prevData) => ({
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
        amountCount: "*Required",
      }));
    } else {
      setErrorMessage((prevData) => ({
        ...prevData,
        amountCount: null,
      }));
    }

    setFormState((prevData) => ({
      ...prevData,
      amountCount,
    }));
  };
  const handleDateChange = (newDate) => {
    if (newDate) {
      const formattedDate = newDate.format("YYYY-MM-DD");
      setExpireDate(newDate);
      setFormState((prev) => ({
        ...prev,
        expiryDate: formattedDate,
      }));
      setErrorMessage((prev) => ({
        ...prev,
        expiryDate: null,
      }));
    }
  };
  const openCalender = () => {
    setIsCalenderOpen(true);
  };
  const closeCalender = () => {
    setIsCalenderOpen(false);
  };

  const isFormValid = () => {
    return (
      formState?.amountCount?.length > 0 &&
      formState?.epinCount > 0 &&
      formState?.transactionPassword.trim() !== ""
    );
  };

  const handleSubmit = async (event) => {
    event.preventDefault();
    if (!isFormValid()) {
      return;
    }
    const currentDate = new Date();
    currentDate.setHours(23, 59, 59, 999); // Set time to midnight

    const expiryDate = new Date(formState.expiryDate);
    if (expiryDate <= currentDate) {
      // Display an error message if the expiryDate is not greater than today
      setErrorMessage((prevErrors) => ({
        ...prevErrors,
        expiryDate: t("expiry_date_must_be_greater_than_today"),
      }));
      return;
    }
    const amountValues = formState.amountCount?.map((option) => option.value);
    const data = {
      amountCode: amountValues,
      epinCount: formState.epinCount,
      expiryDate: formState.expiryDate,
      transactionPassword: formState.transactionPassword,
    };
    purchaseMutation.mutateAsync(data, {
      onSuccess: (res) => {
        if (res.status === 200) {
          setErrorMessage({
            amountCount: null,
            transactionPassword: null,
          });
          setFormState({
            amountCount: [],
            epinCount: "",
            expiryDate: "",
            transactionPassword: "",
          });
          setExpireDate(dayjs());
          queryClient.invalidateQueries({ queryKey: ["epin-tiles"] });
          queryClient.invalidateQueries({ queryKey: ["epin-lists"] });
          queryClient.invalidateQueries({ queryKey: ["purchased-epin-list"] });
          handleClose(false);
        } else {
          if (res?.data?.code === 1014) {
            setErrorMessage((prevErrors) => ({
              ...prevErrors,
              amountCount: t(res?.data?.description),
            }));
          } else if (res?.data?.code === 1015) {
            setErrorMessage((prevErrors) => ({
              ...prevErrors,
              transactionPassword: t(res?.data?.description),
            }));
          } else if (res?.data?.code === 429) {
            setErrorMessage((prevErrors) => ({
              ...prevErrors,
              epinCount: t(res?.data?.description),
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
    <Offcanvas show={show} onHide={handleClose} placement="end">
      <Offcanvas.Header closeButton>
        <Offcanvas.Title>{t("ePinPurchase")}</Offcanvas.Title>
      </Offcanvas.Header>
      <Offcanvas.Body>
        <Form>
          <Form.Group className="mb-3">
            <Form.Label>{t("currentBalance")}</Form.Label>
            <div className="input-group">
              <Form.Control as="select" className="max-40">
                <option value="">{currency?.symbolLeft}</option>
              </Form.Control>
              <Form.Control
                id="balance"
                type="text"
                placeholder="Current Balance"
                value={CurrencyConverter(balance, conversionFactor)}
                disabled
              />
            </div>
          </Form.Group>

          <Form.Group className="mb-3">
            <Form.Label>{t("amount")}</Form.Label>
            <MultiSelect
              id="amountCount"
              options={epinCurrencyConverter(amounts, conversionFactor) ?? ""}
              value={formState.amountCount}
              onChange={handleAmountChange}
              labelledBy="Select"
              className={
                errorMessage?.amountCount !== null ? errorClassName : ""
              }
            />
            <div className="required_dropDown">
              {errorMessage?.amountCount ? errorMessage?.amountCount : ""}
            </div>
          </Form.Group>

          <Form.Group className="mb-3 epin-count">
            <Form.Label>{t("epinCount")}</Form.Label>
            <CurrencyInput
              className="form-control"
              name="epinCount"
              id="epinCount"
              placeholder={t("epinCount")}
              value={formState?.epinCount}
              onValueChange={(value, id) =>
                handleChange({ target: { value, id } })
              }
              required
              allowNegativeValue={false}
            />
            <div className="number-field-invalid-feedback">
              {errorMessage.epinCount}
            </div>
          </Form.Group>

          <Form.Group className="mb-3 expire-date">
            <Form.Label>{t("expiryDate")}</Form.Label>
            <DatePickerComponent
              className={"date-picker"}
              date={date}
              handleDateChange={handleDateChange}
              isCalenderOpen={isCalenderOpen}
              openCalender={openCalender}
              closeCalender={closeCalender}
            />
            <div className="number-field-invalid-feedback">
              {errorMessage.expiryDate}
            </div>
          </Form.Group>

          <Form.Group className="mb-3">
            <Form.Label>{t("transactionPassword")}</Form.Label>
            <Form.Control
              id="transactionPassword"
              type="password"
              value={formState.transactionPassword}
              placeholder="Transaction Password"
              onChange={(e) => handleChange(e)}
              required
              isInvalid={errorMessage?.transactionPassword}
              autoComplete="current-password"
            />
            <Form.Control.Feedback type="invalid">
              {errorMessage.transactionPassword}
            </Form.Control.Feedback>
          </Form.Group>

          <SubmitButton
            isSubmitting={!isFormValid() || purchaseMutation.isLoading}
            click={handleSubmit}
            text="submit"
            isLoading={purchaseMutation.isLoading}
            className={"btn btn-primary submit_btn"}
          />
        </Form>
      </Offcanvas.Body>
    </Offcanvas>
  );
};

export default EpinPurchase;
