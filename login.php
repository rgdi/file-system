<?php
session_start();
include('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Consulta del usuario en la base de datos
    $query = $conn->prepare("SELECT id, password FROM usuarios WHERE username = ?");
    $query->bind_param("s", $username);
    $query->execute();
    $result = $query->get_result();

    // Verificar el usuario y la contraseña
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $username;
            header('Location: dashboard.php');
            exit;
        } else {
            echo "<p>Contraseña incorrecta.</p>";
        }
    } else {
        echo "<p>Usuario no encontrado.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inicio de Sesión</title>
    <link rel="stylesheet" type="text/css" href="assets/styles.css">
</head>
<body>
<div class="container">
    <h2>Inicio de Sesión</h2>
    <form action="login.php" method="POST">
        <input type="text" name="username" required placeholder="Nombre de usuario">
        <input type="password" name="password" required placeholder="Contraseña">
        <input type="submit" value="Iniciar sesión">
    </form>
    <a href="register.php">¿No tienes cuenta? Regístrate aquí</a>
</div>
</body>
</html>
