<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: home.php");
    exit();
}

function scalar_query($conn, $sql, $field, $default = 0) {
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        return $default;
    }

    $row = mysqli_fetch_assoc($result);
    return $row && $row[$field] !== null ? $row[$field] : $default;
}

$username = htmlspecialchars($_SESSION['username'] ?? 'User', ENT_QUOTES, 'UTF-8');

$veg_count = (int) scalar_query($conn, "SELECT COUNT(*) as total FROM vegetables", "total");
$today_records = (int) scalar_query($conn, "SELECT COUNT(*) as total FROM records WHERE DATE(created_at) = CURDATE()", "total");
$alerts = (int) scalar_query($conn, "SELECT COUNT(*) as total FROM records WHERE status != 'Normal'", "total");
$users = (int) scalar_query($conn, "SELECT COUNT(*) as total FROM users", "total");

$normal = (int) scalar_query($conn, "SELECT COUNT(*) as c FROM records WHERE status='Normal'", "c");
$high = (int) scalar_query($conn, "SELECT COUNT(*) as c FROM records WHERE status='High'", "c");
$low = (int) scalar_query($conn, "SELECT COUNT(*) as c FROM records WHERE status='Low'", "c");
$avg_temp = (float) scalar_query($conn, "SELECT ROUND(AVG(temperature), 1) as avg_temp FROM records", "avg_temp", 0);
$latest_temp = scalar_query($conn, "SELECT temperature FROM records ORDER BY id DESC LIMIT 1", "temperature", "--");

$latest_alert = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT r.*, v.name
    FROM records r
    LEFT JOIN vegetables v ON r.veg_id = v.id
    WHERE r.status != 'Normal'
    ORDER BY r.id DESC
    LIMIT 1
"));

$latest = mysqli_fetch_assoc(mysqli_query($conn, "SELECT status FROM records ORDER BY id DESC LIMIT 1"));
$latest_status = $latest['status'] ?? 'No Data';

$statusClass = "status-idle";
if ($latest_status === "Normal") {
    $statusClass = "status-normal";
} elseif ($latest_status === "Low") {
    $statusClass = "status-low";
} elseif ($latest_status === "High") {
    $statusClass = "status-high";
}

$recent_records = mysqli_query($conn, "
    SELECT r.*, v.name
    FROM records r
    LEFT JOIN vegetables v ON r.veg_id = v.id
    ORDER BY r.id DESC
    LIMIT 6
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TempMonitor Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --bg: #07111f;
            --panel: rgba(255, 255, 255, 0.09);
            --panel-strong: rgba(255, 255, 255, 0.14);
            --line: rgba(255, 255, 255, 0.16);
            --text: #eef6ff;
            --muted: #9fb1c7;
            --cyan: #2de2e6;
            --green: #33d17a;
            --yellow: #ffd166;
            --red: #ff5c7a;
            --violet: #8f7cff;
            --shadow: 0 24px 70px rgba(0, 0, 0, 0.35);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            color: var(--text);
            font-family: Inter, "Segoe UI", Arial, sans-serif;
            background:
                radial-gradient(circle at 12% 18%, rgba(45, 226, 230, 0.18), transparent 30%),
                radial-gradient(circle at 85% 8%, rgba(143, 124, 255, 0.22), transparent 32%),
                linear-gradient(135deg, #06101e 0%, #10152b 52%, #041a23 100%);
            overflow-x: hidden;
        }

        body::before,
        body::after {
            content: "";
            position: fixed;
            inset: auto;
            width: 420px;
            height: 420px;
            border-radius: 999px;
            filter: blur(18px);
            opacity: 0.22;
            pointer-events: none;
            animation: drift 13s ease-in-out infinite alternate;
            z-index: 0;
        }

        body::before {
            left: -120px;
            bottom: 6%;
            background: conic-gradient(from 90deg, var(--cyan), transparent, var(--green), transparent);
        }

        body::after {
            right: -140px;
            top: 18%;
            background: conic-gradient(from 180deg, var(--violet), transparent, var(--red), transparent);
            animation-delay: -5s;
        }

        .dashboard {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: 280px minmax(0, 1fr);
            min-height: 100vh;
        }

        .sidebar {
            position: sticky;
            top: 0;
            height: 100vh;
            padding: 28px 18px;
            border-right: 1px solid var(--line);
            background: rgba(5, 13, 27, 0.72);
            backdrop-filter: blur(22px);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px 28px;
        }

        .brand-mark {
            width: 44px;
            height: 44px;
            display: grid;
            place-items: center;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--cyan), var(--green));
            box-shadow: 0 14px 32px rgba(45, 226, 230, 0.26);
            color: #04101a;
            font-weight: 900;
        }

        .brand h1 {
            margin: 0;
            font-size: 22px;
            letter-spacing: 0;
        }

        .brand span {
            display: block;
            margin-top: 3px;
            color: var(--muted);
            font-size: 12px;
            font-weight: 600;
        }

        .nav {
            display: grid;
            gap: 8px;
        }

        .nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            min-height: 46px;
            padding: 12px 14px;
            color: #dbe9f9;
            text-decoration: none;
            border: 1px solid transparent;
            border-radius: 8px;
            font-weight: 700;
            transition: transform 0.24s ease, background 0.24s ease, border-color 0.24s ease;
        }

        .nav a:hover,
        .nav a.active {
            transform: translateX(5px);
            border-color: rgba(45, 226, 230, 0.28);
            background: rgba(45, 226, 230, 0.1);
        }

        .nav-icon {
            width: 28px;
            height: 28px;
            display: grid;
            place-items: center;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.1);
            color: var(--cyan);
            font-size: 13px;
        }

        .sidebar-footer {
            position: absolute;
            left: 18px;
            right: 18px;
            bottom: 22px;
            padding: 16px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.07);
        }

        .sidebar-footer small {
            display: block;
            color: var(--muted);
            line-height: 1.5;
        }

        .main {
            min-width: 0;
            padding: 28px;
        }

        .hero {
            position: relative;
            overflow: hidden;
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 24px;
            align-items: center;
            min-height: 190px;
            padding: 30px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background:
                linear-gradient(120deg, rgba(255, 255, 255, 0.14), rgba(255, 255, 255, 0.05)),
                linear-gradient(135deg, rgba(45, 226, 230, 0.12), rgba(143, 124, 255, 0.13));
            box-shadow: var(--shadow);
            animation: riseIn 0.7s ease both;
        }

        .hero::after {
            content: "";
            position: absolute;
            inset: -80px -160px auto auto;
            width: 360px;
            height: 360px;
            background: radial-gradient(circle, rgba(45, 226, 230, 0.22), transparent 62%);
            animation: pulseGlow 3.8s ease-in-out infinite;
        }

        .eyebrow {
            margin: 0 0 10px;
            color: var(--cyan);
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .hero h2 {
            margin: 0;
            max-width: 780px;
            font-size: clamp(30px, 5vw, 58px);
            line-height: 1.02;
            letter-spacing: 0;
        }

        .hero p {
            max-width: 660px;
            margin: 16px 0 0;
            color: #c1d1e4;
            font-size: 16px;
            line-height: 1.7;
        }

        .health-ring {
            position: relative;
            width: 160px;
            height: 160px;
            display: grid;
            place-items: center;
            border-radius: 50%;
            background: conic-gradient(var(--green) 0 68%, rgba(255,255,255,0.12) 68% 100%);
            animation: rotateIn 1s ease both;
        }

        .health-ring::before {
            content: "";
            position: absolute;
            inset: 13px;
            border-radius: 50%;
            background: #081421;
            border: 1px solid var(--line);
        }

        .health-ring strong,
        .health-ring span {
            position: relative;
            z-index: 1;
        }

        .health-ring strong {
            font-size: 34px;
        }

        .health-ring span {
            display: block;
            color: var(--muted);
            font-size: 12px;
            font-weight: 800;
            text-align: center;
            text-transform: uppercase;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
            margin: 20px 0;
        }

        .card {
            position: relative;
            overflow: hidden;
            min-height: 150px;
            padding: 20px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: var(--panel);
            box-shadow: 0 16px 50px rgba(0, 0, 0, 0.18);
            backdrop-filter: blur(18px);
            animation: riseIn 0.7s ease both;
            transition: transform 0.25s ease, border-color 0.25s ease, background 0.25s ease;
        }

        .card:nth-child(2) { animation-delay: 0.08s; }
        .card:nth-child(3) { animation-delay: 0.16s; }
        .card:nth-child(4) { animation-delay: 0.24s; }

        .card::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(120deg, transparent, rgba(255,255,255,0.16), transparent);
            transform: translateX(-120%);
            transition: transform 0.65s ease;
        }

        .card:hover {
            transform: translateY(-7px);
            border-color: rgba(45, 226, 230, 0.38);
            background: var(--panel-strong);
        }

        .card:hover::before {
            transform: translateX(120%);
        }

        .card-label {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            color: var(--muted);
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .spark {
            width: 38px;
            height: 38px;
            border-radius: 8px;
            display: grid;
            place-items: center;
            color: #04101a;
            font-weight: 900;
            background: linear-gradient(135deg, var(--cyan), var(--green));
        }

        .card-value {
            margin-top: 20px;
            font-size: clamp(34px, 4vw, 48px);
            line-height: 1;
            font-weight: 900;
        }

        .card-note {
            margin-top: 12px;
            color: #b7c9df;
            font-size: 13px;
            line-height: 1.5;
        }

        .status-normal .spark { background: linear-gradient(135deg, #33d17a, #b7f7d2); }
        .status-low .spark { background: linear-gradient(135deg, #ffd166, #fff2b2); }
        .status-high .spark { background: linear-gradient(135deg, #ff5c7a, #ffb0be); }
        .status-idle .spark { background: linear-gradient(135deg, #9fb1c7, #eef6ff); }

        .content-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.45fr) minmax(320px, 0.8fr);
            gap: 20px;
            align-items: start;
        }

        .panel {
            border: 1px solid var(--line);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.08);
            box-shadow: var(--shadow);
            backdrop-filter: blur(18px);
            animation: riseIn 0.75s ease both;
        }

        .panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 20px 20px 0;
        }

        .panel-header h3 {
            margin: 0;
            font-size: 18px;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-height: 34px;
            padding: 7px 11px;
            border: 1px solid var(--line);
            border-radius: 999px;
            color: #cde0f7;
            font-size: 12px;
            font-weight: 800;
            background: rgba(255,255,255,0.08);
        }

        .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--green);
            box-shadow: 0 0 18px var(--green);
            animation: blink 1.4s ease-in-out infinite;
        }

        .chart-wrap {
            height: 380px;
            padding: 18px 20px 22px;
        }

        .records {
            padding: 12px;
        }

        .record {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 12px;
            align-items: center;
            padding: 14px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .record:last-child {
            border-bottom: 0;
        }

        .record strong {
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .record span {
            display: block;
            margin-top: 4px;
            color: var(--muted);
            font-size: 12px;
        }

        .badge {
            min-width: 76px;
            padding: 8px 10px;
            border-radius: 999px;
            text-align: center;
            color: #06101e;
            font-size: 12px;
            font-weight: 900;
        }

        .badge.Normal { background: var(--green); }
        .badge.High { background: var(--red); }
        .badge.Low { background: var(--yellow); }

        .empty {
            padding: 28px 16px;
            color: var(--muted);
            text-align: center;
        }

        .alert-toast {
            position: fixed;
            right: 24px;
            top: 24px;
            z-index: 5;
            width: min(380px, calc(100vw - 48px));
            padding: 16px;
            border: 1px solid rgba(255, 92, 122, 0.35);
            border-radius: 8px;
            background: rgba(60, 12, 24, 0.88);
            box-shadow: 0 20px 55px rgba(255, 92, 122, 0.22);
            backdrop-filter: blur(18px);
            animation: toastIn 0.55s ease both, toastOut 0.45s ease 5.2s forwards;
        }

        .alert-toast strong {
            display: block;
            margin-bottom: 5px;
        }

        .alert-toast span {
            color: #ffd3db;
            font-size: 13px;
            line-height: 1.5;
        }

        @keyframes riseIn {
            from {
                opacity: 0;
                transform: translateY(22px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes drift {
            from { transform: translate3d(0, 0, 0) rotate(0deg); }
            to { transform: translate3d(42px, -34px, 0) rotate(32deg); }
        }

        @keyframes pulseGlow {
            0%, 100% { transform: scale(0.92); opacity: 0.5; }
            50% { transform: scale(1.08); opacity: 0.85; }
        }

        @keyframes rotateIn {
            from {
                opacity: 0;
                transform: rotate(-24deg) scale(0.88);
            }
            to {
                opacity: 1;
                transform: rotate(0) scale(1);
            }
        }

        @keyframes blink {
            0%, 100% { opacity: 0.45; transform: scale(0.9); }
            50% { opacity: 1; transform: scale(1.14); }
        }

        @keyframes toastIn {
            from {
                opacity: 0;
                transform: translateX(32px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes toastOut {
            to {
                opacity: 0;
                transform: translateX(32px);
                visibility: hidden;
            }
        }

        @media (max-width: 1180px) {
            .cards {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .content-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 860px) {
            .dashboard {
                grid-template-columns: 1fr;
            }

            .sidebar {
                position: relative;
                height: auto;
                padding: 18px;
            }

            .brand {
                padding-bottom: 16px;
            }

            .nav {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .nav a:hover,
            .nav a.active {
                transform: translateY(-3px);
            }

            .sidebar-footer {
                position: static;
                margin-top: 16px;
            }

            .main {
                padding: 18px;
            }

            .hero {
                grid-template-columns: 1fr;
            }

            .health-ring {
                width: 132px;
                height: 132px;
            }
        }

        @media (max-width: 560px) {
            .cards,
            .nav {
                grid-template-columns: 1fr;
            }

            .hero,
            .card,
            .panel {
                border-radius: 8px;
            }

            .hero {
                padding: 22px;
            }

            .chart-wrap {
                height: 310px;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <aside class="sidebar">
            <div class="brand">
                <div class="brand-mark">TM</div>
                <div>
                    <h1>TempMonitor</h1>
                    <span>Cold-chain intelligence</span>
                </div>
            </div>

            <nav class="nav" aria-label="Main navigation">
                <a class="active" href="index.php"><span class="nav-icon">01</span>Dashboard</a>
                <a href="temperature.php"><span class="nav-icon">02</span>Temperature</a>
                <a href="vegetables.php"><span class="nav-icon">03</span>Vegetables</a>
                <a href="alerts.php"><span class="nav-icon">04</span>Alerts</a>
                <a href="reports.php"><span class="nav-icon">05</span>Reports</a>
                <a href="logout.php"><span class="nav-icon">06</span>Logout</a>
            </nav>

            <div class="sidebar-footer">
                <small>Latest system state</small>
                <strong><?php echo htmlspecialchars($latest_status, ENT_QUOTES, 'UTF-8'); ?></strong>
            </div>
        </aside>

        <main class="main">
            <section class="hero">
                <div>
                    <p class="eyebrow">Live storage command center</p>
                    <h2>Welcome back, <?php echo $username; ?>.</h2>
                    <p>Monitor produce temperature health, spot risk instantly, and keep every storage zone performing inside its ideal range.</p>
                </div>

                <div class="health-ring" aria-label="Average temperature">
                    <div>
                        <strong><?php echo htmlspecialchars((string) $avg_temp, ENT_QUOTES, 'UTF-8'); ?>&deg;</strong>
                        <span>Avg Temp</span>
                    </div>
                </div>
            </section>

            <section class="cards" aria-label="Dashboard metrics">
                <article class="card">
                    <div class="card-label">
                        Total Vegetables
                        <span class="spark">V</span>
                    </div>
                    <div class="card-value"><?php echo $veg_count; ?></div>
                    <div class="card-note">Tracked produce profiles in the monitoring system.</div>
                </article>

                <article class="card">
                    <div class="card-label">
                        Today's Records
                        <span class="spark">T</span>
                    </div>
                    <div class="card-value"><?php echo $today_records; ?></div>
                    <div class="card-note">Fresh readings logged since midnight.</div>
                </article>

                <article class="card <?php echo $statusClass; ?>">
                    <div class="card-label">
                        Active Alerts
                        <span class="spark">A</span>
                    </div>
                    <div class="card-value"><?php echo $alerts; ?></div>
                    <div class="card-note">Latest reading: <?php echo htmlspecialchars((string) $latest_temp, ENT_QUOTES, 'UTF-8'); ?>&deg; with <?php echo htmlspecialchars($latest_status, ENT_QUOTES, 'UTF-8'); ?> status.</div>
                </article>

                <article class="card">
                    <div class="card-label">
                        Users
                        <span class="spark">U</span>
                    </div>
                    <div class="card-value"><?php echo $users; ?></div>
                    <div class="card-note">Authorized people with access to monitoring.</div>
                </article>
            </section>

            <section class="content-grid">
                <article class="panel">
                    <div class="panel-header">
                        <h3>Temperature Status Analytics</h3>
                        <span class="pill"><span class="dot"></span>Live dataset</span>
                    </div>
                    <div class="chart-wrap">
                        <canvas id="statusChart"></canvas>
                    </div>
                </article>

                <article class="panel">
                    <div class="panel-header">
                        <h3>Recent Readings</h3>
                        <span class="pill"><?php echo $normal + $high + $low; ?> total</span>
                    </div>
                    <div class="records">
                        <?php if ($recent_records && mysqli_num_rows($recent_records) > 0) { ?>
                            <?php while ($row = mysqli_fetch_assoc($recent_records)) {
                                $name = htmlspecialchars($row['name'] ?? 'Unknown item', ENT_QUOTES, 'UTF-8');
                                $temperature = htmlspecialchars((string) $row['temperature'], ENT_QUOTES, 'UTF-8');
                                $status = htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8');
                                $created = htmlspecialchars($row['created_at'] ?? 'Recently', ENT_QUOTES, 'UTF-8');
                            ?>
                                <div class="record">
                                    <div>
                                        <strong><?php echo $name; ?></strong>
                                        <span><?php echo $temperature; ?>&deg;C recorded <?php echo $created; ?></span>
                                    </div>
                                    <span class="badge <?php echo $status; ?>"><?php echo $status; ?></span>
                                </div>
                            <?php } ?>
                        <?php } else { ?>
                            <div class="empty">No temperature readings yet.</div>
                        <?php } ?>
                    </div>
                </article>
            </section>
        </main>
    </div>

    <?php if ($latest_alert) {
        $alertName = htmlspecialchars($latest_alert['name'] ?? 'Unknown item', ENT_QUOTES, 'UTF-8');
        $alertStatus = htmlspecialchars($latest_alert['status'], ENT_QUOTES, 'UTF-8');
        $alertTemp = htmlspecialchars((string) $latest_alert['temperature'], ENT_QUOTES, 'UTF-8');
    ?>
        <div class="alert-toast" role="status" aria-live="polite">
            <strong>Temperature alert detected</strong>
            <span><?php echo $alertName; ?> is <?php echo $alertStatus; ?> at <?php echo $alertTemp; ?>&deg;C.</span>
        </div>
    <?php } ?>

    <script>
        const chartCanvas = document.getElementById('statusChart');
        const chartGradient = chartCanvas.getContext('2d').createLinearGradient(0, 0, 0, 360);
        chartGradient.addColorStop(0, 'rgba(45, 226, 230, 0.95)');
        chartGradient.addColorStop(0.52, 'rgba(51, 209, 122, 0.72)');
        chartGradient.addColorStop(1, 'rgba(143, 124, 255, 0.45)');

        new Chart(chartCanvas, {
            type: 'bar',
            data: {
                labels: ['Normal', 'High', 'Low'],
                datasets: [{
                    label: 'Temperature Status',
                    data: [<?php echo $normal; ?>, <?php echo $high; ?>, <?php echo $low; ?>],
                    backgroundColor: [
                        chartGradient,
                        'rgba(255, 92, 122, 0.86)',
                        'rgba(255, 209, 102, 0.9)'
                    ],
                    borderColor: [
                        'rgba(45, 226, 230, 1)',
                        'rgba(255, 92, 122, 1)',
                        'rgba(255, 209, 102, 1)'
                    ],
                    borderWidth: 2,
                    borderRadius: 8,
                    barThickness: 62
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 1500,
                    easing: 'easeOutQuart'
                },
                plugins: {
                    legend: {
                        labels: {
                            color: '#cde0f7',
                            boxWidth: 14,
                            font: {
                                weight: '700'
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(5, 13, 27, 0.94)',
                        borderColor: 'rgba(45, 226, 230, 0.35)',
                        borderWidth: 1,
                        titleColor: '#eef6ff',
                        bodyColor: '#cde0f7',
                        padding: 12,
                        displayColors: false
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#cde0f7',
                            font: {
                                weight: '800'
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: '#9fb1c7',
                            precision: 0
                        },
                        grid: {
                            color: 'rgba(255,255,255,0.09)'
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
