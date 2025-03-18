<?php
// Incluye el archivo de configuración de la base de datos
require_once 'db_config.php';
require_once 'auth.php'; 
requireAdmin();
// Verifica si se enviaron los datos del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']); // ID del rol a editar
    $nombre = trim($_POST['nombre']); // Nuevo nombre del rol

    // Validación básica
    if (empty($nombre)) {
        echo "<script>alert('Error: El nombre del rol no puede estar vacío.'); window.history.back();</script>";
        exit();
    }

    try {
        // Conexión a la base de datos usando PDO
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepara la consulta SQL para actualizar el rol
        $stmt = $pdo->prepare("UPDATE roles SET nombre = :nombre WHERE id = :id");
        $stmt->execute(['nombre' => $nombre, 'id' => $id]);

        // Verifica si se actualizó algún registro
        if ($stmt->rowCount() > 0) {
            // Muestra un mensaje de éxito y redirige a la lista de roles
            echo "<script>alert('Rol actualizado correctamente.'); window.location.href = 'listar_roles.php';</script>";
        } else {
            // Muestra un mensaje si no se encontró el rol
            echo "<script>alert('Error: No se encontró el rol.'); window.history.back();</script>";
        }
    } catch (PDOException $e) {
        // Muestra un mensaje de error si ocurre una excepción
        echo "<script>alert('Error al actualizar el rol: " . $e->getMessage() . "'); window.history.back();</script>";
    }
} else {
    // Muestra un mensaje si no se enviaron los datos del formulario
    echo "<script>alert('Error: Método no permitido.'); window.location.href = 'listar_roles.php';</script>";
}
?>