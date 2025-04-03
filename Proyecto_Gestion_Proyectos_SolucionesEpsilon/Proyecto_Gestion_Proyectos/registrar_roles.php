<?php
session_start();
require_once 'db_config.php';
include 'Plantilla.php';
require_once 'auth.php'; 
requireAdmin();
// Conexión a la base de datos
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}

// Funciones para la gestión de roles
function listarRoles($pdo) {
    $sql = "
        SELECT r.id, r.nombre, COUNT(e.id) AS num_usuarios
        FROM roles r
        LEFT JOIN usuarios e ON r.id = e.role_id
        GROUP BY r.id, r.nombre
    ";
    $stmt = $pdo->query($sql);
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
    <link rel="stylesheet" href="../CSS/estilos.css">
   
</head>
<body>
<?php MostrarNavbar(); ?>
    <div class="main-container">
        <div class="form-container" id="registro-rol"> 
            <h2>Registrar Nuevo Rol</h2>
            <form method="POST" action="">
                <label for="nombre">Nombre del Rol:</label>
                <input type="text" id="nombre" name="nombre" required>
                <button type="submit" name="registrar">Registrar Rol</button>
            </form>
        </div>

        <div class="form-container" id="registro-rol"> 
            <h2>Lista de Roles</h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Número de Empleados</th>
                    <th>Acciones</th>
                </tr>
                <?php
                $roles = listarRoles($pdo);
                foreach ($roles as $rol) {
                    echo "<tr>";
                    echo "<td>{$rol['id']}</td>";
                    echo "<td>{$rol['nombre']}</td>";
                    echo "<td>{$rol['num_usuarios']}</td>";
                    echo "<td>
                        <a href='editar_rol.php?id={$rol['id']}'>Editar</a> |
                        <a href='eliminarRol.php?id={$rol['id']}' onclick='return confirm(\"¿Estás seguro de eliminar este rol?\")'>Eliminar</a>
                    </td>";
                    echo "</tr>";
                }
                ?>
            </table>
        </div>
    </div>

    <?php
    // Procesar registro de rol
    if (isset($_POST['registrar'])) {
        $nombre = $_POST['nombre'];
        registrarRol($pdo, $nombre);
        echo "<p>Rol registrado exitosamente.</p>";
        header("Refresh:0"); 
    }
    ?>
</body>
</html>