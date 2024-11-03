<?php
session_start();
include('db.php');

if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

$id_grupo = $_GET['id_grupo'] ?? null;
if (!$id_grupo) {
    die("ID de grupo no proporcionado.");
}

// Obtener información del grupo y sus archivos
$query_grupo = $conn->prepare("SELECT g.nombre_grupo, c.nombre_carpeta, g.fecha_modificacion 
                               FROM grupos g
                               JOIN carpetas c ON g.id_carpeta = c.id
                               WHERE g.id = ?");
$query_grupo->bind_param("i", $id_grupo);
$query_grupo->execute();
$grupo_result = $query_grupo->get_result();
$grupo = $grupo_result->fetch_assoc();

$query_archivos = $conn->prepare("SELECT * FROM archivos WHERE id_grupo = ?");
$query_archivos->bind_param("i", $id_grupo);
$query_archivos->execute();
$archivos_result = $query_archivos->get_result();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalles del Grupo - <?php echo htmlspecialchars($grupo['nombre_grupo']); ?></title>
    <link rel="stylesheet" type="text/css" href="assets/styles.css">
</head>
<body>
<div class="container">
    <h2>Detalles del Grupo: <?php echo htmlspecialchars($grupo['nombre_grupo']); ?></h2>
    <p>Carpeta: <?php echo htmlspecialchars($grupo['nombre_carpeta']); ?></p>
    <p>Última Modificación: <?php echo $grupo['fecha_modificacion']; ?></p>

    <h3>Archivos en este grupo:</h3>
    <?php if ($archivos_result->num_rows > 0): ?>
        <ul>
            <?php while ($archivo = $archivos_result->fetch_assoc()): ?>
                <li>
                    <strong><?php echo htmlspecialchars($archivo['nombre_archivo']); ?></strong><br>
                    Clasificación: <?php echo htmlspecialchars($archivo['clasificacion']); ?><br>
                    Comentario: <?php echo htmlspecialchars($archivo['comentario']); ?><br>
                    Fecha de Subida: <?php echo $archivo['fecha_subida']; ?><br>
                    <a href="<?php echo htmlspecialchars($archivo['ruta']); ?>" target="_blank">Abrir Archivo</a>
                    <div class="file-options">
                        <?php if (pathinfo($archivo['nombre_archivo'], PATHINFO_EXTENSION) === 'pdf'): ?>
                            <a href="dividir_pdf.php?id_archivo=<?php echo $archivo['id']; ?>" class="option-icon">Dividir PDF</a>
                        <?php endif; ?>
                        
                        <?php if ($archivo['clasificacion'] === 'Audio'): ?>
                            <a href="marcar_transcripcion.php?id_archivo=<?php echo $archivo['id']; ?>" class="option-icon">Marcar para Transcripción</a>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>No hay archivos en este grupo.</p>
    <?php endif; ?>

    <a href="dashboard.php">Volver al Dashboard</a>
</div>
</body>
</html>
