<?php
session_start();
require_once 'DB_connection.php';

header('Content-Type: application/json');

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 10;
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Calcular offset para paginación
$offset = ($page - 1) * $limit;

$where = "";
if ($search !== '') {
    $search_escaped = $conn->real_escape_string($search);
    $where = " WHERE 
        title LIKE '%$search_escaped%' OR
        description LIKE '%$search_escaped%' OR
        tags LIKE '%$search_escaped%'";
}

// Obtener total de registros para paginación
$totalSql = "SELECT COUNT(*) as total FROM pruebas $where";
$totalResult = $conn->query($totalSql);
$totalRows = 0;
if ($totalResult) {
    $row = $totalResult->fetch_assoc();
    $totalRows = intval($row['total']);
}

// Obtener registros
$sql = "SELECT process_id, title, generation_combined, author, contributor, reg_date, mod_date, exec_time, tags, noreloj, description, document, documentURL FROM pruebas $where ORDER BY reg_date DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

$pruebas = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $pruebas[] = $row;
    }
}

$response = [
    'success' => true,
    'page' => $page,
    'limit' => $limit,
    'total' => $totalRows,
    'pruebas' => $pruebas
];

echo json_encode($response);
$conn->close();
?>
