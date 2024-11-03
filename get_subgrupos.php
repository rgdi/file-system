<?php
include('db.php');

$carpeta_id = $_POST['carpeta_id'];

$subgrupos_query = $conn->prepare("SELECT id, nombre_subgrupo FROM subgrupos WHERE carpeta_id = ?");
$subgrupos_query->bind_param("i", $carpeta_id);
$subgrupos_query->execute();
$subgrupos_result = $subgrupos_query->get_result();

while ($subgrupo = $subgrupos_result->fetch_assoc()) {
    echo "<option value='" . $subgrupo['id'] . "'>" . htmlspecialchars($subgrupo['nombre_subgrupo']) . "</option>";
}