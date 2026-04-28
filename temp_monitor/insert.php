<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";

if (isset($_POST['submit'])) {
    $temp = $_POST['temperature'];
    $hum = $_POST['humidity'];

    $query = "INSERT INTO temperature_humidity(temperature,humidity) VALUES('$temp','$hum')";
    mysqli_query($conn, $query);

    $message = "Temperature and humidity data inserted successfully.";
}

$page_title = "Add Temperature & Humidity";
$active_page = "temperature";
include "header.php";
?>

<section class="page-hero">
    <div>
        <p class="eyebrow">Legacy sensor entry</p>
        <h2>Add temperature and humidity.</h2>
        <p>Use this screen for the older combined temperature-humidity table used by chart and record utilities.</p>
    </div>
    <div class="actions">
        <a class="btn btn-muted" href="view.php">View Records</a>
    </div>
</section>

<article class="form-card">
    <h3>New Sensor Row</h3>
    <?php if ($message !== "") { ?>
        <div class="message success"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php } ?>

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
