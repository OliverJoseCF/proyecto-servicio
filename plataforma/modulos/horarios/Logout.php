<?php
require_once __DIR__ . '/../../shared/lib/auth.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrfVerify()) {
    authLogout('login.php');
}
header('Location: login.php');
exit;
