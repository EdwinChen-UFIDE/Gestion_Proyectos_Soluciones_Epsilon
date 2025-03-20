<?php
$db_host = 'localhost';
$db_name = 'soluciones_epsilon';
$db_user = 'root';
$db_pass = ''; // Cambiar si tienes contraseña configurada

define('BASE_PATH', __DIR__ . '/');
define('BASE_URL', 'http://localhost/Gestion_Proyectos_Soluciones_Epsilon/Proyecto_Gestion_Proyectos_SolucionesEpsilon/Proyecto_Gestion_Proyectos/');
define('IMG_URL', 'http://localhost/Gestion_Proyectos_Soluciones_Epsilon/Proyecto_Gestion_Proyectos_SolucionesEpsilon/IMG/');
// Crear conexión
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

?>