<?php
session_start();
// Incluye la configuración de la base de datos
require_once 'db_config.php';
include 'Plantilla.php';
require_once 'auth.php'; 
requireAdmin();
// Conexión a la base de datos
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}

// Obtener los roles de la base de datos
try {
    $stmt = $pdo->prepare("SELECT id, nombre FROM roles");
    $stmt->execute();
    $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener los roles: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Empleado</title>
    <link rel="stylesheet" href="../CSS/estilos.css"> 
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;600&family=Roboto+Slab:wght@400&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert2 -->
</head>
<body>

<?php MostrarNavbar(); ?>

<div class="form-container"> 
    <h2>Registrar Nuevo Empleado</h2>
    <form id="registroEmpleadoForm" method="POST" action="procesar_registro_empleado.php">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" required>

        <label for="apellidos">Apellidos:</label>
        <input type="text" id="apellidos" name="apellidos" required>

        <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
        <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required>

        <label for="cedula">Cédula:</label>
        <input type="text" id="cedula" name="cedula" required>

        <label for="telefono">Número Telefónico:</label>
        <input type="text" id="telefono" name="telefono" required>

        <label for="email">Correo Electrónico:</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required>

        <label for="rol">Rol:</label>
        <select id="rol" name="rol" required>
            <option value="">Seleccione un rol</option>
            <?php foreach ($roles as $rol): ?>
                <option value="<?= htmlspecialchars($rol['id']); ?>">
                    <?= htmlspecialchars($rol['nombre']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Registrar Empleado</button>
    </form>
</div>

<script>
document.getElementById('registroEmpleadoForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Evita el envío inmediato

    Swal.fire({
        title: "¿Confirmar Registro?",
        text: "¿Deseas registrar a este empleado?",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sí, registrar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            this.submit(); // Envía el formulario si el usuario confirma
        }
    });
});

// Mostrar alertas de sesión si existen
<?php if (isset($_SESSION['alert'])) : ?>
    Swal.fire({
        icon: "<?= $_SESSION['alert']['type']; ?>",
        title: "<?= $_SESSION['alert']['message']; ?>",
        confirmButtonColor: "#3085d6",
        confirmButtonText: "Aceptar"
    });
    <?php unset($_SESSION['alert']); ?>
<?php endif; ?>
</script>

</body>
</html>
