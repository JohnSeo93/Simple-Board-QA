<?php
require_once __DIR__ . '/../includes/helpers.php';

if (!isLoggedIn()) {
    jsonResponse(401, ['message' => '로그인이 필요합니다.']);
}

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    jsonResponse(400, ['message' => '유효하지 않은 게시글 ID입니다.']);
}

$db   = getDB();
$stmt = $db->prepare('SELECT id, user_id FROM posts WHERE id = ? AND is_deleted = 0');
$stmt->execute([$id]);
$post = $stmt->fetch();

if (!$post) {
    jsonResponse(404, ['message' => '게시글을 찾을 수 없습니다.']);
}

$user = getCurrentUser();
if ((int)$post['user_id'] !== (int)$user['id']) {
    jsonResponse(403, ['message' => '수정 권한이 없습니다.']);
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

$db->prepare('UPDATE posts SET title = ?, content = ? WHERE id = ?')
   ->execute([$title, $content, $id]);

jsonResponse(200, ['message' => '게시글이 수정되었습니다.']);
