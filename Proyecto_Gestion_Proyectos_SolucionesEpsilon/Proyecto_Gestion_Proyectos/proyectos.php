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
        $estado = $_POST['estado']; // Nuevo campo para estado
        $fecha_creacion = date('Y-m-d');

        // Insertar el proyecto con estado en la base de datos
        $stmt = $pdo->prepare("INSERT INTO proyectos (nombre, cliente, estado, fecha_creacion) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nombre, $cliente, $estado, $fecha_creacion]);

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
    <h2>Crear Nuevo Proyecto</h2>
    <form action="proyectos.php" method="POST">
        <label for="nombre">Nombre del Proyecto:</label>
        <input type="text" name="nombre" id="nombre" required>

        <label for="cliente">Cliente:</label>
        <input type="text" name="cliente" id="cliente" required>

        <!-- Nuevo campo para seleccionar estado -->
        <label for="estado">Estado del Proyecto:</label>
        <select name="estado" id="estado">
            <option value="En progreso">En progreso</option>
            <option value="En revisión">En revisión</option>
            <option value="Finalizado">Finalizado</option>
            <option value="Inactivo">Inactivo</option>
        </select>

        <button type="submit" class="btn">Crear Proyecto</button>
        <a href="listarproyectos.php" class="btn">Ver Proyectos Creados</a>
    </form>
</div>

</body>
</html>
