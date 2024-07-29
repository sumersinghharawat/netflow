import React, { useState } from "react";
import ReplicaRegisterForm from "../../components/Replica/ReplicaRegisterForm";
import ReplicaRegisterModal from "../../components/Common/modals/ReplicaRegisterModal";
import anime from "animejs/lib/anime.es.js";
import { useTranslation } from "react-i18next";
import { ApiHook } from "../../hooks/apiHook";

const ReplicaRegisterLayout = () => {
  const [showRegisterModal, setShowRegisterModal] = useState(false);
  const [activeStep, setActiveStep] = useState(1);
  const { t } = useTranslation();

  // ----------------------------------------- API --------------------------------------------
  const registerFields1 = ApiHook.CallReplicaRegisterFields();

  if (registerFields1.isFetching) {
    return (
      <div className="theme-loader">
        <div className="spinner">
          <div className="spinner-bounce one"></div>
          <div className="spinner-bounce two"></div>
          <div className="spinner-bounce three"></div>
        </div>
      </div>
    );
  }

  const handleToggleRegisterModal = () => {
    setShowRegisterModal(!showRegisterModal);
  };
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
      <div className="container ">
        <div className="register_row align-items-center justify-content-center">
          <div className="main-regsiter-left-section">
            <div className="register-left-cnt-row opacity-1">
              <h2>{t("registerNow")}</h2>
            </div>
            {registerFields1?.data?.data?.registrationSteps?.map(
              (step, index) => (
                <div
                  key={index}
                  className={`register-left-cnt-row ${
                    index === activeStep - 1 ? "active" : ""
                  }`}
                >
                  <span>{step.step}</span> {t(`${step.label}`)}
                </div>
              )
            )}
          </div>
          <ReplicaRegisterForm
            activeStep={activeStep}
            setActiveStep={setActiveStep}
            handleToggleRegisterModal={handleToggleRegisterModal}
            animateStepTransition={animateStepTransition}
            data={registerFields1?.data?.data}
          />
        </div>
      </div>
      <ReplicaRegisterModal
        show={showRegisterModal}
        handleClose={handleToggleRegisterModal}
        data={
          registerFields1?.data?.data?.termsAndCondition?.termsAndConditions
        }
      />
    </>
  );
};

export default ReplicaRegisterLayout;
