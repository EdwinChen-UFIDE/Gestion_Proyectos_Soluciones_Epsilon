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

<div class="main-container">
    <div class="form-container">
        <h2>Generar Reporte Mensual</h2>
        <form method="GET" action="">
            <label>Mes y Año:</label>
            <input type="month" name="mes_anio" value="<?= "$anio-" . str_pad($mes, 2, '0', STR_PAD_LEFT) ?>" required>

            <label>Estado:</label>
            <select name="estado">
                <option value="todas" <?= ($estado == 'todas') ? 'selected' : '' ?>>Todas</option>
                <option value="pagadas" <?= ($estado == 'pagadas') ? 'selected' : '' ?>>Solo pagadas</option>
                <option value="impagas" <?= ($estado == 'impagas') ? 'selected' : '' ?>>Solo impagas</option>
            </select>

            <button type="submit" class="btn-submit">Ver Reporte</button>
        </form>
    </div>

    <?php if ($resultados): ?>
        <div class="form-container">
            <h3>Resultados de Facturación - <?= $mes ?>/<?= $anio ?></h3>
            <a href="?mes_anio=<?= "$anio-" . str_pad($mes, 2, '0', STR_PAD_LEFT) ?>&estado=<?= $estado ?>&exportar=pdf">
                <button class="btn-submit">Exportar a PDF</button>
            </a>
            <a href="?mes_anio=<?= "$anio-" . str_pad($mes, 2, '0', STR_PAD_LEFT) ?>&estado=<?= $estado ?>&exportar=csv">
                <button class="btn-submit">Exportar a CSV</button>
            </a>
            <br><br>
            <table border="1" cellpadding="8" cellspacing="0" style="width:100%;">
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
</div>
</body>
</html>