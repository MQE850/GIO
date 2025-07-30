let currentPage = 1;
const itemsPerPage = 10;
let tests = [];

document.addEventListener("DOMContentLoaded", () => {
    fetchTests();

    const btnAdd = document.getElementById("btnAddTest");
    const modalForm = document.getElementById("modalForm");
    const form = document.getElementById("testForm");
    const closeModal = document.getElementById("closeModal");
    const deleteBtn = document.getElementById("deleteBtn");

    if (btnAdd && modalForm && form) {
        btnAdd.addEventListener("click", () => {
            // Limpiar campos del formulario
            form.reset();
            document.getElementById("process_id").value = "";
            document.getElementById("description").value = "";
            if (window.quill) quill.setContents([]);
            if (window.tagsManager) tagsManager.setTagsFromString("");

            // Ocultar botón eliminar
            deleteBtn?.classList.add("hidden");

            // Mostrar modal
            modalForm.classList.remove("hidden");
        });

        closeModal.addEventListener("click", () => {
            modalForm.classList.add("hidden");
        });
    }
});

async function fetchTests() {
    try {
        const response = await fetch("obtener_pruebas.php");
        const data = await response.json();
        tests = data;
        renderTests();
    } catch (error) {
        console.error("Error obteniendo pruebas:", error);
    }
}

function renderTests() {
    const container = document.getElementById("testContainer");
    container.innerHTML = "";

    const startIndex = (currentPage - 1) * itemsPerPage;
    const currentTests = tests.slice(startIndex, startIndex + itemsPerPage);

    currentTests.forEach(test => {
        const card = document.createElement("div");
        card.className = "test-card";
        card.innerHTML = `
            <h3 class="test-title">${test.titulo}</h3>
            <div class="test-detail-row">
                <span><strong>Generación:</strong> ${test.generacion}</span>
                <span><strong>Tiempo:</strong> ${test.tiempo}</span>
                <span><strong>Fecha:</strong> ${test.fecha}</span>
                <span><strong>Creador:</strong> ${test.noreloj}</span>
            </div>
            <div class="tags-wrapper">
                ${(JSON.parse(test.etiquetas || "[]") || []).map(tag => `<span class="tag-chip">${tag}<span class="remove-tag">×</span></span>`).join("")}
            </div>
            <div class="test-description">${test.descripcion}</div>
            <div class="test-actions">
                <button class="btn-edit" data-id="${test.id}">Editar</button>
                <button class="btn-delete" data-id="${test.id}">Eliminar</button>
            </div>
        `;
        container.appendChild(card);
    });

    renderPagination();
    attachButtonEvents();
}

function renderPagination() {
    const pagination = document.getElementById("pagination");
    pagination.innerHTML = "";

    const totalPages = Math.ceil(tests.length / itemsPerPage);
    for (let i = 1; i <= totalPages; i++) {
        const btn = document.createElement("button");
        btn.textContent = i;
        btn.className = i === currentPage ? "active" : "";
        btn.addEventListener("click", () => {
            currentPage = i;
            renderTests();
        });
        pagination.appendChild(btn);
    }
}

function attachButtonEvents() {
    document.querySelectorAll(".btn-edit").forEach(btn => {
        btn.addEventListener("click", async () => {
            const id = btn.getAttribute("data-id");
            const test = tests.find(t => t.id === id);
            if (!test) return;

            // Llenar datos en el formulario
            document.getElementById("process_id").value = test.id;
            document.getElementById("titulo").value = test.titulo;
            document.getElementById("generacion").value = test.generacion;
            document.getElementById("tiempo_estimado").value = test.tiempo;
            document.getElementById("fecha").value = test.fecha;

            if (window.quill) quill.root.innerHTML = test.descripcion || "";
            if (window.tagsManager) tagsManager.setTagsFromString(test.etiquetas || "");

            document.getElementById("modalForm").classList.remove("hidden");
            document.getElementById("deleteBtn").classList.remove("hidden");
        });
    });

    document.querySelectorAll(".btn-delete").forEach(btn => {
        btn.addEventListener("click", async () => {
            const id = btn.getAttribute("data-id");
            if (confirm("¿Estás seguro de eliminar esta prueba?")) {
                await fetch("eliminar_prueba.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ id })
                });
                fetchTests();
            }
        });
    });
}
