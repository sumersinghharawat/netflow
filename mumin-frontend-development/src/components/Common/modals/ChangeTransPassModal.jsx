import React, { useState } from "react";
import { Form, Modal } from "react-bootstrap";
import { ApiHook } from "../../../hooks/apiHook";
import { toast } from "react-toastify";
import { useTranslation } from "react-i18next";
import SubmitButton from "../buttons/SubmitButton";

const ChangeTransPassModal = ({ showModal, onHide, passwordPolicy }) => {
  const { t } = useTranslation()
  const [formData, setFormdata] = useState({
    currentPassword: "",
    newPassword: "",
    passwordConfirm: "",
  });
  const [errorMessage, setErrorMessage] = useState({
    currentPassword: null,
    newPassword: null,
    passwordConfirm: null,
  });
  const changeTransactionPasswordMutation = ApiHook.CallChangeTransactionPassword();

  const isFormValid = () => {
    const { currentPassword, newPassword, passwordConfirm } = formData;
    const { minLength, mixedCase, number, spChar, enablePolicy } =
      passwordPolicy || {};

    if (!enablePolicy) {
      // If password policy is not enabled, only check for required fields and matching passwords
      const minLengthCheck = newPassword.length >= minLength;
      return (
        minLengthCheck &&
        currentPassword.trim() !== "" &&
        newPassword.trim() !== "" &&
        passwordConfirm.trim() !== "" &&
        newPassword === passwordConfirm
      );
    }

    // Password policy is enabled, apply all checks
    const minLengthCheck = newPassword.length >= minLength;
    const mixedCaseCheck = mixedCase ? /[A-Z]/.test(newPassword) : false;
    const numberCheck = number ? /\d/.test(newPassword) : false;
    const spCharCheck = spChar ? /[!@#$%^&*]/.test(newPassword) : false;

    return (
      currentPassword.trim() !== "" &&
      newPassword.trim() !== "" &&
      passwordConfirm.trim() !== "" &&
      newPassword === passwordConfirm &&
      minLengthCheck &&
      mixedCaseCheck &&
      numberCheck &&
      spCharCheck
    );
  };

  const handleChange = (e) => {
    const { id, value } = e.target;
    setFormdata((prevData) => ({
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
        [id]: t("required"),
      }));
    }
  };

  const changePassword = () => {
    if (!isFormValid()) {
      const { currentPassword, newPassword, passwordConfirm } = formData;
      if (currentPassword === "") {
        setErrorMessage((prevData) => ({
          ...prevData,
          currentPassword: t("required"),
        }));
      }

      if (newPassword === "") {
        setErrorMessage((prevData) => ({
          ...prevData,
          newPassword: t("required"),
        }));
      }
      if (passwordConfirm === "") {
        setErrorMessage((prevData) => ({
          ...prevData,
          passwordConfirm: t("required"),
        }));
      } else {
        if (formData.newPassword.length < passwordPolicy?.minLength) {
          setErrorMessage((prevData) => ({
            ...prevData,
            newPassword: t('MinimumlengthIs', { minLength: passwordPolicy?.minLength }),
          }));
        }
      }
      if (passwordPolicy?.enablePolicy) {
        if (passwordConfirm === "") {
          setErrorMessage((prevData) => ({
            ...prevData,
            passwordConfirm: t("required"),
          }));
        } else {
          if (passwordPolicy?.mixedCase) {
            // Check for at least one uppercase letter
            if (!/[A-Z]/.test(formData.newPassword)) {
              setErrorMessage((prevData) => ({
                ...prevData,
                newPassword:
                  "Password must contain at least one uppercase letter",
              }));
            }
          }

          if (passwordPolicy?.number) {
            // Check for at least one digit
            if (!/\d/.test(formData.newPassword)) {
              setErrorMessage((prevData) => ({
                ...prevData,
                newPassword: "Password must contain at least one digit",
              }));
            }
          }

          if (passwordPolicy?.spChar) {
            // Check for at least one special character
            if (!/[!@#$%^&*]/.test(formData.newPassword)) {
              setErrorMessage((prevData) => ({
                ...prevData,
                newPassword:
                  "Password must contain at least one special character",
              }));
            }
          }
        }
      }
      if (!(formData.newPassword === formData.passwordConfirm)) {
        setErrorMessage((prevData) => ({
          ...prevData,
          passwordConfirm: "Passwords must match",
        }));
      }
    } else {
      changeTransactionPasswordMutation.mutateAsync(formData, {
        onSuccess: (res) => {
          if(res?.status) {
            toast.success(t(res?.data));
            onHide();
            setFormdata({
              currentPassword: "",
              newPassword : "",
              passwordConfirm : ""
            })
          }
          if(res?.code) {
            setErrorMessage((prevData) => ({
              ...prevData,
              currentPassword:t(res?.description)
            }));
          } else {
            console.log(res.message);
          }
        },
      });
    }
  };

  return (
    <Modal show={showModal} onHide={onHide} dialogClassName="custom-modal-dialog">
      <Modal.Body style={{ padding: "0px" }}>
        <div className="row align-items-center">
          <div className="col-md-6">
            <div className="change_password_modal_left_sec">
              <h5 className="modal-title" id="exampleModalLabel">
                {t("resetTransactionPassword")}
              </h5>
              <div className="change_password_image">
                <img src="/images/change_password_img.png" alt="" />
              </div>
            </div>
          </div>
          <div className="col-md-6">
            <div className="modal_change_password_right_cnt">
            <div className="form-group mb-2">
                <Form.Group>
                  <Form.Label>{t("currentTransactionPassword")}</Form.Label>
                  <Form.Control
                    id="currentPassword"
                    type="password"
                    value={formData?.currentPassword}
                    onChange={(e) => handleChange(e)}
                    isInvalid={errorMessage?.currentPassword !== null}
                  />
                  <Form.Control.Feedback type="invalid">
                    {errorMessage.currentPassword}
                  </Form.Control.Feedback>
                </Form.Group>
              </div>
              <div className="form-group mb-2">
                <Form.Group>
                  <Form.Label>{t("newTransactionPassword")}</Form.Label>
                  <Form.Control
                    id="newPassword"
                    type="password"
                    value={formData?.newPassword}
                    onChange={(e) => handleChange(e)}
                    isInvalid={errorMessage?.newPassword !== null}
                  />
                  <Form.Control.Feedback type="invalid">
                    {errorMessage.newPassword}
                  </Form.Control.Feedback>
                </Form.Group>
              </div>
              <div className="form-group mb-2">
                <Form.Group>
                  <Form.Label>{t("repeatTransactionPassword")}</Form.Label>
                  <Form.Control
                    id="passwordConfirm"
                    type="password"
                    value={formData?.passwordConfirm}
                    onChange={(e) => handleChange(e)}
                    isInvalid={errorMessage?.passwordConfirm !== null}
                  />
                  <Form.Control.Feedback type="invalid">
                    {errorMessage.passwordConfirm}
                  </Form.Control.Feedback>
                </Form.Group>
              </div>
              <div className="modal-footer">
              <button type="button" className="btn btn-secondary" onClick={onHide}>
                  {t('close')}
                </button>
                <SubmitButton
                  className="btn btn-primary"
                  text={
                    changeTransactionPasswordMutation.isLoading
                      ? "updating.."
                      : "saveChanges"
                  }
                  click={changePassword}
                  isSubmitting={changeTransactionPasswordMutation.isLoading}
                />
              </div>
            </div>
          </div>
        </div>
      </Modal.Body>
    </Modal>
  );
};

export default ChangeTransPassModal;
