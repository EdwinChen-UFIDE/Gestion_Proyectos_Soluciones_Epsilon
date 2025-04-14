<?php
Session_start();
require_once 'db_config.php';
require_once 'dompdf/autoload.inc.php';
include 'Plantilla.php';

use Dompdf\Dompdf;

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

if (isset($_GET['mes_anio'])) {
    [$anio, $mes] = explode('-', $_GET['mes_anio']);
} else {
    $mes = date('m');
    $anio = date('Y');
}

$estado = $_GET['estado'] ?? 'todas';
$resultados = [];

if (isset($_GET['mes_anio'])) {
    $query = "SELECT f.*, c.nombre FROM facturas f 
              JOIN clientes c ON f.cliente_id = c.id 
              WHERE MONTH(f.fecha_emision) = :mes AND YEAR(f.fecha_emision) = :anio";

    if ($estado == 'pagadas') {
        $query .= " AND f.pagada = 1";
    } elseif ($estado == 'impagas') {
        $query .= " AND f.pagada = 0";
    }

    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':mes' => $mes,
        ':anio' => $anio
    ]);
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Exportar a PDF
if (isset($_GET['exportar']) && $_GET['exportar'] == 'pdf') {
    $html = "<h2>Reporte Mensual - $mes/$anio</h2><table border='1' cellspacing='0' cellpadding='5'><tr>
                <th>Cliente</th><th>Fecha</th><th>Monto</th><th>Estado</th><th>Descripción</th></tr>";

    foreach ($resultados as $r) {
        $estado_txt = $r['pagada'] ? 'Pagada' : 'Impaga';
        $html .= "<tr>
                    <td>{$r['nombre']}</td>
                    <td>{$r['fecha_emision']}</td>
                    <td>₡" . number_format($r['monto'], 2) . "</td>
                    <td>$estado_txt</td>
                    <td>{$r['descripcion']}</td>
                  </tr>";
    }

    $html .= "</table>";

    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    $dompdf->stream("Reporte_Mensual_{$mes}_{$anio}.pdf");
    exit;
}

// Exportar a CSV
if (isset($_GET['exportar']) && $_GET['exportar'] == 'csv') {
    header('Content-Type: text/csv');
    header("Content-Disposition: attachment; filename=Reporte_Mensual_{$mes}_{$anio}.csv");

    $output = fopen('php://output', 'w');
    fputcsv($output, ['Cliente', 'Fecha', 'Monto', 'Estado', 'Descripción']);

    foreach ($resultados as $r) {
        $estado_txt = $r['pagada'] ? 'Pagada' : 'Impaga';
        fputcsv($output, [$r['nombre'], $r['fecha_emision'], $r['monto'], $estado_txt, $r['descripcion']]);
    }

    fclose($output);
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Mensual</title>
    <link rel="stylesheet" href="../CSS/contabilidad.css">
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
        grid-template-columns: 150px 1fr;
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
    }

    .btn-submit {
        background-color: #007bff;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        cursor: pointer;
    }

    .btn-submit:hover {
        background-color: #0056b3;
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

    .export-buttons {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 10px;
    }

    .export-buttons a button {
        background-color: #28a745;
    }

    .export-buttons a button:hover {
        background-color: #218838;
    }
</style>

<div class="form-container">
    <h2 style="text-align: center;">Reporte Mensual</h2>
    <form method="GET" action="" class="form-grid">
        <label for="mes_anio">Mes y Año:</label>
        <input type="month" name="mes_anio" id="mes_anio" value="<?= "$anio-" . str_pad($mes, 2, '0', STR_PAD_LEFT) ?>" required>

        <label for="estado">Estado:</label>
        <select name="estado" id="estado">
            <option value="todas" <?= ($estado == 'todas') ? 'selected' : '' ?>>Todas</option>
            <option value="pagadas" <?= ($estado == 'pagadas') ? 'selected' : '' ?>>Solo pagadas</option>
            <option value="impagas" <?= ($estado == 'impagas') ? 'selected' : '' ?>>Solo impagas</option>
        </select>

        <div class="form-actions">
            <button type="submit" class="btn-submit">Ver Reporte</button>
        </div>
    </form>
</div>

<?php if ($resultados): ?>
<div class="form-container">
    <h3 style="text-align: center;">Resultados de Facturación - <?= $mes ?>/<?= $anio ?></h3>

    <div class="export-buttons">
        <a href="?mes_anio=<?= "$anio-" . str_pad($mes, 2, '0', STR_PAD_LEFT) ?>&estado=<?= $estado ?>&exportar=pdf">
            <button class="btn-submit">Exportar a PDF</button>
        </a>
        <a href="?mes_anio=<?= "$anio-" . str_pad($mes, 2, '0', STR_PAD_LEFT) ?>&estado=<?= $estado ?>&exportar=csv">
            <button class="btn-submit">Exportar a CSV</button>
        </a>
    </div>

    <table>
        <tr>
            <th>Cliente</th>
            <th>Fecha</th>
            <th>Monto</th>
            <th>Estado</th>
            <th>Descripción</th>
        </tr>
        <?php foreach ($resultados as $r): ?>
            <tr>
                <td><?= htmlspecialchars($r['nombre']) ?></td>
                <td><?= $r['fecha_emision'] ?></td>
                <td>₡<?= number_format($r['monto'], 2) ?></td>
                <td><?= $r['pagada'] ? 'Pagada' : 'Impaga' ?></td>
                <td><?= htmlspecialchars($r['descripcion']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php endif; ?>
</body>
</html>