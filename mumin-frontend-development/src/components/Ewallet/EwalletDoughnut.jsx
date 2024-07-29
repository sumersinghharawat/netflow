import React, { useEffect, useRef } from "react";
import Chart from "chart.js/auto";
import { useTranslation } from "react-i18next";
import CurrencyConverter from "../../Currency/CurrencyConverter";

const EwalletChart = ({
  spend,
  balance,
  spentRatio,
  balanceRatio,
  currency,
  conversionFactor,
}) => {
  const { t } = useTranslation();
  const chartInstanceRef = useRef(null);
  useEffect(() => {
    const chartOptions = {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false,
        },
      },
      cutout: 40,
    };

    let chart = null; // Declare chart variable

    if (spentRatio === 0 && balanceRatio === 0) {
      const chartData = {
        datasets: [
          {
            data: [1], // A small value to create a placeholder
            backgroundColor: ["#cccccc"],
          },
        ],
      };
      chartOptions.plugins.tooltip = { enabled: false }; // Disable tooltips for the message

      chart = new Chart(chartInstanceRef.current, {
        type: "doughnut",
        data: chartData,
        options: chartOptions,
      });
    } else {
      const chartData = {
        labels: ["Spent", "Balance"],
        datasets: [
          {
            data: [`${spentRatio}`, `${balanceRatio}`],
            backgroundColor: ["#044796", "#0ADAB4"],
            borderColor: ["#044796", "#0ADAB4"],
            borderWidth: 1,
          },
        ],
      };

      chart = new Chart(chartInstanceRef.current, {
        type: "doughnut",
        data: chartData,
        options: chartOptions,
      });
    }

    // Cleanup the chart when the component is unmounted
    return () => {
      chart.destroy();
    };
  }, [spentRatio, balanceRatio]);

  return (
    <div className="col-xl-3 col-md-12">
      <div className="ewallet_top_qc_balance_box p-0">
        <div className="row">
          <div className="col-xl-8 col-lg-6" style={{ position: "relative" }}>
            <canvas
              height="100"
              ref={chartInstanceRef}
              id="transactions"
              style={{ display: "block", height: "100%", width: "100%" }}
            ></canvas>
            <div className="ewallet_top_balance">
              <span>{t("balance")}</span>
              {`${currency?.symbolLeft ?? ''} ${CurrencyConverter(
                balance,
                conversionFactor
              )}`}
            </div>
          </div>
          <div className="col-xl-4 col-lg-6">
            <div className="top_chart_legend_ewallet__txt_bx">
              <div className="top_chart_legend_ewallet_hd">{t("spent")}</div>
              <div className="top_chart_legend_ewallet_val">{`${
                currency?.symbolLeft
              } ${CurrencyConverter(spend, conversionFactor)}`}</div>
            </div>
            <div className="top_chart_legend_ewallet__txt_bx">
              <div className="top_chart_legend_ewallet_hd">{t("balance")}</div>
              <div className="top_chart_legend_ewallet_val">{`${
                currency?.symbolLeft
              } ${CurrencyConverter(balance, conversionFactor)}`}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default EwalletChart;
