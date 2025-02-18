<?php
session_start();
// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirigir al login
    exit(); 
}
include 'Plantilla.php';
?>

<!DOCTYPE html>

<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../CSS/estilos.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;600&family=Roboto+Slab:wght@400&display=swap" rel="stylesheet"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php
    MostrarNavbar();
    ?>

            <main class="content">
                <h1>Bienvenido a la Página con Plantilla</h1>
                <p>Aquí va el contenido específico de cada página.</p>
            </main>
        </div>
    </div>
</body>

</body>
</html>
