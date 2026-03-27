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
        $message = "Invalid login!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Login</title>

<style>
body {
    font-family: 'Segoe UI';
    background: linear-gradient(135deg, #141e30, #243b55);
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
}

.card {
    background:white;
    padding:40px;
    border-radius:15px;
    width:320px;
    text-align:center;
    box-shadow:0 10px 30px rgba(0,0,0,0.3);
}

input {
    width:100%;
    padding:10px;
    margin:10px 0;
    border-radius:8px;
    border:1px solid #ccc;
}

button {
    width:100%;
    padding:10px;
    background:#141e30;
    color:white;
    border:none;
    border-radius:8px;
    cursor:pointer;
}

.msg { color:red; }
</style>
</head>

<body>

<div class="card">
    <h2>Admin Login 👨‍💼</h2>

    <div class="msg"><?php echo $message; ?></div>

    <form method="POST">
        <input type="text" name="username" placeholder="Username">
        <input type="password" name="password" placeholder="Password">
        <button name="login">Login</button>
    </form>
</div>

</body>
</html>