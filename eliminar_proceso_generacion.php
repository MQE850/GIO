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
    echo "Acceso no autorizado.";
    exit;
}

$id = intval($_POST['id'] ?? 0);
if ($id <= 0) {
    http_response_code(400);
    echo "ID inválido.";
    exit;
}

// Obtener ruta de documento
$stmt = $conn->prepare("SELECT document FROM procesos WHERE process_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$proceso = $result->fetch_assoc();
$stmt->close();

// Eliminar archivo si existe
if ($proceso && !empty($proceso['document'])) {
    $ruta = realpath('../archivos/' . basename($proceso['document']));
    if ($ruta && file_exists($ruta)) {
        unlink($ruta);
    }
}

// Eliminar proceso
$stmt = $conn->prepare("DELETE FROM procesos WHERE process_id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "Proceso eliminado correctamente.";
} else {
    http_response_code(500);
    echo "Error al eliminar: " . $stmt->error;
}

$stmt->close();
$conn->close();
