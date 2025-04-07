<?php
session_start();
require_once 'db_config.php';
require_once 'auth.php'; 
requireAdmin();
if (!isset($_GET['id'])) {
    die("ID de evaluacion no proporcionado.");
}
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Eliminar la evaluación
    $stmt = $pdo->prepare("DELETE FROM evaluaciones_desempeno WHERE id = ?");
    $stmt->execute([$_GET['id']]);

    // Redirigir a la página que mostrá SweetAlert
    header("Location: listar_evaluaciones.php?status=success");
    exit();

} catch (PDOException $e) {
    die("Error al eliminar la evaluación: " . $e->getMessage());
}
?>
