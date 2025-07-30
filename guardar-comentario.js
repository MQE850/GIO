document.addEventListener("DOMContentLoaded", () => {
  const btnComentar = document.getElementById("btn-comentar");
  const modalComentario = document.getElementById("modal-comentario");
  const formComentario = document.getElementById("form-comentario");
  const closeBtn = document.querySelector(".close");

  if (btnComentar && modalComentario && formComentario && closeBtn) {
    btnComentar.addEventListener("click", () => {
      modalComentario.style.display = "flex";
    });

    closeBtn.addEventListener("click", () => {
      modalComentario.style.display = "none";
    });

    window.addEventListener("click", (e) => {
      if (e.target === modalComentario) {
        modalComentario.style.display = "none";
      }
    });

    formComentario.addEventListener("submit", function (e) {
      e.preventDefault();

      const formData = new FormData(formComentario);

      fetch("../../Manual/includes/guardar_comentario.php", {
        method: "POST",
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        let mensaje = formComentario.querySelector(".mensaje-estado");
        if (!mensaje) {
          mensaje = document.createElement("p");
          mensaje.className = "mensaje-estado";
          formComentario.appendChild(mensaje);
        }
        mensaje.textContent = data.message;
        mensaje.style.color = data.success ? "green" : "red";

        if (data.success) {
          formComentario.reset();
          setTimeout(() => {
            modalComentario.style.display = "none";
            mensaje.remove();
          }, 2000);
        }
      })
      .catch(() => {
        alert("Ocurrió un error al enviar el comentario.");
      });
    });
  }
});
