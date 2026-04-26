<?php

session_start();
require_once __DIR__ . '/../config/connect.php';
require_once __DIR__ . '/../server/utils/sanitize.php';
require_once __DIR__ . '/../server/queries/admin_queries.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../client/auth/login.html");
    exit();
}


$acting_id = sanitize_int($_SESSION['user_id']);
if ($acting_id === false) {
    session_destroy();
    header("Location: ../../client/auth/login.html");
    exit();
}

$stmt = $conn->prepare("SELECT user_name FROM users WHERE user_id = ?");
$stmt->bind_param("i", $acting_id);
$stmt->execute();
$actor = $stmt->get_result()->fetch_assoc();
$stmt->close();

$actor = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : null;
if (!$actor || $actor !== 'admin_renier') {
    header("Location: ../../client/pages/dashboard.php");
    exit();
}


$target_id = sanitize_int($_POST['user_id'] ?? '');
if ($target_id === false || $target_id <= 0) {
    redirect_with_msg('../../client/admin/admin.php', 'Invalid user ID.');
}


if ($target_id === $acting_id) {
    redirect_with_msg('../../client/admin/admin.php', 'You cannot delete your own account.');
}

if (delete_user($conn, $target_id)) {
    header("Location: ../../client/admin/admin.php");
} else {
    error_log("control.php: failed to delete user_id=$target_id by admin_id=$acting_id");
    redirect_with_msg('../../client/admin/admin.php', 'Delete failed. User may not exist.');
}
exit();