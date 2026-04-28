<?php
include("admin_check.php");
include("../db.php");

$column_check = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'is_active'");
if ($column_check && mysqli_num_rows($column_check) === 0) {
    mysqli_query($conn, "ALTER TABLE users ADD COLUMN is_active TINYINT DEFAULT 1");
}

$message = "";

if (isset($_POST['add_user'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if ($username === "" || $email === "" || $password === "") {
        $message = "All fields are required to add a user.";
    } else {
        $safe_username = mysqli_real_escape_string($conn, $username);
        $safe_email = mysqli_real_escape_string($conn, $email);
        $check = mysqli_query($conn, "SELECT id FROM users WHERE email='$safe_email' OR username='$safe_username' LIMIT 1");

        if ($check && mysqli_num_rows($check) > 0) {
            $message = "Username or email already exists.";
        } else {
            $hashed = mysqli_real_escape_string($conn, password_hash($password, PASSWORD_DEFAULT));
            mysqli_query($conn, "INSERT INTO users (username, email, password, is_active) VALUES ('$safe_username', '$safe_email', '$hashed', 1)");
            $message = "User created successfully.";
        }
    }
}

if (isset($_GET['toggle'])) {
    $id = intval($_GET['toggle']);
    $current = intval($_GET['state'] ?? 1);
    $next = $current === 1 ? 0 : 1;
    mysqli_query($conn, "UPDATE users SET is_active=$next WHERE id=$id");
    header("Location: users.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM users WHERE id=$id");
    header("Location: users.php");
    exit();
}

$result = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");
$admin_name = htmlspecialchars($_SESSION['admin_name'] ?? 'Admin', ENT_QUOTES, 'UTF-8');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
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
                <a class="active" href="users.php"><span class="nav-icon">03</span>Users</a>
                <a href="admin_records.php"><span class="nav-icon">04</span>Records</a>
                <a href="admin_reports.php"><span class="nav-icon">05</span>Reports</a>
                <a href="admin_logout.php"><span class="nav-icon">06</span>Logout</a>
            </nav>
        </aside>

        <main class="main">
            <section class="page-hero">
                <div>
                    <p class="eyebrow">Access management</p>
                    <h2>Manage registered users.</h2>
                    <p>Create users, disable access without deleting history, and remove accounts when needed.</p>
                </div>
                <div class="actions">
                    <a class="btn btn-muted" href="admin_dashboard.php">Admin Dashboard</a>
                </div>
            </section>

            <?php if ($message !== "") { ?>
                <div class="message success"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php } ?>

            <section class="grid-two">
                <article class="form-card">
                    <h3>Add New User</h3>
                    <form method="POST">
                        <div class="field">
                            <label for="username">Username</label>
                            <input id="username" type="text" name="username" required>
                        </div>
                        <div class="field" style="margin-top:14px;">
                            <label for="email">Email</label>
                            <input id="email" type="email" name="email" required>
                        </div>
                        <div class="field" style="margin-top:14px;">
                            <label for="password">Temporary password</label>
                            <input id="password" type="password" name="password" required>
                        </div>
                        <button style="margin-top:18px;" name="add_user">Create User</button>
                    </form>
                </article>

                <article class="panel">
                    <div class="panel-header">
                        <h3>Users</h3>
                        <span class="badge badge-active"><?php echo $result ? mysqli_num_rows($result) : 0; ?> users</span>
                    </div>

                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result && mysqli_num_rows($result) > 0) { ?>
                                    <?php while ($row = mysqli_fetch_assoc($result)) {
                                        $active = isset($row['is_active']) ? (int) $row['is_active'] : 1;
                                    ?>
                                        <tr>
                                            <td><?php echo (int) $row['id']; ?></td>
                                            <td><?php echo htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?php echo htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><span class="badge <?php echo $active ? 'badge-active' : 'badge-danger'; ?>"><?php echo $active ? 'Active' : 'Disabled'; ?></span></td>
                                            <td><?php echo htmlspecialchars($row['created_at'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td>
                                                <a class="btn btn-muted" href="?toggle=<?php echo (int) $row['id']; ?>&state=<?php echo $active; ?>"><?php echo $active ? 'Disable' : 'Enable'; ?></a>
                                                <a class="btn btn-danger" href="?delete=<?php echo (int) $row['id']; ?>" onclick="return confirm('Delete this user?')">Delete</a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                <?php } else { ?>
                                    <tr><td colspan="6">No users found.</td></tr>
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
