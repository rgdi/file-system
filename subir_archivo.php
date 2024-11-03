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
            if (move_uploaded_file($tmp_name, $file_path)) {
                // Registrar el archivo en la base de datos
                $stmt = $conn->prepare("INSERT INTO archivos (nombre_archivo, ruta, grupo_id, carpeta_id, clasificacion, comentario, fecha_subida) VALUES (?, ?, ?, ?, ?, ?, NOW())");
                $stmt->bind_param("ssiiss", $file_name, $file_path, $_POST['grupo_id'], $_POST['carpeta_id'], $_POST['clasificacion'], $_POST['comentario']);
                $stmt->execute();
            }
        }
    }
}

// Obtener grupos y carpetas para el formulario
$grupos_result = $conn->query("SELECT * FROM grupos ORDER BY fecha_modificacion DESC");
$carpetas_result = $conn->query("SELECT * FROM carpetas ORDER BY fecha_modificacion DESC");
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
    <h2>Subir Archivos</h2>
    <form action="subir_archivo.php" method="POST" enctype="multipart/form-data">
        <label for="grupo_id">Selecciona un grupo:</label>
        <select name="grupo_id" id="grupo_id" required>
            <?php while ($grupo = $grupos_result->fetch_assoc()): ?>
                <option value="<?php echo $grupo['id']; ?>"><?php echo htmlspecialchars($grupo['nombre_grupo']); ?></option>
            <?php endwhile; ?>
        </select>
        
        <label for="carpeta_id">Selecciona una carpeta:</label>
        <select name="carpeta_id" id="carpeta_id" required>
            <?php while ($carpeta = $carpetas_result->fetch_assoc()): ?>
                <option value="<?php echo $carpeta['id']; ?>"><?php echo htmlspecialchars($carpeta['nombre_carpeta']); ?></option>
            <?php endwhile; ?>
        </select>

        <label for="clasificacion">Clasificación:</label>
        <input type="text" name="clasificacion" required>

        <label for="comentario">Comentario:</label>
        <textarea name="comentario" rows="3" placeholder="Agrega un comentario"></textarea>

        <label for="files">Selecciona archivos:</label>
        <input type="file" name="files[]" multiple required>

        <input type="submit" value="Subir Archivos">
    </form>
    <a href="dashboard.php">Volver al dashboard</a>
</div>
</body>
</html>
