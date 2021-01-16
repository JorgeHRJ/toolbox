import {initDatepicker} from "../components/datepicker";

function init() {
  const datepickerElement = document.querySelector('[data-component="datepicker"]');
  if (datepickerElement) {
    const datepickerOptions = {format: 'mm/yyyy', pickLevel: 1}
    initDatepicker(datepickerElement, datepickerOptions);
  }
}

export default init;
