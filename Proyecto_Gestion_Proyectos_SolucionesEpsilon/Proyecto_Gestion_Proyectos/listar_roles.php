<?php
session_start();
require_once 'db_config.php';
Include 'Plantilla.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Conexión a la base de datos
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener roles y número de empleados en cada rol
    $stmt = $pdo->query("
        SELECT r.id, r.nombre, COUNT(e.id) AS num_empleados
        FROM roles r
        LEFT JOIN empleados e ON r.id = e.role_id
        GROUP BY r.id, r.nombre
    ");
    $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al conectar con la base de datos: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Roles</title>
    <link rel="stylesheet" href="../CSS/estilos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php MostrarNavbar(); ?>

    <div class="container mt-4">
        <h2 class="text-center mb-4">Lista de Roles</h2>
        <div class="text-center mt-3">
            <a href="registrar_roles.php" class="btn btn-success">Registrar Nuevo Rol</a>
        </div>
        <table class="table table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre del Rol</th>
                    <th>Número de Empleados</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($roles)): ?>
                    <?php foreach ($roles as $rol): ?>
                        <tr>
                            <td><?= htmlspecialchars($rol['id']) ?></td>
                            <td><?= htmlspecialchars($rol['nombre']) ?></td>
                            <td><?= htmlspecialchars($rol['num_empleados']) ?></td>
                            <td>
                                <a href="editar_Rol.php?id=<?= $rol['id'] ?>" class="btn btn-primary btn-sm">Editar</a>
                                <a href="eliminarRol.php?id=<?= $rol['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este rol?');">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center">No hay roles registrados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
