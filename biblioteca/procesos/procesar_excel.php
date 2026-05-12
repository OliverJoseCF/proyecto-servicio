<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "Admin", "churumuske");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

if (isset($_FILES['archivo_excel']) && $_FILES['archivo_excel']['error'] == 0) {
    $nombreArchivo = $_FILES['archivo_excel']['tmp_name'];

    $documento = IOFactory::load($nombreArchivo);
    $hoja = $documento->getActiveSheet();
    $filas = $hoja->toArray();

    for ($i = 1; $i < count($filas); $i++) {
        if (count($filas[$i]) < 6) continue; // Evita errores por columnas incompletas

        list($id, $nombre, $editorial, $clasificacion, $autor, $codigo) = $filas[$i];

        $sql = "INSERT INTO libros (id, nombre, editorial, clasificacion, autor, codigo) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("Error al preparar la consulta: " . $conn->error);
        }

        $stmt->bind_param("isssss", $id, $nombre, $editorial, $clasificacion, $autor, $codigo);
        $stmt->execute();
        $stmt->close();
    }

    echo "Libros importados correctamente.";
} else {
    echo "Error al subir el archivo.";
}

$conn->close();
?>
