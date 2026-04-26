<?php

function get_allowed_sort(): array {
    return [
        'name'  => 'u.user_name ASC',
        'date'  => 'u.user_date ASC',
        'sales' => 'total_sales DESC',
    ];
}


function get_all_users(mysqli $conn, string $sort): array {
    $allowed  = get_allowed_sort();
    $order_by = $allowed[$sort] ?? 'u.user_id ASC';

  
    $stmt = $conn->prepare("
        SELECT
            u.user_id,
            u.user_name,
            u.user_date,
            IFNULL(SUM(s.total_sales), 0) AS total_sales
        FROM users u
        LEFT JOIN sales s ON u.user_id = s.user_id_fk
        WHERE u.user_name != 'admin_renier'
        GROUP BY u.user_id
        ORDER BY $order_by
    ");
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $rows;
}


function delete_user(mysqli $conn, int $user_id): bool {
    $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ? AND user_name != 'admin_renier'");
    $stmt->bind_param("i", $user_id);
    $ok = $stmt->execute() && $stmt->affected_rows > 0;
    $stmt->close();
    return $ok;
}