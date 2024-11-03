<?php
session_start();
include('db.php');

if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo'])) {
    $archivo = $_FILES['archivo'];
    $grupo_id = $_POST['grupo_id'];
    $carpeta_id = $_POST['carpeta_id'];
    $clasificacion = $_POST['clasificacion'];
    $comentario = $_POST['comentario'];

    // Validar archivo
    if ($archivo['error'] === UPLOAD_ERR_OK) {
        // Obtener el nombre de la carpeta
        $carpeta_result = $conn->query("SELECT * FROM carpetas WHERE id = $carpeta_id");
        $carpeta = $carpeta_result->fetch_assoc();
        $carpeta_path = 'uploads/' . $carpeta['grupo_id'] . '/' . $carpeta['nombre_carpeta'] . '/';
        
        // Asegurarse de que la carpeta existe
        if (!is_dir($carpeta_path)) {
            mkdir($carpeta_path, 0755, true); // Crear la carpeta si no existe
        }

        // Ruta para guardar el archivo
        $file_name = basename($archivo['name']);
        $upload_file = $carpeta_path . $file_name;

        // Mover el archivo subido a la carpeta de destino
        if (move_uploaded_file($archivo['tmp_name'], $upload_file)) {
            // Registrar el archivo en la base de datos
            $stmt = $conn->prepare("INSERT INTO archivos (nombre_archivo, ruta, grupo_id, carpeta_id, clasificacion, comentario, fecha_subida) VALUES (?, ?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("ssiisss", $file_name, $upload_file, $grupo_id, $carpeta_id, $clasificacion, $comentario);
            $stmt->execute();

            echo "<p>Archivo subido con éxito.</p>";
        } else {
            echo "<p>Error al subir el archivo.</p>";
        }
    } else {
        echo "<p>Error en la carga del archivo.</p>";
    }
}

// Obtener grupos y carpetas para el formulario
$grupos_result = $conn->query("SELECT * FROM grupos ORDER BY fecha_modificacion DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Subir Archivo</title>
    <link rel="stylesheet" type="text/css" href="assets/styles.css">
</head>
<body>
<div class="container">
    <h1>Subir Nuevo Archivo</h1>
    <form action="crear_archivo.php" method="POST" enctype="multipart/form-data">
        <label for="grupo_id">Selecciona un grupo:</label>
        <select name="grupo_id" id="grupo_id" required>
            <?php while ($grupo = $grupos_result->fetch_assoc()): ?>
                <option value="<?php echo $grupo['id']; ?>"><?php echo htmlspecialchars($grupo['nombre_grupo']); ?></option>
            <?php endwhile; ?>
        </select>
        
        <label for="carpeta_id">Selecciona una carpeta:</label>
        <select name="carpeta_id" id="carpeta_id" required>
            <?php
            // Obtener carpetas del grupo seleccionado
            if (isset($_POST['grupo_id'])) {
                $selected_group_id = $_POST['grupo_id'];
                $carpetas_result = $conn->query("SELECT * FROM carpetas WHERE grupo_id = $selected_group_id");
                while ($carpeta = $carpetas_result->fetch_assoc()):
            ?>
                <option value="<?php echo $carpeta['id']; ?>"><?php echo htmlspecialchars($carpeta['nombre_carpeta']); ?></option>
            <?php endwhile; } ?>
        </select>

        <label for="archivo">Selecciona un archivo:</label>
        <input type="file" name="archivo" accept="*.*" required>

        <label for="clasificacion">Clasificación:</label>
        <input type="text" name="clasificacion" required>

        <label for="comentario">Comentario:</label>
        <textarea name="comentario" rows="3" placeholder="Agrega un comentario"></textarea>

        <input type="submit" value="Subir Archivo">
    </form>
    <a href="dashboard.php">Volver al Dashboard</a>
</div>
</body>
</html>
