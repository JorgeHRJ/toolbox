import {Calendar} from '@fullcalendar/core';
import interactionPlugin from '@fullcalendar/interaction';
import dayGridPlugin from '@fullcalendar/daygrid';
import esLocale from '@fullcalendar/core/locales/es';
import {Modal} from 'bootstrap';

import {initDatepicker} from '../components/datepicker';
import initTagController from './tag_controller';

let calendar = null;
let addModal = null;
let editModal = null;
let editDatepicker = null;
let infoDrop = null;
let typingTimer = null;
const doneTypingInterval = 1000;

function convertDate(dateStr) {
  return dateStr.split('/').reverse().join('-');
}

function getClassListForTaskPreview(color) {
  return `badge bg-${color} text-white p-2`;
}

function getClassNameForEvent(color, status) {
  let className = `bg-${color} border border-light rounded p-1`;
  if (parseInt(status, 10) === 1) {
    className = className + ' task-done';
  }

  return className;
}

function getEvent(id, title, date, color, status) {
  return {
    id: id,
    title: title,
    start: date,
    end: date,
    className: getClassNameForEvent(color, status),
    draggable: true
  };
}

function applyDivDataset(element, id, title, date, color, status, editUrl, deleteUrl) {
  element.dataset.id = id;
  element.dataset.title = title;
  element.dataset.date = date;
  element.dataset.color = color;
  element.dataset.status = status;
  element.dataset.edit = editUrl;
  element.dataset.delete = deleteUrl;
  element.dataset.component = 'task-event';
}

function editInDom(id, title, date, color, status) {
  const element = document
    .querySelector('[data-component="task-container"]')
    .querySelector(`[data-id="${id}"]`);

  applyDivDataset(element, id, title, date, color, status, element.dataset.edit, element.dataset.delete);
}

function addInDom(id, title, date, color, status) {
  const container = document.querySelector('[data-component="task-container"]');

  let div = document.createElement('div');
  let editUrl = container.dataset.edit;
  editUrl = editUrl.replace('0', id);

  let deleteUrl = container.dataset.delete;
  deleteUrl = deleteUrl.replace('0', id);

  applyDivDataset(div, id, title, date, color, status, editUrl, deleteUrl);

  container.appendChild(div);
}

function removeReady(event) {
  const httpRequest = event.currentTarget;
  if (httpRequest.readyState === 4) {
    const data = JSON.parse(httpRequest.response);
    if (httpRequest.status === 200) {
      const eventCalendar = calendar.getEventById(data.id);
      if (eventCalendar) {
        eventCalendar.remove();
      }

      if (editModal !== null) {
        editModal.hide();
        editModal = null;
      }
    }

    if (httpRequest.status === 400) {
      const alert = document
        .querySelector('[data-component="modal-edit-task"]')
        .querySelector('[data-component="error-message"]');
      alert.innerText = alert.message;
    }
  }
}

function patchReady(event) {
  const httpRequest = event.currentTarget;
  if (httpRequest.readyState === 4) {
    const data = JSON.parse(httpRequest.response);
    if (httpRequest.status === 200) {
      const eventCalendar = calendar.getEventById(data.id);
      if (eventCalendar) {
        eventCalendar.setProp('title', data.title);
        eventCalendar.setStart(data.date);
        eventCalendar.setProp('classNames', getClassNameForEvent(data.tag.color, data.status).split(' '));
      }
      editInDom(data.id, data.title, data.date, data.tag.color, data.status);

      if (editModal !== null) {
        editModal.hide();
        editModal = null;
      }

      const form = document.querySelector('form');
      form.reset();
    }

    if (httpRequest.status === 400) {
      const alert = document
        .querySelector('[data-component="modal-edit-task"]')
        .querySelector('[data-component="error-message"]');
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
      const event = getEvent(data.id, data.title, data.date, data.tag.color, data.status);
      calendar.addEvent(event);
      addInDom(data.id, data.title, data.date, data.tag.color, data.status);

      if (addModal !== null) {
        addModal.hide();
        addModal = null;
      }

      const form = document.querySelector('form');
      form.reset();
    }

    if (httpRequest.status === 400) {
      const alert = document
        .querySelector('[data-component="modal-add-task"]')
        .querySelector('[data-component="error-message"]');
      alert.innerText = alert.message;
    }
  }
}

function remove(url) {
  const httpRequest = new XMLHttpRequest();

  httpRequest.onreadystatechange = removeReady;
  httpRequest.open('DELETE', url);
  httpRequest.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

  httpRequest.send();
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

function onDelete(event) {
  event.preventDefault();

  const form = document.querySelector('[data-component="modal-edit-task"]').querySelector('form');
  const id = form.dataset.id;

  const eventDom = document
    .querySelector('[data-component="task-container"]')
    .querySelector(`[data-id="${id}"]`)
  if (eventDom) {
    const url = eventDom.dataset.delete;

    remove(url);
  }
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

    const statusSwitch = document.querySelector('input[name="status"]');
    data['status'] = statusSwitch.checked ? 1 : 0;

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

function getBadgeClassList(color) {
  return `badge bg-${color} text-white p-2`;
}

function handleTaskTitleInput(event, modal) {
  const input = event.currentTarget;
  const taskPreview = modal.querySelector('[data-component="task-preview"]');

  clearTimeout(typingTimer);
  if (input.value.trim() !== '') {
    typingTimer = setTimeout(() => {
      taskPreview.innerText = input.value;
    }, doneTypingInterval);
  }
}

function onColorButtonClicked(event, modal) {
  const button = event.currentTarget;
  const {color, id} = button.dataset;

  const tagPreview = modal.querySelector('[data-component="task-preview"]');
  tagPreview.classList.value = getBadgeClassList(color);

  const inputTag = modal.querySelector('input[name="tag"]');
  inputTag.value = id;
}

function initTaskPreview(modal) {
  const inputTitle = modal.querySelector('input[name="title"]');
  inputTitle.addEventListener('keyup', (event) => {
    handleTaskTitleInput(event, modal);
  });

  const colorButtons = document.querySelectorAll('[data-color]');
  colorButtons.forEach((colorButton) => {
    colorButton.addEventListener('click', (event) => {
      onColorButtonClicked(event, modal);
    });
  });
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

    initTaskPreview(modalElement);

    addModal = new Modal(modalElement);
    addModal.show();
  }
}

function openEditModal(info) {
  const modalElement = document.querySelector('[data-component="modal-edit-task"]');
  const eventDom = document
    .querySelector('[data-component="task-container"]')
    .querySelector(`[data-id="${info.event.id}"]`)

  if (modalElement && eventDom) {
    const titleInput = modalElement.querySelector('input[name="title"]');
    titleInput.value = info.event.title;

    const statusSwitch = modalElement.querySelector('input[name="status"]');
    statusSwitch.checked = parseInt(eventDom.dataset.status, 10) === 1;

    editDatepicker.setDate(info.event.start);

    const form = modalElement.querySelector('form');
    form.dataset.id = info.event.id;
    form.addEventListener('submit', onPatchSubmit);

    const deleteButton = modalElement.querySelector('[data-component="task-delete"]');
    deleteButton.addEventListener('click', onDelete);

    const taskPreview = modalElement.querySelector('[data-component="task-preview"]');
    taskPreview.innerText = info.event.title;
    taskPreview.classList.value = getClassListForTaskPreview(eventDom.dataset.color);

    const tagInput = modalElement.querySelector('input[name="tag"]');
    tagInput.value = eventDom.dataset.tag;

    initTaskPreview(modalElement);

    editModal = new Modal(modalElement);
    editModal.show();
  }
}

function getEventsFromDom() {
  let events = [];
  const eventsDom = document.querySelectorAll('[data-component="task-event"]');
  eventsDom.forEach((eventDom) => {
    const { id, title, date, color, status } = eventDom.dataset;
    events.push(getEvent(id, title, date, color, status));
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
      left: 'title',
      right: 'prev,next,dayGridDay,dayGridWeek'
    },
    events: getEventsFromDom(),
    dateClick: (d) => {
      openAddModal(d)
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
      const datepickerOptions = {format: 'dd/mm/yyyy'};
      editDatepicker = initDatepicker(datepickerElement, datepickerOptions);
    }
  }
}

function init() {
  const calendar = document.querySelector('[data-component="calendar"]');
  if (calendar) {
    initCalendar(calendar);
  }

  initEditModal();

  const tagsModal = document.querySelector('[data-component="modal-tags"]');
  if (tagsModal) {
    initTagController();
  }
}

export default init;
