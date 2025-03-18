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
    <link rel="stylesheet" href="../CSS/contabilidad.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmarEliminacion(id) {
            Swal.fire({
                title: "¿Estás seguro?",
                text: "No podrás revertir esta acción.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "eliminar_transaccion.php?id=" + id;
                }
            });
        }
    </script>
</head>
<body>
<?php MostrarNavbar(); ?>
    <div class="main-container">
        <h2>Listado de Transacciones</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tipo</th>
                    <th>Monto</th>
                    <th>Descripción</th>
                    <th>Fecha</th>
                    <th>Categoría</th>
                    <th>Acciones</th>
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
                        <td class="actions">
                            <a href="editar_transaccion.php?id=<?php echo $transaccion['id']; ?>">
                                <button class="btn-edit">Editar</button>
                            </a>
                            <button class="btn-delete" onclick="confirmarEliminacion(<?php echo $transaccion['id']; ?>)">Eliminar</button>
                            <a href="generar_recibo.php?id=<?php echo $transaccion['id']; ?>" target="_blank">
                                <button class="btn-receipt">Generar Recibo</button>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <br>
        <a href="contabilidad.php">
            <button class="btn-back">← Volver a Contabilidad</button>
        </a>
    </div>
</body>
</html>
