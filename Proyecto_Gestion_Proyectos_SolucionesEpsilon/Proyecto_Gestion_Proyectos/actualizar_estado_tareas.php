<?php
session_start();
require_once 'db_config.php';

header('Content-Type: application/json');

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener datos enviados desde JavaScript
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['id']) || !isset($data['estado_id'])) {
        echo json_encode(["error" => "Datos incompletos."]);
        exit;
    }

    $taskId = (int)$data['id'];
    $estadoId = (int)$data['estado_id'];

    // Verificar si el estado es válido
    $validStates = [1, 2, 3]; 
    if (!in_array($estadoId, $validStates)) {
        echo json_encode(["error" => "Estado inválido."]);
        exit;
    }

    // Actualizar estado en la base de datos
    $sql = "UPDATE tareas SET estado_id = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$estadoId, $taskId]);

    // Verificar si se actualizó correctamente
    if ($stmt->rowCount() > 0) {
        echo json_encode(["success" => true, "message" => "Estado actualizado correctamente"]);
    } else {
        echo json_encode(["error" => "No se actualizó el estado."]);
    }

} catch (PDOException $e) {
    echo json_encode(["error" => "Error en la base de datos: " . $e->getMessage()]);
}
?>