<?php
session_start();
require_once __DIR__ . '/../../config/connect.php';
require_once __DIR__ . '/../../server/utils/sanitize.php';
require_once __DIR__ . '/../../server/queries/sales_queries.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.html");
    exit();
}

$user_id = sanitize_int($_SESSION['user_id']);
if ($user_id === false) {
    session_destroy();
    header("Location: ../auth/login.html");
    exit();
}

$totals       = get_sales_totals($conn, $user_id);
$top_items    = get_top_items($conn, $user_id);
$sales_records = get_sales_records($conn, $user_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arca Sales</title>
    <link rel="icon" type="image/png" sizes="32x32" href="../assets/img/logo.png">
    <link rel="stylesheet" href="../assets/css/sales.css">
</head>
<body>
<div class="glow-orb glow-orb-1"></div>
<div class="glow-orb glow-orb-2"></div>
<div class="glow-orb glow-orb-3"></div>
<header>
    <div class="logo"><img src="../assets/img/arca.png" alt="logo"></div>

    <nav id="nav-menu">
        <a href="dashboard.php">Dashboard</a>
        <a href="counter.php">Counter</a>
        <a href="sales.php" class="active">Sales Report</a>
        <form action="../../server/auth/logout.php" method="post" class="nav-logout">
            <button type="submit">Logout</button>
        </form>
    </nav>

    <!-- Desktop logout -->
    <div class="logout">
        <form action="../../server/auth/logout.php" method="post">
            <button type="submit">Logout</button>
        </form>
    </div>

    <!-- Hamburger on the far right -->
    <button class="hamburger" id="hamburger" aria-label="Toggle menu">
        <span></span>
        <span></span>
        <span></span>
    </button>
</header>

<div class="dashboard-container">

    <div class="left_box">

        <div class="card overall-sales">
            <p style="color: #5bc0de;">Overall Sales</p>
            <h2>₱<?= number_format($totals['total_sales'], 2) ?></h2>
        </div>

        <div class="card cost">
            <p style="color: #c9302c;">Total Cost</p>
            <h2>₱<?= number_format($totals['total_cost'], 2) ?></h2>
        </div>

        <div class="card most-bought">
            <p style="font-size: 1.7rem;">Top 5 Most Bought Items</p>
            <table>
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Quantity Sold</th>
                        <th>Total Sales</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($top_items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['item_name'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= (int)$item['total_items'] ?></td>
                        <td>₱<?= number_format($item['total_sales'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>

    <div class="right">
        <div class="card sales-table">
            <p>Sales Records</p>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Item Name</th>
                        <th>Quantity</th>
                        <th>Cost</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sales_records as $sale): ?>
                    <tr>
                        <td><?= date('M d, Y', strtotime($sale['sale_date'])) ?></td>
                        <td><?= htmlspecialchars($sale['item_name'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= (int)$sale['total_items'] ?></td>
                        <td>₱<?= number_format($sale['total_cost'], 2) ?></td>
                        <td>₱<?= number_format($sale['total_sales'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
<script>
    // ─── Hamburger ─────────────────────────────────────────
    const hamburger = document.getElementById('hamburger');
    const navMenu   = document.getElementById('nav-menu');

    hamburger.addEventListener('click', () => {
        hamburger.classList.toggle('open');
        navMenu.classList.toggle('open');
    });
</script>
</body>
</html>