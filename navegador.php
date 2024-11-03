<?php
session_start();
include('db.php');

if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

// Obtener todos los grupos
$grupos_result = $conn->query("SELECT * FROM grupos ORDER BY fecha_modificacion DESC");

// Obtener todos los archivos
$archivos_result = $conn->query("SELECT * FROM archivos ORDER BY fecha_subida DESC");

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Navegador de Archivos y Grupos</title>
    <link rel="stylesheet" type="text/css" href="assets/styles.css">
</head>
<body>
<div class="container">
    <h1>Navegador de Archivos y Grupos</h1>

    <h2>Grupos</h2>
    <?php if ($grupos_result->num_rows > 0): ?>
        <ul class="group-list">
            <?php while ($grupo = $grupos_result->fetch_assoc()): ?>
                <li>
                    <a href="detalles_grupo.php?id_grupo=<?php echo $grupo['id']; ?>"><?php echo htmlspecialchars($grupo['nombre_grupo']); ?></a>
                    <span class="meta">(Última Modificación: <?php echo $grupo['fecha_modificacion']; ?>)</span>
                    <div class="comments">
                        <strong>Comentario:</strong> <?php echo htmlspecialchars($grupo['comentario']); ?>
                    </div>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>No hay grupos disponibles.</p>
    <?php endif; ?>

    <h2>Archivos</h2>
    <?php if ($archivos_result->num_rows > 0): ?>
        <ul class="file-list">
            <?php while ($archivo = $archivos_result->fetch_assoc()): ?>
                <li>
                    <strong><?php echo htmlspecialchars($archivo['nombre_archivo']); ?></strong> 
                    <a href="<?php echo htmlspecialchars($archivo['ruta']); ?>" target="_blank">Abrir</a>
                    <span class="meta">(Fecha de Subida: <?php echo $archivo['fecha_subida']; ?>)</span>
                    <div class="file-meta">
                        <strong>Clasificación:</strong> <?php echo htmlspecialchars($archivo['clasificacion']); ?><br>
                        <strong>Comentarios:</strong> <?php echo htmlspecialchars($archivo['comentario']); ?>
                    </div>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>No hay archivos disponibles.</p>
    <?php endif; ?>

    <a href="dashboard.php">Volver al Dashboard</a>
</div>
</body>
</html>
