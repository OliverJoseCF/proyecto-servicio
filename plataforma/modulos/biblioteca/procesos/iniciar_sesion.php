<?php

session_start();

$usuario = $_POST['usuario'] ?? '';
$clave = $_POST['clave'] ?? '';

if ($usuario === 'admin' && $clave === '1234') {

    $_SESSION['logueado'] = true;
    
    header("Location: ../admin.php");
    exit;
} else {
    echo "<script>
            alert('Usuario o contraseña incorrectos. Intenta de nuevo.');
            window.location.href = '../login.html';
          </script>";
    exit;
}
?>