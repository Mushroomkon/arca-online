<?php
session_start();
include '../../config/connect.php';

require_once __DIR__ . '/../utils/sanitize.php';

$email    = sanitize_email($_POST['email'] ?? '');
$password = sanitize_password($_POST['password'] ?? '');

if (!$email) {
<<<<<<< HEAD
    redirect_with_msg('../../client/auth/login.php', 'Invalid email format.');
}
if (!$password) {
    redirect_with_msg('../../client/auth/login.php', 'Password is required.');
}

=======
    redirect_with_msg('../../client/auth/login.html', 'Invalid email format.');
       }
        if (!$password) {
    redirect_with_msg('../../client/auth/login.html', 'Password is required.');
        }
>>>>>>> ec7508091ec4ae51c75e9152cc3d8006b84052ea

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if ($email === "adminadmin@gmail.com" && $password === "Admin321") {
        $_SESSION['user_id'] = 0;
        $_SESSION['user_name'] = "admin_renier";
        header("Location: ../../client/admin/admin.php");
        exit();
    }

    $sql = "SELECT * FROM users WHERE user_email = '$email' AND user_password = '$password'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);

        if (password_verify($password, $row['user_password'])) {
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['user_name'] = $row['user_name'];
            header("Location: ../../client/pages/dashboard.php");
            exit();
        }

    }
//hello

    header("Location: ../../client/auth/login.php?msg=" . urlencode("Invalid email or password"));
    exit();
}
?>
