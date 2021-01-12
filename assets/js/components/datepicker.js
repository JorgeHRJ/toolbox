import { Datepicker } from 'vanillajs-datepicker';
import es from "vanillajs-datepicker/js/i18n/locales/es";

function initDatepicker(element) {
  Object.assign(Datepicker.locales, es);

  return new Datepicker(element, {
    buttonClass: 'btn',
    format: 'dd/mm/yyyy',
    language: 'es'
  });
}

function initDatepickerComponent() {
  const datepickers = [].slice.call(document.querySelectorAll('[data-component="datepicker"]'));
  const datepickersList = datepickers.map((element) => {
    return initDatepicker(element);
  });
}

export {
  initDatepickerComponent,
  initDatepicker
};
