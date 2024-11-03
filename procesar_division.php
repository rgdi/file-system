<?php
session_start();
include('db.php');
require 'vendor/autoload.php'; // Para usar la librería de PDF

use Smalot\PdfParser\Parser;
use setasign\Fpdi\Fpdi; // Para manipular PDFs

if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pages']) && isset($_POST['pdf_file'])) {
    $pdf_file = $_POST['pdf_file'];
    $selected_pages = $_POST['pages'];
    
    $output_dir = 'uploads/divididos/';
    if (!is_dir($output_dir)) {
        mkdir($output_dir, 0755, true);
    }

    $pdf = new Fpdi();
    $page_count = count($selected_pages);

    foreach ($selected_pages as $page) {
        $pdf->AddPage();
        $pdf->setSourceFile($pdf_file);
        $template_id = $pdf->importPage($page + 1); // Importar la página (FPDI utiliza índices base 1)
        $pdf->useTemplate($template_id);
    }

    $output_file = $output_dir . 'dividido_' . time() . '.pdf';
    $pdf->Output($output_file, 'F');

    echo "<p>Páginas divididas y guardadas en: <a href='$output_file'>Ver PDF</a></p>";
    echo '<a href="dividir_pdf.php">Dividir otro PDF</a>';
} else {
    echo "No se han seleccionado páginas o no se ha proporcionado un archivo PDF.";
}
?>
