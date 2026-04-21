<?php
session_start();
include '../config/connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../frontend/login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

$totals = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT 
        SUM(total_sales) AS total_sales,
        SUM(total_cost) AS total_cost
    FROM sales
    WHERE user_id_fk = $user_id
"));

$most_bought_query = mysqli_query($conn, "
    SELECT item_name, SUM(total_items) AS total_items, SUM(total_cost) AS total_cost, SUM(total_sales) AS total_sales
    FROM sales
    WHERE user_id_fk = $user_id
    GROUP BY item_name
    ORDER BY total_items DESC
    LIMIT 5
");

$sales_query = mysqli_query($conn, "
    SELECT sale_date, item_name, total_sales, total_cost, total_items
    FROM sales
    WHERE user_id_fk = $user_id
    ORDER BY sale_date DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sales Dashboard</title>
<link rel="icon" type="image/png" sizes="32x32" href="/img/logo.png">
<link rel="stylesheet" href="../frontend/sales.css">
</head>
<body>
<header>
    <div class="left">
        <div class="logo"><img src="../img/arca.png" alt="Arca Logo"></div>
        <nav>
            <a href="main.php">Main</a>
            <a href="inventory.php">Inventory</a>
            <a href="counter.php">Counter</a>
            <a href="sales.php">Sales Report</a>
        </nav>
    </div>
    <div class="logout">
        <form action="../backend/logout.php" method="post">
            <button type="submit">Logout</button>
        </form>
    </div>
</header>

<div class="dashboard-container">

    <div class="left_box">
        <div class="card overall-sales">
            <p>Overall Sales</p>
            <h1>₱<?= number_format($totals['total_sales'], 2) ?></h1>
        </div>

        <div class="card cost">
            <p>Total Cost</p>
            <h2>₱<?= number_format($totals['total_cost'], 2) ?></h2>
        </div>

        <div class="card most-bought">
            <p>Top 5 Most Bought Items</p>
            <table>
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Quantity Sold</th>
                        <th>Total Sales</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($item = mysqli_fetch_assoc($most_bought_query)) : ?>
                    <tr>
                        <td><?= $item['item_name'] ?></td>
                        <td><?= $item['total_items'] ?></td>
                        <td>₱<?= number_format($item['total_sales'],2) ?></td>
                    </tr>
                    <?php endwhile; ?>
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
                    <?php while($sale = mysqli_fetch_assoc($sales_query)) : ?>
                    <tr>
                        <td><?= date('M d, Y', strtotime($sale['sale_date'])) ?></td>
                        <td><?= $sale['item_name'] ?></td>
                        <td><?= $sale['total_items'] ?></td>
                        <td>₱<?= number_format($sale['total_cost'],2) ?></td>
                        <td>₱<?= number_format($sale['total_sales'],2) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
</body>
</html>
