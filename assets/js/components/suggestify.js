let typingTimer;
let doneTypingInterval = 1000;

function closeLists(element) {
  const lists = document.querySelectorAll('.suggestify-items');
  if (lists && lists.length > 0) {
    lists.forEach((list) => {
      if (element !== list) {
        list.parentNode.removeChild(list);
      }
    });
  }
}

function buildList(items, input) {
  const list = document.createElement('div');
  list.setAttribute('class', 'suggestify-items');

  input.parentNode.appendChild(list);
  if (items.length > 0) {
    items.forEach((item) => {
      const itemDiv = document.createElement('div');
      itemDiv.dataset.url = input.dataset.callback.replace('_replace_', item.slug);
      itemDiv.innerHTML = `<strong>${item.name}</strong>`;
      itemDiv.addEventListener('click', () => {
        window.location.href = itemDiv.dataset.url;
      });
      list.appendChild(itemDiv);
    });
  } else {
    const itemDiv = document.createElement('div');
    itemDiv.innerHTML = `<strong>No se ha encontrado resultados</strong>`;
    list.appendChild(itemDiv);
  }
}

function suggestionsReady(event, input) {
  const httpRequest = event.currentTarget;
  if (httpRequest.readyState === 4) {
    if (httpRequest.status === 200) {
      const data = JSON.parse(httpRequest.response);
      buildList(data.suggestions, input);
    }
  }
}

function getSuggestions(input) {
    let { url } = input.dataset;
    if (!url) {
      return;
    }

    url = `${url}?q=${input.value}`;

    const httpRequest = new XMLHttpRequest();
    httpRequest.onreadystatechange = (event) => {
        suggestionsReady(event, input);
    };
    httpRequest.open('GET', url);
    httpRequest.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

    httpRequest.send();
}

function init() {
    const suggestInputs = document.querySelectorAll('[data-component="suggestify"]');
    suggestInputs.forEach((input) => {
        input.addEventListener('keyup', () => {
          clearTimeout(typingTimer);
          if (input.value) {
            typingTimer = setTimeout(() => {
                getSuggestions(input);
            }, doneTypingInterval);
          }
        })
    });

    document.addEventListener('click', (event) => {
      closeLists(event.target);
    })
}

export default init;
