import './css/app.scss';

// Stimulus app
import './js/bootstrap';

// import modules
import initTheme from './js/modules/theme';

// import components
import initEditorComponent from './js/components/editor';
import initNotyfComponent from './js/components/notyf';
import initSuggestifyComponent from './js/components/suggestify';

// import controllers
import initTaskController from './js/controllers/task_controller';
import initTransactionCategoryController from './js/controllers/transactioncategory_controller';
import initReservoirController from './js/controllers/reservoir_controller';

// init modules
initTheme();

// init components
initEditorComponent();
initNotyfComponent();
initSuggestifyComponent();

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
