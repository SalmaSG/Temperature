<?php
session_start();
include("db.php");

$message = "";

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if ($email == "" || $password == "") {
        $message = "All fields are required.";
    } else {
        $query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

        if (mysqli_num_rows($query) > 0) {
            $user = mysqli_fetch_assoc($query);

            if (isset($user['is_active']) && (int) $user['is_active'] === 0) {
                $message = "Your account is disabled. Please contact the administrator.";
            } elseif (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];

                header("Location: index.php");
                exit();
            } else {
                $message = "Invalid password.";
            }
        } else {
            $message = "User not found.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="auth-page">
        <section class="auth-card">
            <p class="eyebrow">Secure access</p>
            <h2>Welcome back.</h2>
            <p>Sign in to monitor temperature health, alerts, and produce storage reports.</p>

            <?php if ($message !== "") { ?>
                <div class="message"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php } ?>

            <form method="POST">
                <div class="field">
                    <label for="email">Email address</label>
                    <input id="email" type="email" name="email" placeholder="you@example.com" required>
                </div>

                <div class="field" style="margin-top:14px;">
                    <label for="password">Password</label>
                    <input id="password" type="password" name="password" placeholder="Enter your password" required>
                </div>

                <button style="width:100%; margin-top:18px;" type="submit" name="login">Login</button>
            </form>

            <div class="auth-link">
                <a href="forgot_password.php">Forgot password?</a>
            </div>

            <div class="auth-link">
                Need an account? <a href="register.php">Create one</a>
            </div>
        </section>
    </main>
</body>
</html>
