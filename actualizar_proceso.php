<?php
header('Content-Type: application/json');
require_once 'DB_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $tags = $_POST['tags'] ?? '';
    $generation = $_POST['generation_combined'] ?? '';
    $exec_time = $_POST['exec_time'] ?? '';
    $tipo = $_POST['tipo'] ?? '';
    $mod_date = $_POST['mod_date'] ?? '';
    $contributor = $_POST['contributor'] ?? '';
    $documentURL = $_POST['documentURL'] ?? '';

    // Subida de archivo (opcional)
    $documentName = null;
    if (!empty($_FILES['document']['name'])) {
        $targetDir = "../uploads/";
        $documentName = basename($_FILES["document"]["name"]);
        $targetFilePath = $targetDir . $documentName;

        if (!move_uploaded_file($_FILES["document"]["tmp_name"], $targetFilePath)) {
            echo json_encode(["success" => false, "error" => "Error al subir archivo."]);
            exit;
        }
    }

    // Preparar actualización
    $sql = "UPDATE procesos SET title=?, description=?, tags=?, generation=?, exec_time=?, tipo=?, mod_date=?, contributor=?, document_url=?";
    $params = [$title, $description, $tags, $generation, $exec_time, $tipo, $mod_date, $contributor, $documentURL];

    if ($documentName !== null) {
        $sql .= ", document=?";
        $params[] = $documentName;
    }

    $sql .= " WHERE id=?";
    $params[] = $id;

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(["success" => false, "error" => "Error en la preparación de la consulta."]);
        exit;
    }

    $stmt->bind_param(str_repeat("s", count($params)), ...$params);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["success" => false, "error" => "Método no permitido"]);
}
?>
