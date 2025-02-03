<?php
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
    $rol = isset($_POST['rol']) ? intval($_POST['rol']) : 0;

    if ($id <= 0 || empty($nombre) || empty($apellidos) || empty($fecha_nacimiento) || empty($cedula) || empty($telefono) || empty($email) || $rol <= 0) {
        die("Error: Todos los campos son obligatorios.");
    }

    try {
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Verificar si el rol existe
        $stmt = $pdo->prepare("SELECT id FROM roles WHERE id = :rol");
        $stmt->execute(['rol' => $rol]);
        if ($stmt->rowCount() == 0) {
            die("Error: El rol seleccionado no existe.");
        }

        // Actualizar el empleado
        $stmt = $pdo->prepare("
            UPDATE empleados
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

        echo "<script>alert('Empleado actualizado correctamente.'); window.location.href = 'listar_empleados.php';</script>";
        exit;
    } catch (PDOException $e) {
        die("Error al actualizar: " . $e->getMessage());
    }
}
?>
