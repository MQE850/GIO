<?php
session_start();
require_once 'DB_connection.php';

header('Content-Type: application/json');

// Validar noreloj en sesión
if (!isset($_SESSION['noreloj'])) {
    echo json_encode(['success' => false, 'message' => 'No hay usuario en sesión']);
    exit;
}

$noreloj = $_SESSION['noreloj'];

$process_id = isset($_POST['process_id']) ? intval($_POST['process_id']) : 0;
$title = isset($_POST['title']) ? trim($_POST['title']) : '';
$description = isset($_POST['description']) ? trim($_POST['description']) : '';
$exec_time = isset($_POST['exec_time']) ? intval($_POST['exec_time']) : 0;
$documentURL = isset($_POST['documentURL']) ? trim($_POST['documentURL']) : '';
$generation_arr = isset($_POST['generation_combined']) ? $_POST['generation_combined'] : [];
$generation_combined = implode(',', array_slice($generation_arr, 0, 4));

// Tags vienen serializadas en JSON o string separado (del JS)
$tags_raw = isset($_POST['tags']) ? $_POST['tags'] : '';
// Sanear tags para almacenar
$tags = '';
if ($tags_raw) {
    // Intentar guardar JSON
    $tagsJson = json_decode($tags_raw, true);
    if (is_array($tagsJson)) {
        // Eliminar duplicados
        $tagsJson = array_unique(array_map('trim', $tagsJson));
        $tags = json_encode(array_values($tagsJson));
    } else {
        $tags = $conn->real_escape_string(trim($tags_raw));
    }
}

// Subir archivo (opcional)
$document = '';
if (isset($_FILES['document']) && $_FILES['document']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = '../uploads/pruebas/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $filename = basename($_FILES['document']['name']);
    $targetFile = $uploadDir . time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);

    if (move_uploaded_file($_FILES['document']['tmp_name'], $targetFile)) {
        // Guardamos solo el nombre relativo
        $document = basename($targetFile);
    }
}

// Sanitizar campos para SQL
$title_s = $conn->real_escape_string($title);
$description_s = $conn->real_escape_string($description);
$documentURL_s = $conn->real_escape_string($documentURL);
$generation_s = $conn->real_escape_string($generation_combined);
$tags_s = $conn->real_escape_string($tags);
$exec_time_s = intval($exec_time);

if ($process_id > 0) {
    // Actualizar
    $sqlSet = "title='$title_s', description='$description_s', tags='$tags_s', generation_combined='$generation_s', documentURL='$documentURL_s', exec_time=$exec_time_s, noreloj='$noreloj', mod_date=NOW()";

    if ($document !== '') {
        $document_s = $conn->real_escape_string($document);
        $sqlSet .= ", document='$document_s'";
    }

    $sql = "UPDATE pruebas SET $sqlSet WHERE process_id=$process_id";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true, 'message' => 'Prueba actualizada']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar: ' . $conn->error]);
    }

} else {
    // Insertar
    $document_s = $document !== '' ? "'".$conn->real_escape_string($document)."'" : "NULL";
    $sql = "INSERT INTO pruebas (title, description, tags, generation_combined, document, documentURL, author, noreloj, reg_date, mod_date, exec_time)
            VALUES ('$title_s', '$description_s', '$tags_s', '$generation_s', $document_s, '$documentURL_s', '$noreloj', '$noreloj', NOW(), NOW(), $exec_time_s)";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true, 'message' => 'Prueba creada', 'process_id' => $conn->insert_id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al crear: ' . $conn->error]);
    }
}

$conn->close();
