<?php
include('config.php');

if(!tieneRol(['Gerente RH'])) {
    header("HTTP/1.1 403 Forbidden");
    exit;
}

$params = array();
$filtroFecha = '';
$empleadoId = $_GET['ID_usuario'] ?? null;

if($empleadoId) {
    $params[] = $empleadoId;
    
    if(isset($_GET['fecha_inicio']) && isset($_GET['fecha_fin'])) {
        $filtroFecha = " AND p.Fecha_pago BETWEEN ? AND ?";
        array_push($params, $_GET['fecha_inicio'], $_GET['fecha_fin']);
    }

    $sql = "SELECT p.*, mp.Nombre_metodo, es.Nombre_estado 
            FROM Pagos p
            INNER JOIN Metodo_Pago mp ON p.ID_metodo_pago = mp.ID_metodo_pago
            INNER JOIN Estado es ON p.ID_estado = es.ID_estado
            WHERE p.ID_usuario = ? $filtroFecha
            ORDER BY p.Fecha_pago DESC";
    
    $stmt = sqlsrv_query($conn, $sql, $params);
    
    if($stmt === false) {
        header("HTTP/1.1 500 Internal Server Error");
        exit;
    }

    $pagos = array();
    while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $row['Fecha_pago'] = $row['Fecha_pago']->format('Y-m-d');
        $pagos[] = $row;
    }
    
    header('Content-Type: application/json');
    echo json_encode($pagos);
}
?>