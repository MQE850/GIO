<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
include 'DB_connection.php';

if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], ['admin', 'colaborador'])) {
    http_response_code(403);
    echo json_encode(['error' => 'No tienes permiso para esta acción']);
    exit;
}

$noreloj = $_SESSION['noreloj'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = htmlspecialchars(trim($_POST['title'] ?? ''));
    $description = trim($_POST['description'] ?? '');
    $tag = trim($_POST['tags'] ?? '');
    $generation = htmlspecialchars(trim($_POST['generation_combined'] ?? ''));
    $documentURL = filter_var(trim($_POST['documentURL'] ?? ''), FILTER_SANITIZE_URL);
    $tipo = strtolower(trim($_POST['tipo'] ?? ''));

    // ⛔ Validación fuerte
    if (!$title || !$description || !$generation || !$tipo || !in_array($tipo, ['proceso', 'prueba'])) {
        echo json_encode(['error' => 'Faltan datos obligatorios o tipo inválido']);
        exit;
    }

    function convertirATiempoEnMinutos($tiempoTexto) {
        $tiempoTexto = strtolower(trim($tiempoTexto));
        $totalMinutos = 0;

        if (preg_match('/(\d+)\s*h/', $tiempoTexto, $matchH)) {
            $totalMinutos += intval($matchH[1]) * 60;
        }
        if (preg_match('/(\d+)\s*min/', $tiempoTexto, $matchM)) {
            $totalMinutos += intval($matchM[1]);
        }
        if ($totalMinutos === 0 && preg_match('/^\d+$/', $tiempoTexto)) {
            $totalMinutos = intval($tiempoTexto);
        }

        return $totalMinutos;
    }

    $exec_time_input = $_POST['exec_time'] ?? '';
    $exec_time = convertirATiempoEnMinutos($exec_time_input);

    // Subir documento si se envió
    $uploadedFilePath = null;
    if (isset($_FILES['document']) && $_FILES['document']['error'] === UPLOAD_ERR_OK) {
        $allowedMimeTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];
        $fileTmpPath = $_FILES['document']['tmp_name'];
        $fileName = basename($_FILES['document']['name']);
        $fileType = mime_content_type($fileTmpPath);

        if (!in_array($fileType, $allowedMimeTypes)) {
            echo json_encode(['error' => 'Tipo de archivo no permitido']);
            exit;
        }

        $uploadDir = __DIR__ . '/../uploads/documents/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $newFileName = uniqid('doc_') . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $fileName);
        $destPath = $uploadDir . $newFileName;

        if (!move_uploaded_file($fileTmpPath, $destPath)) {
            echo json_encode(['error' => 'Error al subir archivo']);
            exit;
        }

        $uploadedFilePath = 'uploads/documents/' . $newFileName;
    }

        $stmt = $conn->prepare("INSERT INTO procesos (
            title, author, contributor, description, tag,
            generation, document, documentURL, exec_time,
            reg_date, mod_date, tipo
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW(), ?)");

        $contributor = $noreloj;

        $stmt->bind_param(
            "ssssssssis", // ✅ 10 tipos
            $title,
            $noreloj,
            $contributor,
            $description,
            $tag,
            $generation,
            $uploadedFilePath,
            $documentURL,
            $exec_time,
            $tipo
        );

        if ($stmt->execute()) {
            header('Location: http://30.1.7.50/Manual/webpages/Process.php');
            exit;
        } else {
            echo json_encode(['error' => 'Error al guardar proceso: ' . $stmt->error]);
        }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['error' => 'Método no permitido']);
}
