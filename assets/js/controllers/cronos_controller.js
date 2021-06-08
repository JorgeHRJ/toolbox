import { Calendar } from '@fullcalendar/core';
import { Modal } from 'bootstrap';
import interactionPlugin from '@fullcalendar/interaction';
import dayGridPlugin from '@fullcalendar/daygrid';
import esLocale from '@fullcalendar/core/locales/es';
import { initChoiceSimple } from '../components/choices';
import { initDatetimePicker } from '../components/datetimepicker';
import { initMarkdownEditor } from '../components/markdown_editor';
import { notify } from '../components/notyf';

let calendar = null;
let modal = null;

function getClassNameForEvent(color) {
  return `badge bg-${color} border border-light rounded p-2 w-100 fc-cronos-event`;
}

function getDiff(startAt, endAt) {
  const diffMs = (new Date(endAt)) - (new Date(startAt));
  const diffDays = Math.floor(diffMs / 86400000);
  const diffHrs = Math.floor((diffMs % 86400000) / 3600000);
  const diffMins = Math.round(((diffMs % 86400000) % 3600000) / 60000);

  let diff = '';
  if (diffDays > 0) {
    diff += `${diffDays}d`;
  }

  if (diffHrs > 0) {
    diff += ` ${diffHrs}h`;
  }

  if (diffMins > 0) {
    diff += ` ${diffMins}m`;
  }

  return diff;
}

function getEvent(id, title, startAt, endAt, color) {
  return {
    id: id,
    title: `${title} (${getDiff(startAt, endAt)})`,
    start: startAt,
    end: endAt,
    className: getClassNameForEvent(color),
    allDay: false,
    draggable: true
  };
}

function getEventsFromDom() {
  let events = [];
  const eventsDom = document.querySelectorAll('[data-component="time-event"]');
  eventsDom.forEach((eventDom) => {
    const { id, title, start, end, color } = eventDom.dataset;
    events.push(getEvent(id, title, start, end, color));
  });

  return events;
}

function onRemoveTimeReady(event) {
  const httpRequest = event.currentTarget;
  if (httpRequest.readyState === 4) {
    if (httpRequest.status === 200) {
      const data = JSON.parse(httpRequest.response);
      const eventCalendar = calendar.getEventById(data.id);
      if (eventCalendar) {
        eventCalendar.remove();
      }

      if (modal !== null) {
        modal.hide();
        modal = null;
      }

      notify('¡Tiempo eliminado!')
    } else {
      notify('Hubo algún error al intentar eliminar el tiempo', 'error');
    }
  }
}

function sendFormReady(event) {
  const httpRequest = event.currentTarget;
  if (httpRequest.readyState === 4) {
    const data = JSON.parse(httpRequest.response);
    if (httpRequest.status === 200) {
      const eventCalendar = calendar.getEventById(data.id.toString());
      if (eventCalendar) {
        eventCalendar.setProp('title', data.title);
        eventCalendar.setStart(data.startAt);
        eventCalendar.setEnd(data.endAt);
        eventCalendar.setProp('classNames', getClassNameForEvent(data.client.color).split(' '));
      } else {
        const event = getEvent(data.id, data.title, data.startAt, data.endAt, data.client.color);
        calendar.addEvent(event);
      }

      if (modal !== null) {
        modal.hide();
        modal = null;
      }

      notify('¡Tiempo procesado!', 'success');
    } else {
      notify('Hubo algún error a la hora de procesar el tiempo.', 'error');
    }
  }
}

function removeTime(event) {
  const button = event.currentTarget;
  const { url } = button.dataset;

  const httpRequest = new XMLHttpRequest();
  httpRequest.onreadystatechange = onRemoveTimeReady;
  httpRequest.open('DELETE', url);
  httpRequest.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
  httpRequest.setRequestHeader('Content-Type', 'application/json');

  httpRequest.send();
}

function onSendForm(event, modalEl) {
  const button = event.currentTarget;
  const { url, method } = button.dataset;

  const form = modalEl.querySelector('form');

  // validation
  const startAtInput = form.querySelector('input[name="crono_time[startAt]"]');
  const endAtInput = form.querySelector('input[name="crono_time[endAt]"]');
  if (startAtInput && endAtInput) {
    const startAt = new Date(startAtInput.value);
    const endAt = new Date(endAtInput.value);
    if (startAt.getTime() === endAt.getTime()) {
      notify('Las fechas no deben coincidir', 'error');
      return;
    }

    if (startAt.getTime() > endAt.getTime()) {
      notify('Las fecha de comienzo no puede ser mayor que la fecha final', 'error');
      return;
    }
  }

  // prepare and send form
  const formData = new FormData(form);
  const object = {};
  formData.forEach((value, key) => {
    const newKey = key.match(/[^[\]]+(?=])/g)[0];
    object[newKey] = value;
  });
  const data = JSON.stringify(object);

  const httpRequest = new XMLHttpRequest();
  httpRequest.onreadystatechange = sendFormReady;
  httpRequest.open(method, url);
  httpRequest.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
  httpRequest.setRequestHeader('Content-Type', 'application/json');

  httpRequest.send(data);
}

function initModalComponents(modalEl, date) {
  const sendButton = modalEl.querySelector('[data-component="send-form"]');
  sendButton.addEventListener('click', (event) => {
    onSendForm(event, modalEl);
  });

  const removeButton = modalEl.querySelector('[data-component="remove-time"]');
  if (removeButton) {
    removeButton.addEventListener('click', removeTime);
  }

  const choicesEl = modalEl.querySelectorAll('[data-component="choices"][data-type="simple"]');
  choicesEl.forEach((choiceEl) => initChoiceSimple(choiceEl));

  const datepickersEl = modalEl.querySelectorAll('[data-component="datetimepicker"]');
  datepickersEl.forEach((datepickerEl) => {
    if (!removeButton) {
      datepickerEl.value = date.date.toLocaleDateString('es-ES', { year: 'numeric', month: 'numeric', day: 'numeric'});
    }
    initDatetimePicker(datepickerEl);
  });

  const editor = modalEl.querySelector('[data-component="markdown-editor"]');
  if (editor) {
    initMarkdownEditor(editor);
  }

  const dateTitle = modalEl.querySelector('.modal-title');
  const dateObj = removeButton ? date : date.date;
  dateTitle.innerText = dateObj.toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' });
}

function onModalFormReady(event, modalEl, date) {
  const httpRequest = event.currentTarget;
  if (httpRequest.readyState === 4) {
    if (httpRequest.status === 200) {
      const modalFormContainer = modalEl.querySelector('[data-component="time-form"]');
      const sendButton = modalEl.querySelector('[data-component="send-form"]');
      const removeButton = modalEl.querySelector('[data-component="remove-time"]');

      const responseData = JSON.parse(httpRequest.response);
      modalFormContainer.innerHTML = responseData.html;
      sendButton.dataset.url = responseData.url;
      sendButton.dataset.method = responseData.method;

      if (removeButton) {
        removeButton.dataset.url = responseData.remove_url;
      }

      initModalComponents(modalEl, date);
      modal = new Modal(modalEl);

      modal.show();
    }
  }
}

function openEditModal(info) {
  const modalEl = document.querySelector('[data-component="modal-time"][data-action="edit"]');
  if (modalEl) {
    const modalFormContainer = modalEl.querySelector('[data-component="time-form"]');
    if (modalFormContainer) {
      // reset modal
      modalFormContainer.innerHTML = '';
      modal = null;

      const url = `${modalEl.dataset.url}/${parseInt(info.event.id)}`;

      const httpRequest = new XMLHttpRequest();
      httpRequest.onreadystatechange = (event) => onModalFormReady(event, modalEl, info.event.start);
      httpRequest.open('GET', url);
      httpRequest.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

      httpRequest.send();
    }
  }
}

function openAddModal(date) {
  const modalEl = document.querySelector('[data-component="modal-time"][data-action="add"]');
  if (modalEl) {
    const modalFormContainer = modalEl.querySelector('[data-component="time-form"]');
    if (modalFormContainer) {
      // reset modal
      modalFormContainer.innerHTML = '';
      modal = null;

      // get form from backend
      const { url } = modalEl.dataset;
      const httpRequest = new XMLHttpRequest();
      httpRequest.onreadystatechange = (event) => onModalFormReady(event, modalEl, date);
      httpRequest.open('GET', url);
      httpRequest.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

      httpRequest.send();
    }
  }
}

function initCalendar(element) {
  calendar = new Calendar(element, {
    plugins: [ dayGridPlugin, interactionPlugin ],
    timeZone: 'UTC',
    locale: esLocale,
    initialView: 'dayGridWeek',
    themeSystem: 'bootstrap',
    selectable: true,
    editable: false,
    headerToolbar: {
      left: 'prev,next today',
      center: 'title',
      right: 'dayGridDay,dayGridWeek,dayGridMonth'
    },
    eventTimeFormat: {
      hour: '2-digit',
      minute: '2-digit',
      meridiem: false
    },
    displayEventTime: true,
    displayEventEnd: true,
    events: getEventsFromDom(),
    dateClick: (d) => {
      openAddModal(d);
    },
    eventClick: (info) => {
      openEditModal(info);
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
