<?php
// Mostrar errores para desarrollo (quitar en producción)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

include __DIR__ . '/DB_connection.php';

if (!$conn) {
    echo json_encode(['error' => 'Error en la conexión con la base de datos']);
    exit;
}

// Parámetros GET con saneamiento y valores por defecto
$page  = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? intval($_GET['page']) : 1;
$limit = 8;
$offset = ($page - 1) * $limit;

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$tag    = isset($_GET['tag']) ? trim($_GET['tag']) : '';
$sort   = isset($_GET['sort']) ? $_GET['sort'] : 'title-asc';
$generations = [];

// Validar longitud de entradas de texto
if (strlen($search) > 100) $search = substr($search, 0, 100);
if (strlen($tag) > 50) $tag = substr($tag, 0, 50);

// Validar y limitar valores permitidos para sort
$allowedSorts = ['title-asc', 'title-desc', 'date-asc', 'date-desc'];
if (!in_array($sort, $allowedSorts)) {
    $sort = 'title-asc';
}

// Inicializar condiciones
$conditions = [];
$params     = [];
$types      = '';

// Filtro obligatorio: tipo = 'proceso'
$conditions[] = "tipo = ?";
$params[] = "proceso";
$types .= "s";

// Filtro búsqueda por título o descripción
if ($search !== '') {
    $conditions[] = "(title LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= "ss";
}

// Filtro por generaciones (validar valores)
if (!empty($_GET['generation'])) {
    $inputGenerations = is_array($_GET['generation']) ? $_GET['generation'] : explode(',', $_GET['generation']);
    $validGens = ['GEN8', 'GEN9', 'GEN10', 'GEN11'];

    $genConditions = [];
    foreach ($inputGenerations as $g) {
        $g = trim($g);
        if (in_array($g, $validGens)) {
            $genConditions[] = "generation LIKE ?";
            $params[] = "%$g%";
            $types .= 's';
        }
    }
    if (!empty($genConditions)) {
        $conditions[] = '(' . implode(' OR ', $genConditions) . ')';
    }
}

// Filtro por etiqueta
if ($tag !== '') {
    $conditions[] = "tag LIKE ?";
    $params[] = "%$tag%";
    $types .= 's';
}

// Construir cláusula WHERE
$where = count($conditions) > 0 ? 'WHERE ' . implode(' AND ', $conditions) : '';

// Ordenamiento
switch ($sort) {
    case 'title-desc': $orderBy = 'ORDER BY title DESC'; break;
    case 'date-asc':   $orderBy = 'ORDER BY reg_date ASC'; break;
    case 'date-desc':  $orderBy = 'ORDER BY reg_date DESC'; break;
    default:           $orderBy = 'ORDER BY title ASC'; break;
}

// Consulta principal
$sql = "SELECT 
            process_id, 
            title, 
            author, 
            description, 
            tag, 
            generation, 
            exec_time, 
            document, 
            documentURL AS document_url, 
            contributor, 
            reg_date, 
            mod_date 
        FROM procesos
        $where
        $orderBy
        LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['error' => 'Error al preparar la consulta: ' . $conn->error]);
    exit;
}

// Agregar tipos para LIMIT y OFFSET
$typesWithLimit = $types . 'ii';
$paramsWithLimit = array_merge($params, [$limit, $offset]);

call_user_func_array(
    [$stmt, 'bind_param'],
    refValues(array_merge([$typesWithLimit], $paramsWithLimit))
);

$stmt->execute();
$result = $stmt->get_result();

$procesos = [];
while ($row = $result->fetch_assoc()) {
    $procesos[] = array_map(function($val) {
        return $val === null ? '' : $val;
    }, $row);
}

// Consulta para contar total de resultados sin límite
$countSql = "SELECT COUNT(*) AS total FROM procesos $where";
$countStmt = $conn->prepare($countSql);
if (!$countStmt) {
    echo json_encode(['error' => 'Error al preparar el conteo: ' . $conn->error]);
    exit;
}

if ($types !== '') {
    call_user_func_array(
        [$countStmt, 'bind_param'],
        refValues(array_merge([$types], $params))
    );
}

$countStmt->execute();
$countResult = $countStmt->get_result();
$total = 0;
if ($countRow = $countResult->fetch_assoc()) {
    $total = intval($countRow['total']);
}

// Respuesta final en JSON
echo json_encode([
    'procesos' => $procesos,
    'total'    => $total,
    'page'     => $page,
    'pages'    => ceil($total / $limit)
]);

// Función auxiliar para referencias dinámicas en bind_param
function refValues($arr) {
    $refs = [];
    foreach ($arr as $key => $value) {
        $refs[$key] = &$arr[$key];
    }
    return $refs;
}

const params = new URLSearchParams();
params.set('page', pagina);
params.set('search', filtroActivo.search);
params.set('tag', filtroActivo.tag);
params.set('sort', filtroActivo.orden);

// Generaciones como array: usa append()
if (Array.isArray(filtroActivo.generation)) {
  filtroActivo.generation.forEach(gen => {
    params.append('generation[]', gen); // ✅
  });
}

?>
