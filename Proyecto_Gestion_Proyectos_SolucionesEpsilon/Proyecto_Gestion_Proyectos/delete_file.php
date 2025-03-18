<?php
if (isset($_GET['file'])) {
    $file = basename($_GET['file']); 
    $file_path = "uploads/" . $file; 

    if (file_exists($file_path)) {
        if (unlink($file_path)) {
            echo "Archivo eliminado correctamente.";
        } else {
            echo "Error al eliminar el archivo.";
        }
    } else {
        echo "El archivo no existe.";
    }
} else {
    echo "No se especificó ningún archivo.";
}
?>
