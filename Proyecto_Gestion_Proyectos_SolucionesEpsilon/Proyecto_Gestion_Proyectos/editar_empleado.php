<?php
require_once 'db_config.php';

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
</head>
<body>
    <h2>Editar Empleado</h2>
    <form method="POST" action="procesar_editar_empleado.php">
        <input type="hidden" name="id" value="<?= htmlspecialchars($empleado['id']); ?>">

        <label for="nombre">Nombre:</label><br>
        <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($empleado['nombre']); ?>" required><br><br>

        <label for="apellidos">Apellidos:</label><br>
        <input type="text" id="apellidos" name="apellidos" value="<?= htmlspecialchars($empleado['apellidos']); ?>" required><br><br>

        <label for="fecha_nacimiento">Fecha de Nacimiento:</label><br>
        <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="<?= htmlspecialchars($empleado['fecha_nacimiento']); ?>" required><br><br>

        <label for="cedula">Cédula:</label><br>
        <input type="text" id="cedula" name="cedula" value="<?= htmlspecialchars($empleado['cedula']); ?>" required><br><br>

        <label for="telefono">Teléfono:</label><br>
        <input type="text" id="telefono" name="telefono" value="<?= htmlspecialchars($empleado['telefono']); ?>" required><br><br>

        <label for="email">Correo Electrónico:</label><br>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($empleado['email']); ?>" required><br><br>

        <label for="rol">Rol:</label><br>
        <select id="rol" name="rol" required>
            <?php foreach ($roles as $rol): ?>
                <option value="<?= htmlspecialchars($rol['id']); ?>" <?= $rol['id'] == $empleado['role_id'] ? 'selected' : ''; ?>>
                    <?= htmlspecialchars($rol['nombre']); ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <button type="submit">Actualizar</button>
    </form>
</body>
</html>
