<?php
session_start();
include("../db.php");

$message = "";

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = md5($_POST['password']);

    $result = mysqli_query($conn, "SELECT * FROM admins WHERE username='$username' AND password='$password'");

    if (mysqli_num_rows($result) > 0) {
        $_SESSION['admin_id'] = 1;
        $_SESSION['admin_name'] = $username;

        header("Location: admin_dashboard.php");
        exit();
    } else {
        $message = "Invalid admin login.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <main class="auth-page">
        <section class="auth-card">
            <p class="eyebrow">Admin console</p>
            <h2>Control center access.</h2>
            <p>Sign in to manage users, produce profiles, records, and operational reports.</p>

            <?php if ($message !== "") { ?>
                <div class="message"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php } ?>

            <form method="POST">
                <div class="field">
                    <label for="username">Username</label>
                    <input id="username" type="text" name="username" placeholder="Admin username" required>
                </div>

                <div class="field" style="margin-top:14px;">
                    <label for="password">Password</label>
                    <input id="password" type="password" name="password" placeholder="Admin password" required>
                </div>

                <button style="width:100%; margin-top:18px;" name="login">Login</button>
            </form>

            <div class="auth-link">
                <a href="admin_forgot_password.php">Forgot admin password?</a>
            </div>
        </section>
    </main>
</body>
</html>
