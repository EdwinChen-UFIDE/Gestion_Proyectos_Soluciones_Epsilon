<?php
session_start();
require_once 'db_config.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Conexión a la base de datos
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Consultar los empleados y sus roles
try {
    $stmt = $pdo->prepare("SELECT e.id, e.nombre, e.apellidos, e.cedula, e.telefono, e.email, r.nombre AS rol 
                           FROM empleados e 
                           LEFT JOIN roles r ON e.role_id = r.id 
                           ORDER BY e.id DESC");
    $stmt->execute();
    $empleados = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener empleados: " . $e->getMessage());
}
include 'Plantilla.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Empleados</title>
    <link rel="stylesheet" href="../CSS/estilos.css"> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php
    MostrarNavbar();
    ?>
        <div class="container mt-4">
        <h2 class="text-center mb-4">Lista de Empleados</h2>
        <div class="text-center mt-3">
            <a href="registrar_empleados.php" class="btn btn-success">Registrar Nuevo Empleado</a>
            <a href="listar_roles.php" class="btn btn-secondary ms-2">Ver Roles</a>
        </div>
        
        <table class="table table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Apellidos</th>
                    <th>Cédula</th>
                    <th>Teléfono</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($empleados as $empleado) : ?>
                    <tr>
                        <td><?= htmlspecialchars($empleado['id']) ?></td>
                        <td><?= htmlspecialchars($empleado['nombre']) ?></td>
                        <td><?= htmlspecialchars($empleado['apellidos']) ?></td>
                        <td><?= htmlspecialchars($empleado['cedula']) ?></td>
                        <td><?= htmlspecialchars($empleado['telefono']) ?></td>
                        <td><?= htmlspecialchars($empleado['email']) ?></td>
                        <td><?= htmlspecialchars($empleado['rol']) ?></td>
                        <td>
                            <a href="editar_empleado.php?id=<?= $empleado['id'] ?>" class="btn btn-primary btn-sm">Editar</a>
                            <a href="eliminar_empleado.php?id=<?= $empleado['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que deseas eliminar este empleado?');">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
