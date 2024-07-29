import React, { useState } from "react";
import { NavLink, useNavigate } from "react-router-dom";
import CheckoutPackage from "../../components/shopping/CheckoutPackage";
import CheckoutAddress from "../../components/shopping/CheckoutAddress";
import CheckoutOrderSummary from "../../components/shopping/CheckoutOrderSummary";
import CheckoutPayment from "../../components/shopping/CheckoutPayment";
import anime from "animejs/lib/anime.es.js";
import { ApiHook } from "../../hooks/apiHook";
import { useTranslation } from "react-i18next";
import { useForm } from "react-hook-form";
import { toast } from "react-toastify";

const CheckoutLayout = () => {
  const [activeStep, setActiveStep] = useState(1);
  const [show, setShow] = useState(false);
  const navigate = useNavigate();
  const { t } = useTranslation();
  const {
    register,
    setValue,
    trigger,
    watch,
    formState: { errors },
  } = useForm({
    defaultValues: {
      product: [],
    },
  });
  const steps = ["Step 1", "Step 2", "Step 3", "Step 4"];
  const formValues = watch();

  // ----------------------------------- API -------------------------------------
  const items = ApiHook.CallCartItems();
  const removeCartMutation = ApiHook.CallRemoveCartItem();
  const address = ApiHook.CallGetAddress();
  const paymentMethods = ApiHook.CallPaymentMethods("repurchase");

  const handleNext = async () => {
    let isValid = await trigger();
    if (activeStep === 2) {
      if (!formValues.addressId) {
        if (address.data?.length === 0) {
          toast.error(t("add_new_address"));
        }
        isValid = false;
      }
    }
    if (activeStep === 3) {
      const list = items.data?.map((product) => ({
        name: product.name,
        quantity: product.quantity,
        totalAmount: product.price * product.quantity,
      }));
      setValue("product", list);
      setValue("totalAmount", totalAmount);
    }
    if (isValid) {
      const nextStep = activeStep + 1;
      animateStepTransition(nextStep);
      setActiveStep(nextStep);
    }
  };

  const handleBack = () => {
    const prevStep = activeStep - 1;
    animateStepTransition(prevStep);
    setActiveStep(prevStep);
  };

  const animateStepTransition = () => {
    const containerElement = document.querySelector("#animation");
    // Set initial position of the next step
    anime.set(containerElement, { opacity: 0 });
    // Animate the next step in
    anime({
      targets: containerElement,
      opacity: [0, 1],
      duration: 1100,
      easing: "easeInOutQuad",
    });
  };

  const handleClearAll = () => {
    removeCartMutation.mutate(
      { packageId: "all" },
      {
        onSuccess: () => {
          navigate("/shopping");
        },
      }
    );
  };

  const totalAmount = items?.data?.reduce(
    (total, product) => total + product.price * product.quantity,
    0
  );

  return (
    <>
      <div className="page_head_top">{t("checkout")}</div>
      <div className="checkout_contant_area_section">
        {activeStep === 1 && (
          <div className="checkout_contant_area_head">
            <h3>
              <NavLink to={"/shopping"}>
                <i className="fa fa-angle-left"></i>
              </NavLink>{" "}
              {t("package")}
            </h3>
            <button
              className="checkout_contant_clear_btn"
              onClick={handleClearAll}
            >
              {t("clear_all")}
            </button>
          </div>
        )}
        {activeStep === 2 && (
          <div className="checkout_contant_area_head">
            <h3>
              <button onClick={handleBack}>
                <i className="fa fa-angle-left"></i>
              </button>{" "}
             {t('contact_information')}
            </h3>
            <button
              type="button"
              className="checkout_contant_clear_btn"
              onClick={() => setShow(true)}
            >
             {t('add_new')}
            </button>
          </div>
        )}
        {activeStep === 3 && (
          <div className="checkout_contant_area_head">
            <h3>
              <button onClick={handleBack}>
                <i className="fa fa-angle-left"></i>
              </button>{" "}
              {t('order_summary')}
            </h3>
          </div>
        )}
        {activeStep === 4 && (
          <div className="checkout_contant_area_head">
            <h3>
              <button onClick={handleBack}>
                <i className="fa fa-angle-left"></i>
              </button>{" "}
              {t('payment_order')}
            </h3>
          </div>
        )}
        <div className="checkout_contant_cart_sec">
          <div className="main-content-regsiter">
            <div className="wizard-form py-4 my-2">
              <ul id="progressBar" className="progressbar px-lg-5 px-0">
                {steps.map((step, index) => (
                  <li
                    key={`step-${index + 1}`}
                    id={`progressList-${index + 1}`}
                    className={`d-inline-block w-25 position-relative text-center float-start progressbar-list ${
                      index <= activeStep - 1 ? "active" : ""
                    }`}
                  >
                    {step}
                  </li>
                ))}
              </ul>
            </div>
          </div>
          <div id="animation">
            {activeStep === 1 && (
              <CheckoutPackage
                items={items?.data}
                totalAmount={totalAmount}
                handleNext={handleNext}
              />
            )}
            {activeStep === 2 && (
              <CheckoutAddress
                totalAmount={totalAmount}
                handleNext={handleNext}
                show={show}
                setShow={setShow}
                address={address?.data}
                register={register}
                formValues={formValues}
                setValue={setValue}
                errors={errors}
              />
            )}
            {activeStep === 3 && (
              <CheckoutOrderSummary
                handleNext={handleNext}
                totalAmount={totalAmount}
                items={items?.data}
              />
            )}
            {activeStep === 4 && (
              <CheckoutPayment
                register={register}
                formValues={formValues}
                setValue={setValue}
                errors={errors}
                totalAmount={totalAmount}
                payments={paymentMethods?.data}
              />
            )}
          </div>
        </div>
      </div>
    </>
  );
};

export default CheckoutLayout;
