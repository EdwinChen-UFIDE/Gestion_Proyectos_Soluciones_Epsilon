<?php
session_start();
require_once 'db_config.php';
require_once 'auth.php'; 
requireAdmin();
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
    $rol = isset($_POST['rol']) ? intval($_POST['rol']) : 0;

    if ($id <= 0 || empty($nombre) || empty($apellidos) || empty($fecha_nacimiento) || empty($cedula) || empty($telefono) || empty($email) || $rol <= 0) {
        $_SESSION['alert'] = [
            'type' => 'error',
            'message' => 'Todos los campos son obligatorios.'
        ];
        header("Location: editar_usuario.php?id=$id");
        exit;
    }

    try {
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Verificar si el rol existe
        $stmt = $pdo->prepare("SELECT id FROM roles WHERE id = :rol");
        $stmt->execute(['rol' => $rol]);
        if ($stmt->rowCount() == 0) {
            $_SESSION['alert'] = [
                'type' => 'error',
                'message' => 'El rol seleccionado no existe.'
            ];
            header("Location: editar_usuario.php?id=$id");
            exit;
        }

        // Actualizar el empleado
        $stmt = $pdo->prepare("
            UPDATE usuarios
            SET nombre = :nombre, apellidos = :apellidos, fecha_nacimiento = :fecha_nacimiento,
                cedula = :cedula, telefono = :telefono, email = :email, role_id = :rol
            WHERE id = :id
        ");
        $stmt->execute([
            'id' => $id,
            'nombre' => $nombre,
            'apellidos' => $apellidos,
            'fecha_nacimiento' => $fecha_nacimiento,
            'cedula' => $cedula,
            'telefono' => $telefono,
            'email' => $email,
            'rol' => $rol
        ]);

        $_SESSION['alert'] = [
            'type' => 'success',
            'message' => 'Empleado actualizado correctamente.'
        ];
        header("Location: listar_usuarios.php");
        exit;
    } catch (PDOException $e) {
        $_SESSION['alert'] = [
            'type' => 'error',
            'message' => 'Error al actualizar: ' . $e->getMessage()
        ];
        header("Location: editar_usuario.php?id=$id");
        exit;
    }
}
?>
