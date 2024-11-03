<?php
session_start();
include('db.php');

if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    foreach ($_FILES['files']['tmp_name'] as $key => $tmp_name) {
        if ($_FILES['files']['error'][$key] === UPLOAD_ERR_OK) {
            $file_name = basename($_FILES['files']['name'][$key]);
            $file_path = $upload_dir . $file_name;

            // Mover el archivo subido
            if (move_uploaded_file($tmp_name, $file_path)) {
                // Guardar en la base de datos
                $tipo = pathinfo($file_name, PATHINFO_EXTENSION);
                $importante = isset($_POST['importante'][$key]) ? 1 : 0;
                $dividir = ($tipo === 'pdf' && isset($_POST['dividir'][$key])) ? 1 : 0;

                $stmt = $conn->prepare("INSERT INTO archivos (nombre, tipo, ruta, importante, dividir) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("ssiii", $file_name, $tipo, $file_path, $importante, $dividir);
                $stmt->execute();
            } else {
                $error = "Error al subir el archivo: $file_name.";
            }
        }
    }
    header('Location: dashboard.php');
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Subir Archivos</title>
    <link rel="stylesheet" type="text/css" href="assets/styles.css">
</head>
<body>
<div class="container">
    <h1>Subir Archivos Académicos</h1>
    
    <form action="subir_archivo.php" method="POST" enctype="multipart/form-data">
        <label for="files">Selecciona los archivos:</label>
        <input type="file" name="files[]" accept=".pdf, .mp3, .wav" multiple required>
        
        <div id="opciones">
            <h3>Opciones:</h3>
            <label>
                <input type="checkbox" name="importante[0]"> Marcar como Importante
            </label>
            <label>
                <input type="checkbox" name="dividir[0]"> Dividir PDF (solo si es un archivo PDF)
            </label>
        </div>

        <button type="button" id="addOption">Agregar Otra Opción</button>
        <input type="submit" value="Subir Archivos">
    </form>

    <a href="dashboard.php">Volver al Dashboard</a>
</div>

<script>
    document.getElementById('addOption').addEventListener('click', function() {
        const opciones = document.getElementById('opciones');
        const newOption = document.createElement('div');
        newOption.innerHTML = `
            <label>
                <input type="checkbox" name="importante[${opciones.children.length}]"> Marcar como Importante
            </label>
            <label>
                <input type="checkbox" name="dividir[${opciones.children.length}]"> Dividir PDF (solo si es un archivo PDF)
            </label>
        `;
        opciones.appendChild(newOption);
    });
</script>
</body>
</html>
