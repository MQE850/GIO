<?php
session_start();
include 'DB_connection.php';

if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], ['admin', 'colaborador'])) {
    http_response_code(403);
    echo json_encode(['error' => 'No tienes permiso para esta acción']);
    exit;
}

$noreloj = $_SESSION['noreloj'] ?? '';

$action = $_POST['action'] ?? '';
$id = intval($_POST['id'] ?? 0);

if (!$id) {
    echo json_encode(['error' => 'ID de proceso inválido']);
    exit;
}

if ($action === 'delete') {
    // Eliminar proceso
    $stmt = $conn->prepare("DELETE FROM procesos WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo json_encode(['success' => 'Proceso eliminado']);
    } else {
        echo json_encode(['error' => 'Error al eliminar: ' . $stmt->error]);
    }
    $stmt->close();
    $conn->close();
    exit;
}

if ($action === 'edit') {
    $title = trim($_POST['title'] ?? '');
    $description = $_POST['description'] ?? '';
    $tags = trim($_POST['tags'] ?? '');
    $generation_combined = trim($_POST['generation_combined'] ?? '');
    $exec_time = intval($_POST['exec_time'] ?? 0);
    $documentURL = trim($_POST['documentURL'] ?? '');

    if (!$title || !$description || !$generation_combined) {
        echo json_encode(['error' => 'Faltan datos obligatorios']);
        exit;
    }

    // Si suben nuevo archivo, procesarlo
    $uploadedFilePath = null;
    if (isset($_FILES['document']) && $_FILES['document']['error'] === UPLOAD_ERR_OK) {
        $allowedMimeTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        $fileTmpPath = $_FILES['document']['tmp_name'];
        $fileName = basename($_FILES['document']['name']);
        $fileType = mime_content_type($fileTmpPath);

        if (!in_array($fileType, $allowedMimeTypes)) {
            echo json_encode(['error' => 'Tipo de archivo no permitido']);
            exit;
        }

        $uploadDir = __DIR__ . '/../uploads/procesos/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $newFileName = uniqid('doc_') . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $fileName);
        $destPath = $uploadDir . $newFileName;

        if (!move_uploaded_file($fileTmpPath, $destPath)) {
            echo json_encode(['error' => 'Error al subir archivo']);
            exit;
        }

        $uploadedFilePath = 'uploads/procesos/' . $newFileName;
    }

    if ($uploadedFilePath) {
        // Actualizar con nuevo archivo
        $stmt = $conn->prepare("UPDATE procesos SET title=?, description=?, tags=?, generation_combined=?, document_path=?, document_url=?, exec_time=?, noreloj=?, updated_at=NOW() WHERE id=?");
        $stmt->bind_param("ssssssiii", $title, $description, $tags, $generation_combined, $uploadedFilePath, $documentURL, $exec_time, $noreloj, $id);
    } else {
        // Actualizar sin cambiar archivo
        $stmt = $conn->prepare("UPDATE procesos SET title=?, description=?, tags=?, generation_combined=?, document_url=?, exec_time=?, noreloj=?, updated_at=NOW() WHERE id=?");
        $stmt->bind_param("sssssiii", $title, $description, $tags, $generation_combined, $documentURL, $exec_time, $noreloj, $id);
    }

    if ($stmt->execute()) {
        echo json_encode(['success' => 'Proceso actualizado']);
    } else {
        echo json_encode(['error' => 'Error al actualizar: ' . $stmt->error]);
    }
    $stmt->close();
    $conn->close();
    exit;
}

echo json_encode(['error' => 'Acción no válida']);
