<?php
$tsj_module     = 'visitantes';
$tsj_title      = 'Nuevo Ingreso — TSJ Chapala';
$tsj_extra_css  = ['style.css'];
require_once __DIR__ . '/../../shared/header.php';
?>

    <h1>Nuevo Ingreso</h1>

    <div class="contenido">
        <div class="requisitos">
            <h2>Requisitos de Admisión</h2>
            <h3>Documentación requerida:</h3>
            <ul>
                <li>Copia de la identificación oficial.</li>
                <li>Certificado de estudios anteriores (original y copia).</li>
                <li>Comprobante de domicilio.</li>
                <li>Fotografías tamaño infantil (4 piezas).</li>
                <li>Formulario de inscripción llenado.</li>
            </ul>
        </div>

        <div class="examen">
            <h3>Examen de Admisión</h3>
            <p>El examen de admisión se llevará a cabo el día:
                <input type="number" id="dia-examen" name="dia-examen" value="20" min="1" max="31">
                . Asegúrate de traer los siguientes documentos:
            </p>
            <ul>
                <li>Identificación oficial.</li>
                <li>Comprobante de registro al examen.</li>
            </ul>

            <button onclick="generarPDF()" class="download-button">
                Generar Comprobante del Examen
            </button>
        </div>
    </div>

    <a href="index.php" class="top-right">
        <img src="imagenes/casa.png" alt="Ir a inicio" style="width: 80px; height: auto;">
    </a>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script>
        function generarPDF() {
            const dia = parseInt(document.getElementById("dia-examen").value, 10) || 20;
            const fecha = new Date();
            fecha.setDate(dia);
            const fechaFormateada = fecha.toLocaleDateString("es-MX", {
                day: 'numeric', month: 'long', year: 'numeric'
            });

            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            doc.setFontSize(16);
            doc.text("Comprobante de Examen de Admisión", 20, 20);
            doc.setFontSize(12);
            doc.text("El aspirante se ha registrado para presentar el examen de admisión.", 20, 32);
            doc.text("Detalles del examen:", 20, 45);
            doc.text("Fecha: " + fechaFormateada, 20, 55);
            doc.text("Hora: 8:00 AM", 20, 65);
            doc.text("Lugar: Tecnológico Superior de Jalisco, Campus Chapala", 20, 75);
            doc.text("Nombre del Aspirante: __________________________", 20, 90);
            doc.setFontSize(9);
            doc.text("Documento generado automáticamente — solo válido como recordatorio.", 20, 270);

            doc.save("Comprobante_Examen.pdf");
        }
    </script>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
