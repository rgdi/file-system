<?php
include('db.php');

$grupo_id = $_POST['grupo_id'];

$carpetas_query = $conn->prepare("SELECT id, nombre_carpeta FROM carpetas WHERE grupo_id = ?");
$carpetas_query->bind_param("i", $grupo_id);
$carpetas_query->execute();
$carpetas_result = $carpetas_query->get_result();

while ($carpeta = $carpetas_result->fetch_assoc()) {
    echo "<option value='" . $carpeta['id'] . "'>" . htmlspecialchars($carpeta['nombre_carpeta']) . "</option>";
}