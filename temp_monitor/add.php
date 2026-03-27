<?php

include "db.php";
include "header.php";
if(isset($_POST['submit']))
{

$temp = $_POST['temperature'];
$hum = $_POST['humidity'];

mysqli_query($conn,"INSERT INTO temperature_humidity(temperature,humidity)
VALUES('$temp','$hum')");

header("Location:index.php");

}

?>

<h2>Add Data</h2>

<form method="POST">

Temperature

<input type="text" name="temperature">

Humidity

<input type="text" name="humidity">

<button name="submit">Save</button>

</form>