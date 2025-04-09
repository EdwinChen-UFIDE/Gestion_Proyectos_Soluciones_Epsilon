<?php
Session_start();
require_once 'db_config.php';
Include 'Plantilla.php';
include 'reportes_config.php';
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
    <title>Sección de Reportes</title>
    <link rel="stylesheet" href="../CSS/estilos.css">
</head>
<style>
    /* Estilo del botón */
button.submit-btn {
    background-color: #007bff; 
    color: white;
    padding: 12px 20px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 16px;
    font-weight: bold;
    transition: background 0.3s ease, transform 0.3s ease; 
}

/* Efecto cuando pasa el ratón (hover) */
button.submit-btn:hover {
    background-color: #0056b3; 
    transform: scale(1.05); 
}

/* Efecto al hacer clic (activo) */
button.submit-btn:active {
    background-color: #004085;
    transform: scale(1); 
}

</style>
<body>
    <?php MostrarNavbar(); ?>
    <div class="main-container">
        <h2>Seleccionar Reporte</h2>
        <form action="procesar_reporte.php" method="GET" target="_blank">
    <label for="reporte">Tipo de Reporte:</label>
    <select name="reporte" required>
        <?php foreach ($REPORTES as $key => $reporte): ?>
            <option value="<?= $key ?>"><?= $reporte['titulo'] ?></option>
        <?php endforeach; ?>
    </select>

            <label for="formato">Formato de Exportación:</label>
            <select name="formato" required>
                <option value="pdf">PDF</option>
                <option value="excel">Excel (.xlsx)</option>
                <option value="csv">CSV</option>
            </select>

            <button type="submit" class="submit-btn">Generar Reporte</button>
        </form>
    </div>
</body>
</html>
