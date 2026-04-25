<?php

function get_sales_totals(mysqli $conn, int $user_id): array {
    $stmt = $conn->prepare("
        SELECT 
            COALESCE(SUM(total_sales), 0) AS total_sales,
            COALESCE(SUM(total_cost), 0)  AS total_cost
        FROM sales
        WHERE user_id_fk = ?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $result ?? ['total_sales' => 0, 'total_cost' => 0];
}

function get_top_items(mysqli $conn, int $user_id): array {
    $stmt = $conn->prepare("
        SELECT 
            item_name,
            SUM(total_items) AS total_items,
            SUM(total_cost)  AS total_cost,
            SUM(total_sales) AS total_sales
        FROM sales
        WHERE user_id_fk = ?
        GROUP BY item_name
        ORDER BY total_items DESC
        LIMIT 5
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $rows;
}

function get_sales_records(mysqli $conn, int $user_id): array {
    $stmt = $conn->prepare("
        SELECT sale_date, item_name, total_sales, total_cost, total_items
        FROM sales
        WHERE user_id_fk = ?
        ORDER BY sale_date DESC
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $rows;
}