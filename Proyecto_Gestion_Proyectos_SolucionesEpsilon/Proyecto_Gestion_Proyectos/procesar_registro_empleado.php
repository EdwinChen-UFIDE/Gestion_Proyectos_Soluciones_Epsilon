<?php
require_once 'db_config.php';

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
        echo "<script>alert('Todos los campos son obligatorios.'); window.history.back();</script>";
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
            echo "<script>alert('La cédula o el correo electrónico ya están registrados.'); window.history.back();</script>";
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

        echo "<script>alert('Empleado registrado exitosamente.'); window.location.href = 'homepageAdmin.php';</script>";
    } catch (PDOException $e) {
        echo "<script>alert('Error al registrar el empleado: " . $e->getMessage() . "'); window.history.back();</script>";
    }
}
?>