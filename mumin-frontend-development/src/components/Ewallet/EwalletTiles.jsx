import React from "react";
import EwalletChart from "./EwalletDoughnut";
import { useTranslation } from "react-i18next";
import { useSelector } from "react-redux";
import CurrencyConverter from "../../Currency/CurrencyConverter";
const EwalletTiles = ({ tiles, currency, conversionFactor }) => {
  const { t } = useTranslation();
  const moduleStatus = useSelector(
    (state) => state.dashboard?.appLayout?.moduleStatus
  );

  return (
    <div className="ewallet_top_cnt_sction">
      <div className="row">
        <div className="col-xl-3 col-md-12">
          <div className="ewallet_top_qc_balance_box">
            <div className="row align-items-center">
              <div className="ewallet_top_qc_balance_box_cnt">
                <div className="ewallet_top_qc_balance_box_cnt_ico">
                  <img src={"/images/ewallet-ico2.png"} alt="" />
                </div>
                <div className="ewallet_top_qc_balance_box_cnt_cnt">
                  <div className="ewallet_top_qc_balance_box_cnt_head">
                    {t("ewalletBalance")}
                  </div>
                  <div className="box_amnt_dv">
                    <div className="ewallet_top_qc_balance_box_cnt_val ewallet_top_vl">
                      {currency?.symbolLeft}{" "}
                      {CurrencyConverter(
                        tiles?.ewalletBalance,
                        conversionFactor
                      )}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div className="col-xl-3 col-md-12">
          <div className="ewallet_top_qc_balance_box">
            <div className="row align-items-center">
              <div className="ewallet_top_qc_balance_box_cnt">
                <div className="ewallet_top_qc_balance_box_cnt_ico">
                  <img src={"/images/approved-ico.svg"} alt="" />
                </div>
                <div className="ewallet_top_qc_balance_box_cnt_cnt">
                  <div className="ewallet_top_qc_balance_box_cnt_head">
                    {t("creditedAmount")}
                  </div>
                  <div className="box_amnt_dv">
                    <div className="ewallet_top_qc_balance_box_cnt_val ewallet_top_vl">
                      {currency?.symbolLeft}{" "}
                      {CurrencyConverter(
                        tiles?.creditedAmount,
                        conversionFactor
                      )}
                    </div>
                    <div className="ewallt_top_comparison">
                      {t("lastMonth")}
                      <span
                        className={
                          tiles?.creditSign === "up"
                            ? "cmprson_up"
                            : "cmprson_down"
                        }
                      >
                        {`${tiles?.creditSign === "up" ? "+" : "-"}${
                          tiles?.creditPercentage ?? "0"
                        }% `}
                        <strong>
                          <i
                            className={
                              tiles?.creditSign === "up"
                                ? "fa fa-arrow-up"
                                : "fa fa-arrow-down"
                            }
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
        <div className="col-xl-3 col-md-12">
          <div className="ewallet_top_qc_balance_box">
            <div className="row align-items-center">
              <div className="ewallet_top_qc_balance_box_cnt">
                <div className="ewallet_top_qc_balance_box_cnt_ico">
                  <img src={"/images/paid-ico.svg"} alt="" />
                </div>
                <div className="ewallet_top_qc_balance_box_cnt_cnt">
                  <div className="ewallet_top_qc_balance_box_cnt_head">
                    {t("debitedAmount")}
                  </div>
                  <div className="box_amnt_dv">
                    <div className="ewallet_top_qc_balance_box_cnt_val ewallet_top_vl">
                      {currency?.symbolLeft}{" "}
                      {CurrencyConverter(
                        tiles?.debitedAmount,
                        conversionFactor
                      )}
                    </div>
                    <div className="ewallt_top_comparison">
                      {t("lastMonth")}
                      <span
                        className={
                          tiles?.debitSign === "up"
                            ? "cmprson_up"
                            : "cmprson_down"
                        }
                      >
                        {`${tiles?.debitSign === "up" ? "+" : "-"}${
                          tiles?.debitPercentage ?? "0"
                        }% `}
                        <strong>
                          <i
                            className={
                              tiles?.debitSign === "up"
                                ? "fa fa-arrow-up"
                                : "fa fa-arrow-down"
                            }
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
        {!!moduleStatus?.purchase_wallet && (
          <div className="col-xl-3 col-md-12">
            <div className="ewallet_top_qc_balance_box">
              <div className="row align-items-center">
                <div className="ewallet_top_qc_balance_box_cnt">
                  <div className="ewallet_top_qc_balance_box_cnt_ico">
                    <img src={"/images/ewallet-ico2.png"} alt="" />
                  </div>
                  <div className="ewallet_top_qc_balance_box_cnt_cnt">
                    <div className="ewallet_top_qc_balance_box_cnt_head">
                      {t("purchaseWallet")}
                    </div>
                    <div className="box_amnt_dv">
                      <div className="ewallet_top_qc_balance_box_cnt_val ewallet_top_vl">
                        {currency?.symbolLeft}{" "}
                        {CurrencyConverter(
                          tiles?.purchaseWallet,
                          conversionFactor
                        )}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        )}
        {!!!moduleStatus?.purchase_wallet && (
          <EwalletChart
            spend={tiles?.spent}
            balance={tiles?.balance}
            spentRatio={tiles?.spentRatio}
            balanceRatio={tiles?.balanceRatio}
            currency={currency}
            conversionFactor={conversionFactor}
          />
        )}
      </div>
    </div>
  );
};

export default EwalletTiles;
