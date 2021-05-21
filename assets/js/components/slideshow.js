function plusSlides(slideshowEl, number) {
  let slideIndex = parseInt(slideshowEl.dataset.index);
  slideIndex += number;
  slideshowEl.dataset.index = slideIndex.toString();

  showSlides(slideshowEl, slideIndex);
}

function currentSlide(slideshowEl, number) {
  slideshowEl.dataset.index = number.toString();

  showSlides(slideshowEl, number);
}

function showSlides(slideshowEl, number) {
  const component = slideshowEl.parentNode;
  const slides = slideshowEl.querySelectorAll('.slideshow-item');
  const dots = component.querySelectorAll('.slideshow-dot');

  let slideIndex = number;
  if (number > slides.length) {
    slideIndex = 1
  }

  if (number < 1) {
    slideIndex = slides.length
  }

  slideshowEl.dataset.index = slideIndex.toString();

  slides.forEach((item) => {
    item.style.display = 'none';
  })

  dots.forEach((dot) => {
    if (dot.classList.contains('active')) {
      dot.classList.remove('active');
    }
  })

  slides.item(slideIndex - 1).style.display = 'block';
  dots.item(slideIndex - 1).className += ' active';
}

function initSlideshow(slideshowComponent) {
  const slideshowEl = slideshowComponent.querySelector('.slideshow-container');
  slideshowEl.dataset.index = 1;
  showSlides(slideshowEl, 1);

  const steps = slideshowEl.querySelectorAll('.slideshow-step');
  steps.forEach((step) => {
    step.addEventListener('click', () => {
      plusSlides(slideshowEl, parseInt(step.dataset.number));
    });
  })

  const dots = slideshowComponent.querySelectorAll('.slideshow-dot');
  dots.forEach((dot) => {
    dot.addEventListener('click', () => {
      currentSlide(slideshowEl, parseInt(dot.dataset.number));
    });
  });
}

function init() {
  const slideshows = document.querySelectorAll('[data-component="slideshow"]');
  slideshows.forEach((slideshow) => {
    initSlideshow(slideshow);
  });
}

export default init;
