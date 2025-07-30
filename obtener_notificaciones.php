<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['noreloj'])) {
    echo json_encode(['error' => 'Sesión no iniciada']);
    exit;
}

include '../php/DB_connection.php';

$noreloj = $_SESSION['noreloj'];
$notificaciones = [];

// 1. Obtener notificaciones personales
$sql = "SELECT usuario_afectado, accion, autor, fecha 
        FROM notificaciones 
        WHERE usuario_afectado = ? 
        ORDER BY fecha DESC 
        LIMIT 20";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $noreloj);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $row['fecha'] = date('c', strtotime($row['fecha']));
    $row['accion'] = htmlspecialchars($row['accion'], ENT_QUOTES, 'UTF-8');
    $row['autor'] = htmlspecialchars($row['autor'], ENT_QUOTES, 'UTF-8');
    $notificaciones[] = $row;
}
$stmt->close();

// 2. Verificar si es "Engineer" o "Sr. Engineer"
$esJefe = false;
$queryPuesto = "SELECT puesto FROM empleados WHERE noreloj = ?";
$stmt = $conn->prepare($queryPuesto);
$stmt->bind_param("s", $noreloj);
$stmt->execute();
$res = $stmt->get_result();

if ($res && $emp = $res->fetch_assoc()) {
    $puesto = strtolower(trim($emp['puesto']));
    if ($puesto === 'engineer' || $puesto === 'sr. engineer') {
        $esJefe = true;
    }
}
$stmt->close();

// 3. Si es jefe, incluir notificaciones de otros usuarios
if ($esJefe) {
    $sqlJefe = "SELECT usuario_afectado, accion, autor, fecha 
                FROM notificaciones 
                WHERE usuario_afectado != ? 
                ORDER BY fecha DESC 
                LIMIT 20";
    $stmt2 = $conn->prepare($sqlJefe);
    $stmt2->bind_param("s", $noreloj);
    $stmt2->execute();
    $result2 = $stmt2->get_result();

    while ($row = $result2->fetch_assoc()) {
        $row['fecha'] = date('c', strtotime($row['fecha']));
        $row['accion'] = htmlspecialchars($row['accion'], ENT_QUOTES, 'UTF-8');
        $row['autor'] = htmlspecialchars($row['autor'], ENT_QUOTES, 'UTF-8');
        $notificaciones[] = $row;
    }
    $stmt2->close();
}

$conn->close();

// 4. Eliminar duplicados
$notificaciones = array_map("unserialize", array_unique(array_map("serialize", $notificaciones)));

// 5. Ordenar por fecha descendente
usort($notificaciones, function($a, $b) {
    return strtotime($b['fecha']) - strtotime($a['fecha']);
});

echo json_encode($notificaciones);
