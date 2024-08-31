<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../vendor/autoload.php';

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

function memoryCheck($stage) {
    echo "Memory usage after $stage: " . (memory_get_usage(true) / 1024 / 1024) . " MB\n";
}

// Create new PHPWord object
$phpWord = new PhpWord();
$phpWord->setDefaultFontName('Arial');
$phpWord->setDefaultFontSize(11);

try {
    // Add a section
    $section = $phpWord->addSection();

    // Add title
    $section->addText('In Account with', ['bold' => true, 'underline' => 'single']);
    $section->addText('Diro Advocates LLP', ['bold' => true, 'underline' => 'single']);
    $section->addText('Fee Note', ['bold' => true, 'underline' => 'single']);

    memoryCheck('Adding title');

    // Add details
    $section->addText('Our Ref: APA/89/22                                   Date: 18th July, 2024');
    $section->addText('Invoice Number: 22962');

    // Add address
    $section->addText('Mary Thumbi,');
    $section->addText('Legal Department,');
    $section->addText('APA Insurance Company Limited,');
    $section->addText('Apollo Centre, Ring Road Parklands, Westlands,');
    $section->addText('P.O. Box 30065-00100-GPO,');
    $section->addText('NAIROBI.', ['underline' => 'single']);

    memoryCheck('Adding details and address');

    // Add case details (simplified text to test)
    $section->addText('CMCC NO. E104 OF 2022; STEPHEN MBOGO WANJIRU VS. ONETEL LIMITED.', ['bold' => true]);

    memoryCheck('Adding case details');

    // Create the table (simplified)
    $table = $section->addTable();
    $table->addRow();
    $table->addCell(2000)->addText('Description', ['bold' => true]);
    $table->addCell(8000)->addText('Particulars', ['bold' => true]);
    $table->addCell(2000)->addText('Fees (Kshs.)', ['bold' => true]);

    memoryCheck('Adding table header');

    // Simplified table rows to test if table structure works
    $table->addRow();
    $table->addCell(2000)->addText('Test 1');
    $table->addCell(8000)->addText('Testing basic content in the table.');
    $table->addCell(2000)->addText('1,000.00');

    $table->addRow();
    $table->addCell(2000)->addText('Test 2');
    $table->addCell(8000)->addText('Another test row with simple content.');
    $table->addCell(2000)->addText('2,000.00');

    memoryCheck('Adding simplified table content');

    // Add total (simplified)
    $table->addRow();
    $table->addCell(2000)->addText('');
    $table->addCell(8000)->addText('GRAND TOTAL', ['bold' => true, 'underline' => 'single']);
    $table->addCell(2000)->addText('3,000.00', ['bold' => true, 'underline' => 'single']);

    memoryCheck('Adding grand total');

    // Add footer
    $section->addText("Advocates Name: Diro Advocates LLP                       Advocate's Signature:");
    $section->addText("NOTE: This is not a VAT invoice. A receipted VAT invoice will be issued upon settlement of this Fee Note.");
    $section->addText("Our Account details are as follows; Bank: Absa Bank, Account Name: Diro Advocates LLP, Account Number: 2040477880, Bank Code: 045: KCOOKENA, Hurlingham.", ['underline' => 'single']);

    memoryCheck('Adding footer');

    // Save the document
    $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
    $objWriter->save('FeeNote_Complete.docx');
    echo "Document generated successfully.\n";

    // File integrity check
    $fileSize = filesize('FeeNote_Complete.docx');
    echo "File size: " . $fileSize . " bytes\n";

    $fileContent = file_get_contents('FeeNote_Complete.docx');
    $contentStart = substr($fileContent, 0, 50);
    $contentEnd = substr($fileContent, -50);

    echo "File start: " . bin2hex($contentStart) . "\n";
    echo "File end: " . bin2hex($contentEnd) . "\n";

} catch (Exception $e) {
    echo "Error generating document: " . $e->getMessage() . "\n";
}

memoryCheck('Final');
?>
