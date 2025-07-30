function abrirModalAgregar() {
  document.getElementById('modalAgregar').style.display = 'block';
}

function abrirModalEditar(id, titulo, contenido, imagen = '') {
  document.getElementById('edit-id').value = id;
  document.getElementById('edit-titulo').value = titulo;
  document.getElementById('edit-contenido').value = contenido;

  const imgContainer = document.getElementById('imagen-actual-container');
  const img = document.getElementById('imagen-actual');

  if (imagen) {
    img.src = '../uploads/' + imagen;
    imgContainer.style.display = 'block';
  } else {
    img.src = '';
    imgContainer.style.display = 'none';
  }

  document.getElementById('modalEditar').style.display = 'block';
}

function cerrarModal(id) {
  document.getElementById(id).style.display = 'none';
}

window.onclick = function(event) {
  ['modalAgregar', 'modalEditar'].forEach(id => {
    const modal = document.getElementById(id);
    if (event.target === modal) {
      modal.style.display = "none";
    }
  });
}

function confirmarEliminar(id) {
  if (confirm("¿Estás seguro de que deseas eliminar este concepto?")) {
    window.location.href = "../PHP/Eliminar_Concepto.php?id=" + id;
  }
}
