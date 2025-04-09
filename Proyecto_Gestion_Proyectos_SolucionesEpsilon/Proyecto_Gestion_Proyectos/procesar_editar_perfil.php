<?php
session_start();
require_once 'db_config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $nombre = trim($_POST['nombre']);
    $apellidos = trim($_POST['apellidos']);
    $fecha_nacimiento = trim($_POST['fecha_nacimiento']);
    $cedula = trim($_POST['cedula']);
    $telefono = trim($_POST['telefono']);
    $email = trim($_POST['email']);

    if ($id <= 0 || empty($nombre) || empty($apellidos) || empty($fecha_nacimiento) || empty($cedula) || empty($telefono) || empty($email)) {
        $_SESSION['alert'] = [
            'type' => 'error',
            'message' => 'Todos los campos son obligatorios.'
        ];
        header("Location: editar_perfil.php?id=$id");
        exit;
    }

    try {
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Actualizar el empleado
        $stmt = $pdo->prepare("
            UPDATE usuarios
            SET nombre = :nombre, apellidos = :apellidos, fecha_nacimiento = :fecha_nacimiento,
                cedula = :cedula, telefono = :telefono, email = :email
            WHERE id = :id
        ");
        $stmt->execute([
            'id' => $id,
            'nombre' => $nombre,
            'apellidos' => $apellidos,
            'fecha_nacimiento' => $fecha_nacimiento,
            'cedula' => $cedula,
            'telefono' => $telefono,
            'email' => $email
        ]);

        $_SESSION['alert'] = [
            'type' => 'success',
            'message' => 'Empleado actualizado correctamente.'
        ];
        header("Location: ver_perfil.php");
        exit;
    } catch (PDOException $e) {
        $_SESSION['alert'] = [
            'type' => 'error',
            'message' => 'Error al actualizar: ' . $e->getMessage()
        ];
        header("Location: editar_perfil.php?id=$id");
        exit;
    }
}
?>
