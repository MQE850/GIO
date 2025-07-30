<?php
session_start();
include '../php/DB_connection.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $noreloj = $_POST['noreloj'] ?? '';

    $stmt = $conn->prepare("SELECT noreloj FROM Empleados WHERE noreloj = ?");
    $stmt->bind_param("s", $noreloj);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $_SESSION['reset_noreloj'] = $noreloj;
        header('Location: ./change_password.php');
        exit(); // Detiene ejecución después de redirigir
    } else {
        $error = "No se encontró el usuario con NoReloj: $noreloj";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Recuperar Contraseña</title>
  <link rel="stylesheet" href="../css/Header.css">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

  <style>
    :root {
      --azul-claro: #005f99;
      --azul-oscuro: #081032;
      --azul-pastel: #87cefa;
      --azul-turqueza: #4d9db8;
      --blanco: #ffffff;
      --negro: #000;
      --gris-claro: #ccc;
      --gris-oscuro: #333;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #71b7e6, rgb(7, 28, 105));
      min-height: 100vh;
      margin: 0;
      padding-top: 70px;
      display: flex;
      flex-direction: column;
      align-items: center;
      color: var(--blanco);
    }

    header#navbar {
      background-color: var(--azul-oscuro);
      color: var(--blanco);
      padding: 15px 30px;
      width: 100%;
      position: fixed;
      top: 0;
      z-index: 1000;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    header .logo {
      font-size: 1.5rem;
      font-weight: bold;
    }

    header nav ul {
      list-style: none;
      display: flex;
      margin: 0;
      padding: 0;
    }

    header nav ul li {
      margin-left: 20px;
    }

    header nav ul li a {
      color: var(--blanco);
      text-decoration: none;
      font-weight: 500;
    }

    header nav ul li a:hover {
      text-decoration: underline;
    }

    .container {
      background-color: rgba(0, 0, 0, 0.6);
      padding: 30px 40px;
      border-radius: 10px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
      width: 320px;
      text-align: center;
      margin-top: 100px;
      margin-bottom: auto;
    }

    h2 {
      margin-bottom: 25px;
      font-weight: 700;
    }

    label {
      display: block;
      margin-bottom: 8px;
      font-weight: 600;
      text-align: left;
    }

    input[type="text"] {
      width: 100%;
      padding: 10px 12px;
      margin-bottom: 20px;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      box-sizing: border-box;
    }

    button {
      width: 100%;
      padding: 12px;
      background: #00d1b2;
      border: none;
      border-radius: 6px;
      color: var(--blanco);
      font-weight: 700;
      font-size: 16px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    button:hover {
      background: rgb(18, 119, 104);
    }

    p.error {
      color: #ff6b6b;
      font-weight: 600;
      margin-top: 10px;
    }

  </style>
</head>
<body>

<header id="navbar">
  <div class="logo">Recuperar Contraseña</div>
  <nav>
    <ul>
      <li><a href="../webpages/Login.php"><i class="fas fa-house"></i> Login</a></li>
    </ul>
  </nav>
</header>

<div class="container">
  <h2>Recuperar Contraseña</h2>
  <form method="POST" action="reset_password.php">
    <label for="noreloj">Ingresa tu NoReloj:</label>
    <input type="text" name="noreloj" id="noreloj" required />
    <button type="submit">Enviar</button>
  </form>

  <?php if (!empty($error)): ?>
    <p class="error"><?php echo htmlspecialchars($error); ?></p>
  <?php endif; ?>
</div>

</body>
</html>
