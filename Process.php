<?php 
include '../includes/session.php';
include_once '../includes/load_header.php';
include '../php/DB_connection.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Procesos</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../fonts/fotawesome/css/all.min.css">
  <link rel="stylesheet" href="../css/Header.css">
  <link rel="stylesheet" href="../css/Perfil.css">
  <link rel="stylesheet" href="../css/register.css">
  <link rel="stylesheet" href="../css/Background.css">
  <link rel="stylesheet" href="../css/Footer.css">
  <link rel="stylesheet" href="../css/Comments.css">
  <link rel="stylesheet" href="../css/Process.css">
  <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
<!-- Quill JS -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <style>
    body { background-color: #ccc; color: white;}
    #processFormContainer {
      display: none;  
      margin-bottom:20px;
    }
    /* Estilos para tags en detalle */
    #processDetailView .tags {
      display: flex;
      flex-wrap: wrap;
      gap: 6px;
    }
    #processDetailView .tags .tag {
      background: #f0f0f0;
      color: #333;
      border-radius: 999px;
      padding: 4px 10px;
      font-size: 0.85rem;
      font-weight: 500;
      white-space: nowrap;
      max-width: 120px;
      text-overflow: ellipsis;
      overflow: hidden;
    }
    #viewDescription {
  background-color: #f8f9fa;
  color: #212529;
  border-radius: 8px;
  padding: 16px;
  font-size: 0.95rem;
  line-height: 1.6;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  max-height: 500px;
  overflow-y: auto;
}

#viewDescription h1{
  font-weight: bold;
  margin-top: 6px;
  margin-bottom: 8px;
  font-size:22px;
  color: #1e2a78;
}
#viewDescription h2 {
  font-weight: bold;
  margin-top: 6px;
  margin-bottom: 8px;
  font-size:18px;
  color: #1e2a78;
  margin-right: 5px;
}

#viewDescription h3 {
  font-weight: bold;
  margin-top: 6px;
  margin-bottom: 8px;
  font-size:14px;
  color: #1e2a78;
    margin-right: 10px;
}
#viewDescription h4 {
  font-weight: bold;
  margin-top: 6px;
  margin-bottom: 8px;
  font-size:12px;
  color: #1e2a78;
    margin-right: 10px;
}
#viewDescription h5 {
  font-weight: bold;
  margin-top: 6px;
  margin-bottom: 8px;
  font-size:10px;
  color: #1e2a78;
    margin-right: 10px;
}
#viewDescription h6 {
  font-weight: bold;
  margin-top: 6px;
  margin-bottom: 8px;
  font-size: 9px;
  color: #1e2a78;
  margin-right: 10px;
}
#viewDescription p {
  margin-bottom: 10px;
}

#viewDescription ul, 
#viewDescription ol {
  padding-left: 20px;
  margin-bottom: 10px;
}

#viewDescription li {
  margin-bottom: 5px;
}

#viewDescription a {
  color: #2b3b90;
  text-decoration: underline;
}

#viewDescription strong {
  font-weight: bold;
}

#viewDescription em {
  font-style: italic;
}

#viewDescription pre {
  background-color: #e9ecef;
  padding: 10px;
  border-radius: 6px;
  overflow-x: auto;
}

#viewDescription code {
  background-color: #f1f3f5;
  color: #212529;
  padding: 4px 8px;
  border-radius: 6px;
  font-family: 'Fira Code', 'Courier New', monospace;
  font-size: 0.95em;
  line-height: 1.5;
  display: inline-block;
  box-shadow: inset 0 0 2px rgba(0, 0, 0, 0.1);
  white-space: pre-wrap;
}

.ql-snow .ql-picker.ql-header .ql-picker-label[data-value="4"]::before {
  content: 'Heading 4';
  font-size: 16px;
}
.ql-snow .ql-picker.ql-header .ql-picker-label[data-value="5"]::before {
  content: 'Heading 5';
  font-size: 14px;
}
.ql-snow .ql-picker.ql-header .ql-picker-label[data-value="6"]::before {
  content: 'Heading 6';
  font-size: 12px;
}
.ql-snow .ql-picker.ql-header .ql-picker-item[data-value="4"]::before {
  content: 'Heading 4';
  font-size: 16px;
}
.ql-snow .ql-picker.ql-header .ql-picker-item[data-value="5"]::before {
  content: 'Heading 5';
  font-size: 14px;
}
.ql-snow .ql-picker.ql-header .ql-picker-item[data-value="6"]::before {
  content: 'Heading 6';
  font-size: 12px;
}
  </style>
</head>

<body>
  <div class="wrapper d-flex flex-column min-vh-100">
    <div class="content flex-grow-1">
      <div class="container mt-4">

        <!-- Fila superior: botón y buscador -->
        <div class="row mb-3">
          
        <div class="col-md-3 d-flex justify-content-center align-items-center">
          <button id="btnOpenForm" class="btn btn-primary w-75" style="background: linear-gradient(135deg, #1e2a78, #2b3b90);">+ Agregar Proceso</button>
        </div>

        <div class="col-md-9 position-relative">
          <input type="text" class="form-control search-input ps-5 pe-4" id="searchInput" placeholder="Buscar proceso...">
          <i class="fas fa-search search-icon px-2"></i>
          <span id="clearSearch" class="clear-btn" style="display: none;">&times;</span>
        </div>

      </div>

        <!-- Contenido principal: filtros y lista de procesos -->
        <div class="row" id="mainContent">
          <div class="col-md-3" id="filtersSection">
            <h5>Filtrar por Generación:</h5>
            <div id="filtroPanel" class="filter-panel shadow-sm p-3 rounded mt-2">
            <div id="generationFilters" class="generation-grid mb-2"></div>
              <hr class="bg-light">
              <label for="sortSelect" class="form-label text-white">Ordenar por:</label>
              <select id="sortSelect" class="form-select form-select-sm mb-2">
                <option value="">-- Seleccionar --</option>
                <option value="title-asc">Título A-Z</option>
                <option value="title-desc">Título Z-A</option>
                <option value="date-asc">Fecha más antigua</option>
                <option value="date-desc">Fecha más reciente</option>
              </select>
              <button id="btnLimpiarFiltros" class="btn btn-sm btn-outline-light w-100">Limpiar filtros</button>
            </div>
          </div>

          <div class="col-md-9">
            <h5>Procesos registrados:</h5>
            <div id="processList" class="row"></div> <!-- Aquí se insertan las tarjetas -->
            <div id="pagination" class="mt-3 d-flex gap-0.5 flex-wrap"></div>
          </div>
          <!-- Vista para mostrar detalles (solo lectura) -->
          <div id="processDetailView" style="display:none;" class="col-md-9 fade-slide-in">
            <button id="closeViewBtn" class="btn-close btn-close-white float-end"></button>

            <!-- Primera fila -->
            <div class="row mb-3">
              <div class="col-md-12">
                <h5 id="viewTitle"></h5>
              </div>
              <div class="col-md-2">
                <p id="viewGeneration" class="mb-1"></p>
              </div>
              <div class="col-md-2">
                <p id="viewAuthor" class="mb-1"></p>
                <p id="viewDate" class="mb-1"></p>
              </div>
              <div class="col-md-1">
                <p id="viewExecTime" class="mb-1"></p>
              </div>
              <div class="col-md-2">
                <p id="viewcontributor">Modificado Por:</p>
                <p id="viewMDate">Ultima modificacion</p>
              </div>
              <div class="col-md-4 d-flex flex-column gap-2" id="documentLinksContainer">
                <div id="viewDocument"></div>       <!-- Documento subido (archivo) -->
                <div id="viewDocumentUrl"></div>    <!-- Documento externo (URL) -->
              </div>
            </div>

            <!-- Segunda fila -->
            <div class="row mb-3">
              <div class="col-md-12">
                <div id="viewTags" class="mb-2"></div>
              </div>
            </div>
            <!-- Tercera fila -->
            <div class="row mb-3">
              <div class="col-12">
                <div id="viewDescription"></div>
              </div>
            </div>
            <!-- Botones -->
            <div class="row">
              <div class="col-12 d-flex justify-content-start gap-2">
                <button id="editBtn" class="btn btn-primary">Editar</button>
                <button id="deleteBtn" class="btn btn-info">Eliminar</button>
                <br>
              </div>
              <br>
            </div>
            <br>
          </div>
          </div>
          <!-- Contenedor del formulario, oculto inicialmente -->
          <div id="processFormContainer" class="mt-4">
            <button id="closeFormBtn" class="btn btn-danger mb-3">&times; Cerrar</button>

            <!-- Formulario para agregar proceso -->
            <form id="processForm" method="POST" enctype="multipart/form-data" action="../php/guardar_proceso.php">
              <div class="mb-2">
                <label>Título</label>
                <input type="text" name="title" class="form-control" required>
              </div>

              <div class="mb-2">
                <label>Descripción</label>
                <div id="editor" style="height: 150px;"></div>
                <input type="hidden" name="description" id="description">
              </div>

              <div class="mb-2">
                <label>Tags</label>
                <div id="tagContainer" class="mb-2"></div>
                <input type="text" id="tagInput" class="form-control" placeholder="Presiona Enter para agregar">
                <input type="hidden" name="tags" id="tagsInput">
              </div>

              <div class="mb-2">
                <label>Generaciones</label>
                <div class="dropdown-container">
                  <div id="dropdownDisplay" class="dropdown-display">Seleccionar generaciones</div>
                  <div id="dropdownOptions" class="dropdown-options">
                    <label><input type="checkbox" value="GEN8"> GEN8</label><br>
                    <label><input type="checkbox" value="GEN9"> GEN9</label><br>
                    <label><input type="checkbox" value="GEN10"> GEN10</label><br>
                    <label><input type="checkbox" value="GEN11"> GEN11</label>
                  </div>
                </div>
                <input type="hidden" name="generation_combined" id="generationCombined" required>
              </div>

              <div class="mb-2">
                <label>Subir Archivo (PDF/Word)</label>
                <input type="file" name="document" class="form-control">
              </div>

              <div class="row mb-3">
                <!-- Campo de URL ocupa 8 columnas -->
                <div class="col-md-8">
                  <label>Documentación relacionada</label>
                  <input type="url" name="documentURL" id="docUrlInput" class="form-control" placeholder="Insertar URL" />
                </div>

                <!-- Campo de tiempo estimado ocupa 4 columnas -->
                <div class="col-md-4">
                  <label>Tiempo estimado de ejecución</label>
                  <input type="number" name="exec_time" id="execTimeInput" class="form-control" placeholder="Minutos" min="1" />
                </div>
              </div>

              <button type="submit" class="btn btn-success" id="btnGuardar">Guardar</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  <?php if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], ['admin', 'colaborador'])): ?>
    <!-- Botón flotante que redirige a Perfil.php -->
    <a href="./Perfil.php" class="fab" title="Editar perfil">
        👤
    </a>
  <?php endif; ?>
      <script>
        window.currentUser = "<?php echo $_SESSION['noreloj'] ?? 'Desconocido'; ?>";
        window.currentUser = {
          rol: "<?php echo $_SESSION['rol'] ?? 'publico'; ?>",
          nombre: "<?php echo $_SESSION['noreloj'] ?? ''; ?>"
        };
      </script>
      <script src="../js/obtener-procesos.js"></script>
      <script src="../js/Process.js"></script>
      <script src="../js/editar_proceso.js"></script>
      <?php include '../includes/Footer.php';?>
  <script src="../js/Perfil.js"></script>
<!-- Toast container -->
<div id="toastContainer" style="
  position: fixed;
  bottom: 20px;
  right: 20px;
  z-index: 9999;
"></div>

</body>
</html>
