const urlProcesos = '../php/obtener_procesos.php';

const estiloTarjetas = `
.process-card {
  background: linear-gradient(135deg,rgb(22, 31, 90),rgb(46, 65, 161));
  color: white;
  border-radius: 16px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
  padding: 16px;
  font-family: 'Segoe UI', sans-serif;
  position: relative;
  height: 130px;
  width: 100%;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  transition: border-color 0.15s ease-in-out, transform 0.2s ease-in-out, box-shadow 0.15s ease-in-out;
}

.process-card:hover {
  border-color: #00d1b2;
  transform: translateY(-2.5px);
  box-shadow: 4px 4px 15px rgba(0.25, 0.25, 0.25, 0.75);
}

.process-card h6 {
  margin: 0;
  font-size: 1.1rem;
  font-weight: bold;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.process-card .info {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 0.85rem;
  background: rgba(0, 0, 0, 0.2);
  padding: 6px 10px;
  border-radius: 10px;
}

.process-card .header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
  margin-bottom: 8px;
}

.process-card .tags {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
}

.process-card .tag {
  background: #00d1b2;
  color: #333;
  border-radius: 999px;
  padding: 2px 8px;
  font-size: 0.75rem;
  font-weight: 500;
  white-space: nowrap;
  max-width: 100px;
  text-overflow: ellipsis;
  overflow: hidden;
}

#processDetailView.show {
  display: block !important;
  animation: fadeSlideIn 0.5s ease forwards;
}

@keyframes fadeSlideIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

.ocultar-otros .col-md-3:not(:first-child),
.ocultar-otros .col-md-9:not(#processDetailView),
.ocultar-otros #pagination {
  display: none !important;
}

.fade-slide-in {
  animation: slideIn 0.3s ease forwards;
}

.fade-slide-out {
  animation: slideOut 0.3s ease forwards;
}

@keyframes slideIn {
  from { opacity: 0; transform: translateX(50px); }
  to { opacity: 1; transform: translateX(0); }
}
@keyframes slideOut {
  from { opacity: 1; transform: translateX(0); }
  to { opacity: 0; transform: translateX(50px); }
}
  #processDetailView {
  border-radius: 16px;
  box-shadow: 0 8px 20px rgba(0,0,0,1.3);
  background: linear-gradient(135deg,rgb(22, 31, 90),rgb(46, 65, 161));
  color: #f1f1f1;
  font-family: 'Segoe UI', sans-serif;
  font-size: 1.2rem;
  line-height: 1.5;
  margin-top: 29px;
  margin-bottom: 25px;
}

#processDetailView h5 {
  font-size: 1.7rem;
  font-weight: bold;
  margin-bottom: 12px;
  margin-top: 12px;
}

#processDetailView p {
  margin-bottom: 6px;
  margin-top: 6px;
  font-size: 0.95rem;
}

#processDetailView .tag {
  background: #00d1b2;
  color: #1d3557;
  padding: 4px 10px;
  border-radius: 999px;
  margin-right: 6px;
  font-weight: 500;
  font-size: 0.8rem;
  margin-top: 6px;
  display: inline-block;
}

#viewDocument a.btn {
  margin-top: 6px;
}

#viewDescription {
  margin-top: 10px;
  padding-top: 10px;
  border-top: 1px solid rgba(255, 255, 255, 0.2);
}
#viewDocument a.btn,
#viewDocumentUrl a.btn {
  display: inline-block;
  width: 100%;
  text-align: center;
  margin-top: 4px;
  background-color: #f1f1f1;
  color: #1d3557;
  font-weight: bold;
  border: none;
  padding: 6px 10px;
  border-radius: 8px;
  transition: background-color 0.2s ease;
}

#viewDocument a.btn:hover,
#viewDocumentUrl a.btn:hover {
  background-color: #dceaff;
}
.tag {
  display: inline-block;
  background-color: #00d1b2;
  color: #333;
  padding: 3px 8px;
  border-radius: 12px;
  font-size: 0.8rem;
  margin-right: 5px;
  margin-bottom: 3px;
}

`;
let procesosSeleccionado = null;

let paginaActual = 1;
let filtroActivo = { generation: [],  
                     tag: '',
                     search: '',
                     orden: '' };

document.head.appendChild(Object.assign(document.createElement('style'), { innerHTML: estiloTarjetas }));

document.addEventListener('DOMContentLoaded', () => {
  let currentProcessId = null; // Guarda el id seleccionado

  // Suponiendo que ya tienes una función que muestra el detalle y llama a esta:
  function mostrarDetalleProceso(proceso) {
    currentProcessId = proceso.id; // asigna el id del proceso actual
    document.getElementById('viewTitle').textContent = proceso.title;
    // más datos...
    document.getElementById('processDetailView').style.display = 'block';
  }

  const editBtn = document.getElementById('editBtn');
  const deleteBtn = document.getElementById('deleteBtn');
  
  editBtn.addEventListener('click', () => {
    if (!currentProcessId) {
      alert('Seleccione un proceso primero');
      return;
    }
    abrirFormularioEdicion(currentProcessId);
  });
  
  deleteBtn.addEventListener('click', () => {
    if (!currentProcessId) {
      alert('Seleccione un proceso primero');
      return;
    }
    if (confirm('¿Está seguro que desea eliminar este proceso?')) {
      eliminarProceso(currentProcessId);
    }
  });

  function abrirFormularioEdicion(id) {
    // Aquí muestras el formulario y cargas los datos para edición
    document.getElementById('processDetailView').style.display = 'none';
    document.getElementById('processFormContainer').style.display = 'block';
    // Aquí deberías cargar los datos en el formulario, por ejemplo:
    // fetch o buscar en tus datos el proceso con este id y llenar inputs
    console.log('Abrir edición para id:', id);
  }

  function eliminarProceso(id) {
    fetch(`../php/eliminar_proceso.php?id=${id}`, {
      method: 'GET'
    })
    .then(res => res.json())
    .then(data => {
      if(data.success) {
        alert('Proceso eliminado con éxito');
        // Actualiza la lista
        location.reload();
      } else {
        alert('Error al eliminar: ' + data.message);
      }
    })
    .catch(e => {
      console.error(e);
      alert('Error al eliminar proceso');
    });
  }

  // Exponer la función mostrarDetalleProceso para que se pueda llamar desde otros scripts
  window.mostrarDetalleProceso = mostrarDetalleProceso;
});


document.getElementById('searchInput')?.addEventListener('input', e => {
  filtroActivo.search = e.target.value.trim();
  paginaActual = 1;
  obtenerProcesos();
});

document.getElementById('sortSelect')?.addEventListener('change', e => {
  filtroActivo.orden = e.target.value;
  paginaActual = 1;
  obtenerProcesos();
});

document.getElementById('btnLimpiarFiltros')?.addEventListener('click', () => {
  filtroActivo = { generation: '', tag: '', search: '', orden: '' };
  document.getElementById('searchInput').value = '';
  document.getElementById('sortSelect').value = '';
  document.querySelectorAll('#generationFilters button').forEach(b => b.classList.remove('active'));
  obtenerProcesos();
});

async function obtenerProcesos(pagina = 1) {
  const params = new URLSearchParams({
    page: pagina,
    search: filtroActivo.search,
    generation: filtroActivo.generation,
    tag: filtroActivo.tag,
    sort: filtroActivo.orden
  });

  try {
    const response = await fetch(`${urlProcesos}?${params.toString()}`);
    if (!response.ok) throw new Error('HTTP ' + response.status);
    const data = await response.json();

    if (data.error) {
      console.error('Error en respuesta:', data.error);
      return;
    }

    mostrarProcesos(data.procesos);
    crearPaginacion(data.page, data.pages);
  } catch (error) {
    console.error('Error al obtener procesos:', error);
  }
}


function mostrarProcesos(procesos) {
  const container = document.getElementById('processList');
  if (!container) return;

  container.innerHTML = '';

  procesos.forEach(p => {
    const col = document.createElement('div');
    col.className = 'col-12 col-md-6 mb-4 d-flex justify-content-center';

    const card = document.createElement('div');
    card.className = 'process-card';
    card.setAttribute('data-id', p.process_id);

    const allTags = (p.tag || '')
      .split(',')
      .map(t => t.trim())
      .filter(t => t !== '');

    const firstThreeTags = allTags.slice(0, 2);

    const tagsHTML = firstThreeTags
      .map(t => `<span class="tag">${t}</span>`)
      .join('');

    const generacionesFormateadas = (p.generation || '')
      .split(',')
      .map(g => g.trim())
      .join(' | ');

    const fecha = p.mod_date ? new Date(p.mod_date) : null;
    const fechaFormateada = fecha
      ? fecha.toLocaleDateString('es-MX', { day: '2-digit', month: '2-digit', year: 'numeric' })
      : 'Sin fecha';

    card.innerHTML = `
      <div class="header">
        <h6>${p.title || 'Sin título'}</h6>
        <div class="tags">${tagsHTML}</div>
      </div>
      <div class="info">
        <span>${generacionesFormateadas}</span>
        <span>${p.contributor || 'Sin autor'}</span>
        <span>${fechaFormateada}</span>
      </div>
    `;

    card.addEventListener('click', () => {
      procesosSeleccionado = p;
      mostrarDetalleProceso(p);
    });

    col.appendChild(card);
    container.appendChild(col);
  });

  document.getElementById('editBtn').onclick = () => {
    if (procesosSeleccionado) editarProceso(procesosSeleccionado);
  };

  document.getElementById('deleteBtn').onclick = () => {
    if (procesosSeleccionado) eliminarProceso();
  };
}
function editarProceso(proceso) {
  if (!proceso) return;

  document.getElementById('processDetailView').style.display = 'none';
  document.getElementById('processFormContainer').style.display = 'block';

  // Asumiendo que tu formulario tiene estos campos:
  document.getElementById('formTitle').value = proceso.title || '';
  document.getElementById('formAuthor').value = proceso.author || '';
  document.getElementById('formDescription').value = proceso.description || '';
  document.getElementById('formGeneration').value = proceso.generation || '';
  document.getElementById('formTag').value = proceso.tag || '';
  document.getElementById('formExecTime').value = proceso.exec_time || '';
  document.getElementById('formProcessId').value = proceso.process_id; // Campo hidden
}

function eliminarProceso() {
  if (!procesosSeleccionado || !procesosSeleccionado.process_id) {
    alert('⚠️ No hay proceso seleccionado para eliminar.');
    return;
  }

  const confirmModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
  document.getElementById('confirmDeleteText').textContent = `¿Estás seguro de eliminar el proceso "${procesosSeleccionado.title}"?`;
  confirmModal.show();

  // Evitar duplicación de evento
  const confirmBtn = document.getElementById('btnConfirmDelete');
  const nuevoBoton = confirmBtn.cloneNode(true);
  confirmBtn.parentNode.replaceChild(nuevoBoton, confirmBtn);

  nuevoBoton.addEventListener('click', () => {
    fetch(`../php/eliminar_prueba.php?id=${encodeURIComponent(procesosSeleccionado.process_id)}`, {
      method: 'GET'
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        bootstrap.Modal.getInstance(document.getElementById('confirmDeleteModal')).hide();
        location.reload();
      } else {
        alert('⚠️ ' + (data.error || 'Error al eliminar el proceso.'));
      }
    })
    .catch(err => {
      console.error('Error eliminando proceso:', err);
      alert('❌ Error de red al eliminar proceso.');
    });
  });
}

function mostrarToast(mensaje, tipo = 'info') {
  const contenedor = document.getElementById('toastContainer');
  if (!contenedor) {
    console.warn('No se encontró contenedor para toasts');
    alert(mensaje);
    return;
  }

  const toast = document.createElement('div');
  toast.className = `toast align-items-center text-bg-${tipo} border-0 show`;
  toast.setAttribute('role', 'alert');
  toast.setAttribute('aria-live', 'assertive');
  toast.setAttribute('aria-atomic', 'true');
  toast.innerHTML = `
    <div class="d-flex">
      <div class="toast-body">${mensaje}</div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button>
    </div>
  `;

  contenedor.appendChild(toast);

  setTimeout(() => {
    toast.remove();
  }, 4000);
}


function inicializarFiltrosGeneracion() {
  const contenedor = document.getElementById('generationFilters');
  if (!contenedor) return;

  fetch(urlProcesos)
    .then(resp => resp.json())
    .then(data => {
      const generaciones = [...new Set(
        data.procesos.flatMap(p =>
          (p.generation || '').split(',').map(g => g.trim())
        )
      )].sort();

      generaciones.forEach(g => {
        const btn = document.createElement('button');
        btn.textContent = g;
        btn.dataset.gen = g;

        btn.onclick = () => {
          const index = filtroActivo.generation.indexOf(g);
          if (index >= 0) {
            filtroActivo.generation.splice(index, 1);
          } else {
            filtroActivo.generation.push(g);
          }
          actualizarResaltadoGeneraciones();
          paginaActual = 1;
          obtenerProcesos();
        };

        contenedor.appendChild(btn);
      });
    });
}

function generarFiltrosGeneracion(proceso) {
  const contenedor = document.getElementById('generationFilters');
  if (!contenedor) return;

  contenedor.innerHTML = '';

  const generaciones = [...new Set(
    proceso.flatMap(p =>
      (p.generation || '').split(',').map(g => g.trim())
    )
  )].sort();

  generaciones.forEach(g => {
    const btn = document.createElement('button');
    btn.textContent = g;
    btn.className = 'btn btn-sm btn-outline-light generation-btn';
    btn.dataset.gen = g;


    btn.onclick = () => {
      const index = filtroActivo.generation.indexOf(g);
      if (index >= 0) {
        filtroActivo.generation.splice(index, 1);
      } else {
        filtroActivo.generation.push(g);
      }
      actualizarResaltadoGeneraciones();
      paginaActual = 1;
      obtenerProcesos();
    };

    contenedor.appendChild(btn);
  });

  actualizarResaltadoGeneraciones(); // para reflejar botones activos actuales
}

function actualizarResaltadoGeneraciones() {
  document.querySelectorAll('#generationFilters button').forEach(btn => {
    const gen = btn.dataset.gen;
    if (filtroActivo.generation.includes(gen)) {
      btn.classList.add('active');
    } else {
      btn.classList.remove('active');
    }
  });
}

// Inicializamos Quill en modo edición cuando sea necesario
let quillEditor;  

// Función completa para mostrar detalle del proceso (única)
function mostrarDetalleProceso(proceso) {
  const detailView = document.getElementById('processDetailView');
  const mainContent = document.getElementById('mainContent');
  if (!detailView || !mainContent) return;

  const editBtn = document.getElementById('editBtn');
  const deleteBtn = document.getElementById('deleteBtn');

  // Documentos URL y archivo
  const urlContainer = document.getElementById('viewDocumentUrl');
  urlContainer.innerHTML = '';
  if (proceso.document_url && proceso.document_url.trim() !== '') {
    const enlaceURL = document.createElement('a');
    enlaceURL.href = proceso.document_url;
    enlaceURL.target = '_blank';
    enlaceURL.className = 'btn btn-outline-secondary btn-sm';
    enlaceURL.textContent = '🔗 Acceder al URL';
    urlContainer.appendChild(enlaceURL);
  } else {
    urlContainer.innerHTML = '<span class="text-muted"></span>';
  }

  const docContainer = document.getElementById('viewDocument');
  docContainer.innerHTML = '';
  if (proceso.document && proceso.document.trim() !== '') {
    const enlace = document.createElement('a');
    enlace.href = '../uploads/' + proceso.document;
    enlace.target = '_blank';
    enlace.className = 'btn btn-outline-secondary btn-sm';
    enlace.textContent = '📄 Ver Documento';
    docContainer.appendChild(enlace);
  } else {
    docContainer.innerHTML = '<span class="text-muted" style="color: #fff !important;">📄 No disponible</span>';
  }

  // Control de visibilidad de botones según rol y autor
  editBtn.style.display = 'none';
  deleteBtn.style.display = 'none';
  if (window.currentUser?.rol === 'admin') {
    editBtn.style.display = 'inline-block';
    deleteBtn.style.display = 'inline-block';
  } else if (
    window.currentUser?.rol === 'colaborador' &&
    proceso.author === window.currentUser.nombre
  ) {
    editBtn.style.display = 'inline-block';
    deleteBtn.style.display = 'inline-block';
  }

  // Asignar evento editar (enlace a función de edición)
  editBtn.onclick = () => editarProceso(proceso);

  // Mostrar datos básicos
  document.getElementById('viewTitle').textContent = proceso.title || 'Sin título';
  document.getElementById('viewGeneration').textContent = 'Generación: ' + (proceso.generation || 'N/A');
  document.getElementById('viewAuthor').textContent = 'Autor: ' + (proceso.author || 'N/A');
  document.getElementById('viewcontributor').textContent = 'Contributor: ' + (proceso.contributor || 'N/A');

  const fechaMod = proceso.mod_date
    ? new Date(proceso.mod_date).toLocaleDateString('es-MX', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
      })
    : 'Sin fecha';
  document.getElementById('viewMDate').textContent = 'Fecha: ' + fechaMod;

  const fechaReg = proceso.reg_date
    ? new Date(proceso.reg_date).toLocaleDateString('es-MX', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
      })
    : 'Sin fecha';
  document.getElementById('viewDate').textContent = 'Fecha: ' + fechaReg;

  document.getElementById('viewExecTime').textContent = 'Duración: ' + (proceso.exec_time || 'N/A') + ' minutos';

  document.getElementById('viewDescription').innerHTML = proceso.description || '<em>Sin descripción</em>';

  // Mostrar etiquetas
  const tagsContainer = document.getElementById('viewTags');
  tagsContainer.innerHTML = '';
  (proceso.tag || '').split(',').forEach(tag => {
    if (tag.trim()) {
      const span = document.createElement('span');
      span.className = 'tag me-1';
      span.textContent = tag.trim();
      tagsContainer.appendChild(span);
    }
  });

  // Mostrar vista detalle
  mainContent.classList.add('ocultar-otros');
  detailView.style.display = 'block';
  detailView.classList.remove('fade-slide-out');
  void detailView.offsetWidth; // reinicia animación
  detailView.classList.add('fade-slide-in');
  detailView.scrollIntoView({ behavior: 'smooth' });

  // Botón cerrar detalle
  document.getElementById('closeViewBtn').onclick = () => {
    detailView.classList.remove('fade-slide-in');
    detailView.classList.add('fade-slide-out');
    setTimeout(() => {
      detailView.style.display = 'none';
      detailView.classList.remove('fade-slide-out');
      mainContent.classList.remove('ocultar-otros');
      procesosSeleccionado = null;
    }, 300);
  };

  // Actualizamos variable global
  procesosSeleccionado = proceso;
}

function crearTarjetaProceso(proceso) {
  const col = document.createElement('div');
  col.className = 'col-md-4 mb-3';

  const card = document.createElement('div');
  card.className = 'card h-100 shadow-sm';

  const cardBody = document.createElement('div');
  cardBody.className = 'card-body';

  // Título
  const title = document.createElement('h5');
  title.className = 'card-title mb-2';
  title.textContent = proceso.title || 'Sin título';
  cardBody.appendChild(title);

  // Documentos
  const docGroup = document.createElement('div');

  if (proceso.document) {
    const docLink = document.createElement('a');
    docLink.href = `ruta/a/tu/carpeta/documentos/${proceso.document}`; 
    docLink.target = '_blank';
    docLink.className = 'd-block mb-1 text-decoration-none';

    docLink.innerHTML = `<i class="bi bi-paperclip me-1"></i> Descargar documento`;
    docGroup.appendChild(docLink);
  }

  if (proceso.documentURL) {
    const urlLink = document.createElement('a');
    urlLink.href = proceso.documentURL;
    urlLink.target = '_blank';
    urlLink.className = 'd-block text-decoration-none';

    urlLink.innerHTML = `<i class="bi bi-link-45deg me-1"></i> Ver en línea`;
    docGroup.appendChild(urlLink);
  }

  if (!proceso.document && !proceso.documentURL) {
    const noDoc = document.createElement('p');
    noDoc.textContent = 'Sin documento disponible';
    noDoc.className = 'text-muted';
    docGroup.appendChild(noDoc);
  }

  cardBody.appendChild(docGroup);
  card.appendChild(cardBody);
  col.appendChild(card);

  return col;
}
document.getElementById('formProceso').addEventListener('submit', function(e) {
  e.preventDefault();

  const formData = new FormData(this);

  fetch('../php/actualizar_proceso.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      alert('✅ Proceso actualizado correctamente.');
      location.reload();
    } else {
      alert('⚠️ ' + (data.error || 'Error al actualizar.'));
    }
  })
  .catch(err => {
    console.error('❌ Error de red:', err);
    alert('❌ Error de red al actualizar.');
  });
});

function crearPaginacion(pagina, totalPaginas) {
const paginacionContainer = document.getElementById('pagination');
if (!paginacionContainer) return;

paginacionContainer.innerHTML = '';

const maxNumeros = 9; // máximo de números visibles (sin contar flechas)

const crearBoton = (label, page, isActive = false, disabled = false) => {
  const btn = document.createElement('button');
  btn.textContent = label;
  btn.className = 'btn btn-sm pag-btn' + (isActive ? ' active' : '');
  btn.disabled = disabled;
  btn.onclick = () => {
    paginaActual = page;
    obtenerProcesos(page);
  };
  return btn;
};

// Flecha izquierda
paginacionContainer.appendChild(crearBoton('←', pagina - 1, false, pagina === 1));

const mostrarEllipsis = () => {
  const span = document.createElement('span');
  span.textContent = '...';
  span.className = 'btn btn-sm disabled';
  return span;
};

let start = Math.max(1, pagina - Math.floor(maxNumeros / 2));
let end = start + maxNumeros - 1;

if (end > totalPaginas) {
  end = totalPaginas;
  start = Math.max(1, end - maxNumeros + 1);
}

// Mostrar primer número y "..." si es necesario
if (start > 1) {
  paginacionContainer.appendChild(crearBoton('1', 1));
  if (start > 2) paginacionContainer.appendChild(mostrarEllipsis());
}

// Números intermedios
for (let i = start; i <= end; i++) {
  paginacionContainer.appendChild(crearBoton(i, i, i === pagina));
}

// Mostrar "..." y último número si no se incluye
if (end < totalPaginas) {
  if (end < totalPaginas - 1) paginacionContainer.appendChild(mostrarEllipsis());
  paginacionContainer.appendChild(crearBoton(totalPaginas, totalPaginas));
}

// Flecha derecha
paginacionContainer.appendChild(crearBoton('→', pagina + 1, false, pagina === totalPaginas));
}