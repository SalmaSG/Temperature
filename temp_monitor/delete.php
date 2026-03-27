<?php

include "db.php";
include "header.php";
$id = $_GET['id'];

mysqli_query($conn,"DELETE FROM temperature_humidity WHERE id='$id'");

header("Location:index.php");

?>