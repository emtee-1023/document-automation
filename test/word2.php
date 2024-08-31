<?php
require '../vendor/autoload.php';

$phpWord = new \PhpOffice\PhpWord\PhpWord();
$section = $phpWord->addSection();
$section->addText('Hello World!');

$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
$objWriter->save('TestDocument.docx');

echo "Test document generated.";
?>