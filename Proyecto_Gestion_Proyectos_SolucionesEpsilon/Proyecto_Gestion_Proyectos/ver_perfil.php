<?php
session_start();
require_once 'db_config.php';
include 'Plantilla.php';
require_once 'auth.php'; 

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirigir al login si no está logueado
    exit();
}

$userId = $_SESSION['user_id'];

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener la información del usuario
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id");
    $stmt->execute(['id' => $userId]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        die("Usuario no encontrado.");
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Perfil</title>
    <link rel="stylesheet" href="../CSS/estilos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php MostrarNavbar(); ?>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-lg">
                    <div class="card-header text-white text-center" style="background-color: #0b4c66;">
                        <h2 class="h4">Perfil de Usuario</h2>
                    </div>
                    <div class="card-body">
                        <p><strong>Nombre:</strong> <?= htmlspecialchars($usuario['nombre']); ?></p>
                        <p><strong>Apellidos:</strong> <?= htmlspecialchars($usuario['apellidos']); ?></p>
                        <p><strong>Fecha de Nacimiento:</strong> <?= htmlspecialchars($usuario['fecha_nacimiento']); ?></p>
                        <p><strong>Cédula:</strong> <?= htmlspecialchars($usuario['cedula']); ?></p>
                        <p><strong>Teléfono:</strong> <?= htmlspecialchars($usuario['telefono']); ?></p>
                        <p><strong>Correo Electrónico:</strong> <?= htmlspecialchars($usuario['email']); ?></p>
                        <p><strong>Rol:</strong> <?= htmlspecialchars($usuario['role_id']); ?></p>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="editar_perfil.php?id=<?= htmlspecialchars($usuario['id']); ?>" class="btn btn-primary">Editar Perfil</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
