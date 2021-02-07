import { initDatepicker } from "../components/datepicker";
import Chart from 'chart.js';

const DATEPICKER_OPTIONS = {format: 'mm/yyyy', pickLevel: 1};

function getBalanceAxis() {
  let x = [];
  let y = { incomes: [], expenses: [] };

  const items = document.querySelectorAll('[data-component="balance-item"]');
  items.forEach((item) => {
    x.push(item.dataset.date);

    y.incomes.push(item.dataset.incomes);
    y.expenses.push(item.dataset.expenses);
  });

  return {x: x, y: y};
}

function initBalanceChart() {
  const axisData = getBalanceAxis();

  const ctx = document.querySelector('[data-component="transaction-balance-stats"]').getContext('2d');
  const balanceChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: axisData.x,
      datasets: [
        {
          label: 'Gastos',
          data: axisData.y.expenses,
          backgroundColor: 'rgba(211, 47, 47)',
          borderColor: 'rgba(244, 67, 54)',
          borderWidth: 1
        },
        {
          label: 'Ingresos',
          data: axisData.y.incomes,
          backgroundColor: 'rgba(25, 118, 210)',
          borderColor: 'rgba(33, 150, 243)',
          borderWidth: 1
        }
      ]
    },
    options: {
      responsive: false,
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
            return tooltipItem.value + ' €';
          }
        }
      },
      scales: {
        yAxes: [{
          ticks: {
            beginAtZero: true,
            stepSize: 100,
            userCallback: (value) => {
              return value + ' €';
            }
          }
        }]
      }
    }
  });
}

function onChangeDate(event) {
  const date = event.detail.date;
  const input = event.target;
  const { path } = input.dataset

  const month = ('0' + (date.getMonth() + 1)).slice(-2);
  const year = date.getFullYear();

  window.location.href = `${path}/${year}${month}`;
}

function init() {
  const datepickerElement = document.querySelector('[data-component="datepicker"]');
  if (datepickerElement) {
    initDatepicker(datepickerElement, DATEPICKER_OPTIONS);
  }

  let monthDatepicker = document.querySelector('[data-component="month-datepicker"]');
  if (monthDatepicker) {
    initDatepicker(monthDatepicker, DATEPICKER_OPTIONS);
    monthDatepicker.addEventListener('changeDate', onChangeDate);
  }

  const balanceStats = document.querySelector('[data-component="transaction-balance-stats"]');
  if (balanceStats) {
    initBalanceChart();
  }
}

export default init;
