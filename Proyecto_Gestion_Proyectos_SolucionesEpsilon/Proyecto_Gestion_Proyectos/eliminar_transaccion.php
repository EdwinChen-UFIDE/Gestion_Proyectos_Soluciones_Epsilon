<?php
Session_start();
require_once 'db_config.php';

// Conexión a la base de datos
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}

// Obtener el ID de la transacción a eliminar
$id = $_GET['id'] ?? null;
if (!$id) {
    die("ID de transacción no válido.");
}

// Eliminar la transacción de la base de datos
$sql = "DELETE FROM transacciones WHERE id = ?";
$stmt = $pdo->prepare($sql);
$success = $stmt->execute([$id]);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Transacción</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<script>
    Swal.fire({
        title: "<?php echo $success ? 'Transacción eliminada' : 'Error al eliminar'; ?>",
        text: "<?php echo $success ? 'La transacción ha sido eliminada correctamente.' : 'Hubo un problema al eliminar la transacción.'; ?>",
        icon: "<?php echo $success ? 'success' : 'error'; ?>",
        confirmButtonText: "OK"
    }).then(() => {
        window.location.href = "ver_transacciones.php";
    });
</script>
</body>
</html>
