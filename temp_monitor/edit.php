<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = intval($_GET['id'] ?? 0);
$data = mysqli_query($conn, "SELECT * FROM temperature_humidity WHERE id='$id'");
$row = mysqli_fetch_assoc($data);

if (!$row) {
    header("Location:view.php");
    exit();
}

if (isset($_POST['update'])) {
    $temp = $_POST['temperature'];
    $hum = $_POST['humidity'];

    mysqli_query($conn, "UPDATE temperature_humidity SET temperature='$temp', humidity='$hum' WHERE id='$id'");

    header("Location:view.php");
    exit();
}

$page_title = "Edit Sensor Record";
$active_page = "reports";
include "header.php";
?>

<section class="page-hero">
    <div>
        <p class="eyebrow">Edit record</p>
        <h2>Update sensor reading #<?php echo (int) $id; ?>.</h2>
        <p>Adjust the stored temperature and humidity values for this record.</p>
    </div>
    <div class="actions">
        <a class="btn btn-muted" href="view.php">Back to Records</a>
    </div>
</section>

<article class="form-card">
    <h3>Record Details</h3>
    <form method="POST">
        <div class="form-grid">
            <div class="field">
                <label for="temperature">Temperature</label>
                <input id="temperature" type="number" step="0.1" name="temperature" value="<?php echo htmlspecialchars($row['temperature'], ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>

            <div class="field">
                <label for="humidity">Humidity</label>
                <input id="humidity" type="number" step="0.1" name="humidity" value="<?php echo htmlspecialchars($row['humidity'], ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
        </div>

        <button style="margin-top:18px;" name="update">Update</button>
    </form>
</article>

<?php include "footer.php"; ?>
