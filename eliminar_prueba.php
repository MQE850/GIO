<?php
session_start();
require_once 'DB_connection.php';

header('Content-Type: application/json');

if (!isset($_POST['process_id'])) {
    echo json_encode(['success' => false, 'message' => 'No se especificó la prueba']);
    exit;
}

$process_id = intval($_POST['process_id']);

// Opcional: verificar permisos usuario (noreloj) antes de borrar

$sql = "DELETE FROM pruebas WHERE process_id=$process_id";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['success' => true, 'message' => 'Prueba eliminada']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al eliminar: ' . $conn->error]);
}

$conn->close();
