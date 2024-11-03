<?php
session_start();
include('db.php');

if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

// Obtener grupos para mostrar en el dashboard
$grupos_result = $conn->query("SELECT * FROM grupos ORDER BY fecha_modificacion DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" type="text/css" href="assets/styles.css">
</head>
<body>
<div class="container">
    <h1>Dashboard</h1>
    <div class="links">
        <h2>Gestión de Archivos y Carpetas</h2>
        <ul>
            <li><a href="crear_archivo.php">Crear Archivo</a></li>
            <li><a href="crear_carpeta.php">Crear Carpeta</a></li>
            <li><a href="crear_grupo.php">Crear Grupo</a></li>
            <li><a href="dividir_pdf.php">Dividir PDF</a></li>
            <li><a href="marcar_transcripcion.php">Marcar Transcripción</a></li>
            <li><a href="navegador.php">Navegador de Archivos</a></li>
            <li><a href="subir_archivo.php">Subir Archivo</a></li>
            <li><a href="detalles_grupo.php">Detalles del Grupo</a></li>
        </ul>
        
        <h2>Configuración</h2>
        <ul>
            <li><a href="login.php">Iniciar Sesión</a></li>
            <li><a href="register.php">Registrar Usuario</a></li>
            <li><a href="estructura.txt">Estructura del Sistema</a></li>
        </ul>

        <h2>Grupos Recientes</h2>
        <ul>
            <?php while ($grupo = $grupos_result->fetch_assoc()): ?>
                <li>
                    <a href="detalles_grupo.php?id=<?php echo $grupo['id']; ?>">
                        <?php echo htmlspecialchars($grupo['nombre_grupo']); ?>
                    </a>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>
    <a href="logout.php">Cerrar Sesión</a>
</div>
</body>
</html>
