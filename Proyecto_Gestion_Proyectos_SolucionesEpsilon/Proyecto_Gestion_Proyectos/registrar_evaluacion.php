<?php 
require_once 'db_config.php';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}

// Obtener empleados para asignar la evaluación
try {
    $stmt = $pdo->prepare("SELECT id, nombre, apellidos FROM empleados");
    $stmt->execute();
    $empleados = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener empleados: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Evaluación</title>
</head>
<body>
    <h2>Registrar Nueva Evaluación</h2>
    <form method="POST" action="procesar_evaluacion.php">
        <label for="empleado">Empleado:</label><br>
        <select id="empleado" name="empleado" required>
            <option value="">Seleccione un empleado</option>
            <?php foreach ($empleados as $empleado): ?>
                <option value="<?= htmlspecialchars($empleado['id']); ?>">
                    <?= htmlspecialchars($empleado['nombre'] . " " . $empleado['apellidos']); ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <label for="fecha">Fecha de Evaluación:</label><br>
        <input type="date" id="fecha" name="fecha" required><br><br>

        <label for="comentarios">Comentarios:</label><br>
        <textarea id="comentarios" name="comentarios" required></textarea><br><br>

        <label for="puntuacion">Puntuación (1.0 - 10.0):</label><br>
        <input type="number" id="puntuacion" name="puntuacion" step="0.1" min="1.0" max="10.0" required><br><br>

        <label for="horas_trabajadas">Horas Trabajadas:</label><br>
        <input type="number" id="horas_trabajadas" name="horas_trabajadas" required><br><br>

        <label for="tareas_completadas">Tareas Completadas:</label><br>
        <input type="number" id="tareas_completadas" name="tareas_completadas" required><br><br>

        <label for="tareas_progreso">Tareas en Progreso:</label><br>
        <input type="number" id="tareas_progreso" name="tareas_progreso" required><br><br>

        <label for="cumplimiento_plazos">Cumplimiento de Plazos (%):</label><br>
        <input type="number" id="cumplimiento_plazos" name="cumplimiento_plazos" min="0" max="100" required><br><br>

        <button type="submit">Registrar Evaluación</button>
    </form>
</body>
</html>
