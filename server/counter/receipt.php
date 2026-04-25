<?php
/**
 * receipt.php
 * Location: server/counter/receipt.php
 *
 * WARNING: This file uses GET to mutate data which is not recommended.
 * GET requests can be cached, pre-fetched, or bookmarked — all of which
 * would trigger unintended DB changes. 
 * 
 * RECOMMENDATION: Delete this file and use server/counter/punch.php (POST) instead.
 * Update your counter.php buttons to POST to punch.php directly.
 *
 * This sanitized version is provided only if you need it during transition.
 */
session_start();
require_once __DIR__ . '/../../config/connect.php';
require_once __DIR__ . '/../utils/sanitize.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../client/auth/login.html");
    exit();
}

$user_id = sanitize_int($_SESSION['user_id']);
if ($user_id === false) {
    session_destroy();
    header("Location: ../../client/auth/login.html");
    exit();
}

$item_id = sanitize_int($_GET['id']     ?? '');
$action  = sanitize_text($_GET['action'] ?? '');

if ($item_id === false || $item_id <= 0) {
    redirect_with_msg('../../client/pages/counter.php', 'Invalid item ID.');
}

$allowed_actions = ['punch'];
if (!in_array($action, $allowed_actions, true)) {
    redirect_with_msg('../../client/pages/counter.php', 'Invalid action.');
}

if ($action === 'punch') {

    $stmt = $conn->prepare("
        SELECT product_id, product_name, product_price, product_quantity
        FROM items
        WHERE product_id = ? AND user_id_fk = ?
    ");
    $stmt->bind_param("ii", $item_id, $user_id);
    $stmt->execute();
    $item = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$item) {
        redirect_with_msg('../../client/pages/counter.php', 'Item not found.');
    }

    if ($item['product_quantity'] <= 0) {
        redirect_with_msg('../../client/pages/counter.php', 'Item is out of stock.');
    }

   
    $stmt = $conn->prepare("
        SELECT receipt_id FROM receipt
        WHERE item_id_fk = ? AND user_id_fk = ?
    ");
    $stmt->bind_param("ii", $item_id, $user_id);
    $stmt->execute();
    $existing = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($existing) {
        $stmt = $conn->prepare("
            UPDATE receipt
            SET item_quantity_fk = item_quantity_fk + 1
            WHERE item_id_fk = ? AND user_id_fk = ?
        ");
        $stmt->bind_param("ii", $item_id, $user_id);
        $stmt->execute();
        $stmt->close();
    } else {
        $name  = sanitize_text($item['product_name']);
        $price = (float)$item['product_price'];

        $stmt = $conn->prepare("
            INSERT INTO receipt (item_id_fk, item_name_fk, item_price_fk, item_quantity_fk, user_id_fk)
            VALUES (?, ?, ?, 1, ?)
        ");
        $stmt->bind_param("isdi", $item_id, $name, $price, $user_id);
        $stmt->execute();
        $stmt->close();
    }
}

header("Location: ../../client/pages/counter.php");
exit();