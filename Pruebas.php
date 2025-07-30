<?php 
session_start();
if (!isset($_SESSION['noreloj'])) {
    $_SESSION['noreloj'] = 'USR001'; // Valor de prueba
}
include '../includes/session.php';
include_once '../includes/load_header.php';
include '../php/DB_connection.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Gestión de Pruebas Técnicas</title>
    <link rel="stylesheet" href="../css/Pruebas.css" />
      <link rel="stylesheet" href="../css/Footer.css">
      <link rel="stylesheet" href="../css/Perfil.css">
      <link rel="stylesheet" href="../css/Header.css">

    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet" />
<style>
.test-card {
    border: 1px solid #ccc;
    border-radius: 12px;
    padding: 1em;
    margin: 1em 0;
    background: #fff;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.test-title {
    font-size: 1.3em;
    margin-bottom: 0.5em;
    color: #333;
}

.test-detail-row {
    display: flex;
    flex-wrap: wrap;
    gap: 1em;
    font-size: 0.9em;
    margin-bottom: 0.5em;
}

.test-description {
    margin-top: 0.8em;
    padding: 0.8em;
    background: #f9f9f9;
    border-radius: 8px;
    font-size: 0.95em;
}

.tags-wrapper {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-top: 6px;
}

.tag-chip {
    background-color: #e0f7fa;
    color: #00796b;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 0.85em;
    display: inline-flex;
    align-items: center;
}

.remove-tag {
    margin-left: 6px;
    cursor: pointer;
    color: #c00;
    font-weight: bold;
}

.test-actions {
    margin-top: 1em;
    display: flex;
    gap: 10px;
}

#pagination {
    margin-top: 1.5em;
    text-align: center;
}

#pagination button {
    margin: 0 4px;
    padding: 6px 10px;
    border-radius: 6px;
    border: none;
    background: #eee;
    cursor: pointer;
}

#pagination button.active {
    background: #1976d2;
    color: #fff;
    font-weight: bold;
}
</style>
</head>
<body>

    <div class="container">
        <header>
            <h1>Gestión de Pruebas Técnicas</h1>
            <div class="toolbar">
                <input type="text" id="searchInput" placeholder="Buscar pruebas por título, descripción o etiquetas..." />
                <button id="btnAddTest">+ Agregar prueba</button>
            </div>
        </header>

        <section id="testListContainer">
            <table>
                <thead>
                    <tr><th>Título</th><th>Generaciones</th><th>Autor</th></tr>
                </thead>
                <tbody id="testListBody"></tbody>
            </table>
            <div id="paginationControls"></div>
        </section>

        <section id="testDetailContainer">
            <!-- Aquí se insertará el detalle o formulario al hacer clic en una prueba -->
        </section>

        <!-- Modal para agregar/editar prueba -->
        <div id="modalForm" class="modal hidden">
            <div class="modal-content">
                <span class="close" id="closeModal">&times;</span>
                <h2 id="formTitle">Agregar / Editar Prueba</h2>
                <form id="testForm" enctype="multipart/form-data">
                    <input type="hidden" id="process_id" name="process_id" />
                    <input type="hidden" id="noreloj" name="noreloj" value="<?php echo htmlspecialchars($_SESSION['noreloj']); ?>" />

                    <label for="title">Título:</label>
                    <input type="text" id="title" name="title" required />

                    <label for="descriptionEditor">Descripción:</label>
                    <div id="descriptionEditor" style="height: 150px; background-color: white;"></div>
                    <textarea name="description" id="description" hidden></textarea>

                    <label>Generaciones:</label>
                    <div id="generationOptions">
                        <label><input type="checkbox" name="generation_combined[]" value="GEN1" /> GEN1</label>
                        <label><input type="checkbox" name="generation_combined[]" value="GEN2" /> GEN2</label>
                        <label><input type="checkbox" name="generation_combined[]" value="GEN3" /> GEN3</label>
                        <label><input type="checkbox" name="generation_combined[]" value="GEN4" /> GEN4</label>
                    </div>

                    <label for="document">Archivo:</label>
                    <input type="file" id="document" name="document" />

                    <label for="documentURL">URL Documento:</label>
                    <input type="url" id="documentURL" name="documentURL" />

                    <label for="exec_time">Tiempo estimado (min):</label>
                    <input type="number" id="exec_time" name="exec_time" min="1" />

                    <label>Etiquetas:</label>
                    <div id="tagsContainer"></div>
                    <input type="text" id="tagInput" placeholder="Agregar etiqueta y presiona Enter" />

                    <div class="form-footer" style="margin-top:1rem;">
                        <button type="submit" id="saveBtn">Guardar</button>
                        <button type="button" id="deleteBtn" class="delete-btn hidden">Eliminar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
  <?php if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], ['admin', 'colaborador'])): ?>
    <!-- Botón flotante que redirige a Perfil.php -->
    <a href="./Perfil.php" class="fab" title="Editar perfil">
        👤
    </a>
  <?php endif; ?>
        <?php include '../includes/Footer.php';?>
  <script src="../js/Perfil.js"></script>
    <!-- Librerías necesarias -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    <script src="../js/quill-config.js"></script>
    <script src="../js/tags.js"></script>
    <script src="../js/obtener-pruebas.js"></script>
    <script src="../js/Pruebas.js"></script>
</body>
</html>
