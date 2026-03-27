<?php
session_start();
include("db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$result = mysqli_query($conn, "
SELECT r.*, v.name 
FROM records r 
JOIN vegetables v ON r.veg_id = v.id
WHERE r.status != 'Normal'
");
?>

<h2>Alerts</h2>

<table border="1" cellpadding="10">
<tr>
    <th>Vegetable</th>
    <th>Temperature</th>
    <th>Status</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)) { ?>
<tr>
    <td><?php echo $row['name']; ?></td>
    <td><?php echo $row['temperature']; ?>°C</td>
    <td style="color:red;"><?php echo $row['status']; ?></td>
</tr>
<?php } ?>
</table>