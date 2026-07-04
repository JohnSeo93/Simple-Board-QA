<?php
require_once __DIR__ . '/../includes/helpers.php';

$body     = getJsonBody();
$username = trim($body['username'] ?? '');
$password = $body['password'] ?? '';

if ($username === '' || $password === '') {
    jsonResponse(400, ['message' => '아이디와 비밀번호를 입력해주세요.']);
}

$db   = getDB();
$stmt = $db->prepare('SELECT id, username, password, nickname FROM users WHERE username = ?');
$stmt->execute([$username]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password'])) {
    jsonResponse(401, ['message' => '아이디 또는 비밀번호가 일치하지 않습니다.']);
}

startSession();
session_regenerate_id(true);

$_SESSION['user_id']  = $user['id'];
$_SESSION['username'] = $user['username'];
$_SESSION['nickname'] = $user['nickname'];

jsonResponse(200, [
    'message'  => '로그인 성공',
    'user' => [
        'id'       => $user['id'],
        'username' => $user['username'],
        'nickname' => $user['nickname'],
    ],
]);
