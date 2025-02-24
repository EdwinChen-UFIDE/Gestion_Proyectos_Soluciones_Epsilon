<?php
session_start();
require_once 'db_config.php';

if (!isset($_GET['id'])) {
    die("ID de proyecto no proporcionado.");
}

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("DELETE FROM proyectos WHERE id = ?");
    $stmt->execute([$_GET['id']]);

    echo "<script>
        alert('Proyecto eliminado correctamente');
        window.location.href = 'proyectos.php';
    </script>";
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
