<?php
Session_start();
require_once 'db_config.php';
Include 'Plantilla.php';

// Conexión a la base de datos
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}

// Obtener todas las transacciones
$sql = "SELECT t.id, t.tipo, t.monto, t.descripcion, t.fecha, c.nombre AS categoria 
        FROM transacciones t 
        LEFT JOIN categorias_gastos c ON t.categoria_id = c.id
        ORDER BY t.fecha DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$transacciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Transacciones</title>
    <link rel="stylesheet" href="../CSS/estilos.css">
</head>
<body>
<?php MostrarNavbar(); ?>
    <div class="main-container">
        <h2>Listado de Transacciones</h2>
        <table border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tipo</th>
                    <th>Monto</th>
                    <th>Descripción</th>
                    <th>Fecha</th>
                    <th>Categoría</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transacciones as $transaccion) : ?>
                    <tr>
                        <td><?php echo $transaccion['id']; ?></td>
                        <td><?php echo ucfirst($transaccion['tipo']); ?></td>
                        <td><?php echo number_format($transaccion['monto'], 2); ?> USD</td>
                        <td><?php echo htmlspecialchars($transaccion['descripcion']); ?></td>
                        <td><?php echo $transaccion['fecha']; ?></td>
                        <td><?php echo $transaccion['categoria'] ?? 'N/A'; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <br>
        <a href="contabilidad.php">← Volver a Contabilidad</a>
    </div>
</body>
</html>
