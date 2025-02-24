<?php
session_start();
require_once 'db_config.php';
include 'Plantilla.php';

if (!isset($_GET['id'])) {
    die("ID de proyecto no proporcionado.");
}

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nombre = $_POST['nombre'];
        $cliente = $_POST['cliente'];

        $stmt = $pdo->prepare("UPDATE proyectos SET nombre = ?, cliente = ? WHERE id = ?");
        $stmt->execute([$nombre, $cliente, $_GET['id']]);

        echo "<script>
            alert('Proyecto actualizado correctamente');
            window.location.href = 'proyectos.php';
        </script>";
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM proyectos WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $proyecto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$proyecto) {
        die("Proyecto no encontrado.");
    }
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Proyecto</title>
    <link rel="stylesheet" href="../CSS/estilos.css">
</head>
<body>

<?php MostrarNavbar(); ?>

<div class="form-container">
    <h2>Editar Proyecto</h2>
    <form method="POST">
        <label for="nombre">Nombre del Proyecto:</label>
        <input type="text" name="nombre" id="nombre" value="<?= htmlspecialchars($proyecto['nombre']); ?>" required>

        <label for="cliente">Cliente:</label>
        <input type="text" name="cliente" id="cliente" value="<?= htmlspecialchars($proyecto['cliente']); ?>" required>

        <button type="submit" class="btn">Guardar Cambios</button>
    </form>
    <a href="proyectos.php" class="btn">Cancelar</a>
</div>

</body>
</html>
