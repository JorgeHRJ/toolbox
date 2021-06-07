import Choices from 'choices.js';

function initSimple(element) {
  const simples = new Choices(element);
}

function init() {
  const simples = document.querySelectorAll('[data-component="choices"][data-type="simple"]');
  simples.forEach((simpleEl) => {
    initSimple(simpleEl);
  })
}

export {
  init as initChoicesComponent,
  initSimple as initChoiceSimple
};
