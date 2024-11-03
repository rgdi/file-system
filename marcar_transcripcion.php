<?php
session_start();
include('db.php');

if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

$id_archivo = $_GET['id_archivo'] ?? null;
if (!$id_archivo) {
    die("ID de archivo no proporcionado.");
}

// Obtener información del archivo
$query_archivo = $conn->prepare("SELECT * FROM archivos WHERE id = ?");
$query_archivo->bind_param("i", $id_archivo);
$query_archivo->execute();
$archivo_result = $query_archivo->get_result();
$archivo = $archivo_result->fetch_assoc();

if (!$archivo || $archivo['clasificacion'] !== 'Audio') {
    die("Archivo no encontrado o no es un archivo de audio.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comentario_transcripcion = $_POST['comentario'] ?? '';
    $estado_transcripcion = 1; // 1 significa marcado para transcripción

    // Marcar archivo para transcripción
    $query_insert = $conn->prepare("INSERT INTO transcripciones (id_archivo, comentario, estado) VALUES (?, ?, ?)");
    $query_insert->bind_param("isi", $id_archivo, $comentario_transcripcion, $estado_transcripcion);

    if ($query_insert->execute()) {
        $success_message = "Archivo marcado para transcripción exitosamente.";
    } else {
        $error_message = "Error al marcar el archivo para transcripción.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Marcar Transcripción - <?php echo htmlspecialchars($archivo['nombre_archivo']); ?></title>
    <link rel="stylesheet" type="text/css" href="assets/styles.css">
</head>
<body>
<div class="container">
    <h2>Marcar Transcripción: <?php echo htmlspecialchars($archivo['nombre_archivo']); ?></h2>

    <?php if (isset($success_message)): ?>
        <div class="success"><?php echo $success_message; ?></div>
    <?php endif; ?>
    <?php if (isset($error_message)): ?>
        <div class="error"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <form action="marcar_transcripcion.php?id_archivo=<?php echo $id_archivo; ?>" method="POST">
        <label for="comentario">Comentario (opcional):</label>
        <textarea name="comentario" id="comentario" placeholder="Comentario adicional sobre la transcripción"></textarea>
        <input type="submit" value="Marcar para Transcripción">
    </form>

    <a href="detalles_grupo.php?id_grupo=<?php echo $archivo['id_grupo']; ?>">Volver al grupo</a>
</div>
</body>
</html>
