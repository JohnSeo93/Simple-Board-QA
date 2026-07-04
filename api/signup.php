<?php
require_once __DIR__ . '/../includes/helpers.php';

$body = getJsonBody();

$username        = trim($body['username'] ?? '');
$password        = $body['password'] ?? '';
$passwordConfirm = $body['password_confirm'] ?? '';
$nickname        = trim($body['nickname'] ?? '');

// ── Validation ─────────────────────────────────────────────
$errors = [];

if ($username === '') {
    $errors[] = '아이디를 입력해주세요.';
} elseif (mb_strlen($username) < 4 || mb_strlen($username) > 20) {
    $errors[] = '아이디는 4~20자여야 합니다.';
}

if ($password === '') {
    $errors[] = '비밀번호를 입력해주세요.';
} elseif (mb_strlen($password) < 8 || mb_strlen($password) > 20) {
    $errors[] = '비밀번호는 8~20자여야 합니다.';
}

if ($password !== $passwordConfirm) {
    $errors[] = '비밀번호가 일치하지 않습니다.';
}

if ($nickname === '') {
    $errors[] = '닉네임을 입력해주세요.';
} elseif (mb_strlen($nickname) < 2 || mb_strlen($nickname) > 20) {
    $errors[] = '닉네임은 2~20자여야 합니다.';
}

if (!empty($errors)) {
    jsonResponse(400, ['message' => implode(' ', $errors), 'errors' => $errors]);
}

// ── Duplicate check ────────────────────────────────────────
$db   = getDB();
$stmt = $db->prepare('SELECT id FROM users WHERE username = ?');
$stmt->execute([$username]);

if ($stmt->fetch()) {
    jsonResponse(409, ['message' => '이미 사용중인 아이디입니다.']);
}

// ── Insert ─────────────────────────────────────────────────
$hash = password_hash($password, PASSWORD_BCRYPT);
$stmt = $db->prepare('INSERT INTO users (username, password, nickname) VALUES (?, ?, ?)');
$stmt->execute([$username, $hash, $nickname]);

jsonResponse(201, ['message' => '회원가입이 완료되었습니다.']);
