<?php
$tsj_module     = 'visitantes';
$tsj_title      = 'Comprobante de Reinscripción';
$tsj_extra_css  = ['style.css'];

require_once __DIR__ . '/../../shared/header.php';
?>


<h1>Comprobante de Reinscripción</h1>

<div class="container">

    <div class="seccion">
        <label for="nombre">Nombre completo:</label>
        <input type="text" id="nombre" placeholder="Escribe tu nombre completo">

        <label for="control">Número de control:</label>
        <input type="text" id="control" placeholder="Número de control">

        <label for="carrera">Carrera:</label>
        <select id="carrera">
            <option value="">Selecciona tu carrera</option>
            <option>Mecatrónica</option>
            <option>Sistemas Computacionales</option>
            <option>industrial</option>
            <option>Animacion</option>
            <option>Gastronomia</option>
            <option>Gestion empresarial</option>
        </select>

        <button onclick="generarComprobante()" class="download-button">
            📄 Generar Comprobante PDF
        </button>
    </div>

</div>

<!-- jsPDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<!-- QR Code Generator -->
<script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>

<script>
    async function generarComprobante() {
        const nombre = document.getElementById("nombre").value.trim();
        const control = document.getElementById("control").value.trim();
        const carrera = document.getElementById("carrera").value;

        if (!nombre || !control || !carrera) {
            alert("Por favor llena todos los campos.");
            return;
        }

        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        const fecha = new Date().toLocaleDateString();
        const monto = "$1,500.00 MXN";
        const concepto = "Reinscripción Ciclo Escolar Agosto-Diciembre 2025";
        const folio = "RS-" + Math.floor(100000 + Math.random() * 900000);

        const datosQR = `Folio: ${folio}\nNombre: ${nombre}\nControl: ${control}\nCarrera: ${carrera}\nFecha: ${fecha}`;

        const qrDataURL = await QRCode.toDataURL(datosQR);

        // Cabecera azul
        doc.setFillColor(27, 63, 143);
        doc.rect(0, 0, 210, 30, "F");
        doc.setTextColor(255, 255, 255);
        doc.setFontSize(16);
        doc.text("Departamento de Servicios Escolares", 105, 18, { align: "center" });

        // Título
        doc.setTextColor(0, 0, 0);
        doc.setFontSize(14);
        doc.text("Comprobante Oficial de Pago por Reinscripción", 105, 40, { align: "center" });

        // Fecha y folio
        doc.setFontSize(12);
        doc.text(`Fecha de emisión: ${fecha}`, 20, 50);
        doc.text(`Folio: ${folio}`, 150, 50);

        // Caja de información
        doc.setFillColor(245, 245, 245);
        doc.setDrawColor(200);
        doc.rect(15, 60, 180, 60, "FD");

        doc.setFontSize(12);
        doc.setTextColor(0, 0, 0);
        doc.text(`Nombre del Alumno: ${nombre}`, 20, 70);
        doc.text(`Número de Control: ${control}`, 20, 80);
        doc.text(`Carrera: ${carrera}`, 20, 90);
        doc.text(`Concepto: ${concepto}`, 20, 100);

        // Monto
        doc.setFontSize(14);
        doc.setTextColor(0, 102, 0);
        doc.text(`Monto Pagado: ${monto}`, 20, 115);

        // Código QR
        doc.addImage(qrDataURL, "PNG", 145, 70, 40, 40);

        // Estatus
        doc.setFontSize(12);
        doc.setTextColor(0, 153, 51);
        doc.text("✔ Estatus: Pago Confirmado", 20, 135);

        // Aviso
        doc.setFontSize(10);
        doc.setTextColor(100);
        doc.text("Este documento es oficial y debe conservarse como comprobante del proceso de reinscripción.", 20, 145);

        doc.save("Comprobante_Reinscripcion.pdf");
    }
</script>


<?php require_once __DIR__ . '/../../shared/footer.php'; ?>