<?php
session_start();
require_once __DIR__ . '/../../config/connect.php';
require_once __DIR__ . '/../../server/utils/sanitize.php';
require_once __DIR__ . '/../../server/queries/counter_queries.php';

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

$items   = get_counter_items($conn, $user_id);
$receipt = get_receipt($conn, $user_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arca Counter</title>
    <link rel="icon" type="image/png" sizes="32x32" href="../assets/img/logo.png">
    <link rel="stylesheet" href="../assets/css/counter.css">
</head>
<body>

<header>
    <div class="left">
        <div class="logo"><img src="../assets/img/arca.png" alt="logo"></div>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="inventory.php">Inventory</a>
            <a href="counter.php" class="active">Counter</a>
            <a href="sales.php">Sales Report</a>
        </nav>
    </div>
    <div class="logout">
        <form action="../../server/auth/logout.php" method="post">
            <button type="submit">Logout</button>
        </form>
    </div>
</header>

<main>
    <section class="left-box">
        <table>
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($items): ?>
                    <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['product_name'],     ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($item['product_category'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= number_format($item['product_price'], 2) ?></td>
                        <td>
                            <div class="action">
                                <form action="../../server/counter/punch.php" method="POST" style="display:inline-block;">
                                    <input type="hidden" name="id" value="<?= (int)$item['product_id'] ?>">
                                    <button class="blue" type="submit">Punch</button>
                                </form>
                                <form action="../../server/counter/decrease.php" method="POST" style="display:inline-block;">
                                    <input type="hidden" name="id" value="<?= (int)$item['product_id'] ?>">
                                    <button class="red" type="submit">Decrease</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4" style="text-align:center;">No items found</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>

    <section class="right-box">
        <h1>Receipt</h1>

        <?php if ($receipt): ?>
            <?php
            $grand_total = 0;
            foreach ($receipt as $r) {
                $grand_total += $r['item_price_fk'] * $r['item_quantity_fk'];
            }
            ?>
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($receipt as $r): ?>
                    <tr>
                        <td><?= htmlspecialchars($r['item_name_fk'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= number_format($r['item_price_fk'], 2) ?></td>
                        <td><?= (int)$r['item_quantity_fk'] ?></td>
                        <td><?= number_format($r['item_price_fk'] * $r['item_quantity_fk'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3">Grand Total</th>
                        <th><?= number_format($grand_total, 2) ?></th>
                    </tr>
                </tfoot>
            </table>
        <?php else: ?>
            <p>No items in receipt.</p>
        <?php endif; ?>

        <section class="rightbottom-box">
            <form action="../../server/counter/push.php" method="POST" style="display:inline-block;">
                <button type="submit" class="blue">Push All</button>
            </form>
            <form action="../../server/counter/cancel.php" method="POST" style="display:inline-block;">
                <button type="submit" class="red">Cancel All</button>
            </form>
        </section>
    </section>
</main>

</body>
</html>