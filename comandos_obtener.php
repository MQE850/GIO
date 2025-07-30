<?php
include '../php/DB_connection.php';

$busqueda = isset($_GET['busqueda']) ? $conn->real_escape_string($_GET['busqueda']) : "";

$sql = "SELECT * FROM comandos WHERE titulo LIKE '%$busqueda%' OR contenido LIKE '%$busqueda%' ORDER BY fecha_creacion DESC";
$result = $conn->query($sql);

$comandos = [];

while ($row = $result->fetch_assoc()) {
    $comandos[] = $row;
}

echo json_encode($comandos);
?>
