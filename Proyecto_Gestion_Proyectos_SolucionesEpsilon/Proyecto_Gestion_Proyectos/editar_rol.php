<?php
// Incluye el archivo de configuración de la base de datos
require_once 'db_config.php';

// Verifica si se ha proporcionado un ID válido en la URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = intval($_GET['id']); // Convierte el ID a un entero para seguridad

    try {
        // Conexión a la base de datos usando PDO
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Obtiene los datos del rol actual
        $stmt = $pdo->prepare("SELECT * FROM roles WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $rol = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verifica si se encontró el rol
        if (!$rol) {
            echo "<script>alert('Error: No se encontró el rol.'); window.location.href = 'listar_roles.php';</script>";
            exit();
        }
    } catch (PDOException $e) {
        // Muestra un mensaje de error si ocurre una excepción
        echo "<script>alert('Error al obtener el rol: " . $e->getMessage() . "'); window.history.back();</script>";
        exit();
    }
} else {
    // Muestra un mensaje si no se proporcionó un ID válido
    echo "<script>alert('Error: ID no válido.'); window.location.href = 'listar_roles.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Rol</title>
</head>
<body>
    <h1>Editar Rol</h1>
    <form action="procesar_editar_rol.php" method="POST">
        <input type="hidden" name="id" value="<?php echo $rol['id']; ?>">
        <label for="nombre">Nombre del Rol:</label>
        <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($rol['nombre']); ?>" required>
        <br><br>
        <button type="submit">Guardar Cambios</button>
    </form>
</body>
</html>