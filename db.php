<?php
// Configuración de la conexión a la base de datos
$servername = "localhost";
$username = "test";
$password = "marisol9";
$database = "file_system_academic";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $database);

// Comprobar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
