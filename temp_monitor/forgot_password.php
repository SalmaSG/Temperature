<?php
session_start();
include("db.php");

$message = "";
$success = false;

if (isset($_POST['reset'])) {
    $email = trim($_POST['email']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($email === "" || $new_password === "" || $confirm_password === "") {
        $message = "All fields are required.";
    } elseif ($new_password !== $confirm_password) {
        $message = "Passwords do not match.";
    } elseif (strlen($new_password) < 6) {
        $message = "Password must be at least 6 characters.";
    } else {
        $safe_email = mysqli_real_escape_string($conn, $email);
        $query = mysqli_query($conn, "SELECT id FROM users WHERE email='$safe_email' LIMIT 1");

        if ($query && mysqli_num_rows($query) > 0) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $safe_password = mysqli_real_escape_string($conn, $hashed_password);

            mysqli_query($conn, "UPDATE users SET password='$safe_password' WHERE email='$safe_email'");
            $message = "Password reset successfully. You can login now.";
            $success = true;
        } else {
            $message = "No user account was found with that email.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="auth-page">
        <section class="auth-card">
            <p class="eyebrow">User recovery</p>
            <h2>Reset your password.</h2>
            <p>Enter your registered email address and choose a new password for your TempMonitor account.</p>

            <?php if ($message !== "") { ?>
                <div class="message <?php echo $success ? 'success' : ''; ?>"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php } ?>

            <form method="POST">
                <div class="field">
                    <label for="email">Registered email</label>
                    <input id="email" type="email" name="email" placeholder="you@example.com" required>
                </div>

                <div class="field" style="margin-top:14px;">
                    <label for="new_password">New password</label>
                    <input id="new_password" type="password" name="new_password" placeholder="New password" required>
                </div>

                <div class="field" style="margin-top:14px;">
                    <label for="confirm_password">Confirm password</label>
                    <input id="confirm_password" type="password" name="confirm_password" placeholder="Confirm password" required>
                </div>

                <button style="width:100%; margin-top:18px;" type="submit" name="reset">Reset Password</button>
            </form>

            <div class="auth-link">
                Remembered it? <a href="login.php">Back to login</a>
            </div>
        </section>
    </main>
</body>
</html>
