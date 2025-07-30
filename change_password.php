<?php
session_start();

if (!isset($_SESSION['reset_noreloj'])) {
    header('Location: reset_password.php');
    exit();
}
?>

<!-- HTML (omitido por brevedad) -->

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['new_password'] !== $_POST['confirm_password']) {
        echo "<p class='error'>Las contraseñas no coinciden.</p>";
    } else {
        include '../php/DB_connection.php';

        $noreloj = $_SESSION['reset_noreloj'];
        $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE Empleados SET contrasena = ? WHERE noreloj = ?");
        $stmt->bind_param("ss", $new_password, $noreloj);

        if ($stmt->execute()) {
            echo "<p class='success'>Contraseña actualizada correctamente.</p>";
            echo '<a href="./Login.php">Volver al inicio de sesión</a>';
            
            // Notificaciones
            $autor = $_SESSION['noreloj'] ?? $noreloj;
            $accionAfectado = "Tu contraseña fue modificada";
            $accionAutor = "Has cambiado la contraseña de $noreloj";

            $sqlNotif = "INSERT INTO notificaciones (usuario_afectado, accion, autor, fecha) VALUES (?, ?, ?, NOW())";

            // Afectado
            $stmtNotif = $conn->prepare($sqlNotif);
            $stmtNotif->bind_param("sss", $noreloj, $accionAfectado, $autor);
            $stmtNotif->execute();
            $stmtNotif->close();

            // Editor (si es distinto del afectado)
            if ($autor !== $noreloj) {
                $stmtNotif = $conn->prepare($sqlNotif);
                $stmtNotif->bind_param("sss", $autor, $accionAutor, $autor);
                $stmtNotif->execute();
                $stmtNotif->close();
            }

            // Notificar jefes con puesto "Sr. Engineer" o "Engineer"
            $queryJefes = "SELECT noreloj FROM empleados WHERE puesto IN ('Sr. Engineer', 'Engineer') AND noreloj NOT IN (?, ?)";
            $stmtJefes = $conn->prepare($queryJefes);
            $stmtJefes->bind_param("ss", $noreloj, $autor);
            $stmtJefes->execute();
            $resultJefes = $stmtJefes->get_result();

            while ($row = $resultJefes->fetch_assoc()) {
                $norelojJefe = $row['noreloj'];
                $accionJefe = "El usuario $noreloj cambió su contraseña";
                $stmtNotif = $conn->prepare($sqlNotif);
                $stmtNotif->bind_param("sss", $norelojJefe, $accionJefe, $autor);
                $stmtNotif->execute();
                $stmtNotif->close();
            }

            $stmtJefes->close();
            unset($_SESSION['reset_noreloj']);

        } else {
            echo "<p class='error'>Error al actualizar la contraseña.</p>";
        }

        $stmt->close();
        $conn->close();
    }
}
?>
