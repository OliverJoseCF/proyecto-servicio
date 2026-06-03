<?php
/* Login de horarios reemplazado por el login central de la plataforma. */
require_once __DIR__ . "/../../shared/config.php";
header("Location: " . PLATAFORMA_URL . "/login.php");
exit;
