<?php
Session_start();
require_once 'db_config.php';
include 'Plantilla.php';

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

            <?php if ($registro_exitoso): ?>
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

    <style>
        .form-container {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin: 40px auto;
            width: 85%;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 160px 1fr;
            gap: 15px 20px;
            align-items: center;
        }

        .form-grid label {
            font-weight: bold;
        }

        .form-grid input,
        .form-grid select {
            width: 100%;
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .form-actions {
            grid-column: 2 / 3;
            text-align: right;
            margin-top: 10px;
        }

        .btn-submit {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
        }

        .btn-submit:hover {
            background-color: #218838;
        }

        .btn-group {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .btn-group a button {
            padding: 10px 18px;
            border-radius: 8px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }

        .btn-group a button:hover {
            background-color: #0056b3;
        }
    </style>

    <div class="form-container">
        <h2 style="text-align:center;">Gestión de Contabilidad</h2>

        <div class="btn-group">
            <a href="agregar_categoria.php"><button>Agregar Nueva Categoría</button></a>
            <a href="ver_transacciones.php"><button>Ver Transacciones</button></a>
        </div>

        <form method="POST" class="form-grid">
            <label for="tipo">Tipo de Transacción:</label>
            <select name="tipo" id="tipo" onchange="mostrarSelectorCategoria()" required>
                <option value="ingreso">Ingreso</option>
                <option value="gasto">Gasto</option>
            </select>

            <label for="monto">Monto:</label>
            <input type="number" name="monto" step="0.01" id="monto" required>

            <label for="descripcion">Descripción:</label>
            <input type="text" name="descripcion" id="descripcion" required>

            <label for="fecha">Fecha:</label>
            <input type="date" name="fecha" id="fecha" required>

            <div id="categoriaDiv" style="display:none; grid-column: 1 / span 2;">
                <label style="grid-column: 1 / 2;">Categoría de Gasto:</label>
                <select name="categoria_id" style="grid-column: 2 / 3;">
                    <option value="">Seleccione una categoría</option>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?= $categoria['id'] ?>"><?= htmlspecialchars($categoria['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit">Registrar Transacción</button>
            </div>
        </form>
    </div>

    <script>
        function mostrarSelectorCategoria() {
            var tipo = document.getElementById("tipo").value;
            var categoriaDiv = document.getElementById("categoriaDiv");
            categoriaDiv.style.display = (tipo === "gasto") ? "grid" : "none";
        }

        window.onload = function () {
            mostrarSelectorCategoria();

            <?php if ($registro_exitoso): ?>
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

</body>

</html>