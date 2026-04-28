<?php
session_start();
include("db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";
$saved_status = "";

if (isset($_POST['save'])) {
    $veg_id = $_POST['veg_id'];
    $temp = $_POST['temperature'];

    $veg = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM vegetables WHERE id='$veg_id'"));

    if ($veg) {
        if ($temp < $veg['min_temp']) {
            $status = "Low";
        } elseif ($temp > $veg['max_temp']) {
            $status = "High";
        } else {
            $status = "Normal";
        }

        mysqli_query($conn, "INSERT INTO records (veg_id, temperature, status) VALUES ('$veg_id','$temp','$status')");
        $saved_status = $status;
        $message = "Temperature reading saved successfully.";
    } else {
        $message = "Please select a valid vegetable profile.";
    }
}

$vegetables = mysqli_query($conn, "SELECT * FROM vegetables ORDER BY name ASC");
$recent_records = mysqli_query($conn, "
    SELECT r.*, v.name
    FROM records r
    JOIN vegetables v ON r.veg_id = v.id
    ORDER BY r.id DESC
    LIMIT 5
");

$page_title = "Enter Temperature";
$active_page = "temperature";
include("header.php");
?>

<section class="page-hero">
    <div>
        <p class="eyebrow">Temperature intake</p>
        <h2>Record a fresh storage reading.</h2>
        <p>Choose the produce profile, enter the current temperature, and TempMonitor will classify it against the configured safe range.</p>
    </div>
    <div class="actions">
        <a class="btn btn-muted" href="reports.php">View Reports</a>
    </div>
</section>

<section class="grid-two">
    <article class="form-card">
        <h3>New Reading</h3>

        <?php if ($message !== "") { ?>
            <div class="message <?php echo $saved_status ? 'success' : ''; ?>">
                <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
                <?php if ($saved_status) { ?>
                    <span class="badge <?php echo htmlspecialchars($saved_status, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($saved_status, ENT_QUOTES, 'UTF-8'); ?></span>
                <?php } ?>
            </div>
        <?php } ?>

        <form method="POST">
            <div class="field">
                <label for="veg_id">Vegetable profile</label>
                <select id="veg_id" name="veg_id" required>
                    <?php while ($v = mysqli_fetch_assoc($vegetables)) { ?>
                        <option value="<?php echo (int) $v['id']; ?>">
                            <?php echo htmlspecialchars($v['name'], ENT_QUOTES, 'UTF-8'); ?>
                            (<?php echo htmlspecialchars($v['min_temp'], ENT_QUOTES, 'UTF-8'); ?>&deg;C -
                            <?php echo htmlspecialchars($v['max_temp'], ENT_QUOTES, 'UTF-8'); ?>&deg;C)
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="field" style="margin-top:14px;">
                <label for="temperature">Temperature in Celsius</label>
                <input id="temperature" type="number" step="0.1" name="temperature" placeholder="Example: 7.5" required>
            </div>

            <button style="margin-top:18px;" name="save">Submit Reading</button>
        </form>
    </article>

    <article class="panel">
        <div class="panel-header">
            <h3>Recent Readings</h3>
            <a class="btn btn-muted" href="alerts.php">Alerts</a>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Vegetable</th>
                        <th>Temperature</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($recent_records && mysqli_num_rows($recent_records) > 0) { ?>
                        <?php while ($row = mysqli_fetch_assoc($recent_records)) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($row['temperature'], ENT_QUOTES, 'UTF-8'); ?>&deg;C</td>
                                <td><span class="badge <?php echo htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                                <td><?php echo htmlspecialchars($row['created_at'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr><td colspan="4">No readings have been recorded yet.</td></tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </article>
</section>

<?php include("footer.php"); ?>
