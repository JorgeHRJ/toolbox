import {Calendar} from '@fullcalendar/core';
import interactionPlugin from '@fullcalendar/interaction';
import dayGridPlugin from '@fullcalendar/daygrid';
import esLocale from '@fullcalendar/core/locales/es';
import {Modal} from 'bootstrap';
import {initDatepicker} from "../components/datepicker";

let calendar = null;
let addModal = null;
let editModal = null;
let editDatepicker = null;
let infoDrop = null;

function convertDate(dateStr) {
  return dateStr.split('/').reverse().join('-');
}

function getClassNameForEvent(color) {
  return `bg-${color} border border-light rounded p-1`;
}

function getEvent(id, title, date, color) {
  return {
    id: id,
    title: title,
    start: date,
    end: date,
    className: getClassNameForEvent(color),
    draggable: true
  };
}

function patchReady(event) {
  const httpRequest = event.currentTarget;
  if (httpRequest.readyState === 4) {
    const data = JSON.parse(httpRequest.response);
    if (httpRequest.status === 200) {
      const eventCalendar = calendar.getEventById(data.id);
      if (eventCalendar) {
        eventCalendar.setProp('title', data.title);
        console.log(data.date);
        eventCalendar.setStart(data.date);
        eventCalendar.setProp('className', getClassNameForEvent(data.tag.color));
      }

      if (editModal !== null) {
        editModal.hide();
        editModal = null;
      }

      const form = document.querySelector('form');
      form.reset();
    }

    if (httpRequest.status === 400) {
      const alert = document.querySelector('[data-component="error-message"]');
      alert.innerText = alert.message;

      if (infoDrop !== null) {
        infoDrop.revert();
        infoDrop = null;
      }
    }
  }
}

function postReady(event) {
  const httpRequest = event.currentTarget;
  if (httpRequest.readyState === 4) {
    const data = JSON.parse(httpRequest.response);
    if (httpRequest.status === 201) {
      const event = getEvent(data.id, data.title, data.date, data.tag.color);
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

function patch(url, data) {
  const httpRequest = new XMLHttpRequest();

  httpRequest.onreadystatechange = patchReady;
  httpRequest.open('PATCH', url);
  httpRequest.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
  httpRequest.setRequestHeader('Content-Type', 'application/json');

  httpRequest.send(JSON.stringify(data));
}

function post(url, data) {
  const httpRequest = new XMLHttpRequest();

  httpRequest.onreadystatechange = postReady;
  httpRequest.open('POST', url);
  httpRequest.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
  httpRequest.setRequestHeader('Content-Type', 'application/json');

  httpRequest.send(JSON.stringify(data));
}

function onPatchSubmit(event) {
  event.preventDefault();

  const form = event.target;
  const formData = new FormData(form);
  const id = form.dataset.id;

  const eventDom = document
    .querySelector('[data-component="task-container"]')
    .querySelector(`[data-id="${id}"]`)
  if (eventDom) {
    const url = eventDom.dataset.edit;

    let data = {};
    formData.forEach((value, key) => {
      if (key === 'date') {
        value = convertDate(value);
      }
      data[key] = value;
    });

    patch(url, data);
  }
}

function onPostSubmit(event) {
  event.preventDefault();

  const form = event.target;
  const formData = new FormData(form);

  let data = {};
  formData.forEach((value, key) => {
    data[key] = value;
  });

  post(form.action, data);
}

function onDropEvent(info) {
  infoDrop = info;

  const id = info.event.id;
  const eventDom = document
    .querySelector('[data-component="task-container"]')
    .querySelector(`[data-id="${id}"]`)
  if (eventDom) {
    const url = eventDom.dataset.edit;
    const data = {
      date: info.event.startStr
    };

    patch(url, data);
  }
}

function openAddModal(d) {
  const modalElement = document.querySelector('[data-component="modal-add-task"]');
  if (modalElement) {
    const dateText = modalElement.querySelector('[data-component="task-date"]');
    const dateInput = modalElement.querySelector('input[name="date"]');
    dateText.innerText = d.date.toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' });
    dateInput.value = d.dateStr;

    const form = modalElement.querySelector('form');
    form.addEventListener('submit', onPostSubmit);

    addModal = new Modal(modalElement);
    addModal.show();
  }
}

function openEditModal(info) {
  const modalElement = document.querySelector('[data-component="modal-edit-task"]');
  if (modalElement) {
    const titleInput = modalElement.querySelector('input[name="title"]');
    titleInput.value = info.event.title;

    editDatepicker.setDate(info.event.start);

    const form = modalElement.querySelector('form');
    form.dataset.id = info.event.id;
    form.addEventListener('submit', onPatchSubmit);

    editModal = new Modal(modalElement);
    editModal.show();
  }
}

function getEventsFromDom() {
  let events = [];
  const eventsDom = document.querySelectorAll('[data-component="task-event"]');
  eventsDom.forEach((eventDom) => {
    const { id, title, date, color } = eventDom.dataset;
    events.push(getEvent(id, title, date, color));
  });

  return events;
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
    events: getEventsFromDom(),
    dateClick: (d) => {
      openAddModal(d)
    },
    datesSet: (dateInfo) => {
      console.log(dateInfo);
    },
    eventDrop: (info) => {
      onDropEvent(info);
    },
    eventClick: (info) => {
      openEditModal(info);
    }
  });
  calendar.render();
}

function initEditModal() {
  const modalElement = document.querySelector('[data-component="modal-edit-task"]');
  if (modalElement) {
    const datepickerElement = modalElement.querySelector('[data-component="datepicker"]');
    if (datepickerElement) {
      editDatepicker = initDatepicker(datepickerElement);
    }
  }
}

function init() {
  const calendar = document.querySelector('[data-component="calendar"]');
  if (calendar) {
    initCalendar(calendar);
  }

  initEditModal();
}

export default init;
