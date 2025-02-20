<?php
require_once 'db_config.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    try {
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("DELETE FROM empleados WHERE id = :id");
        $stmt->execute(['id' => $id]);

        // Guardamos el mensaje en una variable de sesión
        session_start();
        $_SESSION['alert'] = ['type' => 'success', 'message' => 'Empleado eliminado correctamente.'];

        // Redirigir a la lista de empleados
        header("Location: listar_empleados.php");
        exit();
    } catch (PDOException $e) {
        session_start();
        $_SESSION['alert'] = ['type' => 'error', 'message' => 'Error al eliminar empleado: ' . $e->getMessage()];
        header("Location: listar_empleados.php");
        exit();
    }
}
?>
