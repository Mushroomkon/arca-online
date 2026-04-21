<?php
session_start();
include '../config/connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if ($email === "admin_renier" && $password === "admin321") {
        $_SESSION['user_id'] = 0;
        $_SESSION['user_name'] = "admin_renier";
        header("Location: ../frontend/admin.php");
        exit();
    }

    $sql = "SELECT * FROM users WHERE user_email = '$email' LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);

        if (password_verify($password, $row['user_password'])) {
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['user_name'] = $row['user_name'];
            header("Location: ../frontend/main.php");
            exit();
        }
    }

    header("Location: ../frontend/login.html?msg=" . urlencode("Invalid email or password"));
    exit();
}
?>
