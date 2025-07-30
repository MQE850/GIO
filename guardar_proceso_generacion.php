<?php
require_once '../php/DB_connection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Método no permitido.";
    exit;
}

if (!isset($_SESSION['noreloj'])) {
    http_response_code(403);
    echo "No has iniciado sesión.";
    exit;
}

$noreloj = $_SESSION['noreloj'];
$titulo = trim($_POST['title'] ?? '');
$descripcion = trim($_POST['description'] ?? '');
$generacion = trim($_POST['generation'] ?? '');
$tipo_archivo = $_POST['tipo_archivo'] ?? '';
$link = trim($_POST['archivo_link'] ?? '');
$id = intval($_POST['id'] ?? 0);

if ($titulo === '' || $descripcion === '' || $generacion === '') {
    http_response_code(400);
    echo "Campos obligatorios vacíos.";
    exit;
}

$archivo = null;
$url = null;

// Subida de PDF
if ($tipo_archivo === 'pdf' && !empty($_FILES['archivo_pdf']['name'])) {
    $nombreArchivo = basename($_FILES['archivo_pdf']['name']);
    $rutaDestino = '../archivos/' . $nombreArchivo;

    $mime = mime_content_type($_FILES['archivo_pdf']['tmp_name']);
    $permitidos = ['application/pdf'];

    if (!in_array($mime, $permitidos)) {
        http_response_code(400);
        echo "Tipo de archivo no permitido.";
        exit;
    }

    if (!move_uploaded_file($_FILES['archivo_pdf']['tmp_name'], $rutaDestino)) {
        http_response_code(500);
        echo "Error al subir archivo.";
        exit;
    }

    $archivo = $nombreArchivo;
} elseif ($tipo_archivo === 'link' && filter_var($link, FILTER_VALIDATE_URL)) {
    $url = $link;
}

// Insertar nuevo
if ($id === 0) {
    $stmt = $conn->prepare("INSERT INTO procesos (title, description, generation, document, documentURL, author, contributor, reg_date, mod_date) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
    $stmt->bind_param("sssssss", $titulo, $descripcion, $generacion, $archivo, $url, $noreloj, $noreloj);
} else {
    // Actualizar existente
    $query = "UPDATE procesos SET title = ?, description = ?, generation = ?, contributor = ?, mod_date = NOW()";
    $params = [$titulo, $descripcion, $generacion, $noreloj];
    
    if ($archivo) {
        $query .= ", document = ?";
        $params[] = $archivo;
    }

    if ($url) {
        $query .= ", documentURL = ?";
        $params[] = $url;
    }

    $query .= " WHERE process_id = ?";
    $params[] = $id;

    $tipos = str_repeat("s", count($params) - 1) . "i";
    $stmt = $conn->prepare($query);
    $stmt->bind_param($tipos, ...$params);
}

if ($stmt->execute()) {
    echo "Proceso guardado correctamente.";
} else {
    http_response_code(500);
    echo "Error al guardar: " . $stmt->error;
}

$stmt->close();
$conn->close();
