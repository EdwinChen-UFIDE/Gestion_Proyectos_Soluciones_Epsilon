<?php
session_start();
require_once 'db_config.php';
require_once 'auth.php'; 
requireAdmin();
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
            $_SESSION['alert'] = [
                'type' => 'success',
                'message' => 'Rol eliminado correctamente.'
            ];
            header("Location: listar_roles.php");
            exit;
        } else {
            // Muestra un mensaje si no se encontró el rol
            $_SESSION['alert'] = [
                'type' => 'error',
                'message' => 'Error: No se encontró el rol.'
            ];
            header("Location: listar_roles.php");
            exit;
        }
    } catch (PDOException $e) {
        // Muestra un mensaje de error si ocurre una excepción
        $_SESSION['alert'] = [
            'type' => 'error',
            'message' => 'Error al eliminar el rol: ' . $e->getMessage()
        ];
        header("Location: listar_roles.php");
        exit;
    }
} else {
    // Muestra un mensaje si no se proporcionó un ID válido
    $_SESSION['alert'] = [
        'type' => 'error',
        'message' => 'Error: ID no válido.'
    ];
    header("Location: listar_roles.php");
    exit;
}
?>
