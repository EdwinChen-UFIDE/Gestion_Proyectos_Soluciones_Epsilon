<?php
$serverName = "JULIAN\SQLEXPRESS01";
$connectionInfo = array(
    "Database" => "SolucionesEpsilon", 
    "UID" => "", 
    "PWD" => ""
);
$conn = sqlsrv_connect($serverName, $connectionInfo);

if(!$conn) {
    die(print_r(sqlsrv_errors(), true));
}

session_start();

// Función para verificar roles
function tieneRol($rolesPermitidos) {
    if(empty($_SESSION['roles'])) return false;
    return !empty(array_intersect($_SESSION['roles'], $rolesPermitidos));
}
?>