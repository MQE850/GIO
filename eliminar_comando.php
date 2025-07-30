<?php
session_start();
include('DB_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    // Validar rol
    $rol = $_SESSION['rol'] ?? '';
    if (!in_array($rol, ['admin', 'colaborador'])) {
        http_response_code(403);
        echo "No autorizado.";
        exit;
    }

    // Obtener nombre de la imagen
    $stmt = $conn->prepare("SELECT imagen FROM comandos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($imagen);
    $stmt->fetch();
    $stmt->close();

    // Eliminar imagen física
    if ($imagen) {
        $ruta = __DIR__ . '/../img/' . $imagen; // Asegúrate que el path sea correcto
        if (file_exists($ruta)) {
            unlink($ruta);
        }
    }

    // Eliminar comando
    $stmt = $conn->prepare("DELETE FROM comandos WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo "Comando eliminado";
    } else {
        http_response_code(500);
        echo "Error al eliminar comando.";
    }
    $stmt->close();
    exit;
} else {
    http_response_code(400);
    echo "Solicitud inválida.";
}
?>
