// js/modal.js  —  Lógica del modal de horarios (compartida)

(function () {
    const modal      = document.getElementById('modalHorario');
    const contentDiv = document.getElementById('modalContent');
    const closeBtn   = modal?.querySelector('.modal-close');
    const overlay    = modal?.querySelector('.modal-overlay');

    function openModal(url) {
        contentDiv.innerHTML = '';
        if (/\.pdf$/i.test(url)) {
            const obj   = document.createElement('object');
            obj.data    = url;
            obj.type    = 'application/pdf';
            obj.width   = '100%';
            obj.height  = '600px';
            contentDiv.appendChild(obj);
        } else {
            const img   = document.createElement('img');
            img.src     = url;
            img.alt     = 'Horario del maestro';
            contentDiv.appendChild(img);
        }
        modal.classList.add('is-open');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        modal.classList.remove('is-open');
        contentDiv.innerHTML = '';
        document.body.style.overflow = '';
    }

    document.querySelectorAll('.open-modal').forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            openModal(this.href);
        });
    });

    closeBtn?.addEventListener('click', closeModal);
    overlay?.addEventListener('click', closeModal);

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeModal();
    });
})();
