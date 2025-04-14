<?php
session_start();
require_once 'db_config.php';
include 'Plantilla.php';

// Conexión
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Obtener historial
$sql = "SELECT f.*, c.nombre FROM facturas f JOIN clientes c ON f.cliente_id = c.id ORDER BY f.fecha_emision DESC";
$facturas = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Historial de Facturas</title>
    <link rel="stylesheet" href="../CSS/contabilidad.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .main-container {
            padding: 20px;
            width: 90%;
            margin: 0 auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: center;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        select {
            padding: 6px;
            border-radius: 6px;
        }
    </style>
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
            width: 90%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }

        table th,
        table td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ccc;
        }

        table th {
            background-color: #007bff;
            color: white;
        }

        select {
            padding: 6px 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
    </style>

    <div class="form-container">
        <h2 style="text-align: center;">Historial de Facturas</h2>
        <table>
            <tr>
                <th>Cliente</th>
                <th>Fecha</th>
                <th>Monto</th>
                <th>Descripción</th>
                <th>Estado</th>
            </tr>
            <?php foreach ($facturas as $f): ?>
                <tr>
                    <td><?= htmlspecialchars($f['nombre']) ?></td>
                    <td><?= $f['fecha_emision'] ?></td>
                    <td>₡<?= number_format($f['monto'], 2) ?></td>
                    <td><?= htmlspecialchars($f['descripcion']) ?></td>
                    <td>
                        <select onchange="cambiarEstadoFactura(<?= $f['id'] ?>, this.value)">
                            <option value="1" <?= $f['pagada'] ? 'selected' : '' ?>>Pagada</option>
                            <option value="0" <?= !$f['pagada'] ? 'selected' : '' ?>>Impaga</option>
                        </select>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <script>
        function cambiarEstadoFactura(id, nuevoEstado) {
            fetch('actualizar_estado_factura.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${id}&estado=${nuevoEstado}`
            })
                .then(res => res.json())
                .then(data => {
                    Swal.fire({
                        title: data.success ? 'Actualizado' : 'Error',
                        text: data.success ? 'Estado actualizado correctamente.' : (data.message || 'No se pudo actualizar.'),
                        icon: data.success ? 'success' : 'error'
                    });
                });
        }
    </script>
</body>

</html>