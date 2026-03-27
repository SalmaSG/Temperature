<?php
session_start();
include "db.php";

// Login check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 🌽 Total Vegetables
$veg_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM vegetables"))['total'];

// 🌡️ Today's Records
$today_records = mysqli_fetch_assoc(mysqli_query($conn, 
    "SELECT COUNT(*) as total FROM records WHERE DATE(created_at) = CURDATE()"
))['total'];

// ⚠️ Alerts
$alerts = mysqli_fetch_assoc(mysqli_query($conn, 
    "SELECT COUNT(*) as total FROM records WHERE status != 'Normal'"
))['total'];

// 👥 Users
$users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users"))['total'];

// Chart data
$normal = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM records WHERE status='Normal'"))['c'];
$high = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM records WHERE status='High'"))['c'];
$low = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM records WHERE status='Low'"))['c'];

// Latest alert
$latest_alert = mysqli_fetch_assoc(mysqli_query($conn, 
    "SELECT * FROM records WHERE status!='Normal' ORDER BY id DESC LIMIT 1"
));

// Card color logic

$latest = mysqli_fetch_assoc(mysqli_query($conn, "SELECT status FROM records ORDER BY id DESC LIMIT 1"));

if ($latest) {
    if ($latest['status'] == 'Normal') {
        $alertColor = "#28a745"; // GREEN
    } elseif ($latest['status'] == 'Low') {
        $alertColor = "#ffc107"; // YELLOW
    } else {
        $alertColor = "#dc3545"; // RED
    }
} else {
    $alertColor = "#36d1dc"; // default
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            display: flex;
            background: #f4f6f9;
        }

        .sidebar {
            width: 220px;
            height: 100vh;
            background: linear-gradient(180deg, #667eea, #764ba2);
            color: white;
            padding-top: 20px;
            position: fixed;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        .sidebar a {
            display: block;
            padding: 15px 20px;
            color: white;
            text-decoration: none;
        }

        .sidebar a:hover {
            background: rgba(255,255,255,0.2);
        }

        .main {
            margin-left: 220px;
            padding: 20px;
            width: 100%;
        }

        .topbar {
            background: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .card {
            padding: 20px;
            border-radius: 15px;
            color: white;
            font-weight: bold;
        }

        .card1 { background: #ff7e5f; }
        .card2 { background: #43cea2; }
        .card4 { background: #ff4b2b; }

        #alertBox {
            position: fixed;
            top: 20px;
            right: 20px;
            background: red;
            color: white;
            padding: 15px;
            border-radius: 10px;
        }
    </style>
</head>

<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2>TempMonitor</h2>

    <a href="index.php">🏠 Dashboard</a>
    <a href="temperature.php">🌡️ Enter Temperature</a>
    <a href="vegetables.php">🥦 Vegetables</a>
    <a href="alerts.php">🚨 Alerts</a>
    <a href="reports.php">📊 Reports</a>
    <a href="logout.php">🚪 Logout</a>
</div>

<!-- Main -->
<div class="main">

    <div class="topbar">
        Welcome, <?php echo $_SESSION['username']; ?> 👋
    </div>

    <div class="cards">

        <div class="card card1">
            🌽 Total Vegetables <br><br>
            <?php echo $veg_count; ?>
        </div>

        <div class="card card2">
            🌡️ Today's Records <br><br>
            <?php echo $today_records; ?>
        </div>

        <!-- ALERT CARD -->
        <div class="card" style="background: <?php echo $alertColor; ?>;">
            ⚠️ Alerts <br><br>
            <?php echo $alerts; ?>
        </div>

        <div class="card card4">
            👥 Users <br><br>
            <?php echo $users; ?>
        </div>

    </div>

    <br>

    <!-- Chart -->
    <canvas id="myChart"></canvas>

</div>

<!-- ALERT POPUP -->
<?php if($latest_alert){ ?>
<div id="alertBox">
    ⚠️ Alert! Temperature is <?php echo $latest_alert['status']; ?>
</div>
<?php } ?>

<script>
var ctx = document.getElementById('myChart');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Normal', 'High', 'Low'],
        datasets: [{
            label: 'Temperature Status',
            data: [<?php echo $normal; ?>, <?php echo $high; ?>, <?php echo $low; ?>]
        }]
    }
});

// Auto hide alert
setTimeout(() => {
    let box = document.getElementById("alertBox");
    if(box) box.style.display = "none";
}, 4000);
</script>

</body>
</html>