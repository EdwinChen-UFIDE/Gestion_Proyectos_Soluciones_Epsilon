<?php
// Inicia sesión y verifica si el usuario está autenticado
session_start();

// Verifica si el usuario tiene el rol de admin
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    echo '<p>No tienes permisos para acceder a esta página.</p>';
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../CSS/adminestilos.css"> <!-- Enlace al CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;600&family=Roboto+Slab:wght@400&display=swap" rel="stylesheet"> 
</head>
<body>

    <div class="admin-container">
        <aside class="sidebar">
            <h2>Panel de Administración</h2>
            <a href="registrarEmpleados.php">Registrar Empleado</a>
            <a href="listar_empleados.php">Ver Empleados Actuales</a>
            <a href="Roles.php">Registrar Rol</a>
            <a href="Roles.php">Listar Roles</a>
        </aside>

        <main class="content">
            <h1>Bienvenido al Panel de Administración</h1>
            <p>Selecciona una opción en el menú.</p>
        </main>
    </div>

</body>
</html>
