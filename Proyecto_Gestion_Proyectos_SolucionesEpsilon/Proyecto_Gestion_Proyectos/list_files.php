<?php
$upload_dir = 'uploads/';

// Verificar que la carpeta existe
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$files = array_diff(scandir($upload_dir), ['.', '..']);

if (count($files) > 0) {
    foreach ($files as $file) {
        $file_extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

        // Asignar iconos según el tipo de archivo
        switch ($file_extension) {
            case 'pdf':
                $icon = '<i class="fas fa-file-pdf"></i>';
                break;
            case 'docx':
                $icon = '<i class="fas fa-file-word"></i>';
                break;
            case 'xlsx':
                $icon = '<i class="fas fa-file-excel"></i>';
                break;
            case 'jpg': case 'png':
                $icon = '<i class="fas fa-file-image"></i>';
                break;
            case 'txt':
                $icon = '<i class="fas fa-file-alt"></i>';
                break;
            case 'html':
                $icon = '<i class="fas fa-file-code"></i>'; // Ícono especial para HTML
                break;
            default:
                $icon = '<i class="fas fa-file"></i>';
                break;
        }

        echo "<div class='file-item'>$icon <a href='$upload_dir$file' target='_blank'>$file</a>
              <button class='delete-btn' onclick='deleteFile(\"$file\")'>Eliminar</button></div>";
    }
} else {
    echo "No hay archivos subidos.";
}
?>
