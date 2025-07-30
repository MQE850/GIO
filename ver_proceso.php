<?php
include '../php/DB_connection.php';

if (!isset($_GET['id'])) {
  echo json_encode(['error' => 'Falta el ID']);
  exit;
}

$id = intval($_GET['id']);

$query = "SELECT * FROM procesos WHERE process_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
  echo json_encode(['error' => 'Proceso no encontrado']);
  exit;
}

echo json_encode($result->fetch_assoc());
?>
