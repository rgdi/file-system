<?php
include('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $query = $conn->prepare("INSERT INTO usuarios (username, password) VALUES (?, ?)");
    $query->bind_param("ss", $username, $password);

    if ($query->execute()) {
        echo "<p>Usuario registrado exitosamente. <a href='login.php'>Inicia sesión aquí</a>.</p>";
    } else {
        echo "<p>Error al registrar el usuario.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Usuario</title>
    <link rel="stylesheet" type="text/css" href="assets/styles.css">
</head>
<body>
<div class="container">
    <h2>Registro de Usuario</h2>
    <form action="register.php" method="POST">
        <input type="text" name="username" required placeholder="Nombre de usuario">
        <input type="password" name="password" required placeholder="Contraseña">
        <input type="submit" value="Registrarse">
    </form>
    <a href="login.php">¿Ya tienes una cuenta? Inicia sesión</a>
</div>
</body>
</html>
