<?php
session_start();
require_once 'db_config.php';
include 'plantilla.php';
$templates = json_decode(file_get_contents('templates.json'), true);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<link rel="stylesheet" href="../CSS/estilos.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;600&family=Roboto+Slab:wght@400&display=swap" rel="stylesheet"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Seleccionar Plantilla</title>
</head>
<body>
    <?php MostrarNavBar(); ?>
    <div class="plantillas">
        <?php foreach ($templates as $template): ?>
            <div class="plantilla">
                <h3><?= htmlspecialchars($template['nombre']); ?></h3>
                <img src="<?= htmlspecialchars($template['preview']); ?>" width="300px" onerror="this.onerror=null; this.src='IMG/Bizland.jpg';">
                <a href="seleccion_plantilla.php?url=<?= urlencode($template['url']); ?>">Seleccionar</a>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
