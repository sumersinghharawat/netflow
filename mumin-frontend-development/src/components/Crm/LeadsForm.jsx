import React, { useState, useRef } from "react";
import { useParams } from "react-router";
import { ApiHook } from "../../hooks/apiHook";
import { Form } from "react-bootstrap";
import SubmitButton from "../Common/buttons/SubmitButton";
import { toast } from "react-toastify";
import { PhoneInput } from "react-international-phone";
import { useForm } from "react-hook-form";
import Select from "react-select";
import { useTranslation } from "react-i18next";

const LeadsForm = () => {
  const { t } = useTranslation();
  const selectInputRef = useRef();
  const params = useParams();
  const adminUsername = params?.adminUsername;
  const hash = params?.hash;
  const username = params?.username;

  // api
  ApiHook.CallGetReplicaApi(adminUsername);
  const companyDetails = ApiHook.CallGetCompanyDetails(username, hash);
  const AddLcpLeadMutation = ApiHook.CallAddLcpLead();
  // state & declaration
  const {
    setError,
    register,
    watch,
    setValue,
    trigger,
    reset,
    formState: { errors },
  } = useForm({
    defaultValues: {
      firstName: "",
      lastName: "",
      emailId: "",
      skypeId: "",
      mobileNo: "",
      countryId: null,
      description: "",
    },
  });
  const formValues = watch();
  const countryList = (data) => {
    return companyDetails?.countries.map((item) => ({
      label: item?.name,
      value: item?.id,
    }));
  };
  const [phoneNumber, setPhoneNumber] = useState("");
  const [selectedCountry, setSelectedCountry] = useState(formValues?.countryId);

  // validation
  const isPhoneValid = (phone) => {
    return phone.length >= 7 && !isNaN(phone);
  };
  const isPhoneNumberValid = isPhoneValid(phoneNumber);

  // handle Functions
  const changeCountry = (country) => {
    setSelectedCountry(country?.value);
    setValue("countryId", country?.value); // Update the form value for country
  };

  const handlePhoneNumber = (phone) => {
    setPhoneNumber(phone);
    setValue("mobileNo", phone);
    setError("mobileNo", { message: "" });
  };

  const handleSubmit = async (e) => {
    if (!phoneNumber) {
      setError("mobileNo", { message: t("this_field_is_required") });
    }
    if (!isPhoneNumberValid) {
      setError("mobileNo", { message: t("min_length") });
    }
    const isValid = await trigger();
    e.preventDefault();
    const submitData = {
      ...formValues,
      hash,
      username,
    };
    if (isValid & isPhoneNumberValid) { 
      AddLcpLeadMutation.mutateAsync(submitData, {
        onSuccess: (res) => {
          if (res.status) {
            toast.success(res.data);
            reset();
            setPhoneNumber("");
            setSelectedCountry(null);
            if (selectInputRef.current && selectInputRef.current.select) {
              selectInputRef.current.select.clearValue();
            }
          } 
        },
      });
    }
  };
  return (
    <section className="lcpBgSection">
      <div className="container centerDiv">
        <div className="box_view_section">
          <div className="box_view_lcp_left_sec">
            <div className="loginLogo">
              <img src={companyDetails?.companyProfile?.logo} alt="" />
            </div>
            <div className="lead_page_img">
              <img src="/images/lead_distribution.svg" alt="" />
            </div>
            <p className="lcpLeftTxt">
              <strong>{t(`“Connecting Dreams, Growing Together”`)}</strong>
              <span>{t("formIntro")}</span>
            </p>
          </div>
          <div className="box_view_lcp">
            <div className="loginLogo_right">
              <img src={companyDetails?.companyProfile?.logo} alt="" />
            </div>
            <h4>{t("fillTheForm")}</h4>
            <Form>
              <Form.Group className="mb-2">
                <Form.Label>
                  {t("firstName")} <span>*</span>
                </Form.Label>
                <Form.Control
                  {...register("firstName", {
                    required: t("this_field_is_required"),
                    pattern: {
                      value: /^[A-Za-z0-9]+$/,
                      message: t("invalid_format"),
                    },
                  })}
                  id="firstName"
                  type="text"
                  placeholder={t("firstName")}
                  required
                />
                {errors.firstName && (
                  <span className="validation-error-message">
                    {errors.firstName.message}
                  </span>
                )}
              </Form.Group>
              <Form.Group className="mb-2">
                <Form.Label>{t("lastName")}</Form.Label>
                <Form.Control
                  {...register("lastName")}
                  id="lastName"
                  type="text"
                  placeholder={t("lastName")}
                />
              </Form.Group>
              <Form.Group className="mb-2">
                <Form.Label>
                  {t("emailAddress")}
                  <span>*</span>
                </Form.Label>
                <Form.Control
                  {...register("emailId", {
                    required: t("this_field_is_required"),
                    pattern: {
                      value: /^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]+$/,
                      message: t("invalid_email_format"),
                    },
                  })}
                  id="emailId"
                  type="text"
                  placeholder={t("email")}
                  required
                />
                {errors.emailId && (
                  <span className="validation-error-message">
                    {errors.emailId.message}
                  </span>
                )}
              </Form.Group>
              <Form.Group className="mb-2">
                <Form.Label>Skype ID</Form.Label>
                <Form.Control
                  {...register("skypeId")}
                  id="skypeId"
                  type="text"
                  placeholder="Skype ID"
                  required
                />
              </Form.Group>
              <Form.Group className="mb-2">
                <Form.Label>
                  {t("mobile")}
                  <span>*</span>
                </Form.Label>
                <PhoneInput
                  defaultCountry="us"
                  id="mobileNo"
                  value={phoneNumber}
                  onChange={handlePhoneNumber}
                />
                {errors.mobileNo && (
                  <span className="validation-error-message">
                    {errors.mobileNo.message}
                  </span>
                )}
              </Form.Group>
              <Form.Group>
                <Form.Label>
                  {t("country")}
                </Form.Label>
                <Select
                  {...register("countryId")}
                  ref={selectInputRef}
                  id="country"
                  value={countryList(companyDetails?.countries)?.find(
                    (item) => item.value === selectedCountry
                  )}
                  options={countryList(companyDetails?.countries)}
                  onChange={changeCountry}
                  isSearchable={false}
                />
                {errors.countryId && (
                  <span className="validation-error-message">
                    {errors.countryId.message}
                  </span>
                )}
              </Form.Group>
              <Form.Group className="mb-2">
                <Form.Label>{t("description")}</Form.Label>
                <Form.Control
                  {...register("description")}
                  as="textarea"
                  id="description"
                  type="text"
                  placeholder={t("description")}
                />
              </Form.Group>
              <SubmitButton
                isLoading={AddLcpLeadMutation?.isLoading}
                click={handleSubmit}
                text="submit"
                className={"btn btn-primary submit_btn"}
              />
            </Form>
          </div>
        </div>
        <p className="lcp_copyright">Copyright © 2023 Infinte MLM Software.</p>
      </div>
    </section>
  );
};

export default LeadsForm;
