<?php
require_once 'db_config.php';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $id = $_POST['id'] ?? null;
    $estado = $_POST['estado'] ?? null;

    if ($id !== null && $estado !== null) {
        $stmt = $pdo->prepare("UPDATE facturas SET pagada = ? WHERE id = ?");
        $stmt->execute([$estado, $id]);

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Datos incompletos.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
