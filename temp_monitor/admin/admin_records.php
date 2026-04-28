<?php
include("admin_check.php");
include("../db.php");

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM records WHERE id=$id");
    header("Location: admin_records.php");
    exit();
}

$status = $_GET['status'] ?? "All";
$where = "";

if (in_array($status, ["Normal", "High", "Low"], true)) {
    $safe_status = mysqli_real_escape_string($conn, $status);
    $where = "WHERE r.status='$safe_status'";
}

$records = mysqli_query($conn, "
    SELECT r.*, v.name
    FROM records r
    LEFT JOIN vegetables v ON r.veg_id = v.id
    $where
    ORDER BY r.id DESC
");

$total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM records"))['c'];
$normal = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM records WHERE status='Normal'"))['c'];
$high = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM records WHERE status='High'"))['c'];
$low = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM records WHERE status='Low'"))['c'];
$admin_name = htmlspecialchars($_SESSION['admin_name'] ?? 'Admin', ENT_QUOTES, 'UTF-8');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Records</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="app-shell">
        <aside class="sidebar">
            <div class="brand">
                <div class="brand-mark">AD</div>
                <div>
                    <h1>Admin Panel</h1>
                    <span><?php echo $admin_name; ?> console</span>
                </div>
            </div>

            <nav class="nav" aria-label="Admin navigation">
                <a href="admin_dashboard.php"><span class="nav-icon">01</span>Dashboard</a>
                <a href="admin_vegetables.php"><span class="nav-icon">02</span>Vegetables</a>
                <a href="users.php"><span class="nav-icon">03</span>Users</a>
                <a class="active" href="admin_records.php"><span class="nav-icon">04</span>Records</a>
                <a href="admin_reports.php"><span class="nav-icon">05</span>Reports</a>
                <a href="admin_logout.php"><span class="nav-icon">06</span>Logout</a>
            </nav>
        </aside>

        <main class="main">
            <section class="page-hero">
                <div>
                    <p class="eyebrow">Data control</p>
                    <h2>Manage temperature records.</h2>
                    <p>Filter readings by status, inspect recent storage conditions, and remove incorrect records when needed.</p>
                </div>
                <div class="actions">
                    <a class="btn <?php echo $status === 'All' ? '' : 'btn-muted'; ?>" href="admin_records.php">All</a>
                    <a class="btn <?php echo $status === 'Normal' ? '' : 'btn-muted'; ?>" href="?status=Normal">Normal</a>
                    <a class="btn <?php echo $status === 'High' ? '' : 'btn-muted'; ?>" href="?status=High">High</a>
                    <a class="btn <?php echo $status === 'Low' ? '' : 'btn-muted'; ?>" href="?status=Low">Low</a>
                </div>
            </section>

            <section class="grid-two">
                <article class="metric-card panel-body">
                    <p class="eyebrow">Total</p>
                    <h2><?php echo (int) $total; ?></h2>
                    <p>All readings.</p>
                </article>
                <article class="metric-card panel-body">
                    <p class="eyebrow">Normal</p>
                    <h2><?php echo (int) $normal; ?></h2>
                    <p>Inside safe range.</p>
                </article>
                <article class="metric-card panel-body">
                    <p class="eyebrow">High</p>
                    <h2><?php echo (int) $high; ?></h2>
                    <p>Above maximum range.</p>
                </article>
                <article class="metric-card panel-body">
                    <p class="eyebrow">Low</p>
                    <h2><?php echo (int) $low; ?></h2>
                    <p>Below minimum range.</p>
                </article>
            </section>

            <article class="panel" style="margin-top:20px;">
                <div class="panel-header">
                    <h3>Records</h3>
                    <span class="badge badge-active"><?php echo $records ? mysqli_num_rows($records) : 0; ?> shown</span>
                </div>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Vegetable</th>
                                <th>Temperature</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($records && mysqli_num_rows($records) > 0) { ?>
                                <?php while ($row = mysqli_fetch_assoc($records)) { ?>
                                    <tr>
                                        <td><?php echo (int) $row['id']; ?></td>
                                        <td><?php echo htmlspecialchars($row['name'] ?? 'Unknown', ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($row['temperature'], ENT_QUOTES, 'UTF-8'); ?>&deg;C</td>
                                        <td><span class="badge <?php echo htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                                        <td><?php echo htmlspecialchars($row['created_at'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td>
                                            <a class="btn btn-danger" href="?delete=<?php echo (int) $row['id']; ?>" onclick="return confirm('Delete this temperature record?')">Delete</a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php } else { ?>
                                <tr><td colspan="6">No records found for this filter.</td></tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </article>
        </main>
    </div>
</body>
</html>
