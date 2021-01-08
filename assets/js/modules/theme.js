import { Tooltip, Popover, Dropdown, Alert } from 'bootstrap';
import Pace from 'pace-js';

function initPace() {
  const paceOptions = {
    restartOnRequestAfter: true,
  };
  Pace.start(paceOptions);
}

function initBootstrapComponents() {
  // init tooltips
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  const tooltipList = tooltipTriggerList.map((tooltipTriggerEl) => {
    return new Tooltip(tooltipTriggerEl);
  });

  // init popovers
  const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
  const popoverList = popoverTriggerList.map((popoverTriggerEl) => {
    return new Popover(popoverTriggerEl);
  });

  // init dropdowns
  const dropdownTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="dropdown"]'));
  const dropdownList = dropdownTriggerList.map((dropdownTriggerEl) => {
    return new Dropdown(dropdownTriggerEl);
  });

  // init alert
  const alertTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="alert"]'));
  const alertList = alertTriggerList.map((alertTriggerEl) => {
    return new Alert(alertTriggerEl);
  });
}

function handleResponsiveSidePanel() {
  const sidePanel = document.getElementById('app-sidepanel');

  if(window.innerWidth >= 1200) {
    // if larger
    sidePanel.classList.remove('sidepanel-hidden');
    sidePanel.classList.add('sidepanel-visible');
  } else {
    // if smaller
    sidePanel.classList.remove('sidepanel-visible');
    sidePanel.classList.add('sidepanel-hidden');
  }
}

function initResponsiveSidePanel() {
  const sidePanelToggler = document.getElementById('sidepanel-toggler');
  const sidePanel = document.getElementById('app-sidepanel');
  const sidePanelDrop = document.getElementById('sidepanel-drop');
  const sidePanelClose = document.getElementById('sidepanel-close');

  handleResponsiveSidePanel();
  window.addEventListener('resive', handleResponsiveSidePanel);

  sidePanelToggler.addEventListener('click', () => {
    if (sidePanel.classList.contains('sidepanel-visible')) {
      sidePanel.classList.remove('sidepanel-visible');
      sidePanel.classList.add('sidepanel-hidden');
    } else {
      sidePanel.classList.remove('sidepanel-hidden');
      sidePanel.classList.add('sidepanel-visible');
    }
  });

  sidePanelClose.addEventListener('click', (e) => {
    e.preventDefault();
    sidePanelToggler.click();
  });

  sidePanelDrop.addEventListener('click', (e) => {
    sidePanelToggler.click();
  });
}

function initMobileSearch() {
  const searchMobileTrigger = document.querySelector('.search-mobile-trigger');
  const searchBox = document.querySelector('.app-search-box');

  searchMobileTrigger.addEventListener('click', () => {
    searchBox.classList.toggle('is-visible');
    let searchMobileTriggerIcon = document.querySelector('.search-mobile-trigger-icon');
    if (searchMobileTriggerIcon.classList.contains('fa-search')) {
      searchMobileTriggerIcon.classList.remove('fa-search');
      searchMobileTriggerIcon.classList.add('fa-times');
    } else {
      searchMobileTriggerIcon.classList.remove('fa-times');
      searchMobileTriggerIcon.classList.add('fa-search');
    }
  });
}

function init() {
  initResponsiveSidePanel();
  initMobileSearch();
  initBootstrapComponents();
  initPace();
}

export default init;
