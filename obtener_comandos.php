<?php
include('DB_connection.php');

$sql = "SELECT * FROM comandos ORDER BY creado_en DESC";
$result = $conn->query($sql);

$comandos = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $comandos[] = $row;
    }
}
?>
