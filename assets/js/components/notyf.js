import { Notyf } from 'notyf';

function initNotification(message, type) {
  const notyf = new Notyf({
    position: {x: 'right', y: 'top'},
    duration: 4000
  });

  switch (type) {
    case 'success':
      notyf.success(message);
      break;
    case 'error':
      notyf.error(message);
      break;
  }
}

function init() {
  const messages = document.querySelectorAll('[data-component="notification"]');
  if (messages) {
    messages.forEach((message) => {
      initNotification(message.innerText, message.dataset.type);
    })
  }
}

export default init;
