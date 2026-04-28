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
    ORDER BY r.id DESC
");

$page_title = "Alerts";
$active_page = "alerts";
include("header.php");
?>

<section class="page-hero">
    <div>
        <p class="eyebrow">Risk monitor</p>
        <h2>Temperature alerts that need attention.</h2>
        <p>Review every reading outside the configured produce range and prioritize the most recent risk first.</p>
    </div>
    <div class="actions">
        <a class="btn" href="temperature.php">Add Reading</a>
        <a class="btn btn-muted" href="reports.php">Full Reports</a>
    </div>
</section>

<article class="panel">
    <div class="panel-header">
        <h3>Alert Log</h3>
        <span class="badge badge-danger"><?php echo $result ? mysqli_num_rows($result) : 0; ?> active</span>
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
                <?php if ($result && mysqli_num_rows($result) > 0) { ?>
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($row['temperature'], ENT_QUOTES, 'UTF-8'); ?>&deg;C</td>
                            <td><span class="badge <?php echo htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                            <td><?php echo htmlspecialchars($row['created_at'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr><td colspan="4">No alerts right now. All monitored readings are normal.</td></tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</article>

<?php include("footer.php"); ?>
