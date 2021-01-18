import { Datepicker } from 'vanillajs-datepicker';
import es from "vanillajs-datepicker/js/i18n/locales/es";

function initDatepicker(element, options = {}) {
  Object.assign(Datepicker.locales, es);

  const defaultOptions = {
    buttonClass: 'btn',
    language: 'es'
  };
  const finalOptions = Object.assign(defaultOptions, options);

  return new Datepicker(element, finalOptions);
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
