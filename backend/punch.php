<?php
session_start();
include '../config/connect.php';

$user_id = $_SESSION['user_id'];
$id = $_POST['id'];

$item = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM items WHERE product_id=$id"));

if ($item) {
    $existing = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM receipt WHERE user_id_fk=$user_id AND item_id_fk=$id"));
    if ($existing) {
        mysqli_query($conn, "UPDATE receipt SET item_quantity_fk = item_quantity_fk + 1 WHERE user_id_fk=$user_id AND item_id_fk=$id");
    } else {
        mysqli_query($conn, "INSERT INTO receipt (user_id_fk, item_id_fk, item_name_fk, item_price_fk, item_quantity_fk) VALUES ($user_id, {$item['product_id']}, '{$item['product_name']}', {$item['product_price']}, 1)");
    }
}

header("Location: ../frontend/counter.php");
exit();
