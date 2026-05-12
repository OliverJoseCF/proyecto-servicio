<?php
$tsj_module     = 'visitantes';
$tsj_title      = 'COORDINADOR/A “ISC”';
$tsj_extra_css  = ['style.css'];
$tsj_head_extra = '<style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #4B0082;
            color: white;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            height: 100vh;
            position: relative;
        }
        h1 {
            background-color: #5757c0;
            padding: 30px 0;
            font-size: 40px;
            font-weight: bold;
        }
        .docentes {
            background-color: white;
            color: black;
            padding: 20px;
            border-radius: 8px;
            width: 80%;
            max-width: 470px;
            margin: 20px auto;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: left;
            font-size: 24px;
        }
        .footer {
            background: #333;
            color: white;
            text-align: center;
            padding: 20px;
            width: 100%;
        }
        .footer img {
            margin: 25px;
        }
        .top-right {
            position: absolute;
            top: 20px;
            right: 20px;
        }
    </style>';
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