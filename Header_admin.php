<?php
// Incluir control de sesión
include_once __DIR__ . '/session.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Modal Registro Centrado</title>
  <style>
    #registroModal {
      display: none;
      position: fixed;
      z-index: 9999;
      left: 0;
      top: 0;
      width: 100%;
      height: 100vh;
      background-color: rgba(0, 0, 0, 0.6);
      overflow-y: auto;
    }
    #registroModal.active {
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .modal-contenido {
      background: #fff;
      padding: 20px 30px;
      border-radius: 10px;
      width: 350px;
      max-width: 90%;
      box-shadow: 0 0 15px rgba(0,0,0,0.2);
      display: flex;
      flex-direction: column;
      gap: 12px;
      position: relative;
    }
    .modal-contenido .cerrar {
      position: absolute;
      right: 15px;
      top: 10px;
      font-size: 20px;
      cursor: pointer;
    }
    .modal-contenido input,
    .modal-contenido select {
      padding: 8px;
      font-size: 14px;
      border-radius: 4px;
      border: 1px solid #ccc;
    }
    .modal-contenido button[type="submit"] {
      background-color: #2ecc71;
      color: white;
      padding: 10px;
      font-size: 15px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
    #mqe-navbar {
      background-color: #081032;
      padding: 10px 20px;
      color: white;
      display: flex;
      align-items: center;
      gap: 20px;
    }
    .user-logo {
      display: flex;
      align-items: center;
      gap: 20px;
    }
    .logo img {
      height: 60px;
      width: 60px;
      object-fit: contain;
    }
    nav ul {
      list-style: none;
      display: flex;
      gap: 15px;
      padding: 0;
      margin: 0;
    }
    nav ul li a {
      color: white;
      text-decoration: none;
      font-size: 18px;
    }
  </style>
</head>
<body>

<header id="mqe-navbar">
  <div class="user-logo">
    <div class="logo">
      <img src="../../Manual/img/MQE_LOGOPUTO.png" alt="MQE Logo">
    </div>
    <div class="user-info" style="color: white;">
      Bienvenido, <?= htmlspecialchars($nombre) ?> (<?= htmlspecialchars($departamento) ?>)
    </div>
  </div>

  <nav>
    <ul>
      <li><a class="fa-solid fa-house" href="../../Manual/index.php" title="Inicio"></a></li>
      <li><a href="../../Manual/webpages/Pruebas.php">Pruebas</a></li>
      <li><a href="../../Manual/webpages/Generation.php" hidden>Generación</a></li>
      <li><a href="../../Manual/webpages/Process.php">Procesos</a></li>
      <li><a href="../../Manual/webpages/Concept.php">Conceptos</a></li>
      <li><a href="../../Manual/webpages/Commands.php">Comandos</a></li>
      <li><a class="fa-solid fa-users" href="#" id="btn-registro" title="Registro de usuarios"></a></li>
      <li><a class="fa-solid fa-users-rectangle" href="../../Manual/webpages/users.php" id="btn-usuarios" title="Usuarios"></a></li>
      <li><a class="fa-solid fa-bell" href="#" id="btn-notificacion" title="Notificaciones"></a></li>
      <li><a class="fa-solid fa-right-from-bracket" href="../../Manual/php/Logout.php" title="Cerrar sesión"></a></li>
    </ul>
  </nav>

  <div id="registroModal" class="modal">
    <div class="modal-contenido">
      <span class="cerrar" id="cerrarRegistro">&times;</span>
      <h2>Registro de Usuario</h2>
      <form id="formRegistro">
        <input type="text" name="noreloj" placeholder="Número de Reloj" required />
        <input type="text" name="nombre" placeholder="Nombre" required />
        <input type="text" name="apellido" placeholder="Apellido" required />
        <select name="rol" required>
          <option value="" disabled selected>Selecciona un rol</option>
          <option value="admin">Admin</option>
          <option value="colaborador">Colaborador</option>
          <option value="publico">Público</option>
        </select>
        <input type="text" name="departamento" placeholder="Departamento" required />
        <input type="password" name="contrasena" placeholder="Contraseña" required />
        <button type="submit">Registrar</button>
      </form>
      <div id="mensajeRegistro"></div>
    </div>
  </div>
</header>

<div id="notificacionesPopup" style="
  display: none; 
  position: fixed; 
  top: 70px; 
  right: 20px; 
  background: white; 
  border: 1px solid #ccc; 
  padding: 15px; 
  width: 300px; 
  max-height: 400px; 
  overflow-y: auto; 
  box-shadow: 0 2px 10px rgba(0,0,0,0.2); 
  z-index: 10000;
"></div>

<script>
  const modalRegistro = document.getElementById('registroModal');
  const btnAbrir = document.getElementById('btn-registro');
  const btnCerrar = document.getElementById('cerrarRegistro');

  btnAbrir.addEventListener('click', e => {
    e.preventDefault();
    modalRegistro.classList.add('active');
  });

  btnCerrar.addEventListener('click', () => {
    modalRegistro.classList.remove('active');
  });

  modalRegistro.addEventListener('click', (e) => {
    if (e.target === modalRegistro) {
      modalRegistro.classList.remove('active');
    }
  });

document.getElementById('formRegistro').addEventListener('submit', function(e) {
  e.preventDefault();
  const formData = new FormData(this);
  fetch('../php/Register_Function.php', {  
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    const msg = document.getElementById('mensajeRegistro');
    msg.style.color = data.success ? 'green' : 'red';
    msg.textContent = data.mensaje || 'Error en el registro';
    if (data.success) {
      this.reset();
      setTimeout(() => {
        modalRegistro.classList.remove('active');
      }, 1500);
    }
  })
  .catch(err => {
    console.error(err);
    document.getElementById('mensajeRegistro').textContent = 'Error en el servidor';
  });
});


const btnNotificacion = document.getElementById('btn-notificacion');
const popupNotif = document.getElementById('notificacionesPopup');

btnNotificacion.addEventListener('click', () => {
  if (popupNotif.style.display === 'none' || popupNotif.style.display === '') {
    fetch('../../Manual/php/obtener_notificaciones.php')
      .then(res => res.json())
      .then(data => {
        if (data.error) {
          popupNotif.innerHTML = `<p style="color:red;">${data.error}</p>`;
          popupNotif.style.display = 'block';
          return;
        }
        popupNotif.innerHTML = data.length
          ? data.map(n => `
              <div style="border-bottom: 1px solid #ccc; padding: 8px 0;">
                <strong>${n.accion}</strong><br>
                Usuario: ${n.usuario_afectado}<br>
                Por: ${n.autor}<br>
                <small>${new Date(n.fecha).toLocaleString()}</small>
              </div>`).join('')
          : '<p>No hay notificaciones.</p>';
        popupNotif.style.display = 'block';
      })
      .catch(err => {
        console.error(err);
        popupNotif.innerHTML = '<p style="color:red;">Error al cargar notificaciones.</p>';
        popupNotif.style.display = 'block';
      });
  } else {
    popupNotif.style.display = 'none';
  }
});

document.addEventListener('click', (e) => {
  if (!popupNotif.contains(e.target) && e.target !== btnNotificacion) {
    popupNotif.style.display = 'none';
  }
});

</script>
</body>
</html>
