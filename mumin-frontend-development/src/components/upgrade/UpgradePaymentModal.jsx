import React, { Fragment, useEffect, useState } from "react";
import { Modal } from "react-bootstrap";
import { ApiHook } from "../../hooks/apiHook";
import { useForm } from "react-hook-form";
import { useTranslation } from "react-i18next";
import { MultiSelect } from "react-multi-select-component";
import { getEpins } from "../../utils/getEpinList";
import { useSelector } from "react-redux";
import CurrencyConverter from "../../Currency/CurrencyConverter";
import { reverseNumberDisplay } from "../../utils/currencyNumberDisplay";
import { toast } from "react-toastify";
import { BASE_URL } from "../../config/config";
import MyPayPalOrderButton from "../payment/PaypalOrderButton";

const UpgradePaymentModal = ({
  show,
  setShow,
  currentProduct,
  data,
  currency,
  conversionFactor,
}) => {
  //   useStates & declaration
  const { t } = useTranslation();
  const { setValue, watch, reset } = useForm();
  const formValues = watch();
  const user = useSelector((state) => state?.user?.loginResponse?.user);
  const username = user ? JSON.parse(user) : null;
  const [epinValues, setEpinValues] = useState([]);
  const [activeTab, setActiveTab] = useState("");
  const [submitButtonActive, setSubmitButtonActive] = useState(true);
  const [getEwallet, setGetEwallet] = useState(false);
  const [transPass, setTransPass] = useState("");
  const [transPassCheck, setTransPassCheck] = useState(false);
  const [file, setFile] = useState(null);
  const [transPassResposne, setTransPassResposne] = useState({
    success: null,
    error: null,
  });
  const [fileResponse, setFileResponse] = useState({
    success: null,
    error: null,
  });
  //------------------------------------- API --------------------------------------------
  const paymentMethods = ApiHook.CallPaymentMethods("membership_renewal");
  const userBalance = ApiHook.CallEwalletBalance(getEwallet, setGetEwallet);
  const upgradeSubscriptionMutation = ApiHook.CallUpgradeSubscription();
  ApiHook.CallTransPasswordCheck(
    transPass,
    transPassCheck,
    setTransPassCheck,
    setSubmitButtonActive,
    formValues?.totalAmount,
    transPassResposne,
    setTransPassResposne
  );
  const Upload = ApiHook.CallBankUpload(
    "upgrade",
    username?.username,
    setSubmitButtonActive,
    setValue,
    setFileResponse
  );

  //   functions
  const epinList = getEpins(
    paymentMethods?.data?.epins,
    conversionFactor,
    currency
  );
  const handleClose = () => {
    setShow(false);
    reset();
    setTransPassResposne({
      success: null,
      error: null,
    });
    setEpinValues([]);
  };
  useEffect(() => {
    if (data) {
      setValue("totalAmount", data.price - currentProduct?.pack?.price);
      const newTotalAmount = CurrencyConverter(
        data.price - currentProduct?.pack?.price,
        conversionFactor
      );
      setValue("totalAmt", newTotalAmount);
    }
  }, [show]);

  const handlePaymentTabClick = (tabId) => {
    setValue("oldProductId", currentProduct?.pack?.id);
    setValue("upgradeProductId", data?.id);
    setActiveTab(tabId);
    if (tabId === 3) {
      setSubmitButtonActive(false);
    } else if (tabId === 2) {
      setGetEwallet(true);
      setSubmitButtonActive(true);
      setValue("transactionPassword", transPass?.transPassword);
    } else {
      setSubmitButtonActive(true);
    }
    setValue("paymentMethod", tabId); // Set the selected payment
  };

  const handleEpinChange = (epinValues) => {
    let newValues = [];
    let totalEpinAmount = 0;
    setEpinValues(epinValues);
    Object.entries(epinValues)?.map(([key, value]) => {
      totalEpinAmount =
        totalEpinAmount + reverseNumberDisplay(String(value.amount));
      newValues.push(value.value);
    });
    const balance =
      Number(reverseNumberDisplay(String(formValues?.totalAmount))) -
      Number(totalEpinAmount);
    if (balance <= 0) {
      setValue("epinBalance", 0);
      setSubmitButtonActive(false);
      toast.success("Total amount achieved");
    } else {
      setSubmitButtonActive(true);
      setValue("epinBalance", reverseNumberDisplay(String(balance)));
    }
    setValue("epins", newValues);
    setValue("totalEpinAmount", reverseNumberDisplay(String(totalEpinAmount)));
  };

  const removeItemByIndex = (index) => {
    let newBalance = 0;
    const remainingValues = [];
    const updatedEpinValues = [...epinValues];
    const removedItem = updatedEpinValues.splice(index, 1)[0]; // Remove and get the removed item
    setEpinValues(updatedEpinValues);
    // update epinValues
    updatedEpinValues.forEach((item) => {
      remainingValues.push(item.value);
    });
    // Recalculate totalEpinAmount and balance
    const newTotalEpinAmount =
      Number(reverseNumberDisplay(String(formValues.totalEpinAmount))) -
      Number(reverseNumberDisplay(String(removedItem.amount.toFixed(2))));
    if (newTotalEpinAmount < formValues.totalAmount) {
      newBalance =
        Number(reverseNumberDisplay(String(formValues?.totalAmount))) -
        newTotalEpinAmount;
    }
    // Update the state values
    setValue(
      "totalEpinAmount",
      reverseNumberDisplay(String(newTotalEpinAmount))
    );
    setValue("epinBalance", reverseNumberDisplay(String(newBalance)) ?? 0);
    setValue("epins", remainingValues);
    if (newBalance <= 0) {
      setSubmitButtonActive(false);
    } else {
      setSubmitButtonActive(true);
    }
  };

  const handleTransPassword = async (item) => {
    const { value } = item;
    setTransPass(value);
    setTransPassResposne({
      success: null,
      error: null,
    });
  };
  const handleFileChange = (event) => {
    setFileResponse({
      success: null,
      error: null,
    });
    const selectedFile = event.target.files[0];
    setFile(selectedFile);
  };

  const handleUpload = () => {
    const type = "upgrade";
    if (file) {
      Upload.mutate(file, type);
    }
  };

  const handleSubmit = async (paymentId) => {
    if (paymentId === 6) {
      formValues.oldProductId = currentProduct?.pack?.id;
      formValues.upgradeProductId = data?.id;
      formValues.paymentMethod = paymentId;
    }
    upgradeSubscriptionMutation.mutateAsync(formValues);
  };

  return (
    <>
      <Modal show={show} onHide={handleClose} size="lg">
        <Modal.Header>
          <Modal.Title>{t("payNow")}</Modal.Title>
          <button
            type="button"
            className="btn-close"
            onClick={handleClose}
            aria-label="Close"
          ></button>
        </Modal.Header>
        <Modal.Body>
          <div className="payment_section_tab">
            <div className="regsiter_step_1_view_left_sec_head">
              {t("totalAmount")} : {currency?.symbolLeft} {formValues?.totalAmt}
              <br />
            </div>
            <div className="row">
              <div className="col-md-3 pe-0">
                <div
                  className="nav nav-tabs payment_tab_reg"
                  id="nav-tab"
                  role="tablist"
                >
                  {paymentMethods?.data?.methods?.map((tab) => (
                    <Fragment key={tab.id}>
                      <button
                        key={tab.id}
                        className={`nav-link ${
                          activeTab === tab.id ? "active" : ""
                        }`}
                        id={`nav-${tab.id}-tab`}
                        data-bs-toggle="tab"
                        data-bs-target={`#nav-${tab.id}`}
                        type="button"
                        role="tab"
                        aria-controls={`nav-${tab.id}`}
                        onClick={() => handlePaymentTabClick(tab.id, tab.title)}
                      >
                        <i className={tab?.icon}></i> {tab.title}
                      </button>
                    </Fragment>
                  ))}
                </div>
              </div>
              <div className="col-md-9 ps-0">
                <div
                  className="tab-content p-3 border mb-5 payment__tab_cnt"
                  id="nav-tabContent"
                >
                  {activeTab === "" && (
                    <p>
                      <strong>{t("preferredPaymentPlaceholder")}</strong>
                    </p>
                  )}
                  {paymentMethods?.data?.methods?.map((tab) => (
                    <div
                      key={tab.id}
                      className={`tab-pane fade ${
                        activeTab === tab.id ? "show active" : ""
                      }`}
                      id={`nav-${tab.id}`}
                    >
                      {tab.title === "e-pin" && (
                        <div className="row">
                          <div className="col-md-6">
                            <div className="form-group">
                              <label>{t("epin")}</label>
                              <MultiSelect
                                disableSearch
                                options={epinList}
                                value={epinValues}
                                onChange={handleEpinChange}
                                labelledBy="Select"
                                hasSelectAll={false}
                                disabled={formValues.epinBalance <= 0}
                                closeOnChangedValue={
                                  formValues.epinBalance <= 0
                                }
                              />
                            </div>
                          </div>
                          <div className="elawwet_blance_sec">
                            {epinValues.map((epinItem, index) => (
                              <div className="elawwet_blance_row" key={index}>
                                <span>{epinItem.label.split(" ")[0]}</span>
                                <strong>
                                  {t("amount")}: {epinItem.label.split(" ")[1]}
                                </strong>
                                <a
                                  href="#/"
                                  className="epin_ball_clear"
                                  onClick={() => removeItemByIndex(index)} // Call the remove function with the index
                                >
                                  <i className="fa fa-close"></i>
                                </a>
                              </div>
                            ))}
                          </div>
                          <div className="valid_epi_pin_box">
                            {t("valid_epin_data")}
                          </div>
                          <div className="total_epin_detail">
                            <table>
                              <tbody>
                                <tr>
                                  <td>{t("total_epin_amount")}</td>
                                  <td className="text-end">
                                    <strong>
                                      {currency?.symbolLeft}
                                      {CurrencyConverter(
                                        formValues?.totalEpinAmount,
                                        conversionFactor
                                      ) ?? 0}
                                    </strong>
                                  </td>
                                </tr>
                                <tr>
                                  <td>{t("totalAmount")}</td>
                                  <td className="text-end">
                                    <strong>
                                      {currency?.symbolLeft}
                                      {CurrencyConverter(
                                        formValues?.totalAmount,
                                        conversionFactor
                                      )}
                                    </strong>
                                  </td>
                                </tr>
                                <tr>
                                  <td>{t("balance")}</td>
                                  <td className="text-end">
                                    <strong>
                                      {currency?.symbolLeft}
                                      {formValues?.epinBalance !== undefined
                                        ? CurrencyConverter(
                                            formValues?.epinBalance,
                                            conversionFactor
                                          )
                                        : CurrencyConverter(
                                            formValues?.totalAmount,
                                            conversionFactor
                                          )}
                                    </strong>
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                        </div>
                      )}
                      {tab.title === "e-wallet" && (
                        <div className="row">
                          <div className="col-md-12">
                            <div className="form-group mb-2">
                              <p>{`${t("ewalletBalance")} : ${
                                currency.symbolLeft
                              } ${CurrencyConverter(
                                userBalance?.data?.balanceAmount,
                                conversionFactor
                              )}`}</p>
                              <br />
                              <label htmlFor="transactionPassword">
                                {t("transaction_password")}
                              </label>
                              <input
                                id="transactionPassword"
                                type="password"
                                placeholder=""
                                className="form-control"
                                name="transPassword"
                                onChange={(e) => handleTransPassword(e.target)}
                              />
                              {transPassResposne?.success && (
                                <div style={{ color: "green" }}>
                                  {t(transPassResposne?.success)}
                                </div>
                              )}
                              {transPassResposne?.error && (
                                <div style={{ color: "red" }}>
                                  {t(transPassResposne?.error)}
                                </div>
                              )}
                            </div>
                          </div>
                          <div className="col-md-12">
                            <button
                              href="#"
                              className="btn btn-sm btn-primary mt-3"
                              onClick={() => setTransPassCheck(true)}
                              disabled={!transPass}
                            >
                              {t("apply")}
                            </button>
                          </div>
                        </div>
                      )}
                      {tab.title === "free-joining" && (
                        <p>
                          <strong>{t("freeJoinPlaceHolder")}</strong>
                        </p>
                      )}
                      {tab.title === "bank-transfer" && (
                        <div className="row">
                          <div className="col-md-12">
                            <div className="form-group mb-2">
                              <label htmlFor="fileUpload">
                                {t("file_upload")}
                              </label>
                              <input
                                id="fileUpload"
                                type="file"
                                placeholder=""
                                className="form-control"
                                name="fileUpload"
                                onChange={handleFileChange}
                              />
                              {fileResponse?.success && (
                                <div style={{ color: "green" }}>
                                  {t(fileResponse?.success)}
                                </div>
                              )}
                              {fileResponse?.error && (
                                <div style={{ color: "red" }}>
                                  {t(fileResponse?.error)}
                                </div>
                              )}
                            </div>
                          </div>
                          <div className="col-md-12">
                            <button
                              href="#"
                              className="btn btn-sm btn-primary mt-3"
                              onClick={handleUpload}
                              disabled={Upload?.isLoading}
                            >
                              {Upload?.isLoading ? "Uploading..." : t("upload")}
                            </button>
                          </div>
                          {Upload?.data?.status === true && (
                            <>
                              <img
                                src={`${BASE_URL}${Upload?.data?.data?.file?.path}`}
                                alt="receipt"
                                style={{
                                  width: "160px",
                                  height: "160px",
                                  padding: "5px",
                                }}
                              />
                              <div style={{ color: "green" }}>
                                {Upload?.data?.message}
                              </div>
                            </>
                          )}
                          {Upload?.data?.status === false && (
                            <span className="error-message-validator">
                              {Upload?.data?.message}
                            </span>
                          )}
                        </div>
                      )}
                      {tab.title === "stripe" && (
                        <p>
                          <strong>{t("addonComingSoon")}</strong>
                        </p>
                      )}
                      {tab.title === "paypal" && (
                        <MyPayPalOrderButton
                          currency={currency?.code}
                          price={formValues.totalAmount}
                          handleSubmit={handleSubmit}
                          paymentId={tab.id}
                        />
                      )}
                    </div>
                  ))}
                </div>
              </div>
            </div>
            <div className="checkout_continuew_btn">
              <button
                className="btn btn-primary checkout_cnt_btn"
                type="submit"
                disabled={submitButtonActive}
                onClick={handleSubmit}
              >
                {t("complete")}
              </button>
            </div>
          </div>
        </Modal.Body>
      </Modal>
    </>
  );
};

export default UpgradePaymentModal;
