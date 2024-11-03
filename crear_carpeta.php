<?php
session_start();
include('db.php');

if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_carpeta = $_POST['nombre_carpeta'];
    $comentario = $_POST['comentario'];

    $query = $conn->prepare("INSERT INTO carpetas (nombre_carpeta, comentario) VALUES (?, ?)");
    $query->bind_param("ss", $nombre_carpeta, $comentario);

    if ($query->execute()) {
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
        <textarea name="comentario" placeholder="Comentario opcional"></textarea>
        <input type="submit" value="Crear Carpeta">
    </form>
    <a href="dashboard.php">Volver al dashboard</a>
</div>
</body>
</html>
