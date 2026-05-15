(function () {
  'use strict';

  var bar        = document.getElementById('progressBar');
  var container  = document.getElementById('progressContainer');
  var checkboxes = document.querySelectorAll('.checklist-item input[type="checkbox"]');

  if (!bar || !checkboxes.length) return;

  function updateProgress() {
    var done = 0;
    for (var i = 0; i < checkboxes.length; i++) {
      if (checkboxes[i].checked) done++;
    }
    var pct = Math.round((done / checkboxes.length) * 100);
    bar.style.width = pct + '%';
    bar.textContent = pct + '%';
    if (container) container.setAttribute('aria-valuenow', pct);

    var state = [];
    for (var i = 0; i < checkboxes.length; i++) state[i] = checkboxes[i].checked;
    try { localStorage.setItem('servicioProgress', JSON.stringify(state)); } catch (e) {}

    for (var i = 0; i < checkboxes.length; i++) {
      checkboxes[i].parentElement.classList.toggle('completed', checkboxes[i].checked);
    }
  }

  for (var i = 0; i < checkboxes.length; i++) {
    checkboxes[i].addEventListener('change', updateProgress);
  }

  var faqBtns = document.querySelectorAll('.faq-question');
  for (var i = 0; i < faqBtns.length; i++) {
    faqBtns[i].addEventListener('click', (function (btn) {
      return function () {
        var item   = btn.parentElement;
        var answer = btn.nextElementSibling;
        var isOpen = item.classList.contains('active');
        item.classList.toggle('active', !isOpen);
        answer.classList.toggle('active', !isOpen);
        btn.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
      };
    })(faqBtns[i]));
  }

  try {
    var saved = localStorage.getItem('servicioProgress');
    if (saved) {
      var state = JSON.parse(saved);
      for (var i = 0; i < checkboxes.length; i++) {
        if (state[i] !== undefined) checkboxes[i].checked = state[i];
      }
      updateProgress();
    }
  } catch (e) {}
})();
