<?php
session_start();

// Definir rol por defecto si no existe en sesión
if (!isset($_SESSION['rol'])) {
    $_SESSION['rol'] = 'publico';
}
$rol = $_SESSION['rol'];

// Definir noreloj solo si rol no es publico
$noreloj = ($rol === 'publico') ? null : (isset($_SESSION['noreloj']) ? $_SESSION['noreloj'] : null);

include('../php/DB_connection.php');
include('../includes/session.php'); // debe ir antes de usar $noreloj
include('../includes/load_header.php');

// Parámetros de búsqueda y paginación
$busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';
$pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$por_pagina = 5;
$offset = ($pagina - 1) * $por_pagina;

// Condiciones para búsqueda
$where = "";
$params = [];
$types = "";

if ($busqueda !== '') {
    $busqueda_like = "%$busqueda%";
    $where = "WHERE titulo LIKE ? OR contenido LIKE ? OR comando LIKE ?";
    $params = [$busqueda_like, $busqueda_like, $busqueda_like];
    $types = "sss";
}

// Consulta para total de registros
$sql_total = "SELECT COUNT(*) FROM comandos $where";
$stmt_total = $conn->prepare($sql_total);
if (!empty($params)) {
    $stmt_total->bind_param($types, ...$params);
}
$stmt_total->execute();
$stmt_total->bind_result($total);
$stmt_total->fetch();
$stmt_total->close();
$total_paginas = ceil($total / $por_pagina);

// Consulta para obtener registros con paginación
$sql = "SELECT * FROM comandos $where ORDER BY id DESC LIMIT ?, ?";
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $params[] = $offset;
    $params[] = $por_pagina;
    $types .= "ii";
    $stmt->bind_param($types, ...$params);
} else {
    $stmt->bind_param("ii", $offset, $por_pagina);
}
$stmt->execute();
$resultado = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Comandos Técnicos</title>
  <link rel="stylesheet" href="../fonts/fotawesome/css/all.min.css" />
  <link rel="stylesheet" href="../css/Header.css" />
  <link rel="stylesheet" href="../css/Perfil.css">
  <link rel="stylesheet" href="../css/register.css" />
  <link rel="stylesheet" href="../css/Commands.css" />
  <link rel="stylesheet" href="../css/Background.css" />
  <link rel="stylesheet" href="../css/Footer.css" />
  <link rel="stylesheet" href="../css/Comments.css" />
  <style>
    .paginacion { text-align: center; margin-top: 20px; }
    .paginacion a, .paginacion span {
      margin: 0 5px; padding: 8px 12px; background: #f0f0f0; border-radius: 5px;
      text-decoration: none; color: #000;
    }
    .paginacion .actual { background-color: #007BFF; color: white; font-weight: bold; }
    .buscador { margin-bottom: 20px; text-align: center; }
    .buscador input { padding: 8px; width: 60%; border-radius: 5px; border: 1px solid #ccc; }
    .buscador button { padding: 8px 16px; background: #007BFF; color: #fff; border: none; border-radius: 5px; }
  </style>
</head>
<body>
<div class="contenedor">
  <h1>Comandos Técnicos</h1>

  <div class="buscador">
    <form method="GET" action="../webpages/Commands.php" class="form-busqueda">
      <input type="text" name="busqueda" placeholder="Buscar..." value="<?= htmlspecialchars($busqueda) ?>" />
      <button type="submit">Buscar</button>
    </form>
  </div>

    <?php if (in_array($rol, ['admin', 'colaborador'])): ?>
      <button id="btn-agregar" class="btn-agregar">+ Agregar Comando</button>
    <?php endif; ?>

  <?php while ($cmd = $resultado->fetch_assoc()): ?>
    <div class="tarjeta" 
      data-id="<?= $cmd['id'] ?>" 
      data-titulo="<?= htmlspecialchars($cmd['titulo']) ?>" 
      data-contenido="<?= htmlspecialchars($cmd['contenido']) ?>"
      data-comando="<?= htmlspecialchars($cmd['comando']) ?>">
      
      <h3><?= htmlspecialchars($cmd['titulo']) ?></h3>
      <?php if ($cmd['imagen'] && file_exists("../uploads/" . $cmd['imagen'])): ?>
        <img src="../uploads/<?= htmlspecialchars($cmd['imagen']) ?>" alt="Imagen comando" />
      <?php endif; ?>
      <p><?= nl2br(htmlspecialchars($cmd['contenido'])) ?></p>
      <div class="comando"><?= htmlspecialchars($cmd['comando']) ?></div>
      <div class="info-creador">Creado por: <?= htmlspecialchars($cmd['noreloj']) ?> | Fecha Creación: <?= $cmd['creado_en'] ?></div>
      <br>
      <?php if (in_array($rol, ['admin', 'colaborador'])): ?>
        <div class="acciones">
          <button class="editar" title="Editar">✏️</button>
          <button class="eliminar" title="Eliminar">🗑️</button>
        </div>
      <?php endif; ?>
    </div>
  <?php endwhile; ?>

  <div class="paginacion">
    <?php if ($pagina > 1): ?>
      <a href="?busqueda=<?= urlencode($busqueda) ?>&pagina=<?= $pagina - 1 ?>">&laquo; Anterior</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
      <?php if ($i == $pagina): ?>
        <span class="actual"><?= $i ?></span>
      <?php else: ?>
        <a href="?busqueda=<?= urlencode($busqueda) ?>&pagina=<?= $i ?>"><?= $i ?></a>
      <?php endif; ?>
    <?php endfor; ?>

    <?php if ($pagina < $total_paginas): ?>
      <a href="?busqueda=<?= urlencode($busqueda) ?>&pagina=<?= $pagina + 1 ?>">Siguiente &raquo;</a>
    <?php endif; ?>
  </div>
</div>
<br>
<!-- Modal para agregar/editar -->
<div id="modal-comando" style="display: none;">
  <div class="modal-contenido">
    <button id="cerrar-modal">&times;</button>
    <h2 id="modal-titulo">Agregar Comando</h2>

    <form id="form-comando" method="POST" enctype="multipart/form-data" action="../php/guardar_comando.php">
      <input type="hidden" name="id" id="id-comando" />
      <label for="titulo">Título:</label>
      <input type="text" name="titulo" id="titulo-comando" maxlength="100" required />

      <label for="contenido">Descripción:</label>
      <textarea name="contenido" id="contenido-comando" maxlength="100" required></textarea>

      <label for="comando">Comando:</label>
      <textarea type="text" name="comando" id="comando-comando" maxlength="200" required></textarea>

      <label for="imagen">Imagen (opcional):</label>
      <input type="file" name="imagen" accept="image/*" />

      <button type="submit">Guardar</button>
    </form>
  </div>
</div>
  <?php if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], ['admin', 'colaborador'])): ?>
    <!-- Botón flotante que redirige a Perfil.php -->
    <a href="./Perfil.php" class="fab" title="Editar perfil">
        👤
    </a>
  <?php endif; ?>
<script src="../../Manual/js/carrusel.js"></script>
<?php include '../includes/Footer.php'; ?>
  <script src="../js/Perfil.js"></script>

</body>
</html>
