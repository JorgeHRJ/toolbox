function init() {
  const colorPicker = document.querySelector('[data-component="color-picker"]');
  if (colorPicker) {
    const { target } = colorPicker.dataset;
    const targetInput = document.querySelector(`#${target}`);
    if (targetInput) {
      const buttons = colorPicker.querySelectorAll('button[data-color]');
      buttons.forEach((button) => {
        button.addEventListener('click', () => {
          const { color } = button.dataset;
          targetInput.value = color;
        });
      });
    }
  }
}

export default init;
