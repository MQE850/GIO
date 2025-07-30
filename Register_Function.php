<?php
session_start();
require_once 'DB_connection.php';

header('Content-Type: application/json; charset=utf-8');

try {
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        throw new Exception("Método no permitido.");
    }

    $conn->set_charset("utf8mb4");

    // Validar datos requeridos
    $campos = ['noreloj', 'nombre', 'apellido', 'rol', 'departamento', 'contrasena'];
    foreach ($campos as $campo) {
        if (empty($_POST[$campo])) {
            throw new Exception("El campo '$campo' es obligatorio.");
        }
    }

    $noreloj = trim($_POST['noreloj']);
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $rol = trim($_POST['rol']);
    $departamento = trim($_POST['departamento']);
    $contrasena_raw = $_POST['contrasena'];

    // Validar rol
    $roles_permitidos = ['admin', 'colaborador', 'publico'];
    if (!in_array($rol, $roles_permitidos)) {
        throw new Exception("Rol no válido.");
    }

    // Verificar si ya existe el número de reloj
    $verificar = $conn->prepare("SELECT noreloj FROM empleados WHERE noreloj = ?");
    $verificar->bind_param("s", $noreloj);
    $verificar->execute();
    $verificar->store_result();
    if ($verificar->num_rows > 0) {
        throw new Exception("Ya existe un usuario con ese número de reloj.");
    }
    $verificar->close();

    // Obtener datos del registrador (usuario actual)
    $registrador = $_SESSION['noreloj'] ?? null;
    $jefe_directo = null;
    if ($registrador) {
        $stmt = $conn->prepare("SELECT nombre, apellido, puesto FROM empleados WHERE noreloj = ?");
        $stmt->bind_param("s", $registrador);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($fila = $res->fetch_assoc()) {
            $jefe_directo = "$registrador - {$fila['nombre']} {$fila['apellido']} ({$fila['puesto']})";
        }
        $stmt->close();
    }

    $contrasena = password_hash($contrasena_raw, PASSWORD_BCRYPT);
    $idioma = "español";

    // Insertar nuevo usuario con idioma por defecto
    $insert = $conn->prepare("INSERT INTO empleados 
        (noreloj, nombre, apellido, rol, departamento, contrasena, jefe_directo, idioma) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $insert->bind_param("ssssssss", $noreloj, $nombre, $apellido, $rol, $departamento, $contrasena, $jefe_directo, $idioma);
    if (!$insert->execute()) {
        throw new Exception("Error al registrar: " . $insert->error);
    }
    $insert->close();

    // Notificación
    $accion = "Registro de usuario";
    $autor = $_SESSION['noreloj'] ?? 'sistema';
    $fecha = date("Y-m-d H:i:s");

    $notif = $conn->prepare("INSERT INTO notificaciones (usuario_afectado, accion, autor, fecha) 
                             VALUES (?, ?, ?, ?)");
    $notif->bind_param("ssss", $noreloj, $accion, $autor, $fecha);
    $notif->execute();
    $notif->close();

    echo json_encode(['success' => true, 'mensaje' => 'Usuario registrado exitosamente.']);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'mensaje' => $e->getMessage()]);
} finally {
    if ($conn && $conn->ping()) {
        $conn->close();
    }
}
