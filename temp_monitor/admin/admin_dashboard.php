<?php
include("admin_check.php");
include("../db.php");

$veg = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM vegetables"))['c'];
$users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM users"))['c'];
$records = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM records"))['c'];
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard</title>

<style>
body {
    margin:0;
    font-family:'Segoe UI';
    display:flex;
    background:#f4f6f9;
}

/* Sidebar */
.sidebar {
    width:220px;
    height:100vh;
    background:linear-gradient(180deg,#141e30,#243b55);
    color:white;
    padding-top:20px;
    position:fixed;
}

.sidebar h2 {
    text-align:center;
}

.sidebar a {
    display:block;
    padding:15px;
    color:white;
    text-decoration:none;
}

.sidebar a:hover {
    background:rgba(255,255,255,0.2);
}

/* Main */
.main {
    margin-left:220px;
    padding:20px;
    width:100%;
}

.topbar {
    background:white;
    padding:15px;
    border-radius:10px;
    margin-bottom:20px;
}

.cards {
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
    gap:20px;
}

.card {
    padding:20px;
    border-radius:15px;
    color:white;
    font-weight:bold;
    box-shadow:0 10px 20px rgba(0,0,0,0.2);
}

.card1 { background:#ff7e5f; }
.card2 { background:#43cea2; }
.card3 { background:#36d1dc; }
</style>
</head>

<body>

<div class="sidebar">
    <h2>Admin Panel</h2>

    <a href="admin_dashboard.php">🏠 Dashboard</a>
    <a href="../vegetables.php">🥦 Manage Vegetables</a>
    <a href="users.php">👥 Manage Users</a>
    <a href="../reports.php">📊 Reports</a>
    <a href="admin_logout.php">🚪 Logout</a>
</div>

<div class="main">

<div class="topbar">
    Welcome Admin 👨‍💼 (<?php echo $_SESSION['admin_name']; ?>)
</div>

<div class="cards">

    <div class="card card1">
        🌽 Vegetables <br><br>
        <?php echo $veg; ?>
    </div>

    <div class="card card2">
        👥 Users <br><br>
        <?php echo $users; ?>
    </div>

    <div class="card card3">
        🌡️ Records <br><br>
        <?php echo $records; ?>
    </div>

</div>

</div>

</body>
</html>