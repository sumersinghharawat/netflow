import React, { useState } from "react";
import { useTranslation } from "react-i18next";
import SubmitButton from "../Common/buttons/SubmitButton";
import { ApiHook } from "../../hooks/apiHook";
import { toast } from "react-toastify";
import { useForm } from "react-hook-form";
import { useQueryClient } from "@tanstack/react-query";
const BankDetailsTab = ({ bank }) => {
  const { t } = useTranslation();
  const queryClient = useQueryClient();
  const {
    register,
    watch,
    trigger,
    formState: { errors },
  } = useForm({
    defaultValues:{
      bankName: bank?.bankName ?? "",
      branchName: bank?.branchName ?? "",
      holderName: bank?.holderName ?? "",
      accountNo: bank?.accountNo ?? "",
      ifsc: bank?.ifsc ?? "",
      pan: bank?.pan ?? "",
    }
  });
  const formValues = watch();
  const [isEditModeEnabled, setIsEditModeEnabled] = useState(false);

  const updateBankMutation = ApiHook.CallUpdateBankDetails(formValues);

  const toggleEditMode = () => {
    setIsEditModeEnabled(!isEditModeEnabled);
  };

  const handleSubmit = async () => {
    const isValid = await trigger();
    if (isValid) {
      updateBankMutation.mutate(formValues, {
        onSuccess: (res) => {
          queryClient.invalidateQueries({ queryKey: ["profile"]})
          toast.success(res?.data);
          setIsEditModeEnabled(false);
        },
      });
    }
  };

  return (
    <div id="thirdTab" className="tabcontent">
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
      <h3>{t("bankDetails")}</h3>
      <div className="tabcontent_form_section">
        <div className="mb-3 row tabBlockClass">
          <label
            htmlFor="bankName"
            className="col-sm-3 col-form-label labelWidthClass"
          >
            {t("bankName")}:
          </label>
          <div className="col-md-9 col-sm-12 col-12">
            <input
              {...register("bankName", {
                required: t("this_field_is_required"),
              })}
              name="bankName"
              type="text"
              className="form-control"
              id="bankName"
              defaultValue={formValues?.bankName}
              disabled={!isEditModeEnabled}
            />
            {errors.bankName && (
              <span className="validation-error-message">
                {errors.bankName.message}
              </span>
            )}
          </div>
        </div>
        <div className="mb-3 row tabBlockClass">
          <label
            htmlFor="branchName"
            className="col-sm-3 col-form-label labelWidthClass"
          >
            {t("branchName")}:
          </label>
          <div className="col-md-9 col-sm-12 col-12">
            <input
              {...register("branchName", {
                required: t("this_field_is_required"),
              })}
              name="branchName"
              type="text"
              className="form-control"
              id="branchName"
              defaultValue={formValues?.branchName}
              disabled={!isEditModeEnabled}
            />
            {errors.branchName && (
              <span className="validation-error-message">
                {errors.branchName.message}
              </span>
            )}
          </div>
        </div>
        <div className="mb-3 row tabBlockClass">
          <label
            htmlFor="accountHolder"
            className="col-sm-3 col-form-label labelWidthClass"
          >
            {t("accountHolder")}:
          </label>
          <div className="col-md-9 col-sm-12 col-12">
            <input
              {...register("holderName", {
                required: t("this_field_is_required"),
              })}
              name="holderName"
              type="text"
              className="form-control"
              id="accountHolder"
              defaultValue={formValues?.holderName}
              disabled={!isEditModeEnabled}
            />
            {errors.holderName && (
              <span className="validation-error-message">
                {errors.holderName.message}
              </span>
            )}
          </div>
        </div>
        <div className="mb-3 row tabBlockClass">
          <label
            htmlFor="accountNumber"
            className="col-sm-3 col-form-label labelWidthClass"
          >
            {t("accountNumber")}:
          </label>
          <div className="col-md-9 col-sm-12 col-12">
            <input
              {...register("accountNo", {
                required: t("this_field_is_required"),
                pattern: {
                  value: /^[0-9]+$/,
                  message: t("only_number"),
                }
              })}
              name="accountNo"
              type="text"
              className="form-control"
              id="accountNumber"
              defaultValue={formValues?.accountNo}
              disabled={!isEditModeEnabled}
            />
            {errors.accountNo && (
              <span className="validation-error-message">
                {errors.accountNo.message}
              </span>
            )}
          </div>
        </div>
        <div className="mb-3 row tabBlockClass">
          <label
            htmlFor="ifscCode"
            className="col-sm-3 col-form-label labelWidthClass"
          >
            {t("ifscCode")}:
          </label>
          <div className="col-md-9 col-sm-12 col-12">
            <input
              {...register("ifsc", {
                required: t("this_field_is_required"),
              })}
              name="ifsc"
              type="text"
              className="form-control"
              id="ifscCode"
              defaultValue={formValues?.ifsc}
              disabled={!isEditModeEnabled}
            />
            {errors.ifsc && (
              <span className="validation-error-message">
                {errors.ifsc.message}
              </span>
            )}
          </div>
        </div>
        <div className="mb-3 row tabBlockClass">
          <label
            htmlFor="pan"
            className="col-sm-3 col-form-label labelWidthClass"
          >
            {t("panNumber")}:
          </label>
          <div className="col-md-9 col-sm-12 col-12">
            <input
              {...register("pan", {
                required: t("this_field_is_required"),
              })}
              name="pan"
              type="text"
              className="form-control"
              id="pan"
              defaultChecked={formValues?.pan}
              disabled={!isEditModeEnabled}
            />
            {errors.pan && (
              <span className="validation-error-message">
                {errors.pan.message}
              </span>
            )}
          </div>
        </div>
        <div
          className={`paymenytLinkBtn ${isEditModeEnabled ? "disabled" : ""}`}
        >
          <SubmitButton
            className="btn"
            isSubmitting={!isEditModeEnabled}
            text="update"
            click={handleSubmit}
          />
        </div>
      </div>
    </div>
  );
};

export default BankDetailsTab;
