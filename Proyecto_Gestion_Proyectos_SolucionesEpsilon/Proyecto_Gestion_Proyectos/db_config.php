<?php
$db_host = 'localhost';
$db_name = 'soluciones_epsilon';
$db_user = 'root';
$db_pass = ''; // Cambiar si tienes contraseña configurada

// Crear conexión
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>