<?php
require_once __DIR__ . '/../includes/helpers.php';

if (!isLoggedIn()) {
    jsonResponse(401, ['message' => '로그인이 필요합니다.']);
}

$db   = getDB();
$stmt = $db->query(
    'SELECT p.id, p.title, u.nickname AS author, p.created_at, p.view_count
     FROM posts p
     JOIN users u ON u.id = p.user_id
     WHERE p.is_deleted = 0
     ORDER BY p.id DESC'
);
$posts = $stmt->fetchAll();

jsonResponse(200, ['posts' => $posts]);
