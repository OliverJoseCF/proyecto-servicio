document.addEventListener('DOMContentLoaded', function () {
    const menuBtn   = document.getElementById('menuBtn');
    const menuPanel = document.getElementById('menuPanel');
    const menuIcon  = menuBtn.querySelector('img');

    menuBtn.addEventListener('click', function () {
        const isActive = menuPanel.classList.toggle('active');
        menuBtn.classList.toggle('active');
        menuBtn.setAttribute('aria-label', isActive ? 'Cerrar menú de navegación' : 'Abrir menú de navegación');
        menuIcon.src = isActive
            ? 'assets/images/logo/close-svgrepo-com.svg'
            : 'assets/images/logo/menu-svgrepo-com.svg';
    });

    // Modal de login
    const loginModalOverlay = document.getElementById('loginModalOverlay');
    const closeLoginModal   = document.getElementById('closeLoginModal');
    const togglePassword    = document.getElementById('togglePassword');
    const loginForm         = document.getElementById('loginForm');
    const siteHeader        = document.getElementById('siteHeader');

    function lockScroll() {
        const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
        document.body.style.paddingRight = scrollbarWidth + 'px';
        document.body.style.overflow = 'hidden';
        if (siteHeader) siteHeader.style.paddingRight = scrollbarWidth + 'px';
    }

    function unlockScroll() {
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
        if (siteHeader) siteHeader.style.paddingRight = '';
    }

    function openModal() {
        lockScroll();
        loginModalOverlay.classList.add('active');
        loginModalOverlay.setAttribute('aria-hidden', 'false');
        const firstInput = loginModalOverlay.querySelector('input');
        if (firstInput) firstInput.focus();
    }

    function closeModal() {
        loginModalOverlay.classList.remove('active');
        loginModalOverlay.setAttribute('aria-hidden', 'true');
        unlockScroll();
    }

    // Abrir modal con cualquier elemento .login-link
    document.querySelectorAll('.login-link').forEach(function (link) {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            menuPanel.classList.remove('active');
            menuBtn.classList.remove('active');
            menuBtn.setAttribute('aria-label', 'Abrir menú de navegación');
            menuIcon.src = 'assets/images/logo/menu-svgrepo-com.svg';
            openModal();
        });
    });

    closeLoginModal.addEventListener('click', closeModal);

    loginModalOverlay.addEventListener('click', function (e) {
        if (e.target === loginModalOverlay) closeModal();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            if (loginModalOverlay.classList.contains('active')) closeModal();
            if (suggestModalOverlay && suggestModalOverlay.classList.contains('active')) closeSuggestModal();
        }
    });

    if (togglePassword) {
        togglePassword.addEventListener('click', function () {
            const passwordField = document.getElementById('password');
            const isText = passwordField.getAttribute('type') === 'text';
            passwordField.setAttribute('type', isText ? 'password' : 'text');
            togglePassword.setAttribute('aria-label', isText ? 'Mostrar contraseña' : 'Ocultar contraseña');
        });
    }

    // Modal "Sugerir empresa"
    const suggestModalOverlay  = document.getElementById('suggestModalOverlay');
    const openSuggestModalBtn  = document.getElementById('openSuggestModalBtn');
    const closeSuggestModalBtn = document.getElementById('closeSuggestModal');
    const suggestForm          = document.getElementById('suggestForm');
    const suggestFormError     = document.getElementById('suggestFormError');

    function openSuggestModal() {
        lockScroll();
        suggestModalOverlay.classList.add('active');
        suggestModalOverlay.setAttribute('aria-hidden', 'false');
        openSuggestModalBtn.setAttribute('aria-expanded', 'true');
        const firstInput = suggestModalOverlay.querySelector('input');
        if (firstInput) firstInput.focus();
    }

    function closeSuggestModal() {
        suggestModalOverlay.classList.remove('active');
        suggestModalOverlay.setAttribute('aria-hidden', 'true');
        openSuggestModalBtn.setAttribute('aria-expanded', 'false');
        if (suggestFormError) {
            suggestFormError.style.display = 'none';
            suggestFormError.textContent   = '';
        }
        unlockScroll();
    }

    if (openSuggestModalBtn) openSuggestModalBtn.addEventListener('click', openSuggestModal);
    if (closeSuggestModalBtn) closeSuggestModalBtn.addEventListener('click', closeSuggestModal);

    if (suggestModalOverlay) {
        suggestModalOverlay.addEventListener('click', function (e) {
            if (e.target === suggestModalOverlay) closeSuggestModal();
        });
    }

    if (suggestForm) {
        suggestForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const submitBtn  = suggestForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.disabled    = true;
            submitBtn.textContent = 'Enviando…';

            if (suggestFormError) {
                suggestFormError.style.display = 'none';
                suggestFormError.textContent   = '';
            }

            fetch('src/pages/sugerir_empresa.php', {
                method: 'POST',
                body: new FormData(suggestForm),
            })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (data.success) {
                    closeSuggestModal();
                    suggestForm.reset();
                    Swal.fire({
                        icon: 'success',
                        title: 'Sugerencia enviada',
                        text: data.message || 'Hemos recibido los datos correctamente.',
                        confirmButtonText: 'Aceptar',
                        confirmButtonColor: '#003087',
                        background: '#ffffff',
                        timer: 4000,
                        timerProgressBar: true,
                    });
                } else {
                    // Mostrar error dentro del modal (no cerrarlo)
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
                    text: 'No logramos conectar con el servidor. Por favor, verifica tu conexión e inténtalo de nuevo.',
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#e0a800',
                    background: '#ffffff',
                });
            })
            .finally(function () {
                submitBtn.disabled    = false;
                submitBtn.textContent = originalText;
            });
        });
    }

    // Validación básica del formulario de login antes de enviar al servidor
    if (loginForm) {
        loginForm.addEventListener('submit', function (e) {
            const emailVal    = document.getElementById('email').value.trim();
            const passwordVal = document.getElementById('password').value;
            if (!emailVal || !passwordVal) {
                e.preventDefault();
                // Mostrar error inline en vez de alert()
                let errEl = loginForm.querySelector('.login-error');
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
});
