<?php
// Incluir control de sesión
include_once __DIR__ . '/session.php';
?>

<header id="mqe-navbar">
  <div class="user-logo">
    <div class="logo">
      <img src="../../Manual/img/MQE_LOGOPUTO.png" alt="MQE Logo" />
    </div>

    <div class="user-info" style="color:white;">
      Bienvenido, <?= htmlspecialchars($nombre) ?> (<?= htmlspecialchars($departamento) ?>)
    </div>
  </div>
  
  <nav>
    <ul>
      <li><a class="fa-solid fa-house" href="../../Manual/index.php" title="Inicio"></a></li>
      <li><a href="../../Manual/webpages/Pruebas.php" hidden>Pruebas</a></li>
      <li><a href="../../Manual/webpages/Generation.php" hidden>Generación</a></li>
      <li><a href="../../Manual/webpages/Process.php">Procesos</a></li>
      <li><a href="../../Manual/webpages/Concept.php">Conceptos</a></li>
      <li><a href="../../Manual/webpages/Commands.php">Comandos</a></li>
      <li><a class="fa-solid fa-right-from-bracket" href="../../Manual/php/Logout.php" title="Cerrar sesión"></a></li>
    </ul>
  </nav>
</header>

<script src="/Manual/js/btn-registro.js"></script>
