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

// Variable para mostrar SweetAlert2 después del registro
$registro_exitoso = false;

// Procesar el formulario de transacciones
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tipo = $_POST['tipo'];
    $monto = $_POST['monto'];
    $descripcion = $_POST['descripcion'];
    $fecha = $_POST['fecha'];
    $categoria_id = ($tipo == 'gasto' && isset($_POST['categoria_id']) && $_POST['categoria_id'] !== '') ? $_POST['categoria_id'] : NULL;

    try {
        $sql = "INSERT INTO transacciones (tipo, monto, descripcion, fecha, categoria_id) VALUES (:tipo, :monto, :descripcion, :fecha, :categoria_id)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':monto', $monto);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->bindParam(':categoria_id', $categoria_id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            $registro_exitoso = true;
        }
    } catch (PDOException $e) {
        echo "<script>
                Swal.fire({
                    title: 'Error en la base de datos',
                    text: '" . $e->getMessage() . "',
                    icon: 'error',
                    confirmButtonText: 'Cerrar'
                });
              </script>";
    }
}

// Obtener categorías de gastos para el selector
$sqlCategorias = "SELECT id, nombre FROM categorias_gastos";
$stmtCategorias = $pdo->prepare($sqlCategorias);
$stmtCategorias->execute();
$categorias = $stmtCategorias->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sección de Contabilidad</title>
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

        function cargarBotones() {
            var placeholder = document.getElementById("botonesPlaceholder");

            placeholder.innerHTML = `
                <div class="form-container">
                    <h2>Opciones de Contabilidad</h2>
                    <a href="agregar_categoria.php"><button class="btn-add">Agregar Nueva Categoría</button></a>
                    <a href="ver_transacciones.php"><button class="btn-view">Ver Transacciones</button></a>
                </div>
            `;
        }

        // Ejecutar funciones cuando la página haya cargado completamente
        window.onload = function () {
            cargarBotones();
            mostrarSelectorCategoria();

            <?php if ($registro_exitoso) : ?>
                Swal.fire({
                    title: '¡Éxito!',
                    text: 'Transacción registrada correctamente.',
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
        <h2>Gestión de Contabilidad</h2>

        <!-- Placeholder donde se cargarán los botones dinámicamente -->
        <div id="botonesPlaceholder"></div>

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

                <button type="submit" class="btn-submit">Registrar Transacción</button>
            </form>
        </div>
    </div>
</body>
</html>
