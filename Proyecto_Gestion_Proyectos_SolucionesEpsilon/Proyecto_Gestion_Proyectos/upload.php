<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $upload_dir = 'uploads/';
    
    // Asegurar que la carpeta existe
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $file_name = basename($_FILES['file']['name']);
    $file_path = $upload_dir . $file_name;
    
    // Extensiones permitidas
    $allowed_extensions = ['pdf', 'docx', 'xlsx', 'jpg', 'png', 'txt', 'html'];
    $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    if (in_array($file_extension, $allowed_extensions)) {
        if (move_uploaded_file($_FILES['file']['tmp_name'], $file_path)) {
            echo "Archivo subido correctamente.";
        } else {
            echo "Error al subir el archivo.";
        }
    } else {
        echo "Tipo de archivo no permitido.";
    }
} else {
    echo "No se recibió ningún archivo.";
}
?>
