// login.js

document.addEventListener('DOMContentLoaded', () => {
  const form = document.querySelector('form');

  form.addEventListener('submit', (e) => {
    const noreloj = document.getElementById('noreloj').value.trim();
    const contra = document.getElementById('contra').value.trim();

    if (!noreloj || !contra) {
      e.preventDefault();
      Swal.fire({
        icon: 'warning',
        title: 'Campos incompletos',
        text: 'Por favor llena todos los campos.',
        confirmButtonColor: '#007bff'
      });
    }
  });
});
