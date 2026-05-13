<?php
require_once __DIR__ . '/config.php';

try {
    $pdo = getDB();
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

$carreras = $pdo->query("SELECT id_carrera, nombre_carrera FROM Carreras")->fetchAll();

$busqueda         = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';
$id_carrera       = isset($_GET['id_carrera']) ? (int)$_GET['id_carrera'] : 0;
$items_por_pagina = 30;
$pagina_actual    = max(1, (int)($_GET['pagina'] ?? 1));
$offset           = ($pagina_actual - 1) * $items_por_pagina;

$where  = 'WHERE 1=1';
$params = [];
if ($busqueda !== '') {
    $where .= ' AND (p.nombre LIKE :busqueda1 OR p.apellido LIKE :busqueda2)';
    $params[':busqueda1'] = "%$busqueda%";
    $params[':busqueda2'] = "%$busqueda%";
}
if ($id_carrera > 0) {
    $where .= ' AND h.id_carrera = :id_carrera';
    $params[':id_carrera'] = $id_carrera;
}

$sql_base = "
    SELECT h.id_horario,
           p.nombre    AS nombre_profesor,
           p.apellido  AS apellido_profesor,
           c.nombre_carrera,
           h.semestre,
           h.imagen_horario
    FROM   Horarios h
    JOIN   Profesores p ON h.id_profesor = p.id_profesor
    JOIN   Carreras   c ON h.id_carrera  = c.id_carrera
    $where
";

$stmt_count = $pdo->prepare("SELECT COUNT(*) FROM ($sql_base) AS t");
$stmt_count->execute($params);
$total_items   = (int)$stmt_count->fetchColumn();
$total_paginas = (int)ceil($total_items / $items_por_pagina);

$stmt = $pdo->prepare("$sql_base LIMIT :limit OFFSET :offset");
foreach ($params as $k => $v) $stmt->bindValue($k, $v);
$stmt->bindValue(':limit',  $items_por_pagina, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset,           PDO::PARAM_INT);
$stmt->execute();
$horarios_paginados = $stmt->fetchAll();

$tsj_module    = 'horarios';
$tsj_title     = 'Maestros y Horarios';
$tsj_extra_css = ['css/normalize.css', 'css/Principal.css'];
require_once __DIR__ . '/../../shared/header.php';
?>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="main-content">

        <h1 class="section-title">BUSCAR MAESTRO</h1>

        <section class="filtros" aria-label="Filtros de búsqueda">
            <p class="section-subtitle">FILTRAR POR:</p>
            <div class="filtros-inner">
                <div class="filtro-busqueda">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <form method="GET" action="" role="search">
                        <input type="text" name="busqueda" placeholder="Buscar maestro..."
                               value="<?= htmlspecialchars($_GET['busqueda'] ?? '') ?>"
                               aria-label="Buscar maestro">
                        <?php if ($id_carrera > 0): ?>
                            <input type="hidden" name="id_carrera" value="<?= $id_carrera ?>">
                        <?php endif; ?>
                        <button type="submit">Buscar</button>
                    </form>
                </div>
                <div class="filtro-carrera">
                    <form method="GET" action="">
                        <?php if ($busqueda !== ''): ?>
                            <input type="hidden" name="busqueda" value="<?= htmlspecialchars($busqueda) ?>">
                        <?php endif; ?>
                        <select name="id_carrera" onchange="this.form.submit()" aria-label="Filtrar por carrera">
                            <option value="0">— Todas las carreras —</option>
                            <?php foreach ($carreras as $c): ?>
                                <option value="<?= $c['id_carrera'] ?>"
                                    <?= $id_carrera == $c['id_carrera'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($c['nombre_carrera']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>
            </div>
        </section>

        <section class="tabla-section" aria-label="Resultados">
            <div class="tabla-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Maestro</th>
                            <th>Horario</th>
                            <th>Carrera</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($horarios_paginados)): ?>
                            <tr><td colspan="3" class="sin-resultados">No se encontraron resultados.</td></tr>
                        <?php else: ?>
                            <?php foreach ($horarios_paginados as $h): ?>
                                <tr>
                                    <td><?= htmlspecialchars($h['nombre_profesor'].' '.$h['apellido_profesor']) ?></td>
                                    <td>
                                        <a href="<?= htmlspecialchars($h['imagen_horario']) ?>"
                                           class="open-modal btn-horario">Ver Horario</a>
                                    </td>
                                    <td><?= htmlspecialchars($h['nombre_carrera']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($total_items > 0): ?>
            <div class="paginacion">
                <span><?= ($offset+1) ?>–<?= min($offset+$items_por_pagina,$total_items) ?> de <?= $total_items ?> resultados</span>
                <div class="paginacion-links">
                    <?php if ($pagina_actual > 1): ?>
                        <a href="?pagina=<?= $pagina_actual-1 ?>&id_carrera=<?= $id_carrera ?>&busqueda=<?= urlencode($busqueda) ?>">« Anterior</a>
                    <?php endif; ?>
                    <?php if ($pagina_actual < $total_paginas): ?>
                        <a href="?pagina=<?= $pagina_actual+1 ?>&id_carrera=<?= $id_carrera ?>&busqueda=<?= urlencode($busqueda) ?>">Siguiente »</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </section>

    </main>

    <!-- MODAL -->
    <div id="modalHorario" class="modal" role="dialog" aria-modal="true" aria-label="Horario">
        <div class="modal-overlay"></div>
        <div class="modal-box">
            <button class="modal-close" aria-label="Cerrar">×</button>
            <div id="modalContent" class="modal-content"></div>
        </div>
    </div>

    <script src="js/modal.js"></script>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>