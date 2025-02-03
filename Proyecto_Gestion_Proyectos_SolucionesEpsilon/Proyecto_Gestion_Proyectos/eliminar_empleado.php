<?php
require_once 'db_config.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    try {
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("DELETE FROM empleados WHERE id = :id");
        $stmt->execute(['id' => $id]);

        echo "<script>alert('Empleado eliminado correctamente.'); window.location.href = 'listar_empleados.php';</script>";
    } catch (PDOException $e) {
        echo "<script>alert('Error al eliminar empleado: " . $e->getMessage() . "'); window.history.back();</script>";
    }
}
?>
