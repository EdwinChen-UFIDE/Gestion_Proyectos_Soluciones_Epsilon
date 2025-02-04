<?php
// Incluye el archivo de configuración de la base de datos
require_once 'db_config.php';

// Verifica si se ha proporcionado un ID válido en la URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = intval($_GET['id']); // Convierte el ID a un entero para seguridad

    try {
        // Conexión a la base de datos usando PDO
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepara la consulta SQL para eliminar el rol
        $stmt = $pdo->prepare("DELETE FROM roles WHERE id = :id");
        $stmt->execute(['id' => $id]);

        // Verifica si se eliminó algún registro
        if ($stmt->rowCount() > 0) {
            // Muestra un mensaje de éxito y redirige a la lista de roles
            echo "<script>alert('Rol eliminado correctamente.'); window.location.href = 'listar_roles.php';</script>";
        } else {
            // Muestra un mensaje si no se encontró el rol
            echo "<script>alert('Error: No se encontró el rol.'); window.history.back();</script>";
        }
    } catch (PDOException $e) {
        // Muestra un mensaje de error si ocurre una excepción
        echo "<script>alert('Error al eliminar el rol: " . $e->getMessage() . "'); window.history.back();</script>";
    }
} else {
    // Muestra un mensaje si no se proporcionó un ID válido
    echo "<script>alert('Error: ID no válido.'); window.history.back();</script>";
}
?>