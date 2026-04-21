<?php
session_start();
include '../config/connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../frontend/login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

mysqli_query($conn, "DELETE FROM receipt WHERE user_id_fk = $user_id");

header("Location: ../frontend/counter.php");
exit();
?>
