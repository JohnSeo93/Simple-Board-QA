<?php
require_once __DIR__ . '/../config/database.php';

define('BASE_PATH', '/simple_board/public');

function startSession(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function isLoggedIn(): bool {
    startSession();
    return isset($_SESSION['user_id']);
}

function getCurrentUser(): ?array {
    startSession();
    if (!isLoggedIn()) return null;
    return [
        'id'       => $_SESSION['user_id'],
        'username' => $_SESSION['username'],
        'nickname' => $_SESSION['nickname'],
    ];
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_PATH . '/login');
        exit;
    }
}

function requireGuest(): void {
    if (isLoggedIn()) {
        header('Location: ' . BASE_PATH . '/posts');
        exit;
    }
}

function jsonResponse(int $status, array $data): void {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function getJsonBody(): array {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function sanitize(string $value): string {
    return trim(htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
}
