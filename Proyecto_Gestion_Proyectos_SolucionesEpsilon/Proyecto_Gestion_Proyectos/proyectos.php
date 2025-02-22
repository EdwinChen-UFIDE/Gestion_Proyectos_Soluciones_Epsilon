<?php
session_start();
require_once 'db_config.php';
include 'Plantilla.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $nombre = $_POST['nombre'];
        $cliente = $_POST['cliente'];
        $fecha_creacion = date('Y-m-d');

        $stmt = $pdo->prepare("INSERT INTO proyectos (nombre, cliente, fecha_creacion) VALUES (?, ?, ?)");
        $stmt->execute([$nombre, $cliente, $fecha_creacion]);

        echo "<script>
            alert('Proyecto creado exitosamente');
            window.location.href = 'proyectos.php';
        </script>";

    } catch (PDOException $e) {
        die("Error al crear el proyecto: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Proyectos</title>
    <link rel="stylesheet" href="../CSS/estilos.css"> 
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<?php MostrarNavbar(); ?>

<div class="form-container">
<?php MostrarNavbar(); ?>
    <h2>Crear Nuevo Proyecto</h2>
    <form action="proyectos.php" method="POST">
        <label for="nombre">Nombre del Proyecto:</label>
        <input type="text" name="nombre" id="nombre" required>

        <label for="cliente">Cliente:</label>
        <input type="text" name="cliente" id="cliente" required>

        <label for="cliente">Encargado:</label>
        <input type="text" name="cliente" id="cliente" required>

        <button type="submit" class="btn">Crear Proyecto</button>
    </form>
</div>
</body>
</html>