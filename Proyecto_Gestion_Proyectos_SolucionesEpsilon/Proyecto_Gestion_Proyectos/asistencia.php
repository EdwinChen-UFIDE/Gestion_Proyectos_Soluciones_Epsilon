<?php
include('config.php');

// Verificar rol supervisor o admin
if(!tieneRol(['Administrador', 'Supervisor'])) {
    header("HTTP/1.1 403 Forbidden");
    exit;
}

$params = array();
$filtroFecha = '';
$empleadoId = $_GET['ID_usuario'] ?? null;

if($empleadoId) {
    $params[] = $empleadoId;
    
    if(isset($_GET['fecha_inicio']) && isset($_GET['fecha_fin'])) {
        $filtroFecha = " AND Fecha BETWEEN ? AND ?";
        array_push($params, $_GET['fecha_inicio'], $_GET['fecha_fin']);
    }

    $sql = "SELECT a.*, es.Nombre_estado 
            FROM Asistencia a
            INNER JOIN Estado es ON a.ID_estado = es.ID_estado
            WHERE a.ID_usuario = ? $filtroFecha
            ORDER BY a.Fecha DESC";
    
    $stmt = sqlsrv_query($conn, $sql, $params);
    
    if($stmt === false) {
        header("HTTP/1.1 500 Internal Server Error");
        exit;
    }

    $asistencias = array();
    while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $row['Fecha'] = $row['Fecha']->format('Y-m-d');
        $asistencias[] = $row;
    }
    
    header('Content-Type: application/json');
    echo json_encode($asistencias);
}
?>