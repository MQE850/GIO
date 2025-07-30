let procesos = {}; // Variable global para el proceso actual

// Configuraciones Quill (igual que tienes)
const Header = Quill.import('formats/header');
Header.whitelist = [1, 2, 3, 4, 5, 6];
Quill.register(Header, true);

const Size = Quill.import('formats/size');
Size.whitelist = ['small', false, 'large', 'huge'];
Quill.register(Size, true);

const quill = new Quill('#editor', {
  theme: 'snow',
  modules: {
    toolbar: [
      [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
      ['bold', 'italic', 'underline', 'strike'],
      [{ 'list': 'ordered' }, { 'list': 'bullet' }],
      [{ 'align': [] }],
      [{ 'color': [] }, { 'background': [] }],
      ['link', 'image', 'code-block'],
      ['clean']
    ]
  }
});

// Elementos DOM
const addProcessBtn = document.getElementById('btnOpenForm');
const closeBtn = document.getElementById('closeFormBtn');
const formContainer = document.getElementById('processFormContainer');
const mainContent = document.getElementById('mainContent');
const form = document.getElementById('processForm');

// Mostrar botón agregar solo a admin o colaborador
if (addProcessBtn) addProcessBtn.style.display = 'none';
if (window.currentUser.rol === 'colaborador' || window.currentUser.rol === 'admin') {
  addProcessBtn.style.display = 'inline-block';
}

addProcessBtn.onclick = () => {
  formContainer.style.display = 'block';
  mainContent.style.display = 'none';
};

closeBtn.onclick = () => {
  formContainer.style.display = 'none';
  mainContent.style.display = 'flex';
  addProcessBtn.style.display = 'inline-block';
};

form.onsubmit = function () {
  document.getElementById('description').value = quill.root.innerHTML;
  this.querySelector('button[type="submit"]').disabled = true;
  return true;
};

// Manejo de tags como array de objetos
const tagInput = document.getElementById('tagInput');
const tagContainer = document.getElementById('tagContainer');
const tagsInput = document.getElementById('tagsInput');
const tags = [];

function actualizarTagsInput() {
  // Convertimos el array de tags a array de objetos {name: "tag"}
  const tagsObjects = tags.map(t => ({ name: t }));
  // Guardamos como JSON string en el input oculto
  tagsInput.value = JSON.stringify(tagsObjects);
}

tagInput.addEventListener('keydown', function (e) {
  if (e.key === 'Enter') {
    e.preventDefault();
    const val = this.value.trim();
    if (val && !tags.includes(val.toLowerCase())) {
      tags.push(val.toLowerCase());
      const tagEl = document.createElement('span');
      tagEl.className = 'tag';
      tagEl.innerHTML = `${val} <span class="remove-tag">&times;</span>`;
      tagEl.querySelector('.remove-tag').onclick = () => {
        tags.splice(tags.indexOf(val.toLowerCase()), 1);
        tagEl.remove();
        actualizarTagsInput();
      };
      tagContainer.appendChild(tagEl);
      actualizarTagsInput();
      this.value = '';
    }
  }
});

// Dropdown generaciones y demás código igual...
const dropdownDisplay = document.getElementById('dropdownDisplay');
const dropdownOptions = document.getElementById('dropdownOptions');
const generationCombined = document.getElementById('generationCombined');

dropdownDisplay.onclick = () => {
  dropdownOptions.style.display = (dropdownOptions.style.display === 'block') ? 'none' : 'block';
};

dropdownOptions.querySelectorAll('input[type="checkbox"]').forEach(cb => {
  cb.onchange = () => {
    const selected = Array.from(dropdownOptions.querySelectorAll('input:checked')).map(i => i.value);
    if (selected.length > 4) {
      cb.checked = false;
      alert('Máximo 4 generaciones.');
      return;
    }
    dropdownDisplay.innerText = selected.join(', ') || 'Seleccionar generaciones';
    generationCombined.value = selected.join(',');
  };
});

document.addEventListener('click', function (e) {
  if (!dropdownDisplay.contains(e.target) && !dropdownOptions.contains(e.target)) {
    dropdownOptions.style.display = 'none';
  }
});

['docUrlInput', 'execTimeInput'].forEach(id => {
  const input = document.getElementById(id);
  input.addEventListener('keydown', e => {
    if (e.key === 'Enter') e.preventDefault();
  });
});
function actualizarTagsInput() {
  // Guardar como cadena separada por comas
  tagsInput.value = tags.join(',');
}

// Guardar proceso (actualizar)
document.getElementById('btnGuardar').addEventListener('click', () => {
  const fileInput = document.getElementById('inputDocument');
  const formData = new FormData();

  formData.append('process_id', procesos.process_id);
  formData.append('title', document.getElementById('inputTitle').value.trim());
  formData.append('generation_combined', generationCombined.value.trim());
  formData.append('author', document.getElementById('inputAuthor').value.trim());
  formData.append('contributor', document.getElementById('inputContributor').value.trim());
  formData.append('reg_date', document.getElementById('inputDate').value);
  formData.append('exec_time', document.getElementById('inputExecTime').value);
  formData.append('tags_json', tagsInput.value);  // Aquí enviamos JSON
  formData.append('description', quill.root.innerHTML);
  formData.append('documentURL', document.getElementById('inputDocumentURL').value.trim());

  if (fileInput.files.length > 0) {
    formData.append('document', fileInput.files[0]);
  }

  fetch('../php/actualizar_proceso.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.text())
  .then(data => {
    console.log('Respuesta:', data);
    alert('Proceso actualizado correctamente.');
    // Actualiza vista si quieres
  })
  .catch(() => alert('Error en la solicitud'));
});
