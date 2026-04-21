<?php
session_start();
include '../config/connect.php';

if (isset($_POST['signup'])) {
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm = trim($_POST['confirm']);


    if (!preg_match("/^[a-zA-Z0-9._%+-]+@(gmail|yahoo|outlook|hotmail)\.[a-z]{2,}$/", $email)) {
        header("Location: ../frontend/signup.html?msg=Invalid email format");
        exit();
    }


    if (strlen($password) < 8 || 
        !preg_match("/[A-Z]/", $password) ||
        !preg_match("/[a-z]/", $password) ||
        !preg_match("/[0-9]/", $password)) {
        header("Location: ../frontend/signup.html?msg=Password must be 8 characters and contain uppercase, lowercase, and number");
        exit();
    }


    if ($password !== $confirm) {
        header("Location: ../frontend/signup.html?msg=Passwords do not match");
        exit();
    }


    $stmt = $conn->prepare("SELECT user_id FROM users WHERE user_email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->close();
        header("Location: ../frontend/signup.html?msg=Email is already registered");
        exit();
    }
    $stmt->close();


    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (user_email, user_name, user_password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $email, $username, $hashed_password);

    if ($stmt->execute()) {
        $_SESSION['user_id'] = $stmt->insert_id;
        $_SESSION['user_name'] = $username;
        header("Location: ../frontend/main.php");
        exit();
    } else {
        header("Location: ../frontend/signup.html?msg=Error during signup");
        exit();
    }
}
?>
