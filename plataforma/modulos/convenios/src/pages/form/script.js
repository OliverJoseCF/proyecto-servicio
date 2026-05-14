document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  var logoInput      = document.getElementById('cv-logo');
  var logoPreview    = document.getElementById('logoPreview');
  var logoName       = document.getElementById('logoName');
  var step1          = document.getElementById('step1');
  var step2          = document.getElementById('step2');
  var step1Indicator = document.getElementById('step1Indicator');
  var step2Indicator = document.getElementById('step2Indicator');
  var continueBtn    = document.getElementById('continueBtn');
  var backBtn        = document.getElementById('backBtn');
  var stepAnnounce   = document.getElementById('step-announce');

  /* ── Vista previa del logo ── */
  if (logoInput) {
    logoInput.addEventListener('change', function (e) {
      if (e.target.files.length > 0) {
        var file = e.target.files[0];
        if (logoName) logoName.textContent = file.name;
        var reader = new FileReader();
        reader.onload = function (ev) {
          var img = document.createElement('img');
          img.src = ev.target.result;
          img.style.cssText = 'width:100%;height:100%;object-fit:contain;';
          img.alt = 'Vista previa del logo';
          logoPreview.innerHTML = '';
          logoPreview.appendChild(img);
          logoPreview.style.display = 'block';
        };
        reader.readAsDataURL(file);
      }
    });
  }

  /* ── Avanzar al Paso 2 ── */
  if (continueBtn) {
    continueBtn.addEventListener('click', function () {
      var step1Fields = document.querySelectorAll('#step1 input[required], #step1 select[required]');
      var isValid = true;

      step1Fields.forEach(function (field) {
        /* Validar nativo + vacío */
        if (!field.value.trim() || !field.checkValidity()) {
          isValid = false;
          field.classList.add('border-red-500');
          field.setAttribute('aria-invalid', 'true');
        } else {
          field.classList.remove('border-red-500');
          field.removeAttribute('aria-invalid');
        }
      });

      if (isValid) {
        step1.hidden = true;
        step2.hidden = false;
        step2.classList.add('step-transition');

        step1Indicator.classList.remove('active');
        step1Indicator.classList.add('completed');
        step1Indicator.setAttribute('aria-selected', 'false');
        step2Indicator.classList.add('active');
        step2Indicator.setAttribute('aria-selected', 'true');

        if (stepAnnounce) stepAnnounce.textContent = 'Paso 2 de 2: Información adicional';

        /* Mover foco al primer campo del paso 2 */
        var firstStep2Field = step2.querySelector('input, select');
        if (firstStep2Field) firstStep2Field.focus();
      } else {
        /* Mover foco al primer campo inválido */
        var firstInvalid = step1.querySelector('[aria-invalid="true"]');
        if (firstInvalid) firstInvalid.focus();
      }
    });
  }

  /* ── Volver al Paso 1 ── */
  if (backBtn) {
    backBtn.addEventListener('click', function () {
      step2.hidden = true;
      step1.hidden = false;
      step1.classList.add('step-transition');

      step2Indicator.classList.remove('active');
      step2Indicator.setAttribute('aria-selected', 'false');
      step1Indicator.classList.remove('completed');
      step1Indicator.classList.add('active');
      step1Indicator.setAttribute('aria-selected', 'true');

      if (stepAnnounce) stepAnnounce.textContent = 'Paso 1 de 2: Información básica';

      /* Devolver foco al primer campo del paso 1 */
      var firstStep1Field = step1.querySelector('input, select');
      if (firstStep1Field) firstStep1Field.focus();
    });
  }
});
