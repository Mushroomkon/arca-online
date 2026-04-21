<?php
session_start();
include '../config/connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../frontend/login.html");
    exit();
}

if (isset($_POST['add'])) {
    $user = $_SESSION['user_id'];
    $items = mysqli_real_escape_string($conn, $_POST['item']);
    $categories = mysqli_real_escape_string($conn, $_POST['category']);
    $quantities = (int)$_POST['quantity'];
    $costs = (float)$_POST['cost'];
    $prices = (float)$_POST['price'];

    $sql = "INSERT INTO items (user_id_fk, product_name, product_category, product_quantity, product_price, product_cost)
            VALUES($user, '$items', '$categories', $quantities, $prices, $costs)";

    if (!mysqli_query($conn, $sql)) {
        echo "error: " . mysqli_error($conn);
    }

    header("Location: ../frontend/inventory.php");
    exit();
}
?>
