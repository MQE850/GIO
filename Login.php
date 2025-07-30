<?php
session_start(); // Inicia la sesión
if (isset($_SESSION['nombre'])) {
    $nombre_usuario = $_SESSION['nombre'];  // Asignar el nombre de la sesión
} else {
    $nombre_usuario = 'Usuario no autenticado'; // En caso de que no haya sesión
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="stylesheet" href="../css/Login.css">
  <link rel="stylesheet" href="../css/Background.css">
<link rel="stylesheet" href="/Manual/css/Footer.css">
  <link rel="stylesheet" href="../css/Header.css" />
  <link rel="stylesheet" href="../css/Comments.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
  .footer-text a {
    color: #fff; /* ← Cambia este valor por el color que prefieras */
    text-decoration: none;
    font-weight: 600;
    transition: color 0.3s ease;
  }

  .footer-text a:hover {
    color: #00d1b2; /* Color al pasar el mouse */
    text-decoration: underline;
  }
</style>

</head>

<body>
  <header id="navbar">
    <div class="logo">Inicio de Sesión</div>
    <nav>
      <ul>
        <li><a href="../index.php"><i class="fa-solid fa-house"></i></a></li>
      </ul> 
    </nav>
  </header>

  <div class="login-container">
    <div class="login-box">
      <h2>Iniciar Sesión</h2>
      <form method="POST" action="../php/Login_Function.php">
        <label for="noreloj"><i class="fa-solid fa-user"></i> NoReloj:</label>
        <input type="text" name="noreloj" id="noreloj" required>
        
        <label for="contra"><i class="fa-solid fa-key"></i> Contraseña:</label>
        <input type="password" name="contra" id="contra" required>
        
        <button type="submit">Iniciar sesión</button>
      </form>
      <p class="footer-text">
        <a href="./reset_password.php">¿Olvidaste tu contraseña?</a>
      </p>
    </div>
  </div>

  <script src="../JS/login.js"></script>
  
  <?php include '../includes/Footer.php'?>
</body>
</html>
