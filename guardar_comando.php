<?php
session_start();
include('DB_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo']);
    $contenido = trim($_POST['contenido']);
    $comando = trim($_POST['comando']);
    $noreloj = $_SESSION['noreloj'] ?? '';

    // Procesar imagen
    $imagen_nombre = '';
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
        $directorio = __DIR__ . '/../uploads/'; // Carpeta uploads, relativa al archivo actual
        if (!file_exists($directorio)) {
            mkdir($directorio, 0777, true);
        }
        $imagen_nombre = uniqid() . '_' . basename($_FILES['imagen']['name']);
        $ruta_destino = $directorio . $imagen_nombre;

        if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_destino)) {
            die("Error al subir la imagen.");
        }
    }

    // Insertar en BD
    $stmt = $conn->prepare("INSERT INTO comandos (titulo, contenido, comando, imagen, noreloj, creado_en) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("sssss", $titulo, $contenido, $comando, $imagen_nombre, $noreloj);

    if ($stmt->execute()) {
        header("Location: ../webpages/Commands.php");
        exit;
    } else {
        echo "Error al guardar el comando: " . $conn->error;
    }
}
?>
