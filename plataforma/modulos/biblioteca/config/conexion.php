<?php
require_once __DIR__ . '/../../../shared/config.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Lanza \mysqli_sql_exception en fallo — el inclusor decide cómo manejarlo.
$conexion = mysqli_init();
$conexion->options(MYSQLI_OPT_CONNECT_TIMEOUT, 5);
$conexion->real_connect(DB_HOST, DB_USER, DB_PASS, DB_BIBLIOTECA, DB_PORT);
$conexion->set_charset(DB_CHARSET);
