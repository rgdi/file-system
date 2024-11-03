<?php
session_start();
include('db.php');

if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

// Obtener grupos existentes
$grupos_query = $conn->query("SELECT id, nombre_grupo FROM grupos");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_carpeta = $_POST['nombre_carpeta'];
    $grupo_id = $_POST['grupo_id'];
    $comentario = $_POST['comentario'];

    $query = $conn->prepare("INSERT INTO carpetas (nombre_carpeta, grupo_id, comentario) VALUES (?, ?, ?)");
    $query->bind_param("sis", $nombre_carpeta, $grupo_id, $comentario);

    if ($query->execute()) {
        // Crear la carpeta física
        $ruta_carpeta = "uploads/" . $grupo_id . "/" . $nombre_carpeta;
        if (!file_exists($ruta_carpeta)) {
            mkdir($ruta_carpeta, 0777, true);
        }
        echo "<p>Carpeta creada exitosamente. <a href='dashboard.php'>Volver al dashboard</a></p>";
    } else {
        echo "<p>Error al crear la carpeta.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Carpeta</title>
    <link rel="stylesheet" type="text/css" href="assets/styles.css">
</head>
<body>
<div class="container">
    <h2>Crear Nueva Carpeta</h2>
    <form action="crear_carpeta.php" method="POST">
        <input type="text" name="nombre_carpeta" required placeholder="Nombre de la carpeta">
        <select name="grupo_id" required>
            <option value="">Seleccione un grupo</option>
            <?php while ($grupo = $grupos_query->fetch_assoc()): ?>
                <option value="<?php echo $grupo['id']; ?>"><?php echo htmlspecialchars($grupo['nombre_grupo']); ?></option>
            <?php endwhile; ?>
        </select>
        <textarea name="comentario" placeholder="Comentario opcional"></textarea>
        <input type="submit" value="Crear Carpeta">
    </form>
    <a href="dashboard.php">Volver al dashboard</a>
</div>
</body>
</html>
