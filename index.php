<?php
session_start();

$logged_in = isset($_SESSION['user_id']);
$is_admin = isset($_SESSION['user_name']) && $_SESSION['user_name'] === "admin_renier";


if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arca Landing Page</title>
    <link rel="icon" type="image/png" sizes="32x32" href="img/logo.png">
    <link rel="stylesheet" href="index.css">
</head>
<body>

<header>
    <div class="logo">
        <img src="img/arca.png" alt="Arca Logo">
    </div>

    <nav>
        <?php if ($logged_in): ?>
            <a href="frontend/main.php">Main</a>
            <a href="frontend/inventory.php">Inventory</a>
            <a href="frontend/counter.php">Counter</a>
            <a href="frontend/sales.php">Sales Report</a>
        <?php endif; ?>
    </nav>

    <div class="auth">
        <?php if (!$logged_in): ?>
            <a href="frontend/login.html"><button>Login</button></a>
            <a href="frontend/signup.html"><button>Sign Up</button></a>
        <?php else: ?>
            <a href="index.php?action=logout"><button>Logout</button></a>
        <?php endif; ?>
    </div>
</header>

<main>
    <section class="hero">
        <h1>Welcome to Arca</h1>
        <p>Your classic and efficient inventory & sales dashboard</p>

        <a href="<?= $logged_in ? 'frontend/main.php' : 'frontend/login.html' ?>" class="btn">
            Get Started
        </a>
    </section>

    <section class="features">
        <div class="feature">
            <h2>Manage Inventory</h2>
            <p>Easily add, edit, and remove items in your inventory.</p>
        </div>
        <div class="feature">
            <h2>Counter</h2>
            <p>Quickly punch items and handle transactions efficiently.</p>
        </div>
        <div class="feature">
            <h2>Sales Reports</h2>
            <p>View overall sales, top items, and track your profits.</p>
        </div>
    </section>

    <div class="bubbles">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
    </div>
</main>

<footer>
    <p>© <?= date('Y') ?> Arca. All rights reserved.</p>
</footer>

</body>
</html>
