<?php
session_start();
include '../config/connect.php';

if (isset($_POST['signup'])) {
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    

    $user_date = date("Y-m-d");

    $sql = "INSERT INTO users (user_email, user_name, user_password, user_date)
            VALUES ('$email', '$username', '$hashed_password', '$user_date')";

    if (mysqli_query($conn, $sql)) {
        $user_id = mysqli_insert_id($conn);
        $_SESSION['user_id'] = $user_id;
        $_SESSION['user_name'] = $username;
        header("Location: ../frontend/main.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
