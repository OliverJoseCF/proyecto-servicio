<?php
$tsj_module     = 'visitantes';
$tsj_title      = 'Docentes — Gastronomía';
$tsj_extra_css  = ['style.css'];
require_once __DIR__ . '/../../shared/header.php';
?>
    <a href="index.php" class="top-right"><img src="imagenes/casa.png" alt="Inicio" style="width:70px;"></a>
    <h1>Docentes de Gastronomía</h1>
    <div class="tabla-container">
        <table><thead><tr><th>Docente</th></tr></thead><tbody id="lista-docentes"></tbody></table>
    </div>
    <script>
        const docentes = [
            { nombre: "Lina Corona",      foto: "user.png" },
            { nombre: "Jessica Álvarez",  foto: "user.png" },
            { nombre: "Jaime Sánchez",    foto: "user.png" },
            { nombre: "Carlos Ramírez",   foto: "user.png" },
            { nombre: "Yessica Regalado", foto: "user.png" },
            { nombre: "Mayra Hinojoza",   foto: "user.png" }
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
