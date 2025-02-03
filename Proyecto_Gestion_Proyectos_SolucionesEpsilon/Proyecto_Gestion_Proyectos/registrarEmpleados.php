<?php
// Incluye la configuración de la base de datos
require_once 'db_config.php';

// Conexión a la base de datos
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}

// Obtener los roles de la base de datos
try {
    $stmt = $pdo->prepare("SELECT id, nombre FROM roles");
    $stmt->execute();
    $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener los roles: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Empleado</title>
</head>
<body>
    <h2>Registrar Nuevo Empleado</h2>
    <form method="POST" action="procesar_registro_empleado.php">
        <label for="nombre">Nombre:</label><br>
        <input type="text" id="nombre" name="nombre" required><br><br>

        <label for="apellidos">Apellidos:</label><br>
        <input type="text" id="apellidos" name="apellidos" required><br><br>

        <label for="fecha_nacimiento">Fecha de Nacimiento:</label><br>
        <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required><br><br>

        <label for="cedula">Cédula:</label><br>
        <input type="text" id="cedula" name="cedula" required><br><br>

        <label for="telefono">Número Telefónico:</label><br>
        <input type="text" id="telefono" name="telefono" required><br><br>

        <label for="email">Correo Electrónico:</label><br>
        <input type="email" id="email" name="email" required><br><br>

        <label for="password">Contraseña:</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <label for="rol">Rol:</label><br>
        <select id="rol" name="rol" required>
            <option value="">Seleccione un rol</option>
            <?php foreach ($roles as $rol): ?>
                <option value="<?= htmlspecialchars($rol['id']); ?>">
                    <?= htmlspecialchars($rol['nombre']); ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <button type="submit">Registrar Empleado</button>
    </form>
</body>
</html>