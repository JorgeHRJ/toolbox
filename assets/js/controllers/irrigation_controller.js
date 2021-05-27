import Chart from "chart.js";

function initZonePage() {
  const types = [];
  const x = [];
  const outdoors = [];
  const indoors = [];

  const irrigationItems = document.querySelectorAll('[data-component="irrigation-data-item"]');
  irrigationItems.forEach((irrigationItem) => {
    const { date } = irrigationItem.querySelector('[data-date]').dataset;

    const rows = irrigationItem.querySelectorAll('tbody tr');
    rows.forEach((row) => {
      const { type } = row.querySelector('[data-type]').dataset;
      const outdoorsValue = row.querySelector('[data-outdoors]').dataset.outdoors;
      const indoorsValue = row.querySelector('[data-indoors]').dataset.indoors;

      if (!types.includes(type)) {
        types.push(type);
      }

      if (!(type in x)) {
        x[type] = [];
      }
      x[type].push(date);

      if (!(type in outdoors)) {
        outdoors[type] = [];
      }
      outdoors[type].push(parseFloat(outdoorsValue));

      if (!(type in indoors)) {
        indoors[type] = [];
      }
      indoors[type].push(parseFloat(indoorsValue));
    })
  });
  const chartsContainer = document.querySelector('[data-component="irrigations-charts-container"]');
  const chartPrototype = chartsContainer.querySelector('[data-compononent="irrigation-chart-prototype"]');
  types.forEach((type) => {
    const chartHolder = chartPrototype.cloneNode(true);
    chartsContainer.appendChild(chartHolder);

    chartHolder.classList.remove('hide');
    chartHolder.querySelector('[data-title]').innerText = type;

    const chartEl = chartHolder.querySelector('[data-component="irrigations-chart"]');
    const chartCtx = chartEl.getContext('2d');
    const chartOptions = {
      type: 'line',
      data: {
        labels: x[type],
        datasets: [
          {
            label: 'Aire Libre',
            data: outdoors[type],
            backgroundColor: 'rgba(218, 50, 99, 0.2)',
            fill: false,
            borderColor: 'rgba(218, 50, 99, 1)',
            borderWidth: 1
          },
          {
            label: 'Invernadero',
            data: indoors[type],
            backgroundColor: 'rgba(50, 144, 218, 0.2)',
            fill: false,
            borderColor: 'rgba(50, 144, 218, 1)',
            borderWidth: 1
          },
        ]
      },
      options: {
        responsive: true,
        tooltips: {
          mode: 'index',
          intersect: false,
          titleMarginBottom: 10,
          bodySpacing: 10,
          xPadding: 16,
          yPadding: 16,
          borderColor: '#e7e9ed',
          borderWidth: 1,
          backgroundColor: '#fff',
          bodyFontColor: '#252930',
          titleFontColor: '#252930',
          callbacks: {
            label: (tooltipItem) => {
              return `${tooltipItem.value} L`;
            }
          }
        },
        scales: {
          yAxes: [{
            ticks: {
              beginAtZero: true,
              userCallback: (value) => {
                return `${value.toLocaleString()} L`;
              }
            }
          }]
        }
      }
    };

    const chart = new Chart(chartCtx, chartOptions);
  });
  chartPrototype.remove();
}

function init() {
  if (document.querySelector('[data-page="zone"]')) {
    initZonePage();
  }
}

export default init;
