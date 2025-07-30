<?php
include '../includes/session.php';
include '../php/DB_connection.php';
include('../includes/load_header.php');

$noreloj = $_SESSION['noreloj'] ?? null;
if (!$noreloj) {
  header("Location: ../index.php");
  exit();
}

$query = $conn->prepare("SELECT * FROM empleados WHERE noreloj = ?");
$query->bind_param("s", $noreloj);
$query->execute();
$result = $query->get_result();
$empleado = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Comandos Técnicos</title>
  <link rel="stylesheet" href="../fonts/fotawesome/css/all.min.css" />
  <link rel="stylesheet" href="../css/Header.css" />
  <link rel="stylesheet" href="../css/register.css" />
  <link rel="stylesheet" href="../css/Commands.css" />
  <link rel="stylesheet" href="../css/Background.css" />
  <link rel="stylesheet" href="../css/Footer.css" />
  <link rel="stylesheet" href="../css/Perfil.css" />
  <link rel="stylesheet" href="../css/Comments.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>
<body>

<div class="perfil-container">
  <div class="perfil-header">
    <div class="perfil-header-left">
      <img src="../uploads/<?php echo htmlspecialchars($empleado['imagen'] ?? 'defaul.webp'); ?>" alt="Foto de perfil">
      <div>
        <h2><?php echo htmlspecialchars($empleado['nombre']) . ' ' . htmlspecialchars($empleado['apellido']); ?></h2>
        <p><strong>Rol:</strong> <?php echo htmlspecialchars($empleado['rol']); ?></p>
      </div>
    </div>
    <div class="acciones">
      <button onclick="abrirModalEdit()"><i class="fas fa-edit"></i> Editar</button>
      <button onclick="abrirModalFoto()"><i class="fas fa-camera"></i> Cambiar Foto</button>
    </div>
  </div>

  <div class="perfil-datos">
    <div class="campo"><label>Número de reloj</label><span><?php echo $empleado['noreloj']; ?></span></div>
    <div class="campo"><label>Departamento</label><span><?php echo $empleado['departamento']; ?></span></div>
    <div class="campo"><label>Correo</label><span><?php echo $empleado['correo'] ?? 'No registrado'; ?></span></div>
    <div class="campo"><label>Teléfono</label><span><?php echo $empleado['telefono'] ?? 'No registrado'; ?></span></div>
    <div class="campo"><label>Idioma</label><span><?php echo $empleado['idioma'] ?? 'Español'; ?></span></div>
    <div class="campo"><label>Puesto</label><span><?php echo $empleado['puesto'] ?? 'No registrado'; ?></span></div>
    <div class="campo"><label>Jefe Directo</label><span><?php echo $empleado['jefe_directo'] ?? 'No registrado'; ?></span></div>
    <div class="campo"><label>Fecha de creación</label><span><?php echo date('d/m/Y H:i', strtotime($empleado['creado_en'])); ?></span></div>
  </div>
</div>

<!-- Modal editar -->
<div class="modal" id="modalEdit">
  <div class="modal-content2">
    <form action="../php/actualizar_perfil.php" method="POST" style="position: relative;">
      <span class="close-btn" onclick="cerrarModalEdit()">×</span>
      <h3>Editar Perfil</h3>

      <input type="hidden" name="noreloj" value="<?php echo $empleado['noreloj']; ?>">

      <label>Nombre</label>
      <input type="text" name="nombre" value="<?php echo $empleado['nombre']; ?>" disabled>

      <label>Apellido</label>
      <input type="text" name="apellido" value="<?php echo $empleado['apellido']; ?>" disabled>

      <label>Teléfono</label>
      <input type="text" name="telefono" placeholder="Teléfono" value="<?php echo $empleado['telefono'] ?? ''; ?>">

      <label>Correo</label>
      <input type="email" name="correo" placeholder="Correo" value="<?php echo $empleado['correo'] ?? ''; ?>">

      <label>Puesto</label>
      <input type="text" name="puesto" placeholder="Puesto" value="<?php echo $empleado['puesto'] ?? ''; ?>">

      <label>Jefe Directo</label>
      <input type="text" value="<?php echo $empleado['jefe_directo'] ?? ''; ?>" readonly disabled>

      <label>Idioma</label>
      <select name="idioma">
        <option value="Español" <?php if(($empleado['idioma'] ?? '') === 'Español') echo 'selected'; ?>>Español</option>
        <option value="Inglés" <?php if(($empleado['idioma'] ?? '') === 'Inglés') echo 'selected'; ?>>Inglés</option>
      </select>

      <button type="submit">Guardar</button>
    </form>
  </div>
</div>


<!-- Modal cambiar foto -->
<div class="modal" id="modalFoto">
  <div class="modal-content">
    <form action="../php/actualizar_foto.php" method="POST" enctype="multipart/form-data">
      <h3>Cambiar Foto</h3>
      <input type="hidden" name="noreloj" value="<?php echo $empleado['noreloj']; ?>">
      <input type="hidden" name="imagen_actual" value="<?php echo $empleado['imagen'] ?? 'default.webp'; ?>">
      <input type="file" name="imagen" accept="image/*" required>
      <button type="submit">Subir</button>
      <button type="button" onclick="cerrarModalFoto()">Cancelar</button>
    </form>
  </div>
</div>
<?php include '../includes/Footer.php'; ?>
<script src="../js/Perfil.js"></script></body>
</html>
