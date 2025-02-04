<?php
// db_config.php: Configuración de la base de datos
require_once 'db_config.php';

// Conexión a la base de datos
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}

// Funciones para la gestión de roles
function listarRoles($pdo) {
    $stmt = $pdo->query("SELECT * FROM roles");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function registrarRol($pdo, $nombre) {
    $stmt = $pdo->prepare("INSERT INTO roles (nombre) VALUES (:nombre)");
    $stmt->execute(['nombre' => $nombre]);
}

function obtenerRol($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM roles WHERE id = :id");
    $stmt->execute(['id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function actualizarRol($pdo, $id, $nombre) {
    $stmt = $pdo->prepare("UPDATE roles SET nombre = :nombre WHERE id = :id");
    $stmt->execute(['nombre' => $nombre, 'id' => $id]);
}


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Roles</title>
</head>
<body>
    <h2>Registrar Nuevo Rol</h2>
    <form method="POST" action="">
        <label for="nombre">Nombre del Rol:</label><br>
        <input type="text" id="nombre" name="nombre" required><br><br>
        <button type="submit" name="registrar">Registrar Rol</button>
    </form>

    <h2>Lista de Roles</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Acciones</th>
        </tr>
        <?php
        $roles = listarRoles($pdo);
        foreach ($roles as $rol) {
            echo "<tr>";
            echo "<td>{$rol['id']}</td>";
            echo "<td>{$rol['nombre']}</td>";
            echo "<td>
                <a href='editar_rol.php?id={$rol['id']}'>Editar</a> |
                <a href='eliminarRol.php?id={$rol['id']}' onclick='return confirm(\"¿Estás seguro de eliminar este rol?\")'>Eliminar</a>
            </td>";
            echo "</tr>";
        }
        ?>
    </table>

    <?php
    // Procesar registro de rol
    if (isset($_POST['registrar'])) {
        $nombre = $_POST['nombre'];
        registrarRol($pdo, $nombre);
        echo "<p>Rol registrado exitosamente.</p>";
        header("Refresh:0"); // Recargar la página para actualizar la lista
    }
    ?>
</body>
</html>
