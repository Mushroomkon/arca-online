<?php
session_start();
include '../config/connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../frontend/login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

$receipt = mysqli_query($conn, "SELECT * FROM receipt WHERE user_id_fk = $user_id");

if (mysqli_num_rows($receipt) == 0) {
    exit("No items to push.");
}

$sale_date = date('Y-m-d H:i:s');

while ($item = mysqli_fetch_assoc($receipt)) {
    $item_name = mysqli_real_escape_string($conn, $item['item_name_fk']);
    $item_price = $item['item_price_fk'];
    $quantity = $item['item_quantity_fk'];

    $itemRes = mysqli_query($conn, "SELECT product_cost FROM items WHERE product_id = {$item['item_id_fk']}");
    $itemDetail = mysqli_fetch_assoc($itemRes);
    $item_cost = $itemDetail['product_cost'] ?? 0;

    $total_price = $item_price * $quantity;
    $total_cost  = $item_cost  * $quantity;

    $insert = mysqli_query($conn, "
        INSERT INTO sales (sale_date, total_sales, total_items, total_cost, user_id_fk, item_name)
        VALUES ('$sale_date', $total_price, $quantity, $total_cost, $user_id, '$item_name')
    ");
    if (!$insert) {
        die("Insert failed: " . mysqli_error($conn));
    }

    $update = mysqli_query($conn, "
        UPDATE items
        SET product_quantity = product_quantity - $quantity
        WHERE product_id = {$item['item_id_fk']}
    ");
    if (!$update) {
        die("Update failed: " . mysqli_error($conn));
    }
}

$delete = mysqli_query($conn, "DELETE FROM receipt WHERE user_id_fk = $user_id");
if (!$delete) {
    die("Delete failed: " . mysqli_error($conn));
}

header("Location: ../frontend/counter.php");
exit();
?>
