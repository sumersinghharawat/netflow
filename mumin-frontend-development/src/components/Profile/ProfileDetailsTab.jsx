import React, { useEffect, useState } from "react";
import { useForm } from "react-hook-form";
import SubmitButton from "../Common/buttons/SubmitButton";
import { ApiHook } from "../../hooks/apiHook";
import { toast } from "react-toastify";
import { useTranslation } from "react-i18next";
import { useDispatch, useSelector } from "react-redux";
import { updateProfile } from "../../store/reducers/userReducer";
import { useQueryClient } from "@tanstack/react-query";
import Select from 'react-select'

const ProfileDetailsTab = () => {
  const { t } = useTranslation();
  const dispatch = useDispatch();
  const queryClient = useQueryClient();
  const profileData = useSelector(
    (state) => state?.user?.profile?.personalDetails
  );
  const {
    register,
    watch,
    setValue,
    trigger,
    formState: { errors },
  } = useForm();
  const formValues = watch();
  const [isEditModeEnabled, setIsEditModeEnabled] = useState(false);
  const updateMutation = ApiHook.CallUpdatePersonalDetails();
  const toggleEditMode = () => {
    setIsEditModeEnabled(!isEditModeEnabled);
  };
  const genderOptions = [
    { value: "M", label: `${t("male")}` },
    { value: "F", label: `${t("female")}` },
    { value: "O", label: `${t("other")}` },
  ]

  const changeGender = (gender) => {
    setValue("gender",gender?.value)
  }

  useEffect(() => {
    if (profileData) {
      setValue("name", profileData.name);
      setValue("secondName", profileData.secondName);
      setValue("gender", profileData.gender);
    }
  }, [profileData,setValue]);

  const onSubmit = async() => {
    const isValid = await trigger()
    if(isValid) {
      updateMutation.mutate(formValues, {
        onSuccess: (res) => {
          if (res.status) {
            dispatch(
              updateProfile({
                profileDetails: formValues,
              })
            );
            queryClient.invalidateQueries({ queryKey: ["profile"]})
            toast.success(res?.data);
            setIsEditModeEnabled(false)
          }
        },
      });
    }
  };
  return (
    <div id="firstTab" className="tabcontent">
      <div className="editSec">
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
      <h3>{t("personalDetails")}</h3>
      <div className="tabcontent_form_section">
        <div className="mb-3 row tabBlockClass">
          <label
            htmlFor="name"
            className="col-sm-3 col-form-label labelWidthClass"
          >
            {t("firstName")}:
          </label>
          <div className="col-md-9 col-sm-12 col-12">
            <input
              {...register("name", {
                required: t("this_field_is_required"),
                pattern: {
                  value: /^[A-Za-z0-9]+$/,
                  message: t("invalid_format"),
                },
              })}
              defaultValue={profileData?.name}
              type="text"
              id="name"
              className="form-control"
              disabled={!isEditModeEnabled}
            />
            {errors.name && (
              <span className="validation-error-message">
                {errors.name.message}
              </span>
            )}
          </div>
        </div>
        <div className="mb-3 row tabBlockClass">
          <label
            htmlFor="secondName"
            className="col-sm-3 col-form-label labelWidthClass"
          >
            {t("lastName")}:
          </label>
          <div className="col-md-9 col-sm-12 col-12">
            <input
              {...register("secondName", {
                pattern: {
                  value: /^[A-Za-z0-9]+$/,
                  message: t("invalid_format"),
                },
              })}
              defaultValue={profileData?.secondName}
              type="text"
              id="secondName"
              className="form-control"
              disabled={!isEditModeEnabled}
            />
            {errors.secondName && (
              <span className="validation-error-message">
                {errors.secondName.message}
              </span>
            )}
          </div>
        </div>
        <div className="mb-3 row tabBlockClass">
          <label
            htmlFor="3"
            className="col-sm-3 col-form-label labelWidthClass"
          >
            {t("gender")}:
          </label>
          <div className="col-md-9 col-sm-12 col-12">
            <Select
            {...register("gender")}
            id="3"
            value={genderOptions.find((item) => item.value === formValues?.gender)}
            isDisabled={!isEditModeEnabled}
            isSearchable={false}
            options={genderOptions}
            onChange={changeGender}
            />
          </div>
        </div>
        <div
          className={`paymenytLinkBtn ${isEditModeEnabled ? "disabled" : ""}`}
        >
          <SubmitButton
            isSubmitting={!isEditModeEnabled}
            className="btn"
            text="update"
            click={onSubmit}
          />
        </div>
      </div>
    </div>
  );
};

export default ProfileDetailsTab;
