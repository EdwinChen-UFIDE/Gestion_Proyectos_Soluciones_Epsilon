<?php
// Inicia sesión y verifica si el usuario está autenticado
session_start();

// Verifica si el usuario tiene el rol de admin
if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 8) { // Rol de admin con ID 1
    echo '<a href="registrarEmpleados.php" style="padding: 10px 15px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px;">Registrar Empleado</a>';
    echo '<a href="listar_empleados.php" style="padding: 10px 15px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px;">Ver Empleados Actuales</a>';
    echo '<a href="Roles.php" style="padding: 10px 15px; background-color:rgb(76, 175, 158); color: white; text-decoration: none; border-radius: 5px; margin-right: 10px;">Registrar Rol</a>';
    echo '<a href="Roles.php" style="padding: 10px 15px; background-color:rgb(76, 175, 158); color: white; text-decoration: none; border-radius: 5px;">Listar Roles</a>';
} else {
    echo '<p>No tienes permisos para registrar empleados.</p>';
}
?>