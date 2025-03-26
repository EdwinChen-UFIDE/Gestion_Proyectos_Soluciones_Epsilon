<?php
session_start();
require_once 'db_config.php';
include 'Plantilla.php';

if (!isset($_GET['id'])) {
    die("ID de proyecto no proporcionado.");
}

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT * FROM proyectos WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $proyecto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$proyecto) {
        die("Proyecto no encontrado.");
    }
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalles del Proyecto</title>
    <link rel="stylesheet" href="../CSS/estilos.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert -->
</head>
<body>

<?php MostrarNavbar(); ?>

<div class="form-container">
    <h2>Detalles del Proyecto</h2>
    <p><strong>ID:</strong> <?= htmlspecialchars($proyecto['id']); ?></p>
    <p><strong>Nombre:</strong> <?= htmlspecialchars($proyecto['nombre']); ?></p>
    <p><strong>Cliente:</strong> <?= htmlspecialchars($proyecto['cliente']); ?></p>
    <p><strong>Fecha de Creación:</strong> <?= htmlspecialchars($proyecto['fecha_creacion']); ?></p>
    <p><strong>Estado:</strong> <?= htmlspecialchars($proyecto['estado']); ?></p> <!-- Nuevo campo agregado -->

    <a href="editar_proyecto.php?id=<?= $proyecto['id']; ?>" class="btn">Editar</a>
    <a href="eliminar_proyecto.php?id=<?= $proyecto['id']; ?>" class="btn btn-delete">Eliminar</a>
    <a href="listar_proyectos.php" class="btn">Volver</a>
</div>

<script>
    // Confirmación para eliminar con SweetAlert
    document.querySelector('.btn-delete').addEventListener('click', function(event) {
        event.preventDefault();
        let url = this.getAttribute('href');

        Swal.fire({
            title: "¿Estás seguro?",
            text: "No podrás revertir esta acción",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Sí, eliminar",
            cancelButtonText: "Cancelar"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    });

    // Mensaje de éxito después de una acción
    function mostrarMensaje(tipo, mensaje) {
        Swal.fire({
            icon: tipo,
            title: mensaje,
            showConfirmButton: false,
            timer: 2000
        });
    }

    // Mensaje de éxito si la URL tiene ?success=true
    <?php if (isset($_GET['success'])): ?>
        mostrarMensaje("success", "Acción realizada con éxito");
    <?php endif; ?>
</script>

</body>
</html>
