<?php
include '../config/conexion.php';

$sql = "SELECT 
            l.id, 
            l.nombre AS titulo, 
            l.autor, 
            l.editorial, 
            l.codigo AS folio,
            (SELECT COUNT(*) FROM solicitud_libros s 
             WHERE s.codigo_libro = l.codigo 
             AND s.estado = 'Aceptado' 
             AND s.entregado = 0) AS ocupado 
        FROM libros l 
        ORDER BY l.id DESC";

try {
    $result = $conexion->query($sql);
    $libros = [];

    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $libros[] = $row;
        }
    }

    header('Content-Type: application/json');
    echo json_encode($libros);

} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(["error" => $e->getMessage()]);
}
?>