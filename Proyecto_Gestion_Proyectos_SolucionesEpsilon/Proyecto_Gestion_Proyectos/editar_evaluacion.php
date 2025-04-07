<?php
require_once 'db_config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    die("Acceso no autorizado.");
}

$pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (!isset($_GET['id'])) {
    die("ID de evaluación no proporcionado.");
}

$id = intval($_GET['id']);

// Obtener datos actuales
$stmt = $pdo->prepare("SELECT e.*, u.nombre AS nombre_usuario 
                       FROM evaluaciones_desempeno e
                       JOIN usuarios u ON e.usuario_id = u.id
                       WHERE e.id = ?");
$stmt->execute([$id]);
$evaluacion = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$evaluacion) {
    die("Evaluación no encontrada.");
}

// Actualizar si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fecha = $_POST['fecha'];
    $comentarios = $_POST['comentarios'];
    $puntuacion = floatval($_POST['puntuacion']);
    $horas = intval($_POST['horas_trabajadas']);
    $completadas = intval($_POST['tareas_completadas']);
    $progreso = intval($_POST['tareas_progreso']);

    $stmt = $pdo->prepare("UPDATE evaluaciones_desempeno SET 
        fecha = ?, comentarios = ?, puntuacion = ?, 
        horas_trabajadas = ?, tareas_completadas = ?, tareas_en_progreso = ?
        WHERE id = ?");
    $stmt->execute([$fecha, $comentarios, $puntuacion, $horas, $completadas, $progreso, $id]);

    header("Location: listar_evaluaciones.php");
    exit;
}
include 'plantilla.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Evaluación</title>
    <link rel="stylesheet" href="../CSS/estilos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php MostrarNavbar(); ?>

    <div class="container mt-4">
        <h2 class="text-white p-3 rounded text-center" style="background-color: #0b4c66;">Editar Evaluación</h2>
        <h3 class="text-center mb-4 fw-semibold">Usuario: <?= htmlspecialchars($evaluacion['nombre_usuario']) ?></h3>

        <form method="POST" class="card p-4">
            <div class="mb-3">
                <label for="fecha" class="form-label">Fecha de Evaluación</label>
                <input type="date" id="fecha" name="fecha" class="form-control" required value="<?= htmlspecialchars($evaluacion['fecha']) ?>">
            </div>

            <div class="mb-3">
                <label for="comentarios" class="form-label">Comentarios</label>
                <textarea id="comentarios" name="comentarios" class="form-control" rows="3" required><?= htmlspecialchars($evaluacion['comentarios']) ?></textarea>
            </div>

            <div class="mb-3">
                <label for="puntuacion" class="form-label">Puntuación (1.0 - 10.0)</label>
                <input type="number" id="puntuacion" name="puntuacion" class="form-control" step="0.1" min="1.0" max="10.0" required value="<?= htmlspecialchars($evaluacion['puntuacion']) ?>">
            </div>

            <div class="mb-3">
                <label for="horas_trabajadas" class="form-label">Horas Trabajadas</label>
                <input type="number" id="horas_trabajadas" name="horas_trabajadas" class="form-control" required value="<?= htmlspecialchars($evaluacion['horas_trabajadas']) ?>">
            </div>

            <div class="mb-3">
                <label for="tareas_completadas" class="form-label">Tareas Completadas</label>
                <input type="number" id="tareas_completadas" name="tareas_completadas" class="form-control" required value="<?= htmlspecialchars($evaluacion['tareas_completadas']) ?>">
            </div>

            <div class="mb-3">
                <label for="tareas_progreso" class="form-label">Tareas en Progreso</label>
                <input type="number" id="tareas_progreso" name="tareas_progreso" class="form-control" required value="<?= htmlspecialchars($evaluacion['tareas_en_progreso']) ?>">
            </div>

            <button type="submit" class="btn btn-primary" style="background-color: #0b4c66;">Guardar Cambios</button>
            <a href="listar_evaluaciones.php" class="btn btn-secondary ms-2">Cancelar</a>
        </form>
    </div>
</body>
</html>
