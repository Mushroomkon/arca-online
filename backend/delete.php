<?php
session_start();
include '../config/connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../frontend/login.html");
    exit();
}

if (isset($_POST['id'])) {
    $id = intval($_POST['id']);

    $sql = "DELETE FROM items WHERE product_id = $id";
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        die("SQL Error: " . mysqli_error($conn));
    }

    header("Location: ../frontend/inventory.php");
    exit();
}
?>
