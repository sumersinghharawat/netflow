import React, { useEffect, useRef } from "react";
import Chart from "chart.js/auto";

const YearlyChart = ({data}) => {
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
            backgroundColor: "#954cea",
            borderColor: "#954cea",
            fill: false,
            data: acceptedValue,
          },
          {
            label: "Rejected",
            backgroundColor: "#2c008a",
            borderColor: "#2c008a",
            fill: false,
            data: rejectedValue,
          },
          {
            label: "Ongoing",
            backgroundColor: "#9b88f9",
            borderColor: "#9b88f9",
            fill: false,
            data: ongoingValue,
          },
        ],
      },
      options: {
        responsive: true,
        animations: {
            tension: {
                duration: 2000,
                easing: 'linear',
                from: 0.1,
                to: 0.45,
                loop: true
            }
        },
        plugins: {
          title: {
            display: true,
            text: "Leads this year",
          },
        },
        scales: {
          x: {
            title: {
              display: true,
              text: "Month",
            },
          },
          y: {
            title: {
              display: true,
            },
            ticks: {
              stepSize: 100,
            },
          },
        },
      },
    };

    const ctx = document.getElementById("lead1");

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
        <canvas id="lead1" />
      </div>
    </div>
  );
};

export default YearlyChart;
