import Chart from "chart.js/auto";

export const createChart = (chartRef, charts) => {
    const colors = {
        purple: {
            default: "rgba(149, 76, 233, 1)",
            half: "rgba(149, 76, 233, 0.5)",
            quarter: "rgba(149, 76, 233, 0.15)",
            zero: "rgba(149, 76, 233, 0)",
        },
        indigo: {
            default: "rgba(80, 102, 120, 1)",
            quarter: "rgba(80, 102, 120, 0.25)",
        },
    };
    const weight = (charts) ? Object?.values(charts) : ''
    const labels = (charts) ? Object?.keys(charts) : ''
    const ctx = chartRef.current.getContext("2d");
    const chartContainer = chartRef.current.parentNode;
    ctx.canvas.height = 250;
    ctx.canvas.width = chartContainer.clientWidth;

    const gradient = ctx.createLinearGradient(0, 25, 0, 300);
    gradient.addColorStop(0, colors.purple.half);
    gradient.addColorStop(0.35, colors.purple.quarter);
    gradient.addColorStop(1, colors.purple.zero);

    const options = {
        type: "line",
        data: {
            labels: labels,
            datasets: [
                {
                    fill: true,
                    backgroundColor: gradient,
                    pointBackgroundColor: colors.purple.default,
                    borderColor: colors.purple.default,
                    data: weight,
                    lineTension: 0.45,
                    borderWidth: 2.5,
                    pointRadius: 4,
                },
            ],
        },
        options: {
            animations: {
                tension: {
                    duration: 2000,
                    easing: 'linear',
                    from: 0.1,
                    to: 0.45,
                    loop: true
                }
            },
            layout: {
                padding: 10,
            },
            responsive: true,
            plugins: {
                legend: {
                    display: false,
                },
            },
            scales: {
                x: {
                    grid: {
                        display: false,
                    },
                    ticks: {
                        padding: 10,
                        autoSkip: false,
                    },
                },
                y: {
                    scaleLabel: {
                        display: true,
                        labelString: "",
                        padding: 10,
                    },
                    grid: {
                        display: false,
                        color: "rgba(75, 0, 130, 0.25)",
                    },
                    ticks: {
                        beginAtZero: false,
                        max: 63,
                        min: 57,
                        padding: 10,
                    },
                    beginAtZero: true
                },
            },
        },
    };

    const chart = new Chart(ctx, options);

    // Update default font color
    Chart.defaults.color = colors.indigo.default;

    // Destroy chart when component unmounts
    return () => {
        chart.destroy();
    };
};