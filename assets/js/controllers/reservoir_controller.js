import Chart from 'chart.js';

let indexChart = null;

function getDetailAxisData() {
  let x = [];
  let y = [];

  const rows = document.querySelectorAll('tbody tr');
  rows.forEach((row) => {
    const dateCell = row.querySelector('[data-date]');
    const { date } = dateCell.dataset;
    x.push(date);

    const fillnessCell = row.querySelector('[data-fillness]');
    const { fillness } = fillnessCell.dataset;
    y.push(fillness);
  });

  return {x: x.reverse(), y: y.reverse()};
}

function getIndexAxisData() {
  let x = [];
  let y = [];

  const items = document.querySelectorAll('[data-component="reservoir-item"]');
  items.forEach((item) => {
    const nameElement = item.querySelector('[data-name]');
    const { name } = nameElement.dataset;
    x.push(name);

    const filnessElement = item.querySelector('[data-fillness]');
    const { fillness } = filnessElement.dataset;
    y.push(parseInt(fillness, 10));
  });

  return {x: x, y: y};
}

function initDetailChart() {
  const axisData = getDetailAxisData();

  const ctx = document.querySelector('[data-component="reservoir-detail-stats"]').getContext('2d');
  indexChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: axisData.x,
      datasets: [{
        label: 'Dataset',
        data: axisData.y,
        backgroundColor: 'rgba(21, 163, 98, 0.2)',
        borderColor: 'rgba(21, 163, 98, 1)',
        borderWidth: 1
      }]
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
          label: function(tooltipItem) {
            return tooltipItem.value + ' %';
          }
        }
      },
      scales: {
        yAxes: [{
          ticks: {
            beginAtZero: true,
            stepSize: 20,
            userCallback: (value) => {
              return value + ' %';
            }
          }
        }]
      }
    }
  });
}

function initIndexChart() {
  const axisData = getIndexAxisData();

  const ctx = document.querySelector('[data-component="reservoir-detail-stats"]').getContext('2d');
  indexChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: axisData.x,
      datasets: [{
        label: 'Datos de la última actualización',
        data: axisData.y,
        backgroundColor: 'rgba(21, 163, 98, 0.2)',
        borderColor: 'rgba(21, 163, 98, 1)',
        borderWidth: 1
      }]
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
          label: function(tooltipItem) {
            return tooltipItem.value + ' %';
          }
        }
      },
      scales: {
        yAxes: [{
          ticks: {
            beginAtZero: true,
            stepSize: 20,
            userCallback: (value) => {
              return value + '%';
            }
          }
        }]
      }
    }
  });
}

function initIndex() {
  initIndexChart()
}

function initDetail() {
  initDetailChart();
}

function init() {
  if (document.querySelector('[data-page="index"]')) {
    initIndex();
  }

  if (document.querySelector('[data-page="detail"]')) {
    initDetail();
  }
}

export default init;
