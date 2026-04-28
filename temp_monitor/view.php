<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$query = "SELECT * FROM temperature_humidity ORDER BY id DESC";
$result = mysqli_query($conn, $query);

$page_title = "Temperature & Humidity Records";
$active_page = "reports";
include "header.php";
?>

<section class="page-hero">
    <div>
        <p class="eyebrow">Sensor archive</p>
        <h2>Temperature and humidity records.</h2>
        <p>Review combined sensor readings from the legacy monitoring table.</p>
    </div>
    <div class="actions">
        <a class="btn" href="insert.php">Add New Data</a>
        <a class="btn btn-muted" href="graph.php">Open Graph</a>
    </div>
</section>

<article class="panel">
    <div class="panel-header">
        <h3>Sensor Records</h3>
        <span class="badge badge-active"><?php echo $result ? mysqli_num_rows($result) : 0; ?> rows</span>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Temperature</th>
                    <th>Humidity</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && mysqli_num_rows($result) > 0) { ?>
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td><?php echo (int) $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['temperature'], ENT_QUOTES, 'UTF-8'); ?>&deg;C</td>
                            <td><?php echo htmlspecialchars($row['humidity'], ENT_QUOTES, 'UTF-8'); ?>%</td>
                            <td><?php echo htmlspecialchars($row['date'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><a class="btn btn-muted" href="edit.php?id=<?php echo (int) $row['id']; ?>">Edit</a></td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr><td colspan="5">No sensor records found.</td></tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</article>

<?php include "footer.php"; ?>
