import './css/app.scss';

// Stimulus app
import './js/bootstrap';

// import modules
import initTheme from './js/modules/theme';

// import components

// import controllers
import initTaskController from './js/controllers/task_controller';
import initTransactionCategory from './js/controllers/transactioncategory_controller';

initTheme();

if (document.querySelector('[data-controller="task"]')) {
  initTaskController();
}

if (document.querySelector('[data-controller="transactioncategory"]')) {
  initTransactionCategory();
}
