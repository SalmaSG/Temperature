<?php
session_start();
include("../db.php");

$message = "";
$success = false;

if (isset($_POST['reset'])) {
    $username = trim($_POST['username']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($username === "" || $new_password === "" || $confirm_password === "") {
        $message = "All fields are required.";
    } elseif ($new_password !== $confirm_password) {
        $message = "Passwords do not match.";
    } elseif (strlen($new_password) < 6) {
        $message = "Password must be at least 6 characters.";
    } else {
        $safe_username = mysqli_real_escape_string($conn, $username);
        $query = mysqli_query($conn, "SELECT * FROM admins WHERE username='$safe_username' LIMIT 1");

        if ($query && mysqli_num_rows($query) > 0) {
            $hashed_password = md5($new_password);
            mysqli_query($conn, "UPDATE admins SET password='$hashed_password' WHERE username='$safe_username'");
            $message = "Admin password reset successfully. You can login now.";
            $success = true;
        } else {
            $message = "No admin account was found with that username.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Forgot Password</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <main class="auth-page">
        <section class="auth-card">
            <p class="eyebrow">Admin recovery</p>
            <h2>Reset admin password.</h2>
            <p>Enter the admin username and choose a new password for the control console.</p>

            <?php if ($message !== "") { ?>
                <div class="message <?php echo $success ? 'success' : ''; ?>"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php } ?>

            <form method="POST">
                <div class="field">
                    <label for="username">Admin username</label>
                    <input id="username" type="text" name="username" placeholder="Admin username" required>
                </div>

                <div class="field" style="margin-top:14px;">
                    <label for="new_password">New password</label>
                    <input id="new_password" type="password" name="new_password" placeholder="New password" required>
                </div>

                <div class="field" style="margin-top:14px;">
                    <label for="confirm_password">Confirm password</label>
                    <input id="confirm_password" type="password" name="confirm_password" placeholder="Confirm password" required>
                </div>

                <button style="width:100%; margin-top:18px;" type="submit" name="reset">Reset Admin Password</button>
            </form>

            <div class="auth-link">
                Remembered it? <a href="admin_login.php">Back to admin login</a>
            </div>
        </section>
    </main>
</body>
</html>
