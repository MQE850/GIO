<?php
session_start();

// Configuración conexión DB
require_once 'db_connection.php'; // Ajusta según tu archivo

// Obtener datos POST
$title = $_POST['title'] ?? '';
$description = $_POST['description'] ?? '';
$tags = $_POST['tags'] ?? '';
$generation = $_POST['generation_combined'] ?? '';
$document_url = $_POST['documentURL'] ?? '';
$exec_time = $_POST['exec_time'] ?? '';
$tipo = $_POST['tipo'] ?? 'proceso';
$mod_date = date('Y-m-d');
$author = $_SESSION['noreloj'] ?? '';

// Validación básica
if (!$title || !$description || !$generation) {
    // Aquí no puedes redirigir porque faltan datos, mejor mostrar mensaje
    die('Faltan campos obligatorios');
}

// Preparar insert
$sql = "INSERT INTO procesos (title, description, tags, generation, documentURL, exec_time, tipo, mod_date, author)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die('Error en la consulta SQL');
}

$stmt->bind_param('sssssssss', $title, $description, $tags, $generation, $document_url, $exec_time, $tipo, $mod_date, $author);

if ($stmt->execute()) {
    // Redirigir a process.php después de crear
    header('Location: ../webpages/process.php');
    exit;
} else {
    die('Error al crear proceso');
}

$stmt->close();
$conn->close();
