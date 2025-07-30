document.addEventListener('DOMContentLoaded', () => {
  const closeBtn = document.getElementById('closeFormBtn');
  const formContainer = document.getElementById('processFormContainer');
  const detailView = document.getElementById('processDetailView');
  const form = document.getElementById('processForm');
  const tagInput = document.getElementById('tagInput');
  const tagContainer = document.getElementById('tagContainer');
  const hiddenTagsInput = document.getElementById('tagsInput');

  if (closeBtn && formContainer) {
    closeBtn.addEventListener('click', () => {
      formContainer.style.display = 'none';
      if (detailView) detailView.style.display = 'block';
    });
  }

  // Agregar tags visuales al presionar Enter
  if (tagInput && tagContainer && hiddenTagsInput) {
    tagInput.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') {
        e.preventDefault();
        const tag = tagInput.value.trim();
        if (tag && !getTags().includes(tag)) {
          addTag(tag);
        }
        tagInput.value = '';
      }
    });
  }

  function getTags() {
    return Array.from(tagContainer.querySelectorAll('.tag')).map(el => el.textContent);
  }

  function addTag(tag) {
    const span = document.createElement('span');
    span.className = 'tag badge bg-info me-1';
    span.textContent = tag;
    span.style.cursor = 'pointer';
    span.title = 'Haz clic para eliminar';
    span.addEventListener('click', () => {
      span.remove();
      updateHiddenTags();
    });
    tagContainer.appendChild(span);
    updateHiddenTags();
  }

  function updateHiddenTags() {
    hiddenTagsInput.value = getTags().join(',');
  }

  // Expuesta globalmente para usar desde obtener-procesos.js
  window.editarProceso = function (proceso) {
    if (!proceso) return;

    if (detailView) detailView.style.display = 'none';
    if (formContainer) formContainer.style.display = 'block';

    form.title.value = proceso.title || '';
    form.exec_time.value = proceso.exec_time || '';
    form.documentURL.value = proceso.document_url || '';

    // Cargar descripción en Quill
    if (window.quillEditor && proceso.description) {
      window.quillEditor.root.innerHTML = proceso.description;
    }

    // Tags
    tagContainer.innerHTML = '';
    (proceso.tag || '').split(',').map(t => t.trim()).filter(Boolean).forEach(addTag);

    // Generaciones
    const genArray = (proceso.generation || '').split(',').map(g => g.trim());
    const genCheckboxes = document.querySelectorAll('#dropdownOptions input[type="checkbox"]');
    genCheckboxes.forEach(chk => {
      chk.checked = genArray.includes(chk.value);
    });
    document.getElementById('generationCombined').value = genArray.join(',');
  };
});
