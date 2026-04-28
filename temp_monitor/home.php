<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if (isset($_SESSION['admin_id'])) {
    header("Location: admin/admin_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TempMonitor Home</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .home-page {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: grid;
            align-items: center;
            padding: 32px;
        }

        .home-wrap {
            width: min(1120px, 100%);
            margin: 0 auto;
            display: grid;
            grid-template-columns: minmax(0, 1.1fr) minmax(320px, 0.9fr);
            gap: 24px;
            align-items: center;
        }

        .home-hero {
            position: relative;
            overflow: hidden;
            min-height: 560px;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 34px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background:
                linear-gradient(140deg, rgba(45, 226, 230, 0.16), rgba(143, 124, 255, 0.12)),
                radial-gradient(circle at 75% 18%, rgba(51, 209, 122, 0.24), transparent 34%),
                rgba(255, 255, 255, 0.08);
            box-shadow: var(--shadow);
            backdrop-filter: blur(18px);
            animation: riseIn 0.7s ease both;
        }

        .home-hero::before {
            content: "";
            position: absolute;
            inset: 34px 34px auto auto;
            width: 190px;
            height: 190px;
            border-radius: 50%;
            border: 16px solid rgba(45, 226, 230, 0.18);
            border-top-color: var(--cyan);
            animation: spin 8s linear infinite;
        }

        .home-hero h1 {
            position: relative;
            z-index: 1;
            margin: 0;
            max-width: 720px;
            font-size: clamp(42px, 7vw, 84px);
            line-height: 0.96;
            letter-spacing: 0;
        }

        .home-hero p {
            position: relative;
            z-index: 1;
            max-width: 620px;
            margin: 18px 0 0;
            color: #c1d1e4;
            font-size: 17px;
            line-height: 1.7;
        }

        .access-panel {
            display: grid;
            gap: 16px;
        }

        .access-card {
            position: relative;
            overflow: hidden;
            padding: 24px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.08);
            box-shadow: var(--shadow);
            backdrop-filter: blur(18px);
            animation: riseIn 0.75s ease both;
            transition: transform 0.24s ease, border-color 0.24s ease, background 0.24s ease;
        }

        .access-card:nth-child(2) {
            animation-delay: 0.12s;
        }

        .access-card:hover {
            transform: translateY(-6px);
            border-color: rgba(45, 226, 230, 0.38);
            background: rgba(255, 255, 255, 0.12);
        }

        .access-card h2 {
            margin: 0 0 10px;
            font-size: 26px;
        }

        .access-card p {
            margin: 0 0 20px;
            color: var(--muted);
            line-height: 1.6;
        }

        .home-brand {
            position: absolute;
            top: 24px;
            left: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--text);
            font-weight: 900;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @media (max-width: 900px) {
            .home-wrap {
                grid-template-columns: 1fr;
            }

            .home-hero {
                min-height: 420px;
            }
        }

        @media (max-width: 560px) {
            .home-page {
                padding: 18px;
            }

            .home-hero {
                min-height: 360px;
                padding: 24px;
            }

            .home-hero::before {
                width: 130px;
                height: 130px;
                border-width: 12px;
            }
        }
    </style>
</head>
<body>
    <main class="home-page">
        <div class="home-wrap">
            <section class="home-hero">
                <div class="home-brand">
                    <span class="brand-mark">TM</span>
                    <span>TempMonitor</span>
                </div>

                <p class="eyebrow">Cold-chain monitoring platform</p>
                <h1>Protect every reading before it becomes a risk.</h1>
                <p>Track vegetables, temperature ranges, humidity data, alerts, reports, and admin control from one professional monitoring workspace.</p>
            </section>

            <section class="access-panel" aria-label="Login options">
                <article class="access-card">
                    <p class="eyebrow">User workspace</p>
                    <h2>Login as User</h2>
                    <p>Enter readings, manage vegetables, review alerts, and inspect reports for daily monitoring work.</p>
                    <a class="btn" href="login.php">User Login</a>
                    <a class="btn btn-muted" href="register.php" style="margin-left:8px;">Register</a>
                    <a class="btn btn-muted" href="forgot_password.php" style="margin-left:8px;">Forgot Password</a>
                </article>

                <article class="access-card">
                    <p class="eyebrow">Administrator</p>
                    <h2>Login as Admin</h2>
                    <p>Open the admin console to manage users, review records, and control operational access.</p>
                    <a class="btn" href="admin/admin_login.php">Admin Login</a>
                    <a class="btn btn-muted" href="admin/admin_forgot_password.php" style="margin-left:8px;">Forgot Password</a>
                    <a class="btn btn-muted" href="admin/admin_dashboard.php" style="margin-left:8px;">Admin Console</a>
                </article>
            </section>
        </div>
    </main>
</body>
</html>
