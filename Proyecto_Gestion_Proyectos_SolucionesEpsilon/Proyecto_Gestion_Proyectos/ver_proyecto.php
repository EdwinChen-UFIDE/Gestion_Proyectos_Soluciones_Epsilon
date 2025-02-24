<?php
session_start();
require_once 'db_config.php';
include 'Plantilla.php';

if (!isset($_GET['id'])) {
    die("ID de proyecto no proporcionado.");
}

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT * FROM proyectos WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $proyecto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$proyecto) {
        die("Proyecto no encontrado.");
    }
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalles del Proyecto</title>
    <link rel="stylesheet" href="../CSS/estilos.css">
</head>
<body>

<?php MostrarNavbar(); ?>

<div class="form-container">
    <h2>Detalles del Proyecto</h2>
    <p><strong>ID:</strong> <?= htmlspecialchars($proyecto['id']); ?></p>
    <p><strong>Nombre:</strong> <?= htmlspecialchars($proyecto['nombre']); ?></p>
    <p><strong>Cliente:</strong> <?= htmlspecialchars($proyecto['cliente']); ?></p>
    <p><strong>Fecha de Creación:</strong> <?= htmlspecialchars($proyecto['fecha_creacion']); ?></p>
    
    <a href="editar_proyecto.php?id=<?= $proyecto['id']; ?>" class="btn">Editar</a>
    <a href="eliminar_proyecto.php?id=<?= $proyecto['id']; ?>" class="btn" onclick="return confirm('¿Estás seguro de eliminar este proyecto?');">Eliminar</a>
    <a href="proyectos.php" class="btn">Volver</a>
</div>

</body>
</html>
