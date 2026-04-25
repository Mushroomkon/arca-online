<?php

function get_all_items(mysqli $conn, int $user_id): array {
    $stmt = $conn->prepare("
        SELECT product_id, product_name, product_category, product_quantity, product_cost, product_price
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


function search_items(mysqli $conn, int $user_id, string $search): array {

    $like = '%' . $search . '%';
    $stmt = $conn->prepare("
        SELECT product_id, product_name, product_category, product_quantity, product_cost, product_price
        FROM items
        WHERE user_id_fk = ?
        AND product_name LIKE ?
        ORDER BY product_name ASC
    ");
    $stmt->bind_param("is", $user_id, $like);
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $rows;
}