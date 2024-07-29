import React from "react";
import { toast } from "react-toastify";
import { useForm } from "react-hook-form";
import { useTranslation } from "react-i18next";
import { ApiHook } from "../../hooks/apiHook";
import { QueryClient } from "@tanstack/react-query";
import { useLocation } from "react-router-dom";

const ContactUs = ({ companyDetails }) => {
  const { t } = useTranslation();
  const location = useLocation();
  const {
    setError,
    register,
    watch,
    setValue,
    trigger,
    formState: { errors },
  } = useForm();
  const formValues = watch();
    // Extracting username from the React Router location
    const match = location.pathname.match(/\/replica\/([^/]+)\//);
    const username = match ? match[1] : "";

  const contactUploadMutation = ApiHook.CallReplicaContactUpload();
  const handleSubmit = async () => {
    setValue("referralId",)
    const isValid = await trigger();
    if (isValid) {
      const payload = {
        referralId:username,
        contactData:{
          name:formValues.name,
          email:formValues.email,
          address:formValues.address,
          phone: formValues.phone,
          contactInfo: formValues.message
        }
      }
      contactUploadMutation.mutateAsync(payload, {
        onSuccess: (res) => {
          if (res.status) {
            toast.success(res?.data);
          }
        },
      });
    }
  };
  return (
    <div className="contact__three page section-padding" id="contact">
      <div className="container">
        <div className="row align-items-center">
          <div className="col-xl-6">
            <div className="contact__two-info">
              <h2 className="mb-60 lg-mb-30">
                <span>{t('getInTouch')}</span>
              </h2>
              <div className="contact__two-info-item">
                <h6>
                  {t('officeAddress')} <span>:</span>
                </h6>
                {companyDetails?.address && (
                  <strong>{companyDetails?.address}</strong>
                )}
              </div>
              <div className="contact__two-info-item">
                <h6>
                  {t('email')} <span>:</span>
                </h6>
                <span>
                  {companyDetails?.email && (
                    <strong>{companyDetails?.email}</strong>
                  )}
                </span>
              </div>
              <div className="contact__two-info-item">
                <h6>
                  {t('mobile')} <span>:</span>
                </h6>
                <span>
                  {companyDetails?.phone && (
                    <strong>{companyDetails?.phone}</strong>
                  )}
                </span>
              </div>
            </div>
          </div>
          <div className="col-xl-6">
            <div className="contact__three-form t-center">
              <div className="contact__three-form-title">
                <h2>{t('contactUs')}</h2>
              </div>
              <div className="row">
                <div className="col-md-6 mb-30">
                  <div className="contact__two-right-form-item contact-item">
                    <span className="fal fa-user"></span>
                    <input
                      {...register("name", {
                        required: t("this_field_is_required"),
                      })}
                      className="form-control"
                      type="text"
                      placeholder={t('fullName')}
                      required
                    />
                    {errors.name && errors.name.type === "required" && (
                      <span className="validation-error-message">
                        {errors.name.message}
                      </span>
                    )}
                  </div>
                </div>
                <div className="col-md-6 md-mb-30">
                  <div className="contact__two-right-form-item contact-item">
                    <span className="far fa-envelope-open"></span>
                    <input
                      {...register("email", {
                        required: t("this_field_is_required"),
                      })}
                      className="form-control"
                      type="text"
                      name="email"
                      placeholder={t("email")}
                      required
                    />
                    {errors.mail && errors.mail.type === "required" && (
                      <span className="validation-error-message">
                        {errors.mail.message}
                      </span>
                    )}
                  </div>
                </div>
                <div className="col-md-6 mb-30">
                  <div className="contact__two-right-form-item contact-item">
                    <span className="fal fa-user"></span>
                    <input
                      {...register("phone", {
                        required: t("this_field_is_required"),
                      })}
                      className="form-control"
                      type="text"
                      name="phone"
                      placeholder={t("mobile")}
                      required
                    />
                    {errors.phone && errors.phone.type === "required" && (
                      <span className="validation-error-message">
                        {errors.phone.message}
                      </span>
                    )}
                  </div>
                </div>
                <div className="col-md-6 md-mb-30">
                  <div className="contact__two-right-form-item contact-item">
                    <span className="far fa-envelope-open"></span>
                    <input
                      {...register("address", {
                        required: t("this_field_is_required"),
                      })}
                      className="form-control"
                      type="text"
                      name="address"
                      placeholder={t("address")}
                      required
                    />
                    {errors.address && errors.address.type === "required" && (
                      <span className="validation-error-message">
                        {errors.address.message}
                      </span>
                    )}
                  </div>
                </div>
                <div className="col-md-12 mb-30">
                  <div className="contact__two-right-form-item contact-item">
                    <span className="far fa-comments"></span>
                    <textarea
                      {...register("message", {
                        required: t("this_field_is_required"),
                      })}
                      name="message"
                      className="form-control"
                      placeholder={t("message")}
                    ></textarea>
                  </div>
                </div>
                <div className="col-md-12">
                  <div className="contact__two-right-form-item">
                    <button className="btn-one" onClick={handleSubmit}>
                      {t("send")}
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default ContactUs;
