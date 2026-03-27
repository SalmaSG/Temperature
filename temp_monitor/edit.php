<?php

include "db.php";
include "header.php";
$id = $_GET['id'];

$data = mysqli_query($conn,"SELECT * FROM temperature_humidity WHERE id='$id'");

$row = mysqli_fetch_assoc($data);

if(isset($_POST['update']))
{

$temp = $_POST['temperature'];
$hum = $_POST['humidity'];

mysqli_query($conn,"UPDATE temperature_humidity 
SET temperature='$temp', humidity='$hum' WHERE id='$id'");

header("Location:index.php");

}

?>

<form method="POST">

Temperature

<input type="text" name="temperature" value="<?php echo $row['temperature']; ?>">

Humidity

<input type="text" name="humidity" value="<?php echo $row['humidity']; ?>">

<button name="update">Update</button>

</form>