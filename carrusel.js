const modal = document.getElementById('modal-comando');
const modalTitulo = document.getElementById('modal-titulo');
const cerrarModalBtn = document.getElementById('cerrar-modal');
const formComando = document.getElementById('form-comando');

// Abrir modal para agregar nuevo comando
document.getElementById('btn-agregar').addEventListener('click', () => {
  modalTitulo.textContent = 'Agregar Comando';
  formComando.reset();
  formComando.action = '../php/guardar_comando.php';

  // Eliminar input oculto de id si existe (para no enviar id al agregar)
  const inputId = formComando.querySelector('input[name="id"]');
  if (inputId) {
    inputId.remove();
  }

  modal.style.display = 'flex';
});

function copiarComando(btn) {
  const texto = btn.parentElement.innerText.trim();
  navigator.clipboard.writeText(texto).then(() => {
    btn.textContent = "✅";
    setTimeout(() => btn.textContent = "📋", 1500);
  });
}

// Cerrar modal al hacer clic en la X
cerrarModalBtn.addEventListener('click', () => {
  modal.style.display = 'none';
});

// Cerrar modal al hacer clic fuera del contenido
window.addEventListener('click', (event) => {
  if (event.target === modal) {
    modal.style.display = 'none';
  }
});

// Botones editar: abrir modal con datos cargados
document.querySelectorAll('.editar').forEach(btn => {
  btn.addEventListener('click', (e) => {
    const tarjeta = e.target.closest('.tarjeta');
    const id = tarjeta.dataset.id;
    const titulo = tarjeta.dataset.titulo;
    const contenido = tarjeta.dataset.contenido;
    const comando = tarjeta.dataset.comando;

    modalTitulo.textContent = 'Editar Comando';
    formComando.titulo.value = titulo;
    formComando.contenido.value = contenido;
    formComando.comando.value = comando;
    formComando.action = '../php/editar_comando.php';

    // Añade input oculto con id si no existe
    let inputId = formComando.querySelector('input[name="id"]');
    if (!inputId) {
      inputId = document.createElement('input');
      inputId.type = 'hidden';
      inputId.name = 'id';
      formComando.appendChild(inputId);
    }
    inputId.value = id;

    modal.style.display = 'flex';
  });
});

// Botones eliminar: confirmación y redirección
document.querySelectorAll('.eliminar').forEach(btn => {
  btn.addEventListener('click', async () => {
    const tarjeta = btn.closest('.tarjeta');
    const id = tarjeta.dataset.id;

    if (confirm('¿Estás seguro que deseas eliminar este comando?')) {
      try {
        const response = await fetch('../php/eliminar_comando.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: 'id=' + encodeURIComponent(id)
        });

        const resultado = await response.text();
        alert(resultado);

        if (response.ok) {
          tarjeta.remove(); // Elimina la tarjeta del DOM
        }
      } catch (error) {
        alert('Error al eliminar puta: ' + error.message);
      }
    }
  });
});

