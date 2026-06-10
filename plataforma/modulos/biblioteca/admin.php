<?php
// Admin legacy de biblioteca — redirige al panel de administración central.
// Este archivo se mantiene para evitar 404 en marcadores existentes.
require_once __DIR__ . '/../../shared/lib/auth.php';
requireAuth('biblioteca', 'login.php');
header('Location: ' . PLATAFORMA_URL . '/admin/biblioteca.php');
exit;
