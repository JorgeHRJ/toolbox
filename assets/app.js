import './css/app.scss';

// Stimulus app
import './js/bootstrap';

// import modules
import initTheme from './js/modules/theme';

// import components
import { initDatepickerComponent } from './js/components/datepicker';

// import controllers
import initTaskController from './js/controllers/task_controller';

initTheme();
initDatepickerComponent();

if (document.querySelector('[data-controller="task"]')) {
  initTaskController();
}
