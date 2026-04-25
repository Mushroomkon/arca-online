<?php

/**
 * sanitize.php
 * Location: server/utils/sanitize.php
 * Small utility file for cleaning user input.
 */


/**
 * Clean up normal text input.
 * - trims spaces
 * - removes HTML tags
 * - escapes special characters
 */
function sanitize_text(string $value): string {
    $value = trim($value);
    $value = strip_tags($value);
    $value = htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    return $value;
}


/**
 * Clean and validate email input.
 * Returns a cleaned email if valid, otherwise false.
 */
function sanitize_email(string $value): string|false {
    $value = trim($value);
    $value = filter_var($value, FILTER_SANITIZE_EMAIL);
    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    return strtolower($value);
}


/**
 * Handle password input safely.
 * Only trims whitespace.
 * 
 */
function sanitize_password(string $value): string {
    return trim($value);
}


/**
 * Basic password strength check.
 * Rules:
 * - at least 8 characters
 * - must include uppercase, lowercase, and a number
 * 
 * Returns true if it passes all checks.
 */
function is_strong_password(string $password): bool {
    if (strlen($password) < 8) return false;
    if (!preg_match('/[A-Z]/', $password)) return false;
    if (!preg_match('/[a-z]/', $password)) return false;
    if (!preg_match('/[0-9]/', $password)) return false;
    return true;
}


/**
 * Validate integer values (IDs, quantities, etc.).
 * Returns the integer if valid, otherwise false.
 */
function sanitize_int(mixed $value): int|false {
    $filtered = filter_var($value, FILTER_VALIDATE_INT);
    return $filtered !== false ? (int)$filtered : false;
}


/**
 * Validate positive numbers (including decimals).
 * Returns the number if valid and >= 0, otherwise false.
 */
function sanitize_positive_number(mixed $value): float|false {
    $filtered = filter_var($value, FILTER_VALIDATE_FLOAT);
    if ($filtered === false || $filtered < 0) return false;
    return $filtered;
}


/**
 * Clean identifiers like usernames or codes.
 * Allows only letters, numbers, underscores, and dashes.
 */
function sanitize_slug(string $value): string {
    $value = trim($value);
    return preg_replace('/[^a-zA-Z0-9_\-]/', '', $value);
}


/**
 * Send a JSON error response and stop execution.
 */
function respond_error(string $message, int $http_code = 400): never {
    http_response_code($http_code);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => $message]);
    exit;
}


/**
 * Send a JSON success response and stop execution.
 */
function respond_success(string $message, array $data = []): never {
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode(array_merge(['success' => true, 'message' => $message], $data));
    exit;
}


/**
 * Redirect to another page with a message in the URL.
 */
function redirect_with_msg(string $url, string $msg): never {
    $safe_msg = urlencode($msg);
    header("Location: {$url}?msg={$safe_msg}");
    exit;
}