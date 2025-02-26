<?php
session_start();
require_once 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $contenido = $_POST['contenido'];

    $sql = "INSERT INTO plantillas (nombre, contenido, fecha_creacion, fecha_actualizacion) VALUES (?, ?, NOW(), NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $nombre, $contenido);

    if ($stmt->execute()) {
        echo "Plantilla creada exitosamente.";
    } else {
        echo "Error al guardar la plantilla.";
    }

    $stmt->close();
    $conn->close();
}
?>
