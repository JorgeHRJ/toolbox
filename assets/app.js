import './css/app.scss';

// Stimulus app
import './js/bootstrap';

// import modules
import initTheme from './js/modules/theme';

// import components
import initEditorComponent from './js/components/editor';
import {initNotyfComponent} from './js/components/notyf';
import initSuggestifyComponent from './js/components/suggestify';
import initSlideshowComponent from './js/components/slideshow';
import initColorPickerComponent from './js/components/color-picker';
import {initChoicesComponent} from './js/components/choices';

// import controllers
import initTaskController from './js/controllers/task_controller';
import initTransactionCategoryController from './js/controllers/transactioncategory_controller';
import initReservoirController from './js/controllers/reservoir_controller';
import initIrrigationController from './js/controllers/irrigation_controller';
import initCronosController from './js/controllers/cronos_controller';

// init modules
initTheme();

// init components
initEditorComponent();
initNotyfComponent();
initSuggestifyComponent();
initSlideshowComponent();
initColorPickerComponent();
initChoicesComponent();

// init controllers
if (document.querySelector('[data-controller="task"]')) {
  initTaskController();
}

if (document.querySelector('[data-controller="transactioncategory"]')) {
  initTransactionCategoryController();
}

if (document.querySelector('[data-controller="reservoir"]')) {
  initReservoirController();
}

if (document.querySelector('[data-controller="irrigation"]')) {
  initIrrigationController();
}

if (document.querySelector('[data-controller="cronos"]')) {
  initCronosController();
}
