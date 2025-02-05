<?php
require_once 'db_config.php';
session_start();

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}

// Obtener datos del usuario autenticado
$usuario_id = $_SESSION['user_id'];
$es_admin = ($_SESSION['role_id'] == 1); // Para ver si es admin

// Filtros opcionales
$filtro_empleado = $es_admin && isset($_GET['empleado_id']) ? intval($_GET['empleado_id']) : $usuario_id;
$filtro_fecha = isset($_GET['fecha']) ? $_GET['fecha'] : null;

// Construir consulta SQL dinámica
$sql = "SELECT e.id, e.fecha, e.comentarios, e.puntuacion, e.horas_trabajadas, 
               e.tareas_completadas, e.tareas_en_progreso, e.cumplimiento_plazos,
               emp.nombre, emp.apellidos
        FROM evaluaciones_desempeno e
        JOIN empleados emp ON e.empleado_id = emp.id
        WHERE e.empleado_id = :empleado_id";

$params = ['empleado_id' => $filtro_empleado];

if ($filtro_fecha) {
    $sql .= " AND e.fecha = :fecha";
    $params['fecha'] = $filtro_fecha;
}

$sql .= " ORDER BY e.fecha DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $evaluaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener evaluaciones: " . $e->getMessage());
}

// Obtener empleados (solo para admins)
$empleados = [];
if ($es_admin) {
    try {
        $stmt = $pdo->query("SELECT id, nombre, apellidos FROM empleados");
        $empleados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error al obtener empleados: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Evaluaciones</title>
</head>
<body>
    <h2>Lista de Evaluaciones</h2>

    <form method="GET" action="listar_evaluaciones.php">
        <?php if ($es_admin): ?>
            <label for="empleado_id">Empleado:</label>
            <select id="empleado_id" name="empleado_id">
                <option value="">Todos</option>
                <?php foreach ($empleados as $empleado): ?>
                    <option value="<?= htmlspecialchars($empleado['id']); ?>" <?= ($filtro_empleado == $empleado['id']) ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($empleado['nombre'] . " " . $empleado['apellidos']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        <?php endif; ?>

        <label for="fecha">Fecha:</label>
        <input type="date" id="fecha" name="fecha" value="<?= htmlspecialchars($filtro_fecha); ?>">
        <button type="submit">Filtrar</button>
    </form>

    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Empleado</th>
                <th>Fecha</th>
                <th>Puntuación</th>
                <th>Horas Trabajadas</th>
                <th>Tareas Completadas</th>
                <th>Tareas en Progreso</th>
                <th>Cumplimiento de Plazos</th>
                <th>Comentarios</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($evaluaciones as $evaluacion): ?>
                <tr>
                    <td><?= htmlspecialchars($evaluacion['id']); ?></td>
                    <td><?= htmlspecialchars($evaluacion['nombre'] . " " . $evaluacion['apellidos']); ?></td>
                    <td><?= htmlspecialchars($evaluacion['fecha']); ?></td>
                    <td><?= htmlspecialchars($evaluacion['puntuacion']); ?></td>
                    <td><?= htmlspecialchars($evaluacion['horas_trabajadas']); ?></td>
                    <td><?= htmlspecialchars($evaluacion['tareas_completadas']); ?></td>
                    <td><?= htmlspecialchars($evaluacion['tareas_en_progreso']); ?></td>
                    <td><?= htmlspecialchars($evaluacion['cumplimiento_plazos']) . '%'; ?></td>
                    <td><?= htmlspecialchars($evaluacion['comentarios']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
