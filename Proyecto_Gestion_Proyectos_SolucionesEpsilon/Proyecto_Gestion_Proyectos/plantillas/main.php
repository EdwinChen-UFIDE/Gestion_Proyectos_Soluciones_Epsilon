<?php
session_start(); // Iniciar sesión
require_once __DIR__ . '/../db_config.php';
require_once __DIR__ . '/../plantilla.php';
// Carpeta donde están las plantillas (dentro de Proyecto_Gestion_Proyectos)
$carpeta = __DIR__ . '/';
$carpeta_thumbnails = '../../IMG/'; // Ruta hacia la carpeta IMG que está a nivel general

// Escanear todos los archivos en la carpeta
$archivos = array_diff(scandir($carpeta), array('..', '.', 'main.php')); // Ignora ".", ".." y este archivo

// Filtrar solo archivos que sigan el patrón plantillaX.html
$plantillas = [];
foreach ($archivos as $archivo) {
    if (preg_match('/^plantilla\d+\.html$/', $archivo)) {
        $plantillas[] = $archivo;
    }
}

// Ordenar las plantillas por número
usort($plantillas, function($a, $b) {
    preg_match('/\d+/', $a, $numA);
    preg_match('/\d+/', $b, $numB);
    return $numA[0] - $numB[0];
});

// Verificar si se seleccionó una plantilla
if (isset($_GET['plantilla'])) {
    $archivo = basename($_GET['plantilla']); // Seguridad: evita rutas externas
    $ruta = $carpeta . $archivo;

    if (file_exists($ruta)) {
        // Mostrar la plantilla seleccionada
        echo file_get_contents($ruta);
        echo '<p style="text-align:center; margin: 20px;">
                <a href="main.php" style="padding:10px 20px; background:#007BFF; color:white; text-decoration:none; border-radius:5px;">
                    Volver al menú de plantillas
                </a>
              </p>';
        exit;
    } else {
        echo "<h2>La plantilla no existe</h2>";
        echo '<p><a href="main.php">Volver</a></p>';
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Selector de Plantillas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../CSS/estilos.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;600&family=Roboto+Slab:wght@400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
       
        h1 { text-align: center; }
        .contenedor { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; padding: 20px; }
        .tarjeta { background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); text-align: center; }
        .tarjeta img { width: 100%; height: 150px; object-fit: cover; border-radius: 5px; }
        .tarjeta h3 { margin: 10px 0; font-size: 18px; }
        .tarjeta a { display: block; padding: 10px; margin-top: 10px; background: #007BFF; color: white; text-decoration: none; border-radius: 5px; }
        .tarjeta a:hover { background: #0056b3; }
    </style>
</head>
<body>
<?php MostrarNavbar(); ?>
<h1>Selecciona una Plantilla</h1>

<div class="contenedor">
    <?php foreach ($plantillas as $archivo): ?>
        <?php
            $numero = preg_replace('/[^0-9]/', '', $archivo);
            $thumbnail = $carpeta_thumbnails . 'plantilla' . $numero . '.jpg';

            // Verificar si la imagen realmente existe en la carpeta IMG
            if (!file_exists(__DIR__ . '/../../IMG/plantilla' . $numero . '.jpg')) {
                $thumbnail = 'https://via.placeholder.com/300x200.png?text=Sin+Vista+Previa';
            }
        ?>
        <div class="tarjeta">
            <img src="<?= $thumbnail ?>" alt="Vista previa de Plantilla <?= $numero ?>">
            <h3>Plantilla <?= $numero ?></h3>
            <a href="main.php?plantilla=<?= urlencode($archivo) ?>">Ver Plantilla</a>
            <a href="descargar.php?archivo=<?= urlencode($archivo) ?>" style="background: green; display: block; margin-top: 10px;">Descargar</a>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>
