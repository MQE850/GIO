<?php  
include '../includes/session.php';
include_once '../includes/load_header.php';
include '../php/DB_connection.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Procesos por Generación</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <link rel="stylesheet" href="../fonts/fotawesome/css/all.min.css">
  <link rel="stylesheet" href="../css/Header.css">
  <link rel="stylesheet" href="../css/Perfil.css">
  <link rel="stylesheet" href="../css/register.css">
  <link rel="stylesheet" href="../css/Background.css">
  <link rel="stylesheet" href="../css/Footer.css">
  <link rel="stylesheet" href="../css/Comments.css">
  <link rel="stylesheet" href="../css/Process.css">
  <link rel="stylesheet" href="../css/Generation.css">
  <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

  <style>
    .titulo-generacion {
      text-align: center;
      font-size: 2rem;
      font-weight: bold;
      margin: 20px 0;
    }

    .top-controls {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      padding: 0 20px;
    }

    .search-bar input {
      padding: 6px;
      width: 200px;
    }

    .gen-buttons {
      display: flex;
      justify-content: center;
      gap: 10px;
      flex-wrap: wrap;
      margin: 20px 0;
    }

    .gen-buttons button,
    .btn-add-process {
      padding: 8px 16px;
      border: none;
      border-radius: 6px;
      background-color: #007bff;
      color: white;
      cursor: pointer;
    }

    .gen-buttons button:hover,
    .btn-add-process:hover {
      background-color: #0056b3;
    }

    .process-card {
      background: white;
      border: 1px solid #ccc;
      border-radius: 8px;
      padding: 15px;
      margin: 10px 0;
    }

    .modal-overlay {
      position: fixed;
      top: 0; left: 0;
      width: 100vw;
      height: 100vh;
      background: rgba(0, 0, 0, 0.6);
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 9999;
    }

    .modal-content {
      background: white;
      padding: 20px;
      border-radius: 8px;
      width: 90%;
      max-width: 600px;
    }

    .form-buttons {
      display: flex;
      justify-content: flex-end;
      gap: 10px;
      margin-top: 10px;
    }
  </style>
</head>
<body>
  <section class="hero text-center">
    <div class="container">
      <h2 class="titulo-generacion">Explora cada Generación</h2>
    </div>
  </section>

  <div class="top-controls">
    <div class="search-bar">
      <input type="text" id="searchInput" placeholder="Buscar por título...">
      <button onclick="buscarProceso()">🔍 Buscar</button>
    </div>

    <?php if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], ['admin', 'colaborador'])): ?>
      <button class="btn-add-process" onclick="abrirFormularioProceso()">+ Agregar Proceso</button>
    <?php endif; ?>
  </div>

  <section class="generations-section" id="generaciones">
    <div class="gen-buttons">
      <button onclick="showGeneration('gen8')">GEN 8</button>
      <button onclick="showGeneration('gen9')">GEN 9</button>
      <button onclick="showGeneration('gen10')">GEN 10</button>
      <button onclick="showGeneration('gen11')">GEN 11</button>
    </div>

    <div id="generation-content">
      <div id="procesos-container">
        <p>Selecciona una generación para ver sus procesos registrados.</p>
      </div>
    </div>
  </section>

  <!-- Modal Formulario -->
  <div id="formularioProceso" class="modal-overlay" style="display:none;">
    <div class="modal-content">
      <h3 id="formTitulo">Agregar Proceso</h3>
      <form id="procesoForm" enctype="multipart/form-data">
        <input type="hidden" name="id" id="procesoId">

        <label for="tituloInput">Título:</label>
        <input type="text" name="title" id="tituloInput" required>

        <label for="editor">Descripción:</label>
        <div id="editor" style="height: 150px;"></div>
        <input type="hidden" name="description" id="descripcion">

        <label for="generacionSelect">Generación:</label>
        <select name="generation" id="generacionSelect" required>
          <option value="gen8">GEN 8</option>
          <option value="gen9">GEN 9</option>
          <option value="gen10">GEN 10</option>
          <option value="gen11">GEN 11</option>
        </select>

        <label for="tipoArchivo">Tipo de archivo:</label>
        <select name="tipo_archivo" id="tipoArchivo" onchange="mostrarInputArchivo()">
          <option value="pdf">PDF</option>
          <option value="link">Link</option>
        </select>

        <div id="campoArchivoPDF">
          <label for="archivo_pdf">Subir PDF:</label>
          <input type="file" name="archivo_pdf" accept="application/pdf">
        </div>

        <div id="campoArchivoLink" style="display:none;">
          <label for="archivo_link">URL del documento:</label>
          <input type="url" name="archivo_link" id="archivoLinkInput">
        </div>

        <div class="form-buttons">
          <button type="submit">Guardar</button>
          <button type="button" onclick="cerrarFormularioProceso()">Cancelar</button>
        </div>
      </form>
    </div>
  </div>

  <?php include '../includes/Footer.php'; ?>

  <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
  <script src="../js/Generacion.js"></script>
</body>
</html>
