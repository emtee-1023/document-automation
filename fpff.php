<?php
require('fpdf186/fpdf.php');
session_start();

class PDF extends FPDF
{
    // Add a header
    function Header()
    {
        // Select Arial bold 15
        $this->SetFont('Arial', 'B', 15);
        // Title
        $imagePath = 'assets/img/submitted/' . $_SESSION['flogo'];
        $this->Image($imagePath, 10, 10, 50); // X, Y, width
        $this->Cell(0, 10, 'Invoice', 0, 1, 'C');
        $this->Ln(10);
    }

    // Add a footer
    function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Select Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }

    // Add a section to the invoice
    function ChapterTitle($title)
    {
        // Arial 12
        $this->SetFont('Arial', 'B', 12);
        // Background color
        $this->SetFillColor(200, 220, 255);
        // Title
        $this->Cell(0, 10, $title, 0, 1, 'L', true);
        // Line break
        $this->Ln(4);
    }

    function ChapterBody($body)
    {
        // Read text file
        $this->SetFont('Arial', '', 12);
        // Output justified text
        $this->MultiCell(0, 10, $body);
        // Line break
        $this->Ln();
    }
}

// Create instance of FPDF class
$pdf = new PDF();
$pdf->AddPage();

// Add Invoice Title
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Invoice #12345', 0, 1, 'C');
$pdf->Ln(10);

// Add Client and Company Details
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'Client: Mr. Mark Talamson', 0, 1);
$pdf->Cell(0, 10, 'Client type: Individual', 0, 1);
$pdf->Cell(0, 10, 'Address: Nairobi, Kenya', 0, 1);
$pdf->Ln(10);

// Add Table Header
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(90, 10, 'Description', 1);
$pdf->Cell(30, 10, 'Unit Price', 1);
$pdf->Cell(30, 10, 'Quantity', 1);
$pdf->Cell(30, 10, 'Total', 1);
$pdf->Ln();

// Add Invoice Items
$pdf->SetFont('Arial', '', 12);
$items = [
    ['Description' => 'Charges for Representation', 'Unit Price' => '5000.00', 'Quantity' => '1', 'Total' => '5000.00'],
    ['Description' => 'Charges for appearing in court', 'Unit Price' => '2000.00', 'Quantity' => '2', 'Total' => '4000.00']
];

foreach ($items as $item) {
    $pdf->Cell(90, 10, $item['Description'], 1);
    $pdf->Cell(30, 10, $item['Unit Price'], 1);
    $pdf->Cell(30, 10, $item['Quantity'], 1);
    $pdf->Cell(30, 10, $item['Total'], 1);
    $pdf->Ln();
}

// Add Total Amount
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(150, 10, 'Total Amount', 1);
$pdf->Cell(30, 10, '9000.00', 1);
$pdf->Ln(10);

// Save PDF to file
$pdf->Output('invoice.pdf', 'D'); // 'D' means download the file



?>
