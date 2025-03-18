<?php
Session_start();
// db_config.php: Configuración de la base de datos
require_once 'db_config.php';
Include 'Plantilla.php';
// Conexión a la base de datos
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seccion de Reportes</title>
    <link rel="stylesheet" href="../CSS/estilos.css">
   
</head>
<body>
<?php MostrarNavbar(); ?>
    <div class="main-container">
    <h2>Seleccionar Reporte</h2>
    <form action="procesar_reporte.php" method="GET" target="_blank">
        <label for="reporte">Tipo de Reporte:</label>
        <select name="reporte" required>
            <option value="proyectos_activos">Proyectos Activos</option>
            <option value="tareas_por_usuario">Tareas por Usuario</option>
            <option value="tareas_estado">Estados de Tareas</option>
            <option value="historial_sesiones">Historial de Sesiones</option>
            <option value="usuarios_activos">Usuarios Activos/Inactivos</option>
            <option value="productividad_empleados">Productividad por Empleado</option>
        </select>

        <label for="filtro">Filtro (Opcional):</label>
        <input type="text" name="filtro" placeholder="Ejemplo: ID de usuario/proyecto">

        <label for="formato">Formato de Exportación:</label>
        <select name="formato" required>
        <option value="pdf">PDF</option>
        <option value="excel">Excel (.xlsx)</option>
        <option value="csv">CSV</option>
        </select>

        <button type="submit">Generar PDF</button>
    </form>


</body>