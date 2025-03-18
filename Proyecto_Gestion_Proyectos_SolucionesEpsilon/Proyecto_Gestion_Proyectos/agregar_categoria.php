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

// Procesar la inserción de la categoría de gasto
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $categoria = $_POST['categoria'];

    try {
        $sql = "INSERT INTO categorias_gastos (nombre) VALUES (?)";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$categoria])) {
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
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Categoría</title>
    <link rel="stylesheet" href="../CSS/contabilidad.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Ejecutar SweetAlert2 cuando la página haya cargado si la categoría se registró con éxito
        window.onload = function () {
            <?php if ($registro_exitoso) : ?>
                Swal.fire({
                    title: '¡Éxito!',
                    text: 'Categoría agregada correctamente.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'contabilidad.php';
                });
            <?php endif; ?>
        };
    </script>
</head>
<body>
<?php MostrarNavbar(); ?>

    <div class="main-container">
        <h2>Agregar Nueva Categoría</h2>
        <form method="POST" action="">
            <label>Nombre de la Categoría:</label>
            <input type="text" name="categoria" required><br>

            <button type="submit" class="btn-submit">Agregar Categoría</button>
        </form>
        <br>
        <a href="contabilidad.php"><button class="btn-back">← Volver</button></a>
    </div>
</body>
</html>
