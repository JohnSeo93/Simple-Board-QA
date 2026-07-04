<?php
require_once __DIR__ . '/../includes/helpers.php';

startSession();

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// 베이스 경로 고정
$base = '/simple_board/public';

// URI에서 베이스 제거
if (str_starts_with($requestUri, $base)) {
    $uri = substr($requestUri, strlen($base));
}
if (empty($uri)) $uri = '/';

$method = $_SERVER['REQUEST_METHOD'];

// ─── API routes ───────────────────────────────────────────────────────────────
if (str_starts_with($uri, '/api/')) {
    header('Content-Type: application/json; charset=utf-8');

    if ($uri === '/api/signup' && $method === 'POST') {
        require_once __DIR__ . '/../api/signup.php'; exit;
    }
    if ($uri === '/api/login' && $method === 'POST') {
        require_once __DIR__ . '/../api/login.php'; exit;
    }
    if ($uri === '/api/logout' && $method === 'POST') {
        require_once __DIR__ . '/../api/logout.php'; exit;
    }
    if ($uri === '/api/posts' && $method === 'GET') {
        require_once __DIR__ . '/../api/posts_list.php'; exit;
    }
    if ($uri === '/api/posts' && $method === 'POST') {
        require_once __DIR__ . '/../api/posts_create.php'; exit;
    }
    if (preg_match('#^/api/posts/(\d+)$#', $uri, $m) && $method === 'GET') {
        $_GET['id'] = $m[1];
        require_once __DIR__ . '/../api/posts_detail.php'; exit;
    }
    if (preg_match('#^/api/posts/(\d+)$#', $uri, $m) && $method === 'PUT') {
        $_GET['id'] = $m[1];
        require_once __DIR__ . '/../api/posts_update.php'; exit;
    }
    if (preg_match('#^/api/posts/(\d+)$#', $uri, $m) && $method === 'DELETE') {
        $_GET['id'] = $m[1];
        require_once __DIR__ . '/../api/posts_delete.php'; exit;
    }
    if ($uri === '/api/comments' && $method === 'POST') {
        require_once __DIR__ . '/../api/comments_create.php'; exit;
    }

    jsonResponse(404, ['message' => 'API endpoint not found']);
}

// ─── Page routes ──────────────────────────────────────────────────────────────
switch (true) {
    case $uri === '/' || $uri === '':
        header("Location: {$base}/posts");
        exit;

    case $uri === '/signup':
        requireGuest();
        require_once __DIR__ . '/signup.php';
        break;

    case $uri === '/login':
        requireGuest();
        require_once __DIR__ . '/login.php';
        break;

    case $uri === '/logout':
        requireLogin();
        header("Location: {$base}/login");
        exit;

    case $uri === '/posts':
        requireLogin();
        require_once __DIR__ . '/posts.php';
        break;

    case $uri === '/posts/write':
        requireLogin();
        require_once __DIR__ . '/post_write.php';
        break;

    case preg_match('#^/posts/(\d+)/edit$#', $uri, $m):
        requireLogin();
        $_GET['id'] = $m[1];
        require_once __DIR__ . '/post_edit.php';
        break;

    case preg_match('#^/posts/(\d+)$#', $uri, $m):
        requireLogin();
        $_GET['id'] = $m[1];
        require_once __DIR__ . '/post_detail.php';
        break;

    default:
        http_response_code(404);
        echo '<h1>404 Not Found</h1>';
        break;
}
