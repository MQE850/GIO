<?php
include '../php/DB_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $noreloj = $_POST['noreloj'];
    $nombre = $_POST['nombre'];
    $tipo = $_POST['tipo'];
    $mensaje = $_POST['mensaje'];
    $fecha = date('Y-m-d H:i:s');
    $estado = 'pendiente';

    $sql = "INSERT INTO comentarios_web (noreloj, nombre, tipo, mensaje, fecha, estado)
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if ($stmt->execute([$noreloj, $nombre, $tipo, $mensaje, $fecha, $estado])) {
        echo json_encode(["success" => true, "message" => "Comentario enviado correctamente."]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al guardar comentario."]);
    }
}
?>
