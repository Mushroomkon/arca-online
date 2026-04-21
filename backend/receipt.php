<?php
session_start();
include '../config/connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../frontend/login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

if (!isset($_GET['action']) || !isset($_GET['id'])) exit("Invalid request.");

$id = intval($_GET['id']);
$action = $_GET['action'];
if ($id <= 0) exit("Invalid ID");

if ($action === "punch") {
    $check = mysqli_query($conn, "SELECT * FROM receipt WHERE item_id_fk = $id AND user_id_fk = $user_id");

    if (mysqli_num_rows($check) > 0) {
        mysqli_query($conn, "
            UPDATE receipt
            SET item_quantity_fk = item_quantity_fk + 1
            WHERE item_id_fk = $id AND user_id_fk = $user_id
        ");
    } else {
        $itemRes = mysqli_query($conn, "SELECT * FROM items WHERE product_id = $id");
        $item = mysqli_fetch_assoc($itemRes);
        if ($item) {
            mysqli_query($conn, "
                INSERT INTO receipt (item_id_fk, item_name_fk, item_price_fk, item_quantity_fk, user_id_fk)
                VALUES ($id, '{$item['product_name']}', '{$item['product_price']}', 1, $user_id)
            ");
        }
    }
}

header("Location: ../frontend/counter.php");
exit();
?>
