<?php
session_start();
include '../config/connect.php';

$user_id = $_SESSION['user_id'];
$id = $_POST['id'];

$existing = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM receipt WHERE user_id_fk=$user_id AND item_id_fk=$id"));

if ($existing) {
    if ($existing['item_quantity_fk'] > 1) {
        mysqli_query($conn, "UPDATE receipt SET item_quantity_fk = item_quantity_fk - 1 WHERE user_id_fk=$user_id AND item_id_fk=$id");
    } else {
        mysqli_query($conn, "DELETE FROM receipt WHERE user_id_fk=$user_id AND item_id_fk=$id");
    }
}

header("Location: ../frontend/counter.php");
exit();
