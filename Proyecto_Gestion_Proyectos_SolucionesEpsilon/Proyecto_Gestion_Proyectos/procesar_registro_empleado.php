<?php
session_start();
require_once 'db_config.php';
require_once 'auth.php'; 
requireAdmin();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $apellidos = trim($_POST['apellidos']);
    $fecha_nacimiento = trim($_POST['fecha_nacimiento']);
    $cedula = trim($_POST['cedula']);
    $telefono = trim($_POST['telefono']);
    $email = trim($_POST['email']);
    $password = password_hash(trim($_POST['password']), PASSWORD_BCRYPT);
    $rol = intval($_POST['rol']);

    // Validar campos obligatorios
    if (empty($nombre) || empty($apellidos) || empty($fecha_nacimiento) || empty($cedula) || empty($telefono) || empty($email) || empty($password) || empty($rol)) {
        $_SESSION['alert'] = [
            'type' => 'error',
            'message' => 'Todos los campos son obligatorios.'
        ];
        header("Location: registrar_empleado.php");
        exit;
    }

    try {
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Validar si la cédula o el email ya existen
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM empleados WHERE cedula = :cedula OR email = :email");
        $stmt->execute(['cedula' => $cedula, 'email' => $email]);
        $existe = $stmt->fetchColumn();

        if ($existe > 0) {
            $_SESSION['alert'] = [
                'type' => 'error',
                'message' => 'La cédula o el correo electrónico ya están registrados.'
            ];
            header("Location: registrar_empleado.php");
            exit;
        }

        // Insertar empleado en la base de datos
        $stmt = $pdo->prepare("
            INSERT INTO empleados (nombre, apellidos, fecha_nacimiento, cedula, telefono, email, password, role_id) 
            VALUES (:nombre, :apellidos, :fecha_nacimiento, :cedula, :telefono, :email, :password, :role_id)
        ");
        $stmt->execute([
            'nombre' => $nombre,
            'apellidos' => $apellidos,
            'fecha_nacimiento' => $fecha_nacimiento,
            'cedula' => $cedula,
            'telefono' => $telefono,
            'email' => $email,
            'password' => $password,
            'role_id' => $rol
        ]);

        $_SESSION['alert'] = [
            'type' => 'success',
            'message' => 'Empleado registrado exitosamente.'
        ];
        header("Location: listar_empleados.php");
        exit;
    } catch (PDOException $e) {
        $_SESSION['alert'] = [
            'type' => 'error',
            'message' => 'Error al registrar el empleado: ' . $e->getMessage()
        ];
        header("Location: registrar_empleado.php");
        exit;
    }
}
?>
