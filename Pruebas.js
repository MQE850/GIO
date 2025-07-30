function clearForm() {
    document.getElementById("formPrueba").reset();
    document.getElementById("id_prueba").value = '';
    if (tagsManager) tagsManager.setTagsFromString('');
    if (window.quill) quill.setContents([]);
}
