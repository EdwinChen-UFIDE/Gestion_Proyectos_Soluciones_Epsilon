<?php
require_once 'db_config.php';
require_once 'auth.php'; 
requireAdmin();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $empleado_id = isset($_POST['empleado']) ? intval($_POST['empleado']) : null;
    $fecha = isset($_POST['fecha']) ? trim($_POST['fecha']) : null;
    $comentarios = isset($_POST['comentarios']) ? trim($_POST['comentarios']) : null;
    $puntuacion = isset($_POST['puntuacion']) ? floatval($_POST['puntuacion']) : null;
    $horas = isset($_POST['horas_trabajadas']) ? intval($_POST['horas_trabajadas']) : null;
    $completadas = isset($_POST['tareas_completadas']) ? intval($_POST['tareas_completadas']) : null;
    $progreso = isset($_POST['tareas_progreso']) ? intval($_POST['tareas_progreso']) : null;
    $plazos = isset($_POST['cumplimiento_plazos']) ? floatval($_POST['cumplimiento_plazos']) : null;

    // Registrar datos recibidos para depuración
    error_log("Datos recibidos: " . print_r($_POST, true));

    // Validar que los campos no sean nulos
    if (!$empleado_id || !$fecha || !$comentarios || !$puntuacion || !$horas || !$completadas || !$progreso || $plazos === null) {
        echo "<script>alert('Todos los campos son obligatorios.'); window.history.back();</script>";
        exit;
    }

    // Validar puntuación dentro del rango permitido
    if ($puntuacion < 1.0 || $puntuacion > 10.0) {
        echo "<script>alert('La puntuación debe estar entre 1.0 y 10.0.'); window.history.back();</script>";
        exit;
    }

    // Validar porcentaje de cumplimiento de plazos
    if ($plazos < 0 || $plazos > 100) {
        echo "<script>alert('El cumplimiento de plazos debe estar entre 0 y 100%.'); window.history.back();</script>";
        exit;
    }

    try {
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Verificar si el empleado existe
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM empleados WHERE id = :empleado_id");
        $stmt->execute(['empleado_id' => $empleado_id]);
        $existe = $stmt->fetchColumn();

        if ($existe == 0) {
            echo "<script>alert('El empleado seleccionado no existe.'); window.history.back();</script>";
            exit;
        }

        // Insertar evaluación en la base de datos
        $stmt = $pdo->prepare("
            INSERT INTO evaluaciones_desempeno 
            (empleado_id, fecha, comentarios, puntuacion, horas_trabajadas, tareas_completadas, tareas_en_progreso, cumplimiento_plazos) 
            VALUES (:empleado_id, :fecha, :comentarios, :puntuacion, :horas, :completadas, :progreso, :plazos)
        ");

        $stmt->execute([
            'empleado_id' => $empleado_id,
            'fecha' => $fecha,
            'comentarios' => $comentarios,
            'puntuacion' => $puntuacion,
            'horas' => $horas,
            'completadas' => $completadas,
            'progreso' => $progreso,
            'plazos' => $plazos
        ]);

        // Si la consulta se ejecutó correctamente, confirmamos la inserción
        if ($stmt->rowCount() > 0) {
            echo "<script>alert('Evaluación registrada exitosamente.'); window.location.href = 'homepageAdmin.php';</script>";
        } else {
            echo "<script>alert('Error: No se pudo insertar la evaluación.'); window.history.back();</script>";
        }

    } catch (PDOException $e) {
        error_log("Error en la consulta SQL: " . $e->getMessage()); // Guarda error en logs
        echo "<script>alert('Error al registrar la evaluación. Revisa el log.'); window.history.back();</script>";
    }
}
?>
