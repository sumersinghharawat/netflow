import React, { Fragment, useState } from "react";
import CurrencyConverter from "../../Currency/CurrencyConverter";
import { useTranslation } from "react-i18next";
import { useSelector } from "react-redux";
import { MultiSelect } from "react-multi-select-component";
import { reverseNumberDisplay } from "../../utils/currencyNumberDisplay";
import { toast } from "react-toastify";
import { getEpins } from "../../utils/getEpinList";
import { ApiHook } from "../../hooks/apiHook";
import { BASE_URL } from "../../config/config";
import { useNavigate } from "react-router";
import MyPayPalOrderButton from "../payment/PaypalOrderButton";

const CheckoutPayment = ({
  register,
  formValues,
  setValue,
  errors,
  totalAmount,
  payments,
}) => {
  const { t } = useTranslation();
  const navigate = useNavigate();
  const [activeTab, setActiveTab] = useState("");
  const [epinValues, setEpinValues] = useState([]);
  const [getEwallet, setGetEwallet] = useState(false);
  const [transPass, setTransPass] = useState("");
  const [transPassCheck, setTransPassCheck] = useState(false);
  const [submitButtonActive, setSubmitButtonActive] = useState(true);
  const [transPassResposne, setTransPassResposne] = useState({
    success: null,
    error: null,
  });
  const [fileResponse, setFileResponse] = useState({
    success: null,
    error: null,
  });
  const [file, setFile] = useState(null);
  const userSelectedCurrency = useSelector(
    (state) => state.user?.selectedCurrency
  );
  const conversionFactor = useSelector(
    (state) => state?.user?.conversionFactor
  );
  const sponsorName = useSelector(
    (state) => state.dashboard?.appLayout?.user?.username
  );
  const handlePaymentTabClick = (tabId) => {
    setActiveTab(tabId);
    if (tabId === 1) {
      setValue(
        "totalAmt",
        `${CurrencyConverter(totalAmount, conversionFactor)}`
      );
      setValue("totalAmount", totalAmount);
    } else if (tabId === 3) {
      setSubmitButtonActive(false);
    } else if (tabId === 2) {
      setGetEwallet(true);
      setSubmitButtonActive(true);
      setValue("transactionPassword", transPass?.transPassword);
    } else {
      setSubmitButtonActive(true);
    }
    setValue("paymentType", tabId, { shouldValidate: true }); // Set the selected payment
  };
  // ------------------------------------ API -------------------------------------------
  const userBalance = ApiHook.CallEwalletBalance(getEwallet, setGetEwallet);
  const transPassCheckData = ApiHook.CallTransPasswordCheck(
    transPass,
    transPassCheck,
    setTransPassCheck,
    setSubmitButtonActive,
    totalAmount,
    transPassResposne,
    setTransPassResposne
  );
  const Upload = ApiHook.CallBankUpload(
    "repurchase",
    sponsorName,
    setSubmitButtonActive,
    setValue,
    setFileResponse
  );
  const epinList = getEpins(
    payments?.epins,
    conversionFactor,
    userSelectedCurrency
  );
  const repurchaseMutation = ApiHook.CallPlaceRepurchaseOrder();
  const deleteBankReciept = ApiHook.CallDeleteBankReceipt(
    setSubmitButtonActive,
    setValue,
    setFileResponse,
    setFile
  )

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

  const handleFileChange = (event) => {
    setFileResponse({
      success: null,
      error: null,
    });
    const selectedFile = event.target.files[0];
    setFile(selectedFile);
  };

  const handleUpload = () => {
    const type = "register";
    if (file) {
      Upload.mutate(file, type);
    }
  };

  const handleDeleteBankReciept = () => {
    const data = {
      filepath :formValues?.bankReceipt,
      type:"repurchase"
    }
    if (formValues?.bankReceipt) {
      deleteBankReciept.mutateAsync(data);
    }
  }

  const handleTransPassword = async (item) => {
    const { value } = item;
    setTransPass(value);
    setTransPassResposne({
      success: null,
      error: null,
    });
  };

  const handleSubmit = (paymentId) => {
    if (paymentId === 6) {
      formValues.paymentType = paymentId;
    }
    setSubmitButtonActive(true);
    repurchaseMutation.mutate(formValues, {
      onSuccess: (res) => {
        if (res.status) {
          toast.success(res?.data);
          navigate("/shopping");
        } else {
          toast.error(res?.data?.description);
        }
      },
    });
  };
  return (
    <div className="col-md-12 m-auto">
      <div className="payment_section_tab">
        <div className="regsiter_step_1_view_left_sec_head">
          {t("payment_type")}
          <br />
          <strong>
            {t("totalAmount")}: {userSelectedCurrency.symbolLeft}{" "}
            {CurrencyConverter(totalAmount, conversionFactor)}
          </strong>
        </div>

        <div className="row">
          <div className="col-md-3 pe-0">
            <div
              className="nav nav-tabs payment_tab_reg"
              id="nav-tab"
              role="tablist"
            >
              {payments?.methods?.map((tab) => (
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
                    aria-selected={formValues.paymentType === tab.id}
                    {...register("paymentType", { required: true })}
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
              {formValues.paymentType === undefined && (
                <p>
                  <strong>{t("preferredPaymentPlaceholder")}</strong>
                </p>
              )}
              {payments?.methods?.map((tab) => (
                <div
                  key={tab.id}
                  className={`tab-pane fade ${
                    activeTab === tab.id ? "show active" : ""
                  }`}
                  id={`nav-${tab.id}`}
                  role="tabpanel"
                  aria-labelledby={`nav-${tab.id}-tab`}
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
                            closeOnChangedValue={formValues.epinBalance <= 0}
                          />
                        </div>
                      </div>
                      <div className="elawwet_blance_sec">
                        {epinValues.map((epinItem, index) => (
                          <div className="elawwet_blance_row" key={index}>
                            <span>{epinItem.value}</span>
                            <strong>
                              {t("amount")}: {userSelectedCurrency?.symbolLeft}
                              {CurrencyConverter(
                                epinItem.amount,
                                conversionFactor
                              )}
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
                                  {userSelectedCurrency?.symbolLeft}
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
                                  {userSelectedCurrency?.symbolLeft}
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
                                  {userSelectedCurrency?.symbolLeft}
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
                            userSelectedCurrency.symbolLeft
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
                          <label htmlFor="fileUpload">{t("file_upload")}</label>
                          <input
                            id="fileUpload"
                            type="file"
                            placeholder=""
                            className="form-control"
                            name="fileUpload"
                            onChange={handleFileChange}
                          />
                          {fileResponse?.success &&
                            formValues?.bankReceipt !== undefined && (
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
                          disabled={
                            Upload.status === "loading" ||
                            formValues?.bankReceipt ||
                            document.getElementById("fileUpload")?.value ===
                              "" ||
                            file === null
                          }
                        >
                          {Upload.status === "loading"
                            ? "Uploading..."
                            : t("upload")}
                        </button>
                      </div>
                      {Upload?.data?.status === true &&
                        formValues?.bankReceipt !== undefined && (
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
                            <button
                              className="recipt_checkout_address_btn"
                              onClick={handleDeleteBankReciept}
                            >
                              <i className="fa fa-xmark"></i>
                            </button>
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
                    <MyPayPalOrderButton
                      currency={userSelectedCurrency?.code}
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
        {errors.paymentType && (
          <span className="error-message-validator">*Required</span>
        )}
      </div>
      <div className="checkout_continuew_btn">
        <button
          className="btn btn-primary checkout_cnt_btn"
          disabled={submitButtonActive}
          onClick={handleSubmit}
        >
          {t("complete")}
        </button>
      </div>
    </div>
  );
};

export default CheckoutPayment;
