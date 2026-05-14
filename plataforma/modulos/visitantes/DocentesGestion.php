<?php
$tsj_module     = 'visitantes';
$tsj_title      = 'Docentes — Ingeniería en Gestión Empresarial';
$tsj_extra_css  = ['style.css'];
require_once __DIR__ . '/../../shared/header.php';
?>
    <a href="index.php" class="top-right"><img src="imagenes/casa.png" alt="Inicio" style="width:70px;"></a>
    <h1>Docentes de Ingeniería en Gestión Empresarial</h1>
    <div class="tabla-container">
        <table><thead><tr><th>Docente</th></tr></thead><tbody id="lista-docentes"></tbody></table>
    </div>
    <script>
        const docentes = [
            { nombre: "Carlos Ramírez",  foto: "user.png" },
            { nombre: "Fidel Rodríguez", foto: "user.png" },
            { nombre: "Alberto Chavoya", foto: "user.png" },
            { nombre: "Alma González",   foto: "user.png" },
            { nombre: "José Aguilera",   foto: "user.png" },
            { nombre: "María Estrada",   foto: "user.png" }
        ];
        const tbody = document.getElementById("lista-docentes");
        docentes.forEach(d => {
            const tr=document.createElement("tr"), td=document.createElement("td");
            const img=document.createElement("img"); img.src="imagenes/"+d.foto; img.alt=d.nombre; img.className="foto-mini"; img.onerror=function(){this.src="imagenes/user.png";};
            const b=document.createElement("b"); b.textContent=d.nombre;
            td.appendChild(img); td.append(" "); td.appendChild(b); tr.appendChild(td); tbody.appendChild(tr);
        });
    </script>
<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
