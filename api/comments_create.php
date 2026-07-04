<?php
require_once __DIR__ . '/../includes/helpers.php';

if (!isLoggedIn()) {
    jsonResponse(401, ['message' => '로그인이 필요합니다.']);
}

$body    = getJsonBody();
$postId  = (int)($body['post_id'] ?? 0);
$content = trim($body['content'] ?? '');

if ($postId <= 0) {
    jsonResponse(400, ['message' => '게시글 ID가 필요합니다.']);
}
if ($content === '') {
    jsonResponse(400, ['message' => '댓글 내용을 입력해주세요.']);
}

$db   = getDB();
$stmt = $db->prepare('SELECT id FROM posts WHERE id = ? AND is_deleted = 0');
$stmt->execute([$postId]);
if (!$stmt->fetch()) {
    jsonResponse(404, ['message' => '게시글을 찾을 수 없습니다.']);
}

$user = getCurrentUser();
$db->prepare('INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)')
   ->execute([$postId, $user['id'], $content]);

jsonResponse(201, ['message' => '댓글이 등록되었습니다.']);
