<?php
session_start();
include("db.php");


// ADD vegetable
if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $min = $_POST['min_temp'];
    $max = $_POST['max_temp'];
    $humidity = $_POST['humidity'];

    mysqli_query($conn, "INSERT INTO vegetables (name, min_temp, max_temp, humidity) 
                         VALUES ('$name','$min','$max','$humidity')");
}

// DELETE
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM vegetables WHERE id=$id");
}

// UPDATE
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $min = $_POST['min_temp'];
    $max = $_POST['max_temp'];
    $humidity = $_POST['humidity'];

    mysqli_query($conn, "UPDATE vegetables 
        SET name='$name', min_temp='$min', max_temp='$max', humidity='$humidity' 
        WHERE id=$id");
}

$result = mysqli_query($conn, "SELECT * FROM vegetables");
?>

<h2>Vegetables Management</h2>

<!-- ADD FORM -->
<form method="POST">
    <input type="text" name="name" placeholder="Vegetable name" required>
    <input type="number" name="min_temp" placeholder="Min Temp" required>
    <input type="number" name="max_temp" placeholder="Max Temp" required>
    <input type="number" name="humidity" placeholder="Humidity %" required>
    <button name="add">Add Vegetable</button>
</form>

<br><br>

<!-- TABLE -->
<table border="1" cellpadding="10">
<tr>
    <th>Name</th>
    <th>Min Temp</th>
    <th>Max Temp</th>
    <th>Humidity</th>
    <th>Actions</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)) { ?>
<tr>
    <form method="POST">
        <td>
            <input type="text" name="name" value="<?php echo $row['name']; ?>">
        </td>
        <td>
            <input type="number" name="min_temp" value="<?php echo $row['min_temp']; ?>">
        </td>
        <td>
            <input type="number" name="max_temp" value="<?php echo $row['max_temp']; ?>">
        </td>
        <td>
            <input type="number" name="humidity" value="<?php echo $row['humidity']; ?>">
        </td>

        <td>
            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
            <button name="update">Update</button>
            <a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Delete?')">Delete</a>
        </td>
    </form>
</tr>
<?php } ?>

</table>