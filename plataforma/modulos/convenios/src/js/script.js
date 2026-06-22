document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  /* ── Modal de login ─────────────────────────────────── */
  var loginModalOverlay = document.getElementById('loginModalOverlay');
  var closeLoginModal   = document.getElementById('closeLoginModal');
  var togglePassword    = document.getElementById('togglePassword');
  var loginForm         = document.getElementById('loginForm');

  function lockScroll() {
    var sw = window.innerWidth - document.documentElement.clientWidth;
    document.body.style.paddingRight = sw + 'px';
    document.body.style.overflow = 'hidden';
  }
  function unlockScroll() {
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';
  }

  function openLoginModal() {
    lockScroll();
    loginModalOverlay.classList.add('active');
    loginModalOverlay.setAttribute('aria-hidden', 'false');
    var firstInput = loginModalOverlay.querySelector('input');
    if (firstInput) firstInput.focus();
  }
  function closeLoginModalFn() {
    loginModalOverlay.classList.remove('active');
    loginModalOverlay.setAttribute('aria-hidden', 'true');
    unlockScroll();
    /* Devolver foco al botón que abrió el modal */
    var opener = document.querySelector('.login-link');
    if (opener) opener.focus();
  }

  /* Activar al cargar si había error (modal ya abierto desde PHP) */
  if (loginModalOverlay && loginModalOverlay.classList.contains('active')) {
    lockScroll();
  }

  document.querySelectorAll('.login-link').forEach(function (link) {
    link.addEventListener('click', function (e) {
      e.preventDefault();
      openLoginModal();
    });
  });

  if (closeLoginModal) closeLoginModal.addEventListener('click', closeLoginModalFn);

  if (loginModalOverlay) {
    loginModalOverlay.addEventListener('click', function (e) {
      if (e.target === loginModalOverlay) closeLoginModalFn();
    });
  }

  /* Toggle visibilidad contraseña */
  if (togglePassword) {
    togglePassword.addEventListener('click', function () {
      var passwordField = document.getElementById('conv-password');
      var isText = passwordField.getAttribute('type') === 'text';
      passwordField.setAttribute('type', isText ? 'password' : 'text');
      togglePassword.setAttribute('aria-label', isText ? 'Mostrar contraseña' : 'Ocultar contraseña');
    });
  }

  /* Validación básica login antes de enviar */
  if (loginForm) {
    loginForm.addEventListener('submit', function (e) {
      var emailVal    = (document.getElementById('conv-email') || {}).value || '';
      var passwordVal = (document.getElementById('conv-password') || {}).value || '';
      if (!emailVal.trim() || !passwordVal) {
        e.preventDefault();
        var errEl = loginForm.querySelector('.login-error');
        if (!errEl) {
          errEl = document.createElement('p');
          errEl.className = 'login-error';
          errEl.setAttribute('role', 'alert');
          loginForm.prepend(errEl);
        }
        errEl.textContent = 'Por favor completa todos los campos.';
      }
    });
  }

  /* ── Modal "Sugerir empresa" ─────────────────────── */
  var suggestModalOverlay  = document.getElementById('suggestModalOverlay');
  var openSuggestModalBtn  = document.getElementById('openSuggestModalBtn');
  var closeSuggestModalBtn = document.getElementById('closeSuggestModal');
  var suggestForm          = document.getElementById('suggestForm');
  var suggestFormError     = document.getElementById('suggestFormError');

  function openSuggestModal() {
    lockScroll();
    suggestModalOverlay.classList.add('active');
    suggestModalOverlay.setAttribute('aria-hidden', 'false');
    if (openSuggestModalBtn) openSuggestModalBtn.setAttribute('aria-expanded', 'true');
    var firstInput = suggestModalOverlay.querySelector('input');
    if (firstInput) firstInput.focus();
  }

  function closeSuggestModal() {
    suggestModalOverlay.classList.remove('active');
    suggestModalOverlay.setAttribute('aria-hidden', 'true');
    if (openSuggestModalBtn) openSuggestModalBtn.setAttribute('aria-expanded', 'false');
    if (suggestFormError) {
      suggestFormError.style.display = 'none';
      suggestFormError.textContent   = '';
    }
    unlockScroll();
    if (openSuggestModalBtn) openSuggestModalBtn.focus();
  }

  if (openSuggestModalBtn) openSuggestModalBtn.addEventListener('click', openSuggestModal);
  if (closeSuggestModalBtn) closeSuggestModalBtn.addEventListener('click', closeSuggestModal);
  if (suggestModalOverlay) {
    suggestModalOverlay.addEventListener('click', function (e) {
      if (e.target === suggestModalOverlay) closeSuggestModal();
    });
  }

  /* ESC cierra cualquier modal abierto */
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      if (loginModalOverlay && loginModalOverlay.classList.contains('active')) closeLoginModalFn();
      if (suggestModalOverlay && suggestModalOverlay.classList.contains('active')) closeSuggestModal();
    }
  });

  /* Submit sugerencia empresa */
  if (suggestForm) {
    suggestForm.addEventListener('submit', function (e) {
      e.preventDefault();
      var submitBtn = suggestForm.querySelector('button[type="submit"]');
      var originalText = submitBtn.textContent;
      submitBtn.disabled = true;
      submitBtn.textContent = 'Enviando…';

      if (suggestFormError) {
        suggestFormError.style.display = 'none';
        suggestFormError.textContent   = '';
      }

      var _base = (document.querySelector('meta[name="plataforma-url"]')?.content || '/plataforma');
      fetch(_base + '/modulos/convenios/src/pages/sugerir_empresa.php', {
        method: 'POST',
        body: new FormData(suggestForm)
      })
      .then(function (res) {
        if (!res.ok) throw new Error('HTTP ' + res.status);
        return res.json();
      })
      .then(function (data) {
        if (data.success) {
          closeSuggestModal();
          suggestForm.reset();
          Swal.fire({
            icon: 'success',
            title: 'Sugerencia enviada',
            text: data.message || 'Hemos recibido los datos correctamente.',
            confirmButtonText: 'Aceptar',
            confirmButtonColor: '#32129a',
            timer: 4000, timerProgressBar: true
          });
        } else {
          if (suggestFormError) {
            suggestFormError.textContent   = data.message || 'Ocurrió un error al procesar tu solicitud.';
            suggestFormError.style.display = 'block';
          }
        }
      })
      .catch(function () {
        closeSuggestModal();
        Swal.fire({
          icon: 'warning',
          title: 'Error de conexión',
          text: 'No logramos conectar con el servidor. Verifica tu conexión e inténtalo de nuevo.',
          confirmButtonText: 'Entendido',
          confirmButtonColor: '#32129a'
        });
      })
      .finally(function () {
        submitBtn.disabled    = false;
        submitBtn.textContent = originalText;
      });
    });
  }
});
