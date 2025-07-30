const quill = new Quill('#editor', {
  theme: 'snow',
  placeholder: 'Describe el proceso...',
  modules: {
    toolbar: [
      ['bold', 'italic', 'underline'],
      [{ list: 'ordered' }, { list: 'bullet' }],
      ['link', 'code-block']
    ]
  }
});

// Mostrar input correcto según tipo de archivo
function mostrarInputArchivo() {
  const tipo = document.getElementById('tipoArchivo').value;
  document.getElementById('campoArchivoPDF').style.display = tipo === 'pdf' ? 'block' : 'none';
  document.getElementById('campoArchivoLink').style.display = tipo === 'link' ? 'block' : 'none';
}

// Mostrar procesos según generación
function showGeneration(gen) {
  fetch(`../php/obtener_procesos_generacion.php?gen=${gen}`)
    .then(res => res.json())
    .then(data => {
      const container = document.getElementById('procesos-container');
      container.innerHTML = '';

      if (!data.length) {
        container.innerHTML = `<p>No hay procesos registrados para esta generación.</p>`;
        return;
      }

      data.forEach(p => {
        const card = document.createElement('div');
        card.className = 'process-card';
        card.innerHTML = `
          <h4>${p.title}</h4>
          <div>${p.description}</div>
          <small>Autor: ${p.author} | Fecha: ${p.reg_date}</small>
        `;
        container.appendChild(card);
      });
    })
    .catch(err => {
      console.error('Error cargando procesos:', err);
    });
}

// Abrir y cerrar formulario
function abrirFormularioProceso() {
  document.getElementById('formularioProceso').style.display = 'block';
}
function cerrarFormularioProceso() {
  document.getElementById('formularioProceso').style.display = 'none';
}

// Buscar proceso
function buscarProceso() {
  const texto = document.getElementById('searchInput').value.toLowerCase();
  const cards = document.querySelectorAll('.process-card');
  cards.forEach(card => {
    const titulo = card.querySelector('h4').textContent.toLowerCase();
    card.style.display = titulo.includes(texto) ? 'block' : 'none';
  });
}

// Guardar proceso
document.getElementById('procesoForm').addEventListener('submit', function (e) {
  e.preventDefault();
  document.getElementById('descripcion').value = quill.root.innerHTML;

  const formData = new FormData(this);

  fetch('../php/guardar_proceso_generacion.php', {
    method: 'POST',
    body: formData
  })
    .then(res => res.text())
    .then(data => {
      alert('✅ Proceso guardado');
      cerrarFormularioProceso();
      showGeneration(formData.get('generacion'));
    })
    .catch(err => {
      console.error(err);
      alert('❌ Error al guardar proceso');
    });
});
