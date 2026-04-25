<?php
session_start();
include '../config/connect.php';

require_once __DIR__ . '/../utils/sanitize.php';

$email    = sanitize_email($_POST['email'] ?? '');
$username = sanitize_text($_POST['username'] ?? '');
$password = sanitize_password($_POST['password'] ?? '');
$confirm  = sanitize_password($_POST['confirm'] ?? '');

if (!$email)                        redirect_with_msg(..., 'Invalid email.');
if (!$username)                     redirect_with_msg(..., 'Username is required.');
if (!is_strong_password($password)) redirect_with_msg(..., 'Weak password.');
if ($password !== $confirm)         redirect_with_msg(..., 'Passwords do not match.');

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
        header("Location: ../../client/pages/dashboard.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
