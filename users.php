<?php
include '../php/DB_connection.php';
include '../includes/session.php';
include_once '../includes/load_header.php';

$tiene_permiso = isset($_SESSION['rol']) && in_array($_SESSION['rol'], ['admin', 'colaborador']);
if (!$tiene_permiso) {
    echo "<h2 style='color: red; text-align:center;'>Acceso denegado</h2>";
    exit;
}

$busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
$busqueda = mysqli_real_escape_string($conn, $busqueda);

$sql = "SELECT * FROM empleados";
if ($busqueda !== '') {
    $sql .= " WHERE nombre LIKE '%$busqueda%' OR apellido LIKE '%$busqueda%' OR noreloj LIKE '%$busqueda%' OR departamento LIKE '%$busqueda%'";
}
$resultado = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Usuarios Registrados</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" href="../css/Header.css">
  <link rel="stylesheet" href="../css/Footer.css">
  <link rel="stylesheet" href="../css/Background.css">
  <link rel="stylesheet" href="../css/register.css">
  <link rel="stylesheet" href="../fonts/fotawesome/css/all.min.css">
  <style>
    body { font-family: Arial, sans-serif; }
    h2 { text-align: center; color: #2c3e50; }
    .buscador { text-align: center; margin: 10px 0 20px; }
    .buscador input[type="text"] {
      padding: 6px; border-radius: 4px; border: 1px solid #ccc; width: 250px;
    }
    table {
      width: 85%; margin: 0 auto; border-collapse: collapse;
      background-color: #fff; box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    th, td {
      border: 1px solid #ccc; padding: 8px; text-align: center; font-size: 14px;
    }
    th { background-color: #2c3e50; color: white; }
    tr:nth-child(even) { background-color: #f2f2f2; }
    .acciones button {
      padding: 5px 8px; margin: 0 2px; border: none;
      border-radius: 4px; font-size: 12px; cursor: pointer;
    }
    .editar { background-color: #3498db; color: white; }
    .eliminar { background-color: #e74c3c; color: white; }
    .imagen-perfil {
      width: 50px; height: 50px; object-fit: cover; border-radius: 50%;
    }
    /* Estilos para inputs del modal para todos los campos */
    #modal-edicion input, #modal-edicion select {
      width: 100%; padding: 8px; margin: 6px 0; box-sizing: border-box;
      border: 1px solid #ccc; border-radius: 4px;
    }
    .fab {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background-color: #00d1b2;
    color: white;
    text-decoration: none;
    border: none;
    border-radius: 50%;
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 26px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
    transition: background 0.3s, transform 0.2s;
    z-index: 999;
  }

  .fab:hover {
    background-color: #1e2a78;
    transform: scale(1.05);
  }
  </style>
</head>
<body>

<h2>Lista de Usuarios Registrados</h2>

<div class="buscador">
  <form method="GET" action="">
    <input type="text" name="buscar" placeholder="Buscar usuario..." value="<?= htmlspecialchars($busqueda) ?>">
  </form>
</div>

<table>
  <thead>
    <tr>
      <th>No. Reloj</th>
      <th>Nombre</th>
      <th>Apellido</th>
      <th>Rol</th>
      <th>Departamento</th>
      <th>Correo</th>
      <th>Teléfono</th>
      <th>Idioma</th>
      <th>Jefe Directo</th>
      <th>Puesto</th>
      <th>Creado En</th>
      <th>Imagen</th>
      <th>Acciones</th>
    </tr>
  </thead>
  <tbody>
    <?php while ($fila = mysqli_fetch_assoc($resultado)): ?>
      <?php
        $imagen = htmlspecialchars($fila['imagen']);
        $ruta_imagen = "../uploads/" . $imagen;
        if (empty($imagen) || !file_exists($ruta_imagen)) {
            $ruta_imagen = "../uploads/default.webp";
        }
      ?>
      <tr>
        <td><?= htmlspecialchars($fila['noreloj']) ?></td>
        <td><?= htmlspecialchars($fila['nombre']) ?></td>
        <td><?= htmlspecialchars($fila['apellido']) ?></td>
        <td><?= htmlspecialchars($fila['rol']) ?></td>
        <td><?= htmlspecialchars($fila['departamento']) ?></td>
        <td><?= htmlspecialchars($fila['correo']) ?></td>
        <td><?= htmlspecialchars($fila['telefono']) ?></td>
        <td><?= htmlspecialchars($fila['idioma']) ?></td>
        <td><?= htmlspecialchars($fila['jefe_directo']) ?></td>
        <td><?= htmlspecialchars($fila['puesto']) ?></td>
        <td><?= htmlspecialchars($fila['creado_en']) ?></td>
        <td><img src="<?= $ruta_imagen ?>" alt="Perfil" class="imagen-perfil"></td>
        <td class="acciones">
          <button class="editar" onclick="abrirPopup('<?= $fila['noreloj'] ?>')">Editar</button>
          <button class="eliminar" onclick="eliminarUsuario('<?= $fila['noreloj'] ?>')">Eliminar</button>
        </td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>
<br>

<!-- Modal para edición -->
<div id="modal-edicion" style="display:none; position:fixed; z-index:9999; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); justify-content:center; align-items:center;">
  <div style="background:white; padding:20px; border-radius:8px; width:350px; max-width:90%; position:relative;">
    <span onclick="cerrarModal()" style="position:absolute; top:10px; right:15px; cursor:pointer;">&times;</span>
    <h3>Editar Usuario</h3>
    <form id="formEditar">
      <input type="hidden" name="noreloj" id="editNoreloj" />
      <input type="text" name="nombre" id="editNombre" placeholder="Nombre" required />
      <input type="text" name="apellido" id="editApellido" placeholder="Apellido" required />
      <input type="text" name="departamento" id="editDepartamento" placeholder="Departamento" required />
      <input type="text" name="correo" id="editCorreo" placeholder="Correo" />
      <input type="text" name="telefono" id="editTelefono" placeholder="Teléfono" />
      <input type="text" name="idioma" id="editIdioma" placeholder="Idioma" />
      <input type="text" name="jefe_directo" id="editJefeDirecto" placeholder="Jefe Directo" />
      <input type="text" name="puesto" id="editPuesto" placeholder="Puesto" />
      <select name="rol" id="editRol" required>
        <option value="admin">Admin</option>
        <option value="colaborador">Colaborador</option>
        <option value="publico">Público</option>
      </select>
      <button type="submit">Guardar Cambios</button>
    </form>
    <div id="msgEditar"></div>
  </div>
</div>

<script>
function cerrarModal() {
  document.getElementById("modal-edicion").style.display = "none";
}

function eliminarUsuario(noreloj) {
  if (confirm('¿Estás seguro de eliminar este usuario?')) {
    fetch('../php/editar_eliminar_usuario.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: new URLSearchParams({
        accion: 'eliminar',
        noreloj: noreloj
      })
    })
    .then(res => res.json())
    .then(data => {
      alert(data.mensaje || 'Sin respuesta del servidor');
      if (data.success) {
        location.reload();
      }
    })
    .catch(err => {
      console.error(err);
      alert('Error al eliminar usuario');
    });
  }
}

function abrirPopup(noreloj) {
  fetch(`../php/editar_eliminar_usuario.php?accion=obtener&noreloj=${encodeURIComponent(noreloj)}`)
    .then(res => res.json())
    .then(data => {
      if (data && !data.error) {
        document.getElementById("editNoreloj").value = data.noreloj;
        document.getElementById("editNombre").value = data.nombre;
        document.getElementById("editApellido").value = data.apellido;
        document.getElementById("editDepartamento").value = data.departamento;
        document.getElementById("editCorreo").value = data.correo || '';
        document.getElementById("editTelefono").value = data.telefono || '';
        document.getElementById("editIdioma").value = data.idioma || '';
        document.getElementById("editJefeDirecto").value = data.jefe_directo || '';
        document.getElementById("editPuesto").value = data.puesto || '';
        document.getElementById("editRol").value = data.rol;
        document.getElementById("modal-edicion").style.display = "flex";
      } else {
        alert(data.error || "No se encontró el usuario.");
      }
    })
    .catch(err => {
      console.error(err);
      alert("Error al obtener usuario.");
    });
}

document.getElementById("formEditar").onsubmit = function (e) {
  e.preventDefault();
  const formData = new FormData(this);
  formData.append('accion', 'actualizar');  // Añadimos acción para el servidor
  fetch("../php/editar_eliminar_usuario.php", {
    method: "POST",
    body: formData
  })
    .then(res => res.json())
    .then(data => {
      const msgDiv = document.getElementById("msgEditar");
      if (data.success) {
        msgDiv.style.color = 'green';
        msgDiv.textContent = data.mensaje;
        setTimeout(() => {
          cerrarModal();
          location.reload();
        }, 1000);
      } else {
        msgDiv.style.color = 'red';
        msgDiv.textContent = data.mensaje || 'Error al guardar.';
      }
    })
    .catch(err => {
      console.error(err);
      document.getElementById("msgEditar").style.color = 'red';
      document.getElementById("msgEditar").textContent = "Error al guardar.";
    });
};
</script>

<?php if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], ['admin', 'colaborador'])): ?>
  <!-- Botón flotante que redirige a Perfil.php -->
  <a href="./Perfil.php" class="fab" title="Editar perfil">👤</a>
<?php endif; ?>

<?php include '../includes/Footer.php'; ?>
</body>
</html>
