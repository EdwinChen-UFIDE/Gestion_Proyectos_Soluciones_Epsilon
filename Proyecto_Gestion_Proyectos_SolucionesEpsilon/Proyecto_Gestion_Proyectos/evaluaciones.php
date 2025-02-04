<?php
include('config.php');

if ($_SESSION['role'] != 'admin' && $_SESSION['ID_usuario'] != $empleadoId) {
    header("HTTP/1.1 403 Forbidden");
    exit;
}

$params = array($_SESSION['ID_usuario']);
$filtroFecha = '';

if(isset($_GET['fecha_inicio']) && isset($_GET['fecha_fin'])) {
    $filtroFecha = " AND Fecha_evaluacion BETWEEN ? AND ?";
    array_push($params, $_GET['fecha_inicio'], $_GET['fecha_fin']);
}

$sql = "SELECT e.*, es.Nombre_estado 
        FROM Evaluaciones e
        INNER JOIN Estado es ON e.ID_estado = es.ID_estado
        WHERE e.ID_usuario = ? $filtroFecha
        ORDER BY e.Fecha_evaluacion DESC";

$stmt = sqlsrv_query($conn, $sql, $params);

if($stmt === false) {
    header("HTTP/1.1 500 Internal Server Error");
    exit;
}

$evaluaciones = array();
while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $row['Fecha_evaluacion'] = $row['Fecha_evaluacion']->format('Y-m-d');
    $evaluaciones[] = $row;
}

header('Content-Type: application/json');
echo json_encode($evaluaciones);
?>