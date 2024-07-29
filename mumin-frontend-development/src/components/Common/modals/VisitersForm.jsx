import React, { useEffect, useState } from "react";
import Cookies from "js-cookie";
import { Modal, Form } from "react-bootstrap";
import { useTranslation } from "react-i18next";
import { ApiHook } from "../../../hooks/apiHook";
import SubmitButton from "../buttons/SubmitButton";
import { toast } from "react-toastify";
import { useForm } from "react-hook-form";
import { PhoneInput } from "react-international-phone";
import Select from "react-select";

const VisitersForm = ({ isVisible, setIsVisible, countries }) => {
  const { t } = useTranslation();
  const demoVisitorAddMutation = ApiHook.CallAddDemoVisitor();
  const resendOtpMutation = ApiHook.CallResendOtp();
  const otpVerifyMutation = ApiHook.CallVerifyOtp();

  const {
    setError,
    register,
    watch,
    setValue,
    trigger,
    formState: { errors },
  } = useForm({
    defaultValues: {
      name: "",
      email: "",
      phone: "",
      countryId: "",
    },
  });
  const formValues = watch();
  const [timeLeft, setTimeLeft] = useState(30);
  const [timerActive, setTimerActive] = useState(false);
  const [showResendButton, setShowResendButton] = useState(false);

  const [show, setShow] = useState(false);
  const [step, setStep] = useState(1);
  const [otpSendMsg, setOtpSendMsg] = useState("");
  const [otp, setOtp] = useState();
  const [otpError, setErrorOtp] = useState();
  const [phoneNumber, setPhoneNumber] = useState(formValues?.phone);
  const [selectedCountry, setSelectedCountry] = useState(formValues?.countryId);
  const countryList = (data) => {
    return data.map((item) => ({
      label: item?.name,
      value: item?.id,
    }));
  };

  const resendOtp = () => {
    const body = {
      visitorId: localStorage.getItem("visitorId"),
    };
    resendOtpMutation.mutateAsync(body, {
      onSuccess: () => {
        startTimer();
      },
    });
  };

  const startTimer = () => {
    setTimeLeft(30);
    setTimerActive(true);
    setShowResendButton(false);
  };

  const stopTimer = () => {
    setTimerActive(false);
    setShowResendButton(true);
  };

  useEffect(() => {
    let interval;

    if (timerActive && timeLeft > 0) {
      interval = setInterval(() => {
        setTimeLeft((prevTime) => prevTime - 1);
      }, 1000);
    } else if (timeLeft === 0) {
      stopTimer();
    }

    return () => {
      clearInterval(interval);
    };
  }, [timerActive, timeLeft]);

  const isPhoneValid = (phone) => {
    // international phoneUtil validation is commented
    // return phoneUtil.isValidNumber(phoneUtil.parseAndKeepRawInput(phone));

    // Add minimum length validation
    return phone.length >= 7 && !isNaN(phone);
  };
  const isPhoneNumberValid = isPhoneValid(phoneNumber);

  const isOtpValid = () => {
    if (otpError === null) {
      return false;
    } else {
      return true;
    }
  };

  const changeCountry = (country) => {
    setSelectedCountry(country?.value);
    setValue("countryId", country?.value); // Update the form value for country
  };

  const handlePhoneNumber = (phone) => {
    setPhoneNumber(phone);
    setValue("phone", phone);
    setError("phone", { message: "" });
  };

  const handleOtpField = (e) => {
    const { value } = e.target;
    setOtp(value);
    setErrorOtp(null);
    if (value === null || value === "") {
      setErrorOtp("*Required");
    }
  };

  const handleNextStep = async () => {
    if (!phoneNumber) {
      setError("phone", { message: t("this_field_is_required") });
    }
    if (!isPhoneNumberValid) {
      setError("phone", { message: t("min_length") });
    }
    const isValid = await trigger();
    if (isValid & isPhoneNumberValid) {
      demoVisitorAddMutation.mutateAsync(formValues, {
        onSuccess: (response) => {
          if (response?.status) {
            setStep(2);
            setOtpSendMsg(response?.data?.message);
            localStorage.setItem("visitorId", response?.data?.visitorId);
            startTimer();
          }
        },
      });
    }
  };

  const handleVerifyOTP = () => {
    const data = {
      otp: otp,
      visitorId: localStorage.getItem("visitorId"),
    };
    otpVerifyMutation.mutateAsync(data, {
      onSuccess: (res) => {
        if (res?.status) {
          toast.success(res?.data);
          setIsVisible(false);
          Cookies.set("visitorID", data.visitorId, { expires: 1 });
        } else {
          setErrorOtp("Otp Verification Failed");
        }
      },
    });
  };

  return (
    <Modal show={isVisible} onHide={() => setShow(false)} size="lg">
      <Modal.Header>
        <Modal.Title>Please fill in your details to continue</Modal.Title>
      </Modal.Header>
      <Modal.Body>
        <div className="row align-items-center">
          <div className="col-md-6">
            <img
              src="/images/lead-vector.png"
              className="lead_vectr_img"
              alt=""
              style={{ maxWidth: "100%" }}
            />
          </div>
          <div className="col-md-6">
            {step === 1 ? (
              <div className="step_1">
                <Form>
                  <Form.Group className="mb-3">
                    <Form.Label>Name*</Form.Label>
                    <Form.Control
                      {...register("name", {
                        required: t("this_field_is_required"),
                      })}
                      id="name"
                      type="text"
                      placeholder="Name"
                      required
                    />
                    {errors.name && (
                      <span className="validation-error-message">
                        {errors.name.message}
                      </span>
                    )}
                  </Form.Group>
                  <Form.Group>
                    <Form.Label>Email*</Form.Label>
                    <Form.Control
                      {...register("email", {
                        required: t("this_field_is_required"),
                        pattern: {
                          value:
                            /^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]+$/,
                          message: t("invalid_email_format"),
                        },
                      })}
                      id="email"
                      type="text"
                      placeholder="Email"
                      required
                    />
                    {errors.email && (
                      <span className="validation-error-message">
                        {errors.email.message}
                      </span>
                    )}
                  </Form.Group>
                  <Form.Group>
                    <Form.Label>Phone*</Form.Label>
                    <PhoneInput
                      defaultCountry="us"
                      value={phoneNumber}
                      onChange={handlePhoneNumber}
                    />
                    {errors.phone && (
                      <span className="validation-error-message">
                        {errors.phone.message}
                      </span>
                    )}
                  </Form.Group>
                  <Form.Group>
                    <Form.Label>Country*</Form.Label>
                    <Select
                      {...register("countryId", {
                        required: t("this_field_is_required"),
                      })}
                      value={countryList(countries)?.find(
                        (item) => item.value === selectedCountry
                      )}
                      options={countryList(countries)}
                      onChange={changeCountry}
                      isSearchable={false}
                    />
                    {errors.countryId && (
                      <span className="validation-error-message">
                        {errors.countryId.message}
                      </span>
                    )}
                  </Form.Group>
                </Form>
              </div>
            ) : (
              <div className="step_2">
                {otpSendMsg && (
                  <div style={{ color: "green" }}>{otpSendMsg}</div>
                )}
                <Form.Group>
                  <Form.Label>OTP</Form.Label>
                  <Form.Control
                    id="otp"
                    type="text"
                    placeholder="OTP"
                    value={otp}
                    onChange={(e) => handleOtpField(e)}
                  />
                  {otpError && (
                    <div style={{ color: "red" }}>{t(otpError)}</div>
                  )}
                </Form.Group>
                {timerActive ? (
                  <p>
                    {"Resend code in " +
                      Math.floor(timeLeft / 60) +
                      ":" +
                      (timeLeft % 60 < 10 ? "0" : "") +
                      (timeLeft % 60)}
                  </p>
                ) : (
                  showResendButton && (
                    <span onClick={resendOtp} style={{ cursor: "pointer" }}>
                      Resend Code
                    </span>
                  )
                )}
              </div>
            )}
          </div>
        </div>
      </Modal.Body>
      <Modal.Footer>
        {step === 1 ? (
          <SubmitButton
            isLoading={demoVisitorAddMutation.isLoading}
            click={handleNextStep}
            text={demoVisitorAddMutation.isLoading ? "Sending OTP..." : "Next"}
            className={"btn btn-primary"}
          />
        ) : (
          <SubmitButton
            isLoading={demoVisitorAddMutation.isLoading}
            isSubmitting={isOtpValid()}
            click={handleVerifyOTP}
            text={
              demoVisitorAddMutation.isLoading ? "Verifying..." : "Verify OTP"
            }
            className={"btn btn-primary"}
          />
        )}
      </Modal.Footer>
    </Modal>
  );
};

export default VisitersForm;
