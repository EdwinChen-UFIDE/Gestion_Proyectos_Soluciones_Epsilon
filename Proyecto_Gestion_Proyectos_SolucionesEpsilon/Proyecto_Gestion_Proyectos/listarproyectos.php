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

if (isset($_GET['ordenar']) && $_GET['ordenar'] == 'fecha') {
    $stmt = $pdo->prepare("SELECT * FROM proyectos ORDER BY fecha_creacion ASC");
} else {
    $stmt = $pdo->prepare("SELECT * FROM proyectos");
}
$stmt->execute();
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
    <a href="?ordenar=fecha" class="btn">Ordenar por fecha</a>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Nombre del Proyecto</th>
            <th>Cliente</th>
            <th>Fecha de Creación</th>
            <th>Acciones</th>
        </tr>
        <?php foreach ($proyectos as $proyecto): ?>
            <tr>
                <td><?= htmlspecialchars($proyecto['id']); ?></td>
                <td><?= htmlspecialchars($proyecto['nombre']); ?></td>
                <td><?= htmlspecialchars($proyecto['cliente']); ?></td>
                <td><?= htmlspecialchars($proyecto['fecha_creacion']); ?></td>
                <td>
                    <a href="ver_proyecto.php?id=<?= $proyecto['id']; ?>" class="btn">Ver Detalles</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

</body>
</html>
