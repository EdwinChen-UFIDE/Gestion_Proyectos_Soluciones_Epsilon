<?php
include('config.php');

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validación de campos
    $camposRequeridos = ['Nombre', 'Apellido', 'Identificacion', 'Fecha_creacion', 'Telefono', 'Correo'];
    foreach($camposRequeridos as $campo) {
        if(empty($data[$campo])) {
            http_response_code(400);
            echo json_encode(['error' => "El campo $campo es requerido"]);
            exit;
        }
    }

    // Validación de formato
    if(!filter_var($data['Correo'], FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['error' => 'Formato de correo inválido']);
        exit;
    }

    // Actualizar en base de datos
    $sql = "UPDATE Usuarios SET 
            Nombre = ?, 
            Apellido = ?, 
            Identificacion = ?, 
            Telefono = ?, 
            Correo = ?
            WHERE ID_usuario = ?";
    
    $params = array(
        $data['Nombre'],
        $data['Apellido'],
        $data['Identificacion'],
        $data['Telefono'],
        $data['Correo'],
        $_SESSION['ID_usuario']
    );

    $stmt = sqlsrv_query($conn, $sql, $params);
    
    if($stmt) {
        echo json_encode(['success' => 'Datos actualizados correctamente']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Error al actualizar: ' . print_r(sqlsrv_errors(), true)]);
    }
}
?>