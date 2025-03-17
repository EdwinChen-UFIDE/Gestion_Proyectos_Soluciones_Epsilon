<?php
Session_start();
// db_config.php: Configuración de la base de datos
require_once 'db_config.php';
Include 'Plantilla.php';

// Conexión a la base de datos
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}

// Obtener categorías de gastos para el selector
$sqlCategorias = "SELECT id, nombre FROM categorias_gastos";
$stmtCategorias = $pdo->prepare($sqlCategorias);
$stmtCategorias->execute();
$categorias = $stmtCategorias->fetchAll(PDO::FETCH_ASSOC);

// Procesar el formulario de transacciones
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tipo = $_POST['tipo'];
    $monto = $_POST['monto'];
    $descripcion = $_POST['descripcion'];
    $fecha = $_POST['fecha'];
    $categoria_id = ($tipo == 'gasto') ? $_POST['categoria_id'] : null;

    $sql = "INSERT INTO transacciones (tipo, monto, descripcion, fecha, categoria_id) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([$tipo, $monto, $descripcion, $fecha, $categoria_id])) {
        echo "<p>Transacción registrada correctamente.</p>";
    } else {
        echo "<p>Error al registrar la transacción.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sección de Contabilidad</title>
    <link rel="stylesheet" href="../CSS/estilos.css">
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
    </script>
</head>
<body>
<?php MostrarNavbar(); ?>

    <div class="main-container">
        <h2>Gestión de Contabilidad</h2>

        <!-- Botón para Agregar Nueva Categoría -->
        <div class="form-container">
            <h2>Opciones de Contabilidad</h2>
            <a href="agregar_categoria.php">
                <button>Agregar Nueva Categoría</button>
            </a>
            <a href="ver_transacciones.php">
                <button>Ver Transacciones</button>
            </a>
        </div>

        <!-- Formulario de Registro de Transacciones -->
        <div class="form-container">
            <h2>Registrar Nueva Transacción</h2>
            <form method="POST" action="">
                <label>Tipo de Transacción:</label>
                <select name="tipo" id="tipo" onchange="mostrarSelectorCategoria()" required>
                    <option value="ingreso">Ingreso</option>
                    <option value="gasto">Gasto</option>
                </select><br>

                <label>Monto:</label>
                <input type="number" name="monto" step="0.01" required><br>

                <label>Descripción:</label>
                <input type="text" name="descripcion" required><br>

                <label>Fecha:</label>
                <input type="date" name="fecha" required><br>

                <!-- Selector de Categoría (Visible solo si es Gasto) -->
                <div id="categoriaDiv" style="display: none;">
                    <label>Categoría de Gasto:</label>
                    <select name="categoria_id">
                        <option value="">Seleccione una categoría</option>
                        <?php foreach ($categorias as $categoria): ?>
                            <option value="<?php echo $categoria['id']; ?>">
                                <?php echo htmlspecialchars($categoria['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select><br>
                </div>

                <button type="submit">Registrar Transacción</button>
            </form>
        </div>
    </div>
</body>
</html>
