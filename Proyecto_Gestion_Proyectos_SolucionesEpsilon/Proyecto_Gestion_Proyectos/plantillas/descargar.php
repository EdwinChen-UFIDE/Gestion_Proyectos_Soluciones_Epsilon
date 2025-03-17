<?php
// Carpeta donde están las plantillas (la misma donde está este archivo descargar.php)
$carpeta = __DIR__ . '/';

// Verificar si se ha proporcionado un archivo para descargar
if (isset($_GET['archivo'])) {
    $archivo = basename($_GET['archivo']); 
    $ruta = $carpeta . $archivo;

    // Verificar si el archivo existe
    if (file_exists($ruta)) {
        // Configurar las cabeceras para forzar la descarga
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($ruta) . '.html"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($ruta));
        readfile($ruta);
        exit;
    } else {
        echo "<h2>El archivo no existe</h2>";
        echo '<p><a href="main.php">Volver</a></p>';
        exit;
    }
} else {
    echo "<h2>No se ha especificado ningún archivo para descargar</h2>";
    echo '<p><a href="main.php">Volver</a></p>';
    exit;
}
?>