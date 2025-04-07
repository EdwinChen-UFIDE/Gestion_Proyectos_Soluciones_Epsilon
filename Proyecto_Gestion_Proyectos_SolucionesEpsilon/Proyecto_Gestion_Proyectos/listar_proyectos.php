<?php
session_start();
require_once 'db_config.php';
include 'Plantilla.php';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}

// Obtener filtro de estado si se ha seleccionado
$filtro_estado = isset($_GET['filtro_estado']) ? $_GET['filtro_estado'] : '';

// Construcción de la consulta SQL
$sql = "SELECT * FROM proyectos";
$params = [];

if ($filtro_estado) {
    $sql .= " WHERE estado = ?";
    $params[] = $filtro_estado;
}

if (isset($_GET['ordenar']) && $_GET['ordenar'] == 'fecha') {
    $sql .= " ORDER BY fecha_creacion ASC";
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$proyectos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Proyectos</title>
    <link rel="stylesheet" href="../CSS/estilos.css"> 
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<?php MostrarNavbar(); ?>

<div class="form-container"> 
    <h2>Lista de Proyectos</h2>

    <!-- Filtro por estado -->
    <form method="GET" action="<?= BASE_URL ?>listar_proyectos.php"">
        <label for="filtro_estado">Filtrar por estado:</label>
        <select name="filtro_estado" onchange="this.form.submit()">
            <option value="">Todos</option>
            <option value="En progreso" <?= $filtro_estado == 'En progreso' ? 'selected' : ''; ?>>En progreso</option>
            <option value="En revisión" <?= $filtro_estado == 'En revisión' ? 'selected' : ''; ?>>En revisión</option>
            <option value="Finalizado" <?= $filtro_estado == 'Finalizado' ? 'selected' : ''; ?>>Finalizado</option>
            <option value="Inactivo" <?= $filtro_estado == 'Inactivo' ? 'selected' : ''; ?>>Inactivo</option>
        </select>
    </form>

    <a href="?ordenar=fecha" class="btn2">Ordenar por fecha</a>
    <a href="board.php" class="btn2">Tareas</a>
    <a href="registrar_proyectos.php" class="btn2">Registrar Proyecto</a>

    <table border="1">
        <tr>
            <th>ID</th>
            <th>Nombre del Proyecto</th>
            <th>Cliente</th>
            <th>Fecha de Creación</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
        <?php foreach ($proyectos as $proyecto): ?>
            <tr>
                <td><?= htmlspecialchars($proyecto['id']); ?></td>
                <td><?= htmlspecialchars($proyecto['nombre']); ?></td>
                <td><?= htmlspecialchars($proyecto['cliente']); ?></td>
                <td><?= htmlspecialchars($proyecto['fecha_creacion']); ?></td>
                <td><?= htmlspecialchars($proyecto['estado']); ?></td>
                <td>
                    <a href="ver_proyecto.php?id=<?= $proyecto['id']; ?>" class="btn">Ver Detalles</a>
                    <a href="editar_proyecto.php?id=<?= $proyecto['id']; ?>" class="btn">Editar</a>
                    <a href="javascript:void(0);" class="btn" onclick="confirmarEliminar(<?= $proyecto['id']; ?>);">Eliminar</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<script>
    function confirmarEliminar(id) {
        event.preventDefault();  // Prevenir que el enlace se siga
        Swal.fire({
            title: '¿Estás seguro?',
            text: "¡Este proyecto se eliminará permanentemente!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirigir al enlace de eliminación con el ID del proyecto
                window.location.href = "eliminar_proyecto.php?id=" + id;
            }
        });
    }
</script>

</body>
</html>
