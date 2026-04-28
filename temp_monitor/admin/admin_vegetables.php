<?php
include("admin_check.php");
include("../db.php");

$message = "";

if (isset($_POST['add'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $min = mysqli_real_escape_string($conn, $_POST['min_temp']);
    $max = mysqli_real_escape_string($conn, $_POST['max_temp']);
    $humidity = mysqli_real_escape_string($conn, $_POST['humidity']);

    mysqli_query($conn, "INSERT INTO vegetables (name, min_temp, max_temp, humidity)
                         VALUES ('$name','$min','$max','$humidity')");
    $message = "Vegetable profile added.";
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM vegetables WHERE id=$id");
    header("Location: admin_vegetables.php");
    exit();
}

if (isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $min = mysqli_real_escape_string($conn, $_POST['min_temp']);
    $max = mysqli_real_escape_string($conn, $_POST['max_temp']);
    $humidity = mysqli_real_escape_string($conn, $_POST['humidity']);

    mysqli_query($conn, "UPDATE vegetables
        SET name='$name', min_temp='$min', max_temp='$max', humidity='$humidity'
        WHERE id=$id");
    $message = "Vegetable profile updated.";
}

$result = mysqli_query($conn, "SELECT * FROM vegetables ORDER BY name ASC");
$admin_name = htmlspecialchars($_SESSION['admin_name'] ?? 'Admin', ENT_QUOTES, 'UTF-8');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Vegetables</title>
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
                <a class="active" href="admin_vegetables.php"><span class="nav-icon">02</span>Vegetables</a>
                <a href="users.php"><span class="nav-icon">03</span>Users</a>
                <a href="admin_records.php"><span class="nav-icon">04</span>Records</a>
                <a href="admin_reports.php"><span class="nav-icon">05</span>Reports</a>
                <a href="admin_logout.php"><span class="nav-icon">06</span>Logout</a>
            </nav>
        </aside>

        <main class="main">
            <section class="page-hero">
                <div>
                    <p class="eyebrow">Admin produce setup</p>
                    <h2>Manage vegetable safe ranges.</h2>
                    <p>Add, edit, or remove produce profiles used by the temperature classification system.</p>
                </div>
                <div class="actions">
                    <a class="btn btn-muted" href="admin_records.php">View Records</a>
                </div>
            </section>

            <?php if ($message !== "") { ?>
                <div class="message success"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php } ?>

            <section class="grid-two">
                <article class="form-card">
                    <h3>Add Vegetable</h3>
                    <form method="POST">
                        <div class="form-grid">
                            <div class="field">
                                <label for="name">Vegetable name</label>
                                <input id="name" type="text" name="name" placeholder="Example: Tomato" required>
                            </div>
                            <div class="field">
                                <label for="humidity">Humidity target</label>
                                <input id="humidity" type="number" name="humidity" placeholder="Example: 85" required>
                            </div>
                            <div class="field">
                                <label for="min_temp">Minimum temperature</label>
                                <input id="min_temp" type="number" step="0.1" name="min_temp" placeholder="Example: 4" required>
                            </div>
                            <div class="field">
                                <label for="max_temp">Maximum temperature</label>
                                <input id="max_temp" type="number" step="0.1" name="max_temp" placeholder="Example: 10" required>
                            </div>
                        </div>
                        <button style="margin-top:18px;" name="add">Add Vegetable</button>
                    </form>
                </article>

                <article class="panel">
                    <div class="panel-header">
                        <h3>Configured Profiles</h3>
                        <span class="badge badge-active"><?php echo $result ? mysqli_num_rows($result) : 0; ?> items</span>
                    </div>

                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Min Temp</th>
                                    <th>Max Temp</th>
                                    <th>Humidity</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result && mysqli_num_rows($result) > 0) { ?>
                                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                                        <tr>
                                            <form method="POST">
                                                <td><input type="text" name="name" value="<?php echo htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'); ?>" required></td>
                                                <td><input type="number" step="0.1" name="min_temp" value="<?php echo htmlspecialchars($row['min_temp'], ENT_QUOTES, 'UTF-8'); ?>" required></td>
                                                <td><input type="number" step="0.1" name="max_temp" value="<?php echo htmlspecialchars($row['max_temp'], ENT_QUOTES, 'UTF-8'); ?>" required></td>
                                                <td><input type="number" name="humidity" value="<?php echo htmlspecialchars($row['humidity'], ENT_QUOTES, 'UTF-8'); ?>" required></td>
                                                <td>
                                                    <input type="hidden" name="id" value="<?php echo (int) $row['id']; ?>">
                                                    <button name="update">Update</button>
                                                    <a class="btn btn-danger" href="?delete=<?php echo (int) $row['id']; ?>" onclick="return confirm('Delete this vegetable profile?')">Delete</a>
                                                </td>
                                            </form>
                                        </tr>
                                    <?php } ?>
                                <?php } else { ?>
                                    <tr><td colspan="5">No vegetable profiles have been added yet.</td></tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </article>
            </section>
        </main>
    </div>
</body>
</html>
