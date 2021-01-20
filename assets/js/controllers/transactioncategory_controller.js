import {initDatepicker} from "../components/datepicker";

const DATEPICKER_OPTIONS = {format: 'mm/yyyy', pickLevel: 1};

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
}

export default init;
