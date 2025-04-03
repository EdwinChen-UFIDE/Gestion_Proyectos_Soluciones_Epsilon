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

// Consultar los usuarios y sus roles
try {
    $stmt = $pdo->prepare("SELECT u.id, u.nombre, u.apellidos, u.cedula, u.telefono, u.email, r.nombre AS rol 
                           FROM usuarios u 
                           LEFT JOIN roles r ON u.role_id = r.id 
                           ORDER BY u.id DESC");
    $stmt->execute();
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener usuarios: " . $e->getMessage());
}

include 'Plantilla.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Usuarios</title>
    <link rel="stylesheet" href="../CSS/estilos.css"> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> 
</head>
<body>
<?php MostrarNavbar(); ?>

<div class="container mt-4">
    <h2 class="text-center mb-4">Lista de Usuarios</h2>
    <div class="text-center mt-3">
        <a href="registrar_usuario.php" class="btn btn-success">Registrar Nuevo Usuario</a>
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
            <?php foreach ($usuarios as $usuario) : ?>
                <tr>
                    <td><?= htmlspecialchars($usuario['id']) ?></td>
                    <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                    <td><?= htmlspecialchars($usuario['apellidos']) ?></td>
                    <td><?= htmlspecialchars($usuario['cedula']) ?></td>
                    <td><?= htmlspecialchars($usuario['telefono']) ?></td>
                    <td><?= htmlspecialchars($usuario['email']) ?></td>
                    <td><?= htmlspecialchars($usuario['rol']) ?></td>
                    <td>
                        <a href="editar_usuario.php?id=<?= $usuario['id'] ?>" class="btn btn-primary btn-sm">Editar</a>
                        <button class="btn btn-danger btn-sm" onclick="confirmarEliminacion(<?= $usuario['id'] ?>)">Eliminar</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
// Función para confirmar eliminación con SweetAlert
function confirmarEliminacion(id) {
    Swal.fire({
        title: "¿Estás seguro?",
        text: "Esta acción no se puede deshacer.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "eliminar_usuario.php?id=" + id;
        }
    });
}

</script>

</body>
</html>
