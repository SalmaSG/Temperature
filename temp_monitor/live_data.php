<?php
include "db.php";

header("Content-Type: application/json");

$query = mysqli_query($conn, "SELECT * FROM temperature_humidity ORDER BY id DESC LIMIT 1");
$row = mysqli_fetch_assoc($query);

echo json_encode([
    "temperature" => $row['temperature'] ?? null,
    "humidity" => $row['humidity'] ?? null
]);
?>
