<?php
require_once 'db_config.php';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("SELECT id, titulo AS title, fecha_inicio AS start, fecha_fin AS end FROM calendario");
    $eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($eventos);
} catch (PDOException $e) {
    echo json_encode([]);
}
?>
