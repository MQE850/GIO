<?php 
session_start();
require_once 'DB_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $noreloj = trim($_POST['noreloj']);
    $contra = trim($_POST['contra']);

    $noreloj = $conn->real_escape_string($noreloj);

    $sql = "SELECT * FROM empleados WHERE noreloj = '$noreloj'";
    $resultado = $conn->query($sql);

    if ($resultado && $resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();

        if (password_verify($contra, $usuario['contrasena'])) {
            $_SESSION['noreloj'] = $usuario['noreloj'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['apellido'] = $usuario['apellido'];
            $_SESSION['rol'] = $usuario['rol'];
            $_SESSION['departamento'] = $usuario['departamento'];

            if ($usuario['rol'] === 'admin' || $usuario['rol'] === 'colaborador') {
                header('Location: ../index.php');
            } else {
                header('Location: ../index.php');
            }
            exit();
        } else {
            echo "<script>
                alert('Contraseña incorrecta');
                window.location.href = '../webpages/Login.php';
            </script>";
        }
    } else {
        echo "<script>
            alert('Usuario no encontrado');
            window.location.href = '../webpages/Login.php';
        </script>";
    }

    $conn->close();
} else {
    header('Location: ../webpages/Login.php');
    exit();
}
