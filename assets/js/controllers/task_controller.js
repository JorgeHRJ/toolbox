import { Calendar } from '@fullcalendar/core';
import interactionPlugin from '@fullcalendar/interaction';
import dayGridPlugin from '@fullcalendar/daygrid';
import esLocale from '@fullcalendar/core/locales/es';
import { Modal } from 'bootstrap';

let calendar = null;
let addModal = null;

function postReady(event) {
  const httpRequest = event.currentTarget;
  if (httpRequest.readyState === 4) {
    const data = JSON.parse(httpRequest.response);
    if (httpRequest.status === 201) {
      const event = {
        id: data.id,
        title: data.title,
        start: data.date,
        end: data.date,
        className: `bg-${data.tag.color} border border-light rounded p-1`,
        draggable: true
      };
      calendar.addEvent(event);

      if (addModal !== null) {
        addModal.hide();
        addModal = null;
      }

      const form = document.querySelector('form');
      form.reset();
    }

    if (httpRequest.status === 400) {
      const alert = document.querySelector('[data-component="error-message"]');
      alert.innerText = alert.message;
    }
  }
}

function post(url, data) {
  const httpRequest = new XMLHttpRequest();

  httpRequest.onreadystatechange = postReady;
  httpRequest.open('POST', url);
  httpRequest.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
  httpRequest.setRequestHeader('Content-Type', 'application/json');

  httpRequest.send(data);
}

function onSubmit(event) {
  event.preventDefault();

  const form = event.target;
  const formData = new FormData(form);

  let data = {};
  formData.forEach((value, key) => {
    data[key] = value;
  });

  post(form.action, JSON.stringify(data));
}

function openAddModal(d) {
  const modalElement = document.querySelector('[data-component="modal-add-task"]');
  if (modalElement) {
    const dateText = modalElement.querySelector('[data-component="task-date"]');
    const dateInput = modalElement.querySelector('input[name="date"]');
    dateText.innerText = d.date.toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' });
    dateInput.value = d.dateStr;

    const form = modalElement.querySelector('form');
    form.addEventListener('submit', onSubmit);

    addModal = new Modal(modalElement);
    addModal.show();
  }
}

function initCalendar(element) {
  calendar = new Calendar(element, {
    plugins: [ dayGridPlugin, interactionPlugin ],
    timeZone: 'UTC',
    locale: esLocale,
    initialView: 'dayGridWeek',
    selectable: true,
    editable: true,
    headerToolbar: {
      left: 'prev,next',
      center: 'title',
      right: 'dayGridDay,dayGridWeek'
    },
    dateClick: (d) => {
      openAddModal(d)
    },
    datesSet: (dateInfo) => {
      console.log(dateInfo);
    }
  });
  calendar.render();
}

function init() {
  const calendar = document.querySelector('[data-component="calendar"]');
  if (calendar) {
    initCalendar(calendar);
  }
}

export default init;
