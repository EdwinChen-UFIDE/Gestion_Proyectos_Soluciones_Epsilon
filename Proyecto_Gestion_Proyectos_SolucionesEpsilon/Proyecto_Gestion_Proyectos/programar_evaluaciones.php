<?php
include('config.php');

if(!tieneRol(['Administrador'])) {
    header("HTTP/1.1 403 Forbidden");
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

// Validar frecuencia
$frecuenciasPermitidas = ['Semanal', 'Mensual', 'Trimestral', 'Anual'];
if(!in_array($data['Frecuencia'], $frecuenciasPermitidas)) {
    http_response_code(400);
    echo json_encode(['error' => 'Frecuencia no válida']);
    exit;
}

$sql = "INSERT INTO Evaluaciones_Programadas 
        (ID_usuario, Frecuencia, Fecha_inicio, ID_estado)
        VALUES (?, ?, ?, 
        (SELECT ID_estado FROM Estado WHERE Nombre_estado = 'Activo'))";
        
$params = array(
    $data['ID_usuario'],
    $data['Frecuencia'],
    $data['Fecha_inicio']
);

$stmt = sqlsrv_query($conn, $sql, $params);

if($stmt) {
    header("Location: homepageAdmin.php");
    exit;
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Error al programar: ' . print_r(sqlsrv_errors(), true)]);
}
?>