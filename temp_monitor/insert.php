<?php

include "db.php";
include "header.php";
if(isset($_POST['submit']))
{

$temp = $_POST['temperature'];
$hum = $_POST['humidity'];

$query = "INSERT INTO temperature_humidity(temperature,humidity)
VALUES('$temp','$hum')";

mysqli_query($conn,$query);

echo "Data Inserted Successfully";

}

?>

<h2>Add Temperature & Humidity</h2>

<form method="POST">

Temperature
<input type="text" name="temperature">

Humidity
<input type="text" name="humidity">

<button name="submit">Save</button>

</form>

<br>

<a href="view.php">View Records</a>