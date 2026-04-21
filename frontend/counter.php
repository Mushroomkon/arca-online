<?php 
session_start();
include '../config/connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../frontend/login.html");
    exit();
}

$user_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arca Counter</title>
    <link rel="icon" type="image/png" sizes="32x32" href="/img/logo.png">
    <link rel="stylesheet" href="../frontend/counter.css">
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
                <?php 
                $sql = "SELECT * FROM items WHERE user_id_fk = $user_id";
                $result = mysqli_query($conn, $sql);

                if(mysqli_num_rows($result) > 0){
                    while($item = mysqli_fetch_assoc($result)){
                        echo "<tr>
                            <td>".$item['product_name']."</td>
                            <td>".$item['product_category']."</td>
                            <td>".$item['product_price']."</td>
                            <td>
                                <div class='action'>
                                    <form action='../backend/punch.php' method='POST' style='display:inline-block;'>
                                        <input type='hidden' name='id' value='".$item['product_id']."'>
                                        <button class='blue' type='submit'>Punch</button>
                                    </form>

                                    <form action='../backend/decrease.php' method='POST' style='display:inline-block;'>
                                        <input type='hidden' name='id' value='".$item['product_id']."'>
                                        <button type='submit' class='red'>Decrease</button>
                                    </form>
                                </div>
                            </td>
                        </tr>";
                    }
                }
                ?>
            </tbody>
        </table>
    </section>

    <section class="right-box">
        <h1>Receipt</h1>

        <?php
        $receipt = mysqli_query($conn, "SELECT * FROM receipt WHERE user_id_fk=$user_id");

        if(mysqli_num_rows($receipt) > 0){
            echo "<table>";
            echo "<tr><th>Item</th><th>Price</th><th>Qty</th><th>Total</th></tr>";

            $grand_total = 0;

            while($r = mysqli_fetch_assoc($receipt)){
                $total = $r['item_price_fk'] * $r['item_quantity_fk'];
                $grand_total += $total;

                echo "<tr>
                    <td>{$r['item_name_fk']}</td>
                    <td>{$r['item_price_fk']}</td>
                    <td>{$r['item_quantity_fk']}</td>
                    <td>$total</td>
                </tr>";
            }

            echo "<tr><th colspan='3'>Grand Total</th><th>$grand_total</th></tr>";
            echo "</table>";
        } else {
            echo "<p>No items in receipt.</p>";
        }
        ?>

        <section class="rightbottom-box"> 
            <form action="../backend/push.php" method="POST" style="display:inline-block;">
                <button type="submit" class="blue">Push All</button>
            </form>

            <form action="../backend/cancel.php" method="POST" style="display:inline-block;">
                <button type="submit" class="red">Cancel All</button>
            </form>
        </section>
    </section>
</main>    
</body>
</html>
