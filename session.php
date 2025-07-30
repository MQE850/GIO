<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Asignar valores por defecto si no hay sesión iniciada
$noreloj = $_SESSION['noreloj'] ?? null;
$rol = $_SESSION['rol'] ?? 'publico';
$nombre = $_SESSION['nombre'] ?? 'Invitado';
$departamento = $_SESSION['departamento'] ?? '';
$tiene_permiso = in_array($rol, ['admin', 'colaborador']);

// Control de acceso por rol según la página
$pagina_actual = basename($_SERVER['PHP_SELF']);

$acceso_restringido = [
    'Inicio_admin.php' => ['admin', 'colaborador', 'publico'],
    'Generation.php' => ['admin', 'colaborador', 'publico'],
    'Process.php' => ['admin', 'colaborador', 'publico'],
    'Concept.php' => ['admin', 'colaborador', 'publico'],
    'Commands.php' => ['admin', 'colaborador', 'publico'],
];

// Si la página requiere permisos y el rol actual no está permitido, redirigir
if (isset($acceso_restringido[$pagina_actual])) {
    $roles_permitidos = $acceso_restringido[$pagina_actual];
    if (!in_array($rol, $roles_permitidos)) {
        header('Location: ../html/index.php');
        exit();
    }
}
?>
