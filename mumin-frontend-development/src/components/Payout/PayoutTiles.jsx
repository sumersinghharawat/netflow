import React from "react";
import { useTranslation } from "react-i18next";
import CurrencyConverter from "../../Currency/CurrencyConverter";
import { useSelector } from "react-redux";

const PayoutTiles = ({ percentage, tiles }) => {
  const { t } = useTranslation();
  const userSelectedCurrency = useSelector(
    (state) => state?.user?.selectedCurrency
  );
  const conversionFactor = useSelector(
    (state) => state?.user?.conversionFactor
  );

  return (
    <div className="ewallet_top_cnt_sction">
      <div className="row">
        <div className="col-md-3" key={"1"}>
          <div className="ewallet_top_qc_balance_box">
            <div className="row align-items-center">
              <div className="ewallet_top_qc_balance_box_cnt">
                <div className="ewallet_top_qc_balance_box_cnt_ico">
                  <img src={"/images/pending-ico.svg"} alt="" />
                </div>
                <div className="ewallet_top_qc_balance_box_cnt_cnt">
                  <div className="ewallet_top_qc_balance_box_cnt_head">
                    {t("requested")}
                  </div>
                  <div className="box_amnt_dv">
                    <div className="ewallet_top_qc_balance_box_cnt_val ewallet_top_vl">{`${
                      userSelectedCurrency.symbolLeft
                    } ${CurrencyConverter(
                      tiles?.payoutRequested,
                      conversionFactor
                    )}`}</div>
                    <div className="ewallt_top_comparison">
                      {t("lastMonth")}
                      <span
                        className={`${
                          percentage?.payoutRequestedSign === "up"
                            ? "cmprson_up"
                            : "cmprson_down"
                        }`}
                      >
                        {`${
                          percentage?.payoutRequestedSign === "up" ? "+" : "-"
                        }${percentage?.payoutRequestedPercentage ?? "0"}% `}
                        <strong>
                          <i
                            className={`${
                              percentage?.payoutRequestedSign === "up"
                                ? "fa fa-arrow-up"
                                : "fa fa-arrow-down"
                            }`}
                          ></i>
                        </strong>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div className="col-md-3" key={"2"}>
          <div className="ewallet_top_qc_balance_box">
            <div className="row align-items-center">
              <div className="ewallet_top_qc_balance_box_cnt">
                <div className="ewallet_top_qc_balance_box_cnt_ico">
                  <img src={"/images/approved-ico.svg"} alt="" />
                </div>
                <div className="ewallet_top_qc_balance_box_cnt_cnt">
                  <div className="ewallet_top_qc_balance_box_cnt_head">
                    {t("approved")}
                  </div>
                  <div className="box_amnt_dv">
                    <div className="ewallet_top_qc_balance_box_cnt_val ewallet_top_vl">{`${
                      userSelectedCurrency.symbolLeft
                    } ${CurrencyConverter(
                      tiles?.payoutApproved,
                      conversionFactor
                    )}`}</div>

                    <div className="ewallt_top_comparison">
                      {t("lastMonth")}
                      <span
                        className={`${
                          percentage?.payoutApprovedSign === "up"
                            ? "cmprson_up"
                            : "cmprson_down"
                        }`}
                      >
                        {`${
                          percentage?.payoutApprovedSign === "up" ? "+" : "-"
                        }${percentage?.payoutApprovedPercentage ?? "0"}% `}
                        <strong>
                          <i
                            className={`${
                              percentage?.payoutApprovedSign === "up"
                                ? "fa fa-arrow-up"
                                : "fa fa-arrow-down"
                            }`}
                          ></i>
                        </strong>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div className="col-md-3" key={"3"}>
          <div className="ewallet_top_qc_balance_box">
            <div className="row align-items-center">
              <div className="ewallet_top_qc_balance_box_cnt">
                <div className="ewallet_top_qc_balance_box_cnt_ico">
                  <img src={"/images/paid-ico.svg"} alt="" />
                </div>
                <div className="ewallet_top_qc_balance_box_cnt_cnt">
                  <div className="ewallet_top_qc_balance_box_cnt_head">
                    {t("paid")}
                  </div>
                  <div className="box_amnt_dv">
                    <div className="ewallet_top_qc_balance_box_cnt_val ewallet_top_vl">{`${
                      userSelectedCurrency.symbolLeft
                    } ${CurrencyConverter(
                      tiles?.payoutPaid,
                      conversionFactor
                    )}`}</div>
                    <div className="ewallt_top_comparison">
                      {t("lastMonth")}
                      <span
                        className={`${
                          percentage?.payoutPaidSign === "up"
                            ? "cmprson_up"
                            : "cmprson_down"
                        }`}
                      >
                        {`${percentage?.payoutPaidSign === "up" ? "+" : "-"}${
                          percentage?.payoutPaidPercentage ?? "0"
                        }% `}
                        <strong>
                          <i
                            className={`${
                              percentage?.payoutPaidSign === "up"
                                ? "fa fa-arrow-up"
                                : "fa fa-arrow-down"
                            }`}
                          ></i>
                        </strong>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div className="col-md-3" key={"4"}>
          <div className="ewallet_top_qc_balance_box">
            <div className="row align-items-center">
              <div className="ewallet_top_qc_balance_box_cnt">
                <div className="ewallet_top_qc_balance_box_cnt_ico">
                  <img src={"/images/rejected-ico.svg"} alt="" />
                </div>
                <div className="ewallet_top_qc_balance_box_cnt_cnt">
                  <div className="ewallet_top_qc_balance_box_cnt_head">
                    {t("rejected")}
                  </div>
                  <div className="box_amnt_dv">
                    <div className="ewallet_top_qc_balance_box_cnt_val ewallet_top_vl">{`${
                      userSelectedCurrency.symbolLeft
                    } ${CurrencyConverter(
                      tiles?.payoutRejected,
                      conversionFactor
                    )}`}</div>
                    <div className="ewallt_top_comparison">
                      {t("lastMonth")}
                      <span
                        className={`${
                          percentage?.payoutRejectedSign === "up"
                            ? "cmprson_up"
                            : "cmprson_down"
                        }`}
                      >
                        {`${
                          percentage?.payoutRejectedSign === "up" ? "+" : "-"
                        }${percentage?.payoutRejectedPercentage ?? "0"}% `}
                        <strong>
                          <i
                            className={`${
                              percentage?.payoutRejectedSign === "up"
                                ? "fa fa-arrow-up"
                                : "fa fa-arrow-down"
                            }`}
                          ></i>
                        </strong>
                      </span>
                    </div>
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

export default PayoutTiles;
