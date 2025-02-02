<?php
// register.php

// Verificar si se enviaron datos por POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['correo'];
    $contrasenna = password_hash($_POST['contrasenna'], PASSWORD_DEFAULT); // Hash de la contraseña

    // Conectar a la base de datos SQL Server
    $serverName = "localhost"; // Cambia esto si tu servidor es remoto
    $connectionOptions = array(
        "Database" => "SolucionesEpsilon",
        "Uid" => "tu_usuario", // Cambia esto por tu usuario de SQL Server
        "PWD" => "tu_contraseña" // Cambia esto por tu contraseña de SQL Server
    );

    // Establecer la conexión
    $conn = sqlsrv_connect($serverName, $connectionOptions);

    if ($conn === false) {
        die(print_r(sqlsrv_errors(), true)); // Mostrar errores de conexión
    }

    // Preparar la llamada al procedimiento almacenado
    $sql = "{call sp_RegistrarUsuario(?, ?, ?, ?, ?)}";
    $params = array(
        array($nombre, SQLSRV_PARAM_IN),
        array($apellido, SQLSRV_PARAM_IN),
        array($correo, SQLSRV_PARAM_IN),
        array($contrasenna, SQLSRV_PARAM_IN),
        array(date("Y-m-d H:i:s"), SQLSRV_PARAM_IN) // Fecha actual
    );

    // Ejecutar el procedimiento almacenado
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        // Mostrar errores si la ejecución falla
        die(print_r(sqlsrv_errors(), true));
    } else {
        // Registro exitoso
        echo "Usuario registrado exitosamente.";
        // Redirigir al usuario a la página de login después de 3 segundos
        header("Refresh: 3; url=login.html");
    }

    // Cerrar la conexión
    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);
}
?>