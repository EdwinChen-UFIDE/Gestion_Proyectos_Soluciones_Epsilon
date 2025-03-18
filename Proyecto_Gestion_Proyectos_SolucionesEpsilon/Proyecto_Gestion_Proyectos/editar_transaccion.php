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

// Obtener ID de la transacción a editar
$id = $_GET['id'] ?? null;
if (!$id) {
    die("ID de transacción no válido.");
}

// Obtener los datos de la transacción
$sql = "SELECT * FROM transacciones WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$transaccion = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$transaccion) {
    die("Transacción no encontrada.");
}

// Obtener categorías de gastos
$sqlCategorias = "SELECT id, nombre FROM categorias_gastos";
$stmtCategorias = $pdo->prepare($sqlCategorias);
$stmtCategorias->execute();
$categorias = $stmtCategorias->fetchAll(PDO::FETCH_ASSOC);

// Variable para mostrar SweetAlert2 después de la actualización
$actualizacion_exitosa = false;

// Procesar la actualización de la transacción
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tipo = $_POST['tipo'];
    $monto = $_POST['monto'];
    $descripcion = $_POST['descripcion'];
    $fecha = $_POST['fecha'];
    $categoria_id = ($tipo == 'gasto') ? $_POST['categoria_id'] : null;

    $sqlUpdate = "UPDATE transacciones SET tipo = ?, monto = ?, descripcion = ?, fecha = ?, categoria_id = ? WHERE id = ?";
    $stmtUpdate = $pdo->prepare($sqlUpdate);
    if ($stmtUpdate->execute([$tipo, $monto, $descripcion, $fecha, $categoria_id, $id])) {
        $actualizacion_exitosa = true;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Transacción</title>
    <link rel="stylesheet" href="../CSS/contabilidad.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        function mostrarSelectorCategoria() {
            var tipo = document.getElementById("tipo").value;
            var categoriaDiv = document.getElementById("categoriaDiv");

            if (tipo === "gasto") {
                categoriaDiv.style.display = "block";
            } else {
                categoriaDiv.style.display = "none";
            }
        }

        // Ejecutar funciones cuando la página haya cargado completamente
        window.onload = function () {
            mostrarSelectorCategoria();

            <?php if ($actualizacion_exitosa) : ?>
                Swal.fire({
                    title: '¡Éxito!',
                    text: 'Transacción actualizada correctamente.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'ver_transacciones.php';
                });
            <?php endif; ?>
        };
    </script>
</head>
<body>
<?php MostrarNavbar(); ?>

    <div class="main-container">
        <h2>Editar Transacción</h2>
        <form method="POST" action="">
            <label>Tipo de Transacción:</label>
            <select name="tipo" id="tipo" onchange="mostrarSelectorCategoria()" required>
                <option value="ingreso" <?= $transaccion['tipo'] == 'ingreso' ? 'selected' : '' ?>>Ingreso</option>
                <option value="gasto" <?= $transaccion['tipo'] == 'gasto' ? 'selected' : '' ?>>Gasto</option>
            </select><br>

            <label>Monto:</label>
            <input type="number" name="monto" value="<?= $transaccion['monto'] ?>" required><br>

            <label>Descripción:</label>
            <input type="text" name="descripcion" value="<?= htmlspecialchars($transaccion['descripcion']) ?>" required><br>

            <label>Fecha:</label>
            <input type="date" name="fecha" value="<?= $transaccion['fecha'] ?>" required><br>

            <div id="categoriaDiv" style="display: none;">
                <label>Categoría de Gasto:</label>
                <select name="categoria_id">
                    <option value="">Seleccione una categoría</option>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?php echo $categoria['id']; ?>" 
                            <?php echo ($transaccion['categoria_id'] == $categoria['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($categoria['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select><br>
            </div>

            <button type="submit" class="btn-edit">Actualizar Transacción</button>
        </form>
    </div>
</body>
</html>
