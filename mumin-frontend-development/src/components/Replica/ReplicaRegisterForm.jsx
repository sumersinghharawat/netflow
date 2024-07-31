import React, { Fragment, useState } from "react";
import { useTranslation } from "react-i18next";
import Select from "react-select";
import { useForm } from "react-hook-form";
import {
  loginPasswordValidator,
  validateAge,
  loginUsernameValidator,
  passwordRules,
} from "../../Validator/register";
import PasswordChecklist from "react-password-checklist";
import { ApiHook } from "../../hooks/apiHook";
import { BASE_URL } from "../../config/config";
import DatePickerComponent from "../Common/DatePickerComponent";
import dayjs from "dayjs";
import { PhoneInput } from "react-international-phone";
import { PhoneNumberUtil } from "google-libphonenumber";
import { useSelector } from "react-redux";
import CurrencyConverter from "../../Currency/CurrencyConverter";
import MyPayPalOrderButton from "../payment/PaypalOrderButton";
import Stripe from "../../views/payments/Stripe";
import NowPayment from "../payment/NowPayment";
import { updateCurrency } from "../../store/actions/userAction";

const ReplicaRegisterForm = ({
  activeStep,
  setActiveStep,
  handleToggleRegisterModal,
  animateStepTransition,
  currency,
  data,
}) => {
  const { t } = useTranslation();
  const [activeTab, setActiveTab] = useState("");
  const [states, setStates] = useState([]);
  const [submitButtonActive, setSubmitButtonActive] = useState(true);
  const [file, setFile] = useState(null);
  const [selectedDob, setSelectedDob] = useState(dayjs());
  const [phoneNumber, setPhoneNumber] = useState("");
  const [isCalenderOpen, setIsCalenderOpen] = useState(false);
  const [selectedGender, setSelectedGender] = useState();
  const [selectedCountry, setSelectedCountry] = useState("");
  const [selectedState, setSelectedState] = useState("");
  const [clientSecret, setClientSecret] = useState({ status: false, data: "" });
  const [stripeKey, setStripeKey] = useState("");
  const [nowpaymentKey, setNowpaymentKey] = useState("");
  const [passwordAgain, setPasswordAgain] = useState("");
  const [isMinAge, setIsMinAge] = useState({
    status: false,
    ageLimit: data?.contactInformation?.contactField.find(
      (item) => item.code === "date_of_birth"
    ).options[0].validation.ageLimit,
  });
  const [fileResponse, setFileResponse] = useState({
    success: null,
    error: null,
  });
  const regAmount = useSelector(
    (state) => state?.dashboard?.appLayout?.configuration?.regAmount
  );
  const conversionFactor = useSelector(
    (state) => state?.user?.conversionFactor
  );
  const userSelectedCurrency = useSelector(
    (state) => state.user?.selectedCurrency
  );

  const {
    register,
    setValue,
    trigger,
    watch,
    setError,
    formState: { errors },
  } = useForm();
  const phoneUtil = PhoneNumberUtil.getInstance();
  const formValues = watch();
  // console.log(formValues);
  // console.log(errors,"error");

  const steps = ["Step 1", "Step 2", "Step 3", "Step 4", "Step 5"];

  // ------------------------------------------- API ------------------------------------------
  const deleteBankRecipt = ApiHook.CallReplicaBankRecieptDelete(
    setSubmitButtonActive,
    setValue,
    setFileResponse,
    setFile
  );
  const checkUsernameField = ApiHook.CallReplicaFieldCheck();
  const checkEmailField = ApiHook.CallReplicaFieldCheck();
  const createIntent = ApiHook.CallPaymentIntent();
  const PaymentGatewayKey = ApiHook.CallNowPaymentIntent();

  const Upload = ApiHook.CallReplicaBankRecieptUpload(
    "register/replica",
    formValues?.username,
    data?.sponsorData?.id,
    setSubmitButtonActive,
    setValue,
    setFileResponse
  );

  const replicaRegisterMutation = ApiHook.CallReplicaRegisterPost();

  // ---------------------------------- Functions ------------------------------------
  const genderOptions = (data) => {
    return data.map((item) => ({
      value: item.value,
      label: t(item.title),
    }));
  };
  const countryList = (data) => {
    return data.map((item) => ({
      label: item?.name,
      value: item?.id,
    }));
  };
  const stateList = (data) => {
    if (!formValues?.country) {
      return []; // Return an empty array if there's no country specified.
    }

    const selectedCountry = data.find(
      (country) => country.id === Number(formValues.country)
    );

    if (!selectedCountry) {
      return []; // Return an empty array if the selected country is not found.
    }

    return selectedCountry.States.map((state) => ({
      value: state.id,
      label: state.name,
    }));
  };
  const changeCountry = (country) => {
    setSelectedCountry(country?.value);
    setSelectedState(null); // Clear the selected state
    setValue("state", "");
    setValue("country", country?.value); // Update the form value for country
  };
  const handleDateChange = (newDate, item) => {
    if (newDate) {
      setIsMinAge({
        status: minAgeValidation(
          newDate,
          item.options[0]?.validation?.ageLimit
        ),
        ageLimit: item.options[0]?.validation?.ageLimit,
      });
      setSelectedDob(newDate);
      const formattedDate = newDate.format("YYYY-MM-DD");
      setValue("date_of_birth", formattedDate);
      setError("date_of_birth", { message: "" });
    }
  };
  const handleDeleteBankReciept = () => {
    const data = {
      filepath: formValues?.bankReceipt,
      type: "replica",
    };
    if (formValues?.bankReceipt) {
      deleteBankRecipt.mutateAsync(data);
    }
  };
  const createIntentData = async () => {
    const payload = {
      email: formValues.email,
      desc: formValues.username,
      amount: formValues?.totalAmount ?? regAmount.toFixed(2) ?? 0,
    };
    await createIntent.mutateAsync(payload).then((response) => {
      setClientSecret((prev) => ({
        ...prev,
        status: true,
        data: response.paymentIntent.client_secret,
      }));
      setStripeKey(response.publicKey);
    });
  };


  const createIntentDataNowpayment = async (paymentId) => {

    await PaymentGatewayKey.mutateAsync(paymentId).then((response) => {
      console.log("public-key",response);
      setNowpaymentKey(response.data.data.publicKey);
    });
    
    // paymentId
  };

  const openCalender = () => {
    setIsCalenderOpen(true);
  };
  const closeCalender = () => {
    setIsCalenderOpen(false);
  };
  const genderChange = (gender) => {
    setSelectedGender(gender);
    setValue("gender", gender?.value);
  };
  const changeState = (state) => {
    setSelectedState(state?.value);
    setValue("state", state?.value);
  };
  const minAgeValidation = (selectedDate, ageLimit) => {
    if (selectedDate) {
      const today = dayjs();
      const minAge = today.subtract(ageLimit, "year"); // Calculate the minimum Age
      return selectedDate.isBefore(minAge);
    } else {
      // Handle the case when selectedDate is null or undefined
      return false; // Or you can throw an error or handle it differently
    }
  };
  const handleNext = async () => {
    let isValid;
    if (
      activeStep === 3 &&
      !checkUsernameField.data?.data?.status &&
      checkUsernameField.data?.data?.field === "username" &&
      checkUsernameField.data?.data?.code === 1117
    ) {
      isValid = false;
    } else if (
      activeStep === 2 &&
      !checkEmailField.data?.data?.status &&
      checkEmailField.data?.data?.field === "email" &&
      checkEmailField.data?.data?.code === 1117
    ) {
      isValid = false;
    } else if (activeStep === 1 && data?.regData) {
      setValue("pv", data?.regData);
      isValid = true;
    } else {
      isValid = await trigger();
    }
    if (!formValues?.mobile && activeStep === 2) {
      isValid = false;
      setError("mobile", { message: t("this_field_is_required") });
    }
    if (!isPhoneNumberValid && activeStep === 2) {
      isValid = false;
      setError("mobile", {
        message: t("min_length"),
      });
    }
    if (!formValues?.date_of_birth && activeStep === 2) {
      isValid = setError("date_of_birth", {
        message: t("this_field_is_required"),
      });
    }
    if (!isMinAge?.status && isMinAge?.ageLimit && activeStep === 2) {
      isValid = setError("date_of_birth", {
        message: t("ageValidation", { age: isMinAge?.ageLimit }),
      });
    }
    // below is international mobile validation : uncomment if needed

    // if (!isPhoneNumberValid && activeStep === 2) {
    //   isValid = false;
    //   setError("mobile",{message:t("invalidPhone")});
    // }

    if (isValid) {
      const nextStep = activeStep + 1;
      animateStepTransition(nextStep);
      setActiveStep(nextStep);
    }
  };

  const handleBack = () => {
    const prevStep = activeStep - 1;
    animateStepTransition(prevStep);
    setActiveStep(prevStep);
    // below step is to clear the uploaded image & value
    if (activeStep === 4 || activeStep === 5) {
      setValue("bankReceipt", undefined);
    }
    if (file) {
      setFile(null);
    }
  };

  const handleSubmit = (paymentId) => {
    if (data?.sponsorData?.username) {
      setValue("placement", data?.sponsorData?.username);
      formValues.placement = data?.sponsorData?.username;
      setValue("referralId", data?.sponsorData?.id);
      formValues.referralId = data?.sponsorData?.id;
    }
    if (formValues.paymentType == 5) {
      formValues.stripeToken = paymentId;
    }
    replicaRegisterMutation.mutate(formValues);
  };

  const handleProductClick = (productId, productName, pv, price) => {
    const totalAmount = Number(price) + JSON.parse(data?.regFee);
    setValue("totalAmt", totalAmount.toFixed(2));
    setValue(
      "product",
      { id: productId, name: productName, price: price },
      { shouldValidate: true }
    );
    setValue("pv", pv);
    setValue("totalAmount", totalAmount.toFixed(2));
    setValue("regAmount", data?.regFee);
  };

  const handlePaymentTabClick = (tabId) => {
    // console.log(tabId);
    setActiveTab(tabId);
    if (tabId === 3) {
      setSubmitButtonActive(false);
    } else if (tabId === 5) {
      createIntentData();
    } else if (tabId === 8) {
      createIntentDataNowpayment(tabId);
    } else {
      setSubmitButtonActive(true);
    }
    setValue("paymentType", tabId, { shouldValidate: true }); // Set the selected payment
    if (!data?.modStatus?.productStatus) {
      setValue("totalAmount", Number(data?.regData?.toFixed(2))); // regAmount is added to total amount. If there is no product
    }
  };

  const handleEmailCheck = async (item) => {
    const { name, value } = item;
    checkEmailField.mutate({ field: name, value: value });
  };

  const handleUsernameCheck = async (item) => {
    const { name, value } = item;
    checkUsernameField.mutate({ field: name, value: value });
  };

  const handleCustomField = (id, value) => {
    if (formValues.customFields?.length > 0) {
      const existingIndex = formValues.customFields.findIndex(
        (item) => item.id === id
      );
      if (existingIndex !== -1) {
        const updatedCustomField = [...formValues.customFields];
        updatedCustomField[existingIndex] = { id: id, value: value };
        setValue("customFields", updatedCustomField);
      } else {
        setValue("customFields", [
          ...formValues.customFields,
          { id: id, value: value },
        ]);
      }
    } else {
      setValue("customFields", [{ id: id, value: value }]);
    }
  };

  const handleFileChange = (event) => {
    setFileResponse({
      success: null,
      error: null,
    });
    const selectedFile = event.target.files[0];
    setFile(selectedFile);
  };

  const handleUpload = () => {
    if (file) {
      Upload.mutate(file);
    }
  };

  const handleCountry = (selectedCountry, statesData) => {
    if (selectedCountry) {
      setValue("country", selectedCountry);
      formValues.country = selectedCountry;
    }
    statesData?.map((value) => {
      if (parseInt(selectedCountry) === value.id) {
        setStates(value.States);
      }
    });
  };
  const handlePhoneNumber = (phone) => {
    setPhoneNumber(phone);
    setValue("mobile", phone);
    setError("mobile", { message: "" });
  };
  const isPhoneValid = (phone) => {
    // international phoneUtil validation is commented
    // return phoneUtil.isValidNumber(phoneUtil.parseAndKeepRawInput(phone));

    // Add minimum length validation
    return phone.length >= 7 && !isNaN(phone);
  };
  const isPhoneNumberValid = isPhoneValid(phoneNumber);

  return (
    <div className="main-content-regsiter" style={{ margin: "65px" }}>
      <div className="row justify-content-center pt-0 p-4" id="wizardRow">
        <div className="col-md-12 text-center">
          <div className="wizard-form py-4 my-2">
            <ul id="progressBar" className="progressbar px-lg-5 px-0">
              {steps.map((step, index) => (
                <li
                  key={`step-${index + 1}`}
                  id={`progressList-${index + 1}`}
                  className={`d-inline-block w-20 position-relative text-center float-start progressbar-list ${
                    index <= activeStep - 1 ? "active" : ""
                  }`}
                >
                  {step}
                </li>
              ))}
            </ul>
          </div>
        </div>
      </div>
      <div id="animation">
        {activeStep === 1 && (
          <div className="row row_top justify-content-center" id="cardSection">
            <div className="col-lg-12 col-md-12">
              <div className="regsiter_step_1_view">
                <div className="row">
                  <div className="col-md-12">
                    <div className="right_product_selection_bx">
                      <div className="regsiter_step_1_view_left_sec_head">
                        {data?.registrationSteps &&
                          t(
                            `${data?.registrationSteps[activeStep - 1]?.label}`
                          )}
                      </div>
                      <div className="right_product_lsting_section">
                        <div className="row">
                          {data?.regData && (
                            <div className="col-md-4">
                              <div
                                className={`right_product_box card active-card`}
                                {...register("product")}
                              >
                                <div className="right_product_box_image">
                                  <img src={"/images/product1.jpg"} alt="" />
                                </div>
                                <div className="right_product_box__dtails">
                                  <div className="right_product_box__head">
                                    {/* {product?.name} */}
                                  </div>
                                  <div className="right_product_box__dsc">
                                    <strong id="price">{`${
                                      userSelectedCurrency?.symbolLeft
                                    } ${CurrencyConverter(
                                      data?.regData,
                                      conversionFactor
                                    )}`}</strong>
                                    <br />
                                    {"PV - "}
                                    {data?.regData}
                                  </div>
                                </div>
                                <button
                                  type="button"
                                  className="btn text-white float-end next mt-4 rounded-3 bg-color-info"
                                  onClick={() => handleNext()}
                                >
                                  {t("buy_now")}
                                </button>
                              </div>
                            </div>
                          )}
                          {data?.regPack?.map((product, index) => (
                            <div className="col-md-4" key={index}>
                              <div
                                className={`right_product_box card ${
                                  formValues.product?.id === product.id
                                    ? "active-card"
                                    : ""
                                }`}
                                {...register("product")}
                                onClick={() =>
                                  handleProductClick(
                                    product.id,
                                    product.name,
                                    product.pairValue,
                                    product.price
                                  )
                                }
                              >
                                <div className="right_product_box_image">
                                  <img
                                    src={
                                      product?.image ?? "/images/product1.jpg"
                                    }
                                    alt=""
                                  />
                                </div>
                                <div className="right_product_box__dtails">
                                  <div className="right_product_box__head">
                                    {product?.name}
                                  </div>
                                  <div className="right_product_box__dsc">
                                    <strong id="price">{`${
                                      userSelectedCurrency?.symbolLeft
                                    } ${CurrencyConverter(
                                      product?.price,
                                      conversionFactor
                                    )}`}</strong>
                                    <br />
                                    {"PV - "}
                                    {product?.pairValue}
                                  </div>
                                </div>
                                <button
                                  type="button"
                                  className="btn text-white float-end next mt-4 rounded-3 bg-color-info"
                                  onClick={() => handleNext()}
                                >
                                  {t("buy_now")}
                                </button>
                              </div>
                            </div>
                          ))}
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        )}
        {activeStep === 2 && (
          <div className="row row_top justify-content-center form-business">
            {data?.modStatus?.mlmPlan === "Binary" && (
              <>
                <div className="regsiter_step_1_view_left_sec_head">
                  {t("placement")}
                </div>
                <div className="regsiter_second_step_bx reg_select_pos">
                  <div className="regsiter_step_1_view_left_btn_row mt-0">
                    <label
                      className={`regsiter_step_1_view_left_btn ${
                        formValues.position === "L" ? "active" : ""
                      }`}
                    >
                      <input
                        type="radio"
                        name="position"
                        value={"L"}
                        {...register("position", {
                          required: true,
                        })}
                      />
                      {t("left")}
                    </label>
                    <label
                      className={`regsiter_step_1_view_left_btn ${
                        formValues.position === "R" ? "active" : ""
                      }`}
                    >
                      <input
                        type="radio"
                        name="position"
                        value={"R"}
                        {...register("position", {
                          required: true,
                        })}
                      />
                      {t("right")}
                    </label>
                  </div>
                  {errors["position"] && formValues.position === null && (
                    <span className="error-message-validator">
                      {t("this_field_is_required")}
                    </span>
                  )}
                </div>
              </>
            )}
            <div className="regsiter_step_1_view_left_sec_head">
              {data?.registrationSteps &&
                t(`${data?.registrationSteps[activeStep - 1]?.label}`)}
            </div>
            <div className="regsiter_second_step_bx">
              <div className="row">
                {data?.contactInformation?.contactField?.map((item, index) => (
                  <div className="col-md-6" key={index}>
                    <div className="regsiter_form_box">
                      <label htmlFor={item.code}>
                        {t(`${item.code}`)}
                        <span className="text-danger" hidden={!item.required}>
                          ٭
                        </span>
                      </label>
                      {item.type === "text" &&
                        item.code !== "state" &&
                        !item.options && (
                          <input
                            id={item.code}
                            name={item.code}
                            type="text"
                            className={`form-control ${
                              errors[item.code] ? "error-field" : ""
                            }`}
                            placeholder={item.placeholder}
                            {...register(item.code, {
                              required: {
                                value: item.required,
                                message: t("this_field_is_required"),
                              },
                              // pattern: {
                              //   value: /^[A-Za-z0-9]+$/,
                              //   message: t("invalid_format"),
                              // },
                            })}
                            onBlur={async () => await trigger(item.code)}
                          />
                        )}
                      {item.type === "email" && (
                        <>
                          <input
                            id={item.code}
                            name={item.code}
                            type="email"
                            className={`form-control ${
                              (!checkEmailField.data?.data?.status &&
                                checkEmailField.data?.data?.field === "email" &&
                                checkEmailField.data?.data?.code === 1117) ||
                              errors[item.code]
                                ? "error-field"
                                : ""
                            }`}
                            placeholder={item.placeholder}
                            {...register("email", {
                              required: {
                                value: item.required,
                                message: t("this_field_is_required"),
                              },
                              pattern: {
                                value:
                                  /^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]+$/,
                                message: t("invalid_email_format"),
                              },
                            })}
                            onChangeCapture={async (e) => {
                              handleEmailCheck(e.target);
                            }}
                            onBlur={async () => await trigger(item.code)}
                          />
                          {!checkEmailField.data?.data?.status &&
                            checkEmailField.data?.data?.field === "email" &&
                            checkEmailField.data?.data?.code === 1117 && (
                              <span className="error-message-validator">
                                {t("email_exists")}
                              </span>
                            )}
                        </>
                      )}
                      {item.type === "date" && (
                        <DatePickerComponent
                          className={`date-picker ${
                            errors[item.code] ? "error-field" : ""
                          }`}
                          date={selectedDob}
                          handleDateChange={(newDate) =>
                            handleDateChange(newDate, item)
                          }
                          isCalenderOpen={isCalenderOpen}
                          openCalender={openCalender}
                          closeCalender={closeCalender}
                        />
                      )}
                      {item.type === "number" && item.code === "mobile" && (
                        <PhoneInput
                          defaultCountry="us"
                          value={phoneNumber}
                          onChange={handlePhoneNumber}
                        />
                      )}
                      {item.type === "number" && !(item.code === "mobile") && (
                        <input
                          id={item.code}
                          name={item.code}
                          type="number"
                          className={`form-control ${
                            errors[item.code] ? "error-field" : ""
                          }`}
                          placeholder={item.placeholder}
                          {...register(item.code, {
                            required: {
                              value: item.required,
                              message: t("this_field_is_required"),
                            },
                            minLength: {
                              value: 5,
                              message: t("min_length_of_5"),
                            },
                          })}
                          onBlur={async () => await trigger(item.code)}
                        />
                      )}
                      {item.type === "text" && item.code === "gender" && (
                        <Select
                          id={item.code}
                          name={item?.code}
                          className={`dropdown-common ${
                            errors[item.code] ? "error-field" : ""
                          }`}
                          {...register(item.code, {
                            required: {
                              value: item.required,
                              message: t("this_field_is_required"),
                            },
                          })}
                          onBlur={async () => await trigger(item.code)}
                          options={genderOptions(item?.options)}
                          onChange={genderChange}
                          value={selectedGender}
                          isSearchable={false}
                        />
                      )}
                      {item.type === "text" && item.code === "country" && (
                        <Select
                          isSearchable={false}
                          id={item.code}
                          name={item?.code}
                          className={` ${
                            errors[item.code] ? "error-field" : ""
                          }`}
                          {...register(item.code, {
                            required: {
                              value: item.required,
                              message: t("this_field_is_required"),
                            },
                          })}
                          onBlur={async () => await trigger(item.code)}
                          value={countryList(item?.options).find(
                            (item) => item.value === selectedCountry
                          )}
                          options={countryList(item?.options)}
                          onChange={changeCountry}
                        />
                      )}
                      {item.type === "text" && item.code === "state" && (
                        <Select
                          isSearchable={false}
                          id={item.code}
                          name={item?.code}
                          className={` ${
                            errors[item.code] ? "error-field" : ""
                          }`}
                          {...register(item.code, {
                            required: {
                              value: item.required,
                              message: t("this_field_is_required"),
                            },
                          })}
                          options={stateList(item?.options)}
                          onBlur={async () => await trigger(item.code)}
                          value={stateList(item?.options).find(
                            (state) => state?.value === selectedState
                          )}
                          onChange={changeState}
                        />
                      )}
                      {errors[item.code] && (
                        <span className="error-message-validator">
                          {errors[item.code].message}
                        </span>
                      )}
                    </div>
                  </div>
                ))}
                {data?.contactInformation?.customField?.map((item, index) => (
                  <div className="col-md-6" key={index}>
                    <div className="regsiter_form_box">
                      <label htmlFor={item.code}>
                        {item.value}{" "}
                        <span className="text-danger" hidden={!item.required}>
                          ٭
                        </span>
                      </label>
                      {item.type === "text" && !item.options && (
                        <input
                          id={item.code}
                          name={item.code}
                          type="text"
                          className={`form-control ${
                            errors[item.code] ? "error-field" : ""
                          }`}
                          placeholder={item.placeholder}
                          {...register(item.code, {
                            required: {
                              value: item.required,
                              message: t("this_field_is_required"),
                            },
                            // pattern: {
                            //   value: /^[A-Za-z0-9]+$/,
                            //   message: t("invalid_format"),
                            // },
                          })}
                          onChange={async (e) => {
                            const inputValue = e.target.value; // Get the current value of the input field
                            handleCustomField(item.id, inputValue);
                            await trigger(item.code);
                          }}
                        />
                      )}
                      {item.type === "email" && (
                        <>
                          <input
                            id={item.code}
                            name={item.code}
                            type="email"
                            className={`form-control ${
                              (!checkEmailField.data?.data?.status &&
                                checkEmailField.data?.data?.field === "email" &&
                                checkEmailField.data?.data?.code === 1117) ||
                              errors[item.code]
                                ? "error-field"
                                : ""
                            }`}
                            placeholder={item.placeholder}
                            {...register(item.code, {
                              required: {
                                value: item.required,
                                message: t("this_field_is_required"),
                              },
                              pattern: {
                                value:
                                  /^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]+$/,
                                message: t("invalid_email_format"),
                              },
                            })}
                            onChangeCapture={async (e) => {
                              const inputValue = e.target.value; // Get the current value of the input field
                              handleCustomField(item.id, inputValue);
                            }}
                          />
                          {!checkEmailField.data?.data?.status &&
                            checkEmailField.data?.data?.field === "email" &&
                            checkEmailField.data?.data?.code === 1117 && (
                              <span className="error-message-validator">
                                {t("email_exists")}
                              </span>
                            )}
                        </>
                      )}
                      {item.type === "date" && (
                        <>
                          <input
                            id={item.code}
                            name={item.code}
                            type="date"
                            className={`form-control ${
                              errors[item.code] ? "error-field" : ""
                            }`}
                            placeholder={item.placeholder}
                            {...register(item.code, {
                              required: {
                                value: item.required,
                                message: t("this_field_is_required"),
                              },
                              validate: (value) => validateAge(value, t),
                            })}
                            onChange={async (e) => {
                              const inputValue = e.target.value; // Get the current value of the input field
                              handleCustomField(item.id, inputValue);
                              await trigger(item.code);
                            }}
                          />
                          {errors.date_of_birth && (
                            <span className="error-message-validator">
                              {errors.date_of_birth.message}
                            </span>
                          )}
                        </>
                      )}
                      {item.type === "textarea" && (
                        <textarea
                          id={item.code}
                          name={item.code}
                          className={`form-control ${
                            errors[item.code] ? "error-field" : ""
                          }`}
                          placeholder={item.placeholder}
                          {...register(item.code, {
                            required: {
                              value: item.required,
                              message: t("this_field_is_required"),
                            },
                          })}
                          onChange={async (e) => {
                            const inputValue = e.target.value; // Get the current value of the input field
                            handleCustomField(item.id, inputValue);
                            await trigger(item.code);
                          }}
                        />
                      )}
                      {item.type === "number" && (
                        <input
                          id={item.code}
                          name={item.code}
                          type="number"
                          className={`form-control ${
                            errors[item.code] ? "error-field" : ""
                          }`}
                          placeholder={item.placeholder}
                          {...register(item.code, {
                            required: {
                              value: item.required,
                              message: t("this_field_is_required"),
                            },
                            minLength: {
                              value: 5,
                              message: t("min_length_of_5"),
                            },
                          })}
                          onChange={async (e) => {
                            const inputValue = e.target.value; // Get the current value of the input field
                            handleCustomField(item.id, inputValue);
                            await trigger(item.code);
                          }}
                        />
                      )}
                      {item.type === "text" && item.code === "gender" && (
                        <select
                          id={item.code}
                          name={item.code}
                          className={`form-control ${
                            errors[item.code] ? "error-field" : ""
                          }`}
                          {...register(item.code, {
                            required: {
                              value: item.required,
                              message: t("this_field_is_required"),
                            },
                          })}
                          onChange={async (e) => {
                            const inputValue = e.target.value; // Get the current value of the input field
                            handleCustomField(item.id, inputValue);
                            await trigger(item.code);
                          }}
                        >
                          {item?.options?.map((option, optionIndex) => (
                            <option key={optionIndex} value={option.value}>
                              {option.title}
                            </option>
                          ))}
                        </select>
                      )}
                      {item.type === "text" && item.code === "country" && (
                        <select
                          id={item.code}
                          name={item.code}
                          className={`form-control ${
                            errors[item.name] ? "error-field" : ""
                          }`}
                          {...register(item.code, {
                            required: {
                              value: item.required,
                              message: t("this_field_is_required"),
                            },
                          })}
                          onChange={async (e) => {
                            const inputValue = e.target.value; // Get the current value of the input field
                            handleCountry(e.target.value, item.options);
                            handleCustomField(item.id, inputValue);
                            await trigger(item.code);
                          }}
                        >
                          <option>{t("select_the_country")}</option>
                          {item?.options?.map((option, optionIndex) => (
                            <option key={optionIndex} value={option.id}>
                              {option.name}
                            </option>
                          ))}
                        </select>
                      )}
                      {item.type === "text" && item.code === "state" && (
                        <select
                          id={item.code}
                          name={item.name}
                          className={`form-control ${
                            errors[item.name] ? "error-field" : ""
                          }`}
                          {...register(item.code, {
                            required: {
                              value: item.required,
                              message: t("this_field_is_required"),
                            },
                          })}
                          onChange={async (e) => {
                            const inputValue = e.target.value; // Get the current value of the input field
                            handleCustomField(item.id, inputValue);
                            await trigger(item.code);
                          }}
                        >
                          {states?.map((option, optionIndex) => (
                            <option key={optionIndex} value={option.id}>
                              {option.name}
                            </option>
                          ))}
                        </select>
                      )}
                      {errors[item.code] && (
                        <span className="error-message-validator">
                          {errors[item.code].message}
                        </span>
                      )}
                    </div>
                  </div>
                ))}
                <div className="col-md-12">
                  <button
                    type="button"
                    className="btn btn-dark text-white float-start back mt-4 rounded-3 bg-color-back"
                    onClick={handleBack}
                  >
                    {t("back")}
                  </button>
                  <button
                    type="button"
                    className="btn text-white float-end next mt-4 rounded-3 bg-color-info"
                    onClick={handleNext}
                  >
                    {t("next")}
                  </button>
                </div>
              </div>
            </div>
          </div>
        )}
        {activeStep === 3 && (
          <div className="row row_top justify-content-center form-business">
            <div className="regsiter_step_1_view_left_sec_head">
              {data?.registrationSteps &&
                t(`${data?.registrationSteps[activeStep - 1]?.label}`)}
            </div>
            <div className="regsiter_second_step_bx">
              <div className="row">
                {data?.loginInformation.map((item, index) => (
                  <div className="col-md-12" key={index}>
                    <div className="regsiter_form_box">
                      <label htmlFor={item.code}>
                        {t(item.code)} <span className="text-danger">٭</span>
                      </label>
                      {item.type === "text" && (
                        <>
                          <input
                            id={item.code}
                            name={item.code}
                            type={item.type}
                            className={`form-control ${
                              (!checkUsernameField.data?.data?.status &&
                                checkUsernameField.data?.data?.field ===
                                  "username" &&
                                checkUsernameField.data?.data?.code === 1117) ||
                              errors[item.code]
                                ? "error-field"
                                : ""
                            }`}
                            placeholder={item.placeholder}
                            {...register(
                              item.code,
                              loginUsernameValidator(item, t)
                            )}
                            onChangeCapture={async (e) => {
                              await handleUsernameCheck(e.target);
                            }}
                            onBlur={async () => await trigger(item.code)}
                          />
                          {!checkUsernameField.data?.data?.status &&
                            checkUsernameField.data?.data?.field ===
                              "username" &&
                            checkUsernameField.data?.data?.code === 1117 && (
                              <span className="error-message-validator">
                                {t("username_exists")}
                              </span>
                            )}
                        </>
                      )}
                      {item.type === "password" && (
                        <>
                          <input
                            id={item.code}
                            name={item.code}
                            type={item.type}
                            style={{ marginBottom: "8px" }}
                            className={`form-control ${
                              errors[item.code] ? "error-field" : ""
                            }`}
                            placeholder={item.placeholder}
                            {...register(
                              item.code,
                              loginPasswordValidator(item, t)
                            )}
                            onBlur={async () => await trigger(item.code)}
                          />
                          <input
                            id={item.code}
                            name="passwordagain"
                            type={item.type}
                            style={{ marginBottom: "8px" }}
                            className={`form-control ${
                              errors[item.code] ? "error-field" : ""
                            }`}
                            placeholder={item.placeholder}
                            onChange={e => setPasswordAgain(e.target.value)}
                          />
                          <PasswordChecklist
                            rules={passwordRules(item.validation)}
                            minLength={item.validation.minLength}
                            value={watch("password", "")}
                            valueAgain={passwordAgain}
                          />
                        </>
                      )}
                      {errors[item.code] && (
                        <span className="error-message-validator">
                          {errors[item.code].message}
                        </span>
                      )}
                    </div>
                  </div>
                ))}
                <div className="col-md-12">
                  <div className="regsiter_form_box">
                    <label className="d-flex" htmlFor="box">
                      <input
                        name={"termsAndCondition"}
                        type="checkbox"
                        id="box"
                        style={{ marginRight: "5px" }}
                        {...register("termsAndCondition", {
                          required: true,
                        })}
                      />
                      <a
                        data-bs-toggle="modal"
                        className="pop_terms_btn"
                        onClick={handleToggleRegisterModal}
                      >
                        {t("iAcceptTermsAndConditions")}
                        <span className="text-danger"> ٭ </span>
                      </a>
                    </label>
                    {errors["termsAndCondition"] &&
                      formValues["termsAndCondition"] === false && (
                        <span className="error-message-validator">
                          *{t("required")}
                        </span>
                      )}
                  </div>
                </div>
                <div className="col-md-12">
                  <button
                    type="button"
                    className="btn btn-dark text-white float-start back mt-4 rounded-3 bg-color-back"
                    onClick={handleBack}
                  >
                    {t("back")}
                  </button>
                  <button
                    type="button"
                    className="btn text-white float-end next mt-4 rounded-3 bg-color-info"
                    onClick={handleNext}
                  >
                    {t("next")}
                  </button>
                </div>
              </div>
            </div>
          </div>
        )}
        {activeStep === 4 && (
          <div className="row row_top justify-content-center form-business">
            <div className="regsiter_second_step_bx">
              <div className="regsiter_overview_box">
                <div className="regsiter_step_1_view_left_sec_head">
                  <strong>{t("product_and_sponsor")}</strong>
                </div>
                <div className="row">
                  {!data?.regData && (
                    <div className="col-md-4 mb-3 regsiter_overview_col">
                      <label htmlFor="product">{t("product")}</label>
                      <strong id="product">{formValues?.product?.name}</strong>
                    </div>
                  )}
                  <div className="col-md-4 mb-3 regsiter_overview_col">
                    <label htmlFor="sponsor">{t("sponsor")}</label>
                    <strong id="sponsor">{data?.sponsorData?.username}</strong>
                  </div>
                  <div className="col-md-4 mb-3 regsiter_overview_col">
                    <label htmlFor="pv">{t("pv")}</label>
                    <strong id="pv">{formValues?.pv}</strong>
                  </div>
                  <div className="col-md-4 mb-3 regsiter_overview_col">
                    <label htmlFor="price">{t("price")}</label>
                    <strong id="price">{`${
                      userSelectedCurrency?.symbolLeft
                    } ${CurrencyConverter(
                      formValues?.product?.price ?? data?.regData,
                      conversionFactor
                    )}`}</strong>
                  </div>
                  <div className="col-md-4 mb-3 regsiter_overview_col">
                    <label htmlFor="total">{t("totalAmount")}</label>
                    <strong id="total">{`${
                      userSelectedCurrency?.symbolLeft
                    } ${CurrencyConverter(
                      formValues?.totalAmount ?? data?.regData,
                      conversionFactor
                    )}`}</strong>
                  </div>
                </div>

                <div className="regsiter_step_1_view_left_sec_head">
                  <strong>{t(`${data?.registrationSteps[1]?.label}`)}</strong>
                </div>

                <div className="row">
                  {Object.entries(formValues)?.map(
                    ([key, value]) =>
                      ![
                        "product",
                        "pv",
                        "regAmount",
                        "termsAndCondition",
                        "totalAmt",
                        "paymentType",
                        "customFields",
                        "bankReceipt",
                        "referralId",
                        "totalAmount",
                        "username",
                        "password",
                        "country",
                        "state",
                      ].includes(key) && (
                        <div
                          className="col-md-4 mb-3 regsiter_overview_col"
                          key={key}
                        >
                          <label htmlFor={`input-${key}`}>{t(key)}</label>
                          <strong id={`input-${key}`}>{value}</strong>
                        </div>
                      )
                  )}
                  {formValues?.customField?.map(([key, value]) => (
                    <div className="col-md-4 mb-3 regsiter_overview_col">
                      <label htmlFor={`input-${key}`}>{t(key)}</label>
                      <strong id={`input-${key}`}>{value}</strong>
                    </div>
                  ))}
                </div>

                <div className="regsiter_step_1_view_left_sec_head">
                  <strong>{t(`${data?.registrationSteps[2]?.label}`)}</strong>
                </div>

                <div className="row">
                  <div className="col-md-4 mb-3 regsiter_overview_col">
                    <label htmlFor="username">{t("username")}</label>
                    <strong id="username">{formValues.username}</strong>
                  </div>
                  <div className="col-md-4 mb-3 regsiter_overview_col">
                    <label htmlFor="password">{t("password")}</label>
                    <strong id="password">********</strong>
                  </div>
                </div>

                <div className="col-md-12">
                  <button
                    type="button"
                    className="btn btn-dark text-white float-start back mt-4 rounded-3 bg-color-back"
                    onClick={handleBack}
                  >
                    {t("back")}
                  </button>
                  <button
                    type="button"
                    className="btn text-white float-end next mt-4 rounded-3 bg-color-info"
                    onClick={handleNext}
                  >
                    {t("next")}
                  </button>
                </div>
              </div>
            </div>
          </div>
        )}
        {activeStep === 5 && (
          <div className="row row_top justify-content-center form-business">
            <div className="col-lg-12 col-md-12" id="payment">
              <div className="payment_section_tab">
                <div className="regsiter_step_1_view_left_sec_head">
                  {data?.registrationSteps &&
                    t(`${data?.registrationSteps[activeStep - 1]?.label}`)}
                  <br />
                  <strong>
                    {t("totalAmount")}:{" "}
                    {`${userSelectedCurrency?.symbolLeft} ${CurrencyConverter(
                      formValues?.totalAmount ?? data?.regFee,
                      conversionFactor
                    )}`}
                  </strong>
                </div>

                <div className="row">
                  <div className="col-md-3 pe-0">
                    <div
                      className="nav nav-tabs payment_tab_reg"
                      id="nav-tab"
                      role="tablist"
                    >
                      {data?.paymentGateways?.map((tab) => (
                        <Fragment key={tab.id}>
                          <button
                            key={tab.id}
                            className={`nav-link ${
                              activeTab === tab.id ? "active" : ""
                            }`}
                            id={`nav-${tab.id}-tab`}
                            data-bs-toggle="tab"
                            data-bs-target={`#nav-${tab.id}`}
                            type="button"
                            role="tab"
                            aria-controls={`nav-${tab.id}`}
                            aria-selected={formValues.paymentType === tab.id}
                            {...register("paymentType", { required: true })}
                            onClick={() =>
                              handlePaymentTabClick(tab.id, tab.title)
                            }
                          >
                            <i className={tab?.icon}></i> {t(tab.title)}
                          </button>
                        </Fragment>
                      ))}
                    </div>
                  </div>
                  <div className="col-md-9 ps-0">
                    <div
                      className="tab-content p-3 border mb-5 payment__tab_cnt"
                      id="nav-tabContent"
                    >
                      {(formValues.paymentType === undefined ||
                        formValues.paymentType === "") && (
                        <p>
                          <strong>{t("preferredPaymentPlaceholder")}</strong>
                        </p>
                      )}

                      {data?.paymentGateways?.map((tab) => (
                        <div
                          key={tab.id}
                          className={`tab-pane fade ${
                            activeTab === tab.id ? "show active" : ""
                          }`}
                          id={`nav-${tab.id}`}
                          role="tabpanel"
                          aria-labelledby={`nav-${tab.id}-tab`}
                        >
                          {tab.title === "free-joining" && (
                            <p>
                              <strong>{t("freeJoinPlaceHolder")}</strong>
                            </p>
                          )}
                          {tab.title === "bank-transfer" && (
                            <div className="row">
                              <div className="col-md-12">
                                <div className="form-group mb-2">
                                  <label
                                    htmlFor="fileUpload"
                                    style={{ marginBottom: "10px" }}
                                  >
                                    {t("file_upload")}
                                  </label>
                                  <input
                                    id="bankReciept"
                                    type="file"
                                    placeholder=""
                                    className="form-control"
                                    style={{ height: "33px" }}
                                    name="fileUpload"
                                    onChange={handleFileChange}
                                  />
                                  {fileResponse?.success &&
                                    formValues?.bankReceipt !== undefined && (
                                      <div style={{ color: "green" }}>
                                        {t(fileResponse?.success)}
                                      </div>
                                    )}
                                  {fileResponse?.error && (
                                    <div style={{ color: "red" }}>
                                      {t(fileResponse?.error)}
                                    </div>
                                  )}
                                </div>
                              </div>
                              <div className="col-md-12">
                                <button
                                  href="#"
                                  className="btn btn-sm btn-primary mt-3"
                                  onClick={handleUpload}
                                  disabled={
                                    Upload.status === "loading" ||
                                    formValues?.bankReceipt ||
                                    document.getElementById("bankReciept")
                                      ?.value === "" ||
                                    file === null
                                  }
                                >
                                  {Upload.status === "loading"
                                    ? "Uploading..."
                                    : t("upload")}
                                </button>
                              </div>
                              {Upload?.data?.status === true &&
                                formValues?.bankReceipt !== undefined && (
                                  <>
                                    <img
                                      src={`${BASE_URL}${Upload?.data?.data?.file?.path}`}
                                      alt="receipt"
                                      style={{
                                        width: "160px",
                                        height: "160px",
                                        padding: "5px",
                                      }}
                                    />
                                    <button
                                      className="recipt_checkout_address_btn"
                                      onClick={handleDeleteBankReciept}
                                    >
                                      <i className="fa fa-xmark"></i>
                                    </button>
                                    <div style={{ color: "green" }}>
                                      {Upload?.data?.message}
                                    </div>
                                  </>
                                )}
                              {Upload?.data?.status === false && (
                                <span className="error-message-validator">
                                  {Upload?.data?.message}
                                </span>
                              )}
                            </div>
                          )}
                          {tab.title === "stripe" && (
                            // <p>
                            //   <strong>Addon Coming Soon</strong>
                            // </p>
                            <div>
                              <p>
                                <>
                                  {clientSecret.status &&
                                    clientSecret.data &&
                                    stripeKey && (
                                      <Stripe
                                        clientSecret={clientSecret.data}
                                        totalAmount={formValues.totalAmount}
                                        action={"register"}
                                        handleSubmitFinish={handleSubmit}
                                        publicKey={stripeKey}
                                      />
                                    )}
                                </>
                              </p>
                            </div>
                          )}
                          {tab.title === "crypto" && (
                            
                            // <p>
                            //   <strong>Addon Coming Soon</strong>
                            // </p>
                            <div>
                              <p>
                                <>
                                  {nowpaymentKey && (
                                    <>{updateCurrency}<NowPayment
                                      currency={currency?.code}
                                      product={formValues?.product}
                                      price={formValues.totalAmount}
                                      totalAmount={formValues.totalAmount}
                                      action={"register"}
                                      nowpaymentKey={nowpaymentKey}
                                      email={formValues.email}
                                      paymentMethodId={tab.id}
                                      handleSubmitFinish={handleSubmit}
                                  />
                                </>)}
                                </>
                              </p>
                            </div>
                          )}
                          {tab.title === "paypal" && (
                            <MyPayPalOrderButton
                              currency={userSelectedCurrency?.code}
                              price={formValues.totalAmount}
                              handleSubmit={handleSubmit}
                              paymentId={tab.id}
                            />
                          )}
                        </div>
                      ))}
                    </div>
                  </div>
                </div>
                {errors.paymentType && (
                  <span className="error-message-validator">*Required</span>
                )}
              </div>
              <button
                type="button"
                className="btn btn-dark text-white float-start back rounded-3 bg-color-back"
                onClick={handleBack}
              >
                {t("back")}
              </button>
              {activeTab != 5 && (
                <button
                  type="submit"
                  className="btn text-white float-end submit-button rounded-3 bg-color-info"
                  disabled={submitButtonActive}
                  onClick={handleSubmit}
                >
                  {t("finish")}
                </button>
              )}
            </div>
          </div>
        )}
      </div>
    </div>
  );
};

export default ReplicaRegisterForm;
