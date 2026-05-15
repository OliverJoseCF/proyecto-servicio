<?php
// El header JSON va primero: garantiza que cualquier excepción devuelva JSON, no HTML.
header('Content-Type: application/json');

try {
    require '../config/conexion.php';

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

    $stmt   = $conexion->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $libros = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($libros);
} catch (\Throwable $e) {
    error_log('obtenerLibros error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Error al cargar los libros. Contacta al administrador.']);
}
