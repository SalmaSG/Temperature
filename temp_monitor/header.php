<?php
$page_title = $page_title ?? "TempMonitor";
$active_page = $active_page ?? "";
$base_path = $base_path ?? "";
$username = htmlspecialchars($_SESSION['username'] ?? 'User', ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="stylesheet" href="<?php echo $base_path; ?>style.css">
</head>
<body>
    <div class="app-shell">
        <aside class="sidebar">
            <div class="brand">
                <div class="brand-mark">TM</div>
                <div>
                    <h1>TempMonitor</h1>
                    <span><?php echo $username; ?> workspace</span>
                </div>
            </div>

            <nav class="nav" aria-label="Main navigation">
                <a class="<?php echo $active_page === 'dashboard' ? 'active' : ''; ?>" href="<?php echo $base_path; ?>index.php"><span class="nav-icon">01</span>Dashboard</a>
                <a class="<?php echo $active_page === 'temperature' ? 'active' : ''; ?>" href="<?php echo $base_path; ?>temperature.php"><span class="nav-icon">02</span>Temperature</a>
                <a class="<?php echo $active_page === 'vegetables' ? 'active' : ''; ?>" href="<?php echo $base_path; ?>vegetables.php"><span class="nav-icon">03</span>Vegetables</a>
                <a class="<?php echo $active_page === 'alerts' ? 'active' : ''; ?>" href="<?php echo $base_path; ?>alerts.php"><span class="nav-icon">04</span>Alerts</a>
                <a class="<?php echo $active_page === 'reports' ? 'active' : ''; ?>" href="<?php echo $base_path; ?>reports.php"><span class="nav-icon">05</span>Reports</a>
                <a href="<?php echo $base_path; ?>logout.php"><span class="nav-icon">06</span>Logout</a>
            </nav>
        </aside>

        <main class="main">
