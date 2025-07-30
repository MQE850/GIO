<footer class="footer">
  <div class="footer-left">
    <a href=""><i class="fa-solid fa-house"></i> Página Principal</a>
    <a href="../../Manual/webpages/Generation.php">Generaciones</a>
    <a href="../../Manual/webpages/Procesos.php">Procesos</a>
    <a href="../../Manual/webpages/Conceptos.php">Conceptos</a>
    <a href="../../Manual/webpages/Comandos.php">Comandos</a>
  </div>
  <div class="footer-center">
    <button id="btn-comentar">Comenta aquí</button>
  </div>
  <div class="footer-right">
    <p><strong>Contacto:</strong></p>
    <p>Ing. Carlos Rosas - Carlos_Rosas@wiwynn.com</p>
    <p>Ing. Mario Jaquez - Mario_Jaquez@wiwynn.com</p>
  </div>
</footer>

<!-- Modal para comentar -->
<div id="modal-comentario" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h2>Enviar Comentario</h2>
      <form id="form-comentario" method="post" action="../../Manual/includes/guardar_comentario.php">
        <input type="text" name="noreloj" placeholder="Número de Reloj" required>
        <input type="text" name="nombre" placeholder="Nombre" required>
        <select name="tipo" required>
          <option value="" disabled selected>Selecciona un tipo</option>
          <option value="Sugerencia">Sugerencia</option>
          <option value="Error">Error</option>
          <option value="Comentario">Comentario</option>
          <option value="Otro">Otro</option>
        </select>
        <textarea name="mensaje" placeholder="Escribe tu comentario..." required></textarea>
        <button type="submit">Enviar</button>
      </form>
  </div>
</div>

<script src="../../Manual/js/guardar-comentario.js"></script>
