import EasyMDE from 'easymde';

let imageData = null;

function initEditor(textarea) {
  const editor = new EasyMDE(
    {
      element: textarea,
      status: ['lines', 'words', 'cursor'],
      autosave: {
        enabled: false,
      },
      spellChecker: false,
      nativeSpellcheck: true,
      previewRender: false,
      autoDownloadFontAwesome: false,
      hideIcons: ['image'],
      toolbar: [
        'bold', 'italic', 'heading', '|', 'undo', 'redo', '|', 'code', 'quote', 'unordered-list', 'ordered-list',
        'link', 'preview', 'side-by-side',
      ],
    }
  );

  editor.codemirror.on('change', () => {
    textarea.value = editor.value();
  });
}

function init() {
  const editors = document.querySelectorAll('[data-component="markdown-editor"]');
  if (editors) {
    editors.forEach((editor) => {
      initEditor(editor);
    });
  }
}

export {
  init as initMarkdownEditorComponent,
  initEditor as initMarkdownEditor
};
