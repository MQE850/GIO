<?php
include('./DB_connection.php');

if (!isset($_GET['id'])) {
    header("Location: ../webpages/Concept.php");
    exit;
}

$id = (int)$_GET['id'];

// Primero obtener el nombre de la imagen para eliminarla del servidor
$sql = "SELECT imagen FROM conceptos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($imagen);
$stmt->fetch();
$stmt->close();

if ($imagen) {
    $ruta_imagen = "../uploads/" . $imagen;
    if (file_exists($ruta_imagen)) {
        unlink($ruta_imagen);
    }
}

// Luego eliminar el registro del concepto
$sql = "DELETE FROM conceptos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

header("Location: ../webpages/Concept.php");
exit;
