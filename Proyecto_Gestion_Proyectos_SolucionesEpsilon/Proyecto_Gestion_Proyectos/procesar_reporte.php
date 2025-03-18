<?php
require 'pdf_reporte_generator.php';
require 'db_config.php';
require_once 'auth.php'; 
requireAdmin();
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$reporte = $_GET['reporte'] ?? '';
$filtro = $_GET['filtro'] ?? null;
$formato = $_GET['formato'] ?? 'pdf';

$REPORTES = [
    'proyectos_activos' => [
        'titulo' => 'Proyectos Activos',
        'query' => "SELECT nombre, cliente, fecha_creacion FROM proyectos WHERE estado = 'En progreso'",
        'headers' => ['Nombre', 'Cliente', 'Fecha de Creación'],
        'params' => false
    ],
    'tareas_por_usuario' => [
        'titulo' => 'Tareas por Usuario',
        'query' => "SELECT t.nombre, e.email, t.estado_id FROM tareas t
                    LEFT JOIN usuarios e ON t.usuario_id = e.id
                    WHERE t.usuario_id = ?",
        'headers' => ['Tarea', 'Usuario', 'Estado'],
        'params' => true
    ],
    'tareas_estado' => [
        'titulo' => 'Estado de las Tareas',
        'query' => "SELECT e.nombre, COUNT(t.id) as total FROM estados e
                    LEFT JOIN tareas t ON e.id = t.estado_id
                    GROUP BY e.nombre",
        'headers' => ['Estado', 'Total'],
        'params' => false
    ],
    'historial_sesiones' => [
        'titulo' => 'Historial de Sesiones',
        'query' => "SELECT u.email, h.ip_address, h.navegador, h.inicio_sesion FROM historial_sesiones h
                    LEFT JOIN usuarios u ON h.usuario_id = u.id",
        'headers' => ['Usuario', 'IP', 'Navegador', 'Fecha'],
        'params' => false
    ],
    'usuarios_activos' => [
        'titulo' => 'Usuarios Activos/Inactivos',
        'query' => "SELECT u.email, MAX(h.inicio_sesion) as ultima_sesion FROM usuarios u
                    LEFT JOIN historial_sesiones h ON u.id = h.usuario_id
                    GROUP BY u.email",
        'headers' => ['Usuario', 'Última Sesión'],
        'params' => false
    ],
    'productividad_empleados' => [
        'titulo' => 'Productividad por Empleado',
        'query' => "SELECT e.nombre, COUNT(t.id) as tareas_completadas FROM empleados e
                    LEFT JOIN tareas t ON e.id = t.usuario_id AND t.estado_id = (
                        SELECT id FROM estados WHERE nombre = 'Completado'
                    )
                    GROUP BY e.nombre",
        'headers' => ['Empleado', 'Tareas Completadas'],
        'params' => false
    ]
];

if (!isset($REPORTES[$reporte])) {
    die('Reporte no válido.');
}

$conf = $REPORTES[$reporte];

// Ejecutar consulta
$stmt = $conn->prepare($conf['query']);
if ($conf['params']) {
    $stmt->bind_param("i", $filtro);
}
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_all(MYSQLI_NUM);

// Exportar según formato
switch (strtolower($formato)) {
    case 'pdf':
        $pdf = new PDFReportGenerator($conf['titulo']);
        $pdf->addTable($conf['headers'], $data);
        $pdf->output("reporte_{$reporte}.pdf");
        break;

    case 'excel':
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($conf['headers'], NULL, 'A1');
        $sheet->fromArray($data, NULL, 'A2');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=reporte_{$reporte}.xlsx");
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        break;

    case 'csv':
        header('Content-Type: text/csv');
        header("Content-Disposition: attachment; filename=reporte_{$reporte}.csv");
        $fp = fopen('php://output', 'w');
        fputcsv($fp, $conf['headers']);
        foreach ($data as $row) {
            fputcsv($fp, $row);
        }
        fclose($fp);
        break;

    default:
        die('Formato no soportado.');
}
?>