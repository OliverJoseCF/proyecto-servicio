<?php
$_clave_carrera = strtoupper(trim($_GET['carrera'] ?? ''));
if (!preg_match('/^[A-Z0-9]{1,10}$/', $_clave_carrera)) {
    header('Location: ofertaAcademica.php');
    exit;
}
require_once __DIR__ . '/_docentes_template.php';
