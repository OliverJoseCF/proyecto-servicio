<?php
$tsj_module     = 'visitantes';
$tsj_title      = 'COORDINADOR/A “ISC”';
$tsj_extra_css  = ['style.css'];

require_once __DIR__ . '/../../shared/header.php';
?>


    <h1>COORDINADOR/A “ISC”</h1>

    <div class="docentes">
        <h2>COORDINADOR</h2>
        <ul id="coordinadorList">
            <li contenteditable="true">claudio.castillo@chapala.tecmm.edu.mx</li>
        </ul>
    </div>

    <a href="index.php" class="top-right">
        <img src="imagenes/casa.png" alt="Ir a inicio" style="width: 80px; height: auto;">
    </a>

    <script>
        const coordinadorList = document.getElementById("coordinadorList");

        // Cargar desde localStorage si existe
        const guardado = JSON.parse(localStorage.getItem("coordinador"));
        if (guardado) {
            coordinadorList.innerHTML = "";
            guardado.forEach(correo => {
                const li = document.createElement("li");
                li.textContent = correo;
                li.contentEditable = "true";
                coordinadorList.appendChild(li);
            });
        }

        // Guardar automáticamente cambios
        coordinadorList.addEventListener("input", () => {
            const datos = Array.from(coordinadorList.children).map(li => li.textContent);
            localStorage.setItem("coordinador", JSON.stringify(datos));
        });
    </script>


<?php require_once __DIR__ . '/../../shared/footer.php'; ?>