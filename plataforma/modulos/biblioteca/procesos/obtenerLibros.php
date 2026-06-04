<?php
header('Content-Type: application/json');

try {
    require '../config/conexion.php';

    // solicitudes_biblioteca es el nombre correcto en kiosko_tsj
    // un libro está "ocupado" si tiene solicitudes aprobadas no devueltas
    $sql = "SELECT
                l.id,
                l.nombre  AS titulo,
                l.autor,
                l.editorial,
                l.codigo  AS folio,
                l.ejemplares,
                (SELECT COUNT(*)
                 FROM solicitudes_biblioteca s
                 WHERE s.libro_id = l.id
                   AND s.estado   = 'aprobada') AS prestamos_activos
            FROM libros l
            WHERE l.activo = 1
            ORDER BY l.nombre ASC";

    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $libros = [];
    while ($row = $result->fetch_assoc()) {
        // ocupado si todos los ejemplares están prestados
        $row['ocupado'] = (int)$row['prestamos_activos'] >= (int)$row['ejemplares'] ? 1 : 0;
        $libros[] = $row;
    }
    echo json_encode($libros);

} catch (\Throwable $e) {
    error_log('obtenerLibros error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Error al cargar los libros.']);
}
