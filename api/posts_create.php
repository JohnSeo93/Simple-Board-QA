<?php
require_once __DIR__ . '/../includes/helpers.php';

if (!isLoggedIn()) {
    jsonResponse(401, ['message' => '로그인이 필요합니다.']);
}

$body    = getJsonBody();
$title   = trim($body['title'] ?? '');
$content = trim($body['content'] ?? '');

if ($title === '') {
    jsonResponse(400, ['message' => '제목을 입력해주세요.']);
}
if ($content === '') {
    jsonResponse(400, ['message' => '내용을 입력해주세요.']);
}

$user = getCurrentUser();
$db   = getDB();
$stmt = $db->prepare('INSERT INTO posts (user_id, title, content) VALUES (?, ?, ?)');
$stmt->execute([$user['id'], $title, $content]);

$postId = (int)$db->lastInsertId();

jsonResponse(201, ['message' => '게시글이 등록되었습니다.', 'post_id' => $postId]);
