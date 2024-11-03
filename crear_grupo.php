<?php
session_start();
include('db.php');

if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

// Obtener todas las carpetas para seleccionarlas en el formulario
$result = $conn->query("SELECT id, nombre_carpeta FROM carpetas ORDER BY fecha_creacion DESC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_grupo = $_POST['nombre_grupo'];
    $id_carpeta = $_POST['id_carpeta'];
    $comentario = $_POST['comentario'];

    $query = $conn->prepare("INSERT INTO grupos (nombre_grupo, id_carpeta, comentario) VALUES (?, ?, ?)");
    $query->bind_param("sis", $nombre_grupo, $id_carpeta, $comentario);

    if ($query->execute()) {
        echo "<p>Grupo creado exitosamente. <a href='dashboard.php'>Volver al dashboard</a></p>";
    } else {
        echo "<p>Error al crear el grupo.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Grupo</title>
    <link rel="stylesheet" type="text/css" href="assets/styles.css">
</head>
<body>
<div class="container">
    <h2>Crear Nuevo Grupo</h2>
    <form action="crear_grupo.php" method="POST">
        <input type="text" name="nombre_grupo" required placeholder="Nombre del grupo">
        <select name="id_carpeta" required>
            <option value="" disabled selected>Selecciona una carpeta</option>
            <?php while ($row = $result->fetch_assoc()): ?>
                <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['nombre_carpeta']); ?></option>
            <?php endwhile; ?>
        </select>
        <textarea name="comentario" placeholder="Comentario opcional"></textarea>
        <input type="submit" value="Crear Grupo">
    </form>
    <a href="dashboard.php">Volver al dashboard</a>
</div>
</body>
</html>
