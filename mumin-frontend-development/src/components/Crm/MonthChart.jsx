import React, { useEffect, useRef } from "react";
import Chart from "chart.js/auto";

const MonthlyChart = ({data}) => {
  const chartRef = useRef(null);
  const labels = Object.keys(data?.accepted || 0)
  const acceptedValue = Object.values(data?.accepted || 0)
  const rejectedValue = Object.values(data?.rejected || 0)
  const ongoingValue  = Object.values(data?.ongoing || 0)

  useEffect(() => {
    const config = {
      type: "line",
      data: {
        labels: labels,
        datasets: [
          {
            label: "Accepted",
            data: acceptedValue,
            backgroundColor: "#954cea",
            borderColor: "#954cea",
            fill: false,
          },
          {
            label: "Rejected",
            data: rejectedValue,
            backgroundColor: "#2c008a",
            borderColor: "#2c008a",
            fill: false,
          },
          {
            label: "Ongoing",
            data: ongoingValue,
            backgroundColor: "#9b88f9",
            borderColor: "#9b88f9",
            fill: false,
          },
        ],
      },
      options: {
        responsive: true,
        animations: {
          tension: {
            duration: 2000,
            easing: "linear",
            from: 0.1,
            to: 0.55,
            loop: true,
          },
        },
        plugins: {
          title: {
            display: true,
            text: "Leads this Month",
          },
        },
        scales: {
          x: {
            title: {
              display: true,
              text: "Day",
            },
          },
          y: {
            title: {
              display: true,
            },
          },
        },
      },
    };

    const ctx = document.getElementById("lead2");

    if (chartRef.current) {
      chartRef.current.destroy(); // Destroy existing chart instance
    }

    if (ctx) {
      chartRef.current = new Chart(ctx, config); // Create new chart
    }

    return () => {
      if (chartRef.current) {
        chartRef.current.destroy(); // Cleanup on unmount
      }
    };
  }, [data]);

  return (
    <div className="col-xl-6 col-lg-12">
      <div className="grph-layout">
        <canvas id="lead2" />
      </div>
    </div>
  );
};

export default MonthlyChart;
