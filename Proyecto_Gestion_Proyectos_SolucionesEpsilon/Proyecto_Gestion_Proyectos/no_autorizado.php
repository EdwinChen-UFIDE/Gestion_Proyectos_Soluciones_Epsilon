<?php
session_start();
include 'Plantilla.php';
require_once 'db_config.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acceso No Autorizado</title>
    <link rel="stylesheet" href="../CSS/estilos.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<?php
    MostrarNavbar();
    ?>
<?php if (isset($_SESSION['alert'])): ?>
    <script>
        Swal.fire({
            icon: "<?= $_SESSION['alert']['type']; ?>",
            title: "<?= $_SESSION['alert']['message']; ?>",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "Aceptar"
        }).then(() => {
            <?php if (!empty($_SESSION['alert']['redirect'])): ?>
                window.location.href = "<?= $_SESSION['alert']['redirect']; ?>";
            <?php endif; ?>
        });
    </script>
    <?php unset($_SESSION['alert']); ?>
<?php else: ?>
    <div class="main-container">
        <h2>No tienes permiso para acceder a esta página.</h2>
        <p><a href="login.php">Volver al inicio de sesión</a></p>
    </div>
<?php endif; ?>

</body>
</html>
