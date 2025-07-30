<?php
include 'DB_connection.php';
include '../includes/session.php';

header('Content-Type: application/json; charset=utf-8');

// Verificar permisos
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], ['admin', 'colaborador'])) {
    echo json_encode(['error' => 'Acceso denegado']);
    exit;
}

$accion = $_REQUEST['accion'] ?? '';
$accionRealizadaPor = $_SESSION['noreloj'] ?? 'sistema';

if ($accion === 'obtener') {
    $noreloj = $_GET['noreloj'] ?? '';
    if (!$noreloj) {
        echo json_encode(['error' => 'No se proporcionó número de reloj']);
        exit;
    }

    $stmt = $conn->prepare("SELECT noreloj, nombre, apellido, rol, departamento, correo, telefono, idioma, jefe_directo, puesto FROM empleados WHERE noreloj = ?");
    $stmt->bind_param("s", $noreloj);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        echo json_encode($user);
    } else {
        echo json_encode(['error' => 'Usuario no encontrado']);
    }

    $stmt->close();
    $conn->close();
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($accion === 'actualizar' && isset($_POST['noreloj'])) {
        $noreloj       = $_POST['noreloj'];
        $nombre        = $_POST['nombre'];
        $apellido      = $_POST['apellido'];
        $rol           = $_POST['rol'];
        $departamento  = $_POST['departamento'];
        $correo        = $_POST['correo'] ?? '';
        $telefono      = $_POST['telefono'] ?? '';
        $idioma        = $_POST['idioma'] ?? '';
        $jefe_directo  = $_POST['jefe_directo'] ?? '';
        $puesto        = $_POST['puesto'] ?? '';

        $stmt = $conn->prepare("UPDATE empleados SET 
                                    nombre = ?, apellido = ?, rol = ?, departamento = ?, correo = ?, telefono = ?, idioma = ?, jefe_directo = ?, puesto = ?
                                WHERE noreloj = ?");
        $stmt->bind_param("ssssssssss", $nombre, $apellido, $rol, $departamento, $correo, $telefono, $idioma, $jefe_directo, $puesto, $noreloj);

        if ($stmt->execute()) {
            $sqlNotif = "INSERT INTO notificaciones (usuario_afectado, accion, autor, fecha) VALUES (?, ?, ?, NOW())";

            // Notificación para el usuario editado
            $stmtNotif = $conn->prepare($sqlNotif);
            $accionAfectado = "Usuario fue editado";
            $stmtNotif->bind_param("sss", $noreloj, $accionAfectado, $accionRealizadaPor);
            $stmtNotif->execute();
            $stmtNotif->close();

            // Notificación para el editor (aunque sea la misma persona)
            $stmtNotif = $conn->prepare($sqlNotif);
            $accionAutor = ($noreloj === $accionRealizadaPor) 
                ? "Has editado tu propio usuario"
                : "Has editado al usuario $noreloj";
            $stmtNotif->bind_param("sss", $accionRealizadaPor, $accionAutor, $accionRealizadaPor);
            $stmtNotif->execute();
            $stmtNotif->close();

            echo json_encode(['success' => true, 'mensaje' => "Usuario actualizado correctamente por $accionRealizadaPor"]);
        } else {
            echo json_encode(['success' => false, 'mensaje' => 'Error al actualizar: ' . $stmt->error]);
        }
        $stmt->close();
        $conn->close();
        exit;
    }
    
    if ($accion === 'eliminar' && isset($_POST['noreloj'])) {
        $noreloj = $_POST['noreloj'];

        if ($noreloj === $accionRealizadaPor) {
            echo json_encode(['success' => false, 'mensaje' => 'No puedes eliminar tu propio usuario.']);
            exit;
        }

        $stmt = $conn->prepare("DELETE FROM empleados WHERE noreloj = ?");
        $stmt->bind_param("s", $noreloj);

        if ($stmt->execute()) {
            $sqlNotif = "INSERT INTO notificaciones (usuario_afectado, accion, autor, fecha) VALUES (?, ?, ?, NOW())";

            // Notificación para usuario eliminado
            $stmtNotif = $conn->prepare($sqlNotif);
            $accionAfectado = "Tu usuario fue eliminado";
            $stmtNotif->bind_param("sss", $noreloj, $accionAfectado, $accionRealizadaPor);
            $stmtNotif->execute();
            $stmtNotif->close();

            // Notificación para el que eliminó
            $stmtNotif = $conn->prepare($sqlNotif);
            $accionAutor = "Usuario Eliminado $noreloj";
            $stmtNotif->bind_param("sss", $accionRealizadaPor, $accionAutor, $accionRealizadaPor);
            $stmtNotif->execute();
            $stmtNotif->close();

            echo json_encode(['success' => true, 'mensaje' => 'Usuario eliminado correctamente']);
        } else {
            echo json_encode(['success' => false, 'mensaje' => 'Error al eliminar: ' . $stmt->error]);
        }

        $stmt->close();
        $conn->close();
        exit;
    }
}


// Si la acción no coincide con las anteriores
http_response_code(400);
echo json_encode(['error' => 'Acción inválida']);
exit;
