<?php
header('Content-Type: application/json');

try {
    require_once __DIR__ . '/../../../shared/config.php';

    // Usar PDO igual que el resto de la plataforma — evita problemas de conexión mysqli
    $pdo = getPDO(DB_NAME);

    $sql = "SELECT
                l.id,
                l.nombre    AS titulo,
                l.autor,
                l.editorial,
                l.codigo    AS folio,
                l.ejemplares,
                COALESCE(pa.prestamos_activos, 0) AS prestamos_activos
            FROM libros l
            LEFT JOIN (
                SELECT libro_id, COUNT(*) AS prestamos_activos
                FROM prestamos
                WHERE devuelto = 0
                GROUP BY libro_id
            ) pa ON pa.libro_id = l.id
            WHERE l.activo = 1
            ORDER BY l.nombre ASC";

    $stmt   = $pdo->query($sql);
    $libros = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $row['ocupado'] = (int)$row['prestamos_activos'] >= (int)$row['ejemplares'] ? 1 : 0;
        $libros[] = $row;
    }

    echo json_encode($libros);

} catch (\Throwable $e) {
    error_log('obtenerLibros error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Error al cargar los libros.']);
}
