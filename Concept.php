<?php
include '../php/DB_connection.php'; 
include '../includes/session.php';
include_once '../includes/load_header.php';

$tiene_permiso = isset($_SESSION['rol']) && in_array($_SESSION['rol'], ['admin', 'colaborador']);
$noreloj_usuario = $_SESSION['noreloj'] ?? '';

$busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
$busqueda = mysqli_real_escape_string($conn, $busqueda);

$sql = "SELECT * FROM conceptos";
if ($busqueda !== '') {
    $sql .= " WHERE titulo LIKE '%$busqueda%' OR contenido LIKE '%$busqueda%'";
}
$resultado = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Conceptos</title>
  <link rel="stylesheet" href="../fonts/fotawesome/css/all.min.css" />
  <link rel="stylesheet" href="../css/Header.css" />
  <link rel="stylesheet" href="../css/Perfil.css">
  <link rel="stylesheet" href="../css/register.css" />
  <link rel="stylesheet" href="../css/Concept.css" />
  <link rel="stylesheet" href="../css/Background.css" />
  <link rel="stylesheet" href="../../Manual/css/Footer.css" />
  <link rel="stylesheet" href="../css/Comments.css" />
</head>
<body>

<div class="contenedor">
    <h1>Conceptos Técnicos</h1>

    <!-- Buscador -->
    <div style="display: flex; justify-content: flex-end; margin-bottom: 20px;">
        <form method="GET" action="../webpages/Concept.php" style="display: flex; gap: 10px; align-items: center;">
            <input 
              type="text" 
              name="buscar" 
              placeholder="Buscar por título o contenido..." 
              value="<?= htmlspecialchars($busqueda) ?>" 
              style="padding:8px; width: 300px; border-radius:4px; border:1px solid #ccc;"
            />
            <button type="submit" style="padding:8px 12px; border:none; background-color:#007BFF; color:#fff; border-radius:4px; cursor:pointer;">
              Buscar
            </button>
        </form>
    </div>

    <!-- Botón agregar -->
    <?php if ($tiene_permiso): ?>
        <button class="btn-agregar" onclick="abrirModalAgregar()">Agregar Concepto</button>
    <?php endif; ?>

    <!-- Tarjetas de conceptos -->
    <div class="grid">
        <?php if (mysqli_num_rows($resultado) > 0): ?>
            <?php while ($fila = mysqli_fetch_assoc($resultado)): ?>
                <div class="flip-card">
                  <div class="flip-card-inner">

                    <!-- Frente -->
                    <div class="flip-card-front">
                    <?php if (!empty($fila['imagen'])): ?>
                        <img src="../uploads/<?= htmlspecialchars($fila['imagen']) ?>" alt="Imagen de <?= htmlspecialchars($fila['titulo']) ?>">
                    <?php endif; ?>
                    <h3><?= htmlspecialchars($fila['titulo']) ?></h3>
                    <?php if (!empty($fila['subtitulo'])): ?>
                        <p class="subtitulo"><?= htmlspecialchars($fila['subtitulo']) ?></p>
                    <?php endif; ?>
                    </div>

                    <!-- Reverso -->
                    <div class="flip-card-back">
                      <h3><?= htmlspecialchars($fila['subtitulo']) ?></h3>
                      <p><?= htmlspecialchars($fila['contenido']) ?></p>

                      <?php if ($tiene_permiso): ?>
                        <div class="acciones">
                          <button onclick="abrirModalEditar(<?= $fila['id'] ?>, '<?= addslashes($fila['titulo']) ?>', '<?= addslashes($fila['contenido']) ?>', '<?= addslashes($fila['imagen']) ?>')">Editar</button>
                          <button onclick="confirmarEliminar(<?= $fila['id'] ?>)">Eliminar</button>
                        </div>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No se encontraron conceptos que coincidan con la búsqueda.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Agregar -->
<div id="modalAgregar" class="modal">
  <div class="modal-contenido">
    <span class="cerrar" onclick="cerrarModal('modalAgregar')">&times;</span>
    <h2>Agregar Concepto</h2>
    <form action="../PHP/Agregar_Concepto.php" method="POST" enctype="multipart/form-data">
        <input type="text" name="titulo" placeholder="Título" required>
        <input type="text" name="subtitulo" placeholder="Subtítulo (opcional)">
        <textarea name="contenido" placeholder="Contenido" required></textarea>
        <input type="file" name="imagen" accept="image/*">
        <div class="botones-popup">
            <button type="submit">Guardar</button>
        </div>
    </form>
  </div>
</div>


<!-- Modal Editar -->
<div id="modalEditar" class="modal">
  <div class="modal-contenido">
    <span class="cerrar" onclick="cerrarModal('modalEditar')">&times;</span>
    <h2>Editar Concepto</h2>
    <form action="../PHP/Editar_Concepto.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" id="edit-id">
        <input type="text" name="titulo" id="edit-titulo" required>
        <textarea name="contenido" id="edit-contenido" required></textarea>

        <div id="imagen-actual-container" style="margin-bottom: 10px; text-align:center; display:none;">
            <img id="imagen-actual" src="" alt="Imagen actual" style="max-width: 100%; max-height: 150px; border-radius: 8px;"/>
            <br/>
            <label>
                <input type="checkbox" name="eliminar_imagen" value="1">
                Eliminar imagen actual
            </label>
        </div>

        <label for="imagen-nueva">Subir nueva imagen (opcional):</label>
        <input type="file" name="imagen" id="imagen-nueva" accept="image/*">
        <div class="botones-popup">
            <button type="submit">Actualizar</button>
        </div>
    </form>
  </div>
</div>
  <?php if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], ['admin', 'colaborador'])): ?>
    <!-- Botón flotante que redirige a Perfil.php -->
    <a href="./Perfil.php" class="fab" title="Editar perfil">
        👤
    </a>
  <?php endif; ?>
<?php include '../includes/Footer.php'; ?>
<script src="../js/Conceptos.js"></script>
  <script src="../js/Perfil.js"></script>

</body>
</html>
