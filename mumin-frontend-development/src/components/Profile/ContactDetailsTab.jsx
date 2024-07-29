import React, { useState } from "react";
import { ApiHook } from "../../hooks/apiHook";
import { useForm } from "react-hook-form";
import { useTranslation } from "react-i18next";
import SubmitButton from "../Common/buttons/SubmitButton";
import { toast } from "react-toastify";
import { useQueryClient } from "@tanstack/react-query";
import Select from "react-select";
import { PhoneInput } from "react-international-phone";
import { PhoneNumberUtil } from 'google-libphonenumber';

const ContactDetailsTab = ({ contact, countries }) => {
  const { t } = useTranslation();
  const queryClient = useQueryClient();
  const {
    setError,
    register,
    watch,
    setValue,
    trigger,
    formState: { errors },
  } = useForm({
    defaultValues: {
      address: contact.address ?? "",
      address2: contact.address2 ?? "",
      country: contact.country?.id ?? "",
      state: contact.state?.id ?? "",
      city: contact.city ?? "",
      zipCode: contact.zipCode ?? "",
      mobile: contact?.mobile ?? "",
    },
  });
  const formValues = watch();
  const phoneUtil = PhoneNumberUtil.getInstance();
  const [isEditModeEnabled, setIsEditModeEnabled] = useState(false);
  const updateContactMutation = ApiHook.CallUpdateContactDetails(formValues);
  const [selectedCountry, setSelectedCountry] = useState(formValues?.country);
  const [selectedState, setSelectedState] = useState(formValues?.state);
  const [phoneNumber, setPhoneNumber] = useState(formValues?.mobile);
  const toggleEditMode = () => {
    setIsEditModeEnabled(!isEditModeEnabled);
  };
  const countryList = (data) => {
    return data.map((item) => ({
      label: item?.name,
      value: item?.id,
    }));
  };
  const changeCountry = (country) => {
    setSelectedCountry(country?.value);
    setSelectedState(null); // Clear the selected state
    setValue("state", "");
    setValue("country", country?.value); // Update the form value for country
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
  const changeState = (state) => {
    setSelectedState(state?.value);
    setValue("state", state?.value);
  };
  const handlePhoneNumber = (phone) => {
    setPhoneNumber(phone);
    setValue("mobile",phone)
    setError("mobile",{message:""});
  };
  const isPhoneValid = (phone) => {
    try {
      return phoneUtil.isValidNumber(phoneUtil.parseAndKeepRawInput(phone));
    } catch (error) {
      return false;
    }
  }
  const isPhoneNumberValid =isPhoneValid(phoneNumber)

  const onSubmit = async () => {
    if(!isPhoneNumberValid) {
      setError("mobile",{message:t("invalidPhone")});
    }
    const isValid = await trigger();
    if (isValid & isPhoneNumberValid) {
      updateContactMutation.mutate(formValues, {
        onSuccess: (res) => {
          if (res.status) {
            queryClient.invalidateQueries({ queryKey: ["profile"] });
            toast.success(res?.data);
            setIsEditModeEnabled(false);
          }
        },
      });
    }
  };

  return (
    <div id="secondTab" className="tabcontent">
      <div className={`editSec ${isEditModeEnabled ? "disabled" : ""}`}>
        <div className="editBg">
          <span
            style={{ textDecoration: "none", cursor: "pointer" }}
            onClick={toggleEditMode}
          >
            <i
              className="fa-solid fa-pen-to-square"
              style={{ color: "#32009c" }}
            ></i>
          </span>
        </div>
      </div>
      <h3>{t("contactDetails")}</h3>
      <div className="tabcontent_form_section">
        <div className="row">
          <div className="col-md-12">
            <div className="mb-3 row tabBlockClass">
              <label
                htmlFor="1"
                className="col-sm-3 col-form-label labelWidthClass"
              >
                {t("addressLine1")}:
              </label>
              <div className="col-md-9 col-sm-12 col-12">
                <input
                  {...register("address", {
                    required: t("this_field_is_required"),
                  })}
                  defaultValue={formValues?.address}
                  type="text"
                  id="1"
                  className="form-control"
                  disabled={!isEditModeEnabled}
                />
                {errors.address && (
                  <span className="validation-error-message">
                    {errors.address.message}
                  </span>
                )}
              </div>
            </div>
          </div>
          <div className="col-md-12">
            <div className="mb-3 row tabBlockClass">
              <label
                htmlFor="2"
                className="col-sm-3 col-form-label labelWidthClass"
              >
                {t("addressLine2")}:
              </label>
              <div className="col-md-9 col-sm-12 col-12">
                <input
                  {...register("address2", {
                    required: t("this_field_is_required"),
                  })}
                  defaultValue={formValues?.address2}
                  type="text"
                  className="form-control"
                  id="2"
                  disabled={!isEditModeEnabled}
                />
                {errors.address2 && errors.address2.type === "required" && (
                  <span className="validation-error-message">
                    {errors.address2.message}
                  </span>
                )}
              </div>
            </div>
          </div>
          <div className="col-md-12">
            <div className="mb-3 row tabBlockClass">
              <label
                htmlFor="3"
                className="col-sm-3 col-form-label labelWidthClass"
              >
                {t("country")}:
              </label>
              <div className="col-md-9 col-sm-12 col-12">
                <Select
                  {...register("country", {
                    required: t("this_field_is_required"),
                  })}
                  id="3"
                  value={countryList(countries)?.find(
                    (item) => item.value === selectedCountry
                  )}
                  isDisabled={!isEditModeEnabled}
                  options={countryList(countries)}
                  onChange={changeCountry}
                />
                {errors.country && errors.country.type === "required" && (
                  <span className="validation-error-message">
                    {errors.country.message}
                  </span>
                )}
              </div>
            </div>
          </div>
          <div className="col-md-12">
            <div className="mb-3 row tabBlockClass">
              <label
                htmlFor="4"
                className="col-sm-3 col-form-label labelWidthClass"
              >
                {t("state")}:
              </label>
              <div className="col-md-9 col-sm-12 col-12">
                <Select
                  {...register("state", {
                    required: t("this_field_is_required"),
                  })}
                  id="4"
                  value={stateList(countries).find(
                    (state) => state?.value === selectedState
                  )}
                  isDisabled={!isEditModeEnabled}
                  options={stateList(countries)}
                  onChange={changeState}
                />
                {errors.state && (
                  <span className="validation-error-message">
                    {errors.state.message}
                  </span>
                )}
              </div>
            </div>
          </div>
          <div className="col-md-12">
            <div className="mb-3 row tabBlockClass">
              <label
                htmlFor="5"
                className="col-sm-3 col-form-label labelWidthClass"
              >
                {t("city")}:
              </label>
              <div className="col-md-9 col-sm-12 col-12">
                <input
                  {...register("city", {
                    required: t("this_field_is_required"),
                  })}
                  type="text"
                  className="form-control"
                  id="5"
                  defaultValue={formValues?.city}
                  disabled={!isEditModeEnabled}
                />
                {errors.city && (
                  <span className="validation-error-message">
                    {errors.city.message}
                  </span>
                )}
              </div>
            </div>
          </div>
          <div className="col-md-12">
            <div className="mb-3 row tabBlockClass">
              <label
                htmlFor="6"
                className="col-sm-3 col-form-label labelWidthClass"
              >
                {t("zipCode")}:
              </label>
              <div className="col-md-9 col-sm-12 col-12">
                <input
                  {...register("zipCode", {
                    required: t("this_field_is_required"),
                    pattern: {
                      value: /^[0-9]+$/,
                      message: t("only_number"),
                    },
                  })}
                  type="text"
                  className="form-control"
                  id="6"
                  defaultValue={formValues?.zipCode}
                  disabled={!isEditModeEnabled}
                />
                {errors.zipCode && (
                  <span className="validation-error-message">
                    {errors.zipCode.message}
                  </span>
                )}
              </div>
            </div>
          </div>
          <div className="col-md-12">
            <div className="mb-3 row tabBlockClass">
              <label
                htmlFor="7"
                className="col-sm-3 col-form-label labelWidthClass"
              >
                {t("mobileNumber")}:
              </label>
              <div className="col-md-9 col-sm-12 col-12">
                <PhoneInput
                defaultCountry="us"
                value={phoneNumber}
                onChange={handlePhoneNumber}
                disabled={!isEditModeEnabled}
                />
                {errors.mobile && (
                  <span className="validation-error-message">
                    {errors.mobile.message}
                  </span>
                )}
              </div>
            </div>
          </div>
        </div>

        <div
          className={`paymenytLinkBtn ${isEditModeEnabled ? "disabled" : ""}`}
        >
          <SubmitButton
            id={"1"}
            isSubmitting={!isEditModeEnabled}
            click={onSubmit}
            text="update"
            className="btn"
          />
        </div>
      </div>
    </div>
  );
};

export default ContactDetailsTab;
