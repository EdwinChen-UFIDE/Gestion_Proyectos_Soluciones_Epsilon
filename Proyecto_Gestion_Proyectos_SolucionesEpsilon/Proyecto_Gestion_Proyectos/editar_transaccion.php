<?php
Session_start();
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

            <?php if ($actualizacion_exitosa): ?>
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

        .btn-edit {
            background-color: #ffc107;
            color: black;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
        }

        .btn-edit:hover {
            background-color: #e0a800;
        }
    </style>

    <div class="form-container">
        <h2 style="text-align: center;">Editar Transacción</h2>
        <form method="POST" class="form-grid">
            <label for="tipo">Tipo de Transacción:</label>
            <select name="tipo" id="tipo" onchange="mostrarSelectorCategoria()" required>
                <option value="ingreso" <?= $transaccion['tipo'] == 'ingreso' ? 'selected' : '' ?>>Ingreso</option>
                <option value="gasto" <?= $transaccion['tipo'] == 'gasto' ? 'selected' : '' ?>>Gasto</option>
            </select>

            <label for="monto">Monto:</label>
            <input type="number" name="monto" id="monto" value="<?= $transaccion['monto'] ?>" required>

            <label for="descripcion">Descripción:</label>
            <input type="text" name="descripcion" id="descripcion"
                value="<?= htmlspecialchars($transaccion['descripcion']) ?>" required>

            <label for="fecha">Fecha:</label>
            <input type="date" name="fecha" id="fecha" value="<?= $transaccion['fecha'] ?>" required>

            <div id="categoriaDiv" style="display: none; grid-column: 1 / span 2;">
                <label style="grid-column: 1 / 2;">Categoría de Gasto:</label>
                <select name="categoria_id" style="grid-column: 2 / 3;">
                    <option value="">Seleccione una categoría</option>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?= $categoria['id'] ?>" <?= ($transaccion['categoria_id'] == $categoria['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($categoria['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-edit">Actualizar Transacción</button>
            </div>
        </form>
    </div>

    <script>
        function mostrarSelectorCategoria() {
            const tipo = document.getElementById("tipo").value;
            const categoriaDiv = document.getElementById("categoriaDiv");
            categoriaDiv.style.display = (tipo === "gasto") ? "grid" : "none";
        }

        window.onload = function () {
            mostrarSelectorCategoria();

            <?php if ($actualizacion_exitosa): ?>
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

</body>

</html>