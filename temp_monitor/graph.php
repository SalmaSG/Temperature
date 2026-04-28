<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$result = mysqli_query($conn, "SELECT * FROM temperature_humidity ORDER BY id ASC");

$temp = [];
$hum = [];
$date = [];

while ($row = mysqli_fetch_assoc($result)) {
    $temp[] = $row['temperature'];
    $hum[] = $row['humidity'];
    $date[] = $row['date'];
}

$page_title = "Sensor Graph";
$active_page = "reports";
include "header.php";
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<section class="page-hero">
    <div>
        <p class="eyebrow">Trend analysis</p>
        <h2>Temperature and humidity graph.</h2>
        <p>Visualize sensor movement over time with a polished animated chart.</p>
    </div>
    <div class="actions">
        <a class="btn" href="insert.php">Add Data</a>
        <a class="btn btn-muted" href="view.php">View Records</a>
    </div>
</section>

<article class="panel">
    <div class="panel-header">
        <h3>Sensor Trend</h3>
        <span class="badge badge-active"><?php echo count($date); ?> points</span>
    </div>
    <div class="panel-body chart-box">
        <canvas id="chart"></canvas>
    </div>
</article>

<script>
    const canvas = document.getElementById('chart');
    const ctx = canvas.getContext('2d');
    const tempGradient = ctx.createLinearGradient(0, 0, 0, 360);
    tempGradient.addColorStop(0, 'rgba(255, 92, 122, 0.35)');
    tempGradient.addColorStop(1, 'rgba(255, 92, 122, 0.02)');

    const humGradient = ctx.createLinearGradient(0, 0, 0, 360);
    humGradient.addColorStop(0, 'rgba(45, 226, 230, 0.35)');
    humGradient.addColorStop(1, 'rgba(45, 226, 230, 0.02)');

    new Chart(canvas, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($date); ?>,
            datasets: [
                {
                    label: 'Temperature',
                    data: <?php echo json_encode($temp); ?>,
                    borderColor: 'rgba(255, 92, 122, 1)',
                    backgroundColor: tempGradient,
                    fill: true,
                    tension: 0.38,
                    pointRadius: 4,
                    pointHoverRadius: 7
                },
                {
                    label: 'Humidity',
                    data: <?php echo json_encode($hum); ?>,
                    borderColor: 'rgba(45, 226, 230, 1)',
                    backgroundColor: humGradient,
                    fill: true,
                    tension: 0.38,
                    pointRadius: 4,
                    pointHoverRadius: 7
                }
            ]
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
                        font: { weight: '700' }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(5, 13, 27, 0.94)',
                    borderColor: 'rgba(45, 226, 230, 0.35)',
                    borderWidth: 1,
                    titleColor: '#eef6ff',
                    bodyColor: '#cde0f7',
                    padding: 12
                }
            },
            scales: {
                x: {
                    ticks: { color: '#9fb1c7' },
                    grid: { color: 'rgba(255,255,255,0.08)' }
                },
                y: {
                    beginAtZero: true,
                    ticks: { color: '#9fb1c7' },
                    grid: { color: 'rgba(255,255,255,0.08)' }
                }
            }
        }
    });
</script>

<?php include "footer.php"; ?>
