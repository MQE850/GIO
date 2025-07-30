<?php
session_start();
include('../php/DB_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo']);
    $subtitulo = trim($_POST['subtitulo']);
    $contenido = trim($_POST['contenido']);
    $noreloj = $_SESSION['noreloj'];

    $imagen_nombre = '';
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
        $directorio = '../uploads/';
        if (!file_exists($directorio)) {
            mkdir($directorio, 0777, true);
        }

        $imagen_nombre = uniqid() . '_' . basename($_FILES['imagen']['name']);
        move_uploaded_file($_FILES['imagen']['tmp_name'], $directorio . $imagen_nombre);
    }

    $stmt = $conn->prepare("INSERT INTO conceptos (titulo, subtitulo, contenido, imagen, noreloj) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $titulo, $subtitulo, $contenido, $imagen_nombre, $noreloj);
    $stmt->execute();

    header("Location: ../webpages/Concept.php");
    exit;
}
?>
