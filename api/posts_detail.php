<?php
require_once __DIR__ . '/../includes/helpers.php';

if (!isLoggedIn()) {
    jsonResponse(401, ['message' => '로그인이 필요합니다.']);
}

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    jsonResponse(400, ['message' => '유효하지 않은 게시글 ID입니다.']);
}

$db = getDB();

// Increment view count
$db->prepare('UPDATE posts SET view_count = view_count + 1 WHERE id = ? AND is_deleted = 0')
   ->execute([$id]);

// Fetch post
$stmt = $db->prepare(
    'SELECT p.id, p.title, p.content, p.view_count, p.user_id,
            u.nickname AS author, p.created_at, p.updated_at
     FROM posts p
     JOIN users u ON u.id = p.user_id
     WHERE p.id = ? AND p.is_deleted = 0'
);
$stmt->execute([$id]);
$post = $stmt->fetch();

if (!$post) {
    jsonResponse(404, ['message' => '게시글을 찾을 수 없습니다.']);
}

// Fetch comments
$stmt = $db->prepare(
    'SELECT c.id, c.content, u.nickname AS author, c.user_id, c.created_at
     FROM comments c
     JOIN users u ON u.id = c.user_id
     WHERE c.post_id = ?
     ORDER BY c.id ASC'
);
$stmt->execute([$id]);
$comments = $stmt->fetchAll();

jsonResponse(200, ['post' => $post, 'comments' => $comments]);
