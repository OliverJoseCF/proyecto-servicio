<?php
$tsj_module     = 'visitantes';
$tsj_title      = 'Servicios Generales';
$tsj_extra_css  = ['style.css'];

require_once __DIR__ . '/../../shared/header.php';
?>


    <h1>Servicios Generales</h1>

    <div class="contenido">
        <div class="requisitos">
            <h2>Nuevo Ingreso</h2>
            <h3>Requisitos:</h3>
            <ul>
                <li>Copia de la identificación oficial.</li>
                <li>Certificado de estudios anteriores.</li>
                <li>Comprobante de domicilio.</li>
                <li>Fotografías tamaño infantil.</li>
                <li>Formulario de inscripción llenado.</li>
            </ul>
        </div>
    
        <div class="examen">
            <h3>Examen de Admisión</h3>
            <p>El examen de admisión se llevará a cabo el día 
                <input type="number" id="dia-examen" name="dia-examen" value="20" min="1" max="31">
                . Asegúrate de traer los siguientes documentos:
            </p>
            <ul>
                <li>Identificación oficial.</li>
                <li>Comprobante de registro al examen.</li>
            </ul>

            <!-- Botón para generar el PDF -->
            <button onclick="generarPDF()" class="download-button">
                Generar Comprobante del Examen
            </button>
        </div> 
    </div>

    <!-- Botón de regreso -->
    <a href="index.php" class="top-right">
        <img src="imagenes/casa.png" alt="Ir a otra página" style="width: 80px; height: auto;">
    </a>

    <!-- Cargar jsPDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <script>
        function generarPDF() {
            // Obtener el valor del día del examen
            const dia = document.getElementById("dia-examen").value;

            // Formatear la fecha
            const fecha = new Date();
            fecha.setDate(dia);  // Ajustar la fecha al día ingresado
            const fechaFormateada = fecha.toLocaleDateString("es-MX", { 
                day: 'numeric', month: 'long', year: 'numeric' 
            });

            // Crear el documento PDF
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            doc.setFontSize(16);
            doc.text("Comprobante de Examen de Admisión", 20, 20);
            doc.setFontSize(12);
            doc.text("Se le informa al aspirante que se ha registrado correctamente para presentar el examen de admisión.", 20, 30);
            doc.text("Detalles del examen:", 20, 45);
            doc.text(`Fecha: ${fechaFormateada}`, 20, 55);
            doc.text("Hora: 8:00 AM", 20, 65);
            doc.text("Lugar: Instituto Tecnológico Superior de Chapala", 20, 75);
            doc.text("Nombre del Aspirante: __________________________", 20, 90);

            // Descargar el archivo PDF
            doc.save("Comprobante_Examen.pdf");
        }
    </script>


<?php require_once __DIR__ . '/../../shared/footer.php'; ?>