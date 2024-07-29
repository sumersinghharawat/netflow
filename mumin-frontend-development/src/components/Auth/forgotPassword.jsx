import React, { useEffect, useState } from "react";
import { useNavigate, useParams } from "react-router";
import { ApiHook } from "../../hooks/apiHook";
import { toast } from "react-toastify";
import { useTranslation } from "react-i18next";
import PasswordChecklist from "react-password-checklist";
import { forgetPasswordValidator, passwordRules } from "../../Validator/register";
import { useForm } from "react-hook-form";

export const ForgotPasswordForm = () => {
  const {
    register,
    trigger,
    watch,
    formState: { errors },
  } = useForm();
  const formValues = watch();
  const [confirmPassword, setConfirmPassword] = useState("");
  const [validationError, setValidationError] = useState("");
  const params = useParams();
  const navigate = useNavigate();
  const { t } = useTranslation();
  const verifyHashKey = ApiHook.CallVerifyForgotPassword({
    hash: params.hashKey,
  });
  const passwordMutation = ApiHook.CallChangeForgotPassword();

  const handleConfirmPasswordChange = (e) => {
    setConfirmPassword(e.target.value);
  };

  const handleSubmit = () => {
    // Password validation logic
    if (formValues?.password !== confirmPassword) {
      setValidationError("Passwords do not match");
      return;
    }

    passwordMutation.mutate(
      { password: formValues?.password, hash: params.hashKey },
      {
        onSuccess: (res) => {
          if (res.status) {
            toast.success(res.data);
            setTimeout(() => {
              navigate("/login");
            }, 3000);
          } else {
            toast.error(res?.data?.description);
            setTimeout(() => {
              navigate("/login");
            }, 3000);
          }
        },
      }
    );

    // Reset validation error message
    setValidationError("");
  };

  if (verifyHashKey?.data?.status === false) {
    toast.error(verifyHashKey?.data?.data?.description);
    // setTimeout(() => {
    //   navigate("/login");
    // }, 3000);
    navigate("/login");
  }
  return (
    verifyHashKey.data?.status && (
      <div>
        <section className="loginSection">
          <div className="container centerDiv">
            <div className="loginBgImg"></div>
            <div className="row justify-content-center">
              <div className="col-md-6 logincredDetail">
                <div className="loginBg">
                  <div className="loginFormSec p-5">
                    <div className="loginLogo">
                      <img src="/images/logo_user.png" alt="" />
                    </div>
                    <p>{t("newPassword")}</p>
                    <div className="loginFormSec mt-5">
                      <p className="text-start text-dark">
                        {`Username: ${verifyHashKey.data?.data?.data?.username}`}
                      </p>
                      <div className="passwordInput">
                        <label htmlFor="password">{t("newPassword")}</label>
                        <input
                          type="password"
                          id="password"
                          name="password"
                          className={`form-control ${
                            errors["register"] ? "error-field" : ""
                          }`}
                          placeholder={t("newPassword")}
                          {...register(
                            "password",
                            forgetPasswordValidator(verifyHashKey?.data?.data?.data, t)
                          )}
                          onBlur={async () => await trigger("password")}
                        />
                      </div>
                      <div className="passwordInput">
                        <label htmlFor="confirmPassword">
                          Confirm Password
                        </label>
                        <input
                          type="password"
                          id="confirmPassword"
                          name="confirmPassword"
                          placeholder="Confirm Password"
                          value={confirmPassword}
                          onChange={handleConfirmPasswordChange}
                        />
                         <PasswordChecklist
                            rules={passwordRules(verifyHashKey?.data?.data?.data?.passwordPolicy)}
                            minLength={verifyHashKey?.data?.data?.data?.passwordPolicy?.minLength}

                            value={watch("password", "")}
                          />
                      </div>
                      {validationError && (
                        <p className="text-danger">{validationError}</p>
                      )}
                    </div>
                    <div className="loginBtn">
                      <button
                        type="submit"
                        className="btn"
                        onClick={handleSubmit}
                      >
                        Change
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>
    )
  );
};
