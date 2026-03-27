<?php
session_start();
include("db.php");

$message = "";

if (isset($_POST['login'])) {

    $email = $_POST['email'];
    $password = $_POST['password'];

    if ($email == "" || $password == "") {
        $message = "All fields are required!";
    } else {

        $query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

        if (mysqli_num_rows($query) > 0) {

            $user = mysqli_fetch_assoc($query);

            if (password_verify($password, $user['password'])) {

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];

                header("Location: index.php");
                exit();

            } else {
                $message = "Invalid password!";
            }

        } else {
            $message = "User not found!";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>

    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #36d1dc, #5b86e5);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
        }

        .card {
            background: white;
            padding: 40px;
            width: 350px;
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.3);
            animation: slide 0.5s ease;
        }

        @keyframes slide {
            from {opacity: 0; transform: translateY(30px);}
            to {opacity: 1; transform: translateY(0);}
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .msg {
            text-align: center;
            color: red;
            margin-bottom: 15px;
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
            border-color: #5b86e5;
            box-shadow: 0 0 5px #5b86e5;
        }

        button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #36d1dc, #5b86e5);
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
            color: #5b86e5;
            font-weight: bold;
        }
    </style>
</head>

<body>

<div class="card">
    <h2>Login</h2>

    <div class="msg"><?php echo $message; ?></div>

    <form method="POST">
        <input type="email" name="email" placeholder="Enter Email">
        <input type="password" name="password" placeholder="Enter Password">

        <button type="submit" name="login">Login</button>
    </form>

    <div class="link">
        <a href="register.php">Create new account</a>
    </div>
</div>

</body>
</html>