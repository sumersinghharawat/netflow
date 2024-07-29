import React from "react";
import CurrencyConverter from "../../Currency/CurrencyConverter";
import { useTranslation } from "react-i18next";
const QuickBalance = ({ tiles, currency, conversionFactor }) => {
  const { t } = useTranslation();
  return (
    <div className="quick_balance_section">
      <div className="quick_balance_section_row">
        <div className="quick_balance_Box">
          <div className="quick_balance_Box_ico">
            <img src="/images/balance_ewallet_ico.svg" alt="" />
          </div>
          <div className="quick_balance_Box_cnt">
            <span>{t("eWallet")}</span>
            <strong>{`${
              currency?.symbolLeft ? currency?.symbolLeft : "$"
            } ${CurrencyConverter(
              tiles?.ewallet ?? 0,
              conversionFactor
            )}`}</strong>
          </div>
        </div>

        <div className="quick_balance_Box">
          <div className="quick_balance_Box_ico">
            <img src="/images/balance_commision_ico.svg" alt="" />
          </div>
          <div className="quick_balance_Box_cnt">
            <span>{t("commission")}</span>
            <strong>{`${
              currency?.symbolLeft ? currency?.symbolLeft : "$"
            } ${CurrencyConverter(
              tiles?.commission,
              conversionFactor
            )}`}</strong>
            <div
              className={`tp_comparison ${
                tiles?.commissionSign ? "up" : "down"
              }`}
            >
              <span>
                {`${tiles?.commissionSign === "up" ? "+" : "-"}${
                  tiles?.commissionPercentage ?? "0"
                }%`}
              </span>
              <i
                className={`fa-solid ${
                  tiles?.commissionSign === "up"
                    ? "fa-arrow-up"
                    : "fa-arrow-down"
                }`}
              ></i>
            </div>
          </div>
        </div>

        <div className="quick_balance_Box">
          <div className="quick_balance_Box_ico">
            <img src="/images/balance_commision_ico.svg" alt="" />
          </div>
          <div className="quick_balance_Box_cnt">
            <span>{t("totalCredit")}</span>
            <strong>{`${
              currency?.symbolLeft ? currency?.symbolLeft : "$"
            } ${CurrencyConverter(
              tiles?.totalCredit,
              conversionFactor
            )}`}</strong>
            <div
              className={`tp_comparison ${tiles?.creditSign ? "up" : "down"}`}
            >
              <span>
                {`${tiles?.creditSign === "up" ? "+" : "-"}${
                  tiles?.totalCreditPercentage ?? "0"
                }%`}
              </span>
              <i
                className={`fa-solid ${
                  tiles?.creditSign === "up" ? "fa-arrow-up" : "fa-arrow-down"
                }`}
              ></i>
            </div>
          </div>
        </div>

        <div className="quick_balance_Box">
          <div className="quick_balance_Box_ico">
            <img src="/images/balance_pending_ico.svg" alt="" />
          </div>
          <div className="quick_balance_Box_cnt">
            <span>{t("totalDebit")}</span>
            <strong>{`${
              currency?.symbolLeft ? currency?.symbolLeft : "$"
            } ${CurrencyConverter(
              tiles?.totalDebit,
              conversionFactor
            )}`}</strong>
            <div
              className={`tp_comparison ${tiles?.debitSign ? "up" : "down"}`}
            >
              <span>
                {`${tiles?.debitSign === "up" ? "+" : "-"}${
                  tiles?.totalDebitPercentage ?? "0"
                }%`}
              </span>
              <i
                className={`fa-solid ${
                  tiles?.debitSign === "up" ? "fa-arrow-up" : "fa-arrow-down"
                }`}
              ></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default QuickBalance;
