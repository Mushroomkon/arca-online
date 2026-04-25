<?php

function get_counter_items(mysqli $conn, int $user_id): array {
    $stmt = $conn->prepare("
        SELECT product_id, product_name, product_category, product_price
        FROM items
        WHERE user_id_fk = ?
        ORDER BY product_name ASC
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $rows;
}


function get_receipt(mysqli $conn, int $user_id): array {
    $stmt = $conn->prepare("
        SELECT item_name_fk, item_price_fk, item_quantity_fk
        FROM receipt
        WHERE user_id_fk = ?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $rows;
}