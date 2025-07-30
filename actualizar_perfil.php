<?php
include '../includes/session.php';
include 'DB_connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $noreloj = $_POST['noreloj'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $telefono = $_POST['telefono'] ?? null;
    $correo = $_POST['correo'] ?? null;
    $idioma = $_POST['idioma'] ?? null;

    $query = $conn->prepare("UPDATE empleados SET nombre = ?, apellido = ?, telefono = ?, correo = ?, idioma = ? WHERE noreloj = ?");
    $query->bind_param("ssssss", $nombre, $apellido, $telefono, $correo, $idioma, $noreloj);

    if ($query->execute()) {
        header("Location: ../webpages/Perfil.php");
        exit();
    } else {
        echo "Error al actualizar: " . $conn->error;
    }
} else {
    header("Location: ../index.php");
    exit();
}
?>
