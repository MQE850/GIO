<?php
session_start();
include 'DB_connection.php';

$limit = 5;
$page = intval($_GET['page'] ?? 1);
$offset = ($page - 1) * $limit;

$search = trim($_GET['search'] ?? '');
$generations = trim($_GET['generations'] ?? '');
$sort = $_GET['sort'] ?? '';

$whereClauses = [];
$params = [];
$types = '';

if ($search) {
    $whereClauses[] = "(title LIKE ? OR description LIKE ? OR tags LIKE ?)";
    $searchParam = "%$search%";
    $params[] = $searchParam; $params[] = $searchParam; $params[] = $searchParam;
    $types .= 'sss';
}

if ($generations) {
    // Filter by any generation included in generation_combined
    $gens = explode(',', $generations);
    $genClauses = [];
    foreach ($gens as $gen) {
        $genClauses[] = "generation_combined LIKE ?";
        $params[] = "%$gen%";
        $types .= 's';
    }
    $whereClauses[] = '(' . implode(' OR ', $genClauses) . ')';
}

$whereSql = '';
if ($whereClauses) {
    $whereSql = 'WHERE ' . implode(' AND ', $whereClauses);
}

// Ordenar
$orderSql = '';
switch ($sort) {
    case 'title-asc': $orderSql = 'ORDER BY title ASC'; break;
    case 'title-desc': $orderSql = 'ORDER BY title DESC'; break;
    case 'date-asc': $orderSql = 'ORDER BY created_at ASC'; break;
    case 'date-desc': $orderSql = 'ORDER BY created_at DESC'; break;
    default: $orderSql = 'ORDER BY created_at DESC';
}

// Contar total
$countSql = "SELECT COUNT(*) FROM procesos $whereSql";
$stmt = $conn->prepare($countSql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$stmt->bind_result($total);
$stmt->fetch();
$stmt->close();

$totalPages = ceil($total / $limit);

// Obtener registros
$sql = "SELECT id, title, description, tags, generation_combined, document_path, document_url, exec_time, noreloj, created_at, updated_at, updated_by FROM procesos $whereSql $orderSql LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);

$params[] = $limit;
$params[] = $offset;
$types .= 'ii';

$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$procesos = [];
while ($row = $result->fetch_assoc()) {
    $procesos[] = $row;
}

echo json_encode([
    'processes' => $procesos,
    'totalPages' => $totalPages,
    'currentPage' => $page
]);
