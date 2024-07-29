import React, { Fragment, useEffect, useRef, useState } from "react";
import { ApiHook } from "../../hooks/apiHook";
import { formatDateWithoutTime } from "../../utils/formateDate";
import { useForm } from "react-hook-form";
import { useSelector } from "react-redux";
import { useTranslation } from "react-i18next";
import { MultiSelect } from "react-multi-select-component";
import CurrencyConverter from "../../Currency/CurrencyConverter";
import { reverseNumberDisplay } from "../../utils/currencyNumberDisplay";
import { toast } from "react-toastify";
import { getEpins } from "../../utils/getEpinList";
import { BASE_URL } from "../../config/config";
import { useNavigate } from "react-router";
import Switch from "@mui/material/Switch";
import FormControlLabel from "@mui/material/FormControlLabel";
import { Modal, Button } from "react-bootstrap";
import MyPayPalOrderButton from "../payment/PaypalOrderButton";
import MyPayPalSubscriptionButton from "../payment/PaypalSubscriptionButton";

const RenewComponent = ({ data, userData, currency }) => {
  const { t } = useTranslation();
  const navigate = useNavigate();
  const { setValue, watch } = useForm();
  const formValues = watch();

  const [epinValues, setEpinValues] = useState([]);
  const [activeTab, setActiveTab] = useState("");
  const [submitButtonActive, setSubmitButtonActive] = useState(true);
  const [getEwallet, setGetEwallet] = useState(false);
  const [transPass, setTransPass] = useState("");
  const [transPassCheck, setTransPassCheck] = useState(false);
  const progressBarRef = useRef(null);
  const [showWarning, setShowWarning] = useState(false);
  const [transPassResposne, setTransPassResposne] = useState({
    success: null,
    error: null,
  });
  const [fileResponse, setFileResponse] = useState({
    success: null,
    error: null,
  });
  const [file, setFile] = useState(null);
  const [autoRenew, setAutoRenew] = useState(
    data?.autoRenewalStatus ? true : false
  );
  const user = useSelector((state) => state?.user?.loginResponse?.user);
  const username = user ? JSON.parse(user) : null;
  const conversionFactor = useSelector(
    (state) => state?.user?.conversionFactor
  );
  // ----------------------------------- API --------------------------------------
  const paymentMethods = ApiHook.CallPaymentMethods("membership_renewal");
  const userBalance = ApiHook.CallEwalletBalance(getEwallet, setGetEwallet);
  const renewsubscriptionMutation = ApiHook.CallRenewSubscription();
  const cancelSubscriptionMutation = ApiHook.CallCancelSubscription();
  const transPassCheckData = ApiHook.CallTransPasswordCheck(
    transPass,
    transPassCheck,
    setTransPassCheck,
    setSubmitButtonActive,
    data?.renewalPrice,
    transPassResposne,
    setTransPassResposne
  );
  // API FOR BANK TRANSFER UPDATE
  const Upload = ApiHook.CallBankUpload(
    "renewal",
    username?.username,
    setSubmitButtonActive,
    setValue,
    setFileResponse
  );

  const epinList = getEpins(
    paymentMethods?.data?.epins,
    conversionFactor,
    currency
  );

  useEffect(() => {
    const strokeDashOffsetValue =
      100 - (data?.productValidity?.packageValidityPercentage ?? 100);
    progressBarRef.current.style.strokeDashoffset = strokeDashOffsetValue;
  }, [data?.productValidity?.packageValidityPercentage]);

  const handleSubscriptionToggleBtn = (event) => {
    if (event.target.checked) {
      setAutoRenew(true);
      if (activeTab === 3) {
        setSubmitButtonActive(true);
      }
    } else {
      setAutoRenew(false);
      if (activeTab === 3) {
        setSubmitButtonActive(false);
      }
      if (data?.subscriptionId) {
        setShowWarning(true);
      }
    }
  };

  const handleCancellation = () => {
    if (data?.subscriptionId) {
      cancelSubscriptionMutation.mutate(
        { id: data?.subscriptionId },
        {
          onSuccess: (res) => {
            if (res.status) {
              toast.success(t(res.data));
              setShowWarning(false);
            } else {
              toast.error(res.message);
            }
          },
        }
      );
    } else {
      setShowWarning(false);
    }
  };

  const handlePaymentTabClick = (tabId) => {
    setValue("packageId", data?.packageId);
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
    setValue("paymentMethod", tabId);
    setValue("totalAmount", data?.renewalPrice);
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
    const type = "renewal";
    if (file) {
      Upload.mutate(file, type);
    }
  };

  const handleBack = () => {
    navigate("/dashboard");
  };

  const handleSubmit = (paymentId) => {
    if (paymentId === 6) {
      formValues.packageId = data?.packageId;
      formValues.totalAmount = data?.renewalPrice;
      formValues.paymentMethod = paymentId;
    }
    renewsubscriptionMutation.mutate(formValues, {
      onSuccess: (res) => {
        if (res.status) {
          toast.success(t(res?.data));
          navigate("/profile");
        } else {
          toast.error(t(res?.description));
        }
      },
    });
  };

  return (
    <>
      <div className="package_upgrade_cnt_sec">
        <div className="renew-plan">
          <div className="row justify-content-center">
            <div className="renewcenterBox">
              <div className="profileBgBox">
                <div className="row align-items-center">
                  <div className="col-lg-4 col-md-12 borderPofileStyle">
                    <div className="rightSide_top_user_dropdown">
                      <div className="rightSide_top_user_dropdown_avatar_sec">
                        <div className="rightSide_top_user_dropdown_avatar_extra_padding avatarProfileStyle">
                          <img
                            src={`${
                              data?.image ?? "/images/profile-image-new.png"
                            }`}
                            alt=""
                          />
                          <svg
                            className="profile_progress avatarProfileProgress"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="-1 -1 34 34"
                          >
                            <circle
                              cx="16"
                              cy="16"
                              r="15.9155"
                              className="progress-bar__background"
                            />
                            <circle
                              ref={progressBarRef}
                              cx="16"
                              cy="16"
                              r="15.9155"
                              className="progress-bar__progress js-progress-bar"
                            />
                          </svg>
                        </div>
                      </div>
                      <div className="profileAvatarnameSec">
                        <h4>{userData?.fullName}</h4>
                        <span className="renewn_remainingdys">
                          {data?.daysLeft} {t("daysLeft")}
                        </span>
                        <div className="kycDetailProfile">
                          <div className="kycDetailProfile_cnt">
                            <h6>{t("purchasedOn")} :</h6>
                            <h6>{formatDateWithoutTime(data?.purchaseDate)}</h6>
                          </div>
                          <div className="kycDetailProfile_cnt">
                            <h6>{t("price")} :</h6>
                            <h6>{`${currency?.symbolLeft} ${CurrencyConverter(
                              data?.renewalPrice,
                              conversionFactor
                            )}`}</h6>
                          </div>
                          <div className="kycDetailProfile_cnt">
                            <h6>{t("pv")} :</h6>
                            <h6>{data?.pairValue}</h6>
                          </div>
                          <div className="kycDetailProfile_cnt">
                            <h6 className="packge_name_bx">
                              {data?.packageName}
                            </h6>
                          </div>
                          <FormControlLabel
                            control={
                              <Switch
                                defaultValue={true}
                                onChange={handleSubscriptionToggleBtn}
                                color="secondary"
                              />
                            }
                            label={t("auto_renewal")}
                            labelPlacement="start"
                          />
                        </div>
                      </div>
                    </div>
                  </div>
                  <div className="col-lg-8 col-md-12 border-prf-left">
                    <div className="row">
                      <div className="col-md-4 pe-0">
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
                                  autoRenew
                                    ? tab.gateway
                                      ? ""
                                      : "buttonDisabled"
                                    : ""
                                } ${activeTab === tab.id ? "active" : ""}`}
                                id={`nav-${tab.id}-tab`}
                                data-bs-toggle="tab"
                                data-bs-target={`#nav-${tab.id}`}
                                type="button"
                                role="tab"
                                aria-controls={`nav-${tab.id}`}
                                onClick={() =>
                                  handlePaymentTabClick(tab.id, tab.title)
                                }
                              >
                                <i className={tab?.icon}></i> {tab.title}
                              </button>
                            </Fragment>
                          ))}
                        </div>
                      </div>
                      <div className="col-md-8 ps-0">
                        <div
                          className="tab-content p-3 border mb-5 payment__tab_cnt"
                          id="nav-tabContent"
                        >
                          {activeTab === "" && (
                            <p>
                              <strong>
                                {t("preferredPaymentPlaceholder")}
                              </strong>
                            </p>
                          )}
                          {paymentMethods?.data?.methods?.map((tab) => (
                            <div
                              key={tab.id}
                              className={`tab-pane fade ${
                                activeTab === tab.id ? "show active" : ""
                              } ${
                                autoRenew
                                  ? tab.gateway
                                    ? ""
                                    : "buttonDisabled"
                                  : ""
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
                                      <div
                                        className="elawwet_blance_row"
                                        key={index}
                                      >
                                        <span>{epinItem.value}</span>
                                        <strong>
                                          {t("amount")}: {currency?.symbolLeft}
                                          {CurrencyConverter(
                                            epinItem.amount,
                                            conversionFactor
                                          )}
                                        </strong>
                                        <a
                                          href="#/"
                                          className="epin_ball_clear"
                                          onClick={() =>
                                            removeItemByIndex(index)
                                          } // Call the remove function with the index
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
                                              {formValues?.epinBalance !==
                                              undefined
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
                                        onChange={(e) =>
                                          handleTransPassword(e.target)
                                        }
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
                                  <span className="error-message-validator">
                                    {transPassCheckData.data?.message}
                                  </span>
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
                                      {Upload?.isLoading
                                        ? "Uploading..."
                                        : t("upload")}
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
                                  <strong>Addon Coming Soon</strong>
                                </p>
                              )}
                              {tab.title === "paypal" && (
                                <>
                                  {autoRenew ? (
                                    <MyPayPalSubscriptionButton
                                      currency={currency?.code}
                                      data={paymentMethods.data}
                                    />
                                  ) : (
                                    <MyPayPalOrderButton
                                      currency={currency?.code}
                                      price={data?.renewalPrice}
                                      handleSubmit={handleSubmit}
                                      paymentId={tab.id}
                                    />
                                  )}
                                </>
                              )}
                            </div>
                          ))}
                        </div>
                      </div>
                    </div>
                    <div className="renewalBackBtn">
                      <button
                        className="plan-choose-back renew_btn mt-2"
                        onClick={handleBack}
                      >
                        {t("back")}
                      </button>
                      <button
                        type="submit"
                        className="plan-choose renew_btn mt-2"
                        disabled={submitButtonActive}
                        onClick={handleSubmit}
                      >
                        {t("finish")}
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <Modal show={showWarning} onHide={() => setShowWarning(false)}>
        <Modal.Header>
          <Modal.Title>{t("auto_renewal")}</Modal.Title>
        </Modal.Header>
        <Modal.Body>
          <p>{t("are_you_sure_to_cancel_subscription")}</p>
        </Modal.Body>
        <Modal.Footer>
          <Button variant="primary" onClick={handleCancellation}>
            {t("yes")}
          </Button>
          <Button variant="secondary" onClick={() => setShowWarning(false)}>
            {t("no")}
          </Button>
        </Modal.Footer>
      </Modal>
    </>
  );
};

export default RenewComponent;
