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
    <title>Seccion de contabilidad</title>
    <link rel="stylesheet" href="../CSS/estilos.css">
   
</head>
<body>
<?php MostrarNavbar(); ?>
    <div class="main-container">
        <div class="form-container" id="contabilidad"> 
            <h2>Placeholder temporal para desarrollo futuro (Contabilidad)</h2>


</body>