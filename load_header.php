<?php
include_once __DIR__ . '/session.php';

// DEBUG
// echo "ROL: " . $rol; exit; // Descomenta si necesitas ver qué valor tiene $rol

switch ($rol) {
    case 'admin':
        include_once __DIR__ . '/Header_admin.php';
        break;
    case 'colaborador':
        include_once __DIR__ . '/Header_empleado.php';
        break;
    case 'publico':
    default:
        include_once __DIR__ . '/Header_publico.php';
        break;
}
?>
