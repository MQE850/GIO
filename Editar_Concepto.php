<?php
include('./DB_connection.php');

$id = $_POST['id'];
$titulo = $_POST['titulo'];
$contenido = $_POST['contenido'];
$eliminar_imagen = isset($_POST['eliminar_imagen']);
$nueva_imagen = $_FILES['imagen'] ?? null;

// Obtener imagen actual
$sql = "SELECT imagen FROM conceptos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($imagen_actual);
$stmt->fetch();
$stmt->close();

// Eliminar imagen si marcado y si existe
if ($eliminar_imagen && $imagen_actual) {
    $ruta_imagen = "../uploads/" . $imagen_actual;
    if (file_exists($ruta_imagen)) {
        unlink($ruta_imagen);
    }
    $imagen_actual = null;
}

// Procesar nueva imagen si fue subida correctamente
if ($nueva_imagen && $nueva_imagen['error'] == UPLOAD_ERR_OK) {
    // Eliminar imagen anterior si existe
    if ($imagen_actual) {
        $ruta_imagen = "../uploads/" . $imagen_actual;
        if (file_exists($ruta_imagen)) {
            unlink($ruta_imagen);
        }
    }

    $nombre_archivo = uniqid() . "_" . basename($nueva_imagen['name']);
    $destino = "../uploads/" . $nombre_archivo;

    if (move_uploaded_file($nueva_imagen['tmp_name'], $destino)) {
        $imagen_actual = $nombre_archivo;
    } else {
        // Manejar error subida si quieres
        // Por ahora ignoramos y dejamos la imagen actual sin cambios
    }
}

// Actualizar el registro con datos nuevos y el nombre de imagen (que puede ser null)
$sql = "UPDATE conceptos SET titulo = ?, contenido = ?, imagen = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssi", $titulo, $contenido, $imagen_actual, $id);
$stmt->execute();
$stmt->close();

header("Location: ../webpages/Concept.php");
exit;
