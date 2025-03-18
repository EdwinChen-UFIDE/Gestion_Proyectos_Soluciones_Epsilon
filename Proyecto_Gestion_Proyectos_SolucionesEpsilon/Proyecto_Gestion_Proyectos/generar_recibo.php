<?php
require_once 'db_config.php';
require('fpdf/fpdf.php');

// Obtener el ID de la transacción
$id = $_GET['id'] ?? null;
if (!$id) {
    die("ID de transacción no válido.");
}

// Conexión a la base de datos
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}

// Obtener los datos de la transacción
$sql = "SELECT t.id, t.tipo, t.monto, t.descripcion, t.fecha, c.nombre AS categoria 
        FROM transacciones t 
        LEFT JOIN categorias_gastos c ON t.categoria_id = c.id
        WHERE t.id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$transaccion = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$transaccion) {
    die("Transacción no encontrada.");
}

// Crear el PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(190, 10, 'Recibo de Pago', 0, 1, 'C');
$pdf->Ln(10);

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(50, 10, 'ID de Transacción:', 0);
$pdf->Cell(50, 10, $transaccion['id'], 0, 1);

$pdf->Cell(50, 10, 'Tipo:', 0);
$pdf->Cell(50, 10, ucfirst($transaccion['tipo']), 0, 1);

$pdf->Cell(50, 10, 'Monto:', 0);
$pdf->Cell(50, 10, number_format($transaccion['monto'], 2) . " USD", 0, 1);

$pdf->Cell(50, 10, 'Descripción:', 0);
$pdf->MultiCell(130, 10, $transaccion['descripcion'], 0);

$pdf->Cell(50, 10, 'Fecha:', 0);
$pdf->Cell(50, 10, $transaccion['fecha'], 0, 1);

$pdf->Cell(50, 10, 'Categoría:', 0);
$pdf->Cell(50, 10, $transaccion['categoria'] ?? 'N/A', 0, 1);

$pdf->Ln(20);
$pdf->Cell(190, 10, 'Gracias por su pago', 0, 1, 'C');

$pdf->Output();
?>
