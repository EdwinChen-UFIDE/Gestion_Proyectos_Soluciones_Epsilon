<?php
Session_start();
require_once 'db_config.php';
Include 'Plantilla.php';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

$registro_exitoso = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];

    $sql = "INSERT INTO clientes (nombre, correo, telefono) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([$nombre, $correo, $telefono])) {
        $registro_exitoso = true;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Cliente</title>
    <link rel="stylesheet" href="../CSS/contabilidad.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<?php MostrarNavbar(); ?>
<div class="main-container">
    <div class="form-container">
        <h2>Registrar Nuevo Cliente</h2>
        <form method="POST">
            <label>Nombre:</label>
            <input type="text" name="nombre" required><br>
            <label>Correo:</label>
            <input type="email" name="correo" required><br>
            <label>Teléfono:</label>
            <input type="text" name="telefono"><br>
            <button type="submit" class="btn-submit">Registrar Cliente</button>
        </form>
        <br>
        <a href="RPA.php"><button class="btn-back">← Volver a RPA</button></a>
    </div>
</div>

<?php if ($registro_exitoso): ?>
<script>
    Swal.fire({
        title: '¡Cliente registrado!',
        text: 'El nuevo cliente ha sido creado correctamente.',
        icon: 'success',
        confirmButtonText: 'OK'
    }).then(() => {
        window.location.href = 'RPA.php';
    });
</script>
<?php endif; ?>
</body>
</html>
