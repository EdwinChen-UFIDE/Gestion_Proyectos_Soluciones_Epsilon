<?php
require_once 'db_config.php';

try {
    // Conexión a la base de datos
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta para obtener los roles
    $stmt = $pdo->query("SELECT id, nombre FROM roles");
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
    <style>
        table {
            width: 50%;
            border-collapse: collapse;
            margin: 20px auto;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        a {
            padding: 6px 12px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        a:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <h2 style="text-align: center;">Lista de Roles</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre del Rol</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($roles)): ?>
                <?php foreach ($roles as $rol): ?>
                    <tr>
                        <td><?php echo $rol['id']; ?></td>
                        <td><?php echo $rol['nombre']; ?></td>
                        <td>
                            <a href="editar_Rol.php?id=<?php echo $rol['id']; ?>">Editar</a>
                            <a href="eliminarRol.php?id=<?php echo $rol['id']; ?>" onclick="return confirm('¿Estás seguro de eliminar este rol?');">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" style="text-align: center;">No hay roles registrados.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div style="text-align: center;">
        <a href="Roles.php">Registrar Nuevo Rol</a>
    </div>
</body>
</html>
