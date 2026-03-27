<?php

include "db.php";
include "header.php";
$query = "SELECT * FROM temperature_humidity";

$result = mysqli_query($conn,$query);

?>

<h2>Temperature & Humidity Records</h2>

<table border="1">

<tr>

<th>ID</th>
<th>Temperature</th>
<th>Humidity</th>
<th>Date</th>

</tr>

<?php

while($row = mysqli_fetch_assoc($result))
{

?>

<tr>

<td><?php echo $row['id']; ?></td>

<td><?php echo $row['temperature']; ?></td>

<td><?php echo $row['humidity']; ?></td>

<td><?php echo $row['date']; ?></td>

</tr>

<?php

}

?>

</table>

<br>

<a href="insert.php">Add New Data</a>