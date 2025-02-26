<?php
session_start();
require_once 'db_config.php';

header('Content-Type: application/json');

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Validar si los datos están presentes
    if (!isset($_POST['nombre']) || !isset($_POST['descripcion']) || !isset($_POST['estado'])) {
        echo json_encode(["error" => "Todos los campos son obligatorios."]);
        exit;
    }

    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $estado = (int)$_POST['estado'];
    $usuario_id = isset($_POST['usuario']) && !empty($_POST['usuario']) ? (int)$_POST['usuario'] : null;

    if (empty($nombre) || empty($descripcion)) {
        echo json_encode(["error" => "El nombre y la descripción son obligatorios."]);
        exit;
    }

    // Insertar tarea en la base de datos con usuario_id
    $sql = "INSERT INTO tareas (nombre, descripcion, estado_id, usuario_id, proyecto_id) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nombre, $descripcion, $estado, $usuario_id, $_POST['proyecto_id']]);

    // Obtener la tarea recién insertada
    $lastId = $pdo->lastInsertId();
    $sql = "SELECT id, nombre, descripcion, estado_id FROM tareas WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$lastId]);
    $nuevaTarea = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode(["success" => true, "tarea" => $nuevaTarea]);
} catch (PDOException $e) {
    echo json_encode(["error" => "Error en la base de datos: " . $e->getMessage()]);
}
?>
