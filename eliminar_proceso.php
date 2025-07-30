<?php
include 'DB_connection.php'; // Asegúrate de incluir tu archivo de conexión

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['process_id'])) {
    $process_id = intval($_POST['process_id']);

    // Validación adicional si es necesario

    $stmt = $conn->prepare("DELETE FROM process WHERE process_id = ?");
    $stmt->bind_param("i", $process_id);

    if ($stmt->execute()) {
        // Redirige o muestra mensaje de éxito
        header("Location: procesos.php?eliminado=1");
        exit();
    } else {
        echo "Error al eliminar el proceso.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Datos inválidos.";
}
?>
