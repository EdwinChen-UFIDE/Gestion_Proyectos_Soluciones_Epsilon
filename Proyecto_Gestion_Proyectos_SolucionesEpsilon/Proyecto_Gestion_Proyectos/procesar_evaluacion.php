<?php
require_once 'db_config.php';
require_once 'auth.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = isset($_POST['empleado']) ? intval($_POST['empleado']) : null;
    $fecha = trim($_POST['fecha'] ?? '');
    $comentarios = trim($_POST['comentarios'] ?? '');
    $puntuacion = floatval($_POST['puntuacion'] ?? 0);
    $horas = intval($_POST['horas_trabajadas'] ?? 0);
    $completadas = intval($_POST['tareas_completadas'] ?? 0);
    $progreso = intval($_POST['tareas_progreso'] ?? 0);

    error_log("POST DATA: " . print_r($_POST, true));

    if (!$usuario_id || !$fecha || !$comentarios || !$puntuacion || !$horas || !$completadas || !$progreso) {
        echo "<script>alert('Todos los campos son obligatorios.'); window.history.back();</script>";
        exit;
    }

    if ($puntuacion < 1.0 || $puntuacion > 10.0) {
        echo "<script>alert('La puntuación debe estar entre 1.0 y 10.0.'); window.history.back();</script>";
        exit;
    }

    try {
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE id = :usuario_id");
        $stmt->execute(['usuario_id' => $usuario_id]);
        $exists = $stmt->fetchColumn();

        if (!$exists) {
            echo "<script>alert('El empleado no existe.'); window.history.back();</script>";
            exit;
        }

        $stmt = $pdo->prepare("
            INSERT INTO evaluaciones_desempeno 
            (usuario_id, fecha, comentarios, puntuacion, horas_trabajadas, tareas_completadas, tareas_en_progreso)
            VALUES (:usuario_id, :fecha, :comentarios, :puntuacion, :horas, :completadas, :progreso)
        ");

        $stmt->execute([
            'usuario_id' => $usuario_id,
            'fecha' => $fecha,
            'comentarios' => $comentarios,
            'puntuacion' => $puntuacion,
            'horas' => $horas,
            'completadas' => $completadas,
            'progreso' => $progreso
        ]);

        if ($stmt->rowCount() > 0) {
            echo "<script>alert('Evaluación registrada exitosamente.'); window.location.href = 'listar_evaluaciones.php';</script>";
        } else {
            echo "<script>alert('No se pudo guardar la evaluación.'); window.history.back();</script>";
        }
    } catch (PDOException $e) {
        error_log("Error SQL: " . $e->getMessage());
        echo "<script>alert('Error al guardar la evaluación.'); window.history.back();</script>";
    }
}
?>