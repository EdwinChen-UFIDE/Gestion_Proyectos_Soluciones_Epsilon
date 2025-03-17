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

// Procesar la inserción de la categoría de gasto
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $categoria = $_POST['categoria'];

    $sql = "INSERT INTO categorias_gastos (nombre) VALUES (?)";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([$categoria])) {
        echo "<p>Categoría de gasto registrada correctamente.</p>";
    } else {
        echo "<p>Error al registrar la categoría de gasto.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Categoría de Gasto</title>
    <link rel="stylesheet" href="../CSS/estilos.css">
</head>
<body>
<?php MostrarNavbar(); ?>
    <div class="main-container">
        <div class="form-container">
            <h2>Agregar Nueva Categoría de Gasto</h2>
            <form method="POST" action="">
                <label>Nombre de la Categoría:</label>
                <input type="text" name="categoria" required><br>

                <button type="submit">Agregar Categoría</button>
            </form>
            <br>
            <a href="contabilidad.php">← Volver a Contabilidad</a>
        </div>
    </div>
</body>
</html>
