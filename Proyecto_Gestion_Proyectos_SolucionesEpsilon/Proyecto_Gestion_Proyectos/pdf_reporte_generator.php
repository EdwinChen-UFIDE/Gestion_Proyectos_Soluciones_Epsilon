<?php
require __DIR__ . '/../../vendor/autoload.php'; // Carga el TCPDF

class CustomPDF extends TCPDF {
    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $fecha = date('d/m/Y H:i');
        $this->Cell(0, 10, "Generado el $fecha - Página " . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C');
    }
}
class PDFReportGenerator {
    private $pdf;

    public function __construct($titulo) {
        // Crea instancia de TCPDF 
        $this->pdf = new TCPDF();
        $this->pdf->SetCreator('Sistema de Reportes');
        $this->pdf->SetAuthor('Sistema de Reportes');
        $this->pdf->SetTitle($titulo);
        $this->pdf->SetAutoPageBreak(true, 10);
        $this->pdf->AddPage();
        $this->pdf->SetFont('helvetica', 'B', 16);
        $this->pdf->Cell(0, 10, $titulo, 0, 1, 'C');
        $this->pdf->Ln(10);
    }

    public function addTable($headers, $data) {
        $this->pdf->SetFont('helvetica', 'B', 12);
        foreach ($headers as $header) {
            $this->pdf->Cell(60, 10, $header, 1, 0, 'C');
        }
        $this->pdf->Ln();

        $this->pdf->SetFont('helvetica', '', 10);
        foreach ($data as $row) {
            foreach ($row as $col) {
                $this->pdf->Cell(60, 10, $col, 1, 0);
            }
            $this->pdf->Ln();
        }
    }

    public function output($filename) {
        ob_clean(); 
        $this->pdf->Output($filename, 'I'); 
    }
}
?>
