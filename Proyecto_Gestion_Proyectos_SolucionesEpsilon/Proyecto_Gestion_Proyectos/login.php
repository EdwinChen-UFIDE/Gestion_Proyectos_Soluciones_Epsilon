<?php
// login.php

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST['correo'];
    $contrasenna = $_POST['contrasenna'];

    // Conectar a la base de datos
    $serverName = "localhost"; // o la dirección de tu servidor SQL Server
    $connectionOptions = array(
        "Database" => "SolucionesEpsilon",
        "Uid" => "tu_usuario", // Cambia esto por tu usuario de SQL Server
        "PWD" => "tu_contraseña" // Cambia esto por tu contraseña de SQL Server
    );

    // Establecer la conexión
    $conn = sqlsrv_connect($serverName, $connectionOptions);

    if ($conn === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Preparar la llamada al procedimiento almacenado
    $sql = "{call sp_Login(?, ?)}";
    $params = array(
        array($correo, SQLSRV_PARAM_IN),
        array($contrasenna, SQLSRV_PARAM_IN)
    );

    // Ejecutar el procedimiento almacenado
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Obtener el resultado
    if (sqlsrv_fetch($stmt)) {
        $id_usuario = sqlsrv_get_field($stmt, 0);
        $nombre = sqlsrv_get_field($stmt, 1);
        $correo = sqlsrv_get_field($stmt, 2);
        $contrasenna_hash = sqlsrv_get_field($stmt, 3);

        // Verificar la contraseña
        if (password_verify($contrasenna, $contrasenna_hash)) {
            // Iniciar sesión
            $_SESSION['id_usuario'] = $id_usuario;
            $_SESSION['nombre'] = $nombre;
            $_SESSION['correo'] = $correo;

            // Redirigir al usuario a la página de inicio
            header("Location: Homepage.html");
            exit();
        } else {
            echo "Contraseña incorrecta.";
        }
    } else {
        echo "Usuario no encontrado.";
    }

    // Cerrar la conexión
    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);
}
?>