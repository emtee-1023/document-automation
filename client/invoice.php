<?php
include 'php/dbconn.php';
require('../fpdf186/fpdf.php');
session_start();

$firm = $_SESSION['fid'];
$invoiceid = $_GET['invoiceid'];

$firmQuery = "SELECT * FROM firms where FirmID = $firm";
$firmResult = mysqli_query($conn, $firmQuery);


while($row = mysqli_fetch_assoc($firmResult)){
    $firmlogo = $row['FirmLogo'];
    $address = $row['FirmName'] .' . '.$row['FirmMail'].' . '.$row['FirmContact']. ' . '.$row['Address'];
}

$invoiceQuery = 
                "
                SELECT 
                    i.*, 
                    c.clientid, CONCAT(c.prefix,' ',c.fname,' ',c.mname,' ',c.lname) as clientname, 
                    u.userid, CONCAT(u.fname,' ', u.lname) as username
                FROM 
                    invoices i
                JOIN 
                    clients c ON c.clientid = i.clientid
                JOIN 
                    users u ON u.userid = i.userid
                WHERE 
                    i.invoiceid = $invoiceid

                ";
$invoiceResult = mysqli_query($conn, $invoiceQuery);


while($row = mysqli_fetch_assoc($invoiceResult)){
    $invoicenumber = $row['InvoiceNumber'];
    $datecreated = $row['CreatedAt'];
    $expirydate = $row['ExpiresAt'];
    $clientname = $row['clientname'];
    $createdby = $row['username'];

}

$itemsQuery = 
                "
                SELECT 
                    *
                FROM 
                    invoice_items
                WHERE 
                    invoiceid = $invoiceid

                ";
$itemsResult = mysqli_query($conn, $itemsQuery);



class PDF extends FPDF
{
    // Add a header
    function Header()
    {
        global $firmlogo;
        global $address;

        // Get the page width
        $pageWidth = $this->GetPageWidth();

        // Specify the image width
        $imageWidth = 60; // Width of the image

        // Calculate the X-coordinate for centering
        $x = ($pageWidth - $imageWidth) / 2;

        

        // Select Arial bold 15
        $this->SetFont('Arial', 'I', 10);
        // Title
        $imagePath = '../assets/img/submitted/'.$firmlogo;
        
        
        // Add the image centered horizontally
        $this->Image($imagePath, $x, 5, $imageWidth); // X, Y, width
        $this->Ln(15);

        //Address Information
        $this->Cell(0, 10, $address, 0, 1, 'C');
        $this->Ln(5);
    }

    // Add a footer
    function Footer()
    {
        global $address;

        // Position at 1.5 cm from bottom
        $this->SetY(-25);
        // Select Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
        $this->Ln(10);
        $this->Cell(0, 10, $address, 0, 0, 'C');

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
    $pdf->Cell(0, 10, 'INVOICE', 0, 1, 'C');
    $pdf->Ln(10);

    // Add Client and Company Details
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(0, 10, $invoicenumber, 0, 1);
    $pdf->Cell(0, 10, 'Client Name: '.$clientname, 0, 1);
    //$pdf->Cell(0, 10, 'Client type: Individual', 0, 1);
    $pdf->Ln(10);

    $pdf->Cell(0, 10, 'Created by: '.$createdby, 0, 1);
    $pdf->Cell(0, 10, 'Date Created: '.$datecreated, 0, 1);
    $pdf->Cell(0, 10, 'Expiry Date: '.$expirydate, 0, 1);

    $pdf->Ln(10);

    // Add Table Header
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(130, 10, 'Description', 1, 0, 'L');
    $pdf->Cell(50, 10, 'Amount in Ksh.', 1, 0, 'C');
    $pdf->Ln();

    // Add Invoice Items
    $pdf->SetFont('Arial', '', 12);
    $items = $itemsResult;

    foreach ($items as $item) {
        $pdf->SetFont('Arial', '', 10);

        // Description cell with left alignment (default)
        $pdf->Cell(130, 10, $item['Description'], 1, 0, 'L');

        // Amount cell with right alignment
        $pdf->Cell(50, 10, number_format($item['Amount'], 2), 1, 1, 'R'); // Align amount to the right and move to next line
    }

    // Calculate Total Amount
    $totalAmount = 0;
    foreach ($items as $item) {
        $totalAmount += $item['Amount'];
    }

    // Move to the right side of the page before adding the total amount row
    $leftMargin = -40; // Adjust this value based on your page layout
    $pageWidth = $pdf->GetPageWidth();
    $tableWidth = 160; // Adjust this based on your table width
    $x = $pageWidth - $tableWidth - $leftMargin; // Calculate X position to align row to the right

    // Set X position
    $pdf->SetX($x);

    // Add Total Amount row
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(50, 10, 'Total Amount', 1, 0, 'C'); // Label cell, aligned left within the cell
    $pdf->Cell(50, 10, number_format($totalAmount, 2), 1, 1, 'R'); // Total amount cell, aligned right within the cell

    // Save PDF to file
    $pdf->Output('invoice.pdf', 'D'); // 'I' means view in browser

?>
