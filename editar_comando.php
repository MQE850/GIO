<?php
session_start();
include('DB_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $titulo = trim($_POST['titulo']);
    $contenido = trim($_POST['contenido']);
    $comando = trim($_POST['comando']);
    $noreloj = $_SESSION['noreloj'] ?? '';

    // Procesar imagen si la actualizaste (opcional: agregar manejo similar a guardar)
    $imagen_actual = null;
    $stmt = $conn->prepare("SELECT imagen FROM comandos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($imagen_actual);
    $stmt->fetch();
    $stmt->close();

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
        $directorio = __DIR__ . '/../uploads/';
        if (!file_exists($directorio)) {
            mkdir($directorio, 0777, true);
        }
        // Elimina imagen vieja si existe
        if ($imagen_actual && file_exists($directorio . $imagen_actual)) {
            unlink($directorio . $imagen_actual);
        }

        $imagen_nombre = uniqid() . '_' . basename($_FILES['imagen']['name']);
        move_uploaded_file($_FILES['imagen']['tmp_name'], $directorio . $imagen_nombre);
    } else {
        $imagen_nombre = $imagen_actual;
    }

    $stmt = $conn->prepare("UPDATE comandos SET titulo = ?, contenido = ?, comando = ?, imagen = ?, noreloj = ? WHERE id = ?");
    $stmt->bind_param("sssssi", $titulo, $contenido, $comando, $imagen_nombre, $noreloj, $id);
    $stmt->execute();

    header("Location: ../webpages/commands.php");
    exit;
}
