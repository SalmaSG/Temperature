<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['submit'])) {
    $temp = $_POST['temperature'];
    $hum = $_POST['humidity'];

    mysqli_query($conn, "INSERT INTO temperature_humidity(temperature,humidity) VALUES('$temp','$hum')");

    header("Location:view.php");
    exit();
}

$page_title = "Add Data";
$active_page = "temperature";
include "header.php";
?>

<section class="page-hero">
    <div>
        <p class="eyebrow">Sensor utility</p>
        <h2>Add a combined sensor reading.</h2>
        <p>Store temperature and humidity values for the legacy sensor chart and records table.</p>
    </div>
</section>

<article class="form-card">
    <h3>Add Data</h3>
    <form method="POST">
        <div class="form-grid">
            <div class="field">
                <label for="temperature">Temperature</label>
                <input id="temperature" type="number" step="0.1" name="temperature" required>
            </div>

            <div class="field">
                <label for="humidity">Humidity</label>
                <input id="humidity" type="number" step="0.1" name="humidity" required>
            </div>
        </div>

        <button style="margin-top:18px;" name="submit">Save</button>
    </form>
</article>

<?php include "footer.php"; ?>
