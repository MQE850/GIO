<!-- Modal de registro -->
<div id="modal-registro" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; 
  background: rgba(0,0,0,0.6); justify-content:center; align-items:center; z-index:9999; color: #081032;">
  <div style="background:#fff; padding:20px; border-radius:8px; max-width:400px; width:90%; position:relative;">
    <button id="close-registro" style="position:absolute; top:10px; right:10px; cursor:pointer;">&times;</button>
    <h2>Registrar nuevo usuario</h2>
    <form action="..//php/Register_Function.php" method="POST"><br>
      <label for="noreloj">NoReloj:</label>
      <input type="text" name="noreloj" id="noreloj" required><br><br>

      <label for="nombre">Nombre:</label>
      <input type="text" name="nombre" id="nombre" required><br><br>

      <label for="apellido">Apellido:</label>
      <input type="text" name="apellido" id="apellido" required><br><br>

      <label for="rol">Rol (admin, colaborador, publico):</label>
      <input type="text" name="rol" id="rol" required><br><br>

      <label for="departamento">Departamento:</label>
      <input type="text" name="departamento" id="departamento" required><br><br>

      <label for="password">Contraseña:</label>
      <input type="password" name="password" id="password" required><br><br>

      <button type="submit">Registrar</button>
    </form>
  </div>
</div>