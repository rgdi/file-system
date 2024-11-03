<?php
session_start();
include('db.php');
require 'vendor/autoload.php'; // Para usar la librería de PDF

use Smalot\PdfParser\Parser;

if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

// Manejo de carga de archivo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdf'])) {
    $pdf_file = $_FILES['pdf'];
    
    // Validar archivo
    if ($pdf_file['error'] === UPLOAD_ERR_OK) {
        $file_name = basename($pdf_file['name']);
        $upload_dir = 'uploads/pdf_temp/';
        
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $upload_file = $upload_dir . $file_name;

        // Mover el archivo subido
        if (move_uploaded_file($pdf_file['tmp_name'], $upload_file)) {
            // Cargar el PDF y contar las páginas
            $parser = new Parser();
            $pdf = $parser->parseFile($upload_file);
            $pages = $pdf->getPages();
            $page_count = count($pages);
        } else {
            $error = "Error al subir el archivo.";
        }
    } else {
        $error = "Error en la carga del archivo.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dividir PDF</title>
    <link rel="stylesheet" type="text/css" href="assets/styles.css">
    <style>
        .page-preview {
            display: inline-block;
            margin: 5px;
            border: 1px solid #ccc;
            padding: 10px;
        }
        .page-preview img {
            max-width: 100px; /* Ajusta el tamaño según tus necesidades */
            max-height: 150px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Dividir PDF</h1>
    
    <?php if (isset($error)): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>

    <form action="dividir_pdf.php" method="POST" enctype="multipart/form-data">
        <label for="pdf">Selecciona un archivo PDF:</label>
        <input type="file" name="pdf" accept="application/pdf" required>
        <input type="submit" value="Cargar PDF">
    </form>

    <?php if (isset($page_count)): ?>
        <h2>Páginas del PDF (Total: <?php echo $page_count; ?>)</h2>
        <form action="procesar_division.php" method="POST">
            <?php for ($i = 0; $i < $page_count; $i++): ?>
                <div class="page-preview">
                    <label>
                        <input type="checkbox" name="pages[]" value="<?php echo $i; ?>"> Página <?php echo $i + 1; ?>
                    </label>
                    <br>
                    <?php
                    // Cargar imagen de la página (puedes utilizar una librería que convierta PDF a imagen)
                    // Esto es solo un ejemplo. Asegúrate de tener una función para obtener la imagen de cada página.
                    echo '<img src="data:image/png;base64,' . base64_encode($pages[$i]->getImage()) . '" alt="Página ' . ($i + 1) . '">';
                    ?>
                </div>
            <?php endfor; ?>
            <input type="hidden" name="pdf_file" value="<?php echo $upload_file; ?>">
            <input type="submit" value="Dividir Seleccionadas">
        </form>
    <?php endif; ?>
    <a href="dashboard.php">Volver al Dashboard</a>
</div>
</body>
</html>
