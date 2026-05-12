document.addEventListener('DOMContentLoaded', function () {
    var menuBtn   = document.getElementById('menuBtn');
    var menuPanel = document.getElementById('menuPanel');
    var menuIcon  = menuBtn.querySelector('img');

    menuBtn.addEventListener('click', function () {
        var isActive = menuPanel.classList.toggle('active');
        menuBtn.classList.toggle('active');
        menuBtn.setAttribute('aria-label', isActive ? 'Cerrar menú de navegación' : 'Abrir menú de navegación');
        menuIcon.src = isActive
            ? 'assets/images/logo/close-svgrepo-com.svg'
            : 'assets/images/logo/menu-svgrepo-com.svg';
    });
});
