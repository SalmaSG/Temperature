<?php
session_start();
include("db.php");

if (!isset($_SESSION['captcha'])) {
    $_SESSION['captcha'] = rand(1000, 9999);
}

$message = "";
$success = false;

if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $captcha_input = $_POST['captcha'];

    if ($captcha_input != $_SESSION['captcha']) {
        $message = "Invalid CAPTCHA code.";
    } elseif ($username == "" || $email == "" || $password == "") {
        $message = "All fields are required.";
    } else {
        $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

        if (mysqli_num_rows($check) > 0) {
            $message = "Email already exists.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $insert = mysqli_query($conn,
                "INSERT INTO users (username, email, password)
                 VALUES ('$username', '$email', '$hashed_password')"
            );

            if ($insert) {
                $message = "Registration successful. You can now login.";
                $success = true;
                unset($_SESSION['captcha']);
            } else {
                $message = "An error occurred while creating your account.";
            }
        }
    }

    $_SESSION['captcha'] = rand(1000, 9999);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="auth-page">
        <section class="auth-card">
            <p class="eyebrow">Create workspace</p>
            <h2>Start monitoring.</h2>
            <p>Create an account to manage produce profiles, readings, reports, and temperature alerts.</p>

            <?php if ($message !== "") { ?>
                <div class="message <?php echo $success ? 'success' : ''; ?>"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php } ?>

            <form method="POST">
                <div class="field">
                    <label for="username">Username</label>
                    <input id="username" type="text" name="username" placeholder="Your name" required>
                </div>

                <div class="field" style="margin-top:14px;">
                    <label for="email">Email address</label>
                    <input id="email" type="email" name="email" placeholder="you@example.com" required>
                </div>

                <div class="field" style="margin-top:14px;">
                    <label for="password">Password</label>
                    <input id="password" type="password" name="password" placeholder="Choose a strong password" required>
                </div>

                <div class="field" style="margin-top:14px;">
                    <label for="captcha">CAPTCHA</label>
                    <div class="captcha-row">
                        <div class="captcha-code"><?php echo htmlspecialchars((string) $_SESSION['captcha'], ENT_QUOTES, 'UTF-8'); ?></div>
                        <input id="captcha" type="text" name="captcha" placeholder="Enter code" required>
                    </div>
                </div>

                <button style="width:100%; margin-top:18px;" type="submit" name="register">Create Account</button>
            </form>

            <div class="auth-link">
                Already have an account? <a href="login.php">Login</a>
            </div>
        </section>
    </main>
</body>
</html>
