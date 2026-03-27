<?php
session_start();
include("db.php");

// Generate CAPTCHA
if (!isset($_SESSION['captcha'])) {
    $_SESSION['captcha'] = rand(1000, 9999);
}

$message = "";

if (isset($_POST['register'])) {

    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $captcha_input = $_POST['captcha'];

    // CAPTCHA check
    if ($captcha_input != $_SESSION['captcha']) {
        $message = "❌ Invalid CAPTCHA!";
    } 
    else if ($username == "" || $email == "" || $password == "") {
        $message = "All fields are required!";
    } 
    else {
        $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
        
        if (mysqli_num_rows($check) > 0) {
            $message = "Email already exists!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $insert = mysqli_query($conn, 
                "INSERT INTO users (username, email, password) 
                 VALUES ('$username', '$email', '$hashed_password')"
            );

            if ($insert) {
                $message = "✅ Registration successful!";
                unset($_SESSION['captcha']); // regenerate next time
            } else {
                $message = "❌ Error occurred!";
            }
        }
    }

    // regenerate captcha after each submit
    $_SESSION['captcha'] = rand(1000, 9999);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>

    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #667eea, #764ba2);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }

        .card {
            background: white;
            padding: 40px;
            width: 350px;
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.3);
            animation: fadeIn 0.6s ease;
        }

        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(20px);}
            to {opacity: 1; transform: translateY(0);}
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .msg {
            text-align: center;
            margin-bottom: 15px;
            color: red;
            font-size: 14px;
        }

        input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 10px;
            border: 1px solid #ccc;
            outline: none;
            transition: 0.3s;
        }

        input:focus {
            border-color: #667eea;
            box-shadow: 0 0 5px #667eea;
        }

        button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            transform: scale(1.05);
        }

        .link {
            text-align: center;
            margin-top: 15px;
        }

        .link a {
            text-decoration: none;
            color: #667eea;
            font-weight: bold;
        }
    </style>
</head>

<body>

<div class="card">
    <h2>Create Account</h2>

    <div class="msg"><?php echo $message; ?></div>

    <form method="POST">
        <input type="text" name="username" placeholder="Username">
        <input type="email" name="email" placeholder="Email">
        <input type="password" name="password" placeholder="Password">
<div style="margin-top:10px;">
    <label>Enter CAPTCHA</label><br>

    <div style="display:flex; align-items:center; gap:10px;">
        <div style="
            background:#eee;
            padding:10px 15px;
            font-weight:bold;
            letter-spacing:3px;
            border-radius:8px;">
            <?php echo $_SESSION['captcha']; ?>
        </div>

        <input type="text" name="captcha" placeholder="Enter code" required>
    </div>
</div>
        <button type="submit" name="register">Register</button>
    </form>

    <div class="link">
        <a href="login.php">Already have account? Login</a>
    </div>
</div>

</body>
</html>