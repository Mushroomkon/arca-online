<?php
session_start();
include '../config/connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../frontend/login.html");
    exit();
}

if (isset($_POST['edit'])) {
    $user = $_SESSION['user_id'];
    $id = (int)$_POST['updateId'];
    $items = mysqli_real_escape_string($conn, $_POST['item']);
    $categories = mysqli_real_escape_string($conn, $_POST['category']); 
    $quantities = (int)$_POST['quantity'];
    $costs = (float)$_POST['cost'];
    $prices = (float)$_POST['price'];

    $sql = "UPDATE items 
            SET 
            user_id_fk = '$user',
            product_name ='$items',
            product_category='$categories',
            product_quantity='$quantities',
            product_price='$prices',
            product_cost='$costs'
            WHERE product_id = $id";

    if (mysqli_query($conn, $sql)) {
        header("Location: ../frontend/inventory.php");
        exit();
    } else {
        echo "Error editing record: " . mysqli_error($conn);
    }
} else {
    echo "No ID specified for update.";
}
?>
