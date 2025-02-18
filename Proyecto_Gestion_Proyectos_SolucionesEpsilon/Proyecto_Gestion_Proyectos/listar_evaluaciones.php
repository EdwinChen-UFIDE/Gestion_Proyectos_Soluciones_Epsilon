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
if (!isset($_SESSION['user_id'])) {
    die("Error: Usuario no autenticado.");
}

$usuario_id = intval($_SESSION['user_id']);
$es_admin = isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1;

// Obtener valores del filtro
$filtro_empleado = $es_admin && isset($_GET['empleado_id']) && $_GET['empleado_id'] !== '' ? intval($_GET['empleado_id']) : null;
$filtro_fecha = isset($_GET['fecha']) && !empty($_GET['fecha']) ? $_GET['fecha'] : null;

// Construcción de la consulta SQL
$sql = "SELECT e.id, e.fecha, e.comentarios, e.puntuacion, e.horas_trabajadas, 
               e.tareas_completadas, e.tareas_en_progreso, e.cumplimiento_plazos,
               emp.nombre, emp.apellidos
        FROM evaluaciones_desempeno e
        JOIN empleados emp ON e.empleado_id = emp.id";

$params = [];

if ($filtro_empleado) {
    $sql .= " WHERE e.empleado_id = :empleado_id";
    $params['empleado_id'] = $filtro_empleado;
}

if ($filtro_fecha) {
    $sql .= $filtro_empleado ? " AND" : " WHERE";
    $sql .= " DATE(e.fecha) = :fecha";
    $params['fecha'] = $filtro_fecha;
}

// Ordenar por fecha y limitar a 10 resultados
$sql .= " ORDER BY e.fecha DESC LIMIT 10";

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
include 'plantilla.php'
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Evaluaciones</title>
    <link rel="stylesheet" href="../CSS/estilos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">
    <?php
    MostrarNavbar();
    ?>
    <div class="container mt-4">
        <h2 class="text-white p-3 rounded"style="background-color: #0b4c66;">Mis Evaluaciones</h2>

        <!-- Filtros -->
        <div class="card p-3 mb-3">
            <form method="GET" action="listar_evaluaciones.php" class="row g-3">
                <?php if ($es_admin): ?>
                    <div class="col-md-6">
                        <label for="empleado_id" class="form-label">Empleado:</label>
                        <select id="empleado_id" name="empleado_id" class="form-select">
                            <option value="">Todos</option>
                            <?php foreach ($empleados as $empleado): ?>
                                <option value="<?= htmlspecialchars($empleado['id']); ?>" <?= ($filtro_empleado == $empleado['id']) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($empleado['nombre'] . " " . $empleado['apellidos']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>

                <div class="col-md-4">
                    <label for="fecha" class="form-label">Fecha:</label>
                    <input type="date" id="fecha" name="fecha" value="<?= htmlspecialchars($filtro_fecha); ?>" class="form-control">
                </div>

                <div class="col-md-2 d-flex align-items-center justify-content-center">
                    <button type="submit" class="btn btn-primary w-100" style="background-color: #0b4c66;">Filtrar</button>
                </div>
            </form>
        </div>

        <!-- Lista de Evaluaciones -->
        <?php foreach ($evaluaciones as $evaluacion): ?>
            <div class="card mb-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><?= date('F Y', strtotime($evaluacion['fecha'])); ?></h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <p class="mb-1"><strong>Horas Trabajadas:</strong> <?= htmlspecialchars($evaluacion['horas_trabajadas']); ?> horas</p>
                            <p class="mb-1"><strong>Tareas Completadas:</strong> <?= htmlspecialchars($evaluacion['tareas_completadas']); ?> tareas</p>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-1"><strong>Cumplimiento de plazos:</strong> <span class="text-success"><?= htmlspecialchars($evaluacion['cumplimiento_plazos']); ?>%</span></p>
                        </div>
                        <div class="col-md-4 text-end">
                            <span class="badge bg-secondary fs-5"><?= number_format($evaluacion['puntuacion'], 1); ?>/10</span>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light">
                    <strong>Comentario Adicional:</strong>
                    <p class="mb-0"><?= htmlspecialchars($evaluacion['comentarios']); ?></p>
                </div>
            </div>
        <?php endforeach; ?>

    </div>
</body>
</html>
