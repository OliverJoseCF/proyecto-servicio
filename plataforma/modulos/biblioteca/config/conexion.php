<?php
require_once __DIR__ . '/../../../shared/config.php';

// Conexión mysqli centralizada vía helper de shared/config.php
// (lanza \mysqli_sql_exception en fallo — el inclusor decide cómo manejarlo).
$conexion = getMysqli(DB_BIBLIOTECA);
