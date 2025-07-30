<?php
header('Content-Type: application/json');
include __DIR__ . '/DB_connection.php';

if (!$conn) {
    echo json_encode(['error' => 'Error en la conexión con la base de datos']);
    exit;
}

// Parámetros GET
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 8;
$offset = ($page - 1) * $limit;

// Contar total
$totalQuery = $conn->query("SELECT COUNT(*) as total FROM procesos");
$totalResult = $totalQuery->fetch_assoc();
$total = $totalResult['total'] ?? 0;

// Consulta paginada y ordenada alfabéticamente
$query = "SELECT process_id, title, description, tag, generation, exec_time, document AS document_path, documentURL AS document_url, author, reg_date 
          FROM procesos 
          ORDER BY title ASC 
          LIMIT $limit OFFSET $offset";

$result = $conn->query($query);
$procesos = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $procesos[] = $row;
    }
    echo json_encode([
        'procesos' => $procesos,
        'total' => $total,
        'page' => $page,
        'pages' => ceil($total / $limit)
    ]);
} else {
    echo json_encode(['error' => 'Error en la consulta: ' . $conn->error]);
}
