import React, { useState } from "react";
import anime from "animejs/lib/anime.es.js";
import RegisterModal from "../../components/Common/modals/RegisterModal";
import RegisterForm from "../../components/Register/RegisterForm";
import { ApiHook } from "../../hooks/apiHook";
import { useSelector } from "react-redux";
import { useTranslation } from "react-i18next";
import { useLocation } from "react-router-dom";

const RegisterLayout = () => {
  const location = useLocation();
  let placement = location?.state?.parent;
  let position = location?.state?.position;
  const stepsLabel = [
    { step: "01", label: "pick_your_products" },
    { step: "02", label: "contact_information" },
    { step: "03", label: "login_information" },
    { step: "04", label: "over_view" },
    { step: "05", label: "payment" },
  ];
  const [showRegisterModal, setShowRegisterModal] = useState(false);
  const [activeStep, setActiveStep] = useState(1);
  const { t } = useTranslation();

  const registerFields = ApiHook.CallRegisterFields();
  const userSelectedCurrency = useSelector(
    (state) => state.user?.selectedCurrency
  );

  const handleToggleRegisterModal = () => {
    setShowRegisterModal(!showRegisterModal);
  };

  // animation for the register
  const animateStepTransition = (nextStep) => {
    const containerElement = document.querySelector("#animation");
    const nextStepElement = document.querySelector(
      `.register-left-cnt-row:nth-child(${nextStep + 1})`
    );

    // Set initial position of the next step
    anime.set(containerElement, { opacity: 0 });
    anime.set(nextStepElement, { translateY: "100%" });

    // Animate the next step in
    anime({
      targets: containerElement,
      opacity: [0, 1],
      duration: 1000,
      easing: "easeInOutQuad",
    });
    anime({
      targets: nextStepElement,
      translateY: "0%",
      duration: 500,
      easing: "easeInOutQuad",
    });
  };

  return (
    <>
      <div className="page_head_top">{t("register")}</div>
      <div className="container ">
        <div className="register_row align-items-center justify-content-center">
          <div className="main-regsiter-left-section">
            <div className="register-left-cnt-row opacity-1">
              <h2>{t("registerNow")}</h2>
            </div>
            {registerFields.isLoading &&
              stepsLabel?.map((step, index) => (
                <div
                  key={index}
                  className={`register-left-cnt-row ${
                    index === activeStep - 1 ? "active" : ""
                  }`}
                >
                  <span>{step.step}</span> {t(`${step.label}`)}
                </div>
              ))}
            {registerFields?.data?.registrationSteps?.map((step, index) => (
              <div
                key={index}
                className={`register-left-cnt-row ${
                  index === activeStep - 1 ? "active" : ""
                }`}
              >
                <span>{step.step}</span> {t(`${step.label}`)}
              </div>
            ))}
          </div>
          <RegisterForm
            activeStep={activeStep}
            setActiveStep={setActiveStep}
            handleToggleRegisterModal={handleToggleRegisterModal}
            animateStepTransition={animateStepTransition}
            data={registerFields?.data}
            currency={userSelectedCurrency}
            placement={placement}
            position={position}
          />
        </div>
      </div>
      <RegisterModal
        show={showRegisterModal}
        handleClose={handleToggleRegisterModal}
        data={registerFields?.data?.termsAndCondition?.termsAndConditions}
      />
    </>
  );
};

export default RegisterLayout;
