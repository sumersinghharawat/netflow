import { useQueryClient } from "@tanstack/react-query";
import React, { useState } from "react";
import { Offcanvas, Form } from "react-bootstrap";
import { MultiSelect } from "react-multi-select-component";
import { ApiHook } from "../../hooks/apiHook";
import SubmitButton from "../Common/buttons/SubmitButton";
import { epinCurrencyConverter } from "../../utils/epinCurrencyConversion";
import { toast } from "react-toastify";
import { useTranslation } from "react-i18next";
import CurrencyInput from "react-currency-input-field";
import DatePickerComponent from "../Common/DatePickerComponent";
import dayjs from "dayjs";

const EpinRequest = ({ show, handleClose, amounts, conversionFactor, setSelectedPending }) => {
  const { t } = useTranslation();
  const [date, setExpireDate] = useState(dayjs());
  const [formState, setFormState] = useState({
    amountCount: [],
    epinCount: "",
    expiryDate: dayjs(),
  });
  const [errorMessage, setErrorMessage] = useState({
    amountCount: null,
    epinCount: null,
    expiryDate: null,
  });
  const [isCalenderOpen, setIsCalenderOpen] = useState(false);

  const queryClient = useQueryClient();
  const requestMutation = ApiHook.CallEpinRequest();

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
    return formState?.amountCount?.length > 0 && formState?.epinCount > 0;
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
    const amountValues = formState.amountCount.map((option) => option.value);
    const data = {
      amountCode: amountValues,
      epinCount: formState.epinCount,
      expiryDate: formState.expiryDate,
    };
    requestMutation.mutateAsync(data, {
      onSuccess: (res) => {
        if (res.status === 200) {
          setErrorMessage({
            amountCount: null,
            transactionPassword: null,
          });
          setFormState({
            amountCount: [],
            epinCount: "",
            expiryDate: dayjs(),
          });
          setExpireDate(dayjs());
          setSelectedPending(true);
          queryClient.invalidateQueries({ queryKey: ["epin-tiles"] });
          queryClient.invalidateQueries({ queryKey: ["epin-lists"] });
          queryClient.invalidateQueries({ queryKey: ["epin-pending"] });
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
          } else if (res?.data?.code === 1045) {
            toast.error(t(res?.data?.description));
          } else if (res?.data?.code === 429) {
            setErrorMessage((prevErrors) => ({
              ...prevErrors,
              amountCount: t(res?.data?.description),
            }));
          } else if (res?.data?.code === 1103) {
            setErrorMessage((prevErrors) => ({
              ...prevErrors,
              expiryDate: t(res?.data?.description),
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
        <Offcanvas.Title>{t("epinRequest")}</Offcanvas.Title>
      </Offcanvas.Header>
      <Offcanvas.Body>
        <Form>
          <Form.Group className="mb-3">
            <Form.Label>{t("amount")}</Form.Label>
            <MultiSelect
              id="amountCount"
              options={epinCurrencyConverter(amounts, conversionFactor)}
              value={formState.amountCount}
              onChange={handleAmountChange}
              labelledBy="Select"
            />
            <div className="required_dropDown">
              {errorMessage?.amountCount ? errorMessage?.amountCount : ""}
            </div>
          </Form.Group>

          <Form.Group className="mb-3 epin-count">
            <Form.Label>{t("epinCount")}</Form.Label>
            <CurrencyInput
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
          <SubmitButton
            isSubmitting={!isFormValid() || requestMutation.isLoading}
            click={handleSubmit}
            text="epinRequest"
            isLoading={requestMutation.isLoading}
            className={"btn btn-primary submit_btn"}
          />
        </Form>
      </Offcanvas.Body>
    </Offcanvas>
  );
};

export default EpinRequest;
