<?php
session_start();
include '../config/connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../frontend/login.html");
    exit();
}

if (isset($_POST['user_id'])) {
    $delete_id = (int)$_POST['user_id'];
    $current_user = $_SESSION['user_id'];

    if ($delete_id !== $current_user) {
        mysqli_query($conn, "DELETE FROM users WHERE user_id=$delete_id");
        mysqli_query($conn, "DELETE FROM sales WHERE user_id_fk=$delete_id");
        mysqli_query($conn, "DELETE FROM receipt WHERE user_id_fk=$delete_id");
    }
}

header("Location: ../frontend/admin.php");
exit();
