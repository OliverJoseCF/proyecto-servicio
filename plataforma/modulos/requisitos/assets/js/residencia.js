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
    try { localStorage.setItem('residenciaProgress', JSON.stringify(state)); } catch (e) {}

    for (var i = 0; i < checkboxes.length; i++) {
      checkboxes[i].parentElement.classList.toggle('completed', checkboxes[i].checked);
    }
  }

  for (var i = 0; i < checkboxes.length; i++) {
    checkboxes[i].addEventListener('change', updateProgress);
  }

  function calculateCredits() {
    var total   = parseFloat(document.getElementById('totalCredits').value) || 0;
    var current = parseFloat(document.getElementById('currentCredits').value) || 0;
    var result  = document.getElementById('creditResult');
    if (!result) return;
    if (total > 0 && current >= 0) {
      var pct       = Math.round((current / total) * 100);
      var needed70  = Math.round(total * 0.7);
      var remaining = needed70 - current;
      var msg;
      if (pct >= 80) {
        msg = '¡Felicidades! Ya puedes iniciar tu residencia (' + pct + '% de créditos)';
        result.style.background = '#ecfdf5';
        result.style.color      = '#065f46';
        result.style.border     = '1px solid #16a34a';
      } else if (pct >= 70) {
        msg = '¡Casi listo! Ya puedes comenzar el proceso (' + pct + '%)';
        result.style.background = '#fffbeb';
        result.style.color      = '#92400e';
        result.style.border     = '1px solid #f59e0b';
      } else {
        msg = 'Aún no — tienes ' + pct + '%, te faltan ' + (remaining > 0 ? remaining : 0) + ' créditos para el 70%';
        result.style.background = '#fef2f2';
        result.style.color      = '#991b1b';
        result.style.border     = '1px solid #dc2626';
      }
      result.textContent = msg;
    } else {
      result.textContent = 'Ingresa tus créditos para calcular';
      result.style.cssText = '';
    }
  }
  window.calculateCredits = calculateCredits;

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
    var saved = localStorage.getItem('residenciaProgress');
    if (saved) {
      var state = JSON.parse(saved);
      for (var i = 0; i < checkboxes.length; i++) {
        if (state[i] !== undefined) checkboxes[i].checked = state[i];
      }
      updateProgress();
    }
  } catch (e) {}
})();
