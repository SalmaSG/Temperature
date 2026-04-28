<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    mysqli_query($conn, "DELETE FROM temperature_humidity WHERE id='$id'");
}

header("Location:view.php");
exit();
?>
