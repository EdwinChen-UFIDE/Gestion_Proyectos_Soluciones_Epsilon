<?php
session_start();
require_once 'db_config.php';
include 'Plantilla.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Verifica si se ha proporcionado un ID válido en la URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = intval($_GET['id']); // Convierte el ID a un entero para seguridad

    try {
        // Conexión a la base de datos
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Obtener datos del rol
        $stmt = $pdo->prepare("SELECT * FROM roles WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $rol = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verifica si se encontró el rol
        if (!$rol) {
            echo "<script>alert('Error: No se encontró el rol.'); window.location.href = 'listar_roles.php';</script>";
            exit();
        }
    } catch (PDOException $e) {
        echo "<script>alert('Error al obtener el rol: " . $e->getMessage() . "'); window.history.back();</script>";
        exit();
    }
} else {
    echo "<script>alert('Error: ID no válido.'); window.location.href = 'listar_roles.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Rol</title>
    <link rel="stylesheet" href="../CSS/estilos.css"> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php MostrarNavbar(); ?>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-lg">
                    <div class="card-header text-white text-center" style="background-color: #0b4c66;">
                        <h2 class="h4">Editar Rol</h2>
                    </div>
                    <div class="card-body">
                        <form action="procesar_editar_rol.php" method="POST">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($rol['id']); ?>">

                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre del Rol:</label>
                                <input type="text" id="nombre" name="nombre" class="form-control" value="<?= htmlspecialchars($rol['nombre']); ?>" required>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-success">Guardar Cambios</button>
                                <a href="listar_roles.php" class="btn btn-secondary mt-2">Cancelar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
