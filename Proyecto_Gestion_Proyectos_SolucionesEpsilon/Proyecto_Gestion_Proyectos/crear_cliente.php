<?php
Session_start();
require_once 'db_config.php';
include 'Plantilla.php';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

$registro_exitoso = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];

    $sql = "INSERT INTO clientes (nombre, correo, telefono) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([$nombre, $correo, $telefono])) {
        $registro_exitoso = true;
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Registrar Cliente</title>
    <link rel="stylesheet" href="../CSS/contabilidad.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .form-container {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin: 40px auto;
            width: 40%;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 140px 1fr;
            gap: 15px 20px;
            align-items: center;
        }

        .form-grid label {
            font-weight: bold;
        }

        .form-grid input {
            width: 100%;
            padding: 8px 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .form-actions {
            grid-column: 2 / 3;
            text-align: right;
            margin-top: 20px;
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

        .btn-back {
            background-color: #6c757d;
            color: white;
            padding: 8px 18px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }

        .btn-back:hover {
            background-color: #5a6268;
        }

        .back-container {
            text-align: right;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <?php MostrarNavbar(); ?>

    <div class="form-container">
        <h2 style="text-align:center;">Registrar Nuevo Cliente</h2>
        <form method="POST" class="form-grid">
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" id="nombre" required>

            <label for="correo">Correo:</label>
            <input type="email" name="correo" id="correo" required>

            <label for="telefono">Teléfono:</label>
            <input type="text" name="telefono" id="telefono">

            <div class="form-actions">
                <button type="submit" class="btn-submit">Registrar Cliente</button>
            </div>
        </form>
    </div>

    <?php if ($registro_exitoso): ?>
        <script>
            Swal.fire({
                title: '¡Cliente registrado!',
                text: 'El nuevo cliente ha sido creado correctamente.',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'RPA.php';
            });
        </script>
    <?php endif; ?>
</body>

</html>