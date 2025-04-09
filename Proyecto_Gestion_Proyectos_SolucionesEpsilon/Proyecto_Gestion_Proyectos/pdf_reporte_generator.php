<?php
require __DIR__ . '/../../vendor/autoload.php'; // Carga el TCPDF

class CustomPDF extends TCPDF {
    public function Header() {
        // Título
        $this->SetFont('helvetica', 'B', 14);
        $this->Cell(0, 15, 'Soluciones Épsilon', 0, 1, 'C'); 
        $this->Ln(5); // Espacio entre el título y el logo
    }
    
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
        $this->pdf = new CustomPDF();
        $this->pdf->SetCreator('Sistema de Reportes');
        $this->pdf->SetAuthor('Sistema de Reportes');
        $this->pdf->SetTitle($titulo);
        $this->pdf->SetAutoPageBreak(true, 10);
        $this->pdf->AddPage();

        // Título
        $this->pdf->SetFont('helvetica', 'B', 16);
        $this->pdf->Cell(0, 10, $titulo, 0, 1, 'C'); 
        $this->pdf->Ln(15);

        // Logo debajo del título
        $logoX = ($this->pdf->getPageWidth() - 30) / 2; // Centra la imagen
        $this->pdf->Image('..\IMG/Logo_SE.png', $logoX, 20, 30, 0, 'PNG');  // posición de la imagen (Y, X, Z), ancho, alto, tipo de imagen)
        $this->pdf->Ln(20); // Espacio después del logo antes de la tabla
    }
    public function addTable($headers, $data) {
        // Calculamos el ancho de las columnas basado en los encabezados y los datos
        $columnWidths = [];
        
        // Calcular el ancho de las columnas basado en el contenido de los encabezados
        foreach ($headers as $header) {
            $columnWidths[] = max(40, $this->pdf->GetStringWidth($header) + 5); // Asegura un mínimo de ancho
        }

        // Ahora calculamos el ancho de cada columna basado en los datos
        foreach ($data as $row) {
            foreach ($row as $index => $col) {
                $columnWidths[$index] = max(40, $this->pdf->GetStringWidth($col) + 5);
            }
        }

        // Imprimir los encabezados
        $this->pdf->SetFont('helvetica', 'B', 12);
        foreach ($headers as $index => $header) {
            $this->pdf->Cell($columnWidths[$index], 10, $header, 1, 0, 'C');
        }
        $this->pdf->Ln();

        $this->pdf->SetFont('helvetica', '', 10);

        foreach ($data as $row) {
            foreach ($row as $index => $col) {
                $this->pdf->Cell($columnWidths[$index], 10, $col, 1, 0, 'C');
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
