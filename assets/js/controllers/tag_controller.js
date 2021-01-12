import {Modal} from "bootstrap";

let typingTimer = null;
const doneTypingInterval = 1000;

function getBadgeClassList(color) {
  return `badge rounded-pill bg-${color} w-auto mr-1`;
}

function addBadge(name, color) {
  const span = document.createElement('span');
  span.classList.value = getBadgeClassList(color);
  span.innerText = name;

  const tagsContainer = document.querySelector('[data-component="tags-container"]');
  if (tagsContainer) {
    tagsContainer.appendChild(span);
  }
}

function postReady(event) {
  const httpRequest = event.currentTarget;
  if (httpRequest.readyState === 4) {
    const data = JSON.parse(httpRequest.response);
    const modalTags = document.querySelector('[data-component="modal-tags"]');

    if (httpRequest.status === 201) {
      const form = modalTags.querySelector('form');
      form.reset();

      const tagPreview = document.querySelector('[data-component="tag-preview"]');
      tagPreview.innerText = '';

      addBadge(data.name, data.color);
    }

    if (httpRequest.status === 400) {
      const alert = modalTags.querySelector('[data-component="error-message"]');
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

  httpRequest.send(JSON.stringify(data));
}

function handleTagNameInput(event) {
  const input = event.currentTarget;
  const tagPreview = document.querySelector('[data-component="tag-preview"]');

  clearTimeout(typingTimer);
  if (input.value.trim() !== '') {
    typingTimer = setTimeout(() => {
      tagPreview.innerText = input.value;
    }, doneTypingInterval);
  }
}

function onColorButtonClicked(event) {
  const button = event.currentTarget;
  const {color} = button.dataset;

  const tagPreview = document.querySelector('[data-component="tag-preview"]');
  tagPreview.classList.value = getBadgeClassList(color);

  const inputColor = document.querySelector('input[name="color"]');
  inputColor.value = color;
}

function onSubmit(event) {
  event.preventDefault();
  console.log('submit');
  const form = event.target;
  const formData = new FormData(form);

  let data = {};
  formData.forEach((value, key) => {
    data[key] = value;
  });
  console.log(data);
  post(form.action, data);
}

function initTagsModalForm() {
  const modalElement = document.querySelector('[data-component="modal-tags"]');
  if (modalElement) {
    const inputName = modalElement.querySelector('input[name="name"]');
    inputName.addEventListener('keyup', handleTagNameInput);

    const colorButtons = document.querySelectorAll('[data-color]');
    colorButtons.forEach((colorButton) => {
      colorButton.addEventListener('click', onColorButtonClicked);
    });

    const form = modalElement.querySelector('form');
    form.addEventListener('submit', onSubmit);
  }
}

function openTagsModal() {
  const modalElement = document.querySelector('[data-component="modal-tags"]');
  if (modalElement) {
    const tagsModal = new Modal(modalElement);
    tagsModal.show();
  }
}

function initTagsModal() {
  const tagsModalButton = document.querySelector('[data-component="open-modal-tags"]');
  if (tagsModalButton) {
    tagsModalButton.addEventListener('click', openTagsModal);

    initTagsModalForm();
  }
}

function init() {
  initTagsModal();
}

export default init;
