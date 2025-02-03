<?php
require_once 'db_config.php';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener todos los empleados
    $stmt = $pdo->query("SELECT empleados.id, empleados.nombre, empleados.apellidos, empleados.cedula, empleados.email, roles.nombre AS rol
                         FROM empleados
                         JOIN roles ON empleados.role_id = roles.id");
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
    <title>Lista de Empleados</title>
</head>
<body>
    <h2>Lista de Empleados</h2>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Apellidos</th>
                <th>Cédula</th>
                <th>Correo Electrónico</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($empleados as $empleado): ?>
                <tr>
                    <td><?= htmlspecialchars($empleado['id']); ?></td>
                    <td><?= htmlspecialchars($empleado['nombre']); ?></td>
                    <td><?= htmlspecialchars($empleado['apellidos']); ?></td>
                    <td><?= htmlspecialchars($empleado['cedula']); ?></td>
                    <td><?= htmlspecialchars($empleado['email']); ?></td>
                    <td><?= htmlspecialchars($empleado['rol']); ?></td>
                    <td>
                        <a href="editar_empleado.php?id=<?= $empleado['id']; ?>">Editar</a>
                        <a href="eliminar_empleado.php?id=<?= $empleado['id']; ?>" onclick="return confirm('¿Está seguro de que desea eliminar este empleado?');">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
