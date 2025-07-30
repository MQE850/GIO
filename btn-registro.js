document.addEventListener("DOMContentLoaded", function () {
  const modal = document.getElementById("registroModal");
  const btnRegistro = document.getElementById("btn-registro");
  const cerrar = document.querySelector(".cerrar");
  const form = document.getElementById("formRegistro");
  const mensaje = document.getElementById("mensajeRegistro");

  if (!btnRegistro || !modal || !cerrar || !form || !mensaje) {
    console.warn("Faltan elementos necesarios para el registro.");
    return;
  }

  // Mostrar el modal centrado
  btnRegistro.onclick = () => {
    modal.classList.add("active"); // Usa la clase para mostrarlo con display: flex
  };

  // Cerrar al hacer clic en la "X"
  cerrar.onclick = () => {
    cerrarModal();
  };

  // Cerrar al hacer clic fuera del contenido
  window.onclick = (event) => {
    if (event.target === modal) {
      cerrarModal();
    }
  };

  // Función para cerrar y limpiar el modal
  function cerrarModal() {
    modal.classList.remove("active");
    form.reset();
    mensaje.textContent = "";
    mensaje.style.color = "";
  }

  // Envío del formulario
  form.onsubmit = function (e) {
    e.preventDefault();
    const formData = new FormData(form);

    fetch("/Manual/php/Register_Function.php", {
      method: "POST",
      body: formData,
    })
      .then((res) => res.text())
      .then((data) => {
        mensaje.textContent = data.trim();
        mensaje.style.color = data.includes("exitosamente") ? "green" : "red";
        if (data.includes("exitosamente")) {
          form.reset();
          setTimeout(cerrarModal, 2000);
        }
      })
      .catch((error) => {
        mensaje.textContent = "Error al registrar usuario.";
        mensaje.style.color = "red";
        console.error(error);
      });
  };
});
