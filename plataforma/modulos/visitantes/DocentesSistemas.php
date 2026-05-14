<?php
$tsj_module     = 'visitantes';
$tsj_title      = 'Docentes — Ingeniería en Sistemas Computacionales';
$tsj_extra_css  = ['style.css'];
require_once __DIR__ . '/../../shared/header.php';
?>

    <a href="index.php" class="top-right">
        <img src="imagenes/casa.png" alt="Inicio" style="width: 70px;">
    </a>

    <h1>Docentes de Sistemas Computacionales</h1>

    <div class="tabla-container">
        <table>
            <thead>
                <tr><th>Docente</th></tr>
            </thead>
            <tbody id="lista-docentes"></tbody>
        </table>
    </div>

    <script>
        const docentes = [
            { nombre: "Miguel Ángel Delgado López",   foto: "miguel.png" },
            { nombre: "Alberto Chavolla",              foto: "user.png"   },
            { nombre: "Francisco Javier González",     foto: "user.png"   },
            { nombre: "Julio César Chávez Novoa",      foto: "julio.png"  },
            { nombre: "Edgar Martínez",                foto: "user.png"   },
            { nombre: "José Jorge Hernández Ochoa",    foto: "jorge.png"  },
            { nombre: "Carmen Leticia Salcedo",        foto: "carmen.png" },
            { nombre: "José Guadalupe Gamas",          foto: "gamas.png"  }
        ];

        const tbody = document.getElementById("lista-docentes");
        docentes.forEach(d => {
            const tr  = document.createElement("tr");
            const td  = document.createElement("td");
            const img = document.createElement("img");
            img.src = "imagenes/" + d.foto;
            img.alt = d.nombre;
            img.className = "foto-mini";
            img.onerror = function() { this.src = "imagenes/user.png"; };
            const b = document.createElement("b");
            b.textContent = d.nombre;
            td.appendChild(img); td.append(" "); td.appendChild(b);
            tr.appendChild(td);
            tbody.appendChild(tr);
        });
    </script>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
