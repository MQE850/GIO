<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/> <!-- Check -->
  <title>MQE - Manufacturing Quality Engineer</title>
  <link rel="stylesheet" href="./fonts/fotawesome/css/all.min.css">
  <link rel="stylesheet" href="./css/Perfil.css">
  <link rel="stylesheet" href="./css/Header.css">
  <link rel="stylesheet" href="../Manual/css//register.css" />
  <link rel="stylesheet" href="../Manual/css/styles.css">
  <link rel="stylesheet" href="./css/Background.css">
  <link rel="stylesheet" href="../Manual/css/Footer.css">
  <link rel="stylesheet" href="../Manual/css/Comments.css">
  <link rel="icon" type="image/png" href="./img/tabLogo.png">
</head>
<body>
  <script src="./js/Background.js"></script>
  <?php include './includes/load_header.php'; ?>

  <!-- Contenido -->
  <main>
    <div class="manual-container">
      <div class="text-content">
        <h1>Manual –<br>Manufacturing Quality Engineer</h1>
        <p>Conocimiento práctico, resultados reales.</p>
        <a href="./about.php" class="listen-btn">Ver más</a>
      </div>
      <div class="image-content">
        <img src="../Manual/img/logo.jpg" alt="Logo Manual" />
      </div>
    </div>
  </main>

  <div class="bottom-banner">
    Simplificamos lo complejo.
    <br>
  </div>
  <section class="generaciones">
    <h2>Generaciones</h2>
    <div class="generacion-grid">
      <article class="generacion-card">
        <img src="../Manual/img/1.jpg" alt="Gen8" />
        <h3>Generacion 8</h3>
        <p>Herramientas y Piezas</p>
        <a href="../Manual/about.php" class="boton-escuchar">Ver más</a>
      </article>
      <article class="generacion-card">
        <img src="../Manual/img/2.jpg" alt="Gen9" />
        <h3>Generacion 9</h3>
        <p>Herramientas y Piezas</p>
        <a href="../Manual/webpages/Generation.php" class="boton-escuchar">Ver más</a>
      </article>
      <article class="generacion-card">
        <img src="../Manual/img/3.jpg" alt="Gen10" />
        <h3>Generacion 10</h3>
        <p>Herramientas y Piezas</p>
        <a href="../Manual/webpages/Generation.php" class="boton-escuchar">Ver más</a>
      </article>
      <article class="generacion-card">
        <img src="../Manual/img/4.jpg" alt="Gen11" />
        <h3>Generacion 11</h3>
        <p>Herramientas y Piezas</p>
        <a href="../Manual/webpages/Generation.php" class="boton-escuchar">Ver más</a>
      </article>
    </div>
  </section><br>
  <section class="procesos-section">
    <div class="procesos-overlay">
      <h2>Explora nuestros procesos clave</h2>
      <p>Descubre cómo llevamos la calidad a cada etapa.</p>
      <a href="../Manual/webpages/Process.php" class="btn-procesos">Ver procesos</a>
    </div>
  </section><br>
  <section class="conceptos-section">
    <div class="conceptos-texto">
      <h2>CONCEPTOS CLAVE</h2>
      <p style="color: #fff;">
        Comprende los principios esenciales que guían nuestros procesos. 
        Desde la mejora continua hasta el control de calidad, esta sección 
        te ayudará a dominar los fundamentos que impulsan la excelencia operativa.
      </p>
      <a href="../Manual/webpages/Concept.php" class="btn-ir" style="background-color: #4d9db8;">Explorar conceptos</a>
    </div>
    <div class="conceptos-imagen">
      <img src="../Manual/img/concepto.jpg" alt="Ilustración de conceptos clave" />
    </div>
  </section>
  <section class="comandos-section">
    <div class="comandos-imagen">
      <img src="../Manual/img/comando.jpg" alt="Ilustración sobre comandos" />
    </div>
    <div class="comandos-texto">
      <h2>COMANDOS ÚTILES</h2>
      <p>
        Descubre los comandos más utilizados para navegar, ejecutar tareas
        y automatizar procesos. Dominar estos atajos puede ahorrarte tiempo 
        y mejorar tu eficiencia significativamente.
      </p>
      <a href="../Manual/webpages/Commands.php" class="btn-ir">Ver comandos</a>
    </div>
  </section>
  <?php if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], ['admin', 'colaborador'])): ?>
    <!-- Botón flotante que redirige a Perfil.php -->
    <a href="../Manual/webpages/Perfil.php" class="fab" title="Editar perfil">
        👤
    </a>
  <?php endif; ?>

  <?php include './includes/Footer.php'; ?>
  <script src="../../Manual/js/btn-registro.js"></script>
  <script src="/js/Perfil.js"></script>
</body>
</html>
