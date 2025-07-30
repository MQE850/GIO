<?php
$host = 'localhost';
$usuario = 'root';
$contrasena = '';
$base_de_datos = 'manual';

$conn = new mysqli($host, $usuario, $contrasena, $base_de_datos);

if ($conn->connect_error) {
    die("Error en la conexión: " . $conn->connect_error);
}
?>
