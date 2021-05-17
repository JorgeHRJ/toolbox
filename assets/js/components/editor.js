// https://github.com/editor-js/awesome-editorjs
import EditorJS from '@editorjs/editorjs';
import Header from '@editorjs/header';
import List from '@editorjs/list';
import Table from '@editorjs/table';
import Embed from '@editorjs/embed';

function getOptions(holder, data, placeholder = '', imageEndpoint = '') {
  return {
    holder: holder,
    data: data,
    placeholder: placeholder,
    logLevel: 'ERROR',
    tools: {
      header: {
        class: Header,
        inlineToolbar: true
      },
      list: {
        class: List,
        inlineToolbar: true
      },
      embed: {
        class: Embed,
        config: {
          services: {
            youtube: true,
            codepen: true,
            twitter: true
          }
        }
      },
      table: {
        class: Table,
      },
    },
    i18n: {
      toolNames: {
        Hyperlink: 'Link',
        tools: {
          hyperlink: {
            'Save': 'Guardar',
            'Select target': 'Target',
            'Select rel': 'Rel'
          }
        }
      },
    },
  };
}

function initEditor(textarea) {
  // create div to hold the editor
  const editorDiv = document.createElement('div');
  editorDiv.id = `editor-${textarea.id}`;
  editorDiv.classList.add('mt-2');
  editorDiv.dataset.target = `#${textarea.id}`;

  // add to the container
  textarea.parentElement.insertBefore(editorDiv, textarea.nextSibling);

  // initialize editor
  const data = textarea.value ? JSON.parse(textarea.value) : {};
  let placeholder = textarea.getAttribute('placeholder');
  if (!placeholder) {
    placeholder = '';
  }

  let base = getOptions(editorDiv, data, placeholder, textarea.dataset.uploadimage);
  const options = Object.assign(base, {
    onChange: (api) => {
      api.saver.save().then((data) => {
        textarea.value = JSON.stringify(data);
      });
    }
  })

  const editor = new EditorJS(options);
}

function init() {
  const editors = document.querySelectorAll('[data-component="editor"]');
  if (editors) {
    editors.forEach((editor) => {
      initEditor(editor);
    });
  }
}
export default init;
