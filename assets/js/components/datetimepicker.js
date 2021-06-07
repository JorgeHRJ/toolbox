import flatpickr from 'flatpickr';

function initDatetimePicker(element) {
  const format = element.dataset.format ? element.dataset.format : 'd/m/Y H:i';
  const time = !!element.dataset.time;

  flatpickr(element, {
    allowInput: true,
    enableTime: time,
    dateFormat: format,
    monthSelectorType: 'static'
  });
}

function init() {
  const datepickers = [].slice.call(document.querySelectorAll('[data-component="datetimepicker"]'));
  const datepickersList = datepickers.map((element) => {
    return initDatetimePicker(element);
  });
}

export {
  init as initDatetimePickerComponent,
  initDatetimePicker
};
