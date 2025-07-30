<?php
include('./php/DB_connection.php');
include('./includes/session.php');
include('./includes/load_header.php');

// Determina si el usuario tiene permiso según su rol
$tiene_permiso = isset($_SESSION['rol']) && ($_SESSION['rol'] === 'admin' || $_SESSION['rol'] === 'colaborador');

// Captura el número de reloj del usuario para usarlo en el formulario
$noreloj_usuario = $_SESSION['noreloj'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Sobre MQE</title>
  <link rel="stylesheet" href="./fonts/fotawesome/css/all.min.css" />
  <link rel="stylesheet" href="./css/Header.css" />
  <link rel="stylesheet" href="./css/register.css" />
  <link rel="stylesheet" href="./css/about.css" />
  <link rel="stylesheet" href="./css/Perfil.css">
  <link rel="stylesheet" href="./css/Background.css" />
  <link rel="stylesheet" href="./css/Footer.css" />
  <link rel="stylesheet" href="./css/Comments.css" />
</head>
<body>
  <section class="intro">
      <img src="./img/MQE.jpg" alt="Herramientas MQE">
        <div class="text-content">
            <h2>¿Qué es MQE?</h2>
            <br>
            <p>
            MQE significa <strong>Manufacturing Quality Engineering</strong>. Es una rama de la ingeniería centrada en garantizar la calidad dentro de los procesos de manufactura, enfocándose en la prevención de errores, la mejora continua y el cumplimiento de los estándares de calidad en cada etapa de producción.
            </p>
        </div>
  </section>
  <section class="history">
      <img src="./img/mqe1.jpg" alt="Herramientas MQE">
        <div class="text-content">
            <h2>Origen e Historia</h2>
            <br>
            <p>
            El concepto de calidad en manufactura surgió con la revolución industrial, pero fue formalizado durante el siglo XX con las metodologías de Deming, Juran y otros pioneros. El enfoque MQE moderno combina estos principios clásicos con nuevas tecnologías como automatización, análisis de datos e inteligencia artificial.
            </p>
        </div>
  </section>
  <section class="objectives">
      <img src="./img/op.jpg" alt="Herramientas MQE">
        <div class="text-content">
            <h2>Objetivos principales</h2>
            <br>
            <ul>
            <li>Reducir la variabilidad en los procesos de producción.</li>
            <li>Garantizar la conformidad del producto con los estándares de cliente y normativas.</li>
            <li>Mejorar la eficiencia operativa mediante la eliminación de desperdicios.</li>
            <li>Fortalecer la colaboración entre manufactura, ingeniería y calidad.</li>
            </ul>
        </div>
  </section>
  <section class="tools">
      <img src="./img/HM.jpg" alt="Herramientas MQE">
        <div class="text-content">
            <h2>Herramientas y metodologías clave</h2>
            <br>
            <ul>
            <li><strong>SPC</strong> (Control Estadístico de Procesos)</li>
            <li><strong>FMEA</strong> (Análisis de Modo y Efecto de Fallo)</li>
            <li><strong>DMAIC</strong> (Definir, Medir, Analizar, Mejorar, Controlar)</li>
            <li><strong>8D</strong> (Metodología para resolución de problemas)</li>
            <li><strong>Root Cause Analysis</strong> (Ishikawa, 5 porqués)</li>
            <li><strong>Control Plans</strong> y <strong>PPAP</strong></li>
            </ul>
        </div>
  </section>
  <section class="roles">
      <img src="./img/Rol.png" alt="Herramientas MQE">
        <div class="text-content">
            <h2>Rol del Ingeniero MQE</h2>
            <br>
            <p>
            Un ingeniero MQE actúa como puente entre los departamentos de producción y calidad. Sus responsabilidades incluyen:
            </p>
            <ul>
            <li>Supervisar la implementación de estándares de calidad.</li>
            <li>Monitorear procesos críticos y datos de calidad.</li>
            <li>Auditar líneas de producción y asegurar la trazabilidad.</li>
            <li>Capacitar al personal en herramientas de calidad.</li>
            <li>Colaborar con diseño e ingeniería para asegurar el cumplimiento desde el desarrollo del producto.</li>
            </ul>
        </div>
  </section>
  <section class="examples">
      <img src="./img/APP.jpg" alt="Herramientas MQE">
        <div class="text-content">
            <h2>Ejemplos de aplicación</h2>
            <br>
            <p>
            Algunas aplicaciones comunes del enfoque MQE incluyen:
            </p>
            <ul>
            <li>Reducción del scrap en líneas SMT mediante análisis de datos SPC.</li>
            <li>Implementación de poka-yokes para evitar errores humanos.</li>
            <li>Desarrollo de dashboards de calidad para el monitoreo en tiempo real.</li>
            <li>Resolución estructurada de problemas de campo con análisis 8D.</li>
            </ul>
        </div>
  </section>
  <section class="benefits">
      <img src="./img/BEN.jpg" alt="Herramientas MQE">
        <div class="text-content">
            <h2>Beneficios de MQE</h2>
            <br>
            <p>
            MQE permite que las organizaciones entreguen productos de alta calidad de forma consistente, mejorando la satisfacción del cliente y reduciendo costos relacionados a defectos y reprocesos. Además, fortalece la cultura de calidad en todos los niveles operativos.
            </p>
        </div>
  </section>
  <section class="resources">
      <img src="./img/herr.jpg" alt="Herramientas MQE">
        <div class="text-content">
            <h2>Recursos adicionales</h2>
            <br>
            <p>Próximamente incluiremos enlaces a guías, plantillas y herramientas útiles para ingenieros MQE.</p>
        </div>
  </section>
    <?php if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], ['admin', 'colaborador'])): ?>
    <!-- Botón flotante que redirige a Perfil.php -->
    <a href="../Manual/webpages/Perfil.php" class="fab" title="Editar perfil">
        👤
    </a>
  <?php endif; ?>
  <?php include './includes/Footer.php'; ?>
    <script src="/js/Perfil.js"></script>

</body>
</html>
