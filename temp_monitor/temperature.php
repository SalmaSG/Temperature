<?php
session_start();
include("db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['save'])) {
    $veg_id = $_POST['veg_id'];
    $temp = $_POST['temperature'];

    $veg = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM vegetables WHERE id='$veg_id'"));

    if ($temp < $veg['min_temp']) {
        $status = "Low";
    } elseif ($temp > $veg['max_temp']) {
        $status = "High";
    } else {
        $status = "Normal";
    }

    mysqli_query($conn, "INSERT INTO records (veg_id, temperature, status) VALUES ('$veg_id','$temp','$status')");
}

$vegetables = mysqli_query($conn, "SELECT * FROM vegetables");
?>

<h2>Enter Temperature</h2>

<form method="POST">
    <select name="veg_id">
        <?php while($v = mysqli_fetch_assoc($vegetables)) { ?>
            <option value="<?php echo $v['id']; ?>"><?php echo $v['name']; ?></option>
        <?php } ?>
    </select>

    <input type="number" name="temperature" placeholder="Temperature" required>
    <button name="save">Submit</button>
</form>