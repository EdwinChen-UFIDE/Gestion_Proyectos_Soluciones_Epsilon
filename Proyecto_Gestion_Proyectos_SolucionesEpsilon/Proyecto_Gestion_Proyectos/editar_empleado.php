<?php
session_start();
require_once 'db_config.php';
include 'Plantilla.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    try {
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Obtener información del empleado
        $stmt = $pdo->prepare("SELECT * FROM empleados WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $empleado = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$empleado) {
            die("Empleado no encontrado.");
        }

        // Obtener roles para el dropdown
        $rolesStmt = $pdo->query("SELECT id, nombre FROM roles");
        $roles = $rolesStmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Empleado</title>
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
                        <h2 class="h4">Editar Empleado</h2>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="procesar_editar_empleado.php">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($empleado['id']); ?>">

                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre:</label>
                                <input type="text" id="nombre" name="nombre" class="form-control" value="<?= htmlspecialchars($empleado['nombre']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="apellidos" class="form-label">Apellidos:</label>
                                <input type="text" id="apellidos" name="apellidos" class="form-control" value="<?= htmlspecialchars($empleado['apellidos']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento:</label>
                                <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="form-control" value="<?= htmlspecialchars($empleado['fecha_nacimiento']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="cedula" class="form-label">Cédula:</label>
                                <input type="text" id="cedula" name="cedula" class="form-control" value="<?= htmlspecialchars($empleado['cedula']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="telefono" class="form-label">Teléfono:</label>
                                <input type="text" id="telefono" name="telefono" class="form-control" value="<?= htmlspecialchars($empleado['telefono']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Correo Electrónico:</label>
                                <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($empleado['email']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="rol" class="form-label">Rol:</label>
                                <select id="rol" name="rol" class="form-select" required>
                                    <?php foreach ($roles as $rol): ?>
                                        <option value="<?= htmlspecialchars($rol['id']); ?>" <?= $rol['id'] == $empleado['role_id'] ? 'selected' : ''; ?>>
                                            <?= htmlspecialchars($rol['nombre']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-success">Actualizar</button>
                                <a href="listar_empleados.php" class="btn btn-secondary mt-2">Cancelar</a>
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
