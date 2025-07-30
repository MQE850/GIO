<?php
include '../includes/session.php';
include 'DB_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $noreloj = $_POST['noreloj'] ?? null;

    if (!$noreloj || !isset($_FILES['imagen'])) {
        die("Datos inválidos.");
    }

    $imagen = $_FILES['imagen'];
    $nombreArchivo = basename($imagen['name']);
    $tipoArchivo = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));
    $permitidos = ['jpg', 'jpeg', 'png', 'gif','webp'];

    // Validar tipo
    if (!in_array($tipoArchivo, $permitidos)) {
        die("Solo se permiten imágenes JPG, JPEG, PNG, WEBP o GIF.");
    }

    // Verificar si es una imagen válida
    $check = getimagesize($imagen['tmp_name']);
    if ($check === false) {
        die("El archivo no es una imagen válida.");
    }

    // Obtener imagen anterior
    $stmt = $conn->prepare("SELECT imagen FROM empleados WHERE noreloj = ?");
    $stmt->bind_param("s", $noreloj);
    $stmt->execute();
    $result = $stmt->get_result();
    $empleado = $result->fetch_assoc();
    $imagenAnterior = $empleado['imagen'] ?? 'defaul.webp';

    // Crear nombre único
    $directorio = "../uploads/";
    if (!is_dir($directorio)) {
        mkdir($directorio, 0755, true);
    }
    $nuevoNombre = "perfil_" . $noreloj . "_" . time() . "." . $tipoArchivo;
    $rutaDestino = $directorio . $nuevoNombre;

    // Subir nueva imagen
    if (move_uploaded_file($imagen['tmp_name'], $rutaDestino)) {
        // Actualizar imagen en BD
        $stmt = $conn->prepare("UPDATE empleados SET imagen = ? WHERE noreloj = ?");
        $stmt->bind_param("ss", $nuevoNombre, $noreloj);
        if ($stmt->execute()) {
            // Eliminar imagen anterior si no es default
            if ($imagenAnterior !== 'defaul.webp') {
                $rutaAnterior = $directorio . $imagenAnterior;
                if (file_exists($rutaAnterior)) {
                    unlink($rutaAnterior);
                }
            }
            header("Location: ../webpages/Perfil.php?foto=ok");
            exit();
        } else {
            echo "Error al actualizar en la base de datos: " . $stmt->error;
        }
    } else {
        echo "Error al subir la nueva imagen.";
    }
} else {
    echo "Acceso no autorizado.";
}
?>
