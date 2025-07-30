  document.getElementById('editProfileBtn').addEventListener('click', () => {
    const inputs = document.querySelectorAll('input');
    inputs.forEach(input => input.removeAttribute('readonly'));
    alert("Ahora puedes editar tu perfil");
  });
  let editable = false;
document.getElementById('editProfileBtn').addEventListener('click', () => {
  const inputs = document.querySelectorAll('input');
  editable = !editable;
  inputs.forEach(input => {
    if (editable) {
      input.removeAttribute('readonly');
    } else {
      input.setAttribute('readonly', true);
    }
  });
});
  // Verifica si el archivo actual NO es Perfil.php
  if (!window.location.pathname.includes('Perfil.php')) {
    const fab = document.createElement('a');
    fab.href = 'Perfil.php';
    fab.className = 'fab';
    fab.title = 'Ir al perfil';
    fab.innerHTML = '👤'; // Ícono de usuario

    document.body.appendChild(fab);
  }

function abrirModalEdit() {
  document.getElementById('modalEdit').classList.add('active');
}
function cerrarModalEdit() {
  document.getElementById('modalEdit').classList.remove('active');
}
function abrirModalFoto() {
  document.getElementById('modalFoto').classList.add('active');
}
function cerrarModalFoto() {
  document.getElementById('modalFoto').classList.remove('active');
}
