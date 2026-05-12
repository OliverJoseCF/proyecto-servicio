<?php
session_start();
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'];
    $clave = $_POST['clave'];

    if ($usuario === 'admin' && $clave === '1234') {
        $_SESSION['logueado'] = true; 
        header("Location: admin.php");
        exit;
    } else {
        $error = "Usuario o contraseña incorrectos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Biblioteca | Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  
  <style>
    :root {
      --deep: #1a0533;
      --purple: #3D2E81;
      --gold: #c9a84c;
      --cream: #f5f0e8;
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'DM Sans', sans-serif;
      background-color: var(--deep);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      overflow: hidden;
      position: relative;
    }

    .bg-layer {
      position: fixed;
      inset: 0;
      background:
        radial-gradient(ellipse at 20% 50%, rgba(61,46,129,0.6) 0%, transparent 60%),
        radial-gradient(ellipse at 80% 20%, rgba(201,168,76,0.15) 0%, transparent 50%),
        radial-gradient(ellipse at 60% 80%, rgba(61,46,129,0.3) 0%, transparent 50%);
      z-index: 0;
    }

    .bg-dots {
      position: fixed;
      inset: 0;
      background-image: radial-gradient(rgba(255,255,255,0.04) 1px, transparent 1px);
      background-size: 32px 32px;
      z-index: 0;
    }

    .top-bar {
      position: relative;
      z-index: 10;
      display: flex;
      justify-content: flex-start;
      padding: 24px 40px;
    }

    .btn-back {
      font-family: 'DM Sans', sans-serif;
      font-size: 0.8rem;
      font-weight: 500;
      letter-spacing: 0.1em;
      color: var(--gold);
      border: 1px solid rgba(201,168,76,0.4);
      background: rgba(201,168,76,0.08);
      padding: 8px 20px;
      border-radius: 2px;
      cursor: pointer;
      text-decoration: none;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .btn-back:hover {
      background: rgba(201,168,76,0.18);
      border-color: var(--gold);
      color: var(--gold);
    }

    .login-container {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      position: relative;
      z-index: 5;
      padding-bottom: 50px;
    }

    .login-box {
      background: rgba(245,240,232,0.04);
      border: 1px solid rgba(201,168,76,0.2);
      backdrop-filter: blur(10px);
      border-radius: 4px;
      padding: 48px 32px;
      width: 100%;
      max-width: 380px;
      text-align: center;
      animation: fadeUp 0.8s ease forwards;
    }

    .login-title {
      font-family: 'Cormorant Garamond', serif;
      font-size: 2.2rem;
      color: var(--cream);
      margin-bottom: 8px;
    }

    .login-subtitle {
      font-size: 0.75rem;
      color: var(--gold);
      text-transform: uppercase;
      letter-spacing: 0.2em;
      margin-bottom: 30px;
    }

    .input-group {
      position: relative;
      margin-bottom: 20px;
    }

    .input-group i {
      position: absolute;
      left: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--gold);
      opacity: 0.7;
    }

    .input-group input {
      width: 100%;
      padding: 12px 12px 12px 45px;
      background: rgba(255,255,255,0.05);
      border: 1px solid rgba(201,168,76,0.2);
      border-radius: 2px;
      color: var(--cream);
      outline: none;
      transition: all 0.3s;
    }

    .btn-submit {
      width: 100%;
      padding: 12px;
      background: var(--gold);
      color: var(--deep);
      border: none;
      border-radius: 2px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.1em;
      cursor: pointer;
      transition: all 0.3s;
      margin-top: 10px;
    }

    .btn-submit:hover {
      background: #e0bc5a;
      transform: translateY(-2px);
    }

    .error-msg {
      color: #ff6b6b;
      font-size: 0.8rem;
      margin-bottom: 15px;
      background: rgba(255,107,107,0.1);
      padding: 8px;
    }

    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(24px); }
      to   { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>

  <div class="bg-layer"></div>
  <div class="bg-dots"></div>

  <div class="top-bar">
    <a href="index.html" class="btn-back">
      <i class="fas fa-arrow-left"></i> Página Inicial
    </a>
  </div>

  <div class="login-container">
    <div class="login-box">
      <h2 class="login-title">Bienvenido</h2>
      <p class="login-subtitle">Acceso Administrativo</p>
      
      <?php if ($error): ?>
        <div class="error-msg"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div>
      <?php endif; ?>

      <form method="POST" action="">
        <div class="input-group">
          <i class="fas fa-user"></i>
          <input type="text" name="usuario" placeholder="Usuario" required>
        </div>

        <div class="input-group">
          <i class="fas fa-lock"></i>
          <input type="password" name="clave" placeholder="Contraseña" required>
        </div>

        <button type="submit" class="btn-submit">Ingresar al Sistema</button>
      </form>
    </div>
  </div>

</body>
</html>